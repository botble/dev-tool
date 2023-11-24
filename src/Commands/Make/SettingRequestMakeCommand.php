<?php

namespace Botble\DevTool\Commands\Make;

use Botble\DevTool\Commands\Abstracts\BaseMakeCommand;
use Botble\DevTool\Commands\Concerns\HasModuleSelector;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'cms:make:setting:request', description: 'Make new setting form request')]
class SettingRequestMakeCommand extends BaseMakeCommand implements PromptsForMissingInput
{
    use HasModuleSelector;

    public function handle(): int
    {
        $settingName = $this->getSetting();
        $path = sprintf('%s/%sRequest.php', $this->getPath(), $settingName);

        if (File::exists($path)) {
            $this->components->error("Setting request [{$path}] already exists.");

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $path);
        $this->searchAndReplaceInFiles($settingName, $path);
        $this->renameFiles($settingName, $path);

        $this->components->info("Setting request [{$path}] created successfully.");

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
        return __DIR__ . '/../../../stubs/module/src/Http/Requests/Settings/{Name}Request.stub';
    }

    protected function getSetting(): string
    {
        return $this->argument('name');
    }

    protected function getPath(): string
    {
        return platform_path(sprintf('%s/src/Http/Requests/Settings', $this->promptModule()));
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the setting (e.g. BlogSetting)')
            ->addArgument('module', InputArgument::OPTIONAL, 'The name of the module (e.g. plugins/blog)');
    }
}
