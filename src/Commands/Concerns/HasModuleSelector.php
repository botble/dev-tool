<?php

namespace Botble\DevTool\Commands\Concerns;

use Botble\DevTool\Helper;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;

trait HasModuleSelector
{
    protected string $module;

    public function promptModule(): string
    {
        $modules = glob(
            platform_path(Helper::joinPaths(['*', '*'])),
            GLOB_ONLYDIR
        );
        $choices = array_map(fn (string $module): string => str_replace(platform_path(), '', $module), $modules);

        $module = $this->argument('module');

        if (empty($module)) {
            if (windows_os()) {
                $module = select('Select which module you want to create:', $choices, scroll: 15);
            } else {
                $module = search(
                    label: 'Which module you want to create?',
                    options: fn ($search) => array_values(array_filter(
                        $choices,
                        fn ($choice) => str_contains(strtolower($choice), strtolower($search))
                    )),
                    placeholder: 'Search...',
                    scroll: 15,
                );
            }
        }

        if (! in_array($module, $choices)) {
            $this->components->error('Module not found.');

            exit(1);
        }

        $this->module = $module;

        return $module;
    }

    public function getModule(): string
    {
        if (isset($this->module)) {
            return $this->module;
        }

        return $this->promptModule();
    }

    public function transformModuleToNamespace(): string
    {
        return str($this->module)
            ->replace(DIRECTORY_SEPARATOR, '\\')
            ->afterLast('\\')
            ->studly()
            ->prepend('Botble\\');
    }
}
