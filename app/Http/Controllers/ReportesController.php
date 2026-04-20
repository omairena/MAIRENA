<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Sales;
use App\Configuracion;
use App\Sales_item;
use App\Otrocargo;
use App\Facelectron;
use App\Items_exonerados;
use App\Cliente;
use App\Productos;
use App\Cxcobrar;
use App\Cxpagar;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\CapabilityProfiles\DefaultCapabilityProfile;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use App\Log_cajas;
use App\Cajas;
use App\User;
use Illuminate\Support\Arr;
use App\Log_cxcobrar;
use App\Mov_cxcobrar;
use App\Pedidos;
use App\Pedidos_item;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Receptor;
use App\Mail\ReceptorXmlMail;
use Illuminate\Support\Facades\Mail;
use App\App_settings;
use DateTimeZone;
class ReportesController extends Controller
{
public function filtroPDFXML(Request $request)
{
    $datos = $request->validate([
        'fecha_desde' => 'required|date',
        'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
    ]);

    $sales = Sales::where([
        ['fecha_creada', '>=', $datos['fecha_desde']],
        ['fecha_creada', '<=', $datos['fecha_hasta']],
        ['idconfigfact', '=', Auth::user()->idconfigfact],
    ])->with('facelectron')->get();

    $filePaths = [];

    foreach ($sales as $sales_pdf) {
        $id_fe = $sales_pdf->facelectron; // Suponiendo que tienes la relación configurada

        if ($id_fe) {
            $filePaths = array_merge($filePaths, array_filter([
                file_exists($ruta = public_path(ltrim($id_fe->pdf_factura, './'))) ? $ruta : null,
                file_exists($ruta = public_path(ltrim($id_fe->rutaxml, './'))) ? $ruta : null,
                file_exists($ruta = public_path(ltrim($id_fe->respuesta_xml, './'))) ? $ruta : null
            ]));
        }
    }

    if (!empty($filePaths)) {
        $zipFileName = 'facturas.zip';
        $zipFilePath = public_path($zipFileName);
        $zip = new \ZipArchive();

        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {
            foreach ($filePaths as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();

            // Enviar el archivo ZIP por correo
            Mail::to(Auth::user()->email)->send(new ReceptorXmlMail($zipFilePath));
        } else {
            return redirect()->route('reportes.sales')->withStatus(__('No se pudo crear el archivo ZIP.'));
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    return redirect()->route('reportes.sales')->withStatus(__('No se encontraron archivos para descargar.'));
}
public function pdfBoleta($id)
    {

      $pedido = Pedidos::find($id);
      $pedido_items = Pedidos_item::where('idpedido', $id)->get();
      $configuracion = Configuracion::find($pedido->idconfigfact);
      $cliente = Cliente::find($pedido->idcliente);
      $data = [
        'pedido' => $pedido,
        'pedido_items' => $pedido_items,
        'cliente' => $cliente,
        'configuracion' => $configuracion
      ];

      $pdf = PDF::loadView('pdf.pdf_boleta', $data);
      $nombreArchivo = 'Boleta#'.$pedido->numero_documento.'.pdf';
      $url = './PDF/'.$pedido->idconfigfact.'/Pedidos/'.$nombreArchivo;
      //Guardalo en una variable
      $output =  $pdf->output();
      file_put_contents('./PDF/'.$pedido->idconfigfact.'/Pedidos/'.$nombreArchivo, $output);
      Pedidos::where('idpedido', $id)
              ->update(
                ['pdf_pedido' =>  ''.$url]);
      // Una vez lo guardes en local lo puedes subir o enviar a un ftp.
      return $pdf->download($nombreArchivo)->header('Content-Type','application/pdf');
    }

  

public function filtroPDF(Request $request)
{
    $datos = $request->all();
    $sales = Sales::where([
        ['tipo_documento', '!=', '08'],
        ['fecha_creada', '>=', $datos['fecha_desde']],
        ['fecha_creada', '<=', $datos['fecha_hasta']],
        ['idconfigfact', '=', Auth::user()->idconfigfact],
    ])->get();

    // Array para almacenar las rutas de los archivos
    $filePaths = [];

    foreach ($sales as $sales_pdf) {
        $id_fe = Facelectron::where([
            ['idsales', '=', $sales_pdf->idsale],
        ])->first();

        if ($id_fe) {
            $rutaEnvio = public_path(ltrim($id_fe->pdf_factura, './'));
            if (file_exists($rutaEnvio)) {
                $filePaths[] = $rutaEnvio; // Agrega la ruta del archivo al array
            }
        }
    }

  if (count($filePaths) > 0) {
    // Nombre del archivo ZIP
    $zipFileName = 'facturas.zip';
    $zipFilePath = public_path($zipFileName); // Crear la ruta completa del ZIP

    $zip = new \ZipArchive();

    // Intenta abrir el archivo ZIP
    if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
        foreach ($filePaths as $file) {
            // Verifica que el archivo existe antes de agregarlo
            if (file_exists($file)) {
                $zip->addFile($file, basename($file)); // Agrega el archivo al ZIP
            } else {
                // Manejo de error para archivos inexistentes
                error_log("El archivo no existe: $file");
            }
        }

        $zip->close(); // Cierra el archivo ZIP
    } else {
        // Manejo de error al abrir el ZIP
        return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
    }

    // Descarga el archivo ZIP
    return response()->download($zipFilePath)->deleteFileAfterSend(true);
}

// Redirige si no se encontraron archivos
return redirect()->route('reportes.sales')->withStatus(__('No se encontraron archivos para descargar.'));
}


     	public function pdf_factura($id)
    {
    	$sales = Sales::find($id);
    	$sales_item = Sales_item::where('idsales',$id)->get();
        $sales_item_otrocargo = Otrocargo::where('idsales',$id)->get();
      if (!empty($sales_item) && isset($sales_item[0]->idsales)) {  
        $item_exonerado = Items_exonerados::where('idsalesitem',$sales_item[0]->idsales)->get();
        $item_exonerado = Items_exonerados::where('idsalesitem', $sales_item[0]->idsales)->get();  
} else {  
    // Maneja el caso en que $sales_item esté vacío  
    $item_exonerado = collect(); // Crea una colección vacía  
    // O imprime un mensaje de error, o lanza una excepción según necesites  
    //echo "No hay elementos en sales_item.";  
}  
    	$facelectron = Facelectron::where('idsales', $id)->get();

//dd($sales);

    if (is_object($sales) && isset($sales->referencia_sale)) {
    if ($sales->referencia_sale == 0) {
        $refnc = 3; // Asignar como número
    } else {
        $refnc = $sales->referencia_sale; // Mantener el valor original
    }
} else {
    // Manejar el caso en que $sales no es un objeto
    $refnc = 3; // O cualquier valor por defecto que desees
    // También puedes lanzar un error o registrar un mensaje
    // Log::error('El objeto $sales no está definido o no es un objeto.');
}
    	//	dd($refnc);
    	$consulta_fac = Facelectron::where( 'idsales', $refnc)->get(); ///para obtener refencia de la NC de la que viene

       	$configuracion = Configuracion::find($facelectron[0]->idconfigfact);
    	$cliente = Cliente::find($sales->idcliente);
        $link = App_settings::where('idsettings', 1)->get();
        $contents = QrCode::format('png')->generate($link[0]->lin. $facelectron[0]->clave);
        //$contents = QrCode::format('png')->generate('snesteban.com/sistema/qr.php?numeroDocumento=' . $facelectron[0]->clave);
      switch ($sales->condicion_venta) {
        case '01':
          $condicion = 'Contado';
          break;
          case '02':
          $condicion = 'Credito';
          break;
           case '10':
          $condicion = 'Venta crédito IVA - 90 días (Art27 LIVA)';
          break;
           case '11':
          $condicion = 'Pago de venta a crédito en IVA hasta 90 días (Artículo 27, LIVA) ';
          break;
      }
      if(!is_null($sales->medio_pago) && !empty($sales->medio_pago)){
          switch ($sales->medio_pago) {
            case '01':
              $medio_pago = 'Efectivo';
              break;
              case '02':
              $medio_pago = 'Tarjeta';
              break;
              case '03':
              $medio_pago = 'Cheque';
              break;
              case '04':
              $medio_pago = 'Transferencia – depósito bancario';
              break;
              case '05':
               $medio_pago = 'Recaudado por terceros';
              break;
              case '06':
                $medio_pago = 'Sinpe Movil';
               break;
               case '07':
                $medio_pago = 'Plataforma Digital';
               break;
          }
      } else {
        $medio_pago = $sales->medioPagos()->get();
      }
      
      $strContents = file_get_contents($facelectron[0]->rutaxml);
      $strDatas = $this->Xml2Array($strContents);
      //Donde guardar el documento
      switch ($facelectron[0]->tipodoc) {
        case '01':
        $fecha_hora=$strDatas['FacturaElectronica']['FechaEmision'];
          $rutaGuardado = "./PDF/".$facelectron[0]->idconfigfact."/Facturas/Envio/";
          //Nombre del Documento.
          $nombreArchivo = $facelectron[0]->clave.'.pdf';
        break;
        case '02':
          $fecha_hora=$strDatas['NotaDebitoElectronica']['FechaEmision'];
          $rutaGuardado = "./PDF/".$facelectron[0]->idconfigfact."/NotaDebito/Envio/";
          //Nombre del Documento.
          $nombreArchivo = $facelectron[0]->clave.'.pdf';
        break;
        case '03':
          $fecha_hora=$strDatas['NotaCreditoElectronica']['FechaEmision'];
          $rutaGuardado = "./PDF/".$facelectron[0]->idconfigfact."/NotaCredito/Envio/";
          //Nombre del Documento.
          $nombreArchivo = $facelectron[0]->clave.'.pdf';
        break;
        case '04':
          $fecha_hora=$strDatas['TiqueteElectronico']['FechaEmision'];
          $rutaGuardado = "./PDF/".$facelectron[0]->idconfigfact."/Tiquete/Envio/";
          //Nombre del Documento.
          $nombreArchivo = $facelectron[0]->clave.'.pdf';
        break;
        case '08':
          $fecha_hora=$strDatas['FacturaElectronicaCompra']['FechaEmision'];
          $rutaGuardado = "./PDF/".$facelectron[0]->idconfigfact."/FacturaCompra/Envio/";
          //Nombre del Documento.
         // $nombreArchivo = 'Tiquete#'.$facelectron[0]->clave.'.pdf';
           $nombreArchivo = $facelectron[0]->clave.'.pdf';
        break;
        case '09':
          $fecha_hora=$strDatas['FacturaElectronicaExportacion']['FechaEmision'];
          $rutaGuardado = "./PDF/".$facelectron[0]->idconfigfact."/FacturaExportacion/Envio/";
          //Nombre del Documento.
          $nombreArchivo = $facelectron[0]->clave.'.pdf';
        break;
      }
      
         if (empty($fecha_hora)) {
       $zone = new \DateTimeZone('America/Costa_Rica');
        $now = new \DateTime('now', $zone);
        // Formato deseado: 2025-12-27T10:35:58-06:00
        $fecha_hora = $now->format('Y-m-d\\TH:i:sP');
    }
    
    
      $feh=explode('T', $fecha_hora);
      $fe2=explode('-', $feh[1]);
      $fecha_completa=$feh[0].' '.$fe2[0];
    	$data = [
          	'sales' => $sales,
          	'sales_item' => $sales_item,
            'sales_item_otrocargo' => $sales_item_otrocargo,
          	'configuracion' => $configuracion,
          	'facelectron' => $facelectron[0],
          	'consulta_fac'=> $consulta_fac[0] ?? [],
          	'cliente' => $cliente,
            'url' => $contents,
             //'url' => 'https://www.google.com/',
            'condicion' => $condicion,
            'medio_pago' => $medio_pago,
            'direccion_emisor' => wordwrap($configuracion->direccion_emisor, 35),
            'direccion_receptor' => wordwrap($cliente->direccion, 35),
            'moneda' => $sales->tipo_moneda,
            'fecha_hora' => $fecha_completa,
            'tipo_cambio' => $sales->tipo_cambio
            ];

    	$pdf = PDF::loadView('pdf.pdf_factura', $data);


      $url = $rutaGuardado.$nombreArchivo;
      //Guardalo en una variable
      $output =  $pdf->output();
     // dd($url);
      file_put_contents($url, $output);
      Facelectron::where('clave', $facelectron[0]->clave)
              ->update(
                ['pdf_factura' =>  ''.$url]);
      // Una vez lo guardes en local lo puedes subir o enviar a un ftp.
              //Donde guardar el documento
      switch ($facelectron[0]->tipodoc) {
        case '01':
          return $pdf->download('Factura#'.$facelectron[0]->numdoc.'-'.$cliente->nombre.'.pdf')->header('Content-Type','application/pdf');
        break;
        case '02':
          return $pdf->download('NotaDebito#'.$facelectron[0]->numdoc.$cliente->nombre.'.pdf')->header('Content-Type','application/pdf');
        break;
        case '03':
          return $pdf->download('NotaCredito#'.$facelectron[0]->numdoc.$cliente->nombre.'.pdf')->header('Content-Type','application/pdf');
        break;
        case '04':
          return $pdf->download($facelectron[0]->clave.'.pdf')->header('Content-Type','application/pdf');
        break;
         case '08':
          return $pdf->download('FEC#'.$facelectron[0]->numdoc.'.pdf')->header('Content-Type','application/pdf');
        break;
         case '09':
          return $pdf->download('FEE#'.$facelectron[0]->numdoc.$cliente->nombre.'.pdf')->header('Content-Type','application/pdf');
        break;
      }

    }
//15-09-2023

public function diariocon_op(Request $request)
    {
         Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)
               ->update(
                ['sum_op' =>  '0']);

        //return redirect()->route('facturar.index')->withStatus(__('Base de Datos Limpiada correctamente.'));
       // return view('reporte.ddaily', ['resultados' => $resultados, 'callback' => $callback, 'fecha' => $fecha]);
      // return view('reporte.ddaily');
       return redirect()->route('reportes.daily')->withStatus(__('Reporte con Orden de Pedido Activado.'));
    }

    public function diariosin_op(Request $request)
    {
         Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)
               ->update(
                ['sum_op' =>  '1']);

        //return redirect()->route('facturar.index')->withStatus(__('Base de Datos Limpiada correctamente.'));
       // return view('reporte.ddaily', ['resultados' => $resultados, 'callback' => $callback, 'fecha' => $fecha]);
      // return view('reporte.ddaily');
       return redirect()->route('reportes.daily')->withStatus(__('Reporte con Orden de Pedido Desactivado.'));
    }

   //omairena 27-06-2022

