<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cantones;
use DB;
use App\Configuracion;
use App\Facelectron;
use App\Cliente;
use App\Actividad;
use App\Productos;
use App\Inventario;
use App\Inventario_item;
use App\Cajas;
use App\Sales_item;
use App\Sales;
use App\Bancos;
use Auth;
use App\Items_masivo;
use App\Pedidos_item;
use App\Jobs\ConsultarRecepcion;

class PeticionesController extends Controller
{
    public function ajaxCantones(Request $request)
    {
    	$input = $request->all();
    	$cantones = DB::table('cantones')->where('idprovincia', $input['provincia'])->get();
        return response()->json(['success'=> $cantones]);
    }

    public function ajaxDistritos(Request $request)
    {
    	$input = $request->all();
    	$distritos = DB::table('distritos')->where('idcanton', $input['canton'])->get();
        return response()->json(['success'=> $distritos]);
    }

    public function ajaxNumFactura(Request $request)
    {
        $input = $request->all();

           //consecutivo
         
if (in_array($input['tipo_documento'], ['01', '02', '03', '04', '08', '09', '10'])) {
  
       // Paso 1: Obtener el rango de nè´‚meros de documento  
$consecutivo = DB::table('consecutivos')->where('idcaja', $input['idcaja'])  
    ->where('tipo_documento', $input['tipo_documento'])  
    ->first();  

if (!$consecutivo) {  
    // Manejo de error si no se encuentra el consecutivo  
    Session::flash('message', "No se encontrè´— el consecutivo.");  
    return redirect()->back();  
}  

$docDesde = $consecutivo->doc_desde;  
$docHasta = $consecutivo->numero_documento;  

// Paso 2: Obtener los nè´‚meros de documento emitidos en sales  
$numerosEmitidos = DB::table('sales')  
    ->where('idcaja', $input['idcaja'])  
    ->where('tipo_documento', $input['tipo_documento']) 
    ->where('estatus_sale', '=' , 2)
    ->pluck('numero_documento')  
    ->toArray();  

// Paso 3: Comparar los nè´‚meros  
$huecos = [];  
for ($i = $docDesde; $i <= $docHasta; $i++) {  
    // Completar con ceros a la izquierda hasta 10 dè´øgitos  
    $numeroCompleto = str_pad($i, 10, "0", STR_PAD_LEFT);  
    if (!in_array($numeroCompleto, $numerosEmitidos)) {  
        $huecos[] = $numeroCompleto; // Agregar a los huecos si no estè´° emitido  
    }  
}  

// Resultado  
if (empty($huecos)) {  
               $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $input['idcaja']],
                ['tipo_documento', '=', $input['tipo_documento']],
            ])->get();
            $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
           
