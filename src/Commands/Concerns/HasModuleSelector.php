<?php

namespace Botble\DevTool\Commands\Concerns;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;

trait HasModuleSeclector
{
    public function promptModule(): string
    {
        $modules = glob(platform_path('*/*'), GLOB_ONLYDIR);
        $choices = array_map(fn (string $module): string => str_replace(platform_path(), '', $module), $modules);

        $module = $this->argument('module');

        if (empty($module)) {
            if (windows_os()) {
                $module = select('Select which module you want to create:', $choices, scroll: 15);
            } else {
                $module = search(
                    label: 'Which module you want to create?',
                    placeholder: 'Search...',
                    options: fn ($search) => array_values(array_filter(
                        $choices,
                        fn ($choice) => str_contains(strtolower($choice), strtolower($search))
                    )),
                    scroll: 15,
                );
            }
        }

        if (! in_array($module, $choices)) {
            $this->components->error('Module not found.');

            exit(0);
        }

        return $module;
    }
}
