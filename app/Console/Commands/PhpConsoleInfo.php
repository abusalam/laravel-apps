<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PhpConsoleInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'php:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("disable_functions=" . ini_get("disable_functions"));
        $this->info("allow_url_fopen=" . ini_get("allow_url_fopen"));
    }
}
