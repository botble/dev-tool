<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Commands\Concerns\HasModuleSelector;
use Botble\DevTool\Commands\Concerns\HasSubModule;
use Botble\DevTool\Helper;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'cms:make:setting', description: 'Make new setting resource (controller, form request, form builder)')]
class SettingMakeCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use HasModuleSelector;
    use HasSubModule;

    public function handle(): int
    {
        $module = $this->promptModule();

        $this->call('cms:make:setting:form', [
            'name' => $this->argument('name'),
            'module' => $module,
        ]);
        $this->call('cms:make:setting:request', [
            'name' => $this->argument('name'),
            'module' => $module,
        ]);
        $this->call('cms:make:setting:controller', [
            'name' => $this->argument('name'),
            'module' => $module,
        ]);

        $this->handleReplacements(platform_path($module), [
            Helper::joinPaths(['routes', 'web-settings.stub']),
            Helper::joinPaths(['src', 'PanelSections', 'PanelSection.stub'])
                => Helper::joinPaths(['src', 'Providers', '{Module}ServiceProvider.stub']),
        ]);

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the setting (e.g. BlogSetting)')
            ->addArgument('module', InputArgument::OPTIONAL, 'The name of the module (e.g. plugins/blog)');
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{-module}' => strtolower($this->getModule()),
            '{Module}' => str($this->getModule())->afterLast('/')->studly(),
        ];
    }

    public function getStub(): string
    {
        return Helper::joinPaths([
            dirname(__DIR__, 3),
            'stubs',
            'module',
        ]);
    }
}