 public function reportedDailysales()
    {

          $now = Carbon::now()->toDateTimeString();
          $valores = explode(' ', $now);
          $fecha = [];
          $fecha['desde'] = $valores[0];
          $fecha['hasta'] = $valores[0];
          $documentos = DB::table('sales')
          ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.observaciones', 'sales.total_descuento')
          ->where('sales.estatus_sale', 2)
          ->where('sales.tipo_doc_ref','!=',17)
          ->where('facelectron.estatushacienda', '=', 'aceptado')
          ->where('facelectron.fechahora', '=', $valores[0])
          ->where('sales.idconfigfact', '=', Auth::user()->idconfigfact)
          ->get();

          $resultados = [];
          $positivo = 0;
          $negativo = 0;
          $impuestos = 0;
          $positivo_rs = 0;
          $negativo_rs = 0;
          $impuestos_rs = 0;

          foreach ($documentos as $doc) {

            if ($doc->tipo_documento != '03') {

                $positivo += $doc->total_comprobante;
                $impuestos += $doc->total_impuesto;
            } else {

                $negativo += $doc->total_comprobante;
            }
          }
          // Consulta de Regimen Simplificado
          $documentos_rs = DB::table('sales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.observaciones', 'sales.total_descuento', 'sales.tipo_documento', 'sales.fecha_creada', 'sales.numero_documento')
          ->where('sales.estatus_sale', 2)
           ->where('sales.tipo_doc_ref','!=',17)
          ->whereIn('sales.tipo_documento', ['96', '95'])
          ->whereBetween('sales.fecha_creada', [$valores[0], $valores[0]])
          ->where('sales.idconfigfact', '=', Auth::user()->idconfigfact)
          ->get();

          foreach ($documentos_rs as $doc_rs) {

            if ($doc_rs->tipo_documento != '95') {

                $positivo_rs += $doc_rs->total_comprobante;
                $impuestos_rs += $doc_rs->total_impuesto;
            } else {

                $negativo_rs += $doc_rs->total_comprobante;
            }
          }
          $resultados['notas_credito'] = $negativo + $negativo_rs;
          $resultados['facturas'] = $positivo + $positivo_rs;
          $resultados['impuestos'] = $impuestos + $impuestos_rs;

          $callback = [];
          foreach ($documentos as $document)
          {

            // logica para tabla
            $excento_0 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;
            // Seccion de cabecera
            $array['tipo_documento'] = $document->tipodoc;//facelectron
            $array['numero_documento'] = $document->numdoc;//facelectron
            $array['condicion'] = $document->condicion_venta; //sales
             $array['idsale'] = $document->idsale; //sales
               $array['observaciones'] = $document->observaciones; //sales
            $array['fecha_documento'] = $document->fechahora;//facelectron
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = $document->estatushacienda;//facelectron


            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

              switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                 case '01':  
                 case '11': // Corregido para múltiples casos  
                 $no_sujeto += $detalle->valor_neto;  
                break;  
              }
            
            // Seccion de Impuestos

            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }
}
            array_push($callback, $array);
          }
          foreach ($documentos_rs as $document) {
            $excento_0 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;
            // Seccion de cabecera
            $array['tipo_documento'] = $document->tipo_documento;//sales
            $array['numero_documento'] = $document->numero_documento;//sales
            $array['condicion'] = $document->condicion_venta; //sales
             $array['idsale'] = $document->idsale; //sales
             $array['observaciones'] = $document->observaciones; //sales
            $array['fecha_documento'] = $document->fecha_creada;//sales
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = 'No Aplica';//facelectron


            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

               switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                 case '01':  
                 case '11': // Corregido para múltiples casos  
                 $no_sujeto += $detalle->valor_neto;  
                break;  
              }
            
            // Seccion de Impuestos

            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }
}
            array_push($callback, $array);
          }




          return view('reporte.ddaily', ['resultados' => $resultados, 'callback' => $callback, 'fecha' => $fecha ]);
    }
    //////
    public function filtrarDailyconsolidado(Request $request)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
        $fecha = [];
        $fecha['desde'] = $datos['fecha_desde'];
        $fecha['hasta'] = $datos['fecha_hasta'];
         if(Auth::user()->id == 82){
          $documentos = DB::table('sales')
        ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
        ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
        ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
        ->leftjoin('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
        ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento','configuracion.nombre_empresa')
        ->where('sales.estatus_sale', 2)
         ->where('sales.tipo_doc_ref','!=',17)
        ->where('sales.tipo_documento', '!=', '08')
        ->where('facelectron.estatushacienda', '=', 'aceptado')
        ->whereIn('sales.idconfigfact',  ['02','03','04','13'] )
        ->whereBetween('facelectron.fechahora', [$datos['fecha_desde'], $datos['fecha_hasta']])
        ->get();

         }
 if(Auth::user()->id == 97){
    $documentos = DB::table('sales')
        ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
        ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
        ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
        ->leftjoin('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
        ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento','configuracion.nombre_empresa')
        ->where('sales.estatus_sale', 2)
         ->where('sales.tipo_doc_ref','!=',17)
        ->where('sales.tipo_documento', '!=', '08')
        ->where('facelectron.estatushacienda', '=', 'aceptado')
        ->whereIn('sales.idconfigfact',  ['15','16'] )
        ->whereBetween('facelectron.fechahora', [$datos['fecha_desde'], $datos['fecha_hasta']])
        ->get();
 }

          $resultados = [];
          $positivo = 0;
          $negativo = 0;
          $impuestos = 0;
          $positivo_rs = 0;
          $negativo_rs = 0;
          $impuestos_rs = 0;

          $impuestof=0;
          $impuestofrs=0;
          $impuestofnc=0;
          $impuestofrsnc=0;

          foreach ($documentos as $doc) {

            if ($doc->tipo_documento != '03') {

                $positivo += $doc->total_comprobante;
                $impuestos += $doc->total_impuesto;
                $impuestof += $doc->total_impuesto;

            } else {

                $negativo += $doc->total_comprobante;
                $impuestofnc+= $doc->total_impuesto;
            }
          }
           $sum_ops = configuracion::where('idconfigfact','=', Auth::user()->idconfigfact)->first();

            if ($sum_ops->sum_op != '1'){
          // Consulta de Regimen Simplificado
           if(Auth::user()->id == 82){
          $documentos_rs = DB::table('sales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
          ->leftjoin('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento', 'sales.tipo_documento', 'sales.fecha_creada', 'sales.numero_documento','configuracion.nombre_empresa')
          ->where('sales.estatus_sale', 2)
           ->where('sales.tipo_doc_ref','!=',17)
          ->whereIn('sales.tipo_documento', ['96', '95'])
          ->whereBetween('sales.fecha_creada', [$datos['fecha_desde'], $datos['fecha_hasta']])
          ->whereIn('sales.idconfigfact',  ['02','03','04','13'] )
          ->get();
           }
            if(Auth::user()->id == 97){

               $documentos_rs = DB::table('sales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
          ->leftjoin('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento', 'sales.tipo_documento', 'sales.fecha_creada', 'sales.numero_documento','configuracion.nombre_empresa')
          ->where('sales.estatus_sale', 2)
           ->where('sales.tipo_doc_ref','!=',17)
          ->whereIn('sales.tipo_documento', ['96', '95'])
          ->whereBetween('sales.fecha_creada', [$datos['fecha_desde'], $datos['fecha_hasta']])
          ->whereIn('sales.idconfigfact',  ['15','16'] )
          ->get();

            }
          foreach ($documentos_rs as $doc_rs) {

            if ($doc_rs->tipo_documento != '95') {

                $positivo_rs += $doc_rs->total_comprobante;
                $impuestos_rs += $doc_rs->total_impuesto;
                $impuestofrs += $doc_rs->total_impuesto;
            } else {

                $negativo_rs += $doc_rs->total_comprobante;
                $impuestofrsnc+= $doc_rs->total_impuesto;
            }
          }
            }
          $resultados['notas_credito'] = $negativo + $negativo_rs;
          $resultados['facturas'] = $positivo + $positivo_rs;
          $resultados['impuestos'] = $impuestos + $impuestos_rs;
           $resultados['netoimpuesto'] = $impuestof + $impuestofrs-$impuestofrsnc-$impuestofnc;

          // logica para tabla

          $callback = [];

          foreach ($documentos as $document) {
            $excento_0 = 0;
            $reducida_05 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;

            // Seccion de cabecera
            $array['nombre_empresa'] = $document->nombre_empresa;//sales
            $array['tipo_documento'] = $document->tipodoc;//facelectron
            $array['numero_documento'] = $document->numdoc;//facelectron
            $array['condicion'] = $document->condicion_venta; //sales
            $array['fecha_documento'] = $document->fechahora;//facelectron
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = $document->estatushacienda;//facelectron
            $array['codigo_actividad'] = $document->codigo_actividad;//actividades
            $array['tipo_cambio'] = $document->tipo_cambio;//sales
            $array['moneda'] = $document->tipo_moneda;//sales
            $array['total_descuento'] = $document->total_descuento;

            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

              switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                 case '01':  
                 case '11': // Corregido para múltiples casos  
                 $no_sujeto += $detalle->valor_neto;  
                break;  
              }
            }
            // Seccion de Impuestos
            $array['excento_0'] = $excento_0;
            $array['reducida_05'] = $reducida_05;
            $array['reducida_1'] = $reducida_1;
            $array['reducida_2'] = $reducida_2;
              $array['reducida_4'] = $reducida_4;
            $array['transitorio_0'] = $transitorio_0;
            $array['transitorio_4'] = $transitorio_4;
            $array['transitorio_8'] = $transitorio_8;
            $array['gravado_13'] = $gravado_13;
            $array['no_sujeto'] = $no_sujeto;
            // Seccion de Totales
            $array['total_iva'] = $document->total_impuesto;//sales
            $array['otros_cargos'] = $document->total_otros_cargos;//sales
            $array['iva_devuelto'] = $document->total_iva_devuelto;//sales
            $array['total_exonerado'] = $document->total_exonerado;//sales
            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }

            array_push($callback, $array);
          }
            if ($sum_ops->sum_op != '1'){
          foreach ($documentos_rs as $document) {
            $excento_0 = 0;
            $reducida_05 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;
            // Seccion de cabecera
            $array['nombre_empresa'] = $document->nombre_empresa;//sales
            $array['tipo_documento'] = $document->tipo_documento;//sales
            $array['numero_documento'] = $document->numero_documento;//sales
            $array['condicion'] = $document->condicion_venta; //sales
            $array['fecha_documento'] = $document->fecha_creada;//sales
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = 'No Aplica';//facelectron
            $array['codigo_actividad'] = $document->codigo_actividad;//actividades
            $array['tipo_cambio'] = $document->tipo_cambio;//sales
            $array['moneda'] = $document->tipo_moneda;//sales
            $array['total_descuento'] = $document->total_descuento;

            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

               switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                 case '01':  
                 case '11': // Corregido para múltiples casos  
                 $no_sujeto += $detalle->valor_neto;  
                break;  
              }
            }
            // Seccion de Impuestos
            $array['excento_0'] = $excento_0;
             $array['reducida_05'] = $reducida_05;
            $array['reducida_1'] = $reducida_1;
            $array['reducida_2'] = $reducida_2;
            $array['reducida_4'] = $reducida_4;
            $array['transitorio_0'] = $transitorio_0;
            $array['transitorio_4'] = $transitorio_4;
            $array['transitorio_8'] = $transitorio_8;
            $array['gravado_13'] = $gravado_13;
            $array['no_sujeto'] = $no_sujeto;
            // Seccion de Totales
            $array['total_iva'] = $document->total_impuesto;//sales
            $array['otros_cargos'] = $document->total_otros_cargos;//sales
            $array['iva_devuelto'] = $document->total_iva_devuelto;//sales
            $array['total_exonerado'] = $document->total_exonerado;//sales
            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }

            array_push($callback, $array);
          }

            }
      return view('reporte.dailyconso', ['resultados' => $resultados, 'callback' => $callback, 'fecha' => $fecha]);
    }
    public function reporteDailysalesconso()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
          $now = Carbon::now()->toDateTimeString();
          $valores = explode(' ', $now);
          $fecha = [];
          $fecha['desde'] = $valores[0];
          $fecha['hasta'] = $valores[0];
          if(Auth::user()->id == 82){
          $documentos = DB::table('sales')
          ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
           ->leftjoin('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento','configuracion.nombre_empresa')
          ->where('sales.estatus_sale', 2)
           ->where('sales.tipo_doc_ref','!=',17)
          ->where('sales.tipo_documento', '!=', '08')
          ->where('facelectron.estatushacienda', '=', 'aceptado')
          ->where('facelectron.fechahora', '=', $valores[0])
          ->where('sales.idconfigfact', '=', '02' and 'sales.idconfigfact', '=', '03' and 'sales.idconfigfact', '=', '04' and 'sales.idconfigfact', '=', '13')
          ->get();
          }
          if(Auth::user()->id == 97){
          $documentos = DB::table('sales')
          ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
           ->leftjoin('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento','configuracion.nombre_empresa')
          ->where('sales.estatus_sale', 2)
           ->where('sales.tipo_doc_ref','!=',17)
          ->where('sales.tipo_documento', '!=', '08')
          ->where('facelectron.estatushacienda', '=', 'aceptado')
          ->where('facelectron.fechahora', '=', $valores[0])
          ->where('sales.idconfigfact', '=', '15' and 'sales.idconfigfact', '=', '16' )
          ->get();
          }

          $resultados = [];
          $positivo = 0;
          $negativo = 0;
          $impuestos = 0;
          $positivo_rs = 0;
          $negativo_rs = 0;
          $impuestos_rs = 0;

          $impuestof=0;
          $impuestofrs=0;
          $impuestofnc=0;
          $impuestofrsnc=0;

          foreach ($documentos as $doc) {

            if ($doc->tipo_documento != '03') {

                $positivo += $doc->total_comprobante;
                $impuestos += $doc->total_impuesto;
                 $impuestof += $doc->total_impuesto;

            } else {

                $negativo += $doc->total_comprobante;
                 $impuestofnc+= $doc->total_impuesto;
            }
          }


          $sum_ops = configuracion::where('idconfigfact','=', Auth::user()->idconfigfact)->first();

            if ($sum_ops->sum_op != '1'){
          // Consulta de Regimen Simplificado
           if(Auth::user()->id == 82){
          $documentos_rs = DB::table('sales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
           ->leftjoin('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento', 'sales.tipo_documento', 'sales.fecha_creada', 'sales.numero_documento','configuracion.nombre_empresa')
          ->where('sales.estatus_sale', 2)
           ->where('sales.tipo_doc_ref','!=',17)
          ->whereIn('sales.tipo_documento', ['96', '95'])
          ->whereBetween('sales.fecha_creada', [$valores[0], $valores[0]])
          ->whereIn('sales.idconfigfact',  ['02','03','04','13'] )
          ->get();
           }
            if(Auth::user()->id == 97){
                $documentos_rs = DB::table('sales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
           ->leftjoin('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento', 'sales.tipo_documento', 'sales.fecha_creada', 'sales.numero_documento','configuracion.nombre_empresa')
          ->where('sales.estatus_sale', 2)
           ->where('sales.tipo_doc_ref','!=',17)
          ->whereIn('sales.tipo_documento', ['96', '95'])
          ->whereBetween('sales.fecha_creada', [$valores[0], $valores[0]])
          ->whereIn('sales.idconfigfact',  ['15','16'] )
          ->get();


            }
          foreach ($documentos_rs as $doc_rs) {

            if ($doc_rs->tipo_documento != '95') {

                $positivo_rs += $doc_rs->total_comprobante;
                $impuestos_rs += $doc_rs->total_impuesto;
                   $impuestofrs += $doc_rs->total_impuesto;


            } else {

                $negativo_rs += $doc_rs->total_comprobante;
                 $impuestofrsnc+= $doc_rs->total_impuesto;
            }
          }
            }
          $resultados['notas_credito'] = $negativo + $negativo_rs;
          $resultados['facturas'] = $positivo + $positivo_rs;
          $resultados['impuestos'] = $impuestos + $impuestos_rs;
          $resultados['netoimpuesto'] = $impuestof + $impuestofrs-$impuestofrsnc-$impuestofnc;
          $callback = [];

          foreach ($documentos as $document)
          {

            // logica para tabla
            $excento_0 = 0;
            $reducida_05 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;
            // Seccion de cabecera
             $array['nombre_empresa'] = $document->nombre_empresa;//sales
            $array['tipo_documento'] = $document->tipodoc;//facelectron
            $array['numero_documento'] = $document->numdoc;//facelectron
            $array['condicion'] = $document->condicion_venta; //sales
            $array['fecha_documento'] = $document->fechahora;//facelectron
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = $document->estatushacienda;//facelectron
            $array['codigo_actividad'] = $document->codigo_actividad;//actividades
            $array['tipo_cambio'] = $document->tipo_cambio;//sales
            $array['moneda'] = $document->tipo_moneda;//sales
            $array['total_descuento'] = $document->total_descuento;

            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

               switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                 case '01':  
                 case '11': // Corregido para múltiples casos  
                 $no_sujeto += $detalle->valor_neto;  
                break;  
              }
            }
            // Seccion de Impuestos
            $array['excento_0'] = $excento_0;
            $array['reducida_05'] = $reducida_05;
            $array['reducida_1'] = $reducida_1;
            $array['reducida_2'] = $reducida_2;
            $array['reducida_4'] = $reducida_4;
            $array['transitorio_0'] = $transitorio_0;
            $array['transitorio_4'] = $transitorio_4;
            $array['transitorio_8'] = $transitorio_8;
            $array['gravado_13'] = $gravado_13;
            $array['no_sujeto'] = $no_sujeto;
            // Seccion de Totales
            $array['total_iva'] = $document->total_impuesto;//sales
            $array['otros_cargos'] = $document->total_otros_cargos;//sales
            $array['iva_devuelto'] = $document->total_iva_devuelto;//sales
            $array['total_exonerado'] = $document->total_exonerado;//sales
            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }

            array_push($callback, $array);
          }
           if ($sum_ops->sum_op != '1'){
          foreach ($documentos_rs as $document) {
            $excento_0 = 0;
            $reducida_05 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;
            // Seccion de cabecera
             $array['nombre_empresa'] = $document->nombre_empresa;//sales
            $array['tipo_documento'] = $document->tipo_documento;//sales
            $array['numero_documento'] = $document->numero_documento;//sales
            $array['condicion'] = $document->condicion_venta; //sales
            $array['fecha_documento'] = $document->fecha_creada;//sales
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = 'No Aplica';//facelectron
            $array['codigo_actividad'] = $document->codigo_actividad;//actividades
            $array['tipo_cambio'] = $document->tipo_cambio;//sales
            $array['moneda'] = $document->tipo_moneda;//sales
            $array['total_descuento'] = $document->total_descuento;

            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

               switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                 case '01':  
                 case '11': // Corregido para múltiples casos  
                 $no_sujeto += $detalle->valor_neto;  
                break;  
              }
            }
            // Seccion de Impuestos
            $array['excento_0'] = $excento_0;
            $array['reducida_05'] = $reducida_05;
            $array['reducida_1'] = $reducida_1;
            $array['reducida_2'] = $reducida_2;
            $array['reducida_4'] = $reducida_4;
            $array['transitorio_0'] = $transitorio_0;
            $array['transitorio_4'] = $transitorio_4;
            $array['transitorio_8'] = $transitorio_8;
            $array['gravado_13'] = $gravado_13;
            $array['no_sujeto'] = $no_sujeto;
            // Seccion de Totales
            $array['total_iva'] = $document->total_impuesto;//sales
            $array['otros_cargos'] = $document->total_otros_cargos;//sales
            $array['iva_devuelto'] = $document->total_iva_devuelto;//sales
            $array['total_exonerado'] = $document->total_exonerado;//sales
            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }

            array_push($callback, $array);
          }
           }
          ///fin doc no electronicos
          return view('reporte.dailyconso', ['resultados' => $resultados, 'callback' => $callback, 'fecha' => $fecha]);
    }
