<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
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
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Http::macro('accessToken', function () {
            $token = Http::post('https://devapi.graphiclead.com/lv1/login', [
                'username' => 'peter50',
                'password' => 'password',
            ])['token'];
            return $token;
        });
    }
}
