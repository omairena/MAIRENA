<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\ReportesController;

class AbyFec extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aby:fec';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command for fec report';

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
     * @return int
     */
    public function handle()
    {
        \Log::info("aby:fec begin cummand run!");
        $controller = new ReportesController();
        $controller->salesfec();
        \Log::info("aby:fec end cummand run successfully!");
    }
}
