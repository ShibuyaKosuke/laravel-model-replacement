<?php

namespace ShibuyaKosuke\LaravelModelReplacement\Providers;

use Illuminate\Support\ServiceProvider;
use ShibuyaKosuke\LaravelModelReplacement\Console\ReplaceModelCommand;

/**
 * Class CommandServiceProvider
 * @package ShibuyaKosuke\LaravelModelReplacement\Providers
 */
class CommandServiceProvider extends ServiceProvider
{
    private const COMMANDS = [
        'shibuyakosuke.replace.model'
    ];

    protected $defer = true;

    public function boot()
    {
        $this->registerCommands();
    }

    protected function registerCommands()
    {
        $this->app->singleton('shibuyakosuke.replace.model', function () {
            return new ReplaceModelCommand();
        });
        $this->commands(self::COMMANDS);
    }

    public function provides()
    {
        return self::COMMANDS;
    }
}
