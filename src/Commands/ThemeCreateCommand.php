<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Helper;
use Botble\Theme\Commands\Traits\ThemeTrait;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Services\ThemeService;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('cms:theme:create', 'Generate theme structure')]
class ThemeCreateCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use ThemeTrait;

    protected ?string $parentTheme;

    public function __construct(protected ThemeService $themeService)
    {
        parent::__construct();
    }

    public function handle(File $files): int
    {
        $theme = $this->getTheme();
        $path = $this->getPath();
        $this->parentTheme = $this->option('parent');

        if (! empty($this->parentTheme) && ! $this->isValidParentTheme()) {
            return static::FAILURE;
        }

        if ($files->isDirectory($path)) {
            $this->components->error(sprintf('Theme "%s" is already exists.', $theme));

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $path);

        if ($files->isDirectory($this->getStub())) {
            $screenshot = Helper::joinPaths([
                dirname(__DIR__, 2),
                'resources',
                'assets',
                'images',
                rand(1, 5) . '.png',
            ]);
            $files->copy($screenshot, $path . DIRECTORY_SEPARATOR . 'screenshot.png');
        }

        $this->searchAndReplaceInFiles($theme, $path);

        $this->renameFiles($theme, $path);

        $this->themeService->publishAssets($theme);

        $this->components->info(sprintf('Theme "%s" has been created.', $theme));

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        return Helper::joinPaths([dirname(__DIR__, 2), 'stubs', $this->parentTheme ? 'child-theme' : 'theme']);
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
            '{parent}' => strtolower($this->parentTheme),
        ];
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The theme name that you want to create')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to theme directory')
            ->addOption('parent', null, InputOption::VALUE_REQUIRED, 'Parent theme name (if you want to create a child theme)');
    }

    protected function isValidParentTheme(): bool
    {
        if (! Theme::exists($this->parentTheme)) {
            $this->components->error(sprintf('Parent theme "%s" does not exist.', $this->parentTheme));

            return false;
        }

        $config = $this->themeService->getThemeConfig($this->parentTheme);

        if (Arr::has($config, 'inherit') && Arr::get($config, 'inherit')) {
            $this->components->error(sprintf('Parent theme "%s" does not support child theme.', $this->parentTheme));

            return false;
        }

        return true;
    }
}
