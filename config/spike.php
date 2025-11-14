<?php

use Opcodes\Spike\CreditAmount;

return [

    /*
    |--------------------------------------------------------------------------
    | Path to the Spike billing portal
    |--------------------------------------------------------------------------
    |
    | Here you can configure the path which the Spike billing portal
    | will be accessible on. For example, the value of "billing"
    | will make the billing portal accessible via "/billing".
    | Setting this to `null` will disable the billing portal.
    |
    */

    'path' => 'billing',

    /*
    |--------------------------------------------------------------------------
    | Spike middleware
    |--------------------------------------------------------------------------
    |
    | Here you can configure the middleware that will be applied to all
    | routes of the Spike billing portal. By default, Spike will
    | only be accessible by authenticated users.
    |
    */

    'middleware' => [
        'web',
        'auth',
    ],

    /*
    |--------------------------------------------------------------------------
    | Spike Theme
    |--------------------------------------------------------------------------
    |
    | Here you can configure the color and the logo of the Spike billing
    | portal. You should prefer dark theme colors. If you would like
    | the logo to be hidden, you can set the value to `null`.
    |
    */

    'theme' => [
        'color' => '#047857',
        'logo_url' => '/vendor/spike/images/spike-logo-white.png',

        // URL/path to the favicon (small logo in browser tab).
        'favicon_url' => '/vendor/spike/images/spike-favicon.png',

        // If you would like to change how the avatar is resolved,
        // use Spike::resolveAvatarUsing($callback) in your AppServiceProvider
        'display_avatar' => true,

        // Whether to display zero values for credit types that the user did not yet purchase.
        'display_empty_credit_balances' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Return to your app URL
    |--------------------------------------------------------------------------
    |
    | Here you can configure which URL the user can return to after
    | they are finished with the billing. If you would like the
    | link to be hidden, you can set this value to `null`.
    |
    */

    'return_url' => '/',

    /*
    |--------------------------------------------------------------------------
    | Billable models
    |--------------------------------------------------------------------------
    |
    | Here you should add all the different billable models you have in
    | your app that have subscriptions. These models will be used to
    | distribute monthly credits to active subscribers.
    |
    */

    'billable_models' => [
        'App\Models\User',
    ],

    /*
    |--------------------------------------------------------------------------
    | Credit types
    |--------------------------------------------------------------------------
    |
    | You can have multiple types of credits that you can sell and use,
    | like API credits, SMS, emails, etc. Here you can configure the
    | different credit types that will be available for users.
    |
    */

    'credit_types' => [
        [
            // ID is used when referencing the credit type in the code, i.e. `$user->credits('sms')->balance()`.
            'id' => 'credits',

            // The translation key with inflection to use for this credit type.
            'translation_key' => 'spike::translations.credits',

            // The icon for credit type. Leaving this `null` will use the default icon (coins).
            // Accepted values are: URL string (absolute or relative), SVG string, or null.
            'icon' => null,
        ],

        // [
        //     'id' => 'sms',
        //     'translation_key' => 'app.sms',
        //     'icon' => null,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Credit spend grouping
    |--------------------------------------------------------------------------
    |
    | By default, credit usage information is grouped daily. This means that
    | if a user makes 100 requests in a day, there will only be a single
    | usage record for that day. This is useful for performance, but
    | does not allow adding notes to each individual usage record.
    |
    */

    'group_credit_spend_daily' => true,

    /*
    |--------------------------------------------------------------------------
    | Process soft-deleted carts
    |--------------------------------------------------------------------------
    |
    | When enabled, webhook handlers will process carts that have been
    | soft-deleted. This can be useful when carts are deleted during
    | checkout abandonment but webhooks arrive after the user completes
    | payment. When disabled, webhooks will only process active carts.
    |
    */

    'process_soft_deleted_carts' => false,

    /*
    |--------------------------------------------------------------------------
    | Subscriptions
    |--------------------------------------------------------------------------
    |
    | Here you can configure the different subscription plans to be
    | available for your users. Below are just some examples and
    | you should change these values for your own. You can
    | learn more about configuring subscriptions here:
    |
    | https://spike.opcodes.io/docs/3.x/configuring-spike/subscriptions
    |
    */

    'subscriptions' => [
        [
            'id' => 'free',
            'name' => 'Free',
            'short_description' => 'Free plan to try out our product',
            'features' => [
                'Full access to the API',
                '10 requests per minute',
            ],
            'provides_monthly' => [
                CreditAmount::make(200),    // will default to the first credit type
                // CreditAmount::make(100, 'sms'),  // add as many providables as you want
            ],
        ],

        [
            'id' => 'standard',
            'name' => 'Standard',
            'short_description' => 'Standard plan to cover your basic needs',
            'payment_provider_price_id_monthly' => env('SPIKE_PROVIDER_PRICE_ID_STANDARD_MONTHLY'),
            'payment_provider_price_id_yearly' => env('SPIKE_PROVIDER_PRICE_ID_STANDARD_YEARLY'),
            'price_in_cents_monthly' => 10_00,
            'price_in_cents_yearly' => 100_00,
            'features' => [
                'Full access to the API',
                '60 requests per minute',
                'Priority support',
            ],
            'provides_monthly' => [
                CreditAmount::make(5_000),
            ],
        ],

        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    |
    | Here you can configure the different products (one-off purchases)
    | to be available for your users. Below are just some examples
    | and you should change these values for your own. You can
    | learn more about configuring products here:
    |
    | https://spike.opcodes.io/docs/3.x/configuring-spike/products
    |
    */

    'products' => [
        [
            'id' => '10_dollars',
            'name' => 'Standard pack',
            'short_description' => 'Great value for occasional use',
            'payment_provider_price_id' => env('SPIKE_PROVIDER_PRICE_ID_10_DOLLARS'),
            'price_in_cents' => 10_00,
            'features' => [
                '60 requests per minute',
                'Priority support',
            ],
            'provides' => [
                CreditAmount::make(5_000)    // will default to the first credit type
                    ->expiresAfterMonths(6),
                // CreditAmount::make(2_000, 'sms'), // add as many providables as you want
            ],
        ],

        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Date formats
    |--------------------------------------------------------------------------
    |
    | Here you can configure the date format patterns used across the application.
    | These formats use PHP date format strings and will be used with Carbon's
    | translatedFormat method for localization support.
    |
    */

    'date_formats' => [
        // Credit transaction formats
        'transaction_date' => 'F j, Y',               // Credit transaction date with year
        'transaction_date_current_year' => 'F j',     // Credit transaction date in current year
        'transaction_expiry_date' => 'F j, Y',        // Credit expiration date with year
        'transaction_expiry_current_year' => 'F j',   // Credit expiration date in current year
        'transaction_time' => 'g:i a',                // Time format for usage transactions
        'transaction_usage_datetime' => 'F j, H:i:s', // Usage date with time in current year
        'transaction_usage_with_year' => 'F j Y, H:i:s', // Usage date with time for previous years

        // Invoice formats
        'invoice_date' => 'F j, Y',                   // Date format on invoices

        // Subscription formats
        'subscription_end_date' => 'F j, Y',          // Subscription end date

        // Payment method formats
        'payment_method_expiry' => 'F, Y',            // Payment method expiry date
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe payment gateway configuration
    |--------------------------------------------------------------------------
    |
    | This section is used to configure the Stripe payment gateway only.
    | https://spike.opcodes.io/docs/3.x/payment-providers/stripe
    |
    */

    'stripe' => [

        /*
        |--------------------------------------------------------------------------
        | Use Stripe Checkout instead of the in-built UI
        |--------------------------------------------------------------------------
        |
        | When disabled, Spike will use the in-built checkout UI for both
        | products and subscriptions. If you would rather use
        | Stripe's Checkout UI, you can enable this.
        |
        */

        'checkout' => [
            'enabled' => false,

            // Whether to generate invoices for product purchases (one-time payments) made using Stripe Checkout.
            // Post-checkout invoices for one-time payments are not free. Please see the Stripe support article below:
            // https://support.stripe.com/questions/pricing-for-post-payment-invoices-for-one-time-purchases-via-checkout-and-payment-links
            'generate_invoices_for_products' => false,

            // For support Stripe Checkout locales, please see this link:
            // https://docs.stripe.com/api/checkout/sessions/create#create_checkout_session-locale
            'default_locale' => null,
        ],

        /*
        |--------------------------------------------------------------------------
        | Discount offers when cancelling a subscription
        |--------------------------------------------------------------------------
        |
        | Stripe gateway allows using Promotion Codes and Coupons as offers for your users
        | when they try to cancel their subscription. You can configure multiple, and
        | the user will be presented with each offer in sequence until they accept
        | one, or they run out of offers and proceed with cancellation.
        |
        */

        'cancellation_offers' => [
            // new \Opcodes\Spike\Stripe\Offers\PromotionCodeOffer(
            //     promoCodeId: 'promo_code_id_from_stripe',
            // ),

            // new \Opcodes\Spike\Stripe\Offers\CouponCodeOffer(
            //     couponId: 'coupon_id_from_stripe',
            // ),
        ],

        /*
        |--------------------------------------------------------------------------
        | Invoice details
        |--------------------------------------------------------------------------
        |
        | Here you can configure the details of the invoices downloadable
        | by your users. Invoices are created automatically for every
        | subscription and product purchase. The details below
        | will be visible on every invoice downloaded.
        |
        | https://spike.opcodes.io/docs/3.x/payment-providers/stripe#invoice-details
        |
        */

        'invoice_details' => [
            'vendor' => 'Spike',
            'product' => env('APP_NAME', 'Spike'),
            // 'street' => 'Main Str. 1',
            // 'location' => '08220 Vilnius, Lithuania',
            // 'phone' => '+370 646 00 000',
            // 'email' => 'info@example.com',
            // 'url' => 'https://example.com',
            // 'vendorVat' => 'LT123456789',
        ],

        /*
        |--------------------------------------------------------------------------
        | Allow users to enter discount codes
        |--------------------------------------------------------------------------
        |
        | Configure whether users should be able to enter discount codes.
        | Discount codes can be configured in the Stripe dashboard.
        |
        | https://spike.opcodes.io/docs/3.x/payment-providers/stripe#using-coupons
        |
        */

        'allow_discount_codes' => true,

        /*
        |--------------------------------------------------------------------------
        | Persist applied discounts when switching plans
        |--------------------------------------------------------------------------
        |
        | When enabled, the previously applied discount code will carry over
        | to the new plan when switching plans. When disabled, the discount
        | will be removed when switching plans.
        |
        */

        'persist_discounts_when_switching_plans' => true,

        /*
        |--------------------------------------------------------------------------
        | Allow incomplete subscription updates
        |--------------------------------------------------------------------------
        |
        | When enabled, subscription plan changes will proceed even if the payment
        | for price difference fails. This may result in subscriptions going into
        | a "past_due" state that may be considered inactive. When disabled,
        | plan changes will fail completely if payment cannot be completed.
        |
        */

        'allow_incomplete_subscription_updates' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Asaas payment gateway configuration
    |--------------------------------------------------------------------------
    |
    | This section is used to configure the Asaas payment gateway only.
    |
    */

    'asaas' => [

        /*
        |--------------------------------------------------------------------------
        | Asaas API Key
        |--------------------------------------------------------------------------
        |
        | Your Asaas API key. You can find it in your Asaas dashboard.
        |
        */

        'api_key' => env('ASAAS_API_KEY'),

        /*
        |--------------------------------------------------------------------------
        | Asaas Environment
        |--------------------------------------------------------------------------
        |
        | The Asaas environment to use. Can be 'sandbox' or 'production'.
        |
        */

        'environment' => env('ASAAS_ENVIRONMENT', 'sandbox'),

        /*
        |--------------------------------------------------------------------------
        | Asaas Webhook Secret
        |--------------------------------------------------------------------------
        |
        | The webhook secret for verifying Asaas webhooks.
        |
        */

        'webhook_secret' => env('ASAAS_WEBHOOK_SECRET'),

    ],

];
