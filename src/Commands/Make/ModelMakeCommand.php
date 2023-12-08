<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Commands\Concerns\HasModuleSelector;
use Botble\DevTool\Helper;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:make:model', 'Make a model')]
class ModelMakeCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use HasModuleSelector;

    public function handle(): int
    {
        if (! preg_match('/^[a-z0-9\-_]+$/i', $this->argument('name'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $name = $this->argument('name');
        $path = platform_path(
            Helper::joinPaths([$this->getModule(), 'src', 'Models', ucfirst(Str::studly($name)) . '.php'])
        );

        $this->publishStubs($this->getStub(), $path);
        $this->renameFiles($name, $path);
        $this->searchAndReplaceInFiles($name, $path);

        $this->components->info(sprintf('Created successfully <comment>%s</comment>!', $path));

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return Helper::joinPaths([
            dirname(__DIR__, 3),
            'stubs',
            'module',
            'src',
            'Models',
            '{Name}.stub',
        ]);
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{Module}' => $this->transformModuleToNamespace(),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The model name that you want to create');
        $this->addArgument('module', InputArgument::OPTIONAL, 'The module name');
    }
}
