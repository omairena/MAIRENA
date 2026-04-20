<?php

namespace App\Jobs;

use App\Http\Controllers\PosController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TimbrarPosSaleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $idsale;
    protected $idconfigfact;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($idsale, $idconfigfact)
    {
        $this->idsale = $idsale;
        $this->idconfigfact = $idconfigfact;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $posController = app(PosController::class);
            $seguridad = $posController->armarSeguridad($this->idconfigfact);
            $xml = $posController->armarXml((int) $this->idsale);

            include_once(public_path() . '/funcionFacturacion506.php');
            Timbrar_documentos($xml, $seguridad);
        } catch (\Throwable $e) {
            Log::error('Error al timbrar factura POS en cola', [
                'idsale' => $this->idsale,
                'idconfigfact' => $this->idconfigfact,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
