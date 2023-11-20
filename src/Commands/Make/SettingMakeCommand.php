<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Concerns\HasModuleSelector;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'cms:make:setting', description: 'Make new setting resource (controller, form request, form builder)')]
class SettingMakeCommand extends Command implements PromptsForMissingInput
{
    use HasModuleSelector;

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

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the setting (e.g. BlogSetting)')
            ->addArgument('module', InputArgument::OPTIONAL, 'The name of the module (e.g. plugins/blog)');
    }
}
