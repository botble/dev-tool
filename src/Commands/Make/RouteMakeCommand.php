<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Commands\Concerns\HasModuleSelector;
use Botble\DevTool\Helper;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:make:route', 'Make a route')]
class RouteMakeCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use HasModuleSelector;

    public function handle(): int
    {
        $name = $this->argument('name');

        if (! preg_match('/^[a-z0-9\-_]+$/i', $name)) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $path = platform_path(
            Helper::joinPaths([$this->getModule(), 'routes', ucfirst($name) . '.php'])
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
            'routes',
            'web.stub',
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
        $this->addArgument('name', InputArgument::REQUIRED, 'The route name that you want to create');
        $this->addArgument('module', InputArgument::OPTIONAL, 'The module name');
    }
}
