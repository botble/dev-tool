<?php

namespace Botble\DevTool\Commands\Concerns;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;

trait HasSettingModulePrompt
{
    public function promptModule(): string
    {
        $modules = glob(platform_path('*/*'), GLOB_ONLYDIR);
        $choices = array_map(fn (string $module): string => str_replace(platform_path(), '', $module), $modules);

        return $this->argument('module') ?? (windows_os() ? select('Select which module you want to create setting for', $choices, scroll: 15) : search(
            label: 'Which module you want to create setting for?',
            placeholder: 'Search...',
            options: fn ($search) => array_values(array_filter(
                $choices,
                fn ($choice) => str_contains(strtolower($choice), strtolower($search))
            )),
            scroll: 15,
        ));
    }
}
