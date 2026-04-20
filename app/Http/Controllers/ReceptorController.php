<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Cliente;
use App\Sales;
use App\Sales_item;
use App\Consecutivos;
use App\Facelectron;
use App\Receptor;
use App\Actividad;
use DB;
use Input;
use App\Http\Requests\ReceptorRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Cajas;
use Redirect;
use Carbon\Carbon;
use App\Clasificaciones;
use App\Jobs\RecepcionAutomatica;
use Artisan;
use App\Http\Controllers\CronController;

class ReceptorController extends Controller
{

        public function index(Receptor $model)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        //$receptor = Receptor::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-t');
        $receptor = Receptor::where([
           
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_xml_envio', '>=', $fecha_desde],
            ['fecha_xml_envio', '<=', $fecha_hasta]
        ])->orderBy('idreceptor', 'desc')->get();
        $new_recepcion = Receptor::where('numero_documento_receptor', '=', '9999999999')->where('idconfigfact', '=', Auth::user()->idconfigfact)->count();
        $clasificaciones = Clasificaciones::where('estatus', '=', 1)->get();
        // validacion para ejecutar la consulta dinamica
        $qry_status = Receptor::where('xml_respuesta', '=', NULL)->where('idconfigfact', '=', Auth::user()->idconfigfact)->count();
        if ($qry_status > 0) {

            app('App\Http\Controllers\PeticionesController')->ajaxConsultarReceptor();
        }
    	return view('receptor.index', ['sales' => $receptor, 'new_recepcion' => $new_recepcion, 'clasificaciones' => $clasificaciones]);
    }

        public function create()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $cajas = Cajas::where([
            ['estatus', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        return view('receptor.create', ['cajas'  => $cajas]);
    }

        public function store(ReceptorRequest $request)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
        include_once(public_path(). '/funcionFacturacion506.php');
        foreach($request->file('cargar_documento') as $doc){
            try {

                $destinationPath = public_path().'/receptor/'.Auth::user()->idconfigfact.'/carga';
                $filename = time(). $doc->getClientOriginalName();
                
                $doc->move($destinationPath, $filename);
                $strContents = file_get_contents($destinationPath.'/'.$filename);
                $strDatas = $this->Xml2Array($strContents);
                 
                  // Obtener la versión del XML  
$namespace = $strDatas['FacturaElectronica_attr']['xmlns']   
    ?? $strDatas['NotaCreditoElectronica_attr']['xmlns']   
    ?? $strDatas['FacturaElectronicaCompra_attr']['xmlns']   
    ?? $strDatas['NotaDebitoElectronica_attr']['xmlns']   
    ?? null;  

if ($namespace) {  
    preg_match('/v(\d+\.\d+)/', $namespace, $matches);  
    $version = isset($matches[1]) ? $matches[1] : null;  
} else{
    $version = '4.0'; 
}     
// Obtener la versión del XML 
                switch ($datos['tipo_documento']) {

                    case '01':
                        $documento = 'FacturaElectronica';
                        if (!isset($strDatas[$documento])) {
                            return Redirect::back()->withErrors(['cargar_documento' => ['Selecciono Factura y el XML cargado no Corresponde']]);
                        }
                    break;
                    case '02':
                    $documento = 'NotaDebitoElectronica';
                        if (!isset($strDatas[$documento])) {
                            return Redirect::back()->withErrors(['cargar_documento' => ['Selecciono Nota de Debito y el XML cargado no Corresponde']]);
                        }
                    break;
                    case '03':
                    $documento = 'NotaCreditoElectronica';
                        if (!isset($strDatas[$documento])) {
                            return Redirect::back()->withErrors(['cargar_documento' => ['Selecciono Nota de Credito y el XML cargado no Corresponde']]);
                        }
                    break;

                }
                
                 if($version == '4.4'){
                 $codigo_actividad= $strDatas[$documento]['CodigoActividadEmisor'];
                 }else{
                 $codigo_actividad= $strDatas[$documento]['CodigoActividad'];
                 }

                $seguridad = $this->armarSeguridad($datos['idconfigfact']);
                $configuracion = Configuracion::find($datos['idconfigfact']);
                $actividad = Actividad::find($datos['actividad']);
                $consulta = Receptor::where('cedula_emisor', '=', ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'])
                ->where('consecutivo_doc_receptor', '=', ''.$strDatas[$documento]['NumeroConsecutivo'])
                ->where('estatus_hacienda', '=', 'aceptado')
                ->count();

                if ($consulta == 0) {

                    if($configuracion->numero_id_emisor.'' === ''.$strDatas[$documento]['Receptor']['Identificacion']['Numero']){

                        switch ($datos['procesar_doc']) {

                            case '05':
                                $mensaje = 1;
                            break;
                            case '06':
                                $mensaje = 2;
                            break;
                            case '07':
                                $mensaje = 3;
                            break;
                        }

                        $punto_venta = substr($strDatas[$documento]['NumeroConsecutivo'], 3, 5);
                        $sucursal = substr($strDatas[$documento]['NumeroConsecutivo'], 0, 3);
                        if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {

                            $total_impuesto =  $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];

                        } else {

                            $total_impuesto = '0.00000';

                        }
                        switch ($datos['condicion_impuesto']) {

                            case '0':
                                if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {

                                    $imp_a_acreditar = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                                    $gasto_aplicable = '0.00000';
                                } else {

                                    $imp_a_acreditar = '0.00000';
                                    $gasto_aplicable = '0.00000';
                                }
                            break;
                            case '01':

                                $imp_a_acreditar = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                                $gasto_aplicable = '0.00000';
                            break;
                            case '02':

                                $imp_a_acreditar = '0.00000';
                                $gasto_aplicable = '0.00000';
                            break;
                            case '03':

                                $imp = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                                $imp_a_acreditar = ($imp * $configuracion->factor_receptor)/100;
                                $gasto_aplicable = '0.00000';
                            break;
                            case '04':

                                $imp_a_acreditar = '0.00000';
                                $gasto_aplicable = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                            break;
                            case '05':
                                $imp = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                                $imp_a_acreditar = ($imp * $configuracion->factor_receptor)/100;
                                $gasto_aplicable = $imp - $imp_a_acreditar;
                            break;
                        }
                        
                        
                        ///inici
                                    ///NUEVO CNSECUTIVO 03-07-2025
             //nuevo mandejo de consecutivos
             // Paso 1: Obtener el rango de números de documento  
$consecutivo = DB::table('consecutivos')->where('idcaja', $datos['idcaja'])  
    ->where('tipo_documento', $datos['procesar_doc'])  
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
    ->where('idcaja', $datos['idcaja'])  
    ->where('tipo_documento', $datos['procesar_doc'])  
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
//dd($huecos);
// Resultado  
if (empty($huecos)) {  
               $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $datos['idcaja']],
                ['tipo_documento', '=', $datos['procesar_doc']],
            ])->get();
            $num_receptor = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        
          $consecutivo_fac = DB::table('receptor')->where([  
    ['idcaja', '=', $datos['idcaja']],  
    ['tipo_documento', '=', $datos['procesar_doc']],  
    ['numero_documento_receptor', '=', $num_receptor],  
    
])->get(); 

 //dd($consecutivo_fac);  
if ($consecutivo_fac->isEmpty()) {  
                $new = $num_receptor + 1;
             
                $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['procesar_doc'].' and idcaja = '.$datos['idcaja']);
                
                
}else{
    
    $new = $num_receptor +1;
    $num_receptor=str_pad($new, 10, "0", STR_PAD_LEFT);;
    $new=$num_receptor+1;
    
    $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['procesar_doc'].' and idcaja = '.$datos['idcaja']);
        
    //Session::flash('message', "Hay un problema con el consecutivo de la factura, SOLO SE PUEDE FACTURAR CON UNA PANTALLA ACTIVA, ve a VER DOC ELEC y LIMPA FACTURA EN PROCESO o Elimina alguna de los Documentos duplicados para continuar");  
    //return redirect()->back(); 
    // }
}
} else {  
    
   
    $num_receptor=$huecos[0];
 
    }       
                        
              // dd($num_receptor);         
                        
                        ///termina
                        
                        
                       // $num_receptor = DB::table('consecutivos')->where([
                        //    ['idcaja', '=', $datos['idcaja']],['tipo_documento', '=',  $datos['procesar_doc']],
                       // ])->value('numero_documento');
                        
                        //$num_receptor = str_pad($num_receptor, 10, "0", STR_PAD_LEFT);

                        // consulta de clasificacion por proveedor
                        $cliente = Cliente::where('num_id', '=', ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'])->where('idconfigfact', '=',$datos['idconfigfact'])->first();
                        if (empty($cliente)) {

                            $cron = new CronController();
                            $respuesta = $cron->registrarProveedor($datos['idconfigfact'] ,$strDatas[$documento]);
                            $cliente = Cliente::where('num_id', '=', ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'])->where('idconfigfact', '=',$datos['idconfigfact'])->first();
                        }

                        $array_data = [
                            'idcliente' => $cliente->idcliente,
                            'codigo_actividad' => ''.$codigo_actividad
                        ];
                   $clasificacion = $this->getClasifica($array_data);  


                $clasificacion_proveedor = 8; // Valor por defecto  

                if (isset($clasificacion['clasificacion_proveedor'])) {  
                if (count($clasificacion['clasificacion_proveedor']) > 0) {  
                 $clasificacion_proveedor = $clasificacion['clasificacion_proveedor'][0]->tipo_clasificacion;  
                 }  
                }  

  
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
                                       // dd($codigo_actividad);
                        $receptor = Receptor::create([
                            'idconfigfact' => $datos['idconfigfact'],
                            'idcaja' => $datos['idcaja'],
                            'idcodigoactv' => ''.$actividad->idcodigoactv,
                            'tipo_documento' => $datos['procesar_doc'],
                            'detalle_mensaje' => $datos['detalle_mensaje'],
                            //'ruta_carga' => $destinationPath.'/'.$filename,
                            'ruta_carga' => './receptor/'.$datos['idconfigfact'].'/carga/'.$filename,
                            'fecha' => date('Y-m-d'),
                            'nombre_emisor' => $strDatas[$documento]['Emisor']['Nombre'],
                            'total_impuesto' => $total_impuesto,
                            'total_comprobante' => $strDatas[$documento]['ResumenFactura']['TotalComprobante'],
                            'cedula_emisor' => $strDatas[$documento]['Emisor']['Identificacion']['Numero'],
                            'clasifica_d151' => $clasificacion_proveedor,
                            'condicion_impuesto' => $datos['condicion_impuesto'],
                            'imp_creditar' => $datos['imp_creditar'],
                            'gasto_aplica' => $datos['gasto_aplica'],
                            'hacienda_imp_creditar' => $imp_a_acreditar,
                            'hacienda_gasto_aplica' => $gasto_aplicable,
                            'tipo_documento_recibido' => $datos['tipo_documento'],
                            'fecha_xml_envio' => $strDatas[$documento]['FechaEmision'],
                            'numero_documento_receptor' => $num_receptor,
                            'consecutivo_doc_receptor' => ''.$strDatas[$documento]['NumeroConsecutivo'],
                            'moneda' => $moneda,
                            'tc' => $tc,
                            'version' => $version,
                            'codigo_act_xml' => $codigo_actividad,
                        ]);
                        $xml = [
                            'tipoDocumento' => ''.$datos['procesar_doc'], // 05 Aceptado 06 Parcialmente aceptado 07 rechazado
                            'sucursal' => ''.$sucursal, // 3 digitos sucursal de donde proviene el documento para el armado de clave
                            'puntoVenta' => ''.$punto_venta, // 5 digitos punto de venta del cual se armo el documento
                            'idreceptor' => ''.$receptor->idreceptor,
                            'idconfigfact' => ''.$datos['idconfigfact'],
                            'comando' => 0,
                            'numeroFactura' => ''.$num_receptor, //correspondiente al numero del documento para el receptor
                            'fechaEmision' => date('c'), // fecha de emision del documento no mayor a 72 horas, date_default_timezone_set debe ser costa rica
                            'rutaxml' => ''.$destinationPath.'/'.$filename, // ruta del xml para la factura es el XML recibido por el emisor
                            'CondicionImpuesto' => ''.$datos['condicion_impuesto'],
                            'MontoTotalImpuestoAcreditar' => ''.$imp_a_acreditar,
                            'MontoTotalDeGastoAplicable' => ''.$gasto_aplicable,
                            'Emisor' => array(
                                'NumeroCedulaEmisor' => ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'], // cedula del emisor del documento
                                'Mensaje' => ''.$mensaje, // 1 para cuando el mensaje es aceptado, o en su defecto igual a 05, 2 para 06 y 3 para 07
                                'CodigoActividad' => ''.$actividad->codigo_actividad,
                                'MontoTotalImpuesto' => ''.$total_impuesto, // Monto total del impuesto de la factura
                                'TotalFactura' => ''.$strDatas[$documento]['ResumenFactura']['TotalComprobante'], // Monto total de la factura
                                'DetalleMensaje' => ''.$datos['detalle_mensaje'] // Alguna nota o detalle que queramos agregar para el Mensaje Receptor
                            )
                        ];
                        $facturar = Timbrar_receptor($xml, $seguridad);
                        $num_receptor = $num_receptor+1 ;
                        //$consecutivo = DB::update('update consecutivos set numero_documento = '.$num_receptor.' where tipo_documento = '.$datos['procesar_doc'].' and idcaja = '.$datos['idcaja']);
                        $message = 'Facturas Cargadas Correctamente.';
                    } else {

                        $message = 'Emisor no corresponde con el receptor de la factura.';
                    }
                } else {

                    $message = 'Emisor ya tiene registrado el documento como recibido.';
                }
            } catch (Exception $e) {

                return Redirect::back()->withErrors(['cargar_documento' => [''.$e->getMessage()]]);

            }
        }
        app('App\Http\Controllers\PeticionesController')->ajaxConsultarReceptor();
        return redirect()->route('receptor.index')->withStatus(__(''.$message));
    }
        public function armarSeguridad($idconfigfact)
    {
            $buscar = Configuracion::find($idconfigfact);
            if ($buscar->client_id === 1) {
               $entorno = 'api-prod';
            }else{
               $entorno = 'api-stag';
            }

            if(\Auth::check()){

                $seguridad =  [
                    'certificado' => ''.public_path().'/certificados/'.$buscar->ruta_certificado, // Ruta del certificado donde se encuentra
                    'clave_certificado' => ''.$buscar->clave_certificado, //clave del certificado .p12
                    'credenciales_conexion' => ''.$buscar->credenciales_conexion, //credenciales de Hacienda
                    'clave_conexion' => ''.$buscar->clave_conexion, //Contraseña de hacienda
                    'client_id' => $entorno //api-stag para pruebas y api-prod para el entorno produccion
                ];

            } else {

                $seguridad =  [
                    'certificado' => ''.public_path().'/certificados/'.$buscar->ruta_certificado, // Ruta del certificado donde se encuentra
                    'clave_certificado' => ''.$buscar->clave_certificado, //clave del certificado .p12
                    'credenciales_conexion' => ''.$buscar->credenciales_conexion, //credenciales de Hacienda
                    'clave_conexion' => ''.$buscar->clave_conexion, //Contraseña de hacienda
                    'client_id' => $entorno, //api-stag para pruebas y api-prod para el entorno produccion
                    'idconfigfact' => ''.$idconfigfact,
                ];
            }

            return $seguridad;
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

        public function haciendaReceptor(Request $request)
    {
        $input = $request->all();
        $sales = Receptor::find($input['idsales']);
        return response()->json(['success'=> $sales]);
    }

        public function filtrarReceptor(Request $request)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
          $receptor = Receptor::where([
           
            ['fecha_xml_envio', '>=', $datos['fecha_desde']],
            ['fecha_xml_envio', '<=', $datos['fecha_hasta']],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        $new_recepcion = Receptor::where('numero_documento_receptor', '=', '9999999999')->where('idconfigfact', '=', Auth::user()->idconfigfact)->count();
        $clasificaciones = Clasificaciones::where('estatus', '=', 1)->get();

        return view('receptor.index', ['sales' => $receptor, 'new_recepcion' => $new_recepcion, 'clasificaciones' => $clasificaciones]);
    }

        public function recepcionAutomatica(Receptor $model)
    {
        $receptor = DB::table('receptor')
        ->leftjoin('log_recepcion_automatica', 'receptor.idreceptor', '=', 'log_recepcion_automatica.idrecepcion')
        ->whereNotIn('receptor.idreceptor', DB::table('log_recepcion_automatica')->pluck('idrecepcion'))
        ->select('receptor.*')
        ->where('idconfigfact', '=', Auth::user()->idconfigfact)
        
       // ->where('receptor.numero_documento_receptor', '=', '9999999999')
        //->where('receptor.estatus_hacienda', '=', 'pendiente')
        ->where('receptor.pendiente', '=', 0)
        ->whereIn('receptor.tipo_documento_recibido', ["01","02","03"])
        ->get();
        
         
        
        $cajas = DB::table('caja_usuario')
        ->leftjoin('cajas', 'caja_usuario.idcaja', '=', 'cajas.idcaja')
        ->select('cajas.*')
        ->where('cajas.idconfigfact', '=', Auth::user()->idconfigfact)
        ->where('caja_usuario.estado', '=', 1)
        ->get();



        $cja = $cajas->count();
        if ($cja<1){

            Session::flash('message', "Asignar una caja para el Usuario que inicio Sesión");
            return redirect()->route('cajas.index');

        }
        return view('receptor.automatica', ['sales' => $receptor, 'cajas' => $cajas]);
    }

        public function infReceptor(Request $request)
    {
        $input = $request->all();

        $receptor = DB::table('receptor')
        ->select('receptor.*')
        ->where('idconfigfact', '=', Auth::user()->idconfigfact)
        ->where('receptor.idreceptor', '=', $input['idreceptor'])
        ->first();

        return response()->json(['success'=> $receptor]);
    }

        public function sendRecepcion(Request $request)
    {
        $datos = $request->all();
        if ($datos['is_masive'] > 0) {

            $receptor = Receptor::where('tipo_documento_recibido', '=', '01')
            ->where('numero_documento_receptor', '=', '9999999999')
            ->where('idconfigfact', '=', Auth::user()->idconfigfact)
            ->get();

            if (count($receptor) > 0) {

                $array_config_receptor = [
                    'config_receptor.idconfigfact' => $datos['idconfigfact'],
                    'config_receptor.estatus' => 'en espera',
                    'config_receptor.procesar_doc' => $datos['procesar_doc'],
                    'config_receptor.idcaja' => $datos['idcaja'],
                    'config_receptor.idcodigoactv' => $datos['actividad'],
                    'config_receptor.detalle_mensaje' => $datos['detalle_mensaje'],
                    'config_receptor.condicion_impuesto' => $datos['condicion_impuesto'],
                    'config_receptor.condicion_impuesto' => $datos['condicion_impuesto'],
                ];
                $idconfig_receptor = DB::table('config_receptor')->insertGetId($array_config_receptor);

                foreach ($receptor as $facturas) {

                    $array_log_recepcion_automatica = [
                        'log_recepcion_automatica.idrecepcion' => $facturas->idreceptor,
                        'log_recepcion_automatica.idconfig_receptor' => $idconfig_receptor,
                        'log_recepcion_automatica.estatus_recepcion' => 'por enviar'
                    ];
                    $log_recepcion_automatica = DB::table('log_recepcion_automatica')->insertGetId($array_log_recepcion_automatica);
                }
            } else {

                $receptor_nc = Receptor::where('tipo_documento_recibido', '=', '03')
                ->where('numero_documento_receptor', '=', '9999999999')
                ->where('idconfigfact', '=', Auth::user()->idconfigfact)
                ->get();

                if (count($receptor_nc) > 0) {

                    $array_config_receptor = [
                        'config_receptor.idconfigfact' => $datos['idconfigfact'],
                        'config_receptor.estatus' => 'en espera',
                        'config_receptor.procesar_doc' => $datos['procesar_doc'],
                        'config_receptor.idcaja' => $datos['idcaja'],
                        'config_receptor.idcodigoactv' => $datos['actividad'],
                        'config_receptor.detalle_mensaje' => $datos['detalle_mensaje'],
                        'config_receptor.condicion_impuesto' => $datos['condicion_impuesto'],
                        'config_receptor.condicion_impuesto' => $datos['condicion_impuesto'],
                    ];
                    $idconfig_receptor = DB::table('config_receptor')->insertGetId($array_config_receptor);

                    foreach ($receptor_nc as $notasc) {

                        $array_log_recepcion_automatica = [
                            'log_recepcion_automatica.idrecepcion' => $notasc->idreceptor,
                            'log_recepcion_automatica.idconfig_receptor' => $idconfig_receptor,
                            'log_recepcion_automatica.estatus_recepcion' => 'por enviar'
                        ];
                        $log_recepcion_automatica = DB::table('log_recepcion_automatica')->insertGetId($array_log_recepcion_automatica);
                    }
                } else {

                    $receptor_nd = Receptor::where('tipo_documento_recibido', '=', '02')
                    ->where('numero_documento_receptor', '=', '9999999999')
                    ->where('idconfigfact', '=', Auth::user()->idconfigfact)
                    ->get();
                    if (count($receptor_nd)) {

                        $array_config_receptor = [
                            'config_receptor.idconfigfact' => $datos['idconfigfact'],
                            'config_receptor.estatus' => 'en espera',
                            'config_receptor.procesar_doc' => $datos['procesar_doc'],
                            'config_receptor.idcaja' => $datos['idcaja'],
                            'config_receptor.idcodigoactv' => $datos['actividad'],
                            'config_receptor.detalle_mensaje' => $datos['detalle_mensaje'],
                            'config_receptor.condicion_impuesto' => $datos['condicion_impuesto'],
                            'config_receptor.condicion_impuesto' => $datos['condicion_impuesto'],
                        ];
                        $idconfig_receptor = DB::table('config_receptor')->insertGetId($array_config_receptor);
                        foreach ($receptor_nd as $notasd) {

                            $array_log_recepcion_automatica = [
                                'log_recepcion_automatica.idrecepcion' => $notasc->idreceptor,
                                'log_recepcion_automatica.idconfig_receptor' => $idconfig_receptor,
                                'log_recepcion_automatica.estatus_recepcion' => 'por enviar'
                            ];
                            $log_recepcion_automatica = DB::table('log_recepcion_automatica')->insertGetId($array_log_recepcion_automatica);
                        }
                    }
                }
            }

            return redirect()->route('receptor.index')->withStatus(__('Documentos Aceptados Correctamente'));
        } else {

            $inicio = $this->initRecepcion($datos);

            if ($inicio > 0) {

                app('App\Http\Controllers\PeticionesController')->ajaxConsultarReceptor();
                $message = 'Documentos Cargados Correctamente.';
                return redirect()->route('receptor.automatica')->withStatus(__(''.$message));
            } else {

                $message = 'Documentos no se pudieron Cargar.';
                return redirect()->route('receptor.automatica')->withStatus(__(''.$message));
            }

        }
    }

    public function initRecepcion($datos)
    {
        try {

            $receptor = Receptor::find($datos['idrecepcion']);
            include_once(public_path(). '/funcionFacturacion506.php');

            $strContents = file_get_contents($receptor->ruta_carga);
            $strDatas = $this->Xml2Array($strContents);
            // Obtener la versión del xml 
               $namespace = $strDatas['FacturaElectronica_attr']['xmlns'] ?? null;  
               if ($namespace) {  
               preg_match('/v(\d+\.\d+)/', $namespace, $matches);  
               $version = isset($matches[1]) ? $matches[1] : null;  
               } 
            switch ($receptor->tipo_documento_recibido)
            {
                case '01':
                    $documento = 'FacturaElectronica';
                    if (!isset($strDatas[$documento])) {

                        return Redirect::back()->withErrors(['cargar_documento' => ['Selecciono Factura y el XML cargado no Corresponde']]);
                    }
                break;
                case '02':
                    $documento = 'NotaDebitoElectronica';
                    if (!isset($strDatas[$documento])) {

                        return Redirect::back()->withErrors(['cargar_documento' => ['Selecciono Nota de Debito y el XML cargado no Corresponde']]);
                    }
                break;
                case '03':
                    $documento = 'NotaCreditoElectronica';
                    if (!isset($strDatas[$documento])) {

                        return Redirect::back()->withErrors(['cargar_documento' => ['Selecciono Nota de Credito y el XML cargado no Corresponde']]);
                    }
                break;
            }
 if($version == '4.4'){
                 $codigo_actividad= $strDatas[$documento]['CodigoActividadEmisor'];
                 }else{
                 $codigo_actividad= $strDatas[$documento]['CodigoActividad'];
                 }
            $seguridad = $this->armarSeguridad($datos['idconfigfact']);
            $configuracion = Configuracion::find($datos['idconfigfact']);
            $actividad = Actividad::find($datos['actividad']);

            switch ($datos['procesar_doc']) {

                case '05':
                    $mensaje = 1;
                break;
                case '06':
                    $mensaje = 2;
                break;
                case '07':
                    $mensaje = 3;
                break;
            }

            $punto_venta = substr($strDatas[$documento]['NumeroConsecutivo'], 3, 5);
            $sucursal = substr($strDatas[$documento]['NumeroConsecutivo'], 0, 3);

            if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {

                $total_impuesto =  $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];

            } else {

                $total_impuesto = '0.00000';

            }
            switch ($datos['condicion_impuesto'])
            {
                case '0':
                    if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {

                        $imp_a_acreditar = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                        $gasto_aplicable = '0.00000';

                    } else {
                        $imp_a_acreditar = '0.00000';
                        $gasto_aplicable = '0.00000';
                    }
                break;
                case '01':
                    $imp_a_acreditar = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                    $gasto_aplicable = '0.00000';
                break;
                case '02':
                    $imp_a_acreditar = '0.00000';
                    $gasto_aplicable = '0.00000';
                break;
                case '03':
                    $imp = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                    $imp_a_acreditar = ($imp * $configuracion->factor_receptor)/100;
                    $gasto_aplicable = '0.00000';
                break;
                case '04':
                    $imp_a_acreditar = '0.00000';
                    $gasto_aplicable = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                break;
                case '05':
                    $imp = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                    $imp_a_acreditar = ($imp * $configuracion->factor_receptor)/100;
                    $gasto_aplicable = $imp - $imp_a_acreditar;
                break;
            }
            ///NUEVO CNSECUTIVO 03-07-2025
             //nuevo mandejo de consecutivos
             // Paso 1: Obtener el rango de números de documento  
$consecutivo = DB::table('consecutivos')->where('idcaja', $datos['idcaja'])  
    ->where('tipo_documento', $datos['procesar_doc'])  
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
    ->where('idcaja', $datos['idcaja'])  
    ->where('tipo_documento', $datos['procesar_doc'])  
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
//dd($huecos);
// Resultado  
if (empty($huecos)) {  
               $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $datos['idcaja']],
                ['tipo_documento', '=', $datos['procesar_doc']],
            ])->get();
            $num_receptor = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        
          $consecutivo_fac = DB::table('receptor')->where([  
    ['idcaja', '=', $datos['idcaja']],  
    ['tipo_documento', '=', $datos['procesar_doc']],  
    ['numero_documento_receptor', '=', $num_receptor],  
    
])->get(); 

 //dd($consecutivo_fac);  
if ($consecutivo_fac->isEmpty()) {  
                $new = $num_receptor + 1;
             
                $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['procesar_doc'].' and idcaja = '.$datos['idcaja']);
                
                
}else{
    
    $new = $num_receptor +1;
    $num_receptor=str_pad($new, 10, "0", STR_PAD_LEFT);;
    $new=$num_receptor+1;
    
    $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['procesar_doc'].' and idcaja = '.$datos['idcaja']);
        
    //Session::flash('message', "Hay un problema con el consecutivo de la factura, SOLO SE PUEDE FACTURAR CON UNA PANTALLA ACTIVA, ve a VER DOC ELEC y LIMPA FACTURA EN PROCESO o Elimina alguna de los Documentos duplicados para continuar");  
    //return redirect()->back(); 
    // }
}
} else {  
    
   
    $num_receptor=$huecos[0];
 
    }       
            
        // dd($num_receptor);
            
            ///FIN NUEVO CONSECUTIVO

            // consulta de clasificacion por proveedor
            $cliente = Cliente::where('num_id', '=', ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'])->where('idconfigfact', '=',$datos['idconfigfact'])->first();
            if (empty($cliente)) {

                $cron = new CronController();
                $respuesta = $cron->registrarProveedor($datos['idconfigfact'] ,$strDatas[$documento]);
                $cliente = Cliente::where('num_id', '=', ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'])->where('idconfigfact', '=',$datos['idconfigfact'])->first();
            }

            $array_data = [
                'idcliente' => $cliente->idcliente,
                'codigo_actividad' => ''.$codigo_actividad
            ];
           $clasificacion = $this->getClasifica($array_data);  

// Verificar si 'clasificacion_proveedor' existe en el arreglo y no es null  
if (isset($clasificacion['clasificacion_proveedor']) && !is_null($clasificacion['clasificacion_proveedor'])) {  

    // Verificar que sea un arreglo y que tenga al menos un elemento  
    if (is_array($clasificacion['clasificacion_proveedor']) && count($clasificacion['clasificacion_proveedor']) > 0) {  
        $clasificacion_proveedor = $clasificacion['clasificacion_proveedor'][0]->tipo_clasificacion; // Acceder al tipo_clasificacion  
    } else {  
        $clasificacion_proveedor = 8; // Valor por defecto si no hay clasificaciones  
    }  
} else {  
    $clasificacion_proveedor = 8; // Asignar 8 si 'clasificacion_proveedor' es null  
}  

            DB::table('receptor')
            ->where('receptor.idreceptor', $datos['idrecepcion'])
            ->update([
                'receptor.idcaja' => $datos['idcaja'],
                'idconfigfact' => $datos['idconfigfact'],
                'idcodigoactv' => $datos['actividad'],
                'tipo_documento' => $datos['procesar_doc'],
                'detalle_mensaje' => $datos['detalle_mensaje'],
                'fecha' => date('Y-m-d'),
                'nombre_emisor' => $strDatas[$documento]['Emisor']['Nombre'],
                'total_impuesto' => $total_impuesto,
                'total_comprobante' => $strDatas[$documento]['ResumenFactura']['TotalComprobante'],
                'cedula_emisor' => $strDatas[$documento]['Emisor']['Identificacion']['Numero'],
                'clasifica_d151' => $clasificacion_proveedor,
                'condicion_impuesto' => $datos['condicion_impuesto'],
                'imp_creditar' => $datos['imp_creditar'],
                'gasto_aplica' => $datos['gasto_aplica'],
                'hacienda_imp_creditar' => $imp_a_acreditar,
                'hacienda_gasto_aplica' => $gasto_aplicable,
                'fecha_xml_envio' => $strDatas[$documento]['FechaEmision'],
                'numero_documento_receptor' => $num_receptor
            ]);

            $xml = [
                'tipoDocumento' => ''.$datos['procesar_doc'], // 05 Aceptado 06 Parcialmente aceptado 07 rechazado
                'sucursal' => ''.$sucursal, // 3 digitos sucursal de donde proviene el documento para el armado de clave
                'puntoVenta' => ''.$punto_venta, // 5 digitos punto de venta del cual se armo el documento
                'idreceptor' => ''.$receptor->idreceptor,
                'idconfigfact' => ''.$datos['idconfigfact'],
                'comando' => '0',
                'numeroFactura' => ''.$num_receptor, //correspondiente al numero del documento para el receptor
                'fechaEmision' => date('c'), // fecha de emision del documento no mayor a 72 horas, date_default_timezone_set debe ser costa rica
                'rutaxml' => ''.$receptor->ruta_carga, // ruta del xml para la factura es el XML recibido por el emisor
                'CondicionImpuesto' => ''.$datos['condicion_impuesto'],
                'MontoTotalImpuestoAcreditar' => ''.$imp_a_acreditar,
                'MontoTotalDeGastoAplicable' => ''.$gasto_aplicable,
                'Emisor' => array(
                    'NumeroCedulaEmisor' => ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'], // cedula del emisor del documento
                    'Mensaje' => ''.$mensaje, // 1 para cuando el mensaje es aceptado, o en su defecto igual a 05, 2 para 06 y 3 para 07
                    'CodigoActividad' => ''.$actividad->codigo_actividad,
                    'MontoTotalImpuesto' => ''.$total_impuesto, // Monto total del impuesto de la factura
                    'TotalFactura' => ''.$strDatas[$documento]['ResumenFactura']['TotalComprobante'], // Monto total de la factura
                    'DetalleMensaje' => ''.$datos['detalle_mensaje'] // Alguna nota o detalle que queramos agregar para el Mensaje Receptor
                )
            ];
            $facturar = Timbrar_receptor($xml, $seguridad);
            //$consecutivo = DB::update('update consecutivos set numero_documento = '.$num_receptor.' where tipo_documento = '.$datos['procesar_doc'].' and idcaja = '.$datos['idcaja']);

            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function initMasiveRecepcion($datos, $idreceptor)
    {
        try {

            $receptor = Receptor::find($idreceptor);
            include_once(public_path(). '/funcionFacturacion506.php');
 if (!$receptor) {
            \Log::error('Receptor not found', ['idreceptor' => $idreceptor, 'datos' => $datos]);
            // Opcional: lanzar excepción o retornar para evitar continuar
            return;
        }

        \Log::info('Receptor found', [
            'id' => $receptor->id ?? null,
            'ruta_carga' => $receptor->ruta_carga ?? null,
            'tipo_documento_recibido' => $receptor->tipo_documento_recibido ?? null,
        ]);
        
            $strContents = file_get_contents(public_path($receptor->ruta_carga));
            $strDatas = $this->Xml2Array($strContents);
            // Obtener la versión del xml 
               $namespace = $strDatas['FacturaElectronica_attr']['xmlns'] ?? null;  
               if ($namespace) {  
               preg_match('/v(\d+\.\d+)/', $namespace, $matches);  
               $version = isset($matches[1]) ? $matches[1] : null;  
               } 
            switch ($receptor->tipo_documento_recibido)
            {
                case '01':
                    $documento = 'FacturaElectronica';
                    if (!isset($strDatas[$documento])) {

                        return Redirect::back()->withErrors(['cargar_documento' => ['Selecciono Factura y el XML cargado no Corresponde']]);
                    }
                break;
                case '02':
                    $documento = 'NotaDebitoElectronica';
                    if (!isset($strDatas[$documento])) {

                        return Redirect::back()->withErrors(['cargar_documento' => ['Selecciono Nota de Debito y el XML cargado no Corresponde']]);
                    }
                break;
                case '03':
                    $documento = 'NotaCreditoElectronica';
                    if (!isset($strDatas[$documento])) {

                        return Redirect::back()->withErrors(['cargar_documento' => ['Selecciono Nota de Credito y el XML cargado no Corresponde']]);
                    }
                break;
            }
 if($version == '4.4'){
                 $codigo_actividad= $strDatas[$documento]['CodigoActividadEmisor'];
                 }else{
                 $codigo_actividad= $strDatas[$documento]['CodigoActividad'];
                 }
            $seguridad = $this->armarSeguridad($datos['idconfigfact']);
            $configuracion = Configuracion::find($datos['idconfigfact']);
            $actividad = Actividad::find($datos['actividad']);

            switch ($datos['procesar_doc']) {

                case '05':
                    $mensaje = 1;
                break;
                case '06':
                    $mensaje = 2;
                break;
                case '07':
                    $mensaje = 3;
                break;
            }

            $punto_venta = substr($strDatas[$documento]['NumeroConsecutivo'], 3, 5);
            $sucursal = substr($strDatas[$documento]['NumeroConsecutivo'], 0, 3);

            if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {

                $total_impuesto =  $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];

            } else {

                $total_impuesto = '0.00000';

            }
            switch ($datos['condicion_impuesto'])
            {
                case '0':
                    if (isset($strDatas[$documento]['ResumenFactura']['TotalImpuesto'])) {

                        $imp_a_acreditar = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                        $gasto_aplicable = '0.00000';

                    } else {
                        $imp_a_acreditar = '0.00000';
                        $gasto_aplicable = '0.00000';
                    }
                break;
                case '01':
                    $imp_a_acreditar = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                    $gasto_aplicable = '0.00000';
                break;
                case '02':
                    $imp_a_acreditar = '0.00000';
                    $gasto_aplicable = '0.00000';
                break;
                case '03':
                    $imp = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                    $imp_a_acreditar = ($imp * $configuracion->factor_receptor)/100;
                    $gasto_aplicable = '0.00000';
                break;
                case '04':
                    $imp_a_acreditar = '0.00000';
                    $gasto_aplicable = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                break;
                case '05':
                    $imp = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'];
                    $imp_a_acreditar = ($imp * $configuracion->factor_receptor)/100;
                    $gasto_aplicable = $imp - $imp_a_acreditar;
                break;
            }
            ///NUEVO CNSECUTIVO 03-07-2025
             //nuevo mandejo de consecutivos
             // Paso 1: Obtener el rango de números de documento  
$consecutivo = DB::table('consecutivos')->where('idcaja', $datos['idcaja'])  
    ->where('tipo_documento', $datos['procesar_doc'])  
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
    ->where('idcaja', $datos['idcaja'])  
    ->where('tipo_documento', $datos['procesar_doc'])  
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
//dd($huecos);
// Resultado  
if (empty($huecos)) {  
               $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $datos['idcaja']],
                ['tipo_documento', '=', $datos['procesar_doc']],
            ])->get();
            $num_receptor = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        
          $consecutivo_fac = DB::table('receptor')->where([  
    ['idcaja', '=', $datos['idcaja']],  
    ['tipo_documento', '=', $datos['procesar_doc']],  
    ['numero_documento_receptor', '=', $num_receptor],  
   
])->get(); 

 //dd($consecutivo_fac);  
if ($consecutivo_fac->isEmpty()) {  
                $new = $num_receptor + 1;
             
                $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['procesar_doc'].' and idcaja = '.$datos['idcaja']);
                
                
}else{
    
    $new = $num_receptor +1;
    $num_receptor=str_pad($new, 10, "0", STR_PAD_LEFT);;
    $new=$num_receptor+1;
    
    $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['procesar_doc'].' and idcaja = '.$datos['idcaja']);
        
    //Session::flash('message', "Hay un problema con el consecutivo de la factura, SOLO SE PUEDE FACTURAR CON UNA PANTALLA ACTIVA, ve a VER DOC ELEC y LIMPA FACTURA EN PROCESO o Elimina alguna de los Documentos duplicados para continuar");  
    //return redirect()->back(); 
    // }
}
} else {  
    
   
    $num_receptor=$huecos[0];
  
    }       
            
          // dd($num_receptor);
            
            ///FIN NUEVO CONSECUTIVO

            // consulta de clasificacion por proveedor
            $cliente = Cliente::where('num_id', '=', ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'])->where('idconfigfact', '=',$datos['idconfigfact'])->first();
            if (empty($cliente)) {

                $cron = new CronController();
                $respuesta = $cron->registrarProveedor($datos['idconfigfact'] ,$strDatas[$documento]);
                $cliente = Cliente::where('num_id', '=', ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'])->where('idconfigfact', '=',$datos['idconfigfact'])->first();
            }

            $array_data = [
                'idcliente' => $cliente->idcliente,
                'codigo_actividad' => ''.$codigo_actividad
            ];
            $clasificacion = $this->getClasifica($array_data);

            if (!is_null($clasificacion['clasificacion_proveedor'])) {

                if (count($clasificacion) > 0) {

                    $clasificacion_proveedor = $clasificacion['clasificacion_proveedor'][0]->tipo_clasificacion;
                } else {

                    $clasificacion_proveedor = 8;
                }
            } else {

                $clasificacion_proveedor = 8;
            }

            DB::table('receptor')
            ->where('receptor.idreceptor', $idreceptor)
            ->update([
                'receptor.idcaja' => $datos['idcaja'],
                'idconfigfact' => $datos['idconfigfact'],
                'idcodigoactv' => $datos['actividad'],
                'tipo_documento' => $datos['procesar_doc'],
                'detalle_mensaje' => $datos['detalle_mensaje'],
                'fecha' => date('Y-m-d'),
                'nombre_emisor' => $strDatas[$documento]['Emisor']['Nombre'],
                'total_impuesto' => $total_impuesto,
                'total_comprobante' => $strDatas[$documento]['ResumenFactura']['TotalComprobante'],
                'cedula_emisor' => $strDatas[$documento]['Emisor']['Identificacion']['Numero'],
                'clasifica_d151' => $clasificacion_proveedor,
                'condicion_impuesto' => $datos['condicion_impuesto'],
                'imp_creditar' => $datos['imp_creditar'],
                'gasto_aplica' => $datos['gasto_aplica'],
                'hacienda_imp_creditar' => $imp_a_acreditar,
                'hacienda_gasto_aplica' => $gasto_aplicable,
                'fecha_xml_envio' => $strDatas[$documento]['FechaEmision'],
                'numero_documento_receptor' => $num_receptor
            ]);

            $xml = [
                'tipoDocumento' => ''.$datos['procesar_doc'], // 05 Aceptado 06 Parcialmente aceptado 07 rechazado
                'sucursal' => ''.$sucursal, // 3 digitos sucursal de donde proviene el documento para el armado de clave
                'puntoVenta' => ''.$punto_venta, // 5 digitos punto de venta del cual se armo el documento
                'idreceptor' => ''.$receptor->idreceptor,
                'idconfigfact' => ''.$datos['idconfigfact'],
                'comando' => '1',
                'numeroFactura' => ''.$num_receptor, //correspondiente al numero del documento para el receptor
                'fechaEmision' => date('c'), // fecha de emision del documento no mayor a 72 horas, date_default_timezone_set debe ser costa rica
                'rutaxml' => ''.$receptor->ruta_carga, // ruta del xml para la factura es el XML recibido por el emisor
                'CondicionImpuesto' => ''.$datos['condicion_impuesto'],
                'MontoTotalImpuestoAcreditar' => ''.$imp_a_acreditar,
                'MontoTotalDeGastoAplicable' => ''.$gasto_aplicable,
                'Emisor' => array(
                    'NumeroCedulaEmisor' => ''.$strDatas[$documento]['Emisor']['Identificacion']['Numero'], // cedula del emisor del documento
                    'Mensaje' => ''.$mensaje, // 1 para cuando el mensaje es aceptado, o en su defecto igual a 05, 2 para 06 y 3 para 07
                    'CodigoActividad' => ''.$actividad->codigo_actividad,
                    'MontoTotalImpuesto' => ''.$total_impuesto, // Monto total del impuesto de la factura
                    'TotalFactura' => ''.$strDatas[$documento]['ResumenFactura']['TotalComprobante'], // Monto total de la factura
                    'DetalleMensaje' => ''.$datos['detalle_mensaje'] // Alguna nota o detalle que queramos agregar para el Mensaje Receptor
                )
            ];
            $facturar = Timbrar_receptor($xml, $seguridad);
            //$consecutivo = DB::update('update consecutivos set numero_documento = '.$num_receptor.' where tipo_documento = '.$datos['procesar_doc'].' and idcaja = '.$datos['idcaja']);

            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function docRecibido(Request $request)
    {
        $input = $request->all();

        $receptor = DB::table('receptor')
        ->select('receptor.*')
        ->where('receptor.idreceptor', '=', $input['idreceptor'])
        ->first();
        $strContents = file_get_contents($receptor->ruta_carga);
        $strDatas = $this->Xml2Array($strContents);
      // Obtener la versión del XML  
$namespace = $strDatas['FacturaElectronica_attr']['xmlns']   
    ?? $strDatas['NotaCreditoElectronica_attr']['xmlns']   
    ?? $strDatas['FacturaElectronicaCompra_attr']['xmlns']   
    ?? $strDatas['NotaDebitoElectronica_attr']['xmlns']   
    ?? null;  

if ($namespace) {  
    preg_match('/v(\d+\.\d+)/', $namespace, $matches);  
    $version = isset($matches[1]) ? $matches[1] : null;  
} else{
    $version = '4.0'; 
}      
        $callback = [];
        switch ($receptor->tipo_documento_recibido) {

            case '01':
                $documento = 'FacturaElectronica';
                $tipo = 'Factura Electronica';
            break;
            case '02':
                $documento = 'NotaDebitoElectronica';
                $tipo = 'Nota Debito Electronica';
            break;
            case '03':
                $documento = 'NotaCreditoElectronica';
                $tipo = 'Nota Credito Electronica';
            break;
        }
         if($version == '4.4'){
                 $codigo_actividad= $strDatas[$documento]['CodigoActividadEmisor'];
                 }else{
                 $codigo_actividad= $strDatas[$documento]['CodigoActividad'];
                 }
        $callback['documento'] = 'Documento de tipo : '.$tipo.' Número #'. substr($strDatas[$documento]['NumeroConsecutivo'], 10, 20);
        $callback['clave'] = "Clave del Documento: ".$strDatas[$documento]['Clave'];
        $callback['fecha'] = "Fecha del Documento: ". Carbon::parse($strDatas[$documento]['FechaEmision'])->toDateTimeString();
        $callback['identificacion'] = "Identificación: ".$strDatas[$documento]['Emisor']['Identificacion']['Numero'];
        $callback['nombre_emisor'] = "Nombre: ".$strDatas[$documento]['Emisor']['Nombre'];
         $callback['codigo_activ'] = "Codigo Actividad: ".$codigo_actividad;
        $callback['correo_emisor'] = "Correo: ".$strDatas[$documento]['Emisor']['CorreoElectronico'] ?? 'factura@feisaac.com';
        $detalle = [];
        $callback['detalle'] = [];

        // Armado del detalle DetalleServicio
        if (!isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['NumeroLinea'])) {

            for ($i=0; $i < count($strDatas[$documento]['DetalleServicio']['LineaDetalle']); $i++) {

                $detalle = [];
                $detalle['codigo_comercial'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['CodigoComercial']['Codigo'] ?? 'GNL';
                $detalle['cabys'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['CodigoCABYS'] ?? 
                   $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Codigo'] ?? null;
                $detalle['descripcion'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Detalle'];
                $detalle['cantidad'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Cantidad'];
                $detalle['unidad_medida'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['UnidadMedida'];
                $detalle['subtotal'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['SubTotal'];
                //calcular si es 1 impuesto o mas
                if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto'])) {

                    $detalle['tarifa'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Tarifa'];
                    $detalle['impuesto_mto'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['Impuesto']['Monto'];
                } else {

                    $detalle['tarifa'] = 'No Aplica';
                    $detalle['impuesto_mto'] = 'No Aplica';
                }
                $detalle['total_linea'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle'][$i]['MontoTotalLinea'];
                array_push($callback['detalle'], $detalle);
            }
        } else {

            $detalle['codigo_comercial'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle']['CodigoComercial']['Codigo'] ?? 'GNL';
            $detalle['cabys'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle']['CodigoCABYS'] ?? 
                   $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Codigo'] ?? null;
            $detalle['descripcion'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Detalle'];
            $detalle['cantidad'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Cantidad'];
            $detalle['unidad_medida'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle']['UnidadMedida'];
            $detalle['subtotal'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle']['SubTotal'];

            //calcular si es 1 impuesto o mas
            if (isset($strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto'])) {
                $detalle['tarifa'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Tarifa'];
                $detalle['impuesto_mto'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle']['Impuesto']['Monto'];
            } else {

                $detalle['tarifa'] = 'No Aplica';
                $detalle['impuesto_mto'] = 'No Aplica';
            }

            $detalle['total_linea'] = $strDatas[$documento]['DetalleServicio']['LineaDetalle']['MontoTotalLinea'];

            array_push($callback['detalle'], $detalle);
        }


        // Seccion de totales
        $callback['moneda'] = $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['CodigoMoneda'] ?? 'CRC';
        $callback['cambio'] = $strDatas[$documento]['ResumenFactura']['CodigoTipoMoneda']['TipoCambio'] ?? 0.00000;
        $callback['total_gravado'] = $strDatas[$documento]['ResumenFactura']['TotalGravado'] ?? 0.00000;
        $callback['total_exento'] = $strDatas[$documento]['ResumenFactura']['TotalExento'] ?? 0.00000;
        $callback['total_venta'] = $strDatas[$documento]['ResumenFactura']['TotalVenta'] ?? 0.00000;
        $callback['total_descuento'] = $strDatas[$documento]['ResumenFactura']['TotalDescuentos'] ?? 0.00000;
        $callback['total_neta'] = $strDatas[$documento]['ResumenFactura']['TotalVentaNeta'] ?? 0.00000;
        $callback['total_impuesto'] = $strDatas[$documento]['ResumenFactura']['TotalImpuesto'] ?? 0.00000;
        $callback['total_iva_devuelto'] = $strDatas[$documento]['ResumenFactura']['TotalIVADevuelto'] ?? 0.00000;
        $callback['total_comprobante'] = $strDatas[$documento]['ResumenFactura']['TotalComprobante'] ?? 0.00000;

        return response()->json(['success'=> $callback]);
    }

    public function getClasifica($array_data)//$array_data
    {
        $consulta = DB::table('clasificacion_proveedor')
        ->select('clasificacion_proveedor.*')
        ->where([
            ['clasificacion_proveedor.idcliente', '=', $array_data['idcliente']],
            ['clasificacion_proveedor.codigo_actividad', '=', $array_data['codigo_actividad']]
        ])
        ->get();
        if (count($consulta) > 0) {

            return ['clasificacion_proveedor' => $consulta];
        } else {

            return [' clasificacion_proveedor'=> null];
        }
    }

    public function showClasificacion(Request $request)
    {
        $datos = $request->all();
        $consulta = DB::table('receptor')
        ->select('receptor.*')
        ->where('receptor.idreceptor', '=', $datos['idreceptor'])
        ->first();
        return response()->json($consulta);
    }

    public function editModalClasificacion(Request $request)
    {
        $datos = $request->all();
        DB::table('receptor')
        ->where('receptor.idreceptor', $datos['idreceptor'])
        ->update([
            'clasifica_d151' => $datos['clasificacion']
        ]);
        //Reviso si existe en la tabla de clasificaciones por proveedor
        $check = $this->checkClasificaProveedor($datos['idreceptor'],$datos['clasificacion']);
        $message = 'Clasificacion actualizada correctamente';
        return redirect()->route('receptor.index')->withStatus(__(''.$message));
    }

    public function checkClasificaProveedor($idreceptor, $clasificacion)
    {
        $recepcion = DB::table('receptor')
        ->select('receptor.*')
        ->where('receptor.idreceptor', $idreceptor)
        ->first();

        $proveedor = DB::table('clientes')
        ->select('clientes.*')
        ->where('clientes.num_id', $recepcion->cedula_emisor)
        ->where('clientes.idconfigfact', $recepcion->idconfigfact)
        ->first();

        $strContents = file_get_contents($recepcion->ruta_carga);
        $strDatas = $this->Xml2Array($strContents);
        // Obtener la versión del xml 
               $namespace = $strDatas['FacturaElectronica_attr']['xmlns'] ?? null;  
               if ($namespace) {  
               preg_match('/v(\d+\.\d+)/', $namespace, $matches);  
               $version = isset($matches[1]) ? $matches[1] : null;  
               } 
        switch ($recepcion->tipo_documento_recibido) {

            case '01':
                $documento = 'FacturaElectronica';
                $tipo = 'Factura Electronica';
            break;
            case '02':
                $documento = 'NotaDebitoElectronica';
                $tipo = 'Nota Debito Electronica';
            break;
            case '03':
                $documento = 'NotaCreditoElectronica';
                $tipo = 'Nota Credito Electronica';
            break;
        }
         if($version == '4.4'){
                 $codigo_actividad= $strDatas[$documento]['CodigoActividadEmisor'];
                 }else{
                 $codigo_actividad= $strDatas[$documento]['CodigoActividad'];
                 }
        $codigo_actividad = ''.$codigo_actividad;
        //consulto contra la tabla de clasificacion para saber si ya esta registrado
        $consulta = DB::table('clasificacion_proveedor')
        ->select('clasificacion_proveedor.*')
        ->where([
            ['clasificacion_proveedor.idcliente', '=', $proveedor->idcliente],
            ['clasificacion_proveedor.codigo_actividad', '=', $codigo_actividad],
            ['clasificacion_proveedor.tipo_clasificacion', '=', $clasificacion]
        ])
        ->count();

        if ($consulta == 0) {

            $controller_cron = new CronController();
            $razon_social = $controller_cron->actividad($recepcion->cedula_emisor, $codigo_actividad);

            $consulta_clasificacion = DB::table('clasificaciones')
            ->select('clasificaciones.*')
            ->where('clasificaciones.idclasifica', '=', $clasificacion)
            ->first();

            $data = array(
                'idcliente' => $proveedor->idcliente,
                'codigo_actividad' => $codigo_actividad,
                'razon_social' => $razon_social,
                'tipo_clasificacion' => $clasificacion,
                'descripcion_clasificacion' => $consulta_clasificacion->descripcion,
                'por_defecto' => 0
            );
            DB::table('clasificacion_proveedor')->insertGetId($data);
        }
    }

    public function startCronRecepcionAutomatica()
    {

        $config_receptor = DB::table('config_receptor')
        ->select('config_receptor.*')
        ->where('config_receptor.estatus', '=', 'en espera')
        ->get();

        try {

            foreach ($config_receptor as $recepcion) {

                $log_recepcion = DB::table('log_recepcion_automatica')
                ->select('log_recepcion_automatica.*')
                ->where('log_recepcion_automatica.estatus_recepcion', '=', 'por enviar')
                ->where('log_recepcion_automatica.idconfig_receptor', '=', $recepcion->idconfig_receptor)
                ->get();

                foreach ($log_recepcion as $rec) {

                    $datos = [
                        'procesar_doc' => $recepcion->procesar_doc,
                        'idcaja' => $recepcion->idcaja,
                        'actividad' => $recepcion->idcodigoactv,
                        'detalle_mensaje' => $recepcion->detalle_mensaje,
                        'condicion_impuesto' => $recepcion->condicion_impuesto ,
                        'factor_credito' =>  $recepcion->factor_credito,
                        'imp_creditar' =>  $recepcion->imp_creditar,
                        'gasto_aplica' =>  $recepcion->gasto_aplica,
                        'idrecepcion' => $rec->idrecepcion,
                        'idconfigfact' => $recepcion->idconfigfact,
                    ];
                    $inicio = $this->initMasiveRecepcion($datos, $rec->idrecepcion);
                    $array = [
                        'log_recepcion_automatica.estatus_recepcion' => 'enviada',
                    ];
                    DB::table('log_recepcion_automatica')
                    ->where('log_recepcion_automatica.idrecepcion_automatica', $rec->idrecepcion_automatica)
                    ->update($array);
                }

                $array_config = [
                    'config_receptor.estatus' => 'enviada',
                ];
                DB::table('config_receptor')
                ->where('config_receptor.idconfig_receptor', $recepcion->idconfig_receptor)
                ->update($array_config);
            }
            return 1;

        } catch (Exception $e) {

            return 0;
        }
    }
}
