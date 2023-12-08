<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Commands\Concerns\HasModuleSelector;
use Botble\DevTool\Helper;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'cms:make:panel-section', description: 'Make a new panel section')]
class PanelSectionMakeCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use HasModuleSelector;

    public function handle(): int
    {
        $panelSection = $this->getPanelSection();
        $path = $this->getPath();

        if (File::exists($path)) {
            $this->components->error("Panel section [{$path}] already exists.");

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $path);
        $this->renameFiles($panelSection, $path);
        $this->searchAndReplaceInFiles($panelSection, $path);

        $this->components->info("Panel section [{$path}] created successfully.");

        return self::SUCCESS;
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{id}' => Str::snake($replaceText),
            '{title}' => Str::title(Str::snake($replaceText, ' ')),
            '{Module}' => $this->transformModuleToNamespace(),
        ];
    }

    public function getStub(): string
    {
        return Helper::joinPaths([
            dirname(__DIR__, 3),
            'stubs',
            'module',
            'src',
            'PanelSections',
            '{Name}PanelSection.stub',
        ]);
    }

    protected function getPanelSection(): string
    {
        return $this->argument('name');
    }

    protected function getPath(): string
    {
        return platform_path(
            Helper::joinPaths([
                $this->promptModule(),
                'src',
                'PanelSections',
                $this->getPanelSection() . 'PanelSection.php',
            ])
        );
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the panel section, (e.g. SettingBlog)')
            ->addArgument('module', InputArgument::OPTIONAL, 'The name of the module (e.g. plugins/blog)');
    }
}
