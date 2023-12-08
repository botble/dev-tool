<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Commands\Concerns\HasSubModule;
use Botble\DevTool\Helper;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:package:make:crud', 'Create a CRUD inside a package')]
class PackageMakeCrudCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use HasSubModule;

    public function handle(): int
    {
        if (
            ! preg_match('/^[a-z0-9\-]+$/i', $this->argument('package'))
            || ! preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))
        ) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $package = strtolower($this->argument('package'));
        $location = package_path($package);

        if (! $this->laravel['files']->isDirectory($location)) {
            $this->components->error(sprintf('Plugin named [%s] does not exists.', $package));

            return self::FAILURE;
        }

        $name = strtolower($this->argument('name'));

        $this->publishStubs($this->getStub(), $location);
        $this->removeUnusedFiles($location);
        $this->renameFiles($name, $location);
        $this->searchAndReplaceInFiles($name, $location);

        $this->components->info(
            sprintf(
                '<info>The CRUD for package </info> <comment>%s</comment> <info>was created in</info> <comment>%s</comment><info>, customize it!</info>',
                $package,
                $location
            )
        );

        $this->call('cache:clear');

        $this->handleReplacements($location, [
            Helper::joinPaths(['config', 'permissions.stub']),
            Helper::joinPaths(['helpers', 'helpers.stub']),
            Helper::joinPaths(['routes', 'web.stub']),
            Helper::joinPaths(['src', 'Providers', '{Module}ServiceProvider.stub']),
        ]);

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return dirname(__DIR__, 3) .
            DIRECTORY_SEPARATOR .
            Helper::joinPaths(['dev-tool', 'stubs', 'module']);
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
            $this->laravel['files']->delete(sprintf('%s%s%s', $location, DIRECTORY_SEPARATOR, $file));
        }
    }

    public function getReplacements(string $replaceText): array
    {
        $module = strtolower($this->argument('package'));

        return [
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
        $this->addArgument('package', InputArgument::REQUIRED, 'The package name');
        $this->addArgument('name', InputArgument::REQUIRED, 'The CRUD name');
    }
}
