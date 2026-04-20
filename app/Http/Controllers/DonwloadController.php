<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facelectron;
use App\Sales;
use App\Cliente;
use App\Receptor;
use Storage;
use Mail;
use App\Mail\RespuestasReceived;
use DB;
use App\Cxcobrar;
use App\Mov_cxcobrar;
use App\Log_cxcobrar;
use App\Cxpagar;
use App\Mov_cxpagar;
use App\Log_cxpagar;
use App\Configuracion;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use App\Pedidos;
use Auth;

class DonwloadController extends Controller
{
       public function xmlfactura($id)
    {
    	$buscar = Facelectron::where('idsales', $id)->get();
    	$pathToFile = public_path($buscar[0]->rutaxml);
    	return response()->download($pathToFile);
    }

       public function xmlfactura_respuesta($id)
    {
    	$buscar = Facelectron::where('idsales', $id)->get();
    	$pathToFile = public_path($buscar[0]->respuesta_xml);
    	return response()->download($pathToFile);
    }

       public function xmlreceptor($id)
    {
        $buscar = Receptor::where('idreceptor', $id)->get();
        $pathToFile = public_path($buscar[0]->xml_envio);
        return response()->download($pathToFile);
    }

       public function xmlreceptor_respuesta($id)
    {
        $buscar = Receptor::where('idreceptor', $id)->get();
        $pathToFile = public_path($buscar[0]->xml_respuesta);
        return response()->download($pathToFile);
    }
    public function xmlreceptor_original($id)
    {
        $buscar = Receptor::where('idreceptor', $id)->get();
        $pathToFile = public_path($buscar[0]->ruta_carga);
        return response()->download($pathToFile);
    }

        public function correoXml(Request $request, $id)
    {
        app('App\Http\Controllers\PeticionesController')->ajaxConsultar();
        $sale = Sales::find($id);
        $cliente = Cliente::find($sale->idcliente);
        $buscar = Facelectron::where('idsales', $id)->get();
        
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

$emails = [];

// Construcción de la lista de correos según la presencia de $sale->cc_correo
$cc_correo_str = $sale->cc_correo ?? ''; // por seguridad en caso de que no exista
if (trim($cc_correo_str) !== '') {
    // Caso: hay cc_correo, usar solo: correo principal + cc_correo
   
    $emails[] = trim($cliente->email); // correo principal del cliente

    // Agregar los correos de cc_correo (posiblemente varias direcciones separadas por coma)
    $cc_emails = explode(',', $cc_correo_str);
    foreach ($cc_emails as $cc_email) {
        $emails[] = trim($cc_email);
    }
} else {
    // Caso original: usar correo principal y los emails adicionales (si existen)
    if (!is_null($cliente->additional_email)) {
        $adit_em = explode(',', $cliente->additional_email);

        // Correo principal del cliente
        $emails[] = trim($cliente->email);

        // Correos adicionales
        foreach ($adit_em as $adicional_em) {
            $emails[] = trim($adicional_em);
        }
    } else {
        // Solo el correo principal
        $emails[] = trim($cliente->email);
    }
}

// Agregar el correo electrónico del emisor a la lista de correos
if (isset($configuracion->email_emisor) && !empty($configuracion->email_emisor) && $configuracion->auto_copia_email == 1 ) {
    $emails[] = trim($configuracion->email_emisor);
}

// Ahora $emails contiene todos los correos a los que se quiere enviar
$for = $emails;

        $arreglo = $sale;
        if (!is_null($buscar[0]->estatushacienda)) {

            if ($buscar[0]->estatushacienda != 'procesando') {

                if ($buscar[0]->estatushacienda != 'pendiente') {
                    try {
                        $attach2 =  public_path($buscar[0]->respuesta_xml);
                        $attach3 =  public_path($buscar[0]->pdf_factura);
                       $datosCorreo = array_merge($request->all(), [
    'mensaje_adicional' => 'Este es un mensaje adicional que quieres incluir en el cuerpo del correo',
]);

Mail::send('mails.respuesta_hacienda', $datosCorreo, function($msj) use($subject, $for, $attach, $attach2, $attach3) {
    $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
    $msj->subject($subject);
    $msj->to($for);
    
    if ($attach) {
        $msj->attach($attach);
    }
    if ($attach2) {
        $msj->attach($attach2);
    }
    if ($attach3) {
        $msj->attach($attach3);
    }
});
                        $update  = Facelectron::where('idsales', $id)->update([
                           [ 'enviado_correo' => 2],
                           ['creado_por' => Auth::user()->email ]
                        ]);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                } else {
                    try {
                        $attach2 =  public_path($buscar[0]->pdf_factura);
                        Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $attach2){
                            $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                            $msj->subject($subject);
                            $msj->to($for);
                            $msj->attach($attach);
                            $msj->attach($attach2);
                        });
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                }
            } else {
                try {

                    $attach2 =  public_path($buscar[0]->pdf_factura);
                    Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $attach2){
                        $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                        $msj->subject($subject);
                        $msj->to($for);
                        $msj->attach($attach);
                        $msj->attach($attach2);
                    });
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        } else {
            try {

                Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $arreglo){
                    $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                    $msj->subject($subject);
                    $msj->to($for);
                    $msj->attach($attach);
                });
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
       // dd($buscar[0]->tipodoc);
        if($buscar[0]->tipodoc=='09'){
        return redirect()->route('fee.index')->withStatus(__('Factura Enviada Por Email Correctamente.'));
    }else{
        return redirect()->route('facturar.index')->withStatus(__('Factura Enviada Por Email Correctamente.'));
    }
    }

