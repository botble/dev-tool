<?php

namespace Botble\DevTool\Commands\Concerns;

use function Laravel\Prompts\info;

trait HasSubModule
{
    protected function handleReplacements(string $location, array $replacements): void
    {
        foreach ($replacements as $replacement => $destination) {
            $this->components->info(
                sprintf('Add below code into %s', $this->replacementSubModule(
                    null,
                    sprintf('[%s/%s]', str_replace(base_path(), '', $location), $destination)
                ))
            );

            info($this->replacementSubModule($replacement));
        }
    }

    protected function replacementSubModule(string $file = null, $content = null): string
    {
        $name = strtolower($this->argument('name'));

        if ($file && empty($content)) {
            $content = file_get_contents(sprintf('%s/../sub-module/%s', $this->getStub(), $file));
        }

        $replace = $this->getReplacements($name) + $this->baseReplacements($name);

        return str_replace(array_keys($replace), $replace, $content);
    }
}
