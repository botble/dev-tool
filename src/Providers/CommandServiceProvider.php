<?php

namespace Tiryaq\DevTool\Providers;

use Tiryaq\Base\Supports\ServiceProvider;
use Tiryaq\DevTool\Commands\LocaleCreateCommand;
use Tiryaq\DevTool\Commands\LocaleRemoveCommand;
use Tiryaq\DevTool\Commands\Make\ControllerMakeCommand;
use Tiryaq\DevTool\Commands\Make\FormMakeCommand;
use Tiryaq\DevTool\Commands\Make\ModelMakeCommand;
use Tiryaq\DevTool\Commands\Make\PanelSectionMakeCommand;
use Tiryaq\DevTool\Commands\Make\RequestMakeCommand;
use Tiryaq\DevTool\Commands\Make\RouteMakeCommand;
use Tiryaq\DevTool\Commands\Make\SettingControllerMakeCommand;
use Tiryaq\DevTool\Commands\Make\SettingFormMakeCommand;
use Tiryaq\DevTool\Commands\Make\SettingMakeCommand;
use Tiryaq\DevTool\Commands\Make\SettingRequestMakeCommand;
use Tiryaq\DevTool\Commands\Make\TableMakeCommand;
use Tiryaq\DevTool\Commands\PackageCreateCommand;
use Tiryaq\DevTool\Commands\PackageMakeCrudCommand;
use Tiryaq\DevTool\Commands\PackageRemoveCommand;
use Tiryaq\DevTool\Commands\PluginCreateCommand;
use Tiryaq\DevTool\Commands\PluginMakeCrudCommand;
use Tiryaq\DevTool\Commands\RebuildPermissionsCommand;
use Tiryaq\DevTool\Commands\TestSendMailCommand;
use Tiryaq\DevTool\Commands\ThemeCreateCommand;
use Tiryaq\DevTool\Commands\WidgetCreateCommand;
use Tiryaq\DevTool\Commands\WidgetRemoveCommand;
use Tiryaq\PluginManagement\Providers\PluginManagementServiceProvider;
use Tiryaq\Theme\Providers\ThemeServiceProvider;
use Tiryaq\Widget\Providers\WidgetServiceProvider;

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
            PackageCreateCommand::class,
            PackageMakeCrudCommand::class,
            PackageRemoveCommand::class,
            TestSendMailCommand::class,
            RebuildPermissionsCommand::class,
            LocaleRemoveCommand::class,
            LocaleCreateCommand::class,
        ]);

        if (version_compare(get_core_version(), '7.0.0', '>=')) {
            $this->commands([
                PanelSectionMakeCommand::class,
                SettingControllerMakeCommand::class,
                SettingRequestMakeCommand::class,
                SettingFormMakeCommand::class,
                SettingMakeCommand::class,
            ]);
        }

        if (class_exists(PluginManagementServiceProvider::class)) {
            $this->commands([
                PluginCreateCommand::class,
                PluginMakeCrudCommand::class,
            ]);
        }

        if (class_exists(ThemeServiceProvider::class)) {
            $this->commands([
                ThemeCreateCommand::class,
            ]);
        }

        if (class_exists(WidgetServiceProvider::class)) {
            $this->commands([
                WidgetCreateCommand::class,
                WidgetRemoveCommand::class,
            ]);
        }
    }
}
