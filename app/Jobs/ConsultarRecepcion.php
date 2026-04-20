<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Auth;
use DB;
use App\Configuracion;

class ConsultarRecepcion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("Job:consulta begin cummand run!");

        include_once(public_path(). '/consulta_documento.php');
        $arreglo = DB::table('receptor')->where([
            ['pendiente', '=', '0'],
        ])->get();
        foreach ($arreglo as $array) {

            $buscar = Configuracion::find($array->idconfigfact);
            if ($buscar->client_id === 1) {

               $entorno = 'api-prod';
            } else {

               $entorno = 'api-stag';
            }
            $seguridad =  [
                'certificado' => ''.public_path().'/certificados/'.$buscar->ruta_certificado, // Ruta del certificado donde se encuentra
                'clave_certificado' => ''.$buscar->clave_certificado, //clave del certificado .p12
                'credenciales_conexion' => ''.$buscar->credenciales_conexion, //credenciales de Hacienda
                'clave_conexion' => ''.$buscar->clave_conexion, //Contraseña de hacienda
                'client_id' => $entorno, //api-stag para pruebas y api-prod para el entorno produccion
                'idconfigfact' => $array->idconfigfact, //variable solo para el job para saber que empresa es
            ];
            $xml = [
                'tipoDocumento' => ''.$array->tipo_documento, //tipo de documento a consultar
                'numero_consecutivo' => ''.$array->consecutivo, //Numero consecutivo del documento
                'clave' => ''.$array->clave, //clave del documento
                'idreceptor' => ''.$array->idreceptor
            ];
            $envio = Enviar_documentos($xml, $seguridad);
        }
        \Log::info("Job:consulta end cummand run successfully!");
    }
}
