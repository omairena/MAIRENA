<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronController;

class cargaAutomatica extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carga:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processing load from folder of all documents received.';

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
        \Log::info("carga:start begin cummand run!");
        $controller = new CronController();
        $controller->startCronStore();
        \Log::info("carga:start end cummand run successfully!");
    }
}
