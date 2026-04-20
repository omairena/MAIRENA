<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // Importa el facade DB
use Illuminate\Support\Facades\Log; // Importa el facade Log 
use Illuminate\Support\Facades\Auth; // Importa el facade Auth
use App\Facelectron; // Importa el modelo Facelectron
use App\Configuracion;
use App\Sales; // Importa el modelo Sales
use App\Http\Controllers\ReportesController; // Importa el controlador para generar PDFs
use Carbon\Carbon; // Importa Carbon para manejar fechas

class ConsultarDocumentosCommand extends Command
{
    protected $signature = 'documentos:consultar';
    protected $description = 'Consulta documentos pendientes de los últimos 3 meses';

    public function handle()
    {
        // Incluir el archivo
        include_once(public_path() . '/consulta_documento.php');

        // Define la fecha límite como hace 3 meses desde hoy
        $fechaLimite = Carbon::now()->subMonths(3);

        // Filtrar los registros de facelectron donde fechahora sea desde 3 meses hasta hoy
        $arreglo = DB::table('facelectron')
            ->where('pendiente', '=', '0')
            ->where('fechahora', '>=', $fechaLimite)
            ->get();

        foreach ($arreglo as $array) {
            $seguridad = $this->armarSeguridad($array->idconfigfact);
            $xml = [
                'tipoDocumento' => (string)$array->tipodoc,
                'numero_consecutivo' => (string)$array->consecutivo,
                'clave' => (string)$array->clave
            ];

            // Enviar el documento
            $envio = Enviar_documentos($xml, $seguridad);
            Log::info("consulta_doc: " . $envio);

            // Obtener el cliente para generar el PDF
            $idcli = Sales::where('idsale', $array->idsales)->first();
            $generar = app(ReportesController::class)->pdf_factura($array->idsales);
        }

        Log::info('Consulta de documentos completada.');
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