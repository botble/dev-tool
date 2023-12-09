<?php

namespace Botble\DevTool\Commands\Concerns;

use Botble\DevTool\Helper;

use function Laravel\Prompts\info;

trait HasSubModule
{
    protected function handleReplacements(string $location, array $replacements): void
    {
        foreach ($replacements as $replacement => $destination) {
            $this->components->info(
                sprintf('Add below code into [%s]', $this->replacementSubModule(
                    null,
                    Helper::joinPaths([str_replace(base_path(), '', $location), $destination])
                ))
            );

            info($this->replacementSubModule($destination));
        }
    }

    protected function replacementSubModule(string $file = null, $content = null): string
    {
        $name = strtolower($this->argument('name'));

        if ($file && empty($content)) {
            $content = file_get_contents(
                Helper::joinPaths([$this->getStub(), '..', 'sub-module', $file])
            );
        }

        $replace = $this->getReplacements($name) + $this->baseReplacements($name);

        return str_replace(array_keys($replace), $replace, $content);
    }
}
