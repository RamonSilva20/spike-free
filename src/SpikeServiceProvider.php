<?php

namespace Opcodes\Spike;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\View\View;
use Livewire\Livewire;
use Opcodes\Spike\Actions\Products\ProcessCartPayment;
use Opcodes\Spike\Console\Commands\ClearCreditsCacheCommand;
use Opcodes\Spike\Console\Commands\GenerateFakeDataCommand;
use Opcodes\Spike\Console\Commands\InstallCommand;
use Opcodes\Spike\Console\Commands\PublishCommand;
use Opcodes\Spike\Console\Commands\RenewSubscriptionCreditsCommand;
use Opcodes\Spike\Console\Commands\RenewSubscriptionProvidablesCommand;
use Opcodes\Spike\Console\Commands\SyncProvidablesCommand;
use Opcodes\Spike\Console\Commands\UpdateCommand;
use Opcodes\Spike\Console\Commands\VerifyCommand;
use Opcodes\Spike\Facades\Spike;
use Opcodes\Spike\Http\Livewire\AddPaymentMethodModal;
use Opcodes\Spike\Http\Livewire\CheckoutModal;
use Opcodes\Spike\Http\Livewire\CreditTransactions;
use Opcodes\Spike\Http\Livewire\Invoices;
use Opcodes\Spike\Http\Livewire\PaddlePaymentMethod;
use Opcodes\Spike\Http\Livewire\PurchaseCredits;
use Opcodes\Spike\Http\Livewire\StripePaymentMethods;
use Opcodes\Spike\Http\Livewire\SubscribeModal;
use Opcodes\Spike\Http\Livewire\Subscriptions;
use Opcodes\Spike\Http\Livewire\ValidateCart;
use Opcodes\Spike\Livewire\PaddleCheckoutButton;
use Opcodes\Spike\Livewire\UpdatePaymentMethodPaddle;
use Opcodes\Spike\Observers\BillableModelObserver;
use Opcodes\Spike\Paddle\Customer as PaddleCustomer;
use Opcodes\Spike\Paddle\Listeners\PaddleEventListener;
use Opcodes\Spike\Paddle\PaymentGateway as PaddlePaymentGateway;
use Opcodes\Spike\Paddle\Subscription as PaddleSubscription;
use Opcodes\Spike\Paddle\SubscriptionItem as PaddleSubscriptionItem;
use Opcodes\Spike\Paddle\Transaction as PaddleTransaction;
use Opcodes\Spike\Stripe\Actions\SubscriptionCheckoutRedirect;
use Opcodes\Spike\Stripe\Interfaces\SubscriptionCheckoutRedirectInterface;
use Opcodes\Spike\Stripe\Listeners\StripeWebhookListener;
use Opcodes\Spike\Stripe\PaymentGateway as StripePaymentGateway;
use Opcodes\Spike\Stripe\Subscription as StripeSubscription;
use Opcodes\Spike\Stripe\SubscriptionItem as StripeSubscriptionItem;
use Opcodes\Spike\View\Components\Layout;
use Opcodes\Spike\View\Components\UsageChart;

class SpikeServiceProvider extends ServiceProvider
{
    public static ?PaymentProvider $_cached_payment_provider;

    protected static bool $shouldRegisterVendorMigrations = false;

    public static function basePath(string $path): string
    {
        return __DIR__.'/..'.$path;
    }

    public static function paymentProvider(): PaymentProvider
    {
        if (! isset(static::$_cached_payment_provider)) {
            static::$_cached_payment_provider = match (true) {
                class_exists(\Laravel\Cashier\Cashier::class) => PaymentProvider::Stripe,
                class_exists(\Laravel\Paddle\Cashier::class) => PaymentProvider::Paddle,
                class_exists(\Opcodes\Spike\Asaas\PaymentGateway::class) => PaymentProvider::Asaas,
                default => PaymentProvider::None,
            };
        }

        return static::$_cached_payment_provider;
    }

    public static function shouldRegisterVendorMigrations(bool $shouldLoad = true): void
    {
        self::$shouldRegisterVendorMigrations = $shouldLoad;
    }

    public function register(): void
    {
        $this->configure();

        $this->app->bind('spike', fn () => new SpikeManager());

        match (static::paymentProvider()) {
            PaymentProvider::Stripe => $this->registerCashierStripe(),
            PaymentProvider::Paddle => $this->registerCashierPaddle(),
            PaymentProvider::Asaas => $this->registerCashierAsaas(),
            default => null,
        };
    }

