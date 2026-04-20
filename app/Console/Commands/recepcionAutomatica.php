<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronController;

class recepcionAutomatica extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recepcion:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to start automatic mail reception.';

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
        \Log::info("recepcion:start begin cummand run!");
        $controller = new CronController();
        $controller->startCron();
        \Log::info("recepcion:start end cummand run successfully!");
    }
}
