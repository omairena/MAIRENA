<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Receptor;

class RecepcionAutomatica implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $datos;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $receptor = Receptor::where('tipo_documento_recibido', '=', '01')
        ->where('numero_documento_receptor', '=', '9999999999')
        ->where('idconfigfact', '=', $this->datos['idconfigfact'])
        ->get();

        if (count($receptor) > 0) {

            foreach ($receptor as $facturas) {

                $array = [
                    'datos' => $this->datos,
                    'idreceptor' => $facturas->idreceptor
                ];
                Artisan::call('factura:recepcion', ['--receptor' => $array]);
            }
        } else { 

            $receptor_nc = Receptor::where('tipo_documento_recibido', '=', '03')
            ->where('numero_documento_receptor', '=', '9999999999')
            ->where('idconfigfact', '=', $this->datos['idconfigfact'])
            ->get();

            if (count($receptor_nc) > 0) {
                foreach ($receptor_nc as $notasc) {

                    $array = [
                        'datos' => $this->datos,
                        'idreceptor' => $notasc->idreceptor
                    ];
                    Artisan::call('factura:recepcion', ['--receptor' => $array]);

                } 
            } else {

                $receptor_nd = Receptor::where('tipo_documento_recibido', '=', '02')
                ->where('numero_documento_receptor', '=', '9999999999')
                ->where('idconfigfact', '=', $this->datos['idconfigfact'])
                ->get();

                if (count($receptor_nd)) {

                    foreach ($receptor_nd as $notasd) {

                        $array = [
                            'datos' => $this->datos,
                            'idreceptor' => $notasd->idreceptor
                        ];
                        Artisan::call('factura:recepcion', ['--receptor' => $array]);
                    }
                }
            }
        }
    }
}
