<?php

namespace ShibuyaKosuke\LaravelModelReplacement\Console;

use Illuminate\Console\Command;

class ReplaceModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replace:model {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup model directory as you want.';

    public function handle()
    {
    }
}