///omairea para optica
 public function filtrardDaily(Request $request)
    {




        $datos = $request->all();
        $fecha = [];
        $fecha['desde'] = $datos['fecha_desde'];
        $fecha['hasta'] = $datos['fecha_hasta'];
        $actividad= $datos['actividad'];
        $query = DB::table('sales')
        ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
        ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
        ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
        ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.observaciones', 'sales.total_descuento')
        ->where('sales.estatus_sale', 2)
         ->where('sales.tipo_doc_ref','!=',17)
        ->where('sales.tipo_documento','!=', '08')
        ->where('facelectron.estatushacienda', '=', 'aceptado')
        ->where('sales.idconfigfact', '=', Auth::user()->idconfigfact)
        ->whereBetween('facelectron.fechahora', [$datos['fecha_desde'], $datos['fecha_hasta']]);

      if (!empty($actividad)) {
    $query->where('sales.idcodigoactv', '=', $actividad);
}

// Ejecutar la consulta y obtener los resultados
$documentos = $query->get();

          $resultados = [];
          $positivo = 0;
          $negativo = 0;
          $impuestos = 0;
          $positivo_rs = 0;
          $negativo_rs = 0;
          $impuestos_rs = 0;

          foreach ($documentos as $doc) {

            if ($doc->tipo_documento != '03') {

                $positivo += $doc->total_comprobante;
                $impuestos += $doc->total_impuesto;
            } else {

                $negativo += $doc->total_comprobante;
            }
          }
          // Consulta de Regimen Simplificado
          $query_rs = DB::table('sales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.observaciones', 'sales.total_descuento', 'sales.tipo_documento', 'sales.fecha_creada', 'sales.numero_documento')
          ->where('sales.estatus_sale', 2)
           ->where('sales.tipo_doc_ref','!=',17)
          ->whereIn('sales.tipo_documento', ['96', '95'])
          ->whereBetween('sales.fecha_creada', [$datos['fecha_desde'], $datos['fecha_hasta']])
          ->where('sales.idconfigfact', '=', Auth::user()->idconfigfact);

  if (!empty($actividad)) {
    $query_rs->where('sales.idcodigoactv', '=', $actividad);
}

// Ejecutar la consulta y obtener los resultados
$documentos_rs = $query_rs->get();
          foreach ($documentos_rs as $doc_rs) {

            if ($doc_rs->tipo_documento != '95') {

                $positivo_rs += $doc_rs->total_comprobante;
                $impuestos_rs += $doc_rs->total_impuesto;
            } else {

                $negativo_rs += $doc_rs->total_comprobante;
            }
          }
          $resultados['notas_credito'] = $negativo + $negativo_rs;
          $resultados['facturas'] = $positivo + $positivo_rs;
          $resultados['impuestos'] = $impuestos + $impuestos_rs;

          // logica para tabla

          $callback = [];

          foreach ($documentos as $document) {
            $excento_0 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;

            // Seccion de cabecera
            $array['tipo_documento'] = $document->tipodoc;//facelectron
            $array['numero_documento'] = $document->numdoc;//facelectron
            $array['condicion'] = $document->condicion_venta; //sales
             $array['idsale'] = $document->idsale; //sales
             $array['observaciones'] = $document->observaciones; //sales
            $array['fecha_documento'] = $document->fechahora;//facelectron
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = $document->estatushacienda;//facelectron


            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

               switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                 case '01':  
                 case '11': // Corregido para múltiples casos  
                 $no_sujeto += $detalle->valor_neto;  
                break;  
              }
           
            // Seccion de Impuestos

            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }
 }
            array_push($callback, $array);
          }

          foreach ($documentos_rs as $document) {
            $excento_0 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;
            // Seccion de cabecera
            $array['tipo_documento'] = $document->tipo_documento;//sales
            $array['numero_documento'] = $document->numero_documento;//sales
            $array['condicion'] = $document->condicion_venta; //sales
             $array['idsale'] = $document->idsale; //sales
             $array['observaciones'] = $document->observaciones; //sales
            $array['fecha_documento'] = $document->fecha_creada;//sales
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = 'No Aplica';//facelectron


            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

               switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                 case '01':  
                 case '11': // Corregido para múltiples casos  
                 $no_sujeto += $detalle->valor_neto;  
                break;  
              }
            
            // Seccion de Impuestos

            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }
}
            array_push($callback, $array);
          }



      return view('reporte.ddaily', ['resultados' => $resultados, 'callback' => $callback, 'fecha' => $fecha ]);
    }

/// fin 27-06-2022
///fin
      public function pdf_regimen($id)
    {

      $sales = Sales::find($id);
      $sales_item = Sales_item::where('idsales',$id)->get();
      $configuracion = Configuracion::find(Auth::user()->idconfigfact);
      $cliente = Cliente::find($sales->idcliente);

      switch ($sales->condicion_venta) {

        case '01':
          $condicion = 'Contado';
        break;
          case '02':
          $condicion = 'Credito';
        break;

      }

      switch ($sales->medio_pago) {

        case '01':
          $medio_pago = 'Efectivo';
        break;

        case '02':
          $medio_pago = 'Tarjeta';
        break;

        case '03':
          $medio_pago = 'Cheque';
        break;

        case '04':
          $medio_pago = 'Transferencia – depósito bancario';
        break;

        case '05':
          $medio_pago = 'Recaudado por terceros';
        break;

      }

      $data = [
        'sales' => $sales,
        'sales_item' => $sales_item,
        'configuracion' => $configuracion,
        'cliente' => $cliente,
        'condicion' => $condicion,
        'medio_pago' => $medio_pago,
        'direccion_emisor' => wordwrap($configuracion->direccion_emisor, 35),
        'direccion_receptor' => wordwrap($cliente->direccion, 35),
        'moneda' => $sales->tipo_moneda,
        'tipo_cambio' => $sales->tipo_cambio
      ];

      $pdf = PDF::loadView('pdf.pdf_regimen', $data);

      //Guardalo en una variable
      $output =  $pdf->output();
      return $pdf->download('Documento#'.$sales->numero_documento.'.pdf')->header('Content-Type','application/pdf');

    }

        public function reporteSales()
    {
        return view('reporte.sales');
    }
     public function fecSales()
    {
        return view('reporte.fec');
    }
    public function reaSales()
    {
        return view('reporte.rea');
    }

        public function filtrarSales(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportSales($datos['fecha_desde'], $datos['fecha_hasta']);
    }

    public function filtrarfec(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportfec($datos['fecha_desde'], $datos['fecha_hasta']);
    }
public function filtrarSalescolon(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportSalescolon($datos['fecha_desde'], $datos['fecha_hasta']);
    }
    

        public function reporteRegimen()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        return view('reporte.regimen');
    }
    public function reporteop()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        return view('reporte.Op');
    }

        public function filtrarRegimen(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportRegimen($datos['fecha_desde'], $datos['fecha_hasta']);
    }
     public function filtrarop(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportop($datos['fecha_desde'], $datos['fecha_hasta']);
    }

        public function reporteRecepcion()
    {
        return view('reporte.recepcion');
    }


        public function filtrarRecepcion(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportarReceptor($datos['fecha_desde'], $datos['fecha_hasta']);
    }
     public function filtrarrea(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportrea($datos['fecha_desde'], $datos['fecha_hasta']);
    }



public function receptorxml(Request $request)
{
    // Validación de las fechas
    $datos = $request->validate([
        'fecha_desde' => 'required|date',
        'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
    ]);

    // Obtén los receptores de acuerdo a las fechas y configuración del usuario
    $receptores = receptor::where([
        ['fecha_xml_envio', '>=', $datos['fecha_desde']],
        ['fecha_xml_envio', '<=', $datos['fecha_hasta']],
        ['idconfigfact', '=', Auth::user()->idconfigfact],
    ])->get();

    // Verifica si hay resultados
    if ($receptores->isEmpty()) {
        \Log::warning('No se encontraron receptores en las fechas especificadas.');
        return redirect()->route('reportes.recepcion')
                         ->withStatus(__('No se encontraron archivos para descargar.'));
    }

    // Recorre los receptores y colecta las rutas de archivos válidas (solo archivos)
    $filePaths = $receptores->flatMap(function($receptor_xml) {
        return collect([
            $this->getFilePath($receptor_xml->ruta_carga),
            $this->getFilePath($receptor_xml->xml_envio),
            $this->getFilePath($receptor_xml->xml_respuesta),
        ])->filter(function($path) {
            return is_file($path);
        });
    })->unique();

    // Verifica si hay archivos para agregar al ZIP
    if ($filePaths->isEmpty()) {
        \Log::warning('No se encontraron archivos válidos para añadir al archivo ZIP.');
        return redirect()->route('reportes.recepcion')
                         ->withStatus(__('No se encontraron archivos para descargar.'));
    }

    // Crear la ruta del archivo ZIP
    $zipFilePath = public_path('Recepciones.zip');

    // Elimina el archivo ZIP existente si es necesario
    if (file_exists($zipFilePath) && is_file($zipFilePath)) {
        \Log::info("Eliminando archivo existente: $zipFilePath");
        @unlink($zipFilePath);
    }

    // Crear el archivo ZIP
    $zip = new \ZipArchive();

    if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
        \Log::error("No se pudo abrir el archivo ZIP para creación: $zipFilePath");
        return redirect()->route('reportes.recepcion')
                         ->withStatus(__('No se pudo crear el archivo ZIP.'));
    }

    // Añadir archivos de forma robusta
    foreach ($filePaths as $file) {
        if (is_file($file)) {
            // Nombre dentro del ZIP: usa un prefijo único para evitar sobrescrituras
            $zip->addFile($file, uniqid('_', true) . '_' . basename($file));
        } else {
            \Log::warning("Archivo no válido o no existente para ZIP: $file");
        }
    }

    // Cierra el ZIP solo si hay archivos añadidos
    if ($zip->numFiles > 0) {
        $zip->close();

        // Verifica si el archivo ZIP se creó correctamente
        if (file_exists($zipFilePath)) {
            // Enviar el ZIP por correo
            try {
                Mail::to(Auth::user()->email)->send(new ReceptorXmlMail($zipFilePath));
            } catch (\Throwable $e) {
                \Log::error("Error enviando correo con ZIP: " . $e->getMessage());
                // No fallar la respuesta por fallo de correo; aún puedes ofrecer la descarga
            }

            return $this->downloadZip($zipFilePath);
        } else {
            \Log::error("El ZIP no se creó correctamente: $zipFilePath");
            return $this->zipCreationError();
        }
    } else {
        // Cierra el ZIP sin añadir archivos
        $zip->close();
        \Log::warning('El archivo ZIP fue creado, pero no se añadieron archivos.');
        return redirect()->route('reportes.recepcion')
                         ->withStatus(__('No se encontraron archivos para descargar.'));
    }
}

// Método para obtener la ruta del archivo solo si existe
private function getFilePath($path)
{
    $fullPath = public_path(ltrim($path));
    return file_exists($fullPath) ? $fullPath : null;
}

// Método para manejar la descarga del ZIP
private function downloadZip($zipFilePath)
{
    \Log::info("El archivo ZIP se creó correctamente: $zipFilePath");
    return response()->download($zipFilePath)->deleteFileAfterSend(true);
}