       public function correoCXml(Request $request, $id)
    {
        $info = $request->all();
        $valores = explode(',', $info['copias'][0]);
        $for = [];
        for ($i=0; $i < count($valores); $i++) {
            $for[] = trim($valores[$i]);
        }
        app('App\Http\Controllers\PeticionesController')->ajaxConsultar();
        $sale = Sales::find($id);
        $cliente = Cliente::find($sale->idcliente);
        $buscar = Facelectron::where('idsales', $id)->get();
        $configuracion = Configuracion::find($buscar[0]->idconfigfact);
        $attach = public_path($buscar[0]->rutaxml);
      //  $subject = 'Factura #'.$buscar[0]->clave;
        // $subject = 'Factura #'.$buscar[0]->numdoc.' Emisor: '.$configuracion->nombre_emisor.' Cedula Emisor : '.$configuracion->numero_id_emisor;
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
        $for[] = $cliente->email;
        $arreglo = $sale;
        if (!is_null($buscar[0]->estatushacienda)) {

            if ($buscar[0]->estatushacienda != 'procesando') {

                if ($buscar[0]->estatushacienda != 'pendiente') {

                    try {

                        $attach2 =  public_path($buscar[0]->respuesta_xml);
                        $attach3 =  public_path($buscar[0]->pdf_factura);
                        Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $attach2, $attach3){
                            $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                            $msj->subject($subject);
                            $msj->to($for);
                            $msj->attach($attach);
                            $msj->attach($attach2);
                            $msj->attach($attach3);
                        });
                      $update  = Facelectron::where('idsales', $id)->update([
                           [ 'enviado_correo' => 2],
                           ['creado_por' => Auth::user()->email ]
                        ]);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                } else {

                    try {
                        $attach2 =  public_path($buscar[0]->pdf_factura);
                        Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $attach2){
                            $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                            $msj->subject($subject);
                            $msj->to($for);
                            $msj->attach($attach);
                            $msj->attach($attach2);
                        });
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            } else {

                try {
                    $attach2 =  public_path($buscar[0]->pdf_factura);
                    Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $attach2){
                        $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                        $msj->subject($subject);
                        $msj->to($for);
                        $msj->attach($attach);
                        $msj->attach($attach2);
                    });
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        } else {

            try {
                Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $arreglo){
                    $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                    $msj->subject($subject);
                    $msj->to($for);
                    $msj->attach($attach);
                });
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        return redirect()->route('facturar.imprimir', ['id' => $id]);
    }

       public function correoPos(Request $request, $id)
    {
        app('App\Http\Controllers\PeticionesController')->ajaxConsultar();
        $sale = Sales::find($id);
        $cliente = Cliente::find($sale->idcliente);
        $buscar = Facelectron::where('idsales', $id)->get();
        $configuracion = Configuracion::find($buscar[0]->idconfigfact);
        $attach = public_path($buscar[0]->rutaxml);
        //$subject = 'Factura #'.$buscar[0]->clave;
        // $subject = 'Factura #'.$buscar[0]->numdoc.' Emisor: '.$configuracion->nombre_emisor.' Cedula Emisor : '.$configuracion->numero_id_emisor;
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
$emails = [];

// Construcción de la lista de correos según la presencia de $sale->cc_correo
$cc_correo_str = $sale->cc_correo ?? ''; // por seguridad en caso de que no exista
if (trim($cc_correo_str) !== '') {
    // Caso: hay cc_correo, usar solo: correo principal + cc_correo
    $emails[] = trim($cliente->email); // correo principal del cliente

    // Agregar los correos de cc_correo (posiblemente varias direcciones separadas por coma)
    $cc_emails = explode(',', $cc_correo_str);
    foreach ($cc_emails as $cc_email) {
        $emails[] = trim($cc_email);
    }
} else {
    // Caso original: usar correo principal y los emails adicionales (si existen)
    if (!is_null($cliente->additional_email)) {
        $adit_em = explode(',', $cliente->additional_email);

        // Correo principal del cliente
        $emails[] = trim($cliente->email);

        // Correos adicionales
        foreach ($adit_em as $adicional_em) {
            $emails[] = trim($adicional_em);
        }
    } else {
        // Solo el correo principal
        $emails[] = trim($cliente->email);
    }
}

// Agregar el correo electrónico del emisor a la lista de correos
if (isset($configuracion->email_emisor) && !empty($configuracion->email_emisor) && $configuracion->auto_copia_email == 1 ) {
    $emails[] = trim($configuracion->email_emisor);
}

// Ahora $emails contiene todos los correos a los que se quiere enviar
$for = $emails;

// Aquí podrías utilizar $for para enviar el correo
        $arreglo = $sale;
        if (!is_null($buscar[0]->estatushacienda)) {

            if ($buscar[0]->estatushacienda != 'procesando') {

                if ($buscar[0]->estatushacienda != 'pendiente') {

                    try {

                        $attach2 =  public_path($buscar[0]->respuesta_xml);
                        $attach3 =  public_path($buscar[0]->pdf_factura);
                        Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $attach2, $attach3){
                            $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                            $msj->subject($subject);
                            $msj->to($for);
                            $msj->attach($attach);
                            $msj->attach($attach2);
                            $msj->attach($attach3);
                        });
                       $update  = Facelectron::where('idsales', $id)->update([
                           [ 'enviado_correo' => 2],
                           ['creado_por' => Auth::user()->email ]
                        ]);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                } else {

                    try {

                        $attach2 =  public_path($buscar[0]->pdf_factura);
                        Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $attach2){
                            $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                            $msj->subject($subject);
                            $msj->to($for);
                            $msj->attach($attach);
                            $msj->attach($attach2);
                        });
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            } else {

                try {
                    $attach2 =  public_path($buscar[0]->pdf_factura);
                    Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $attach2){
                        $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                        $msj->subject($subject);
                        $msj->to($for);
                        $msj->attach($attach);
                        $msj->attach($attach2);
                    });
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        } else {

            try {

                Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $arreglo){
                    $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                    $msj->subject($subject);
                    $msj->to($for);
                    $msj->attach($attach);
                });
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        return redirect()->route('facturar.imprimir', $id);
    }

        public function correoXmlNC(Request $request, $id)
    {
        app('App\Http\Controllers\PeticionesController')->ajaxConsultarNC();
        $sale = Sales::find($id);
        $cliente = Cliente::find($sale->idcliente);
        $buscar = Facelectron::where('idsales', $id)->get();
        $attach = public_path($buscar[0]->rutaxml);
        $subject = 'Nota de Credito #'.$buscar[0]->clave;
        
$emails = [];

// Construcción de la lista de correos según la presencia de $sale->cc_correo
$cc_correo_str = $sale->cc_correo ?? ''; // por seguridad en caso de que no exista
if (trim($cc_correo_str) !== '') {
    // Caso: hay cc_correo, usar solo: correo principal + cc_correo
    $emails[] = trim($cliente->email); // correo principal del cliente

    // Agregar los correos de cc_correo (posiblemente varias direcciones separadas por coma)
    $cc_emails = explode(',', $cc_correo_str);
    foreach ($cc_emails as $cc_email) {
        $emails[] = trim($cc_email);
    }
} else {
    // Caso original: usar correo principal y los emails adicionales (si existen)
    if (!is_null($cliente->additional_email)) {
        $adit_em = explode(',', $cliente->additional_email);

        // Correo principal del cliente
        $emails[] = trim($cliente->email);

        // Correos adicionales
        foreach ($adit_em as $adicional_em) {
            $emails[] = trim($adicional_em);
        }
    } else {
        // Solo el correo principal
        $emails[] = trim($cliente->email);
    }
}

// Agregar el correo electrónico del emisor a la lista de correos
if (isset($configuracion->email_emisor) && !empty($configuracion->email_emisor) && $configuracion->auto_copia_email == 1 ) {
    $emails[] = trim($configuracion->email_emisor);
}

// Ahora $emails contiene todos los correos a los que se quiere enviar
$for = $emails;

// Aquí podrías utilizar $for para enviar el correo
        $arreglo = $sale;
        if (!is_null($buscar[0]->estatushacienda)) {

            if ($buscar[0]->estatushacienda != 'procesando') {

                if ($buscar[0]->estatushacienda != 'pendiente') {

                    try {

                        $attach2 =  public_path($buscar[0]->respuesta_xml);
                        $attach3 =  public_path($buscar[0]->pdf_factura);
                        Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $attach2, $attach3){
                            $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                            $msj->subject($subject);
                            $msj->to($for);
                            $msj->attach($attach);
                            $msj->attach($attach2);
                            $msj->attach($attach3);
                        });
                       $update  = Facelectron::where('idsales', $id)->update([
                           [ 'enviado_correo' => 2],
                           ['creado_por' => Auth::user()->email ]
                        ]);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                } else {

                    try {

                        $attach2 =  public_path($buscar[0]->pdf_factura);
                        Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $attach2){
                            $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                            $msj->subject($subject);
                            $msj->to($for);
                            $msj->attach($attach);
                            $msj->attach($attach2);
                        });
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            } else {

                try {

                    $attach2 =  public_path($buscar[0]->pdf_factura);
                    Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $attach2){
                        $msj->from("facturacion@snsilvestre.com","Sistema Oscar Mairena");
                        $msj->subject($subject);
                        $msj->to($for);
                        $msj->attach($attach);
                        $msj->attach($attach2);
                    });
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        } else {

            try {

                Mail::send('mails.respuesta_hacienda',$request->all(), function($msj) use($subject,$for,$attach, $arreglo){
                    $msj->from("facturacion@snsilvestre.com","Sistema Oscar Mairena");
                    $msj->subject($subject);
                    $msj->to($for);
                    $msj->attach($attach);
                });
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        return redirect()->route('notacredito.index')->withStatus(__('Factura Enviada Por Email Correctamente.'));
    }

public function reenviarCorreoManual(Request $request)
{
    $request->validate([
        'sale_id' => 'required|integer|exists:sales,idsale',
        'email'   => 'required|email'
    ]);

    $id = $request->input('sale_id');
    $customEmail = $request->input('email');

    \Log::info("reenviarCorreoManual: inicio idsales={$id} email={$customEmail}");

    try {
        $sale = Sales::findOrFail($id);
        $cliente = Cliente::find($sale->idcliente);
        $buscar = Facelectron::where('idsales', $id)->get();
        if ($buscar->isEmpty()) {
            return response()->json(['message' => 'Datos de factura no encontrados.'], 400);
        }

        $configuracion = Configuracion::find($buscar[0]->idconfigfact);
        $attach = public_path($buscar[0]->rutaxml);

        // Construir subject en todos los caminos
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

        $for = [$customEmail];

        // Envío (ajusta según tus adjuntos si aplica)
        $arreglo = $sale;
        if (!is_null($buscar[0]->estatushacienda)) {
            if ($buscar[0]->estatushacienda != 'procesando' && $buscar[0]->estatushacienda != 'pendiente') {
                $attach2 = public_path($buscar[0]->respuesta_xml);
                $attach3 = public_path($buscar[0]->pdf_factura);

                Mail::send('mails.respuesta_hacienda', ['idsale' => $id], function($msj) use($subject, $for, $attach, $attach2, $attach3){
                    $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                    $msj->subject($subject);
                    $msj->to($for);
                    $msj->attach($attach);
                    $msj->attach($attach2);
                    $msj->attach($attach3);
                });
            } else {
                $attach2 = public_path($buscar[0]->pdf_factura);
                Mail::send('mails.respuesta_hacienda', ['idsale' => $id], function($msj) use($subject, $for, $attach, $attach2){
                    $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                    $msj->subject($subject);
                    $msj->to($for);
                    $msj->attach($attach);
                    $msj->attach($attach2);
                });
            }
        } else {
            Mail::send('mails.respuesta_hacienda', ['idsale' => $id], function($msj) use($subject, $for, $attach){
                $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                $msj->subject($subject);
                $msj->to($for);
                $msj->attach($attach);
            });
        }

        Facelectron::where('idsales', $id)->update([
            'enviado_correo' => 2,
            'creado_por' => Auth::user()->email
        ]);

        return response()->json(['success' => true, 'message' => 'Correo enviado correctamente.']);
    } catch (\Throwable $th) {
        \Log::error("reenviarCorreoManual: error idsales={$id} error=".$th->getMessage());
        return response()->json(['message' => 'Error al enviar correo. Detalle: '.$th->getMessage()], 500);
    }
}

        public function reenviarCorreoXml($id)
    {
        app('App\Http\Controllers\PeticionesController')->ajaxConsultar();
        $sale = Sales::find($id);
        
        $cliente = Cliente::find($sale->idcliente);
        $buscar = Facelectron::where('idsales', $id)->get();
        $configuracion = Configuracion::find($buscar[0]->idconfigfact);
        $attach = public_path($buscar[0]->rutaxml);
       // $subject = 'Factura #'.$buscar[0]->clave;
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
      $emails = [];

// Construcción de la lista de correos según la presencia de $sale->cc_correo
$cc_correo_str = $sale->cc_correo ?? ''; // por seguridad en caso de que no exista
if (trim($cc_correo_str) !== '') {
    // Caso: hay cc_correo, usar solo: correo principal + cc_correo
    $emails[] = trim($cliente->email); // correo principal del cliente

    // Agregar los correos de cc_correo (posiblemente varias direcciones separadas por coma)
    $cc_emails = explode(',', $cc_correo_str);
    foreach ($cc_emails as $cc_email) {
        $emails[] = trim($cc_email);
    }
} else {
    // Caso original: usar correo principal y los emails adicionales (si existen)
    if (!is_null($cliente->additional_email)) {
        $adit_em = explode(',', $cliente->additional_email);

        // Correo principal del cliente
        $emails[] = trim($cliente->email);

        // Correos adicionales
        foreach ($adit_em as $adicional_em) {
            $emails[] = trim($adicional_em);
        }
    } else {
        // Solo el correo principal
        $emails[] = trim($cliente->email);
    }
}

// Agregar el correo electrónico del emisor a la lista de correos
if (isset($configuracion->email_emisor) && !empty($configuracion->email_emisor) && $configuracion->auto_copia_email == 1 ) {
    $emails[] = trim($configuracion->email_emisor);
}

// Ahora $emails contiene todos los correos a los que se quiere enviar
$for = $emails;

// Aquí podrías utilizar $for para enviar el correo
        $arreglo = $sale;
        if (!is_null($buscar[0]->estatushacienda)) {

            if ($buscar[0]->estatushacienda != 'procesando') {

                if ($buscar[0]->estatushacienda != 'pendiente') {

                    try {

                        $attach2 =  public_path($buscar[0]->respuesta_xml);
                        $attach3 =  public_path($buscar[0]->pdf_factura);
                        Mail::send('mails.respuesta_hacienda',['idsale' => $id], function($msj) use($subject,$for,$attach, $attach2, $attach3){
                            $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                            $msj->subject($subject);
                            $msj->to($for);
                            $msj->attach($attach);
                            $msj->attach($attach2);
                            $msj->attach($attach3);
                        });
                       $update  = Facelectron::where('idsales', $id)->update([
                           [ 'enviado_correo' => 2],
                           ['creado_por' => Auth::user()->email ]
                        ]);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                } else {

                    try {

                        $attach2 =  public_path($buscar[0]->pdf_factura);
                        Mail::send('mails.respuesta_hacienda',['idsale' => $id], function($msj) use($subject,$for,$attach, $attach2){
                            $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                            $msj->subject($subject);
                            $msj->to($for);
                            $msj->attach($attach);
                            $msj->attach($attach2);
                        });
                      $update  = Facelectron::where('idsales', $id)->update([
                           [ 'enviado_correo' => 2],
                           //['creado_por' => Auth::user()->email ]
                        ]);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            } else {

                try {
                    $attach2 =  public_path($buscar[0]->pdf_factura);
                    Mail::send('mails.respuesta_hacienda',['idsale' => $id], function($msj) use($subject,$for,$attach, $attach2){
                        $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                        $msj->subject($subject);
                        $msj->to($for);
                        $msj->attach($attach);
                        $msj->attach($attach2);
                    });
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        } else {

            try {

                Mail::send('mails.respuesta_hacienda',['idsale' => $id], function($msj) use($subject,$for,$attach, $arreglo){
                    $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                    $msj->subject($subject);
                    $msj->to($for);
                    $msj->attach($attach);
                });
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return redirect()->route('facturar.index')->withStatus(__('Factura Enviada Por Email Correctamente.'));
    }

        public function envio_masivo()
    {
        $arreglo = DB::table('facelectron')->where([
            ['enviado_correo', '=', '1'],
           //['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
       
        foreach ($arreglo as $array) {
            $this->reenviarCorreoXml($array->idsales);
             $update  = Facelectron::where('idsales', $array->idsales)->update([
                            'enviado_correo' => 2 ]);
        }
        return redirect()->route('facturar.index')->withStatus(__('Facturas Enviadas Por Email Correctamente.'));

    }

 public function correoCxc(Request $request, $id)
    {
        $log_cxcobrar = Log_cxcobrar::find($id);
        $mov_cxcobrar = Mov_cxcobrar::find($log_cxcobrar->idmovcxcobrar);
        $cxcobrar = Cxcobrar::find($mov_cxcobrar->idcxcobrar);
        $cliente = Cliente::find($cxcobrar->idcliente);
        $subject = 'Recibo #'.$log_cxcobrar->num_recibo_abono;
        $for = $cliente->email;
        $datos = [
            'cxcobrar'=> $cxcobrar,
            'cliente' => $cliente,
            'log_cxcobrar' => $log_cxcobrar,
            'mov_cxcobrar' => $mov_cxcobrar
        ];
        try {

            Mail::send('mails.abono',$datos, function($msj) use($subject,$for){
                $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                $msj->subject($subject);
                $msj->to($for);
            });
        } catch (\Throwable $th) {
            //throw $th;
        }

        return redirect()->route('cxcobrar.imprimir', ['id' => $id]);
        //return redirect()->route('cxcobrar.index')->withStatus(__('Abono Agregado Correctamente.'));
    }

        

        public function correoCxcmasivo(Request $request)
    {
        $datos = $request->all();
        $for = [];
        $for[] = 'luisd_484@hotmail.com';
        foreach ($datos['correos'] as $dat) {
            $log_cxcobrar = Log_cxcobrar::find($dat);
            $mov_cxcobrar = Mov_cxcobrar::find($log_cxcobrar->idmovcxcobrar);
            $cxcobrar = Cxcobrar::find($mov_cxcobrar->idcxcobrar);
            $cliente = Cliente::find($cxcobrar->idcliente);
            $subject = 'Recibo #'.$log_cxcobrar->num_recibo_abono;
            $for[] = $cliente->email;
            $datos = [
                'cxcobrar'=> $cxcobrar,
                'cliente' => $cliente,
                'log_cxcobrar' => $log_cxcobrar,
                'mov_cxcobrar' => $mov_cxcobrar
            ];
            try {
                Mail::send('mails.abono',$datos, function($msj) use($subject,$for){
                    $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                    $msj->subject($subject);
                    $msj->to($for);
                });
            } catch (\Throwable $th) {
                //throw $th;
            }

        }
        return redirect()->route('cxcobrar.index')->withStatus(__('Abonos Agregados Correctamente.'));
    }

        public function correoCxp(Request $request, $id)
    {
        $log_cxpagar = Log_cxpagar::find($id);
        $mov_cxpagar = Mov_cxpagar::find($log_cxpagar->idmovcxpagar);
        $cxpagar = Cxpagar::find($mov_cxpagar->idcxpagar);
        $cliente = Cliente::find($cxpagar->idcliente);
        $subject = 'Recibo #'.$log_cxpagar->num_recibo_abono;
        $for = $cliente->email;
        $datos = [
            'cxpagar'=> $cxpagar,
            'cliente' => $cliente,
            'log_cxpagar' => $log_cxpagar,
            'mov_cxpagar' => $mov_cxpagar
        ];
        try {
            Mail::send('mails.abono_cxp',$datos, function($msj) use($subject,$for){
                $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                $msj->subject($subject);
                $msj->to($for);
            });
        } catch (\Throwable $th) {
            //throw $th;
        }

        return redirect()->route('cxpagar.index')->withStatus(__('Abono Agregado Correctamente.'));
    }

        public function correoCotizacion(Request $request, $id)
    {
        app('App\Http\Controllers\ReportesController')->pdfPedido($id);
        $pedido = Pedidos::find($id);
        $cliente = Cliente::find($pedido->idcliente);
        $configuracion = Configuracion::find($pedido->idconfigfact);
        $subject = 'Pedido #'.$pedido->numero_documento;
        //$for = $cliente->email;
        $from = $configuracion->email_emisor;
        $arreglo = $pedido;
        $attach =  public_path($pedido->pdf_pedido);
        $datos = [
            'cliente' => $cliente,
            'pedido' => $pedido
        ];
        $emails = [];

// Construcción de la lista de correos según la presencia de $sale->cc_correo
$cc_correo_str = $sale->cc_correo ?? ''; // por seguridad en caso de que no exista
if (trim($cc_correo_str) !== '') {
    // Caso: hay cc_correo, usar solo: correo principal + cc_correo
    $emails[] = trim($cliente->email); // correo principal del cliente

    // Agregar los correos de cc_correo (posiblemente varias direcciones separadas por coma)
    $cc_emails = explode(',', $cc_correo_str);
    foreach ($cc_emails as $cc_email) {
        $emails[] = trim($cc_email);
    }
} else {
    // Caso original: usar correo principal y los emails adicionales (si existen)
    if (!is_null($cliente->additional_email)) {
        $adit_em = explode(',', $cliente->additional_email);

        // Correo principal del cliente
        $emails[] = trim($cliente->email);

        // Correos adicionales
        foreach ($adit_em as $adicional_em) {
            $emails[] = trim($adicional_em);
        }
    } else {
        // Solo el correo principal
        $emails[] = trim($cliente->email);
    }
}

// Agregar el correo electrónico del emisor a la lista de correos
if (isset($configuracion->email_emisor) && !empty($configuracion->email_emisor) && $configuracion->auto_copia_email == 1 ) {
    $emails[] = trim($configuracion->email_emisor);
}

// Ahora $emails contiene todos los correos a los que se quiere enviar
$for = $emails;
        try {

            Mail::send('mails.cotizacion',$datos, function($msj) use($subject,$for,$attach){
                $msj->from("facturacion@snsilvestre.com","Factura Electrónica San Esteban");
                $msj->subject($subject);
                $msj->to($for);
                $msj->attach($attach);
            });
        } catch (\Throwable $th) {
            //throw $th;
        }

        return redirect()->route('pedidos.index')->withStatus(__('Cotización Enviada Por Email Correctamente.'));
    }
}
