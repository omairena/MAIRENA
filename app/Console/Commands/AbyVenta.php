<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\ReportesController;

class AbyVenta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aby:venta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command for sales report';

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
        \Log::info("aby:venta begin cummand run!");
        $controller = new ReportesController();
        $controller->salesColonizado();
        \Log::info("aby:venta end cummand run successfully!");
    }
}
