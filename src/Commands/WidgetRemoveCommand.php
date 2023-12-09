<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Helper;
use Botble\Theme\Facades\Theme;
use Botble\Widget\Models\Widget;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:widget:remove', 'Remove a widget')]
class WidgetRemoveCommand extends Command implements PromptsForMissingInput
{
    use ConfirmableTrait;

    public function handle(Filesystem $files): int
    {
        if (! confirm('Are you sure you want to permanently delete?')) {
            return self::FAILURE;
        }

        $widget = $this->getWidget();
        $path = $this->getPath();

        if (! $files->isDirectory($path)) {
            $this->components->error(sprintf('Widget "%s" is not existed.', $widget));

            return self::FAILURE;
        }

        try {
            $files->deleteDirectory($path);
            Widget::query()
                ->where([
                    'widget_id' => sprintf('%sWidget', Str::studly($widget)),
                    'theme' => Theme::getThemeName(),
                ])
                ->each(fn (Widget $widget) => $widget->delete());

            $this->components->info(sprintf('Widget "%s" has been deleted.', $widget));
        } catch (Exception $exception) {
            $this->components->info($exception->getMessage());
        }

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

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The widget that you want to remove');
        $this->addOption('force', 'f', null, 'Force to remove widget without confirmation');
    }
}