// Método para manejar el error de creación del ZIP
private function zipCreationError()
{
    \Log::error("El archivo ZIP no se creó. Verifique permisos y espacio.");
    return redirect()->route('reportes.recepcion')->withStatus(__('El archivo ZIP no se creó. Verifica permisos y espacio.'));
}

        public function reporteProductos()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $productos = Productos::all();
        return view('reporte.productos', ['productos' => $productos]);
    }

        public function filtrarProductos(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportarProducto($datos['idproducto'], $datos['fecha_desde'], $datos['fecha_hasta']);
    }
 public function filtrarbancos(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportarBancos($datos['actividad'], $datos['fecha_desde'], $datos['fecha_hasta']);
    }

      public function reportebanco()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $configuracion = Configuracion::where('idconfigfact','=', Auth::user()->idconfigfact)->get();
        return view('reporte.banco', ['configuracion' => $configuracion]);
    }

        public function reporteCxc()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $cxcobrar = Cxcobrar::all();
        return view('reporte.cxc', ['cxcobrar' => $cxcobrar]);
    }

        public function filtrarCxc(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportCxc($datos['fecha_desde'], $datos['fecha_hasta'],$datos['idcxcobrar']);
    }

        public function reporteCxcabono()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $cxcobrar = Cxcobrar::all();
        return view('reporte.cxcabono', ['cxcobrar' => $cxcobrar]);
    }

        public function filtrarCxcabono(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportCxcab($datos['fecha_desde'], $datos['fecha_hasta'],$datos['idcxcobrar']);
    }

        public function reporteCxp()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $cxpagar = Cxpagar::all();
        return view('reporte.cxp', ['cxpagar' => $cxpagar]);
    }

        public function filtrarCxp(Request $request)
    {
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportCxp($datos['fecha_desde'], $datos['fecha_hasta'],$datos['idcxpagar']);
    }

        public function reporteDventas()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        return view('reporte.dventas');
    }

        public function filtrarDventas(Request $request)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportDventas($datos['fecha_desde'], $datos['fecha_hasta']);
    }

        public function reporteDcompras()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        return view('reporte.dcompras');
    }

        public function filtrarDcompras(Request $request)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
        return app('App\Http\Controllers\ExcelController')->exportDcompras($datos['fecha_desde'], $datos['fecha_hasta']);
    }

        public function reporteUtilidad()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        return view('reporte.utilidad');
    }

        public function filtrarUtilidad(Request $request)
    {
        $datos = $request->all();
        $sales = Sales::whereBetween('fecha_creada', [$datos['fecha_desde'], $datos['fecha_hasta']])
        ->where('estatus_sale', '=', 2)
        ->where('tipo_documento', '!=', '03')
        ->where('idconfigfact', '=', Auth::user()->idconfigfact)
        ->get();

        if (count($sales) > 0) {
          $sumatoria_utilidad_factura = 0;
          foreach ($sales as $venta) {

            $qry = DB::table('sales_item')
            ->leftjoin('productos', 'sales_item.idproducto', '=', 'productos.idproducto')
            ->select('productos.costo', 'sales_item.cantidad', 'sales_item.valor_neto')
            ->where('sales_item.idsales', '=', $venta->idsale)
            ->get();
            foreach ($qry as $detalle) {
              $sumatoria_utilidad_factura += $detalle->valor_neto - ($detalle->costo * $detalle->cantidad);
            }
          }

          $sales_nc = Sales::whereBetween('fecha_creada', [$datos['fecha_desde'], $datos['fecha_hasta']])
          ->where('estatus_sale', '=', 2)
          ->where('tipo_documento', '=', '03')
          ->where('idconfigfact', '=', Auth::user()->idconfigfact)
          ->get();

          if (count($sales_nc) > 0) {

            $sumatoria_utilidad_nc = 0;
            foreach ($sales_nc as $venta) {

              $qry = DB::table('sales_item')
              ->leftjoin('productos', 'sales_item.idproducto', '=', 'productos.idproducto')
              ->select('productos.costo', 'sales_item.cantidad', 'sales_item.valor_neto')
              ->where('sales_item.idsales', '=', $venta->idsale)
              ->get();
              foreach ($qry as $detalle) {
                $sumatoria_utilidad_nc += $detalle->valor_neto - ($detalle->costo * $detalle->cantidad);
              }
            }
          } else {
            $sumatoria_utilidad_nc = 0;
          }

          $total = $sumatoria_utilidad_factura - $sumatoria_utilidad_nc;
        } else {

          $sumatoria_utilidad_factura = 0;
          $sumatoria_utilidad_nc = 0;
          $total = 0;

        }
        $data = [
          'sumatoria_utilidad_nc' => number_format($sumatoria_utilidad_nc, 2,',','.'),
          'sumatoria_utilidad_factura' => number_format($sumatoria_utilidad_factura, 2,',','.'),
          'total' => number_format($total,2,',','.'),
        ];
        return view('reporte.filtro_utilidad', compact('data'));
    }
        public function imprimirCxc($id)
    {
      $log_cxcobrar = Log_cxcobrar::find($id);
      $mov_cxcobrar = Mov_cxcobrar::find($log_cxcobrar->idmovcxcobrar);
      $cxcobrar = Cxcobrar::find($mov_cxcobrar->idcxcobrar);
      $configuracion = Configuracion::find($cxcobrar->idconfigfact);
      $cliente = Cliente::find($cxcobrar->idcliente);
      $caja = Cajas::find($log_cxcobrar->idcaja);
      switch ($log_cxcobrar->tipo_mov) {
        case '1':
          $tipo_mov = 'ABONO';
        break;
        case '2':
          $tipo_mov = 'ABONO PARCIAL';
        break;
      }
      try {
        if (!is_null($caja->nombre_imp)) {
            $connector = new WindowsPrintConnector($caja->nombre_imp);
        }else{
            $connector = new NetworkPrintConnector("".$caja->ip_imp, 9100);
        }
        /* Print a "Hello world" receipt" */
        $printer = new Printer($connector);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_FONT_B);
        $printer->text($configuracion->nombre_emisor."\n");
        $printer->text("Identificacion: ". $configuracion->numero_id_emisor ."\n");
        $printer->text('Correo: '. $configuracion->email_emisor."\n");
        $printer->text('Tel: '. $configuracion->telefono_emisor."\n");
        $printer->text('Direccion: '. $configuracion->direccion_emisor."\n");
        $printer->setJustification();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text('Fecha: '. $log_cxcobrar->fecha_rec_mov."\n");
        $printer->text('Tipo Documento: Abono '."\n");
        $printer->text('Tipo Movimiento: '.$tipo_mov."\n");
        $printer->text('Referencia: '. $log_cxcobrar->referencia."\n");
        $printer->text('Numero de Abono: '. $log_cxcobrar->num_recibo_abono."\n");
        $printer->text('Cliente: '. $cliente->nombre."\n");
        $printer->text('Identificacion: '. $cliente->num_id."\n");
        $printer->text('Telefono: '. $cliente->telefono."\n");
        $printer->text('Correo: '. $cliente->email."\n");
        $printer->setJustification();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->textRaw("{$log_cxcobrar->fecha_rec_mov} - {$log_cxcobrar->num_recibo_abono} - {$log_cxcobrar->monto_abono}\n");
        $printer->text("________________________________ \n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Cuenta Pendiente Monto Inicial:");
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text(str_pad(number_format($mov_cxcobrar->monto_mov,2,',','.'), 25, " ", STR_PAD_LEFT)." \n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Total Abonos:");
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text(str_pad(number_format($mov_cxcobrar->abono_mov,2,',','.'), 20, " ", STR_PAD_LEFT)." \n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Saldo Pendiente:");
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text(str_pad(number_format($mov_cxcobrar->saldo_pendiente,2,',','.'), 21, " ", STR_PAD_LEFT)." \n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->cut();
        $printer->close();
        return "Success";
      } catch (Exception $e) {
        return "Couldn't print to this printer: " . $e->etMessage() . "\n";
      }
    }
        public function imprimirFactura($id, $idcaja)
    {
      $sales = Sales::find($id);
      $sales_item = Sales_item::where('idsales',$id)->get();
      $facelectron = Facelectron::where('idsales', $id)->get();
      $configuracion = Configuracion::find($facelectron[0]->idconfigfact);
      $consulta_fac = Facelectron::where([ ['idsales', '=', $sales->referencia_sale] ])->get();
      $cliente = Cliente::find($sales->idcliente);
      $caja = Cajas::find($idcaja);
      switch ($sales->condicion_venta) {
        case '01':
          $condicion = 'Contado';
          break;
          case '02':
          $condicion = 'Credito';
          break;
      }
      switch ($sales->medio_pago) {
        case '01':
          $medio_pago = 'Efectivo';
          break;
          case '02':
          $medio_pago = 'Tarjeta';
          break;
          case '03':
          $medio_pago = 'Cheque';
          break;
          case '04':
          $medio_pago = 'Transferencia – depósito bancario';
          break;
          case '05':
           $medio_pago = 'Recaudado por terceros';
          break;
      }
      switch ($facelectron[0]->tipodoc) {
        case '01':
          $tipodoc = 'Factura Electronica';
        break;
        case '02':
          $tipodoc = 'Nota de Debito Electronica';
        break;
        case '03':
          $tipodoc = 'Nota de Credito Electronica';
        break;
        case '04':
          $tipodoc = 'Tiquete Electronico';
        break;
        case '08':
          $tipodoc = 'Factura Electronica de Compra';
        break;
        case '09':
          $tipodoc = 'Factura Electronica de exportacion';
        break;
      }
      try {
        if (!is_null($caja->nombre_imp)) {
            $connector = new WindowsPrintConnector($caja->nombre_imp);
        }else{
            $connector = new NetworkPrintConnector("".$caja->ip_imp, 9100);
        }

        /* Print a "Hello world" receipt" */
        $printer = new Printer($connector);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_FONT_B);
        $printer->text($configuracion->nombre_emisor."\n");
        $printer->text("Identificacion: ". $configuracion->numero_id_emisor ."\n");
        $printer->text('Correo: '. $configuracion->email_emisor."\n");
        $printer->text('Tel: '. $configuracion->telefono_emisor."\n");
        $printer->text('Direccion: '. $configuracion->direccion_emisor."\n");
        $printer->setJustification();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text('Fecha: '. $facelectron[0]->fechahora."\n");
        $printer->text('Tipo Documento: '. $tipodoc."\n");
        $printer->text('Condicion Venta: '. $condicion."\n");
        $printer->text('Tipo Pago: '. $medio_pago."\n");
        $printer->text('Moneda: '. $sales->tipo_moneda."\n");
        $printer->text('Tipo Cambio: '. $sales->tipo_cambio."\n");
        $printer->text('Numero de Factura: '. $facelectron[0]->clave."\n");
        $printer->text('Cliente: '. $cliente->nombre."\n");
        $printer->text('Identificacion: '. $cliente->num_id."\n");
        $printer->text('Telefono: '. $cliente->telefono."\n");
        $printer->text('Correo: '. $cliente->email."\n");
        $printer->text("________________________________ \n");
        $printer->setJustification();
        $total_neto = 0;
        $total_descuento = 0;
        $total_comprobante = 0;
        $total_impuesto = 0;
        $total_iva_devuelto = 0;
        foreach ($sales_item as $sale_i) {
            if ($sale_i->existe_exoneracion == '00') {
              if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {
                  $total_iva_devuelto =  $total_iva_devuelto +  $sale_i->valor_impuesto;
              }
              $total = $sale_i->valor_neto + ($sale_i->valor_impuesto -  $total_iva_devuelto);
              $total_impuesto = $total_impuesto + $sale_i->valor_impuesto;
            }else{
              $exoneracion = App\Items_exonerados::where('idsalesitem', $sale_i->idsalesitem)->get();
              $monto_imp_exonerado = $sale_i->valor_impuesto - $exoneracion[0]->monto_exoneracion;
              if ($sales->medio_pago == '02' and $sale_i->impuesto_prc == '4.00') {
                $total_iva_devuelto =  $total_iva_devuelto +  $sale_i->valor_impuesto;
              }
              $total = ($sale_i->valor_neto+ $monto_imp_exonerado)-$total_iva_devuelto;
              $total_impuesto = $total_impuesto + $monto_imp_exonerado;
            }
            $total_neto = $total_neto + $sale_i->valor_neto;
            $total_descuento = $total_descuento + $sale_i->valor_descuento;
            $total_comprobante = $total_comprobante + $total;
          $printer->setJustification(Printer::JUSTIFY_LEFT);
          $printer->textRaw("{$sale_i->cantidad}x{$sale_i->codigo_producto} - {$sale_i->nombre_producto} - {$total}\n");
        }
        $printer->text("________________________________ \n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Total Neto:");
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text(str_pad(number_format($total_neto,2,',','.'), 25, " ", STR_PAD_LEFT)." \n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Total Descuento:");
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text(str_pad(number_format($total_descuento,2,',','.'), 20, " ", STR_PAD_LEFT)." \n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Total Impuesto:");
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text(str_pad(number_format($total_impuesto,2,',','.'), 21, " ", STR_PAD_LEFT)." \n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Total Comprobante:");
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text(str_pad(number_format($total_comprobante,2,',','.'), 18, " ", STR_PAD_LEFT)." \n\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("AUTORIZADO MEDIANTE RESOLUCION N° DRT-R-033-2019 VERSION XML: 4.3 \n");
        $printer->feed(3);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Nombre y Firma Recibido Conforme.\n");
        $printer->text("Factura Electronica San Esteban Tel: 2460-17/8309-3816 email: fesanesteban@gmail.com.\n");
        $printer->cut();
        $printer->close();
        return "Success";
      } catch (Exception $e) {
        return "Couldn't print to this printer: " . $e->etMessage() . "\n";
      }
    }

      public function pdfCaja($id)
    {

      $log_caja = Log_cajas::find($id);
      $caja = Cajas::find($log_caja->idcaja);
      $usuario = User::find($log_caja->idusuario);
      $data = [
        'log_caja' => $log_caja,
        'caja' => $caja,
        'usuario' => $usuario
      ];

      $pdf = PDF::loadView('pdf.pdf_caja', $data);
      $nombreArchivo = 'Caja#'.$id.'.pdf';
      //Guardalo en una variable
      $output =  $pdf->output();
      file_put_contents('./PDF/Caja/'.$nombreArchivo, $output);
      Log_cajas::where('idlogcaja', $id)
              ->update(
                ['ruta_reporte' =>  ''.$nombreArchivo]);
      // Una vez lo guardes en local lo puedes subir o enviar a un ftp.
      return $pdf->download($nombreArchivo)->header('Content-Type','application/pdf');
    }

       public function reporteIva()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $configuracion = Configuracion::where('idconfigfact','=', Auth::user()->idconfigfact)->get();
        return view('reporte.iva', ['configuracion' => $configuracion]);
    }

    public function reportefac()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $configuracion = Configuracion::where('idconfigfact','=', Auth::user()->idconfigfact)->get();
        return view('reporte.fac', ['configuracion' => $configuracion]);
    }
