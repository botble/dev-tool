<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Helper;
use Botble\Theme\Facades\Theme;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:widget:create', 'Create a new widget')]
class WidgetCreateCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    public function handle(File $files): int
    {
        $widget = $this->getWidget();
        $path = $this->getPath();

        if ($files->isDirectory($path)) {
            $this->components->error(sprintf('Widget "%s" is already exists.', $widget));

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $path);
        $this->searchAndReplaceInFiles($widget, $path);
        $this->renameFiles($widget, $path);

        $this->components->info(sprintf('Widget "%s" has been created in %s.', $widget, $path));

        return self::SUCCESS;
    }

    protected function getWidget(): string
    {
        return strtolower($this->argument('name'));
    }

    protected function getPath(): string
    {
        return theme_path(
            Helper::joinPaths([Theme::getThemeName(), 'widgets', $this->getWidget()])
        );
    }

    public function getStub(): string
    {
        return Helper::joinPaths([dirname(__DIR__, 2), 'stubs', 'widget']);
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{widget}' => strtolower($replaceText),
            '{Widget}' => Str::studly($replaceText),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The widget name that you want to create');
    }
}
