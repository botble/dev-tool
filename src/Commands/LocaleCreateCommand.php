<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Helper;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:locale:create', 'Create a new locale')]
class LocaleCreateCommand extends Command implements PromptsForMissingInput
{
    public function handle(): int
    {
        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('locale'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $defaultLocale = lang_path('en');
        if ($this->laravel['files']->exists($defaultLocale)) {
            $this->laravel['files']->copyDirectory($defaultLocale, lang_path($this->argument('locale')));
            $this->components->info(sprintf('Created: %s', lang_path($this->argument('locale'))));
        }

        foreach (['core', 'packages', 'plugins'] as $name) {
            $this->createLocaleInPath(
                lang_path(Helper::joinPaths(['vendor', $name]))
            );
        }

        return self::SUCCESS;
    }

    protected function createLocaleInPath(string $path): int
    {
        if (! $this->laravel['files']->isDirectory($path)) {
            return self::SUCCESS;
        }

        $folders = $this->laravel['files']->directories($path);

        foreach ($folders as $module) {
            foreach ($this->laravel['files']->directories($module) as $locale) {
                if ($this->laravel['files']->name($locale) == 'en') {
                    $this->laravel['files']->copyDirectory(
                        $locale,
                        $destination = Helper::joinPaths([$module, $this->argument('locale')])
                    );
                    $this->components->info('Created: ' . $destination);
                }
            }
        }

        return count($folders);
    }

    protected function configure(): void
    {
        $this->addArgument('locale', InputArgument::REQUIRED, 'The locale name that you want to create');
    }
}
