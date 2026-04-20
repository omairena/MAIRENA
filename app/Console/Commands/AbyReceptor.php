<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ReportesController;
use Illuminate\Console\Command;

class AbyReceptor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aby:receptor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for receptor report';

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
        \Log::info("aby:receptor begin cummand run!");
        $controller = new ReportesController();
        $controller->receptorAby();
        \Log::info("aby:receptor end cummand run successfully!");
    }
}
