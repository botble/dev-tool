<?php

namespace Botble\DevTool\Commands;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'cms:make:setting:controller', description: 'Make new setting controller')]
class SettingControllerMakeCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    public function handle(): int
    {
        $settingName = $this->getSetting();
        $path = sprintf('%s/%sController.php', $this->getPath(), $settingName);

        if (File::exists($path)) {
            $this->components->error("Setting controller [{$path}] already exists.");

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $path);
        $this->searchAndReplaceInFiles($settingName, $path);
        $this->renameFiles($settingName, $path);

        $this->components->info("Setting controller [{$path}] created successfully.");

        return self::SUCCESS;
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{name}' => $replaceText,
            '{controller}' => "{$replaceText}Controller",
            '{namespace}' => str(str($this->argument('module'))->afterLast('/')->ucfirst())->prepend('Botble\\'),
        ];
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../stubs/setting/{controller}.stub';
    }

    protected function getSetting(): string
    {
        return $this->argument('name');
    }

    protected function getPath(): string
    {
        return platform_path(sprintf('%s/src/Http/Controllers/Settings', $this->argument('module')));
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the setting (e.g. BlogSetting)')
            ->addArgument('module', InputArgument::REQUIRED, 'The name of the module (e.g. plugins/blog)');
    }
}