    protected function registerCashierStripe(): void
    {
        $this->app->bind('spike.payment-gateway', StripePaymentGateway::class);

        if (! $this->app->bound(SubscriptionCheckoutRedirectInterface::class)) {
            $this->app->bind(SubscriptionCheckoutRedirectInterface::class, SubscriptionCheckoutRedirect::class);
        }

        // We'll register the routes ourselves, see `registerRoutes()` below
        \Laravel\Cashier\Cashier::ignoreRoutes();

        \Laravel\Cashier\Cashier::useSubscriptionModel(StripeSubscription::class);
        \Laravel\Cashier\Cashier::useSubscriptionItemModel(StripeSubscriptionItem::class);

        if (count($models = config('spike.billable_models')) === 1) {
            \Laravel\Cashier\Cashier::useCustomerModel($models[0]);
        }
    }

    protected function registerCashierPaddle(): void
    {
        $this->app->bind('spike.payment-gateway', PaddlePaymentGateway::class);

        \Laravel\Paddle\Cashier::useSubscriptionModel(PaddleSubscription::class);
        \Laravel\Paddle\Cashier::useSubscriptionItemModel(PaddleSubscriptionItem::class);
        \Laravel\Paddle\Cashier::useCustomerModel(PaddleCustomer::class);
        \Laravel\Paddle\Cashier::useTransactionModel(PaddleTransaction::class);
    }

    protected function registerCashierAsaas(): void
    {
        $this->app->bind('spike.payment-gateway', \Opcodes\Spike\Asaas\PaymentGateway::class);
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerResources();
        $this->registerCommands();
        $this->registerPublishing();
        $this->bootComponents();
        $this->bootDirectives();

        if (!Spike::hasResolver()) {
            $this->registerDefaultSpikeResolver();
        }

        Spike::processCartPaymentUsing(ProcessCartPayment::class);

        foreach (config('spike.billable_models') as $model) {
            $model::observe(BillableModelObserver::class);
        }

        match (static::paymentProvider()) {
            PaymentProvider::Stripe => $this->bootCashierStripe(),
            PaymentProvider::Paddle => $this->bootCashierPaddle(),
            PaymentProvider::Asaas => $this->bootCashierAsaas(),
            default => null,
        };

        $this->macroViewTests();
    }

    protected function bootCashierStripe(): void
    {
        self::booted(function () {
            Event::listen(\Laravel\Cashier\Events\WebhookHandled::class, StripeWebhookListener::class);
        });
    }

    protected function bootCashierPaddle(): void
    {
        self::booted(function () {
            Event::listen(\Laravel\Paddle\Events\WebhookHandled::class, PaddleEventListener::class);
        });
    }

    protected function bootCashierAsaas(): void
    {
        // TODO: Register Asaas webhook listener if needed
        // For now, no event listener since Asaas webhooks are custom
    }