          $consecutivo_fac = DB::table('sales')->where([  
    ['idcaja', '=', $input['idcaja']],  
    ['tipo_documento', '=', $input['tipo_documento']],  
    ['numero_documento', '=', $numero_factura],  
    //['idsale', '!=', $input['idsale']],
    ['estatus_sale', '=' ,2]
])->get(); 


if ($consecutivo_fac->isEmpty()) {  
                $new = $numero_factura + 1;
             
              
                
                
}else{
    
    $new = $numero_factura +1;
    $numero_factura=str_pad($new, 10, "0", STR_PAD_LEFT);;
    $new=$numero_factura+1;
    
  
        
   
}
} else {  
    
   
    $numero_factura=$huecos[0];
  
    }  
    
}else{
    
    $consecutivo = DB::table('consecutivos')->where([
            ['idcaja', '=', $input['idcaja']],
            ['tipo_documento', '=', $input['tipo_documento']],
        ])->get();

        $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
}
       
       //consecutivo
        $codigo = Actividad::where([
            ['idconfigfact', $input['idconfigfact']],
            ['estado', '0' ],

            ] )-> orderBy('principal', 'Desc')->get();
        $factor = Configuracion::find($input['idconfigfact']);
        $data = [
            'numero_factura' => $numero_factura,
            'factor' => $factor,
            'codigo_actividad'=> $codigo,
        ];
        return response()->json(['success'=> $data]);
    }

        public function ajaxNumMasivo(Request $request)
    {
        $input = $request->all();
        $consecutivo = DB::table('consecutivos')->where([
            ['idcaja', '=', $input['idcaja']],
            ['tipo_documento', '=', $input['tipo_documento']],
        ])->get();
        $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        $codigo = Actividad::where('idconfigfact', $input['idconfigfact'])->get();
        $data = [
            'numero_factura' => $numero_factura,
            'codigo_actividad'=> $codigo,
        ];
        return response()->json(['success'=> $data]);
    }

        public function ajaxConsultar()
    {
        include_once(public_path(). '/consulta_documento.php');

        $arreglo = DB::table('facelectron')->where([
            ['pendiente', '=', '0'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();

        foreach ($arreglo as $array) {
            $seguridad = $this->armarSeguridad($array->idconfigfact);
            $xml = [
                'tipoDocumento' => ''.$array->tipodoc, //tipo de documento a consultar
                'numero_consecutivo' => ''.$array->consecutivo, //Numero consecutivo del documento
                'clave' => ''.$array->clave //clave del documento
            ];

            $envio = Enviar_documentos($xml, $seguridad);
           \Log::info("consulta_doc: ".$envio);
            $idcli = DB::table('sales')->where([
                ['idsale', $array->idsales],
            ])->get();
            $generar = app('App\Http\Controllers\ReportesController')->pdf_factura($array->idsales);
        }
        return redirect()->route('facturar.index')->withStatus(__('Comprobantes Consultados correctamente.'));
    }
    public function ajaxConsultarFec(){
        include_once(public_path(). '/consulta_documento.php');
        $arreglo = DB::table('facelectron')->where([
            ['tipodoc', '=', '08'],
            ['pendiente', '=', '0'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        foreach ($arreglo as $array) {
            $seguridad = $this->armarSeguridad($array->idconfigfact);
            $xml = [
                'tipoDocumento' => ''.$array->tipodoc, //tipo de documento a consultar
                'numero_consecutivo' => ''.$array->consecutivo, //Numero consecutivo del documento
                'clave' => ''.$array->clave //clave del documento
            ];
            $envio = Enviar_documentos($xml, $seguridad);
            $idcli = DB::table('sales')->where([
                ['idsale', $array->idsales],
            ])->get();
        }
        return redirect()->route('fec.index')->withStatus(__('Comprobantes Consultados correctamente.'));
    }
    public function ajaxConsultarFeE(){
        include_once(public_path(). '/consulta_documento.php');
        $arreglo = DB::table('facelectron')->where([
            ['tipodoc', '=', '09'],
            ['pendiente', '=', '0'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        foreach ($arreglo as $array) {
            $seguridad = $this->armarSeguridad($array->idconfigfact);
            $xml = [
                'tipoDocumento' => ''.$array->tipodoc, //tipo de documento a consultar
                'numero_consecutivo' => ''.$array->consecutivo, //Numero consecutivo del documento
                'clave' => ''.$array->clave //clave del documento
            ];
            $envio = Enviar_documentos($xml, $seguridad);
            $idcli = DB::table('sales')->where([
                ['idsale', $array->idsales],
            ])->get();
        }
        return redirect()->route('fee.index')->withStatus(__('Comprobantes Consultados correctamente.'));
    }

    public function ajaxConsultarTiquete(){
        include_once(public_path(). '/consulta_documento.php');
        $arreglo = DB::table('facelectron')->where([
            ['tipodoc', '=', '04'],
            ['pendiente', '=', '0'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        foreach ($arreglo as $array) {
            $seguridad = $this->armarSeguridad($array->idconfigfact);
            $xml = [
                'tipoDocumento' => ''.$array->tipodoc, //tipo de documento a consultar
                'numero_consecutivo' => ''.$array->consecutivo, //Numero consecutivo del documento
                'clave' => ''.$array->clave //clave del documento
            ];
            $envio = Enviar_documentos($xml, $seguridad);
             $idcli = DB::table('sales')->where([
                ['idsale', $array->idsales],
            ])->get();
        }
        return redirect()->route('tiquetes.index')->withStatus(__('Comprobantes Consultados correctamente.'));
    }

    public function ajaxConsultarReceptor(){
        include_once(public_path(). '/consulta_documento.php');
        $arreglo = DB::table('receptor')->where([
            ['pendiente', '=', '0'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        //dd($arreglo);
        foreach ($arreglo as $array) {
            $seguridad = $this->armarSeguridad($array->idconfigfact);
            $xml = [
                'tipoDocumento' => ''.$array->tipo_documento, //tipo de documento a consultar
                'numero_consecutivo' => ''.$array->consecutivo, //Numero consecutivo del documento
                'clave' => ''.$array->clave, //clave del documento
                'idreceptor' => ''.$array->idreceptor,
                'comando' => 0,
            ];
            $envio = Enviar_documentos($xml, $seguridad);
        }

        return redirect()->route('receptor.index')->withStatus(__('Comprobantes Consultados correctamente.'));
    }

    public function ajaxEjecutarConsultarReceptor(){
        ConsultarRecepcion::dispatch();
        return redirect()->route('receptor.index')->withStatus(__('Comprobantes Consultados correctamente.'));

    }
    public function ajaxConsultarNC(){
        include_once(public_path(). '/consulta_documento.php');
        $arreglo = DB::table('facelectron')->where([
            ['pendiente', '=', '0'],
            ['tipodoc', '=', '03'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        foreach ($arreglo as $array) {
            $seguridad = $this->armarSeguridad($array->idconfigfact);
            $xml = [
                'tipoDocumento' => ''.$array->tipodoc, //tipo de documento a consultar
                'numero_consecutivo' => ''.$array->consecutivo, //Numero consecutivo del documento
                'clave' => ''.$array->clave //clave del documento
            ];
            $envio = Enviar_documentos($xml, $seguridad);
            $idcli = DB::table('sales')->where([
                ['idsale', $array->idsales],
            ])->get();
            $generar = app('App\Http\Controllers\ReportesController')->pdf_factura($array->idsales);
        }

        return redirect()->route('notacredito.index')->withStatus(__('Comprobantes Consultados correctamente.'));
    }

    public function ajaxConsultarND(){
        include_once(public_path(). '/consulta_documento.php');
        $arreglo = DB::table('facelectron')->where([
            ['pendiente', '=', '0'],
            ['tipodoc', '=', '02'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        foreach ($arreglo as $array) {
            $seguridad = $this->armarSeguridad($array->idconfigfact);
            $xml = [
                'tipoDocumento' => ''.$array->tipodoc, //tipo de documento a consultar
                'numero_consecutivo' => ''.$array->consecutivo, //Numero consecutivo del documento
                'clave' => ''.$array->clave //clave del documento
            ];
            $envio = Enviar_documentos($xml, $seguridad);
            $idcli = DB::table('sales')->where([
                ['idsale', $array->idsales],
            ])->get();
            $generar = app('App\Http\Controllers\ReportesController')->pdf_factura($array->idsales);
        }

        return redirect()->route('notadebito.index')->withStatus(__('Comprobantes Consultados correctamente.'));
    }

        public function armarSeguridad($idconfigfact)
    {
            $buscar = Configuracion::find($idconfigfact);
            if ($buscar->client_id === 1) {
               $entorno = 'api-prod';
            }else{
               $entorno = 'api-stag';
            }
            $seguridad =  [
                'certificado' => ''.public_path().'/certificados/'.$buscar->ruta_certificado, // Ruta del certificado donde se encuentra
                'clave_certificado' => ''.$buscar->clave_certificado, //clave del certificado .p12
                'credenciales_conexion' => ''.$buscar->credenciales_conexion, //credenciales de Hacienda
                'clave_conexion' => ''.$buscar->clave_conexion, //Contrase√±a de hacienda
                'client_id' => $entorno //api-stag para pruebas y api-prod para el entorno produccion
            ];
            return $seguridad;
    }


        public function buscarExoneracion($id)
    {
        $exoneracion = DB::table('items_exonerados')->where([
            ['idsalesitem', '=', $id],
        ])->get();
        return response()->json(['success'=> $exoneracion]);

    }

        public function buscarInventario(Request $request)
    {
        $input = $request->all();
        $consulta = Inventario_item::find($input['idinventario_item']);
        $producto = Productos::find($consulta->idproducto);
        $actualizar = Inventario_item::where('idinventario_item', $input['idinventario_item'])->update(['cantidad_inventario' => $input['cantidad_inv']]);
        $total = $producto->precio_final * $input['cantidad_inv'];
        return response()->json(['success'=> $total]);
    }

        public function consultaEmpresa(Request $request)
    {
        $input = $request->all();
        $codigo = Actividad::where('idconfigfact', $input['idconfigfact'])->get();
        $data = [
            'codigo_actividad'=> $codigo
        ];
        return response()->json(['success'=> $data]);
    }
    public function consultaEmpresad(Request $request)
    {
        $input = $request->all();
        $codigo = Bancos::where('idconfigfact', $input['idconfigfact'])->get();
        $data = [
            'codigo_actividad'=> $codigo
        ];
        return response()->json(['success'=> $data]);
    }


        public function infoFlotante(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['id']);
        $producto = Productos::find($sales_item->idproducto);
        return response()->json(['success'=> $producto]);
    }

    public function infoFlotanteCot(Request $request)
    {
        $input = $request->all();
        $pedidos_item = Pedidos_item::find($input['id']);
        $producto = Productos::find($pedidos_item->idproducto);
        return response()->json(['success'=> $producto]);
    }
        public function ajaxPcredenciales(Request $request)
    {
        $input = $request->all();
        $string = "username=".$input['credenciales']."&password=".urlencode($input['clave_cre'])."&grant_type=password&client_id=".$input['entorno']."";
        $url = '';
        if ($input['entorno'] === 'api-stag') {
            $url = 'https://idp.comprobanteselectronicos.go.cr/auth/realms/rut-stag/protocol/openid-connect/token';
        }else{
            $url = 'https://idp.comprobanteselectronicos.go.cr/auth/realms/rut/protocol/openid-connect/token';
        }
        $test = $this->Generar_Token($url, $string);
        return response()->json(['success'=> $test]);
    }

        public function Generar_Token($url, $string)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "".$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $string,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
                "postman-token: c1016240-cf6f-fe54-67d6-587ad9b11c39"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $respuesta = "cURL Error #:" . $err;
            return  $respuesta;
        }else{
            $response = json_decode($response);
            if (isset($response->{'error'})) {
                $respuesta = 'error '.$response->{'error_description'};
                return $respuesta;
            }else{
                $respuesta = 'bearer '.$response->{'access_token'};
                return $respuesta;
            }
        }
    }

        public function ajaxNumPedido(Request $request)
    {
        $input = $request->all();
        $consecutivo = DB::table('consecutivos')->where([
            ['idconfigfact', '=', $input['idconfigfact']],
            ['tipo_documento', '=', $input['tipo_documento']],
        ])->get();
        $numero_pedido = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        $data = [
            'numero_pedido' => $numero_pedido
        ];
        return response()->json(['success'=> $data]);
    }

        public function ajaxSerchCliente(Request $request)
    {
        $datos = $request->all();


        $cliente = Cliente::find($datos['cliente']);


            if ($cliente->num_id === 100000000) {
                $es_contado = 1;
                $cli=Cliente::where([
                    ['idconfigfact', '=', Auth::user()->idconfigfact],
                    ['num_id', '!=', '100000000'],

                ])->get();
            }else{
                $es_contado = 0;
                $cli=$cliente;
            }
            return response()->json(['success'=>true,'result'=> $es_contado,'default'=> $cli]);
    }

        public function ajaxNumAbono(Request $request)
    {
        $input = $request->all();
        $consecutivo = DB::table('consecutivos')->where([
            ['idcaja', '=', $input['idcaja']],
            ['tipo_documento', '=', $input['tipo_documento']],
        ])->get();
        $numero_abono = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        $data = [
            'numero_abono' => $numero_abono
        ];
        return response()->json(['success'=> $data]);
    }

        public function ajaxNumReceptor(Request $request)
    {
        $input = $request->all();
        $consecutivo = DB::table('consecutivos')->where([
            ['idcaja', '=', $input['idcaja']],
            ['tipo_documento', '=', $input['tipo_documento']],
        ])->get();
        $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        $codigo = Actividad::where('idconfigfact', $input['idconfigfact'])->get();
        $factor = Configuracion::find($input['idconfigfact']);
        $data = [
            'numero_factura' => $numero_factura,
            'factor' => $factor,
            'codigo_actividad'=> $codigo,
        ];
        return response()->json(['success'=> $data]);
    }

        public function ajaxPorcentajes(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['id_sales_item']);
        if ($input['prc_exo'] > $sales_item->impuesto_prc) {
            $respuesta = 1;
            $prc_a_usar = $sales_item->impuesto_prc;
        }else{
            $respuesta = 0;
            $prc_a_usar = $input['prc_exo'];
        }
        return response()->json(['success'=> $input, 'respuesta' => $respuesta, 'prc_a_usar' => $prc_a_usar]);
    }

        public function ajaxPorcentajesMasivo(Request $request)
    {
        $input = $request->all();
        $sales_item = Items_masivo::find($input['id_sales_item']);
        if ($input['prc_exo'] > $sales_item->impuesto_prc) {
            $respuesta = 1;
            $prc_a_usar = $sales_item->impuesto_prc;
        }else{
            $respuesta = 0;
            $prc_a_usar = $input['prc_exo'];
        }
        return response()->json(['success'=> $input, 'respuesta' => $respuesta, 'prc_a_usar' => $prc_a_usar]);
    }

        public function buscar_id(Request $request)
    {
        $datos = $request->all();
        $cliente = Cliente::where([
          ['num_id', '=', $datos['num_id']],
          ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        if (count($cliente) > 0) {
            $registros = 1;
            return response()->json(['success'=>true,'result'=> $registros,'default'=> $cliente]);
        }else{
            $registros = 0;
            return response()->json(['success'=>false,'result'=> $registros]);
        }
    }

        public function ajaxSerchFacelectron(Request $request)
    {
        $datos = $request->all();
        $consulta_fac = Facelectron::where([
            ['idsales', '=', $datos['idsale']]
        ])->get();
        if (count($consulta_fac) > 0) {
            $registros = 1;
            return response()->json(['success'=>false, 'result'=> $registros]);
        }
    }

        public function ajaxProducto(Request $request)
    {
        $datos = $request->all();
        $producto = Productos::find($datos['idproducto']);
        return response()->json(['success'=> $producto]);
    }

}
