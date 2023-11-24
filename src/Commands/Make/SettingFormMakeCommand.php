<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Commands\Concerns\HasModuleSelector;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'cms:make:setting:form', description: 'Make new setting form builder')]
class SettingFormMakeCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use HasModuleSelector;

    public function handle(): int
    {
        $settingName = $this->getSetting();
        $path = sprintf('%s/%sForm.php', $this->getPath(), $settingName);

        if (File::exists($path)) {
            $this->components->error("Setting form [{$path}] already exists.");

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $path);
        $this->searchAndReplaceInFiles($settingName, $path);
        $this->renameFiles("{$settingName}Form", $path);

        $this->components->info("Setting form [{$path}] created successfully.");

        return self::SUCCESS;
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{Module}' => $this->transformModuleToNamespace(),
        ];
    }

    public function getStub(): string
    {
        return __DIR__ . '/../../../stubs/module/src/Forms/Settings/{Name}Form.stub';
    }

    protected function getSetting(): string
    {
        return $this->argument('name');
    }

    protected function getPath(): string
    {
        return platform_path(sprintf('%s/src/Forms/Settings', $this->promptModule()));
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the setting (e.g. BlogSetting)')
            ->addArgument('module', InputArgument::OPTIONAL, 'The name of the module (e.g. plugins/blog)');
    }
}
