<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // Para acceder a la base de datos
use Illuminate\Support\Facades\Log; // Para registrar información
use Carbon\Carbon; // Para gestionar fechas
use App\Configuracion;
class ConsultarReceptorCommand extends Command
{
    protected $signature = 'receptor:consultar';
    protected $description = 'Consulta receptores pendientes';

    public function handle()
    {
        // Incluir el archivo necesario
        include_once(public_path() . '/consulta_documento.php');

        // Define la fecha límite como hace 3 meses desde hoy
        $fechaLimite = Carbon::now()->subMonths(3);

        // Filtrar los registros de receptor donde pendient​e sea 0 y fechahora sea desde hace 3 meses hasta hoy
        $arreglo = DB::table('receptor')
            ->where('pendiente', '=', '0')
            ->where('fecha', '>=', $fechaLimite)
            ->get();

        // Procesar cada receptor
        foreach ($arreglo as $array) {
            $seguridad = $this->armarSeguridad($array->idconfigfact);
            $xml = [
                'tipoDocumento' => (string)$array->tipo_documento,
                'numero_consecutivo' => (string)$array->consecutivo,
                'clave' => (string)$array->clave,
                'idreceptor' => (string)$array->idreceptor,
                'comando' => 0,
            ];

            // Enviar el documento
            $envio = Enviar_documentos($xml, $seguridad);
            Log::info("Consulta receptor: " . $envio);
        }

        Log::info('Consulta de receptores completada.');
    }

    protected function armarSeguridad($idconfigfact)
    {
      $buscar = Configuracion::find($idconfigfact);
            if ($buscar->client_id === 1) {
               $entorno = 'api-prod';
            }else{
               $entorno = 'api-stag';
            }
            $seguridad =  [
                'idconfigfact' => $idconfigfact, // Asegúrate de incluir esto en el arreglo
                'certificado' => ''.public_path().'/certificados/'.$buscar->ruta_certificado, // Ruta del certificado donde se encuentra
                'clave_certificado' => ''.$buscar->clave_certificado, //clave del certificado .p12
                'credenciales_conexion' => ''.$buscar->credenciales_conexion, //credenciales de Hacienda
                'clave_conexion' => ''.$buscar->clave_conexion, //Contrase単a de hacienda
                'client_id' => $entorno //api-stag para pruebas y api-prod para el entorno produccion
            ];
            return $seguridad;
    }
}