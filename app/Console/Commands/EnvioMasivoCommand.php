<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Facelectron; // Asegúrate de que estás importando el modelo correcto
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Mail\RespuestasReceived;
use App\Sales;
use App\Cliente;
use App\Configuracion;
class EnvioMasivoCommand extends Command
{
    // Nombre y firma del comando
    protected $signature = 'envio:masivo';
    protected $description = 'Enviar facturas por correo masivo';

    // Método de ejecución del comando
    public function handle()
    {
        $arreglo = DB::table('facelectron')->where('enviado_correo', 1)->get();
       
        foreach ($arreglo as $array) {
            // Aquí llamas a la función que envía el correo
            // Suponiendo que se encuentra en el mismo modelo, si no, usa la inyección de dependencias
            $this->reenviarCorreoXml($array->idsales);
            
            // Actualiza el estado en la base de datos
            Facelectron::where('idsales', $array->idsales)->update([
                'enviado_correo' => 2,
            ]);
        }

        $this->info('Facturas enviadas por email correctamente.');
         $this->info(date('Y-m-d H:i:s') . ' - Facturas enviadas por correo correctamente.');

    }

protected function reenviarCorreoXml($idsales)
{
   
    //app('App\Http\Controllers\PeticionesController')->ajaxConsultar();
    $sale = Sales::find($idsales);  // Cambié $id por $idsales
    if (!$sale) {
        Log::error("No se encontró la venta con idsales: {$idsales}");
        return;  // Detener la ejecución si no se encuentra la venta
    }

    $cliente = Cliente::find($sale->idcliente);
    $buscar = Facelectron::where('idsales', $idsales)->get();
    
    if ($buscar->isEmpty()) {
        Log::error("No se encontraron registros de Facelectron para idsales: {$idsales}");
        return;
    }

    $configuracion = Configuracion::find($buscar[0]->idconfigfact);
    $attach = public_path($buscar[0]->rutaxml);
    $tipodoc = $buscar[0]->tipodoc;  
    $numdoc = $buscar[0]->numdoc;  
    $nombreEmisor = $configuracion->nombre_emisor;  
    $numeroIdEmisor = $configuracion->numero_id_emisor;  

    switch ($tipodoc) {  
        case '04':  
            $subject = 'Tiquete #' . $numdoc . ' Emisor: ' . $nombreEmisor . ' Cedula Emisor: ' . $numeroIdEmisor;  
            break;  
        case '01':  
            $subject = 'Factura #' . $numdoc . ' Emisor: ' . $nombreEmisor . ' Cedula Emisor: ' . $numeroIdEmisor;  
            break;  
        case '03':  
            $subject = 'Nota Credito #' . $numdoc . ' Emisor: ' . $nombreEmisor . ' Cedula Emisor: ' . $numeroIdEmisor;  
            break;  
        case '02':  
            $subject = 'Nota Debito #' . $numdoc . ' Emisor: ' . $nombreEmisor . ' Cedula Emisor: ' . $numeroIdEmisor;  
            break;  
        default:  
            $subject = 'Documento #' . $numdoc . ' Emisor: ' . $nombreEmisor . ' Cedula Emisor: ' . $numeroIdEmisor;  
            break;  
    }  

    // Preparar la lista de correos
    if (!is_null($cliente->additional_email)) {
        $adit_em = explode(',', $cliente->additional_email);
        $emails = array_merge([$cliente->email], $adit_em);
    } else {
        $emails = [$cliente->email];
    }

    // Verifica el estado de Hacienda
    if (!is_null($buscar[0]->estatushacienda)) {
        $attachFiles = [$attach]; // Iniciar con el archivo XML adjunto
        
        if ($buscar[0]->estatushacienda != 'procesando') {
            if ($buscar[0]->estatushacienda != 'pendiente') {
                // Adjuntas adicionales en base al estatus
                $attach2 = public_path($buscar[0]->respuesta_xml);
                $attachFiles[] = $attach2;
            }
            $attach3 = public_path($buscar[0]->pdf_factura);
            $attachFiles[] = $attach3;

            try {
                Mail::send('mails.respuesta_hacienda', ['idsale' => $idsales], function ($msj) use($subject, $emails, $attachFiles) {
                    $msj->from("facturacion@snsilvestre.com", "Factura Electrónica San Esteban");
                    $msj->subject($subject);
                    $msj->to($emails);
                    foreach ($attachFiles as $file) {
                        $msj->attach($file);
                    }
                });

                // Actualizar estado a enviado
                Facelectron::where('idsales', $idsales)->update(['enviado_correo' => 2]);
                Log::info("Correo enviado correctamente a: " . implode(', ', $emails));

            } catch (\Throwable $th) {
                Log::error("Error al enviar el correo: " . $th->getMessage());
            }
        }
    } else {
        // Enviar sin estatus
        try {
            Mail::send('mails.respuesta_hacienda', ['idsale' => $idsales], function ($msj) use($subject, $emails, $attach) {
                $msj->from("facturacion@snsilvestre.com", "Factura Electrónica San Esteban");
                $msj->subject($subject);
                $msj->to($emails);
                $msj->attach($attach);
            });
            Log::info("Correo enviado correctamente sin estatus a: " . implode(', ', $emails));
        } catch (\Throwable $th) {
            Log::error("Error al enviar el correo sin estatus: " . $th->getMessage());
        }
    }
}
}