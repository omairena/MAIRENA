<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronController;

class updateFolderXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recepcion:folder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command work for move from a comun folder to a company folder';

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
        \Log::info("recepcion:folder begin cummand run!");
        $controller = new CronController();
        $controller->startUpdateFolder();
        \Log::info("recepcion:folder end cummand run successfully!");
    }
}