public function pdffac(Request $request)
    {

      $datos = $request->all();
      $datoso = $request->all();
      $desde = $datoso['fecha_desde'];
      $hasta = $datoso['fecha_hasta'];
      $config = $datos['idconfigfact'];
      $query = \DB::table('sales_item')->select('sales_item.*', 'sales.tipo_documento',  'configuracion.factor_receptor','configuracion.logo','configuracion.numero_id_emisor','configuracion.nombre_emisor', 'codigo_actividad.codigo_actividad', 'sales.total_iva_devuelto', 'facelectron.estatushacienda')
      ->join('sales', 'sales_item.idsales', '=', 'sales.idsale')
      ->join('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
      ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
      ->join('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
      ->where([
                ['sales.fecha_creada', '>=', $datos['fecha_desde']],
                ['sales.fecha_creada', '<=', $datos['fecha_hasta']],
                ['sales.idconfigfact', '=', $datos['idconfigfact']],
                ['sales.idcodigoactv', '=', $datos['actividad']],
                ['facelectron.estatushacienda', '=', 'aceptado'],
                ['sales.estatus_sale', '=', 2],
                            ])

      ->get();

$documentos = DB::table('sales')
        ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
        ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
        ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
        ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento')
        ->where('sales.estatus_sale', 2)
         ->where('sales.tipo_documento', '!=', '08')
        ->where('facelectron.estatushacienda', '=', 'aceptado')
        ->where('sales.idconfigfact', '=', Auth::user()->idconfigfact)
        ->whereBetween('facelectron.fechahora', [$datos['fecha_desde'], $datos['fecha_hasta']])
        ->get();

        $salest = Sales::where([
            ['tipo_documento','!=', '08'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']]
        ])->orderBy('numero_documento', 'desc')->get();
      $datos = [
        'tipo_impuesto' => []
      ];
      $fcompra = [
        'fcompra' => []
      ];
      $totalmto[1] = 0;
      $totaliva[1] = 0;
      $totalivax[1] = 0;
      $totalmto[2] = 0;
      $totaliva[2] = 0;
      $totalivax[2] = 0;
      $totalmto[3] = 0;
      $totaliva[3] = 0;
      $totalivax[3] = 0;
      $totalmto[4] = 0;
      $totaliva[4] = 0;
      $totalivax[4] = 0;
      $totalmto[5] = 0;
      $totaliva[5] = 0;
      $totalivax[5] = 0;
      $totalmto[6] = 0;
      $totaliva[6] = 0;
      $totalivax[6] = 0;
      $totalmto[7] = 0;
      $totaliva[7] = 0;
      $totalivax[7] = 0;
      $totalmto[8] = 0;
      $totaliva[8] = 0;
      $totalivax[8] = 0;
      $totalmto[9] = 0;
      $totaliva[9] = 0;
      $totalivax[9] = 0;
      foreach ($query as $qry) {

        for ($i=1; $i < 10; $i++) {

          for ($x=1; $x < 5; $x++) {

            if ($qry->tipo_impuesto === '0'.$i) {

              if ($qry->tipo_documento === '0'.$x) {

                if ($qry->tipo_documento === '08') {

                    if (Arr::has($fcompra['fcompra'].'.monto_neto')) {

                      $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                      $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;
                    } else {
                    $fcompra['fcompra'] = [
                      'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                      'monto_iva' => $qry->valor_impuesto
                    ];
                  }
                }else{
                  if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {
                    $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                    $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ( $qry->valor_impuesto );
                     $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                    $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                    $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                    $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];
                  }else{
                    $datos['tipo_impuesto'][$i][$x] = [
                      'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                      'monto_iva' => $qry->valor_impuesto ,
                      'monto_ivax' =>  $qry->exo_monto,
                    ];
                    $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                    $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                      $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                  }
                }
              }
            } else {

                  if ($i == 9) {

                    if ($qry->tipo_impuesto === '99') {

                      if ($qry->tipo_documento === '0'.$x) {

                        if ($qry->tipo_documento === '08') {

                          if (Arr::has($fcompra['fcompra'].'.monto_neto')) {

                            $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;

                          } else {

                            $fcompra['fcompra'] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto
                            ];
                          }

                        } else {

                          if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {

                            $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ($qry->valor_impuesto);
                            $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                            $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                            $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                            $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];

                          } else {

                            $datos['tipo_impuesto'][$i][$x] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto ,
                              'monto_ivax' =>  $qry->exo_monto,
                            ];

                            $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                            $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                            $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                          }
                        }
                      }
                    }
                  }
            }
          }
        }
      }
      $query2 = \DB::table('receptor')->select('receptor.*')
      ->where([
                ['receptor.fecha_xml_envio', '>=', $desde],
                ['receptor.fecha_xml_envio', '<=', $hasta],
                ['receptor.estatus_hacienda', '=', 'aceptado'],
                ['receptor.idconfigfact', '=', $config],
            ])
      ->get();
      $iva_recepcionado = $query2->sum('total_impuesto');
      $total_receptor = $query2->sum('total_comprobante');
      $total_iva_devuelto = $query->sum('total_iva_devuelto');
      $datos_receptor = [
        'clasifica_d151' => []
      ];
      foreach ($query2 as $qry2) {
        for ($i=1; $i < 8; $i++) {
            if ($qry2->clasifica_d151 === ''.$i) {
                if (Arr::has($datos_receptor['clasifica_d151'], $i.'.total_impuesto')) {
                   $datos_receptor['clasifica_d151'][$i]['total_impuesto'] = $datos_receptor['clasifica_d151'][$i]['total_impuesto'] + $qry2->total_impuesto;
                    $datos_receptor['clasifica_d151'][$i]['total_comprobante'] = $datos_receptor['clasifica_d151'][$i]['total_comprobante'] + $qry2->total_comprobante;
                    $datos_receptor['clasifica_d151'][$i]['imp_creditar'] = $datos_receptor['clasifica_d151'][$i]['imp_creditar'] + $qry2->imp_creditar;
                    $datos_receptor['clasifica_d151'][$i]['gasto_aplica'] = $datos_receptor['clasifica_d151'][$i]['gasto_aplica'] + $qry2->gasto_aplica;
                    $datos_receptor['clasifica_d151'][$i]['hacienda_imp_creditar'] = $datos_receptor['clasifica_d151'][$i]['hacienda_imp_creditar'] + $qry2->hacienda_imp_creditar;
                    $datos_receptor['clasifica_d151'][$i]['hacienda_gasto_aplica'] = $datos_receptor['clasifica_d151'][$i]['hacienda_gasto_aplica'] + $qry2->hacienda_gasto_aplica;
                }else{
                    $datos_receptor['clasifica_d151'][$i] = [
                      'total_impuesto' => $qry2->total_impuesto,
                      'total_comprobante' => $qry2->total_comprobante,
                      'imp_creditar' => $qry2->imp_creditar,
                      'gasto_aplica' => $qry2->gasto_aplica,
                      'hacienda_imp_creditar' => $qry2->hacienda_imp_creditar,
                      'hacienda_gasto_aplica' => $qry2->hacienda_gasto_aplica,
                    ];
                }
              }
          }
      }


      $data = [
        'datos' => collect($datos),
        'datoso' => $datoso,
        'totalmto' => $totalmto,
        'totaliva' => $totaliva,
        'query' => $query,
        'fcompra' => $fcompra,
        'iva_recepcionado' => $iva_recepcionado,
        'total_receptor' => $total_receptor,
        'datos_receptor' => $datos_receptor,
        'receptor' => $query2,
        'documentos' =>$documentos,
        'salest' =>$salest,
        'total_iva_devuelto' => $total_iva_devuelto
      ];
      $pdf = PDF::loadView('pdf.pdf_fac', $data)->setPaper('a4', 'landscape');
      $nombreArchivo = 'Reporte_Doc_Emitidos_'.date('m').'.pdf';
      //Guardalo en una variable
      $output =  $pdf->output();
      file_put_contents('./PDF/'.$config.'/IVA/'.$nombreArchivo, $output);
      // Una vez lo guardes en local lo puedes subir o enviar a un ftp.
      return $pdf->download($nombreArchivo)->header('Content-Type','application/pdf');

    }
      public function pdfIva(Request $request)
    {

      $datos = $request->all();
    
      $desde = $datos['fecha_desde'];
      $hasta = $datos['fecha_hasta'];
      $config = $datos['idconfigfact'];
      if(is_null($datos['actividad']))
      $actividad_pdf='TODAS';
      else{
      $actividad_pdf=  $datos['actividad'];  
      }
      
      if (!empty($datos['actividad'])) {  
          
      $query = \DB::table('sales_item')->select('sales_item.*', 'sales.tipo_documento', 'sales.tipo_moneda','sales.tipo_cambio', 'configuracion.factor_receptor', 'codigo_actividad.codigo_actividad', 'sales.total_iva_devuelto', 'facelectron.estatushacienda')
      ->join('sales', 'sales_item.idsales', '=', 'sales.idsale')
      ->join('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
      ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
      ->join('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
      ->where([
                ['sales.fecha_creada', '>=', $datos['fecha_desde']],
                ['sales.fecha_creada', '<=', $datos['fecha_hasta']],
                ['sales.idconfigfact', '=', $datos['idconfigfact']],
                ['sales.idcodigoactv', '=', $datos['actividad']],
                ['facelectron.estatushacienda', '=', 'aceptado'],
                ['sales.estatus_sale', '=', 2],
                ['sales.tipo_doc_ref', '!=', 17],
                 
                            ])->get();
      }else{
           $query = \DB::table('sales_item')->select('sales_item.*', 'sales.tipo_documento', 'sales.tipo_moneda','sales.tipo_cambio', 'configuracion.factor_receptor', 'codigo_actividad.codigo_actividad', 'sales.total_iva_devuelto', 'facelectron.estatushacienda')
      ->join('sales', 'sales_item.idsales', '=', 'sales.idsale')
      ->join('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
      ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
      ->join('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
      ->where([
                ['sales.fecha_creada', '>=', $datos['fecha_desde']],
                ['sales.fecha_creada', '<=', $datos['fecha_hasta']],
                ['sales.idconfigfact', '=', $datos['idconfigfact']],
                 ['sales.tipo_doc_ref', '!=', 17],
                ['facelectron.estatushacienda', '=', 'aceptado'],
                ['sales.estatus_sale', '=', 2],
                            ])->get();
          
      }
      
      //dd($query);
      $datos = [
        'tipo_impuesto' => []
      ];
      $fcompra = [
        'fcompra' => []
      ];
      $totalmto[1] = 0;
      $totaliva[1] = 0;
      $totalivax[1] = 0;
      $totalmto[2] = 0;
      $totaliva[2] = 0;
      $totalivax[2] = 0;
      $totalmto[3] = 0;
      $totaliva[3] = 0;
      $totalivax[3] = 0;
      $totalmto[4] = 0;
      $totaliva[4] = 0;
      $totalivax[4] = 0;
      $totalmto[5] = 0;
      $totaliva[5] = 0;
      $totalivax[5] = 0;
      $totalmto[6] = 0;
      $totaliva[6] = 0;
      $totalivax[6] = 0;
      $totalmto[7] = 0;
      $totaliva[7] = 0;
      $totalivax[7] = 0;
      $totalmto[8] = 0;
      $totaliva[8] = 0;
      $totalivax[8] = 0;
      $totalmto[9] = 0;
      $totaliva[9] = 0;
      $totalivax[9] = 0;
      
      $totalmtousd[1] = 0;
      $totalivausd[1] = 0;
      $totalivaxusd[1] = 0;
      $totalmtousd[2] = 0;
      $totalivausd[2] = 0;
      $totalivaxusd[2] = 0;
      $totalmtousd[3] = 0;
      $totalivausd[3] = 0;
      $totalivaxusd[3] = 0;
      $totalmtousd[4] = 0;
      $totalivausd[4] = 0;
      $totalivaxusd[4] = 0;
      $totalmtousd[5] = 0;
      $totalivausd[5] = 0;
      $totalivaxusd[5] = 0;
      $totalmtousd[6] = 0;
      $totalivausd[6] = 0;
      $totalivaxusd[6] = 0;
      $totalmtousd[7] = 0;
      $totalivausd[7] = 0;
      $totalivaxusd[7] = 0;
      $totalmtousd[8] = 0;
      $totalivausd[8] = 0;
      $totalivaxusd[8] = 0;
      $totalmtousd[9] = 0;
      $totalivausd[9] = 0;
      $totalivaxusd[9] = 0;
      //dd(count($query));
      foreach ($query as $qry) {

        for ($i=1; $i < 10; $i++) {

          for ($x=1; $x < 5; $x++) {

            if ($qry->tipo_impuesto === '0'.$i) {

              if ($qry->tipo_documento === '0'.$x) {

                if ($qry->tipo_documento === '08') {

                    if (Arr::has($fcompra['fcompra'].'.monto_neto')) {

                      $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                      $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;
                    } else {
                    $fcompra['fcompra'] = [
                      'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                      'monto_iva' => $qry->valor_impuesto
                    ];
                  }
                  
                  
                }else{
                 
                  
                          
                  if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {
                    $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                    $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ( $qry->valor_impuesto );
                     $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                    $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                    $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                    $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];
                  }else{
                    $datos['tipo_impuesto'][$i][$x] = [
                      'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                      'monto_iva' => $qry->valor_impuesto ,
                      'monto_ivax' =>  $qry->exo_monto,
                    ];
                    $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                    $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                      $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                  }
             
                 
                }
              }
            } else {

                  if ($i == 9) {

                    if ($qry->tipo_impuesto === '99') {

                      if ($qry->tipo_documento === '0'.$x) {

                        if ($qry->tipo_documento === '08') {

                          if (Arr::has($fcompra['fcompra'].'.monto_neto')) {

                            $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;

                          } else {

                            $fcompra['fcompra'] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto
                            ];
                          }

                        } else {

                          if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {

                            $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ($qry->valor_impuesto);
                            $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                            $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                            $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                            $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];

                          } else {

                            $datos['tipo_impuesto'][$i][$x] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto ,
                              'monto_ivax' =>  $qry->exo_monto,
                            ];

                            $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                            $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                            $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                          }
                        }
                      }
                    }
                  }
            }
          }
        }
      }
      $query2 = \DB::table('receptor')->select('receptor.*')
      ->where([
                ['receptor.fecha_xml_envio', '>=', $desde],
                ['receptor.fecha_xml_envio', '<=', $hasta],
                ['receptor.estatus_hacienda', '=', 'aceptado'],
                ['receptor.idconfigfact', '=', $config],
            ])
      ->get();
      $iva_recepcionado = $query2->sum('total_impuesto');
      $total_receptor = $query2->sum('total_comprobante');
      $total_iva_devuelto = $query->sum('total_iva_devuelto');
      $datos_receptor = [
        'clasifica_d151' => []
      ];
      
      //dd($query2);
      foreach ($query2 as $qry2) {
        for ($i=1; $i < 8; $i++) {
            if ($qry2->clasifica_d151 === ''.$i) {
                if (Arr::has($datos_receptor['clasifica_d151'], $i.'.total_impuesto')) {
                   $datos_receptor['clasifica_d151'][$i]['total_impuesto'] = $datos_receptor['clasifica_d151'][$i]['total_impuesto'] + $qry2->total_impuesto;
                    $datos_receptor['clasifica_d151'][$i]['total_comprobante'] = $datos_receptor['clasifica_d151'][$i]['total_comprobante'] + $qry2->total_comprobante;
                    $datos_receptor['clasifica_d151'][$i]['imp_creditar'] = $datos_receptor['clasifica_d151'][$i]['imp_creditar'] + $qry2->imp_creditar;
                    $datos_receptor['clasifica_d151'][$i]['gasto_aplica'] = $datos_receptor['clasifica_d151'][$i]['gasto_aplica'] + $qry2->gasto_aplica;
                    $datos_receptor['clasifica_d151'][$i]['hacienda_imp_creditar'] = $datos_receptor['clasifica_d151'][$i]['hacienda_imp_creditar'] + $qry2->hacienda_imp_creditar;
                    $datos_receptor['clasifica_d151'][$i]['hacienda_gasto_aplica'] = $datos_receptor['clasifica_d151'][$i]['hacienda_gasto_aplica'] + $qry2->hacienda_gasto_aplica;
                }else{
                    $datos_receptor['clasifica_d151'][$i] = [
                      'total_impuesto' => $qry2->total_impuesto,
                      'total_comprobante' => $qry2->total_comprobante,
                      'imp_creditar' => $qry2->imp_creditar,
                      'gasto_aplica' => $qry2->gasto_aplica,
                      'hacienda_imp_creditar' => $qry2->hacienda_imp_creditar,
                      'hacienda_gasto_aplica' => $qry2->hacienda_gasto_aplica,
                    ];
                }
              }
          }
      }

      //INICIA
 if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }

      //actividad del emisor.
       $codigos_actividad = DB::table('codigo_actividad')
        ->select('codigo_actividad.*')
        ->where('codigo_actividad.idconfigfact', '=', Auth::user()->idconfigfact)
        ->get();
        // dd($codigos_actividad);
       
        $array_final = [];

        foreach ($codigos_actividad as $actividad) {
            $array_proveedores = [];
            $array_proveedores['actividad'] = $actividad->codigo_actividad;
            //dd( $actividad->codigo_actividad);
            //Consulta de las recepciones
            $data_array = [
                'desde' => $desde,
                'hasta' => $hasta,
                'actividad' => $actividad->idcodigoactv
            ];
           //dd($data_array);
            $array_recepciones = $this->getProveedorRecepcion($data_array);
            //final de proveedores
            $array_proveedores['proveedores'] = $array_recepciones;
            array_push($array_final, $array_proveedores);
             // dd($array_proveedores['proveedores'] );
        }
        
      
        //dd($array_final);
       // return view('reporte.comproveedor', ['fecha' => $fecha, 'array_final' => $array_final]);

        //FIN
      $data = [
        'datos' => collect($datos),
        'totalmto' => $totalmto,
        'totaliva' => $totaliva,
        'query' => $query,
        'fcompra' => $fcompra,
        'iva_recepcionado' => $iva_recepcionado,
        'total_receptor' => $total_receptor,
        'datos_receptor' => $datos_receptor,
        'receptor' => $query2,
        'total_iva_devuelto' => $total_iva_devuelto,
       'fecha_inicio' => $desde,
       'fecha_fin' => $hasta,
       'codigo_actividad'=> $actividad_pdf,
       'array_final'=>$array_final
      ];
    
      $pdf = PDF::loadView('pdf.pdf_iva', $data)->setPaper('a4', 'landscape');
      
      $nombreArchivo = 'Reporte_IVA_' . date('m_Y', strtotime($hasta)) . '.' . Auth::user()->config_u[0]->nombre_emisor . '.pdf';  
      //Guardalo en una variable
      $output =  $pdf->output();
       
      file_put_contents('./PDF/'.$config.'/IVA/'.$nombreArchivo, $output);
      // Una vez lo guardes en local lo puedes subir o enviar a un ftp.
    //  dd($nombreArchivo);
      return $pdf->download($nombreArchivo)->header('Content-Type','application/pdf');

    }

     public function pdfPedido($id)
    {

      $pedido = Pedidos::find($id);
      $pedido_items = Pedidos_item::where('idpedido', $id)->get();
      $configuracion = Configuracion::find($pedido->idconfigfact);
      $cliente = Cliente::find($pedido->idcliente);
      $data = [
        'pedido' => $pedido,
        'pedido_items' => $pedido_items,
        'cliente' => $cliente,
        'configuracion' => $configuracion
      ];

      $pdf = PDF::loadView('pdf.pdf_pedido', $data);
      $nombreArchivo = 'Pedido#'.$pedido->numero_documento.'.pdf';
      $url = './PDF/'.$pedido->idconfigfact.'/Pedidos/'.$nombreArchivo;
      //Guardalo en una variable
      $output =  $pdf->output();
      file_put_contents('./PDF/'.$pedido->idconfigfact.'/Pedidos/'.$nombreArchivo, $output);
      Pedidos::where('idpedido', $id)
              ->update(
                ['pdf_pedido' =>  ''.$url]);
      // Una vez lo guardes en local lo puedes subir o enviar a un ftp.
      return $pdf->download($nombreArchivo)->header('Content-Type','application/pdf');
    }

    function Xml2Array($contents, $get_attributes=1, $priority = 'tag') {
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

    function Limpiar_Mensaje($string){

    if (!empty($string)) {

      $string = trim($string);

      $string = str_replace(
          array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
          array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
          $string
      );

      $string = str_replace(
          array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
          array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
          $string
      );

      $string = str_replace(
          array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
          array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
          $string
      );

      $string = str_replace(
          array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
          array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
          $string
      );

      $string = str_replace(
          array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
          array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
          $string
      );

      $string = str_replace(
          array('ñ', 'Ñ', 'ç', 'Ç'),
          array('n', 'N', 'c', 'C',),
          $string
      );

      $string = str_replace(
          array("\\", "¨", "º", "-", "~",
              "#", "@", "|", "!", "\"",
              "·", "$", "%", "&", "/",
              "(", ")", "?", "'", "¡",
              "¿", "[", "^", "<code>", "]",
              "+", "}", "{", "¨", "´",
              ">", "< ", ";", ",", ":",
              ".", " "),
          ' ',
          $string
      );
    return $string;
    }else{
      $string = 'Documento sin detalle mensaje';
      return $string;
    }
    }

       public function reporteDailysales()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
          $now = Carbon::now()->toDateTimeString();
          $valores = explode(' ', $now);
          $fecha = [];
          $fecha['desde'] = $valores[0];
          $fecha['hasta'] = $valores[0];
          $documentos = DB::table('sales')
          ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento')
          ->where('sales.estatus_sale', 2)
          ->where('sales.tipo_doc_ref','!=', 17)
         
          ->where('sales.tipo_documento', '!=', '08')
          ->where('facelectron.estatushacienda', '=', 'aceptado')
          ->where('facelectron.fechahora', '=', $valores[0])
          ->where('sales.idconfigfact', '=', Auth::user()->idconfigfact)
          ->get();

          $resultados = [];
          $positivo = 0;
          $negativo = 0;
          $impuestos = 0;
          $positivo_rs = 0;
          $negativo_rs = 0;
          $impuestos_rs = 0;

          $impuestof=0;
          $impuestofrs=0;
          $impuestofnc=0;
          $impuestofrsnc=0;

          foreach ($documentos as $doc) {

            if ($doc->tipo_documento != '03') {

                $positivo += $doc->total_comprobante;
                $impuestos += $doc->total_impuesto;
                 $impuestof += $doc->total_impuesto;

            } else {

                $negativo += $doc->total_comprobante;
                 $impuestofnc+= $doc->total_impuesto;
            }
          }


          $sum_ops = configuracion::where('idconfigfact','=', Auth::user()->idconfigfact)->first();

            if ($sum_ops->sum_op != '1'){
          // Consulta de Regimen Simplificado
          $documentos_rs = DB::table('sales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento', 'sales.tipo_documento', 'sales.fecha_creada', 'sales.numero_documento')
          ->where('sales.estatus_sale', 2)
           ->where('sales.tipo_doc_ref','!=', 17)
          ->whereIn('sales.tipo_documento', ['96', '95'])
          ->whereBetween('sales.fecha_creada', [$valores[0], $valores[0]])
          ->where('sales.idconfigfact', '=', Auth::user()->idconfigfact)
          ->get();

          foreach ($documentos_rs as $doc_rs) {

            if ($doc_rs->tipo_documento != '95') {

                $positivo_rs += $doc_rs->total_comprobante;
                $impuestos_rs += $doc_rs->total_impuesto;
                   $impuestofrs += $doc->total_impuesto;


            } else {

                $negativo_rs += $doc_rs->total_comprobante;
                 $impuestofrsnc+= $doc->total_impuesto;
            }
          }
            }
          $resultados['notas_credito'] = $negativo + $negativo_rs;
          $resultados['facturas'] = $positivo + $positivo_rs;
          $resultados['impuestos'] = $impuestos + $impuestos_rs;
          $resultados['netoimpuesto'] = $impuestof + $impuestofrs-$impuestofrsnc-$impuestofnc;
          $callback = [];

          foreach ($documentos as $document)
          {

            // logica para tabla
            $excento_0 = 0;
            $reducida_05 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;
            // Seccion de cabecera
            $array['tipo_documento'] = $document->tipodoc;//facelectron
            $array['numero_documento'] = $document->numdoc;//facelectron
            $array['condicion'] = $document->condicion_venta; //sales
            $array['fecha_documento'] = $document->fechahora;//facelectron
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = $document->estatushacienda;//facelectron
            $array['codigo_actividad'] = $document->codigo_actividad;//actividades
            $array['tipo_cambio'] = $document->tipo_cambio;//sales
            $array['moneda'] = $document->tipo_moneda;//sales
            $array['total_descuento'] = $document->total_descuento;

            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

              switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                case '09':
                    $reducida_05 += $detalle->valor_neto;
                break;
                case '01':  
                case '11': 
                    $no_sujeto += $detalle->valor_neto;
                break;
              }
            }
            // Seccion de Impuestos
            $array['excento_0'] = $excento_0;
            $array['reducida_05'] = $reducida_05;
            $array['reducida_1'] = $reducida_1;
            $array['reducida_2'] = $reducida_2;
            $array['reducida_4'] = $reducida_4;
            $array['transitorio_0'] = $transitorio_0;
            $array['transitorio_4'] = $transitorio_4;
            $array['transitorio_8'] = $transitorio_8;
            $array['gravado_13'] = $gravado_13;
            $array['no_sujeto'] = $no_sujeto;
            // Seccion de Totales
            $array['total_iva'] = $document->total_impuesto;//sales
            $array['otros_cargos'] = $document->total_otros_cargos;//sales
            $array['iva_devuelto'] = $document->total_iva_devuelto;//sales
            $array['total_exonerado'] = $document->total_exonerado;//sales
            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }

            array_push($callback, $array);
          }
           if ($sum_ops->sum_op != '1'){
          foreach ($documentos_rs as $document) {
            $excento_0 = 0;
            $reducida_05 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;
            // Seccion de cabecera
            $array['tipo_documento'] = $document->tipo_documento;//sales
            $array['numero_documento'] = $document->numero_documento;//sales
            $array['condicion'] = $document->condicion_venta; //sales
            $array['fecha_documento'] = $document->fecha_creada;//sales
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = 'No Aplica';//facelectron
            $array['codigo_actividad'] = $document->codigo_actividad;//actividades
            $array['tipo_cambio'] = $document->tipo_cambio;//sales
            $array['moneda'] = $document->tipo_moneda;//sales
            $array['total_descuento'] = $document->total_descuento;

            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

              switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                case '09':
                    $reducida_05 += $detalle->valor_neto;
                break;
                case '01':  
                case '11': 
                    $no_sujeto += $detalle->valor_neto;
                break;
              }
            }
            // Seccion de Impuestos
            $array['excento_0'] = $excento_0;
            $array['reducida_05'] = $reducida_05;
            $array['reducida_1'] = $reducida_1;
            $array['reducida_2'] = $reducida_2;
            $array['reducida_4'] = $reducida_4;
            $array['transitorio_0'] = $transitorio_0;
            $array['transitorio_4'] = $transitorio_4;
            $array['transitorio_8'] = $transitorio_8;
            $array['gravado_13'] = $gravado_13;
            $array['no_sujeto'] = $no_sujeto;
            // Seccion de Totales
            $array['total_iva'] = $document->total_impuesto;//sales
            $array['otros_cargos'] = $document->total_otros_cargos;//sales
            $array['iva_devuelto'] = $document->total_iva_devuelto;//sales
            $array['total_exonerado'] = $document->total_exonerado;//sales
            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }

            array_push($callback, $array);
          }
           }
          ///fin doc no electronicos
          return view('reporte.daily', ['resultados' => $resultados, 'callback' => $callback, 'fecha' => $fecha]);
    }

    public function filtrarDaily(Request $request)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
       $datos = $request->all();
        $fecha = [];
        $fecha['desde'] = $datos['fecha_desde'];
        $fecha['hasta'] = $datos['fecha_hasta'];
        $actividades = [];
        $actividades['act']=$datos['actividad'];
        
        $actividad= $datos['actividad'];
        $query = DB::table('sales')
        ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
        ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
        ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
        ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.observaciones', 'sales.total_descuento')
        ->where('sales.estatus_sale', 2)
         ->where('sales.tipo_doc_ref','!=', 17)
        ->where('sales.tipo_documento','!=', '08')
        ->where('facelectron.estatushacienda', '=', 'aceptado')
        ->where('sales.idconfigfact', '=', Auth::user()->idconfigfact)
        ->whereBetween('facelectron.fechahora', [$datos['fecha_desde'], $datos['fecha_hasta']]);

      if (!empty($actividad)) {
    $query->where('sales.idcodigoactv', '=', $actividad);
}

