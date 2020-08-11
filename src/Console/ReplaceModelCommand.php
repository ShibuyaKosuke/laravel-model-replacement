<?php

namespace ShibuyaKosuke\LaravelModelReplacement\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ReplaceModelCommand extends Command
{
    /**
     * @var string Model directory
     */
    private $model_path;

    /**
     * @var string model namespace
     */
    private $namespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replace:model {path=app/Models}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup model directory as you want.';

    public function handle()
    {
        $this->model_path = base_path($this->argument('path'));
        $this->namespace = Str::studly(str_replace([base_path() . '/', '/'], ['', '\\'], $this->model_path));

        if ($this->namespace === $this->getUserModelNamespace()) {
            $this->line('model directory already moved.', 'info');
            return;
        }
        $this->copyUserModel();
        $this->modifyAuthConfig();
        $this->modifyRegisterController();
        $this->line('model directory moved successfully.', 'info');
    }

    private function getReflectionUserModel()
    {
        $defaultUserModel = \config('auth.providers.users.model', \config('auth.model', 'App\User'));
        return new \ReflectionClass($defaultUserModel);
    }

    private function getUserModelNamespace()
    {
        return $this->getReflectionUserModel()->getNamespaceName();
    }

    private function copyUserModel()
    {
        $file_name = $this->getReflectionUserModel()->getFileName();
        $destination = $this->model_path . '/' . basename($file_name);
        if (!file_exists($this->model_path)) {
            mkdir($this->model_path);
        }
        File::move($file_name, $destination);

        $contents = str_replace(
            'namespace App;',
            sprintf('namespace %s;', $this->namespace),
            File::get($destination)
        );
        File::replace($destination, $contents);
    }

    private function modifyAuthConfig()
    {
        $contents = File::get(config_path('auth.php'));
        $contents = str_replace(
            '\'model\' => App\User::class,',
            sprintf('\'model\' => %s\User::class,', $this->namespace),
            $contents
        );
        File::replace(config_path('auth.php'), $contents);
    }

    private function modifyRegisterController()
    {
        $file_name = app_path('Http/Controllers/Auth/RegisterController.php');
        if (File::exists($file_name)) {
            File::replace($file_name, str_replace(
                'use App\User;',
                sprintf('use %s\User;', $this->namespace),
                File::get($file_name)
            ));

            File::replace($file_name, str_replace(
                '@return \App\User',
                sprintf('@return \%s\User', $this->namespace),
                File::get($file_name)
            ));
        }
    }
}