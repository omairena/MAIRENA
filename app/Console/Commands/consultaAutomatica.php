<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PeticionesController;

class consultaAutomatica extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consulta:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to start consult to hacienda.';

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
        \Log::info("consulta:start begin cummand run!");
        $controller = new PeticionesController();
        $controller->ajaxEjecutarConsultarReceptor();
        \Log::info("consulta:start end cummand run successfully!");
    }
}
