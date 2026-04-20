<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Actividad;
use App\Receptor;
use App\Receptor_fec;
use Auth;
use DB;
use App\Configuracion_automatica;
use App\App_settings;

class CronController extends Controller
{
    public function startCron()
    {

        $respuesta = $this->consultaGenerica();
        return $respuesta;
    }
 
public function consultaGenerica()  //modificada con ai
{  
    $imap = null; // Inicializar la variable IMAP
    try {  
        // Configuración del servidor IMAP
        $server = '{imap.gmail.com:993/imap/ssl/novalidate-cert}';  

        // Obtener configuraciones de correo
        $settings = App_settings::where('idsettings', 1)->first();  
        if (!$settings) {
            throw new Exception('Configuración de correo no encontrada.');
        }

        $username = $settings->correo;  
        $password = $settings->contrasena;   

        // Conectar a Gmail
        $imap = imap_open($server, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());  

        // Buscar todos los mensajes en la bandeja de entrada
        $emails = imap_search($imap, 'ALL');  

        if ($emails) {  
            // Invertir el orden para procesar del más reciente al más antiguo  
            $emails = array_reverse($emails); 
            $batchSize = 100; // Tamaño del lote
            $emailsCount = count($emails);
            
            // Procesamiento por lotes
            for ($i = 0; $i < $emailsCount; $i += $batchSize) {
                $batch = array_slice($emails, $i, $batchSize); // Obtener el lote actual
                foreach ($batch as $email_number) {  
                    $header = imap_header($imap, $email_number);  

                    // Procesar el encabezado
                    $email[$email_number]['from'] = $header->from[0]->mailbox . '@' . $header->from[0]->host;  
                    $email[$email_number]['date'] = $header->udate;  
                    $structure = imap_fetchstructure($imap, $email_number);  
                    $attachments = [];  

                    // Procesar los adjuntos
                    if (isset($structure->parts) && count($structure->parts)) {  
                        for ($j = 0; $j < count($structure->parts); $j++) {  
                            $attachments[$j] = [  
                                'is_attachment' => false,  
                                'filename' => '',  
                                'name' => '',  
                                'attachment' => '',  
                            ];  

                            // Obtener el nombre del archivo del adjunto
                            if ($structure->parts[$j]->ifdparameters) {  
                                foreach ($structure->parts[$j]->dparameters as $object) {  
                                    if (strtolower($object->attribute) == 'filename') {  
                                        $attachments[$j]['is_attachment'] = true;  
                                        $attachments[$j]['filename'] = $object->value;  
                                    }  
                                }  
                            }  

                            // Obtener el nombre del archivo a partir de los parámetros
                            if ($structure->parts[$j]->ifparameters) {  
                                foreach ($structure->parts[$j]->parameters as $object) {  
                                    if (strtolower($object->attribute) == 'name') {  
                                        $attachments[$j]['is_attachment'] = true;  
                                        $attachments[$j]['name'] = $object->value;  
                                    }  
                                }  
                            }  

                            // Procesar el adjunto
                            if ($attachments[$j]['is_attachment']) {  
                                $attachments[$j]['attachment'] = imap_fetchbody($imap, $email_number, $j + 1);  
                                
                                if ($structure->parts[$j]->encoding == 3) { // BASE64  
                                    $attachments[$j]['attachment'] = base64_decode($attachments[$j]['attachment']);  
                                } elseif ($structure->parts[$j]->encoding == 4) { // QUOTED-PRINTABLE  
                                    $attachments[$j]['attachment'] = quoted_printable_decode($attachments[$j]['attachment']);  
                                }  
                            }  
                        }  
                    }  

                    // Variable para verificar si hay adjuntos .xml
                    $hasXmlAttachment = false;

                    // Guardar los adjuntos
foreach ($attachments as $attachment) {  
    if ($attachment['is_attachment']) { 
        $name = mb_decode_mimeheader($attachment['name']);  
        $ext = pathinfo($name, PATHINFO_EXTENSION);  
        $contents = $attachment['attachment'];  

        // Usando strtolower para manejar extensiones en mayúsculas
        if (strtolower($ext) === 'xml') {  
            $safe_name = preg_replace('([^.A-Za-z0-9])', '', pathinfo($name, PATHINFO_FILENAME)); // Obtener solo el nombre sin la extensión
            
            // Generar un identificador único sin puntos
            $random_bytes = bin2hex(random_bytes(5)); // 10 caracteres hexadecimales
            $unique_name = $safe_name . '_' . $random_bytes . '.xml'; 

            // Generar la ruta
            $ruta = public_path('/correo_comun/' . $unique_name);  
            file_put_contents($ruta, $contents);  

            // Lógica para hacer la llamada a la API
            $hasXmlAttachment = true; // Marcar que hemos encontrado un adjunto XML
        }
    }
}

                    // Si no hay adjuntos XML, eliminar el correo
                    if (!$hasXmlAttachment) {
                        imap_delete($imap, $email_number); // Eliminar correo
                    } else {
                        // Marcar el correo como leído               
                        imap_setflag_full($imap, $email_number, '\\Seen');  

                        // Mover el correo a la etiqueta "2025"
                        imap_setflag_full($imap, $email_number, '\\Flagged'); // Añadir la marca para que se mueva
                        // Aquí puedes llamar a expunge después de todos los correos que quieres mover
                    }
                }
                // Ejecutar el expunge para eliminar los correos marcados
                imap_expunge($imap);
                
                // Mover correos a la carpeta "2025"
                foreach ($batch as $email_number) {
                    if ($hasXmlAttachment) {
                        imap_mail_move($imap, $email_number, '2025'); // Mover a la etiqueta '2025'
                    }
                }
            }
        }  

        return 'All process success';  

    } catch (Exception $e) {  
        return 'Excepción capturada: ' . $e->getMessage();  
    } finally {
        // Asegúrate de cerrar la conexión IMAP si se estableció
        if ($imap) {
            imap_close($imap);  
        }
    }  
}

