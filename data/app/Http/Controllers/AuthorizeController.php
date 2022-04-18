<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPShopify\AuthHelper;
use PHPShopify\ShopifySDK;


class AuthorizeController extends Controller
{
    public function index()
    {
        new ShopifySDK([
            'ShopUrl' => config('app.shopUrl'),
            'ApiKey' => config('app.apiKey'),
            'SharedSecret' => config('app.secretKey')
        ]);
        $scopes = config('app.scopes');
        $redirectUrl = config('app.url') . '/authRedirect';
        AuthHelper::createAuthRequest($scopes, $redirectUrl);

        exit;
    }
}
