<?php

namespace Botble\DevTool\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\DevTool\Commands\LocaleCreateCommand;
use Botble\DevTool\Commands\LocaleRemoveCommand;
use Botble\DevTool\Commands\Make\ControllerMakeCommand;
use Botble\DevTool\Commands\Make\FormMakeCommand;
use Botble\DevTool\Commands\Make\ModelMakeCommand;
use Botble\DevTool\Commands\Make\RepositoryMakeCommand;
use Botble\DevTool\Commands\Make\RequestMakeCommand;
use Botble\DevTool\Commands\Make\RouteMakeCommand;
use Botble\DevTool\Commands\Make\TableMakeCommand;
use Botble\DevTool\Commands\PackageCreateCommand;
use Botble\DevTool\Commands\PackageMakeCrudCommand;
use Botble\DevTool\Commands\PackageRemoveCommand;
use Botble\DevTool\Commands\PluginCreateCommand;
use Botble\DevTool\Commands\PluginMakeCrudCommand;
use Botble\DevTool\Commands\RebuildPermissionsCommand;
use Botble\DevTool\Commands\TestSendMailCommand;
use Botble\DevTool\Commands\ThemeCreateCommand;
use Botble\DevTool\Commands\WidgetCreateCommand;
use Botble\DevTool\Commands\WidgetRemoveCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            TableMakeCommand::class,
            ControllerMakeCommand::class,
            RouteMakeCommand::class,
            RequestMakeCommand::class,
            FormMakeCommand::class,
            ModelMakeCommand::class,
            RepositoryMakeCommand::class,
            PackageCreateCommand::class,
            PackageMakeCrudCommand::class,
            PackageRemoveCommand::class,
            TestSendMailCommand::class,
            RebuildPermissionsCommand::class,
            LocaleRemoveCommand::class,
            LocaleCreateCommand::class,
        ]);

        if (class_exists(\Botble\PluginManagement\Providers\PluginManagementServiceProvider::class)) {
            $this->commands([
                PluginCreateCommand::class,
                PluginMakeCrudCommand::class,
            ]);
        }

        if (class_exists(\Botble\Theme\Providers\ThemeServiceProvider::class)) {
            $this->commands([
                ThemeCreateCommand::class,
            ]);
        }

        if (class_exists(\Botble\Widget\Providers\WidgetServiceProvider::class)) {
            $this->commands([
                WidgetCreateCommand::class,
                WidgetRemoveCommand::class,
            ]);
        }
    }
}