// Ejecutar la consulta y obtener los resultados
$documentos = $query->get();

          $resultados = [];
          $positivo = 0;
          $negativo = 0;
          $impuestos = 0;
          $positivo_rs = 0;
          $negativo_rs = 0;
          $impuestos_rs = 0;

          $impuestof=0;
          $impuestofrs=0;
          $impuestofnc=0;
          $impuestofrsnc=0;

          foreach ($documentos as $doc) {

            if ($doc->tipo_documento != '03') {

                $positivo += $doc->total_comprobante;
                $impuestos += $doc->total_impuesto;
                $impuestof += $doc->total_impuesto;

            } else {

                $negativo += $doc->total_comprobante;
                $impuestofnc+= $doc->total_impuesto;
            }
          }
           $sum_ops = configuracion::where('idconfigfact','=', Auth::user()->idconfigfact)->first();

            if ($sum_ops->sum_op != '1'){
          // Consulta de Regimen Simplificado
          $query_rs = DB::table('sales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.total_descuento', 'sales.tipo_documento', 'sales.fecha_creada', 'sales.numero_documento')
          ->where('sales.estatus_sale', 2)
           ->where('sales.tipo_doc_ref','!=', 17)
          ->whereIn('sales.tipo_documento', ['96', '95'])
          ->whereBetween('sales.fecha_creada', [$datos['fecha_desde'], $datos['fecha_hasta']])
          ->where('sales.idconfigfact', '=', Auth::user()->idconfigfact);


          if (!empty($actividad)) {
    $query_rs->where('sales.idcodigoactv', '=', $actividad);
}

// Ejecutar la consulta y obtener los resultados
$documentos_rs = $query_rs->get();


          foreach ($documentos_rs as $doc_rs) {

            if ($doc_rs->tipo_documento != '95') {

                $positivo_rs += $doc_rs->total_comprobante;
                $impuestos_rs += $doc_rs->total_impuesto;
                  $impuestofrs += $doc_rs->total_impuesto;
            } else {

                $negativo_rs += $doc_rs->total_comprobante;
                 $impuestofrsnc+= $doc->total_impuesto;
            }
          }
            }
          $resultados['notas_credito'] = $negativo + $negativo_rs;
          $resultados['facturas'] = $positivo + $positivo_rs;
          $resultados['impuestos'] = $impuestos + $impuestos_rs;
           $resultados['netoimpuesto'] = $impuestof + $impuestofrs-$impuestofrsnc-$impuestofnc;

          // logica para tabla

          $callback = [];

          foreach ($documentos as $document) {
            $excento_0 = 0;
            $reducida_05 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;

            // Seccion de cabecera
            $array['tipo_documento'] = $document->tipodoc;//facelectron
            $array['numero_documento'] = $document->numdoc;//facelectron
            $array['condicion'] = $document->condicion_venta; //sales
            $array['fecha_documento'] = $document->fechahora;//facelectron
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = $document->estatushacienda;//facelectron
            $array['codigo_actividad'] = $document->codigo_actividad;//actividades
            $array['tipo_cambio'] = $document->tipo_cambio;//sales
            $array['moneda'] = $document->tipo_moneda;//sales
            $array['total_descuento'] = $document->total_descuento;

            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

              switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                 case '09':
                    $reducida_05 += $detalle->valor_neto;
                break;
                case '01':  
                case '11': 
                    $no_sujeto += $detalle->valor_neto;
                break;
              }
            }
            // Seccion de Impuestos
            $array['excento_0'] = $excento_0;
            $array['reducida_05'] = $reducida_05;
            $array['reducida_1'] = $reducida_1;
            $array['reducida_2'] = $reducida_2;
              $array['reducida_4'] = $reducida_4;
            $array['transitorio_0'] = $transitorio_0;
            $array['transitorio_4'] = $transitorio_4;
            $array['transitorio_8'] = $transitorio_8;
            $array['gravado_13'] = $gravado_13;
            $array['no_sujeto'] = $no_sujeto;
            // Seccion de Totales
            $array['total_iva'] = $document->total_impuesto;//sales
            $array['otros_cargos'] = $document->total_otros_cargos;//sales
            $array['iva_devuelto'] = $document->total_iva_devuelto;//sales
            $array['total_exonerado'] = $document->total_exonerado;//sales
            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }

            array_push($callback, $array);
          }
            if ($sum_ops->sum_op != '1'){
          foreach ($documentos_rs as $document) {
            $excento_0 = 0;
            $reducida_05 = 0;
            $reducida_1 = 0;
            $reducida_2 = 0;
            $reducida_4 = 0;
            $transitorio_0 = 0;
            $transitorio_8 = 0;
            $transitorio_4 = 0;
            $gravado_13 = 0;
            $no_sujeto = 0;
            // Seccion de cabecera
            $array['tipo_documento'] = $document->tipo_documento;//sales
            $array['numero_documento'] = $document->numero_documento;//sales
            $array['condicion'] = $document->condicion_venta; //sales
            $array['fecha_documento'] = $document->fecha_creada;//sales
            $array['identificacion_cliente'] = $document->identificacion_cliente;//cliente
            $array['nombre_cliente'] = $document->nombre;//cliente
            $array['estado_doc'] = 'No Aplica';//facelectron
            $array['codigo_actividad'] = $document->codigo_actividad;//actividades
            $array['tipo_cambio'] = $document->tipo_cambio;//sales
            $array['moneda'] = $document->tipo_moneda;//sales
            $array['total_descuento'] = $document->total_descuento;

            // Seccion de calculo de impuesto
            $sales_item = Sales_item::where('idsales',$document->idsale)->get();

            foreach ($sales_item as $detalle) {

              switch ($detalle->tipo_impuesto) {
                case '10':
                    $excento_0 += $detalle->valor_neto;
                break;
                case '02':
                    $reducida_1 += $detalle->valor_neto;
                break;
                case '03':
                    $reducida_2 += $detalle->valor_neto;
                break;
                case '04':
                    $reducida_4 += $detalle->valor_neto;
                break;
                case '05':
                    $transitorio_0 += $detalle->valor_neto;
                break;
                case '06':
                    $transitorio_4 += $detalle->valor_neto;
                break;
                case '07':
                    $transitorio_8 += $detalle->valor_neto;
                break;
                case '08':
                    $gravado_13 += $detalle->valor_neto;
                break;
                 case '09':
                    $reducida_05 += $detalle->valor_neto;
                break;
                case '01':  
                case '11': 
                    $no_sujeto += $detalle->valor_neto;
                break;
              }
            
            // Seccion de Impuestos
            $array['excento_0'] = $excento_0;
             $array['reducida_05'] = $reducida_05;
            $array['reducida_1'] = $reducida_1;
            $array['reducida_2'] = $reducida_2;
            $array['reducida_4'] = $reducida_4;
            $array['transitorio_0'] = $transitorio_0;
            $array['transitorio_4'] = $transitorio_4;
            $array['transitorio_8'] = $transitorio_8;
            $array['gravado_13'] = $gravado_13;
            $array['no_sujeto'] = $no_sujeto;
            // Seccion de Totales
            $array['total_iva'] = $document->total_impuesto;//sales
            $array['otros_cargos'] = $document->total_otros_cargos;//sales
            $array['iva_devuelto'] = $document->total_iva_devuelto;//sales
            $array['total_exonerado'] = $document->total_exonerado;//sales
            $array['total_comprobante'] = $document->total_comprobante;//sales

            if ($document->tiene_exoneracion == '00') {

                $array['exoneracion'] = 'No';
            } else {

                $array['exoneracion'] = 'Si';
            }
}
            array_push($callback, $array);
          }

            }
      return view('reporte.daily', ['resultados' => $resultados, 'callback' => $callback, 'fecha' => $fecha, 'actividades'=>$actividades]);
    }

    public function reportesComprasProveedor()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $now = Carbon::now()->toDateTimeString();
        $valores = explode(' ', $now);
        $fecha = [];
        $fecha['desde'] = $valores[0]. 'T00:00:01-06:00';
        $fecha['hasta'] = $valores[0]. 'T23:59:59-06:00';

        $codigos_actividad = DB::table('codigo_actividad')
        ->select('codigo_actividad.*')
        ->where('codigo_actividad.idconfigfact', '=', Auth::user()->idconfigfact)
        ->get();
        $array_final = [];

        foreach ($codigos_actividad as $actividad) {
            $array_proveedores = [];
            $array_proveedores['actividad'] = $actividad->codigo_actividad;
            //Consulta de las recepciones
            $data_array = [
                'desde' => $fecha['desde'],
                'hasta' => $fecha['hasta'],
                'actividad' => $actividad->idcodigoactv
            ];
            $array_recepciones = $this->getProveedorRecepcion($data_array);
            //final de proveedores
            $array_proveedores['proveedores'] = $array_recepciones;
            array_push($array_final, $array_proveedores);
        }
        //dd($array_final);
        return view('reporte.comproveedor', ['fecha' => $fecha, 'array_final' => $array_final]);
    }

    public function filtrarComprasProveedor(Request $request)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver el reporte seleccionado");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
        $fecha = [];
        //$fecha['desde'] = $datos['fecha_desde']. 'T00:00:01-06:00';
        //$fecha['hasta'] = $datos['fecha_hasta']. 'T23:59:59-06:00';
        $fecha['desde'] = $datos['fecha_desde'];
        $fecha['hasta'] = $datos['fecha_hasta'];
        $codigos_actividad = DB::table('codigo_actividad')
        ->select('codigo_actividad.*')
        ->where('codigo_actividad.idconfigfact', '=', Auth::user()->idconfigfact)
        ->get();
        $array_final = [];

        foreach ($codigos_actividad as $actividad) {
            $array_proveedores = [];
            $array_proveedores['actividad'] = $actividad->codigo_actividad;
            //Consulta de las recepciones
            $data_array = [
                'desde' => $fecha['desde'],
                'hasta' => $fecha['hasta'],
                'actividad' => $actividad->idcodigoactv,
                'moneda' =>array("USD","CRC")
            ];
            $array_recepciones = $this->getProveedorRecepcion($data_array);
            //final de proveedores
            $array_proveedores['proveedores'] = $array_recepciones;
            array_push($array_final, $array_proveedores);
        }
        //dd($array_final);
        return view('reporte.comproveedor', ['fecha' => $fecha, 'array_final' => $array_final]);
    }

    public function getProveedorRecepcion($data_array)//$data_array
    {
        //$data_array = $request->all();
        $callback = [];
        $callback['recepciones'] = [];
        $recepciones = DB::table('receptor')->select('receptor.*')
        ->where([
            ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],
            ['receptor.pendiente', '=', 1],
            ['receptor.idcodigoactv', '=', $data_array['actividad']],
            //['receptor.moneda', '=', $data_array['moneda']]
        ])
        ->whereBetween('receptor.fecha_xml_envio', [$data_array['desde'],$data_array['hasta']])
        ->get();

        // totales - sumatorias
        $t_imp_0 = 0;
        $t_mto_0 = 0;
        $t_imp_2 = 0;
        $t_mto_2 = 0;
        $t_imp_09 = 0;
        $t_mto_09 = 0;
        $t_imp_3 = 0;
        $t_mto_3 = 0;
        $t_imp_4 = 0;
        $t_mto_4 = 0;
        $t_imp_5 = 0;
        $t_mto_5 = 0;
        $t_imp_6 = 0;
        $t_mto_6 = 0;
        $t_imp_7 = 0;
        $t_mto_7 = 0;
        $t_imp_13 = 0;
        $t_mto_13 = 0;
        $t_imp_otros = 0;
        $t_mto_otros = 0;
        $t_iva_devuelto = 0;
        $t_otros_cargos=0;
        $t_subtotal_neto = 0;
        $t_total_iva = 0;
        $t_total = 0;
        $t_subtotal_iva = 0;
        $t_exonerado_iva = 0;
        $t_exonerado_neto = 0;
        $tmto_no_sujetos4 =0;
        $timp_no_sujetos4 =0;
        $tmto_no_sujetos401 =0;
        $timp_no_sujetos401 =0;

        foreach ($recepciones as $recepcion) {
    $results = [];
    $proveedor = Cliente::where('num_id', '=', $recepcion->cedula_emisor)
                        ->where('idconfigfact', '=', Auth::user()->idconfigfact)
                        ->first();

    if (empty($proveedor)) {

          $data = array(
                'idconfigfact' => Auth::user()->idconfigfact,
                'tipo_id' =>  1,
                'num_id' => $recepcion->cedula_emisor,
                'nombre' => $recepcion->nombre_emisor,
                'email' => 'feisaac@feisaac.com',
                'telefono' =>  '24601755',
                'distrito' =>  '01',
                'canton' =>  '01',
                'provincia' =>  '2',
                'barrio' =>  '01',
                'direccion' =>  'Costa Rica',
                'tipo_cliente' => 2,
                'nombre_contribuyente' => $recepcion->nombre_emisor,
            );
          // dd($data);
            $id = DB::table('clientes')->insertGetId($data);

             $proveedor = Cliente::where('num_id', '=', $recepcion->cedula_emisor)
                        ->where('idconfigfact', '=', Auth::user()->idconfigfact)
                        ->first();

        //dd($proveedor);
    }

    // Clasificacion por Proveedor
    try {
        $consulta = DB::table('clasificacion_proveedor')
            ->select('clasificacion_proveedor.*')
            ->where([
                ['clasificacion_proveedor.idcliente', '=', $proveedor->idcliente],
            ])
            ->first();

    } catch (Exception $e) {
        return "Couldn't print to this printer: " . $e->getMessage() . "\n";
    }

    // Validación si no se encontró clasificacion
    if (is_null($consulta)) {
        $clasificacionDescripcion = 'Compras';
    } else {
        $clasificacionDescripcion = $consulta->descripcion_clasificacion;
    }

    // Si necesitas validar
    $validar2 = $this->validateProveedor($callback['recepciones'], $recepcion->cedula_emisor, $clasificacionDescripcion);
                if ($validar2 > 0 ) {
                    continue;
                }
                //inicio de proveedores
                $results['proveedor'] = $proveedor->nombre;
                $results['identificacion'] = $proveedor->num_id;
                $results['clasificacion'] = $clasificacionDescripcion;
                $results['moneda'] = $recepcion->moneda;
                $recepcion_proveedor = DB::table('receptor')->select('receptor.*')
                ->where([
                    ['receptor.fecha_xml_envio', '>=', $data_array['desde']],
                    ['receptor.fecha_xml_envio', '<=', $data_array['hasta']],
                    ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],
                    ['receptor.pendiente', '=', 1],
                    ['receptor.estatus_hacienda', '=', 'aceptado'],
                    ['receptor.idcodigoactv', '=', $data_array['actividad']],
                    ['receptor.cedula_emisor', '=', $proveedor->num_id],
                    //['receptor.clasifica_d151', '=', $clasificacion->tipo_clasificacion],
                    ['receptor.tipo_documento', '=', 05]

                ])->get();
                
               // dd($recepcion->version);
                
                if($recepcion->version === '4.4'){
                 $codigo_tarifa='CodigoTarifaIVA';
             }else{
                 $codigo_tarifa='CodigoTarifa'; 
             }
             
             
                if(count($recepcion_proveedor) == 0){
                    continue;
                }

                $imp_0 = 0;
                $mto_0 = 0;
                $imp_2 = 0;
                $mto_2 = 0;
                $imp_09 = 0;
                $mto_09 = 0;
                $imp_3 = 0;
                $mto_3 = 0;
                $imp_4 = 0;
                $mto_4 = 0;
                $imp_5 = 0;
                $mto_5 = 0;
                $imp_6 = 0;
                $mto_6 = 0;
                $imp_7 = 0;
                $mto_7 = 0;
                $imp_13 = 0;
                $mto_13 = 0;
                $imp_otros = 0;
                $mto_otros = 0;
                $iva_devuelto = 0;
                $otros_cargos=0;
                $otros_cargo=0;
                $moneda =0;
                $tcd=0;
                $mto_imp_neto = 0;
                $mto_imp_exonerado = 0;
                $imp_no_sujetos4 =0;
                $mto_no_sujetos4=0;
                $imp_no_sujetos401 =0;
                $mto_no_sujetos401=0;
                foreach ($recepcion_proveedor as $rec_pro) {

                $strContents = file_get_contents($rec_pro->ruta_carga);
                $strDatas = $this->Xml2Array($strContents);

                switch ($rec_pro->tipo_documento_recibido) {
                    case '01':
                        $nombre_doc = 'Fáctura Electrónica';
                        $documento = 'FacturaElectronica';
                        $simbolo = 1;
                    break;
                    case '02':
                        $nombre_doc = 'Nota de Débito Electrónica';
                        $documento = 'NotaDebitoElectronica';
                        $simbolo = 1;
                    break;
                    case '03':
                        $nombre_doc = 'Nota de Crédito Electrónica';
                        $documento = 'NotaCreditoElectronica';
                        $simbolo = 0;
                    break;

                }

                  if(isset($strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'])){
                  $mod=$strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'];
                  }else{
                  $mod='CRC';
                  }

                if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {

                    if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'])) {

                        if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'])) {

                            for ($i=0; $i < count($strDatas[$documento]['DetalleServicio']['LineaDetalle']); $i++) {

                                if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'])) {

                                    if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'])) {

                                        for ($im=0; $im < count($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']); $im++) {

                                            if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im][$codigo_tarifa])) {

                                                switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im][$codigo_tarifa]) {
                                                    case '10':
                                                      

                                                         if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_0 = $imp_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_0 = $mto_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_0 = $imp_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_0 = $mto_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                    break;
                                                    case '02':
                                                       
                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_2 = $imp_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_2 = $mto_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_2 = $imp_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_2 = $mto_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                    break;
                                                    case '03':
                                                      
                                                         if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_3 = $imp_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_3 = $mto_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_3 = $imp_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_3 = $mto_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                    break;
                                                    case '04':
                                                      

                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_4 = $imp_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_4 = $mto_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_4 = $imp_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_4 = $mto_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                    break;
                                                    case '05':
                                                      

                                                         if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_5 = $imp_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_5 = $mto_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_5 = $imp_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_5 = $mto_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                    break;
                                                    case '06':
                                                       

                                                         if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_6 = $imp_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_6 = $mto_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_6 = $imp_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_6 = $mto_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                    break;
                                                    case '07':
                                                       

                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_7 = $imp_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_7 = $mto_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_7 = $imp_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_7 = $mto_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                    break;
                                                    case '08':

                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_13 = $imp_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_13 = $mto_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                            $imp_13 = $imp_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_13 = $mto_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                    break;
                                                    case '09':
                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_09 = $imp_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_09 = $mto_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_09 = $imp_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_09 = $mto_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                    break;
                                                    case '01':
                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos401 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_no_sujetos401 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_no_sujetos401 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_no_sujetos401 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                    break;
                                                    case '11':
                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos4 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_no_sujetos4 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_no_sujetos4 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_no_sujetos4 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                    break;
                                                }
                                            } else {

                                               

                                                 if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_otros = $imp_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_otros = $imp_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            }
                                        }
                                    } else {

                                        if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$codigo_tarifa])) {

                                            switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$codigo_tarifa]) {

                                                case '10':
                                                    if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_0 = $imp_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_0 = $mto_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_0 = $imp_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_0 = $mto_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                break;
                                                case '02':
                                                     if ($simbolo > 0 ) {

                                                            if( $mod=='CRC'){
                                                            $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_2 = $imp_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_2 = $mto_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_2 = $imp_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_2 = $mto_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                break;
                                                case '03':
                                                    if ($simbolo > 0 ) {

                                                            if( $mod=='CRC'){
                                                            $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_3 = $imp_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_3 = $mto_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_3 = $imp_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_3 = $mto_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                break;
                                                case '04':
                                                    if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_4 = $imp_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_4 = $mto_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_4 = $imp_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_4 = $mto_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                break;
                                                case '05':
                                                     if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_5 = $imp_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_5 = $mto_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_5 = $imp_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_5 = $mto_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                break;
                                                case '06':
                                                    if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_6 = $imp_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_6 = $mto_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_6 = $imp_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_6 = $mto_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                break;
                                                case '07':
                                                   if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_7 = $imp_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_7 = $mto_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_7 = $imp_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_7 = $mto_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                break;
                                                case '08':

                                                        if ($simbolo > 0 ) {

                                                            if( $mod=='CRC'){
                                                            $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_13 = $imp_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_13 = $mto_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_13 = $imp_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_13 = $mto_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                break;
                                                case '09':

                                                         if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_09 = $imp_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_09 = $mto_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_09 = $imp_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_09 = $mto_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                    break;
                                                    case '01':
                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos401 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_no_sujetos401 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_no_sujetos401 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_no_sujetos401 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                    break;
                                                    case '11':
                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos4 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_no_sujetos4 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_no_sujetos4 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_no_sujetos4 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                    break;
                                            }
                                            // meter la validacion de la exoneracion 21-04 LDCG
                                            if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Exoneracion'])) {
                                                if ($simbolo > 0) {

                                                    $mto_imp_neto += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['ImpuestoNeto'];
                                                    $mto_imp_exonerado += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Exoneracion']['MontoExoneracion'];
                                                } else {
                                                    $mto_imp_neto = $mto_imp_neto - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['ImpuestoNeto'];
                                                    $mto_imp_exonerado = $mto_imp_exonerado - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Exoneracion']['MontoExoneracion'];

                                                }
                                            }
                                        } else {
                                           
                                           if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_otros = $imp_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];

                                                            $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_otros = $imp_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                             }
                                                        }

                                        }

                                    }
                                } else {
                                    if ($simbolo > 0) {

                                         if( $mod=='CRC'){

                                       
                                        $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];

                                           }else{
                                             $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal']    * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                           }
                                    } else {
                                       if( $mod=='CRC'){
                                      
                                        $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                       }else{
                                            $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                       }
                                    }
                                }
                            }
                        }
                    } else {

                        if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'])) {

                            if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'])) {

                                for ($im=0; $im < count($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']); $im++) {

                                    if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im][$codigo_tarifa])) {

                                        switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im][$codigo_tarifa]) {
                                            case '10':
                                               if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_0 = $imp_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_0 = $mto_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_0 = $imp_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_0 = $mto_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }


                                            break;
                                            case '02':
                                               if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_2 = $imp_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_2 = $mto_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_2 = $imp_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_2 = $mto_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            break;
                                            case '03':
                                               if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_3 = $imp_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_3 = $mto_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_3 = $imp_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_3 = $mto_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }


                                            break;
                                            case '04':
                                                if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_4 = $imp_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_4 = $mto_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_4 = $imp_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_4 = $mto_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            break;
                                            case '05':
                                                if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_5 = $imp_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_5 = $mto_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_5 = $imp_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_5 = $mto_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            break;
                                            case '06':
                                               if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_6 = $imp_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_6 = $mto_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_6 = $imp_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_6 = $mto_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            break;
                                            case '07':
                                                if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_7 = $imp_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_7 = $mto_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_7 = $imp_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_7 = $mto_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                            break;
                                           case '08':

                                                        if ($simbolo > 0 ) {

                                                            if( $mod=='CRC'){
                                                            $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im]['Monto'];

                                                            $mto_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                           } else{
                                                                 $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                              if( $mod=='CRC'){
                                                            $imp_13 = $imp_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_13 = $mto_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_13 = $imp_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_13 = $mto_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            break;
                                            case '09':

                                                         if ($simbolo > 0 ) {

                                                             if( $mod=='CRC'){
                                                            $imp_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                            if( $mod=='CRC'){
                                                            $imp_09 = $imp_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_09 = $mto_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_09 = $imp_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_09 = $mto_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                                    break;
                                                   case '01':
                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos401 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_no_sujetos401 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_no_sujetos401 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_no_sujetos401 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                    break;
                                                    case '11':
                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos4 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_no_sujetos4 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_no_sujetos4 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_no_sujetos4 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                    break;
                                        }
                                        // meter la validacion de la exoneracion 21-04 LDCG
                                        if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im]['Exoneracion'])) {
                                            if ($simbolo > 0) {

                                                $mto_imp_neto += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['ImpuestoNeto'];
                                                $mto_imp_exonerado += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im]['Exoneracion']['MontoExoneracion'];
                                            } else {
                                                $mto_imp_neto = $mto_imp_neto - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['ImpuestoNeto'];
                                                $mto_imp_exonerado = $mto_imp_exonerado - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im]['Exoneracion']['MontoExoneracion'];

                                            }
                                        }
                                    } else {

                                        if ($simbolo > 0) {

                                            $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im]['Monto'];
                                           
                                        } else {

                                            $imp_otros = $imp_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im]['Monto'];
                                           
                                        }

                                    }
                                }

                            } else {

                                if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$codigo_tarifa])) {

                                    switch ($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$codigo_tarifa]) {
                                            case '10':
                                              if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                           } else{
                                                                 $imp_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_0 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_0 = $imp_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_0 = $mto_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                             }else{
                                                                  $imp_0 = $imp_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_0 = $mto_0 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }


                                            break;
                                            case '02':
                                               if ($simbolo > 0 ) {

                                                           if( $mod=='CRC'){
                                                            $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                           } else{
                                                                 $imp_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_2 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                            if( $mod=='CRC'){
                                                            $imp_2 = $imp_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_2 = $mto_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                             }else{
                                                                  $imp_2 = $imp_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_2 = $mto_2 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            break;
                                            case '03':
                                               if ($simbolo > 0 ) {

                                                            if( $mod=='CRC'){
                                                            $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                           } else{
                                                                 $imp_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_3 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_3 = $imp_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_3 = $mto_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                             }else{
                                                                  $imp_3 = $imp_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_3 = $mto_3 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }


                                            break;
                                            case '04':
                                                if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                           } else{
                                                                 $imp_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_4 = $imp_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_4 = $mto_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                             }else{
                                                                  $imp_4 = $imp_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_4 = $mto_4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            break;
                                            case '05':
                                                if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                           } else{
                                                                 $imp_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_5 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_5 = $imp_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_5 = $mto_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_5 = $imp_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_5 = $mto_5 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            break;
                                            case '06':
                                              if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_6 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_6 = $imp_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_6 = $mto_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_6 = $imp_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_6 = $mto_6 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            break;
                                            case '07':
                                               if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_7 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_7 = $imp_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_7 = $mto_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_7 = $imp_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_7 = $mto_7 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            break;
                                            case '08':

                                                        if ($simbolo > 0 ) {

                                                          if( $mod=='CRC'){
                                                            $imp_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                           } else{
                                                            $imp_13 += ($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto']) * ($strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio']);

                                                            $mto_13 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $mod=='CRC'){
                                                            $imp_13 = $imp_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_13 = $mto_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                             }else{
                                                                  $imp_13 = $imp_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_13 = $mto_13 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }

                                            break;
                                            case '09':

                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                           } else{
                                                                 $imp_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_09 += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_09 = $imp_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'];

                                                            $mto_09 = $mto_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                                             }else{
                                                                  $imp_09 = $imp_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_09 = $mto_09 - $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                             }
                                                        }

                                                    break;
                                                    case '01':
                                                         if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                           } else{
                                                                 $imp_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_no_sujetos401 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos401 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_no_sujetos401 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                             }else{
                                                                  $imp_no_sujetos401 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_no_sujetos401 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                    break;
                                                    case '11':
                                                        if ($simbolo > 0 ) {

                                                            if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                           } else{
                                                                 $imp_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];

                                                            $mto_no_sujetos4 += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];
                                                            }
                                                        } else {
                                                             if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                                                            $imp_no_sujetos4 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];

                                                            $mto_no_sujetos4 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                                             }else{
                                                                  $imp_no_sujetos4 = $imp_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im]['Monto'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;

                                                            $mto_no_sujetos4 = $mto_no_sujetos4 - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'];;
                                                             }
                                                        }
                                                    break;
                                    }
                                    // meter la validacion de la exoneracion 21-04 LDCG
                                    if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Exoneracion'])) {
                                        if ($simbolo > 0) {

                                            $mto_imp_neto += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['ImpuestoNeto'];
                                            $mto_imp_exonerado += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Exoneracion']['MontoExoneracion'];
                                        } else {
                                            $mto_imp_neto = $mto_imp_neto - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['ImpuestoNeto'];
                                            $mto_imp_exonerado = $mto_imp_exonerado - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Exoneracion']['MontoExoneracion'];

                                        }
                                    }
                                } else {

                                    if ($simbolo > 0) {

                                        $imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];
                                        $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                    } else {

                                        $imp_otros = $imp_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];
                                        $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                    }

                                }
                            }
                        } else {
                            if ($simbolo > 0) {

                               
                               if( $mod=='CRC'){

                                
                                $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                                 }else{
                                      $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ;
                                 }
                            } else {

                               
                                 if( $mod=='CRC'){
                           
                            $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                          }else{
                             $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal']  * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ;
                          }
                            }
                        }
                    }
                } else {
                    if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'])) {
                         if (isset($strDatas[$documento]['DetalleServicio'])) {///condicion del  14-09-2023 revento porque un xml no traia detalle servicio grace murillo radiografica
                        for ($i=0; $i < count($strDatas[$documento]['DetalleServicio']['LineaDetalle']); $i++) {
                            if ($simbolo > 0) {

                                 if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){

                              
                                $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                                 }else{
                                      $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'] * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ;
                                 }
                            } else {

                                if( $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']=='CRC'){
                           
                            $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                          }else{
                             $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal']  * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ;
                          }
                            }
                        }
                         }
                    } else {
                        if ($simbolo > 0) {
                           if( $mod=='CRC'){
                            //$imp_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im]['Monto'];
                            $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                            }else{
                                 $mto_otros += $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal']* $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ;
                            }
                        } else {
                          if( $mod=='CRC'){
                            //$imp_otros = $imp_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'][$im]['Monto'];
                            $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];
                          }else{
                             $mto_otros = $mto_otros - $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal']  * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ;
                          }
                        }
                    }
                }
                 $moneda =$strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda']?? 0.00000;

                 $tcd =$strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio']?? 0.00000;
                $iva_devuelto += $strDatas[$documento]['ResumenFactura']['TotalIVADevuelto'] ?? 0.00000;
                
                
                 if (isset($strDatas[$documento]['ResumenFactura']['TotalOtrosCargos'])) {
                   if ($simbolo > 0) {
                if( $mod=='CRC'){
                 $otros_cargos += $strDatas[$documento]['ResumenFactura']['TotalOtrosCargos'] ?? 0.00000;
                 }else{
                     $otros_cargos   += $strDatas[$documento]['ResumenFactura']['TotalOtrosCargos']  * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ;
                 }
                }else{
                  if( $mod=='CRC'){
                 $otros_cargos -= $strDatas[$documento]['ResumenFactura']['TotalOtrosCargos'] ?? 0.00000;
                 }else{
                     $otros_cargos   -= $strDatas[$documento]['ResumenFactura']['TotalOtrosCargos']  * $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ;
                 }  
                }
                 }else{
                     $otros_cargos=0;
                 }
                
                }
                $results['moneda'] =$moneda;
                $results['10']['monto'] = $mto_0;
                $t_mto_0 += $mto_0;
                $results['10']['iva'] = $imp_0;
                $t_imp_0 += $imp_0;
                $results['01']['monto'] = $mto_no_sujetos401; // Acumular el monto para '10'  
                $results['11']['monto'] = $mto_no_sujetos4; // Acumular el monto para '11'  

