<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Commands\Concerns\HasModuleSelector;
use Botble\DevTool\Helper;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:make:controller', 'Make a controller')]
class ControllerMakeCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use HasModuleSelector;

    public function handle(): int
    {
        $name = $this->argument('name');

        if (! preg_match('/^[a-z0-9\-_]+$/i', $name)) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $module = $this->promptModule();
        $path = platform_path(
            Helper::joinPaths([$module, 'src', 'Http', 'Controllers', ucfirst(Str::studly($name)) . 'Controller.php'])
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
            'Http',
            'Controllers',
            '{Name}Controller.stub',
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
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the controller class');
        $this->addArgument('module', InputArgument::OPTIONAL, 'The module name');
    }
}
