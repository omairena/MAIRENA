<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReceptorController;

class recepcionFactura extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factura:recepcion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recepcion automatica de facturas desde la vista de recepcion automatica masiva';

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
        \Log::info("factura:recepcion begin cummand run!");
        $controller = new ReceptorController();
        $inicio = $controller->startCronRecepcionAutomatica();
        \Log::info("factura:recepcion end cummand run successfully!");
    }
}