$results['01']['iva'] = $imp_no_sujetos401; // Acumular el IVA para '10'  
$results['11']['iva'] = $imp_no_sujetos4; // Acumular el IVA para '11'  
$timp_no_sujetos4 += $imp_no_sujetos4; // Acumulación total del IVA  
$timp_no_sujetos401 += $imp_no_sujetos401;               
$tmto_no_sujetos4 += $mto_no_sujetos4; // Acumulación total del monto  
$tmto_no_sujetos401 += $mto_no_sujetos401;              
                
                
                $results['02']['monto'] = $mto_2;
                $t_mto_2 += $mto_2;
                $results['02']['iva'] = $imp_2;
                $t_imp_2 += $imp_2;

                $results['09']['monto'] = $mto_09;
                $t_mto_09 += $mto_09;
                $results['09']['iva'] = $imp_09;
                $t_imp_09 += $imp_09;

                $results['03']['monto'] = $mto_3;
                $t_mto_3 += $mto_3;
                $results['03']['iva'] = $imp_3;
                $t_imp_3 += $imp_3;
                $results['04']['monto'] = $mto_4;
                $t_mto_4 += $mto_4;
                $results['04']['iva'] = $imp_4;
                $t_imp_4 += $imp_4;
                $results['05']['monto'] = $mto_5;
                $t_mto_5 += $mto_5;
                $results['05']['iva'] = $imp_5;
                $t_imp_5 += $imp_5;
                $results['06']['monto'] = $mto_6;
                $t_mto_6 += $mto_6;
                $results['06']['iva'] = $imp_6;
                $t_imp_6 += $imp_6;
                $results['07']['monto'] = $mto_7;
                $t_mto_7 += $mto_7;
                $results['07']['iva'] = $imp_7;
                $t_imp_7 += $imp_7;
                $results['13']['monto'] = $mto_13;
                $t_mto_13 += $mto_13;
                $results['13']['iva'] = $imp_13;
                $t_imp_13 += $imp_13;
                // parte de resultados
                $results['no_sujeto']['monto'] = $mto_otros;
                $t_mto_otros += $mto_otros;
                $results['no_sujeto']['iva'] = $imp_otros;
                $t_imp_otros += $imp_otros;
                $results['subtotal_neto'] = $mto_0 + $mto_2 + $mto_09 + $mto_3 + $mto_4 + $mto_5 + $mto_6 + $mto_7 + $mto_13 + $mto_otros + $mto_no_sujetos4 + $mto_no_sujetos401 ;
                $t_subtotal_neto += $results['subtotal_neto'];
                $results['subtotal_iva'] = $imp_0 + $imp_2 + $imp_09 + $imp_3 + $imp_4 + $imp_5 + $imp_6 + $imp_7 + $imp_13 + $imp_otros + $imp_no_sujetos4 + $imp_no_sujetos401 ;
                $t_subtotal_iva += $results['subtotal_iva'];
                $results['iva_devuelto'] = $iva_devuelto;
                $t_iva_devuelto += $iva_devuelto;

                 $results['otros_cargos'] = $otros_cargos;
                $t_otros_cargos += $otros_cargos;
                // agrego opciones exoneradas iva y neto
                $results['exonerado_iva'] = $mto_imp_exonerado;
                $t_exonerado_iva += $mto_imp_exonerado;

                $results['exonerado_neto'] = $mto_imp_neto;
                $t_exonerado_neto += $mto_imp_neto;

                $results['total_iva'] = ($results['subtotal_iva'] - $results['exonerado_iva']) - $results['iva_devuelto'];
                $t_total_iva += $results['total_iva'];
                $results['total'] = $results['subtotal_neto'] + $results['total_iva'] + $results['otros_cargos'];
                $t_total += $results['total'];
                 //
                    //$results['tc'] = $tcd; comentado el 05-06-2024 para ver error con el tema de caonversion
                     $results['tc'] = 1;
                  // $tcd += $tcd;

                array_push($callback['recepciones'], $results);
            }

        $callback['totales'] = [
            't_imp_0' => $t_imp_0,
            't_mto_0' => $t_mto_0,
            't_imp_2' => $t_imp_2,
            't_mto_2' => $t_mto_2,
            
            't_imp_11' => $timp_no_sujetos4,
            't_mto_11' => $tmto_no_sujetos4,
            't_imp_01' => $timp_no_sujetos401,
            't_mto_01' => $tmto_no_sujetos401,
            
            
            't_imp_09' => $t_imp_09,
            't_mto_09' => $t_mto_09,
            't_mto_3' => $t_mto_3,
            't_imp_3' => $t_imp_3,
            't_mto_4' => $t_mto_4,
            't_imp_4' => $t_imp_4,
            't_mto_5' => $t_mto_5,
            't_imp_5' => $t_imp_5,
            't_mto_6' => $t_mto_6,
            't_imp_6' => $t_imp_6,
            't_mto_7' => $t_mto_7,
            't_imp_7' => $t_imp_7,
            't_mto_13' => $t_mto_13,
            't_imp_13' => $t_imp_13,
            't_mto_otros' => $t_mto_otros,
            't_imp_otros' => $t_imp_otros,
            't_mto_oc' => $t_otros_cargos,
            't_iva_devuelto' => $t_iva_devuelto,
            't_subtotal_neto' => $t_subtotal_neto,
            't_subtotal_iva' => $t_subtotal_iva,
            't_exonerado' => $t_exonerado_iva,
            't_total_iva' => $t_total_iva,
            't_total' => $t_total,

        ];
        return $callback;
    }

        public function validateProveedor($array, $num_id, $clasificacion)
    {

        if (count($array) > 0) {

            foreach ($array as $recepcion) {

                if ($recepcion['identificacion'] == $num_id) {

                    if ($recepcion['clasificacion'] == $clasificacion) {
                        return 1;
                    }
                } else {

                    continue;
                }
            }
        } else {
            return 0;
        }
    }
}