    public function startCronStore()
    {
        $configuraciones = Configuracion::all();


        foreach ($configuraciones as $config) {
            $this->readDir($config->idconfigfact);
        }
    }
 public function readDir($idconfigfact)
    {
        $config = Configuracion::find($idconfigfact);
        $username = ''.$config->email_servidor;

            if ($username != 'pruebas_internas@feisaac.com') {

                $ruta = public_path('/XML/' . $idconfigfact . "/email");
                \Log::info("idconfigfact: ".$idconfigfact);

                $carpeta = @scandir($ruta);

                if (count($carpeta) > 2) {

                    if (count($carpeta) > 2) {
                        $conteo = count($carpeta);
                    } else {
                        $conteo = count($carpeta);
                    }

                    for ($x=2; $x < $conteo; $x++) {

                        $strContents = file_get_contents($ruta.'/'.$carpeta[$x]);
                        $strDatas = $this->Xml2Array($strContents);
                        
                          // Obtener la versi贸n del XML  
$namespace = $strDatas['FacturaElectronica_attr']['xmlns']   
    ?? $strDatas['NotaCreditoElectronica_attr']['xmlns']   
    ?? $strDatas['FacturaElectronicaCompra_attr']['xmlns']   
    ?? $strDatas['NotaDebitoElectronica_attr']['xmlns']   
    ?? null;  

if ($namespace) {  
    preg_match('/v(\d+\.\d+)/', $namespace, $matches);  
    $version = isset($matches[1]) ? $matches[1] : null;  
}else{
    $version = '4.0'; 
}   
// Obtener la versi贸n del XML 
                        if (isset($strDatas['FacturaElectronica'])) {

                            $documento = 'FacturaElectronica';
                        } elseif (isset($strDatas['NotaDebitoElectronica'])) {

                            $documento = 'NotaDebitoElectronica';
                        } elseif (isset($strDatas['NotaCreditoElectronica'])) {

                            $documento = 'NotaCreditoElectronica';
                        }elseif (isset($strDatas['FacturaElectronicaCompra'])) {

                            $documento = 'FacturaElectronicaCompra';
                        } else {
                            

                            //unlink($ruta.'/'.$carpeta[$x]);
                            continue;
                        }
                        
                         if($version == '4.4'){
                         $codigo_actividad= $strDatas[$documento]['CodigoActividadEmisor'];
                         }else{
                         $codigo_actividad= $strDatas[$documento]['CodigoActividad'];
                         }
                         
                        if ($strDatas[$documento]['NumeroConsecutivo']) {

                            // Variables del XML
                            $configuracion = Configuracion::find($idconfigfact);
                            $tipo_documento = substr($strDatas[$documento]['NumeroConsecutivo'], 8, 2);
                            $punto_venta = substr($strDatas[$documento]['NumeroConsecutivo'], 3, 5);
                            $sucursal = substr($strDatas[$documento]['NumeroConsecutivo'], 0, 3);
                            $condventa = $strDatas[$documento]['CondicionVenta'];
                            
                            
                            
                            \Log::info("N杩唌ero consecutivo para {$documento}: " . $strDatas[$documento]['NumeroConsecutivo']); 
                             \Log::info("configuracion : " . $idconfigfact); 
                            if($documento!='FacturaElectronicaCompra'){
                            $consulta = Receptor::where('cedula_emisor', '=', ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'])
                            ->where('consecutivo_doc_receptor', '=', ''.$strDatas[$documento]['NumeroConsecutivo'])
                            ->count();
                            
                            if(isset($strDatas[$documento]['Receptor']['Identificacion']['Numero'])){
                            $ideti=$strDatas[$documento]['Receptor']['Identificacion']['Numero'];
                            }else{
                            if(isset($strDatas[$documento]['Receptor']['IdentificacionExtranjero'])){
                            $ideti=$strDatas[$documento]['Receptor']['IdentificacionExtranjero'];
                            }else{
                            $ideti=0; 
                            }
                            }
                            }else{
                            //si es FEC
                             $consulta = Receptor_fec::where('cedula_emisor', '=', ''.$strDatas[$documento]['Receptor']['Identificacion']['Numero'])
                            ->where('consecutivo_doc_receptor', '=', ''.$strDatas[$documento]['NumeroConsecutivo'])
                            ->count();
                            $ideti=$strDatas[$documento]['Emisor']['Identificacion']['Numero'];
                            }
                    
                          // dd($condventa);
                           
                            if ($condventa != 11) {  //valida que si el documento viene con condicion de venta 11 no se acepte como factura para evitar errores en la declaracion de impuestos
                            if ($consulta == 0) {

                                //validacion descomenda por omairena 26-04-2021 1:13pm hora CR
                                //VALIDACION PARA VER SI EL DOCUMENTO PERTENECE A EL RECEPTRO
                                if($configuracion->numero_id_emisor.'' === ''.$ideti){

                                    // Mensaje de Procesado o aceptado provicional solo para cumplir con los valores
                                    $mensaje = 1;
                                    if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {

                                        $total_impuesto =  $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];

                                    } else {

                                        $total_impuesto = '0.00000';

                                    }

                                    if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {

                                        $imp_a_acreditar = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                                        $gasto_aplicable = '0.00000';

                                    } else {

                                        $imp_a_acreditar = '0.00000';
                                        $gasto_aplicable = '0.00000';

                                    }

                                    if ($config->config_automatica > 0) {
                                        
                                        if($tipo_documento!='08'){

                                        // Nueva validacion para el proceso automatico con Cron y recepcion automatica configurable
                                        // LDCG 15-03-2022
                                        $controller = new ReceptorController();
                                        $seguridad = $controller->armarSeguridad($idconfigfact);
                                        $configuracion_autm = Configuracion_automatica::where('idconfigfact', '=', $idconfigfact)->first();
                                        switch ($configuracion_autm->estatus) {
                                            case 'aprobado':
                                                $tipo_doc_receptor = '05';
                                            break;
                                            case 'rechazado':
                                                $tipo_doc_receptor = '07';
                                            break;
                                            case 'pendiente':
                                                $tipo_doc_receptor = '06';
                                            break;
                                        }
                                      /// Consecutivo traigo el consecutivo
                                        //$num_receptor = DB::table('consecutivos')->where([
                                        //    ['idcaja', '=', $configuracion_autm->idcaja],
                                        //    ['tipo_documento', '=',  $tipo_doc_receptor],
                                        //])->value('numero_documento');
                                        
                                       // $num_receptor = str_pad($num_receptor, 10, "0", STR_PAD_LEFT);
                                        
                                        ///NUEVO CNSECUTIVO 03-07-2025
             //nuevo mandejo de consecutivos
             // Paso 1: Obtener el rango de números de documento  
$consecutivo = DB::table('consecutivos')->where('idcaja', $configuracion_autm->idcaja)  
    ->where('tipo_documento', $tipo_doc_receptor)  
    ->first();  

if (!$consecutivo) {  
    // Manejo de error si no se encuentra el consecutivo  
    Session::flash('message', "No se encontró el consecutivo.");  
    return redirect()->back();  
}  

$docDesde = $consecutivo->doc_desde; 
$docHasta = $consecutivo->numero_documento;  

// Paso 2: Obtener los números de documento emitidos en sales  
$numerosEmitidos = DB::table('receptor')  
    ->where('idcaja', $configuracion_autm->idcaja)  
    ->where('tipo_documento', $tipo_doc_receptor)  
    ->pluck('numero_documento_receptor')  
    ->toArray();  
 
// Paso 3: Comparar los números  
$huecos = [];  
for ($i = $docDesde; $i <= $docHasta; $i++) {  
    // Completar con ceros a la izquierda hasta 10 dígitos  
    $numeroCompleto = str_pad($i, 10, "0", STR_PAD_LEFT);  
    if (!in_array($numeroCompleto, $numerosEmitidos)) {  
        $huecos[] = $numeroCompleto; // Agregar a los huecos si no está emitido  
    }  
}  

// Resultado  
if (empty($huecos)) {  
               $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $configuracion_autm->idcaja],
                ['tipo_documento', '=', $tipo_doc_receptor],
            ])->get();
            $num_receptor = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        
          $consecutivo_fac = DB::table('receptor')->where([  
    ['idcaja', '=', $configuracion_autm->idcaja],  
    ['tipo_documento', '=', $tipo_doc_receptor],  
    ['numero_documento_receptor', '=', $num_receptor],  
   // ['idreceptor', '!=', $datos['idrecepcion']],
   // ['pendiente', '=' ,1]
])->get(); 

 //dd($consecutivo_fac);  
if ($consecutivo_fac->isEmpty()) {  
                $new = $num_receptor + 1;
             
                $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$tipo_doc_receptor.' and idcaja = '.$configuracion_autm->idcaja);
                
                
}else{
    
    $new = $num_receptor +1;
    $num_receptor=str_pad($new, 10, "0", STR_PAD_LEFT);;
    $new=$num_receptor+1;
    
    $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$tipo_doc_receptor.' and idcaja = '.$configuracion_autm->idcaja);
        
    //Session::flash('message', "Hay un problema con el consecutivo de la factura, SOLO SE PUEDE FACTURAR CON UNA PANTALLA ACTIVA, ve a VER DOC ELEC y LIMPA FACTURA EN PROCESO o Elimina alguna de los Documentos duplicados para continuar");  
    //return redirect()->back(); 
    // }
}
} else {  
    
   
    $num_receptor=$huecos[0];
  
    }       
            
          // dd($num_receptor);
            
            ///FIN NUEVO CONSECUTIVO
                                        // Busco el codigo de actividad por empresa tomo el primer registro
                                        $actividad = Actividad::where('idconfigfact', $idconfigfact)->first();
                                        // Guardo en BD de receptor con datos para cuando se envia automaticamente
                                        if(isset($strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'])){
                                        if($strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']!='CRC'){
                                            $moneda=$strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'];
                                            $tc=$strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                        }else{
                                            $moneda='CRC';
                                            $tc=1;
                                        }
                                        }else{
                                            $moneda='CRC'; 
                                             $tc=1;
                                        }
                                        
                                         if(!isset($strDatas[$documento]['ResumenFactura']['TotalComprobante'])){
                                        $total_comprobante=0;
                                         }else{
                                            $total_comprobante =$strDatas[$documento]['ResumenFactura']['TotalComprobante'];
                                         }
                                      
                                        $receptor = Receptor::create(
                                            [
                                                'idconfigfact' => $idconfigfact,
                                                'idcaja' => $configuracion_autm->idcaja,
                                                'idcodigoactv' => ''.$actividad->idcodigoactv,
                                                'tipo_documento' => $tipo_doc_receptor,
                                                'detalle_mensaje' => $configuracion_autm->detalle_mensaje,
                                                'ruta_carga' => './XML/'.$idconfigfact.'/DocReceptor/Envio/email/'.$carpeta[$x],
                                                'fecha' => date('Y-m-d'),
                                                'nombre_emisor' => $strDatas[$documento]['Emisor']['Nombre'],
                                                'total_impuesto' => $total_impuesto,
                                                'total_comprobante' => $total_comprobante,//$strDatas[$documento]['ResumenFactura']['TotalComprobante'],
                                                'cedula_emisor' => $strDatas[$documento]['Emisor']['Identificacion']['Numero'],
                                                'clasifica_d151' => 1,
                                                'condicion_impuesto' => 0,
                                                'imp_creditar' => $imp_a_acreditar,
                                                'gasto_aplica' => $gasto_aplicable,
                                                'hacienda_imp_creditar' => $imp_a_acreditar,
                                                'hacienda_gasto_aplica' => $gasto_aplicable,
                                                'tipo_documento_recibido' => $tipo_documento,
                                                'fecha_xml_envio' => $strDatas[$documento]['FechaEmision'],
                                                'numero_documento_receptor' => $num_receptor,
                                                'consecutivo_doc_receptor' => ''.$strDatas[$documento]['NumeroConsecutivo'],
                                                'moneda' => $moneda,
                                                'tc' => $tc,
                                                'version' => $version,
                                                'codigo_act_xml' => $codigo_actividad,
                                            
                                                
                                            ]);
                                            $new_ruta = public_path('/XML/' . $idconfigfact . "/DocReceptor/Envio/email/");

                                            copy($ruta.'/'.$carpeta[$x], $new_ruta.''.$carpeta[$x]);
                                            unlink($ruta.'/'.$carpeta[$x]);
                                            $xml = [
                                                'tipoDocumento' => ''.$tipo_doc_receptor, // 05 Aceptado 06 Parcialmente aceptado 07 rechazado
                                                'sucursal' => ''.$sucursal, // 3 digitos sucursal de donde proviene el documento para el armado de clave
                                                'puntoVenta' => ''.$punto_venta, // 5 digitos punto de venta del cual se armo el documento
                                                'idreceptor' => ''.$receptor->idreceptor,
                                                'idconfigfact' => ''.$idconfigfact,
                                                'comando' => 0,
                                                'numeroFactura' => ''.$num_receptor, //correspondiente al numero del documento para el receptor
                                                'fechaEmision' => date('c'), // fecha de emision del documento no mayor a 72 horas, date_default_timezone_set debe ser costa rica
                                                'rutaxml' => ''.$new_ruta.''.$carpeta[$x], // ruta del xml para la factura es el XML recibido por el emisor
                                                'CondicionImpuesto' => '0',
                                                'MontoTotalImpuestoAcreditar' => ''.$imp_a_acreditar,
                                                'MontoTotalDeGastoAplicable' => ''.$gasto_aplicable,
                                                'Emisor' => array(
                                                    'NumeroCedulaEmisor' => ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'], // cedula del emisor del documento
                                                    'Mensaje' => ''.$mensaje, // 1 para cuando el mensaje es aceptado, o en su defecto igual a 05, 2 para 06 y 3 para 07
                                                    'CodigoActividad' => ''.$actividad->codigo_actividad,
                                                    'MontoTotalImpuesto' => ''.$total_impuesto, // Monto total del impuesto de la factura
                                                    'TotalFactura' => ''.$strDatas[$documento]['ResumenFactura']['TotalComprobante'], // Monto total de la factura
                                                    'DetalleMensaje' => ''.$configuracion_autm->detalle_mensaje // Alguna nota o detalle que queramos agregar para el Mensaje Receptor
                                                )
                                            ];
                                            include_once(public_path(). '/funcionFacturacion506.php');
                                            include_once(public_path(). '/consulta_documento.php');

                                            $facturar = Timbrar_receptor($xml, $seguridad);
                                            $num_receptor = $num_receptor + 1;
                                           // $consecutivo = DB::update('update consecutivos set numero_documento = '.$num_receptor.' where tipo_documento = '.$tipo_doc_receptor.' and idcaja = '.$configuracion_autm->idcaja);
                                            // Consulta posterior al envio del documento recepcionado
                                            $arreglo = DB::table('receptor')->where([
                                                ['pendiente', '=', '0'],
                                                ['idconfigfact', '=', $idconfigfact],
                                            ])->get();
                                            foreach ($arreglo as $array) {
                                                $xml_recepcion = [
                                                    'tipoDocumento' => ''.$array->tipo_documento, //tipo de documento a consultar
                                                    'numero_consecutivo' => ''.$array->consecutivo, //Numero consecutivo del documento
                                                    'clave' => ''.$array->clave, //clave del documento
                                                    'idreceptor' => ''.$array->idreceptor,
                                                    'comando' => 1,
                                                ];
                                                $envio = Enviar_documentos($xml_recepcion, $seguridad);
                                            }
                                            
                                        }else{
                                            ///si el doc es FEC
                                             $actividad = Actividad::where('idconfigfact', $idconfigfact)->first();
                                            $configuracion_autm = Configuracion_automatica::where('idconfigfact', '=', $idconfigfact)->first();
                                            
                                            $num_receptor = DB::table('consecutivos')->where([
                                            ['idcaja', '=', $configuracion_autm->idcaja],
                                            ['tipo_documento', '=',  '48'],
                                            ])->value('numero_documento');
                                            $num_receptor = $num_receptor + 1;
                                            $num_receptor = str_pad($num_receptor, 10, "0", STR_PAD_LEFT);
                                        
                                           $receptor = Receptor_fec::create(
                                            [
                                                'idconfigfact' => $idconfigfact,
                                                'idcaja' => $configuracion_autm->idcaja,
                                                'idcodigoactv' => ''.$actividad->idcodigoactv,
                                                'tipo_documento' => '48',
                                                'clave'=>$strDatas[$documento]['Clave'],
                                                'consecutivo'=> $num_receptor,
                                                'detalle_mensaje' => 'DOCUMENTO RECIBIDO AUTOMATICAMENTE REA',
                                                'ruta_carga' => './XML/'.$idconfigfact.'/DocReceptor/Envio/fec/'.$carpeta[$x],
                                                'fecha' => date('Y-m-d'),
                                                'nombre_emisor' => $strDatas[$documento]['Receptor']['Nombre'],
                                                'total_impuesto' => $total_impuesto,
                                                'total_comprobante' => $strDatas[$documento]['ResumenFactura']['TotalComprobante'],
                                                'cedula_emisor' => $strDatas[$documento]['Receptor']['Identificacion']['Numero'],
                                                'pendiente'=> '1',
                                                'estatus_hacienda'=> 'aceptado',
                                                'clasifica_d151' => 1,
                                                'condicion_impuesto' => 0,
                                                'imp_creditar' => $imp_a_acreditar,
                                                'gasto_aplica' => $gasto_aplicable,
                                                'hacienda_imp_creditar' => $imp_a_acreditar,
                                                'hacienda_gasto_aplica' => $gasto_aplicable,
                                                'tipo_documento_recibido' => $tipo_documento,
                                                'fecha_xml_envio' => $strDatas[$documento]['FechaEmision'],
                                                'numero_documento_receptor' => $num_receptor,
                                                'consecutivo_doc_receptor' => ''.$strDatas[$documento]['NumeroConsecutivo'],
                                                'moneda' => $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'],
                                                'tc' => $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'],
                                                'version' => $version,
                                                'codigo_act_xml' => $codigo_actividad,
                                            ]);
                                            $tipo_doc_receptor_FEC= 48;
                                              $consecutivo = DB::update('update consecutivos set numero_documento = '.$num_receptor.' where tipo_documento = '.$tipo_doc_receptor_FEC.' and idcaja = '.$configuracion_autm->idcaja);
                                        $new_ruta = public_path('/XML/' . $idconfigfact . "/DocReceptor/Envio/fec/");

                                        copy($ruta.'/'.$carpeta[$x], $new_ruta.''.$carpeta[$x]);
                                        unlink($ruta.'/'.$carpeta[$x]);
                                            
                                            
                                            
                                        }
                                    } else {

                                        $receptor = Receptor::create(
                                            [
                                                'idconfigfact' => $idconfigfact,
                                                'idcaja' => 1,
                                                'tipo_documento' => '05',
                                                'detalle_mensaje' => 'DOCUMENTO RECIBIDO AUTOMATICAMENTE',
                                                'ruta_carga' => './XML/'.$idconfigfact.'/DocReceptor/Envio/email/'.$carpeta[$x],
                                                'fecha' => date('Y-m-d'),
                                                'nombre_emisor' => $strDatas[$documento]['Emisor']['Nombre'],
                                                'total_impuesto' => $total_impuesto,
                                                'total_comprobante' => $strDatas[$documento]['ResumenFactura']['TotalComprobante'],
                                                'cedula_emisor' => $strDatas[$documento]['Emisor']['Identificacion']['Numero'],
                                                'clasifica_d151' => 1,
                                                'condicion_impuesto' => 0,
                                                'imp_creditar' => $imp_a_acreditar,
                                                'gasto_aplica' => $gasto_aplicable,
                                                'hacienda_imp_creditar' => $imp_a_acreditar,
                                                'hacienda_gasto_aplica' => $gasto_aplicable,
                                                'tipo_documento_recibido' => $tipo_documento,
                                                'fecha_xml_envio' => $strDatas[$documento]['FechaEmision'],
                                                'numero_documento_receptor' => '9999999999',
                                                'consecutivo_doc_receptor' => ''.$strDatas[$documento]['NumeroConsecutivo'],
                                                'moneda' => $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'],
                                                'tc' => $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'],
                                                'version' => $version,
                                                'codigo_act_xml' => $codigo_actividad,
                                            ]);

                                        $new_ruta = public_path('/XML/' . $idconfigfact . "/DocReceptor/Envio/email/");

                                        copy($ruta.'/'.$carpeta[$x], $new_ruta.''.$carpeta[$x]);
                                        unlink($ruta.'/'.$carpeta[$x]);

                                    }

                                    // Nueva validacion para el guardado del proveedor
                                    try {

                                        $this->registrarProveedor($idconfigfact ,$strDatas[$documento]);

                                    } catch (Exception $e) {

                                    }

                                } else {
                                        unlink($ruta.'/'.$carpeta[$x]);
                                    $message = 'Emisor no corresponde con el receptor de la factura.';
                                }
                            } else {

                                unlink($ruta.'/'.$carpeta[$x]);
                            }
                            } else {

                                unlink($ruta.'/'.$carpeta[$x]);
                            }
                        }else{
                            unlink($ruta.'/'.$carpeta[$x]);
                        }
                    }
                }
            }


    }
    public function Xml2Array($contents, $get_attributes=1, $priority = 'tag') {
            if(!$contents) return array();

            if(!function_exists('xml_parser_create')) {
                //print "'xml_parser_create()' function not found!";
                return array();
            }

            //Get the XML parser of PHP - PHP must have this module for the parser to work
            $parser = xml_parser_create('');
            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
            xml_parse_into_struct($parser, trim($contents), $xml_values);
            xml_parser_free($parser);

            if(!$xml_values) return;//Hmm...

            //Initializations
            $xml_array = array();
            $parents = array();
            $opened_tags = array();
            $arr = array();

            $current = &$xml_array; //Refference

            //Go through the tags.
            $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
            foreach($xml_values as $data) {
                unset($attributes,$value);//Remove existing values, or there will be trouble

                //This command will extract these variables into the foreach scope
                // tag(string), type(string), level(int), attributes(array).
                extract($data);//We could use the array by itself, but this cooler.

                $result = array();
                $attributes_data = array();

                if(isset($value)) {
                    if($priority == 'tag') $result = $value;
                    else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
                }

            //Set the attributes too.
            if(isset($attributes) and $get_attributes) {
                foreach($attributes as $attr => $val) {
                    if($priority == 'tag') $attributes_data[$attr] = $val;
                    else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if($type == "open") {//The starting of the tag '<tag>'
                $parent[$level-1] = &$current;
                if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                        $repeated_tag_index[$tag.'_'.$level] = 1;

                        $current = &$current[$tag];

                    } else { //There was another element with the same tag name

                        if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                            $repeated_tag_index[$tag.'_'.$level]++;
                        } else {//This section will make the value an array if multiple tags with the same name appear together
                            $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                            $repeated_tag_index[$tag.'_'.$level] = 2;

                            if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                unset($current[$tag.'_attr']);
                            }

                        }
                        $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                        $current = &$current[$tag][$last_item_index];
                    }

            } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if(!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
                } else { //If taken, put all things inside a list(array)
                    if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

                        if($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag.'_'.$level]++;

                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag.'_'.$level] = 1;
                        if($priority == 'tag' and $get_attributes) {
                            if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                unset($current[$tag.'_attr']);
                            }

                            if($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                    }
                }

            } elseif($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level-1];
            }
        }
            return($xml_array);
        }

    public function ejecutarComandoIndividual()
    {
        $this->consultaGenerica(Auth::user()->idconfigfact);
        $this->moveToRealFolder(Auth::user()->idconfigfact);
        $this->readDir(Auth::user()->idconfigfact);

        return redirect()->route('receptor.index')->withStatus(__('Comando Ejecutado Correctamente'));
    }

       public function registrarProveedor($idconfigfact, $xml)
    {
       $codigoActividad = $xml['CodigoActividad'] ?? $xml['CodigoActividadEmisor'] ?? null;
        $num_id = $xml['Emisor']['Identificacion']['Numero'];
        $qry = DB::table('clientes')
        ->select('clientes.*')
        ->where([
            ['clientes.num_id', '=', $num_id],
            ['clientes.idconfigfact', '=', $idconfigfact]
        ])->count();

        if ($qry == 0) {

            $data = array(
                'idconfigfact' => $idconfigfact,
                'tipo_id' =>  $xml['Emisor']['Identificacion']['Tipo'],
                'num_id' => $num_id,
                'nombre' => $xml['Emisor']['Nombre'],
                'email' => $xml['Emisor']['CorreoElectronico'] ?? 'feisaac@feisaac.com',
                'telefono' => $xml['Emisor']['Telefono']['NumTelefono'] ?? '24601755',
                'distrito' => $xml['Emisor']['Ubicacion']['Distrito'] ?? '01',
                'canton' => $xml['Emisor']['Ubicacion']['Canton'] ?? '01',
                'provincia' => $xml['Emisor']['Ubicacion']['Provincia'] ?? '2',
                'barrio' => $xml['Emisor']['Ubicacion']['Barrio'] ?? '01',
                'direccion' => $xml['Emisor']['Ubicacion']['OtrasSenas'] ?? 'Costa Rica',
                'tipo_cliente' => 2,
                'nombre_contribuyente' => $xml['Emisor']['Nombre']
            );
            $id = DB::table('clientes')->insertGetId($data);
            try {
                $razon_social = $this->actividad($num_id, $codigoActividad);
                $data = array(
                    'idcliente' => $id,
                    'codigo_actividad' => $codigoActividad,
                    'razon_social' => $razon_social,
                    'tipo_clasificacion' => 1,
                    'descripcion_clasificacion' => 'S/Clasificar',
                    'por_defecto' => 1
                );
                DB::table('clasificacion_proveedor')->insertGetId($data);
            } catch (Exception $e) {

            }
            return $id;
        } else {

            $cliente = DB::table('clientes')
            ->select('clientes.*')
            ->where([
                ['clientes.num_id', '=', $num_id],
                ['clientes.idconfigfact', '=', $idconfigfact]
            ])->first();


            //nuevo desarrollo o validacion va aqui
            $qry_consulta = DB::table('clasificacion_proveedor')
            ->select('clasificacion_proveedor.*')
            ->where([
                ['clasificacion_proveedor.idcliente', '=', $cliente->idcliente],
                ['clasificacion_proveedor.codigo_actividad', '=', $codigoActividad]
            ])->count();

            if($qry_consulta == 0){

                try {
                    $razon_social = $this->actividad($num_id, $codigoActividad);
                    $data = array(
                        'idcliente' => $cliente->idcliente,
                        'codigo_actividad' => $codigoActividad,
                        'razon_social' => $razon_social,
                        'tipo_clasificacion' => 1,
                        'descripcion_clasificacion' => 'S/Clasificar',
                        'por_defecto' => 0
                    );
                    DB::table('clasificacion_proveedor')->insertGetId($data);
                } catch (Exception $e) {

                }
            } else {
                return 0;
            }
        }
    }

        public function actividad($num_id, $codigo_actividad)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.hacienda.go.cr/fe/ae?identificacion='.$num_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: TS01d94531=0120156b28b80c03a0ce380088663ea3f7e42464ef1ce0620ebe7466e5feddc510e682a46069feab7025503aee1b2671afb3abfec1'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $datos = json_decode($response, true);
        if (isset($datos['actividades'])) {

            foreach ($datos['actividades'] as $act) {

                if ($act['codigo'] == $codigo_actividad) {
                    return $act['descripcion'];
                }

            }
            return 'Actividad por defecto';

        } else {
            return 'Actividad por defecto';
        }

    }


    public function startUpdateFolder()
    {
       // $configuraciones = Configuracion::all(); se comenta por implementar la configura de recepciona en bd para no recepcionar los que estan en 0
        
         $configuraciones = DB::table('configuracion')
                             ->select('configuracion.*')
                             ->where([
                             ['configuracion.recepciona', '=', 1]
            
                            ])->get();
                            
        //For each para validar cada empresa y migrar documentos a un mismo folder
        foreach ($configuraciones as $config) {
            $this->moveToRealFolder($config->idconfigfact);
        }
    }

    public function moveToRealFolder($idconfigfact)
    {
        $ruta = public_path('/correo_comun/');
        //\Log::info("comando recepcion:folder idconfigfact: ".$idconfigfact);
        $carpeta = @scandir($ruta);
        
        if (count($carpeta) > 2) {
        $arreglo_documento = array_slice($carpeta, 2);     
            foreach ($arreglo_documento as $key => $documento_correo) {
                //for ($x=2; $x < $conteo; $x++) {
         // dd($documento_correo);
               $strContents = file_get_contents($ruta.'/'.$documento_correo);
                $strDatas = $this->Xml2Array($strContents);
                //dd($strDatas);
                if (isset($strDatas['FacturaElectronica'])) {

                    $documento = 'FacturaElectronica';
                } elseif (isset($strDatas['NotaDebitoElectronica'])) {

                   $documento = 'NotaDebitoElectronica';
               } elseif (isset($strDatas['NotaCreditoElectronica'])) {

                    $documento = 'NotaCreditoElectronica';
                } elseif (isset($strDatas['TiqueteElectronico'])) {
                     $documento = 'norecepciona';
                   // unlink($ruta.'/'.$documento_correo);
                }elseif (isset($strDatas['MensajeHacienda'])) {
                     $documento = 'norecepciona';
                   //unlink($ruta.'/'.$documento_correo);
                }elseif (isset($strDatas['FacturaElectronicaCompra'])) {

                            $documento = 'FacturaElectronicaCompra';
                }else {
                    $new_rut_doc =  public_path('/No_ubicado/');
                    copy($ruta.'/'.$documento_correo, $new_rut_doc.''.time().$documento_correo);
                    unlink($ruta.'/'.$documento_correo);
                    continue;
                } 
              //  \Log::info(json_encode($documento_correo));
             //  dd($documento);
           // \Log::info("N杩唌ero consecutivo para {$documento}: " . $strDatas[$documento]['NumeroConsecutivo']);  

             if($documento!='norecepciona'){
                if ($strDatas[$documento]['NumeroConsecutivo']) {
//dd($strDatas[$documento]['NumeroConsecutivo']);
                    $configuracion = Configuracion::find($idconfigfact);
                    //\Log::info($documento);
                    //validacion para saber si el documento le corresponde a la empresa que entro en el foreach de empresa
                    if($documento!='FacturaElectronicaCompra'){
                    if(isset($strDatas[$documento]['Receptor']['Identificacion']['Numero'])){
                        $ideti=$strDatas[$documento]['Receptor']['Identificacion']['Numero'];
                    }else{
                        if(isset($strDatas[$documento]['Receptor']['IdentificacionExtranjero'])){
                        $ideti=$strDatas[$documento]['Receptor']['IdentificacionExtranjero'];
                        }else{
                           $ideti=0; 
                        }
                    }
                    }else{
                        //si es FEC
                         $ideti=$strDatas[$documento]['Emisor']['Identificacion']['Numero'];
                       
                    }
                    if($configuracion->numero_id_emisor.''===''.$ideti){
                         if($documento!='FacturaElectronicaCompra'){
                             $new_ruta = public_path('/XML/' . $idconfigfact . "/email/");
                       // copy($ruta.'/'.$carpeta[$x], $new_ruta.''.$carpeta[$x]);
                         copy($ruta.'/'.$documento_correo, $new_ruta.''.time().$documento_correo);
                        unlink($ruta.'/'.$documento_correo); 
                             
                         }else{
                        if($configuracion->rea == 0 ){
                              //dd($configuracion->rea);
                    $new_rut_doc =  public_path('/No_ubicado/');
                    copy($ruta.'/'.$documento_correo, $new_rut_doc.''.time().$documento_correo);
                    unlink($ruta.'/'.$documento_correo);
                         }else{
                              $new_ruta = public_path('/XML/' . $idconfigfact . "/email/");
                       // copy($ruta.'/'.$carpeta[$x], $new_ruta.''.$carpeta[$x]);
                         copy($ruta.'/'.$documento_correo, $new_ruta.''.time().$documento_correo);
                        unlink($ruta.'/'.$documento_correo);
                         }
                         }
                       
                    } else {
                            //find compra el log de configuraciones, si esta, lo deja, sino lo eleimina

                            $num_id_conf = $ideti;
                            $qryconf = DB::table('configuracion')
                             ->select('configuracion.*')
                             ->where([
                             ['configuracion.numero_id_emisor', '=', $num_id_conf],
                             ['configuracion.recepciona', '=', 1],
            
                            ])->count();

                            if ($qryconf == 0) {
                                 $new_rut =  public_path('/No_idconfifact/');
                              copy($ruta.'/'.$documento_correo, $new_rut.''.time().$documento_correo);
                        unlink($ruta.'/'.$documento_correo);
               
                            }else{
                           continue;
                             }
            //fin omairena
                        //continue;
                    }
                }  else {

                   $new_rut_doc =  public_path('/No_ubicado/');
                    copy($ruta.'/'.$documento_correo, $new_rut_doc.''.time().$documento_correo);
                    unlink($ruta.'/'.$documento_correo);
                }
             }else{
                 $new_rut_doc =  public_path('/no_recepciona/');
                    copy($ruta.'/'.$documento_correo, $new_rut_doc.''.time().$documento_correo);
                    unlink($ruta.'/'.$documento_correo);
             }
            }
        }
    }
    
}
