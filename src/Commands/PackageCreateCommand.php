<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Helper;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:package:create', 'Create a new package.')]
class PackageCreateCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    public function handle(): int
    {
        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $package = strtolower($this->argument('name'));
        $location = package_path($package);

        if ($this->laravel['files']->isDirectory($location)) {
            $this->components->error(sprintf('A package named [%s] already exists.', $package));

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $location);
        $this->renameFiles($package, $location);
        $this->searchAndReplaceInFiles($package, $location);

        $this->components->info(
            sprintf('<info>The package</info> <comment>%s</comment> <info>was created in</info> <comment>%s</comment><info>, customize it!</info>', $package, $location)
        );
        $this->components->info(
            sprintf('<info>Add</info> <comment>"botble/%s": "*@dev"</comment> to composer.json then run <comment>composer update</comment> to install this package!', $package)
        );

        $this->call('cache:clear');

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return dirname(__DIR__, 2) .
            DIRECTORY_SEPARATOR .
            Helper::joinPaths(['stubs', 'module']);
    }

    public function getReplacements(string $replaceText): array
    {
        return [
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
        $this->addArgument('name', InputArgument::REQUIRED, 'The package name that you want to create');
    }
}
