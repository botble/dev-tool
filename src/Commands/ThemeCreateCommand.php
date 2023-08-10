<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\Theme\Commands\Traits\ThemeTrait;
use Botble\Theme\Services\ThemeService;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('cms:theme:create', 'Generate theme structure')]
class ThemeCreateCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use ThemeTrait;

    public function handle(File $files, ThemeService $themeService): int
    {
        $theme = $this->getTheme();
        $path = $this->getPath();

        if ($files->isDirectory($path)) {
            $this->components->error(sprintf('Theme "%s" is already exists.', $theme));

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $path);

        if ($files->isDirectory($this->getStub())) {
            $screenshot = __DIR__ . '/../../resources/assets/images/' . rand(1, 5) . '.png';
            $files->copy($screenshot, $path . '/screenshot.png');
        }

        $this->searchAndReplaceInFiles($theme, $path);
        $this->renameFiles($theme, $path);

        $themeService->publishAssets($theme);

        $this->components->info(sprintf('Theme "%s" has been created.', $theme));

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../stubs/theme';
    }

    public function baseReplacements(string $replaceText): array
    {
        return ['.js.stub' => '.js'] + parent::baseReplacements($replaceText);
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{theme}' => strtolower($replaceText),
            '{Theme}' => Str::studly($replaceText),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The theme name that you want to create');
        $this->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to theme directory');
    }
}