    protected function configure(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/spike.php', 'spike'
        );
    }

    protected function registerRoutes(): void
    {
        if ($spikePath = config('spike.path')) {
            Route::group([
                'namespace' => 'Opcodes\Spike\Http\Controllers',
                'middleware' => config('spike.middleware'),
                'prefix' => $spikePath,
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            });
        }

        if (static::paymentProvider()->isStripe()) {
            Route::group([
                'prefix' => config('cashier.path'),
                'namespace' => 'Opcodes\Spike\Http\Controllers',
                'as' => 'cashier.',
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/stripe.php');
            });
        }
    }

    protected function registerResources(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'spike');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'spike');
    }

    protected function registerMigrations(): void
    {
        if (self::$shouldRegisterVendorMigrations) {
            $this->loadMigrationsFrom(array_filter([
                match (static::paymentProvider()) {
                    PaymentProvider::Stripe => __DIR__.'/../database/migrations/stripe',
                    PaymentProvider::Paddle => __DIR__.'/../database/migrations/paddle',
                    PaymentProvider::Asaas => __DIR__.'/../database/migrations/asaas',
                    default => null,
                },
                __DIR__.'/../database/migrations',
            ]));
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                UpdateCommand::class,
                RenewSubscriptionProvidablesCommand::class,
                RenewSubscriptionCreditsCommand::class,
                SyncProvidablesCommand::class,
                ClearCreditsCacheCommand::class,
                GenerateFakeDataCommand::class,
                VerifyCommand::class,
                PublishCommand::class,
            ]);
        }
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/spike.php' => $this->app->configPath('spike.php'),
            ], 'spike-config');

            $databaseMigrations = [
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ];

            if (static::paymentProvider() === PaymentProvider::Stripe) {
                $databaseMigrations[__DIR__.'/../database/migrations/stripe'] = $this->app->databasePath('migrations');
            } elseif (static::paymentProvider() === PaymentProvider::Paddle) {
                $databaseMigrations[__DIR__.'/../database/migrations/paddle'] = $this->app->databasePath('migrations');
            } elseif (static::paymentProvider() === PaymentProvider::Asaas) {
                $databaseMigrations[__DIR__.'/../database/migrations/asaas'] = $this->app->databasePath('migrations');
            }

            $this->publishes($databaseMigrations, 'spike-migrations');

            $this->publishes([
                __DIR__.'/../resources/lang' => $this->app->resourcePath('lang/vendor/spike'),
            ], 'spike-translations');

            $this->publishes([
                __DIR__.'/../resources/dist' => $this->app->basePath('public/vendor/spike'),
            ], ['spike-assets', 'laravel-assets']);

            $this->publishes([
                __DIR__.'/../resources/views/components/layout.blade.php' => $this->app->resourcePath('views/vendor/spike/components/layout.blade.php'),
            ], 'spike-layout');

            $this->publishes([
                __DIR__.'/../resources/views/components/desktop' => $this->app->resourcePath('views/vendor/spike/components/desktop'),
                __DIR__.'/../resources/views/components/mobile' => $this->app->resourcePath('views/vendor/spike/components/mobile'),
                __DIR__.'/../resources/views/components/shared' => $this->app->resourcePath('views/vendor/spike/components/shared'),
            ], 'spike-components');
        }
    }

    protected function bootComponents(): void
    {
        Blade::component('spike::layout', Layout::class);
        Blade::component('spike::usage-chart', UsageChart::class);
        Blade::anonymousComponentNamespace('spike::components', 'spike');

        if (class_exists(Livewire::class)) {
            // Usage
            Livewire::component('spike::credit-transactions', CreditTransactions::class);

            // Get credits
            Livewire::component('spike::subscriptions', Subscriptions::class);
            Livewire::component('spike::purchase-credits', PurchaseCredits::class);
            Livewire::component('spike::validate-cart', ValidateCart::class);

            // Billing
            Livewire::component('spike::stripe-payment-methods', StripePaymentMethods::class);
            Livewire::component('spike::paddle-payment-method', PaddlePaymentMethod::class);
            Livewire::component('spike::invoices', Invoices::class);

            // Modals
            Livewire::component('spike::checkout', CheckoutModal::class);
            Livewire::component('spike::subscribe', SubscribeModal::class);
            Livewire::component('spike::add-payment-method', AddPaymentMethodModal::class);

            // Extra
            Livewire::component('spike::update-payment-method-paddle', UpdatePaymentMethodPaddle::class);
            Livewire::component('spike::product-checkout-button-paddle', PaddleCheckoutButton::class);
        }
    }

    protected function bootDirectives()
    {
        Blade::directive('spikeJS', function () {
            return "<?php echo view('spike::js'); ?>";
        });
    }

    protected function registerDefaultSpikeResolver(): void
    {
        if (config('spike.billable_models')[0] === 'App\\Models\\Team' && config('jetstream')) {
            // Using Jetstream Teams, let's resolve the current team
            Spike::resolve(function (Request $request) {
                $user = optional($request)->user() ?: Auth::user();

                if (method_exists($user, 'currentTeam')) {
                    return $user->currentTeam;
                }

                return $user;
            });
        } else {
            Spike::resolve(fn (Request $request) => optional($request)->user() ?: Auth::user());
        }
    }

    /**
     * Copy some of the view assertions from `TestResponse`
     * so that we can use them for testing components
     *
     * @see \Illuminate\Testing\TestResponse
     * @link https://laracasts.com/discuss/channels/laravel/testing-blade-components?page=1&replyId=617284
     */
    protected function macroViewTests()
    {
        if (app()->environment() !== 'testing') {
            return;
        }

        View::macro('assertViewIs', function($value) {
            PHPUnit::assertEquals($value, $this->getName());

            return $this;
        });

        View::macro('assertViewHas', function($key, $value = null) {
            if (is_array($key)) {
                return $this->assertViewHasAll($key);
            }

            if (is_null($value)) {
                PHPUnit::assertArrayHasKey($key, $this->gatherData());
            } elseif ($value instanceof \Closure) {
                PHPUnit::assertTrue($value(Arr::get($this->gatherData(), $key)));
            } elseif ($value instanceof Model) {
                PHPUnit::assertTrue($value->is(Arr::get($this->gatherData(), $key)));
            } else {
                PHPUnit::assertSame($value, Arr::get($this->gatherData(), $key));
            }

            return $this;
        });

        View::macro('assertViewMissing', function($key) {
             PHPUnit::assertArrayNotHasKey($key, $this->gatherData());

            return $this;
        });
    }
}
