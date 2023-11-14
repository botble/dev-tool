<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'cms:make:panel-section', description: 'Make a new panel section')]
class PanelSectionMakeCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    public function handle(): int
    {
        $panelSection = $this->getPanelSection();
        $path = $this->getPath();

        if (File::exists($path)) {
            $this->components->error("Section panel [{$path}] already exists.");

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $path);
        $this->searchAndReplaceInFiles($panelSection, $path);
        $this->renameFiles($panelSection, $path);

        $this->components->info("Section panel [{$path}] created successfully.");

        return self::SUCCESS;
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{id}' => Str::snake($replaceText),
            '{title}' => Str::title(Str::snake($replaceText, ' ')),
            '{namespace}' => str(str($this->argument('module'))->afterLast('/')->ucfirst())->prepend('Botble\\')->append('\\PanelSections'),
            '{PanelSection}' => Str::studly($replaceText),
        ];
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../stubs/setting/PanelSection.stub';
    }

    protected function getPanelSection(): string
    {
        return $this->argument('name');
    }

    protected function getPath(): string
    {
        return base_path(sprintf('platform/%s/src/PanelSections/%s.php', $this->argument('module'), $this->getPanelSection()));
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the panel section, (e.g. SettingBlogPanelSection)')
            ->addArgument('module', InputArgument::REQUIRED, 'The name of the module (e.g. plugins/blog)');
    }
}
