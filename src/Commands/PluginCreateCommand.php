<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\PluginManagement\Commands\Concern\HasPluginNameValidation;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:plugin:create', 'Create a plugin in the /platform/plugins directory.')]
class PluginCreateCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use HasPluginNameValidation;

    public function handle(): int
    {
        $plugin = strtolower($this->argument('name'));

        $this->validatePluginName($plugin);

        $location = plugin_path($plugin);

        if (File::isDirectory($location)) {
            $this->components->error('A plugin named [' . $plugin . '] already exists.');

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $location);
        File::copy(__DIR__ . '/../../stubs/plugin/plugin.json', $location . '/plugin.json');
        File::copy(__DIR__ . '/../../stubs/plugin/Plugin.stub', $location . '/src/Plugin.php');
        $this->renameFiles($plugin, $location);
        $this->searchAndReplaceInFiles($plugin, $location);
        $this->removeUnusedFiles($location);
        $this->line('------------------');
        $this->line(
            '<info>The plugin</info> <comment>' . $plugin . '</comment> <info>was created in</info> <comment>' . $location . '</comment><info>, customize it!</info>'
        );
        $this->line('------------------');
        $this->call('cache:clear');

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../stubs/module';
    }

    protected function removeUnusedFiles(string $location): void
    {
        File::delete($location . '/composer.json');
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{type}' => 'plugin',
            '{types}' => 'plugins',
            '{-module}' => strtolower($replaceText),
            '{module}' => Str::snake(str_replace('-', '_', $replaceText)),
            '{+module}' => Str::camel($replaceText),
            '{modules}' => Str::plural(Str::snake(str_replace('-', '_', $replaceText))),
            '{Modules}' => ucfirst(Str::plural(Str::snake(str_replace('-', '_', $replaceText)))),
            '{-modules}' => Str::plural($replaceText),
            '{MODULE}' => strtoupper(Str::snake(str_replace('-', '_', $replaceText))),
            '{Module}' => ucfirst(Str::camel($replaceText)),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The plugin name that you want to create');
    }
}
