<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Commands\Concerns\HasSubModule;
use Botble\DevTool\Helper;
use Botble\PluginManagement\Commands\Concern\HasPluginNameValidation;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:plugin:make:crud', 'Create a CRUD inside a plugin')]
class PluginMakeCrudCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use HasPluginNameValidation;
    use HasSubModule;

    public function handle(): int
    {
        $plugin = strtolower($this->argument('plugin'));

        $this->validatePluginName($plugin);

        $location = plugin_path($plugin);

        if (! File::isDirectory($location)) {
            $this->components->error(sprintf('Plugin named [%s] does not exists.', $plugin));

            return self::FAILURE;
        }

        $name = strtolower($this->argument('name'));
        $this->publishStubs($this->getStub(), $location);
        $this->removeUnusedFiles($location);
        $this->renameFiles($name, $location);
        $this->searchAndReplaceInFiles($name, $location);

        $this->components->info(
            sprintf('<info>The CRUD for plugin </info> <comment>%s</comment> <info>was created in</info> <comment>%s</comment><info>, customize it!</info>', $plugin, $location)
        );

        $this->call('cache:clear');

        $this->handleReplacements($location, [
            Helper::joinPaths(['config', 'permissions.stub']),
            Helper::joinPaths(['helpers', 'helpers.stub']),
            Helper::joinPaths(['routes', 'web.stub']),
            Helper::joinPaths(['src', 'Providers', '{Module}ServiceProvider.stub']),
            Helper::joinPaths(['src', 'Plugin.stub']),
        ]);

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return Helper::joinPaths([dirname(__DIR__, 3), 'dev-tool', 'stubs', 'module']);
    }

    protected function removeUnusedFiles(string $location): void
    {
        $files = [
            Helper::joinPaths(['config', 'permissions.stub']),
            Helper::joinPaths(['helpers', 'constants.stub']),
            Helper::joinPaths(['routes', 'web.stub']),
            Helper::joinPaths(['src', 'Providers', '{Module}ServiceProvider.stub']),
        ];

        foreach ($files as $file) {
            File::delete(Helper::joinPaths([$location, $file]));
        }
    }

    public function getReplacements(string $replaceText): array
    {
        $module = strtolower($this->argument('plugin'));

        return [
            '{type}' => 'plugin',
            '{types}' => 'plugins',
            '{-module}' => strtolower($module),
            '{module}' => Str::snake(str_replace('-', '_', $module)),
            '{+module}' => Str::camel($module),
            '{modules}' => Str::plural(Str::snake(str_replace('-', '_', $module))),
            '{Modules}' => ucfirst(Str::plural(Str::snake(str_replace('-', '_', $module)))),
            '{-modules}' => Str::plural($module),
            '{MODULE}' => strtoupper(Str::snake(str_replace('-', '_', $module))),
            '{Module}' => ucfirst(Str::camel($module)),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('plugin', InputArgument::REQUIRED, 'The plugin name');
        $this->addArgument('name', InputArgument::REQUIRED, 'The CRUD name');
    }
}
