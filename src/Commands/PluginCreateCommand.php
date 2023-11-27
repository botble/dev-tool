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
            $this->components->error(sprintf('A plugin named [%s] already exists.', $plugin));

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $location);
        File::copy(__DIR__ . '/../../stubs/plugin/plugin.json', sprintf('%s/plugin.json', $location));
        File::copy(__DIR__ . '/../../stubs/plugin/Plugin.stub', sprintf('%s/src/Plugin.php', $location));
        $this->renameFiles($plugin, $location);
        $this->searchAndReplaceInFiles($plugin, $location);
        $this->removeUnusedFiles($location);

        $this->components->info(
            sprintf('<info>The plugin</info> <comment>%s</comment> <info>was created in</info> <comment>%s</comment><info>, customize it!</info>', $plugin, $location)
        );

        $this->call('cache:clear');

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../stubs/module';
    }

    protected function removeUnusedFiles(string $location): void
    {
        File::delete(sprintf('%s/composer.json', $location));
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
            '{Module}' => str($replaceText)
                ->replace('/', '\\')
                ->afterLast('\\')
                ->studly()
                ->prepend('Botble\\'),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The plugin name that you want to create');
    }
}
