<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $generator)
    {
        $generator->forceScheme('https');
        $generator->forceRootUrl(config('app.url'));

        // 'export_type' => ['required', 'equal:fugafuga|aaa|bbb'], みたいな感じに使う
        Validator::extend('equal', function ($attrivute, $value, $parameters, $validator) {
            $returnBool = false;

            $parametersAll = explode('|', $parameters[0]);
            if (is_array($parametersAll) && 0 < count($parametersAll)) {
                foreach ($parametersAll as $param) {
                    if (!$param) continue;
                    if ($value === $param) {
                        $returnBool = true;
                        break;
                    }
                }
            }

            return $returnBool;
        });
    }
}
