<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Tokyo',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'ja',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'ja',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'ja_JP',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'Date' => Illuminate\Support\Facades\Date::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Js' => Illuminate\Support\Js::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'RateLimiter' => Illuminate\Support\Facades\RateLimiter::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        // 'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,

    ],

    'shopUrl' => env('SHOP_URL', 'your-store-url'),
    'apiKey' => env('SHOP_API_KEY', 'your-app-api-key'),
    'secretKey' => env('SHOP_SECRET_KEY', 'your-app-secret-key'),

    /**
     * read_content, write_content
     * ブログ、ページ、コメント、リダイレクトデータを読み書きできる権限
     *
     * read_themes, write_themes
     * テーマデータを読み書きできる権限
     *
     * read_products, write_products
     * 商品データ（商品、バリアント、イメージ、コレクション）を読み書きできる権限
     *
     * read_product_listings
     * 商品リスト、コレクションリストを読むことができる権限
     *
     * read_customers, write_customers
     * 顧客データ（顧客検索項目データ）の読み書きできる権限
     *
     * read_orders, write_orders
     * オーダーデータの読み書き権限
     *
     * read_all_orders
     * 60日より前のデータ（全オーダーデータ）を読むことができる権限。この権限を必要とする場合は取得する必要性とともに別途申請する必要があります。
     *
     * read_draft_orders, write_draft_orders
     * ドラフトオーダーデータの読み書きできる権限
     *
     * read_inventory, write_inventory
     * 在庫データの読み書きできる権限
     *
     * read_locations
     * ロケーションデータを読むことができる権限
     *
     * read_script_tags, write_script_tags
     * スクリプトタグデータの読み書きできる権限
     *
     * read_fulfillments, write_fulfillments
     * フルフィルメントデータの読みことができる権限
     *
     * read_assigned_fulfillment_orders, write_assigned_fulfillment_orders
     * 自身のフルフィルメントオーダーデータの読み書きできる権限
     *
     * read_merchant_managed_fulfillment_orders, write_merchant_managed_fulfillment_orders
     * マーチャント管理フルフィルメントオーダーデータの読み書きできる権限
     *
     * read_third_party_fulfillment_orders, write_third_party_fulfillment_orders
     * サードパーティーフルフィルメントオーダーデータの読み書きできる権限
     *
     * read_shipping, write_shipping
     * 配送会社、国、地域の読み書きできる権限
     *
     * read_analytics
     * 分析データを読むことができる権限
     *
     * read_users, write_users
     * Shopifyユーザーデータの読み書きできる権限
     *
     * read_checkouts, write_checkouts
     * チェックアウトデータの読み書きできる権限
     *
     * read_reports, write_reports
     * レポートデータの読み書きできる権限
     *
     * read_price_rules, write_price_rules
     * プライスルールデータの読み書きできる権限
     *
     * read_discounts, write_discounts
     * ディスカウントデータの読み書きできる権限（GraphQLに限定のようです、後で確認します。）
     *
     * read_marketing_events, write_marketing_events
     * マーケティングイベントデータの読み書きできる権限
     *
     * read_resource_feedbacks, write_resource_feedbacks
     * アプリに関するリソースを通知するデータの読み書きできる権限
     *
     * read_shopify_payments_payouts
     * Shopify Payments の支払い、バランス、トランザクションデータを読むことができる権限
     *
     * read_shopify_payments_disputes
     * Shopify PaymentAccessへのクレームデータを読むことができる権限
     *
     * read_translations, write_translations
     * 翻訳データの読み書き権限
     *
     * read_locales, write_locales
     * ショップの地域言語設定データの読み書きできる権限
     *
     * 参考
     * https://qiita.com/syantien/items/7fe9afca71596694f598
     */
    'scopes' => env('SHOP_API_ALLOW_SCOPE', 'read_content,write_content,read_orders'),
];
