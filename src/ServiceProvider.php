<?php

namespace Haruncpi\LaravelOptionFramework;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config';
    const MIGRATION_PATH = __DIR__ . '/../migrations';
    const ROUTE_PATH = __DIR__ . '/../routes';
    const VIEW_PATH = __DIR__ . '/../views';
    const ASSET_PATH = __DIR__ . '/../assets';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path()
        ], 'config');

        $this->publishes([
            self::MIGRATION_PATH => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            self::ASSET_PATH => public_path('option-framework')
        ], 'assets');

        $this->loadRoutesFrom(self::ROUTE_PATH . '/web.php');
        $this->loadViewsFrom(self::VIEW_PATH, 'OptionFramework');

    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH . '/option-framework.php',
            'option-framework'
        );
    }
}
