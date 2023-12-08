<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Helper;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\PromptsForMissingInput;

use function Laravel\Prompts\confirm;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:locale:remove', 'Remove a locale')]
class LocaleRemoveCommand extends Command implements PromptsForMissingInput
{
    use ConfirmableTrait;

    public function handle(): int
    {
        if (! confirm('Are you sure you want to permanently delete?')) {
            return self::FAILURE;
        }

        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('locale'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $defaultLocale = lang_path($this->argument('locale'));
        if ($this->laravel['files']->exists($defaultLocale)) {
            $this->laravel['files']->deleteDirectory($defaultLocale);
            $this->components->info(sprintf('Deleted: %s', $defaultLocale));
        }

        $this->laravel['files']->delete(sprintf('%s.json', lang_path($this->argument('locale'))));

        foreach (['core', 'packages', 'plugins'] as $name) {
            $this->removeLocaleInPath(
                lang_path(Helper::joinPaths(['vendor', $name]))
            );
        }

        $this->components->info(sprintf('Removed locale "%s" successfully!', $this->argument('locale')));

        return self::SUCCESS;
    }

    protected function removeLocaleInPath(string $path): int
    {
        if (! $this->laravel['files']->isDirectory($path)) {
            return self::SUCCESS;
        }

        $folders = $this->laravel['files']->directories($path);

        foreach ($folders as $module) {
            foreach ($this->laravel['files']->directories($module) as $locale) {
                if ($this->laravel['files']->name($locale) == $this->argument('locale')) {
                    $this->laravel['files']->deleteDirectory($locale);
                    $this->components->info('Deleted: ' . $locale);
                }
            }
        }

        return count($folders);
    }

    protected function configure(): void
    {
        $this->addArgument('locale', InputArgument::REQUIRED, 'The locale name that you want to remove');
    }
}
