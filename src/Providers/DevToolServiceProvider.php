<?php

namespace Botble\DevTool\Providers;

use Botble\Base\Supports\ServiceProvider;

class DevToolServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (version_compare('6.8.2', get_core_version(), '>')) {
            return;
        }

        $this->app->register(CommandServiceProvider::class);
    }
}
