<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Cliente;
use App\Sales;
use App\Sales_item;
use App\Consecutivos;
use App\Facelectron;
use App\Actividad;

use App\Cajas_user;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Container\Container;
use App\Items_exonerados;
use App\Productos;
use App\Unidades_medidas;
use DataTables;
use App\Cxcobrar;
use App\Mov_cxcobrar;
use App\Log_cxcobrar;
use Input;
use Response;
use App\Cajas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Otrocargo;
use App\ListaClientes;
use App\Listprice;


class PuntoController extends Controller
{
        public function create()
    {
         $terminos = DB::table('configuracion')->select('configuracion.*')
        ->join('users','configuracion.idconfigfact', '=', 'users.idconfigfact')
        ->where([
            ['users.id', '=', Auth::user()->id]
        ])->get();
        $hoy = date("Y-m-d");
        //dd($terminos[0]->fecha_certificado);
        if($terminos[0]->fecha_certificado <= $hoy){
             Session::flash('message', "Sus Credenciales de Hacienda estan vencidas, por favor genere nuevamente la llave criptografica y el usuario y contraseña de factura electronica y contacte al administrador al 8309-3816.");
            return redirect()->route('cajas.index');
        }
         if ($terminos[0]->status == 0){

            Session::flash('message', "CUENTA BLOQUEADA, contacta al administrador tel: 8309-3816");
            return redirect()->route('facturar.index');
        }

          if($terminos[0]->fecha_plan <= $hoy){

            Session::flash('message', "Plan Caducado por fecha final de plan, para renovar contacta al administrador tel: 8309-3816");
            return redirect()->route('cajas.index');

        }
        $barrio = $terminos[0]->barrio_emisor;
        if (strlen($barrio) < 5) {
    return redirect()->route('config.edit', $terminos[0]->idconfigfact)
                     ->with('alerta', 'Por favor configure correctamente el BARRIO en esta pantalla para poder facturar, el dato por defecto es 01, escriba su barrio, dato necesario para version 4.4');
}
         $valor = Configuracion::where('idconfigfact',  $terminos[0]->idconfigfact)->get();


        $i=0;
        foreach( $valor as $val  ){
        $valo = Sales::where('idconfigfact',$val->idconfigfact)->get();

        $i=$i+count($valo);
        }

        if($terminos[0]->docs < $i){

            Session::flash('message', "Plan Caducado por cantidad de documentos emitidos, para renovar contacta al administrador tel: 8309-3816");
            return redirect()->route('cajas.index');

        }

 $cajas = Cajas_user::with('caja')
    ->where('idusuario', Auth::id())
    ->whereHas('caja', function($query) {
        $query
              ->where('estatus', 1); // Filtramos por el estado en la tabla cajas
    })
    ->get();

        //$cajas = DB::table('caja_usuario')
        //->leftjoin('cajas', 'caja_usuario.idcaja',  'cajas.idcaja')
       // ->select('cajas.*')
        //->where('caja_usuario.idusuario', Auth::user()->id)
        //->where('cajas.idconfigfact',  Auth::user()->idconfigfact)
       // ->where('caja_usuario.estado',  1)
       // ->get();
       //  dd( Auth::user()->id);
        //dd($cajas);
        $cja = $cajas->count();
      // dd($cajas);

        if ($cja<1){

            //Session::flash('message', "Asignar una caja para el Usuario que inicio Sesión");
           // return redirect()->route('cajas.index');

        }
        $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $clientes = Cliente::where([
            ['tipo_cliente', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
       // $productos = Productos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
           $productos = Productos::where([['productos.idconfigfact', '=', Auth::user()->idconfigfact],
               ['productos.codigo_cabys', '!=', 0],
               ])->get();
//dd($cajas);
        $contado = Cliente::where('num_id', '100000000')->get();
        return view('punto.create', ['configuracion'  => $configuracion, 'clientes'  => $clientes, 'productos' => $productos, 'cajas' => $cajas, 'contado' => $contado]);
    }

        public function guardar(Request $request, Sales $model)
    {
        $datos = $request->all();
        $cajas = Cajas::find($datos['idcaja']);
        if (!is_null($datos['observaciones'])) {
            $observaciones = $datos['observaciones'];
        }else{
            $observaciones = '';
        }
        if (empty($datos['p_credito'])) {
            $p_credito = '00';
        }else{
            $p_credito = $datos['p_credito'];
        }
        // Validacion para la Orden de Pedido - Regimen simplificado activo
        if ($datos['tipo_documento'] == '96' && Auth::user()->config_u[0]->usa_op > 0) {

            $es_op = 1;
        } else {

            $es_op = 0;
        }
        if ($datos['cliente'] != '0') {
            $sales = Sales::create([
                'numero_documento' => $datos['numero_documento'],
                'tipo_documento' => $datos['tipo_documento'],
                'punto_venta' => str_pad($cajas->codigo_unico, 5, "0", STR_PAD_LEFT),
                'idcaja' => $datos['idcaja'],
                'idconfigfact' => $datos['idconfigfact'],
                'idcodigoactv' => $datos['actividad'],
                'idcliente' => $datos['cliente'],
                'tipo_moneda' => $datos['moneda'],
                'tipo_cambio' => $datos['tipo_cambio'],
                'condicion_venta' => $datos['condición_venta'],
                'p_credito' => $p_credito,
                'medio_pago' => $datos['medio_pago'],
                'referencia_pago' => $datos['referencia_pago'],
                'observaciones' => $observaciones,
                'total_serv_grab' => '0.00000',
                'total_serv_exento' => '0.00000',
                'total_serv_exonerado' => '0.00000',
                'total_mercancia_grav' => '0.00000',
                'total_mercancia_exenta' => '0.00000',
                'total_mercancia_exonerada' => '0.00000',
                'total_exento' => '0.00000',
                'total_exonerado' => '0.00000',
                'total_neto' => '0.00000',
                'total_descuento' => '0.00000',
                'total_impuesto' => '0.00000',
                'total_otros_cargos' => '0.00000',
                'total_iva_devuelto' => '0.00000',
                'total_comprobante' => '0.00000',
                'tiene_exoneracion' => $datos['existe_exoneracion'],
                'fecha_creada' => date('Y-m-d'),
                'estatus_sale' => 1,
                'es_op' => $es_op,
                'creado_por' => Auth::user()->email,
            ]);
        } else {
            Session::flash('message', "Asignar un Cliente Valido, para continuar con la factura");
            return redirect()->route('punto.create');
        }

        if ($datos['condición_venta'] === '02') {
            $cli_cxcobrar = Cxcobrar::where('idcliente', $datos['cliente'])->get();
            if (count($cli_cxcobrar) > 0) {
                $mov_cxcobrar = Mov_cxcobrar::create(
                [
                    'idcxcobrar' => $cli_cxcobrar[0]->idcxcobrar,
                    'num_documento_mov' => $datos['numero_documento'],
                    'fecha_mov' => date('Y-m-d'),
                    'monto_mov' => '0.00000',
                    'abono_mov' => '0.00000',
                    'saldo_pendiente' => '0.00000',
                    'cant_dias_pendientes' => $p_credito,
                    'estatus_mov' => 1
                ]);
            }else{
                $cxcobrar = Cxcobrar::create(
                [
                    'idcliente' => $datos['cliente'],
                    'idconfigfact' => $datos['idconfigfact'],
                    'saldo_cuenta' => '0.00000',
                    'cantidad_dias' => $p_credito
                ]);

                $mov_cxcobrar = Mov_cxcobrar::create(
                [
                    'idcxcobrar' => $cxcobrar->idcxcobrar,
                    'num_documento_mov' => $datos['numero_documento'],
                    'fecha_mov' => date('Y-m-d'),
                    'monto_mov' => '0.00000',
                    'abono_mov' => '0.00000',
                    'saldo_pendiente' => '0.00000',
                    'cant_dias_pendientes' => $p_credito,
                    'estatus_mov' => 1
                ]);
            }
            $updat  = Sales::where('idsale', $sales->idsale)->update([
                'idmovcxcobrar' => $mov_cxcobrar->idmovcxcobrar
            ]);
        }

                return redirect()->route('punto.edit', $sales->idsale);



    }
      public function edit_data($id)
    {
        $consulta_fac = Facelectron::where([
            ['idsales', '=', $id]
        ])->get();
        if (count($consulta_fac) > 0) {
            $message = 'Por favor realizar nuevamente la factura.';
            return redirect()->route('facturar.index')->withStatus(__(''.$message));
        }else{
            $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
            $sales = Sales::find($id);
            $sales_item = Sales_item::where('idsales', $id   )->get();
            $sales_item_otrocargo = Otrocargo::where('idsales', $id)->get();

            $clientes = Cliente::where([
                ['tipo_cliente', 1],
                ['idconfigfact', '=', Auth::user()->idconfigfact]
            ])->get();
            $cajas = Cajas::where([
                ['estatus', 1],
                ['idconfigfact', '=', Auth::user()->idconfigfact]
            ])->get();
            $usuario = Cliente::find($sales->idcliente);


                //$productos = Productos::where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
                    $productos = Productos::where([['productos.idconfigfact', '=', Auth::user()->idconfigfact],
               ['productos.codigo_cabys', '!=', 0],
               ])->get();


            if($configuracion[0]->usa_listaprecio > 0){

                // Consulta nueva para la listas de precio
                $lista_cli = DB::table('clientes_list_price')
                ->LeftJoin('list_price', 'clientes_list_price.idlist', '=', 'list_price.idlist')
                ->select('list_price.*')
                ->where('clientes_list_price.idcliente', '=', $sales->idcliente)
                ->get();

            } else {
                $lista_cli = [];
            }
            return view('punto.edit_data', ['configuracion'  => $configuracion, 'clientes'  => $clientes, 'productos' => $productos, 'sales' => $sales, 'sales_item' => $sales_item, 'cajas' => $cajas, 'usuario' => $usuario, 'sales_item_otrocargo' => $sales_item_otrocargo, 'lista_cli' => $lista_cli]);

        }
     }
        public function edit($id)
    {
        $consulta_fac = Facelectron::where([
            ['idsales', '=', $id]
        ])->get();
        if (count($consulta_fac) > 0) {
            $message = 'Por favor realizar nuevamente la factura.';
            return redirect()->route('facturar.index')->withStatus(__(''.$message));
        }else{
            $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
            $sales = Sales::find($id);
            $sales_item = Sales_item::where('idsales', $id   )->get();
            $sales_item_otrocargo = Otrocargo::where('idsales', $id)->get();

            $clientes = Cliente::where([
                ['tipo_cliente', 1],
                ['idconfigfact', '=', Auth::user()->idconfigfact]
            ])->get();
            $cajas = Cajas::where([
                ['estatus', 1],
                ['idconfigfact', '=', Auth::user()->idconfigfact]
            ])->get();
            $usuario = Cliente::find($sales->idcliente);


                //$productos = Productos::where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
                    $productos = Productos::where([['productos.idconfigfact', '=', Auth::user()->idconfigfact],
               ['productos.codigo_cabys', '!=', 0],
               ])->get();


            if($configuracion[0]->usa_listaprecio > 0){

                // Consulta nueva para la listas de precio
                $lista_cli = DB::table('clientes_list_price')
                ->LeftJoin('list_price', 'clientes_list_price.idlist', '=', 'list_price.idlist')
                ->select('list_price.*')
                ->where('clientes_list_price.idcliente', '=', $sales->idcliente)
                ->get();

            } else {
                $lista_cli = [];
            }
            return view('punto.edit', ['configuracion'  => $configuracion, 'clientes'  => $clientes, 'productos' => $productos, 'sales' => $sales, 'sales_item' => $sales_item, 'cajas' => $cajas, 'usuario' => $usuario, 'sales_item_otrocargo' => $sales_item_otrocargo, 'lista_cli' => $lista_cli]);

        }
    }


        public function agregarLineaFactura(Request $request)
    {
        $datos = $request->all();
        if(strstr($datos['sales_item'],',') ){
            $valores = explode(',', $datos['sales_item']);
            for ($i=0; $i < count($valores); $i++) {
                $producto = Productos::find($valores[$i]);
                $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
                $sales_item = Sales_item::create(
                    [
                        'idsales' => $datos['idsale'],
                        'idproducto' => $producto->idproducto,
                        'codigo_producto' =>  $producto->codigo_producto,
                        'nombre_producto' =>  $producto->nombre_producto,
                        'costo_utilidad' => $producto->precio_sin_imp,
                        'cantidad' => 1,
                        'valor_neto' => $producto->precio_sin_imp,
                        'valor_descuento' => 0,
                        'valor_impuesto' => $valor_imp,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'descuento_prc' => 0,
                        'existe_exoneracion' => '00'
                    ]);
            }
            return response()->json(['success'=> $datos]);
        }else{
            if (is_null($datos['cantidad'])) {
                $cantidad = 1;
                $producto = Productos::find($datos['sales_item']);
                $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
                $sales_item = Sales_item::create(
                    [
                    'idsales' => $datos['idsale'],
                    'idproducto' => $producto->idproducto,
                    'codigo_producto' =>  $producto->codigo_producto,
                    'nombre_producto' =>  $producto->nombre_producto,
                    'costo_utilidad' => $producto->precio_sin_imp,
                    'cantidad' => $cantidad,
                    'valor_neto' => $producto->precio_sin_imp,
                    'valor_descuento' => 0,
                    'valor_impuesto' => $valor_imp,
                    'tipo_impuesto' => $producto->impuesto_iva,
                    'impuesto_prc' => $producto->porcentaje_imp,
                    'descuento_prc' => 0,
                    'existe_exoneracion' => '00'
                    ]
                );
                return response()->json(['success'=> $datos]);
            }else{
                $cantidad = $datos['cantidad'];
                $producto = Productos::find($datos['sales_item']);
                $valor_neto = round(($producto->precio_sin_imp * $datos['cantidad']),5);

                $valor_impuesto = ($valor_neto * $producto->porcentaje_imp)/100;
                $sales_item = Sales_item::create(
                    [
                        'idsales' => $datos['idsale'],
                        'idproducto' => $producto->idproducto,
                        'codigo_producto' =>  $producto->codigo_producto,
                        'nombre_producto' =>  $producto->nombre_producto,
                        'costo_utilidad' => $producto->precio_sin_imp,
                        'cantidad' => $cantidad,
                        'valor_neto' => $valor_neto,
                        'valor_descuento' => 0,
                        'valor_impuesto' => $valor_impuesto,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'descuento_prc' => 0,
                        'existe_exoneracion' => $datos['existe_exoneracion']
                    ]
                );
                return response()->json(['success'=> $datos]);

            }
        }
    }
 public function update(Request $request, Sales $model)
    {

        $datos = $request->all();
        //Validacion Inicial para no permitir que pueda continuar si es op y si es cliente contado
        if ($datos['tipo_documento'] == '96') {

            if(Auth::user()->config_u[0]->usa_op > 0){

                $cliente_validacion = Cliente::find($datos['cliente']);
                if ($cliente_validacion->es_contado > 0) {

                    Session::flash('message', "El cliente no puede ser un cliente contado");
                    return redirect()->back();


                }
            }
        }
        $consulta_fac = Facelectron::where([
            ['idsales', '=', $datos['idsale']]
        ])->get();

        if (count($consulta_fac) > 0) {

            $message = 'Por favor realizar nuevamente la factura.';
            return redirect()->route('facturar.index')->withStatus(__(''.$message));

        } else {

            $cajas = Cajas::find($datos['idcaja']);

            if (!is_null($datos['observaciones'])) {

                $observaciones = $datos['observaciones'];
            } else {

                $observaciones = '';
            }



             // Paso 1: Obtener el rango de números de documento
$consecutivo = DB::table('consecutivos')->where('idcaja', $datos['idcaja'])
    ->where('tipo_documento', $datos['tipo_documento'])
    ->first();

if (!$consecutivo) {
    // Manejo de error si no se encuentra el consecutivo
    Session::flash('message', "No se encontró el consecutivo.");
    return redirect()->back();
}

$docDesde = $consecutivo->doc_desde;
$docHasta = $consecutivo->numero_documento;

// Paso 2: Obtener los números de documento emitidos en sales
$numerosEmitidos = DB::table('sales')
    ->where('idcaja', $datos['idcaja'])
    ->where('tipo_documento', $datos['tipo_documento'])
    ->where('estatus_sale', '=' , 2)
    ->pluck('numero_documento')
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
                ['idcaja', '=', $datos['idcaja']],
                ['tipo_documento', '=', $datos['tipo_documento']],
            ])->get();
            $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);

          $consecutivo_fac = DB::table('sales')->where([
    ['idcaja', '=', $datos['idcaja']],
    ['tipo_documento', '=', $datos['tipo_documento']],
    ['numero_documento', '=', $numero_factura],
    ['idsale', '!=', $datos['idsale']],
    ['estatus_sale', '=' ,2]
])->get();


if ($consecutivo_fac->isEmpty()) {
                $new = $numero_factura + 1;

                $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['tipo_documento'].' and idcaja = '.$datos['idcaja']);


}else{


    //$numero_factura=$huecos[0];
    
    $numerosEmitidos = DB::table('sales')
    ->where('idcaja', $datos['idcaja'])
    ->where('tipo_documento',  $datos['tipo_documento'])
    ->where('estatus_sale', 2)
    ->pluck('numero_documento')
    ->toArray();

// Convertir los números a enteros
$numerosEmitidosInt = array_map('intval', $numerosEmitidos);

// Obtener el número más alto
$numeroMayor = !empty($numerosEmitidosInt) ? max($numerosEmitidosInt) : null;


   $new = $numeroMayor +1;
    $numero_factura=str_pad($new, 10, "0", STR_PAD_LEFT);
   $new=$numero_factura+1;

    $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['tipo_documento'].' and idcaja = '.$datos['idcaja']);

    //Session::flash('message', "Hay un problema con el consecutivo de la factura, SOLO SE PUEDE FACTURAR CON UNA PANTALLA ACTIVA, ve a VER DOC ELEC y LIMPA FACTURA EN PROCESO o Elimina alguna de los Documentos duplicados para continuar");
    //return redirect()->back();
    // }
}
} else {


    $numero_factura=$huecos[0];

    }






            $sales  = Sales::where('idsale', $datos['idsale'])->update([
                    'numero_documento' => $numero_factura,
                    'tipo_documento' => $datos['tipo_documento'],
                    'punto_venta' => str_pad($cajas->codigo_unico, 5, "0", STR_PAD_LEFT),
                    'idcaja' => $datos['idcaja'],
                    'idconfigfact' => $datos['idconfigfact'],
                    'idcodigoactv' => $datos['actividad'],
                    'idcliente' => $datos['cliente'],
                    'tipo_moneda' => $datos['moneda'],
                    'tipo_cambio' => $datos['tipo_cambio'],
                    'condicion_venta' => $datos['condición_venta'],
                    'p_credito' => $datos['p_credito'],
                    'medio_pago' => $datos['medio_pago'],
                    'referencia_pago' => $datos['referencia_pago'],
                    'tiene_exoneracion' => $datos['existe_exoneracion'],
                    'observaciones' => $observaciones,
                    'fecha_creada' => date('Y-m-d'),
                    'estatus_sale' => 2,
                    'fecha_reenvio' => date('c'),//comentar para cambiar fecha del doc
                    'total_abonos_op' => $datos['abono_op'] ?? 0.00000,
                ]);
                 //dd($numero_factura);
            $sales_item = Sales_item::where('idsales', $datos['idsale'])->get();
            $total_neto = 0;
            $total_descuento = 0;
            $total_impuesto = 0;
            $total_comprobante = 0;
            $total_mercancia_grav = 0;
            $total_exonerado = 0;
            $total_mercancia_exonerada = 0;
            $total_iva_devuelto = 0;
            $total_serv_grab = 0;
            $total_serv_exento = 0;
            $total_serv_exonerado = 0;
            $total_mercancia_exenta = 0;
            $total_IVA_ex=0;
            $total_mercancia_no_sujeto =0;
            $total_serv_no_sujeto = 0;
            foreach ($sales_item as $s_i) {

                $producto = Productos::find($s_i->idproducto);
                $cantidad_stock = $producto->cantidad_stock - $s_i->cantidad;
                $actualizar = Productos::where('idproducto', $s_i->idproducto)->update(['cantidad_stock' => $cantidad_stock]);

                                if ($s_i->existe_exoneracion === '00') {

                    if ($producto->tipo_producto === 2) {

                        $tipos_no_grab = ['01', '11', '10']; // Agrega aquí los otros tipos de impuesto si es necesario

if (in_array($s_i->tipo_impuesto, $tipos_no_grab)) {
    if ($s_i->tipo_impuesto == '01' || $s_i->tipo_impuesto == '11') {
        $total_serv_no_sujeto += ($s_i->costo_utilidad * $s_i->cantidad);
    } elseif ($s_i->tipo_impuesto == '10') {
        $total_serv_exento += ($s_i->costo_utilidad * $s_i->cantidad);
    }
} else {
    if($s_i->existe_exoneracion == '00'){
    $total_serv_grab += ($s_i->costo_utilidad * $s_i->cantidad);
    }
}
                        $total_neto = $total_neto + $s_i->valor_neto;
                        $total_descuento = $total_descuento + $s_i->valor_descuento;
                        $total_impuesto = $total_impuesto + $s_i->valor_impuesto;

                        if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00'){

                            $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                        }

                    } else {

                    $tipos_no_grab = ['01', '11', '10']; // Agrega aquí los otros tipos de impuesto si es necesario

if (in_array($s_i->tipo_impuesto, $tipos_no_grab)) {
    if ($s_i->tipo_impuesto == '01' || $s_i->tipo_impuesto == '11') {
        $total_mercancia_no_sujeto += ($s_i->costo_utilidad * $s_i->cantidad);
    } elseif ($s_i->tipo_impuesto == '10') {
        $total_mercancia_exenta += ($s_i->costo_utilidad * $s_i->cantidad);
    }
} else {
    $total_mercancia_grav += ($s_i->costo_utilidad * $s_i->cantidad);
}
                        $total_neto = $total_neto + $s_i->valor_neto;
                        $total_descuento = $total_descuento + $s_i->valor_descuento;
                        $total_impuesto = $total_impuesto + $s_i->valor_impuesto;

                        if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00'){
                            $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                        }

                    }

                } else {

                    if ($producto->tipo_producto === 2) {

                        $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                        $imps_nto = $s_i->valor_impuesto - $items_exonerados[0]->monto_exoneracion;

                        if ($imps_nto < 0) {

                            $total_impuesto = $total_impuesto + (-1 * $imps_nto);

                        } else {

                            $total_impuesto = $total_impuesto + $imps_nto;
                        }

                        $total_exonerado = $total_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                        $total_serv_exonerado =  $total_serv_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                        $total_serv_grab = $total_serv_grab + ((1-($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc)) * $s_i->valor_neto);
                        $total_neto = $total_neto + $s_i->valor_neto;
                        $total_descuento = $total_descuento + $s_i->valor_descuento;
                        $total_IVA_ex = $total_IVA_ex  + $s_i->exo_monto;
                        if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00'){

                            $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                        }

                    } else {

                        $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                        $imps_nto = $s_i->valor_impuesto - $items_exonerados[0]->monto_exoneracion;

                        if ($imps_nto < 0) {

                            $total_impuesto = $total_impuesto + (-1 * $imps_nto);
                        } else {

                            $total_impuesto = $total_impuesto + $imps_nto;
                        }

                        $total_exonerado = $total_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                        $total_mercancia_exonerada =  $total_mercancia_exonerada + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                        $total_mercancia_grav = $total_mercancia_grav + ((1-($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc)) * $s_i->valor_neto);
                        $total_neto = $total_neto + $s_i->valor_neto;
                        $total_descuento = $total_descuento + $s_i->valor_descuento;
                        $total_IVA_ex = $total_IVA_ex  + $s_i->exo_monto;
                        if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00'){

                            $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                        }
                    }
                }
            }
            // proceso de otros cargos relacionado con el guardado LDCARRENO 27-07-21
            $total_otros_cargos = 0;
            $sales_item_otrocargo = Otrocargo::where('idsales', $datos['idsale'])->get();
            foreach ($sales_item_otrocargo as $otro) {
                $total_otros_cargos += $otro->monto_cargo;
            }
         $total_exento = $total_serv_exento + $total_mercancia_exenta;
            $total_comprobante = ((($total_mercancia_grav + $total_mercancia_exonerada + $total_mercancia_exenta + $total_serv_grab+ $total_serv_exonerado + $total_serv_exento)-$total_descuento) + $total_impuesto) - $total_iva_devuelto + $total_otros_cargos + $total_mercancia_no_sujeto + $total_serv_no_sujeto;
            $sales  = Sales::where('idsale', $datos['idsale'])->update([
                    'total_serv_grab' => $total_serv_grab,
                    'total_serv_exonerado' => $total_serv_exonerado,
                    'total_serv_exento' => $total_serv_exento,
                    'TotalServNoSujeto' => $total_serv_no_sujeto,
                    'total_mercancia_grav' => $total_mercancia_grav,
                    'total_mercancia_exenta' => $total_mercancia_exenta,
                    'total_mercancia_exonerada' => $total_mercancia_exonerada,
                    'TotalMercNoSujeta' => $total_mercancia_no_sujeto,
                    'total_exento' => $total_exento,
                    'total_iva_devuelto' => $total_iva_devuelto,
                    'total_exonerado' => $total_exonerado,
                    'total_neto' => $total_neto,
                    'total_descuento' => $total_descuento,
                    'total_impuesto' => $total_impuesto,
                    'total_comprobante' => $total_comprobante,
                    'total_IVA_ex' => $total_IVA_ex,
                    'total_otros_cargos' => $total_otros_cargos
            ]);

            if ($datos['condición_venta'] === '02') {

                $cli_cxcobrar = Cxcobrar::where('idcliente', $datos['cliente'])->get();
                if (count($cli_cxcobrar) > 0) {

                    $sumando = $cli_cxcobrar[0]->saldo_cuenta + $total_comprobante;
                    $mcxcobrar = Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $sumando]);
                    $mov_cxcobrar = Mov_cxcobrar::where('idmovcxcobrar', $datos['idmovcxcobrar'])->update([
                        'monto_mov' => $total_comprobante,
                        'saldo_pendiente' => $total_comprobante
                    ]);
                }
            }
            // Fragmento de codigo para armar la NC de Regimen Simplificado cuando sea orden de pedido
            // LDCG 06-04-22
            $search = Sales::findOrFail($datos['idsale']);
           //if ($search->viene_de_op > 0) {
            if ($search->viene_de_op > 0 and $datos['tipo_documento'] === '96' ) {

                $controller = new FacturacionController();
                $controller->armarNcOrdenPedido($search->viene_de_op);
                //Actualizo el numero del documento al orden del pedido
                $orden_update = DB::update('update sales set num_documento_convertido = '.$datos['numero_documento'].' where idsale = '.$search->viene_de_op);
                $orden_update2 = DB::update('update sales set estatus_op =1 where idsale = '.$search->viene_de_op);
            }

            if ($datos['tipo_documento'] != '96') {

                $seguridad = $this->armarSeguridad($datos['idconfigfact']);
                $pos = new PosController();
                $xml =  $pos->armarXml($datos['idsale']);
                include_once(public_path(). '/funcionFacturacion506.php');
                $facturar = Timbrar_documentos($xml, $seguridad);
                $caja = Cajas::find($datos['idcaja']);
                $new = $numero_factura + 1;
                //$consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['tipo_documento'].' and idcaja = '.$datos['idcaja']);

                if (empty($datos['cc_correo'][0])) {

                    return redirect()->action('DonwloadController@correoPos', ['idsale' => $datos['idsale']])->withStatus(__('Factura Agregada Correctamente.'));
                } else {

                    return redirect()->action('DonwloadController@correoCXml', ['idsale' => $datos['idsale'], 'copias' => $datos['cc_correo']])->withStatus(__('Factura Agregada Correctamente.'));
                }

            } else {

                $new = $numero_factura + 1;
                //$consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['tipo_documento'].' and idcaja = '.$datos['idcaja']);
                if(Auth::user()->config_u[0]->usa_op > 0){

                    $update_op = DB::update('update sales set es_op =1 where idsale = '.$datos['idsale']);
                    if(isset($datos['desea_enviarcorreo'])){

                        $update_correos = DB::update('update sales set desea_enviarcorreo =1 where idsale = '.$datos['idsale']);
                    }

                }
                return redirect()->route('facturar.imprimir', ['id' => $datos['idsale']]);

            }
        }
    }
        public function updatviejo(Request $request, Sales $model)
    {

        $datos = $request->all();
        //Validacion Inicial para no permitir que pueda continuar si es op y si es cliente contado
        if ($datos['tipo_documento'] == '96') {

            if(Auth::user()->config_u[0]->usa_op > 0){

                $cliente_validacion = Cliente::find($datos['cliente']);
                if ($cliente_validacion->es_contado > 0) {

                    Session::flash('message', "El cliente no puede ser un cliente contado");
                    return redirect()->back();


                }
            }
        }
        $consulta_fac = Facelectron::where([
            ['idsales', '=', $datos['idsale']]
        ])->get();

        if (count($consulta_fac) > 0) {

            $message = 'Por favor realizar nuevamente la factura.';
            return redirect()->route('facturar.index')->withStatus(__(''.$message));

        } else {

            $cajas = Cajas::find($datos['idcaja']);

            if (!is_null($datos['observaciones'])) {

                $observaciones = $datos['observaciones'];
            } else {

                $observaciones = '';
            }


            $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $datos['idcaja']],
                ['tipo_documento', '=', $datos['tipo_documento']],
            ])->get();
            $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);

            // nueva validacion para facturar con el mismo usuario y con la misma caja, comentado 21-06-2022 xq esta dadno error con los consecutivos
            //al abrir cajas nuevas.
           // $consulta_consecutivo = Facelectron::where([
             //   ['numdoc', '=', $datos['numero_documento']],
              //  ['tipodoc', '=', $datos['tipo_documento']],
              //  ['idconfigfact', '=', $datos['idconfigfact']]

            //])->get();
           // if (count($consulta_consecutivo) > 0) {

                $new = $numero_factura + 1;
              //  $new = str_pad($new, 10, "0", STR_PAD_LEFT);
                $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['tipo_documento'].' and idcaja = '.$datos['idcaja']);
              //  $datos['numero_documento'] = $new;
          //  }
            $sales  = Sales::where('idsale', $datos['idsale'])->update([
                    'numero_documento' => $numero_factura,
                    'tipo_documento' => $datos['tipo_documento'],
                    'punto_venta' => str_pad($cajas->codigo_unico, 5, "0", STR_PAD_LEFT),
                    'idcaja' => $datos['idcaja'],
                    'idconfigfact' => $datos['idconfigfact'],
                    'idcodigoactv' => $datos['actividad'],
                    'idcliente' => $datos['cliente'],
                    'tipo_moneda' => $datos['moneda'],
                    'tipo_cambio' => $datos['tipo_cambio'],
                    'condicion_venta' => $datos['condición_venta'],
                    'p_credito' => $datos['p_credito'],
                    'medio_pago' => $datos['medio_pago'],
                    'referencia_pago' => $datos['referencia_pago'],
                    'tiene_exoneracion' => $datos['existe_exoneracion'],
                    'observaciones' => $observaciones,
                    'fecha_creada' => date('Y-m-d'),
                    'estatus_sale' => 2,
                    'fecha_reenvio' => date('c'),//comentar para cambiar fecha del doc
                    'total_abonos_op' => $datos['abono_op'] ?? 0.00000,
                ]);
                 //dd($numero_factura);
            $sales_item = Sales_item::where('idsales', $datos['idsale'])->get();
            $total_neto = 0;
            $total_descuento = 0;
            $total_impuesto = 0;
            $total_comprobante = 0;
            $total_mercancia_grav = 0;
            $total_exonerado = 0;
            $total_mercancia_exonerada = 0;
            $total_iva_devuelto = 0;
            $total_serv_grab = 0;
            $total_serv_exento = 0;
            $total_serv_exonerado = 0;
            $total_mercancia_exenta = 0;
            $total_IVA_ex=0;
            foreach ($sales_item as $s_i) {

                $producto = Productos::find($s_i->idproducto);
                $cantidad_stock = $producto->cantidad_stock - $s_i->cantidad;
                $actualizar = Productos::where('idproducto', $s_i->idproducto)->update(['cantidad_stock' => $cantidad_stock]);

                if ($s_i->existe_exoneracion === '00') {

                    if ($producto->tipo_producto === 2) {

                        if ($s_i->tipo_impuesto != '99') {

                            $total_serv_grab = $total_serv_grab + ($s_i->costo_utilidad * $s_i->cantidad);
                        } else {

                            $total_serv_exento = $total_serv_exento + ($s_i->costo_utilidad * $s_i->cantidad);
                        }
                        $total_neto = $total_neto + $s_i->valor_neto;
                        $total_descuento = $total_descuento + $s_i->valor_descuento;
                        $total_impuesto = $total_impuesto + $s_i->valor_impuesto;

                        if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00'){

                            $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                        }

                    } else {

                        if ($s_i->tipo_impuesto != '99') {

                            $total_mercancia_grav = $total_mercancia_grav + ($s_i->costo_utilidad * $s_i->cantidad);
                        } else {

                            $total_mercancia_exenta = $total_mercancia_exenta + ($s_i->costo_utilidad * $s_i->cantidad);
                        }
                        $total_neto = $total_neto + $s_i->valor_neto;
                        $total_descuento = $total_descuento + $s_i->valor_descuento;
                        $total_impuesto = $total_impuesto + $s_i->valor_impuesto;

                        if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00'){
                            $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                        }

                    }

                } else {

                    if ($producto->tipo_producto === 2) {

                        $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                        $imps_nto = $s_i->valor_impuesto - $items_exonerados[0]->monto_exoneracion;

                        if ($imps_nto < 0) {

                            $total_impuesto = $total_impuesto + (-1 * $imps_nto);

                        } else {

                            $total_impuesto = $total_impuesto + $imps_nto;
                        }

                        $total_exonerado = $total_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                        $total_serv_exonerado =  $total_serv_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                        $total_serv_grab = $total_serv_grab + ((1-($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc)) * $s_i->valor_neto);
                        $total_neto = $total_neto + $s_i->valor_neto;
                        $total_descuento = $total_descuento + $s_i->valor_descuento;
                        $total_IVA_ex = $total_IVA_ex  + $s_i->exo_monto;
                        if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00'){

                            $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                        }

                    } else {

                        $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                        $imps_nto = $s_i->valor_impuesto - $items_exonerados[0]->monto_exoneracion;

                        if ($imps_nto < 0) {

                            $total_impuesto = $total_impuesto + (-1 * $imps_nto);
                        } else {

                            $total_impuesto = $total_impuesto + $imps_nto;
                        }

                        $total_exonerado = $total_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                        $total_mercancia_exonerada =  $total_mercancia_exonerada + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                        $total_mercancia_grav = $total_mercancia_grav + ((1-($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc)) * $s_i->valor_neto);
                        $total_neto = $total_neto + $s_i->valor_neto;
                        $total_descuento = $total_descuento + $s_i->valor_descuento;
                        $total_IVA_ex = $total_IVA_ex  + $s_i->exo_monto;
                        if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00'){

                            $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                        }
                    }
                }
            }
            // proceso de otros cargos relacionado con el guardado LDCARRENO 27-07-21
            $total_otros_cargos = 0;
            $sales_item_otrocargo = Otrocargo::where('idsales', $datos['idsale'])->get();
            foreach ($sales_item_otrocargo as $otro) {
                $total_otros_cargos += $otro->monto_cargo;
            }
            // fin del proceso
            $total_exento = $total_serv_exento + $total_mercancia_exenta;
            $total_comprobante = ((($total_mercancia_grav + $total_mercancia_exonerada + $total_mercancia_exenta + $total_serv_grab+ $total_serv_exonerado + $total_serv_exento)-$total_descuento) + $total_impuesto) - $total_iva_devuelto + $total_otros_cargos;
            $sales  = Sales::where('idsale', $datos['idsale'])->update([
                    'total_serv_grab' => $total_serv_grab,
                    'total_serv_exonerado' => $total_serv_exonerado,
                    'total_serv_exento' => $total_serv_exento,
                    'total_mercancia_grav' => $total_mercancia_grav,
                    'total_mercancia_exenta' => $total_mercancia_exenta,
                    'total_mercancia_exonerada' => $total_mercancia_exonerada,
                    'total_exento' => $total_exento,
                    'total_iva_devuelto' => $total_iva_devuelto,
                    'total_exonerado' => $total_exonerado,
                    'total_neto' => $total_neto,
                    'total_descuento' => $total_descuento,
                    'total_impuesto' => $total_impuesto,
                    'total_comprobante' => $total_comprobante,
                    'total_IVA_ex' => $total_IVA_ex,
                    'total_otros_cargos' => $total_otros_cargos
            ]);

            if ($datos['condición_venta'] === '02') {

                $cli_cxcobrar = Cxcobrar::where('idcliente', $datos['cliente'])->get();
                if (count($cli_cxcobrar) > 0) {

                    $sumando = $cli_cxcobrar[0]->saldo_cuenta + $total_comprobante;
                    $mcxcobrar = Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $sumando]);
                    $mov_cxcobrar = Mov_cxcobrar::where('idmovcxcobrar', $datos['idmovcxcobrar'])->update([
                        'monto_mov' => $total_comprobante,
                        'saldo_pendiente' => $total_comprobante
                    ]);
                }
            }
            // Fragmento de codigo para armar la NC de Regimen Simplificado cuando sea orden de pedido
            // LDCG 06-04-22
            $search = Sales::findOrFail($datos['idsale']);
           //if ($search->viene_de_op > 0) {
            if ($search->viene_de_op > 0 and $datos['tipo_documento'] === '96' ) {

                $controller = new FacturacionController();
                $controller->armarNcOrdenPedido($search->viene_de_op);
                //Actualizo el numero del documento al orden del pedido
                $orden_update = DB::update('update sales set num_documento_convertido = '.$datos['numero_documento'].' where idsale = '.$search->viene_de_op);
                $orden_update2 = DB::update('update sales set estatus_op =1 where idsale = '.$search->viene_de_op);
            }

            if ($datos['tipo_documento'] != '96') {

                $seguridad = $this->armarSeguridad($datos['idconfigfact']);
                $pos = new PosController();
                $xml =  $pos->armarXml($datos['idsale']);
                include_once(public_path(). '/funcionFacturacion506.php');
                $facturar = Timbrar_documentos($xml, $seguridad);
                $caja = Cajas::find($datos['idcaja']);
                $new = $numero_factura + 1;
                //$consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['tipo_documento'].' and idcaja = '.$datos['idcaja']);

                if (empty($datos['cc_correo'][0])) {

                    return redirect()->action('DonwloadController@correoPos', ['idsale' => $datos['idsale']])->withStatus(__('Factura Agregada Correctamente.'));
                } else {

                    return redirect()->action('DonwloadController@correoCXml', ['idsale' => $datos['idsale'], 'copias' => $datos['cc_correo']])->withStatus(__('Factura Agregada Correctamente.'));
                }

            } else {

                $new = $numero_factura + 1;
                //$consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['tipo_documento'].' and idcaja = '.$datos['idcaja']);
                if(Auth::user()->config_u[0]->usa_op > 0){

                    $update_op = DB::update('update sales set es_op =1 where idsale = '.$datos['idsale']);
                    if(isset($datos['desea_enviarcorreo'])){

                        $update_correos = DB::update('update sales set desea_enviarcorreo =1 where idsale = '.$datos['idsale']);
                    }

                }
                return redirect()->route('facturar.imprimir', ['id' => $datos['idsale']]);

            }
        }
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
                'clave_conexion' => ''.$buscar->clave_conexion, //Contraseña de hacienda
                'client_id' => $entorno //api-stag para pruebas y api-prod para el entorno produccion
            ];
            return $seguridad;
    }

        public function armarXml($idsale)
    {
        $cabecera = Sales::find($idsale);
        $detalle = Sales_item::where('idsales', $idsale)->get();
        $emisor = Configuracion::find($cabecera->idconfigfact);
        $codigo_actividad = Actividad::find($cabecera->idcodigoactv);
        $cliente = Cliente::find($cabecera->idcliente);
        $info_ref = Facelectron::where('clave', $cabecera->ref_clave_sale)->get();
        //dd($info_ref);
        $arreglo = [];
        foreach ($detalle as $cuerpo) {
            $producto = Productos::find($cuerpo->idproducto);
            $unidad_medida = Unidades_medidas::find($producto->idunidadmedida);
            $monto_total = round(($cuerpo->costo_utilidad * $cuerpo->cantidad),5) ;//$cuerpo->costo_utilidad * $cuerpo->cantidad;
           // dd($monto_total);
            switch ($cuerpo->tipo_impuesto) {
                case '01':
                    $porcentaje_imp = 0;
                break;
                case '02':
                    $porcentaje_imp = 1;
                break;
                case '03':
                    $porcentaje_imp = 2;
                break;
                case '04':
                    $porcentaje_imp = 4;
                break;
                case '05':
                    $porcentaje_imp = 0;
                break;
                case '06':
                    $porcentaje_imp = 4;
                break;
                case '07':
                    $porcentaje_imp = 8;
                break;
                case '08':
                    $porcentaje_imp = 13;
                break;
                case '09':
                    $porcentaje_imp = 0.50;
                break;
                case '99':
                    $porcentaje_imp = 0;
                break;
            }
            if ($cuerpo->existe_exoneracion === '00') {
                $estructura =
                    array(
    'PartidaArancelaria' => '' . $producto->partida_arancelaria,
    'Codigo' => '' . $producto->codigo_cabys,
    'CodigoComercial' => array(
        'Tipo' => '04', // valor 2 digitos tipo de producto Validar Anexos y estructuras NOTA # 12
        'Codigo' => '' . str_pad($cuerpo->codigo_producto, 10, "0", STR_PAD_LEFT) // Corresponde al codigo del producto en el sistema 10 digitos
    ),
    'Cantidad' => '' . $cuerpo->cantidad,
    'UnidadMedida' => '' . $unidad_medida->simbolo, // Debe ser una de las medidas expresadas en los codigos de Medidas de Hacienda
    'Detalle' => '' . $cuerpo->nombre_producto, // detalle del producto
    'reg_med' => '' . $producto->reg_med,
);

// Verificar si reg_med es mayor que 0
if ($producto->reg_med > 0) {
    $estructura  += array(

        'RegistroMedicamento' => '' . $producto->forma, // detalle del producto
        'FormaFarmaceutica' => '' . $producto->cod_reg_med, // detalle del producto
    );
}

$estructura += array(
    'PrecioUnitario' => '' . $cuerpo->costo_utilidad, // precio neto del producto por unidad
    'MontoTotal' => '' . $monto_total , // Valor neto * cantidad
    'Descuento' => array(
        array(
            'MontoDescuento' => '' . $cuerpo->valor_descuento, // valor descuento concedido si existe
            'NaturalezaDescuento' => 'Descuento por parte del operador' // especificar porque
        )
    ),
    'SubTotal' => '' . $cuerpo->valor_neto, // MontoTotal - MontoDescuento
    'BaseImponible' => '00', // importante para saber si es exoneracion o no
    'EsExoneracion' => '' . $cuerpo->existe_exoneracion, // importante para saber si es exoneracion
);
                    if ($cuerpo->tipo_impuesto != '99') {

                        $estructura['Impuesto'] = [
                                array(
                                    'Codigo' => '01', //Es un campo fijo de dos posiciones. Ver notas 7 y 8  en Anexos y Estructuras
                                    'CodigoTarifa' => ''.$cuerpo->tipo_impuesto, // Debe de expresarse en porcentaje.  decimal 4,2
                                    'Tarifa' => ''.$porcentaje_imp,
                                        //'FactorIVA'=>'0.00000',
                                         'MontoExportacion' => ''.$cuerpo->valor_impuesto,
                                    'Monto' => ''.$cuerpo->valor_impuesto // se obtiene “subtotal” * tarifa del impuesto
                                )
                            ];
                        $estructura['MontoTotalLinea'] = ''.$cuerpo->valor_neto+$cuerpo->valor_impuesto;
                    } else {

                        $estructura['MontoTotalLinea'] = ''.$cuerpo->valor_neto+$cuerpo->valor_impuesto;
                    }
                    array_push($arreglo, $estructura);
            } else {//si lleva exoneracion
                $items_exonerados = Items_exonerados::where('idsalesitem', $cuerpo->idsalesitem)->get();
                $impuesto_nto = $cuerpo->valor_impuesto - $items_exonerados[0]->monto_exoneracion;
                $estructura =
                    array(
                        'PartidaArancelaria' => ''.$producto->partida_arancelaria,
                        'reg_med'=>''.$producto->reg_med,
                        'Codigo' => ''.$producto->codigo_cabys,
                        'CodigoComercial' => array(
                            'Tipo' => '04', //valor 2 digitos tipo de producto Validar Anexos y estructuras NOTA # 12
                            'Codigo' => ''.str_pad($cuerpo->codigo_producto, 10, "0", STR_PAD_LEFT) //Corresponde al codigo del poducto en el sistema 10 digitos
                        ),
                        'Cantidad' => ''.$cuerpo->cantidad,
                        'UnidadMedida' => ''.$unidad_medida->simbolo, // Debe ser una de las medidas expresadas en los codigos de Medidas de Hacienda
                        'Detalle' => ''.$cuerpo->nombre_producto, //detalle del producto
                        'reg_med' => '' . $producto->reg_med,
                         );
                        // Verificar si reg_med es mayor que 0
if ($producto->reg_med > 0) {
    $estructura  += array(
        //'reg_med' => '' . $producto->reg_med,
        'RegistroMedicamento' => '' . $producto->forma, // detalle del producto
        'FormaFarmaceutica' => '' . $producto->cod_reg_med, // detalle del producto
    );
}

$estructura += array(
                        'PrecioUnitario' => ''.$cuerpo->costo_utilidad, // precio neto del producto por unidad
                        'MontoTotal' => ''.$monto_total,//$cuerpo->costo_utilidad * $cuerpo->cantidad, //Valor neto * cantidad
                        'Descuento'=>
                            [
                                array(
                                    'MontoDescuento' => ''.$cuerpo->valor_descuento,// valor descuento concedido si existe descuento existe naturaleza del descuento
                                    'NaturalezaDescuento' => 'Descuento por parte del operador' //si existe descuento debe especificar porque 80 Strig
                                )
                            ],
                        'SubTotal' => ''.$cuerpo->valor_neto, // MontoTotal - MontoDescuento es la nomenclatura del nodo
                        'BaseImponible' => '00', //Importante para saber si es exoneracion o no, en caso de ser exoneracion 01, de lo contrario 00
                        'EsExoneracion' => ''.$cuerpo->existe_exoneracion, //Importante para saber si es exoneracion o no, en caso de ser exoneracion 01, de lo contrario 00
                        'Impuesto' =>
                            [
                                array(
                                    'Codigo' => '01', //Es un campo fijo de dos posiciones. Ver notas 7 y 8  en Anexos y Estructuras
                                    'CodigoTarifa' => ''.$cuerpo->tipo_impuesto, // Debe de expresarse en porcentaje.  decimal 4,2
                                    'Tarifa' => ''.$porcentaje_imp,
                                    //'FactorIVA'=>'0.00000',
                                     'MontoExportacion' => ''.$cuerpo->valor_impuesto,
                                    'Monto' => ''.$cuerpo->valor_impuesto // se obtiene “subtotal” * tarifa del impuesto
                                )
                            ],
                        'Exoneracion' =>
                            array(
                                'TipoDocumento' => ''.$items_exonerados[0]->tipo_exoneracion, //Es un campo fijo de dos posiciones. Ver notas 10.1 y 7  en Anexos y Estructuras
                                'NumeroDocumento' => ''.$items_exonerados[0]->numero_exoneracion, // campo obligado cuando exista exoeracion cantidad maxima 17 digitos string
                                'NombreInstitucion' => ''.$items_exonerados[0]->institucion, // Obligado cuando es exoneracion string 100 , nombre de la institucion a exonerar
                                'FechaEmision' => $items_exonerados[0]->fecha_exoneracion.'T00:00:00-06:00', // Formato: YYYY-MM-DDThh:mi:ss[Z|(+|-)hh:mm] Ejemplo: 2016-09-26T13:00:00+06:00
                                'MontoExoneracion' => ''.$items_exonerados[0]->monto_exoneracion, //Es un número decimal compuesto por 13 enteros y 5 decimales
                                'PorcentajeExoneracion' => ''.$items_exonerados[0]->porcentaje_exoneracion //Es un número entero 3 caracteres obligado porcentaje de exoneracion
                            ),
                        'ImpuestoNeto' => ''.$impuesto_nto, // se obtiene sumando subtotal + monto impuesto
                        'MontoTotalLinea' => ''.($monto_total-$cuerpo->valor_descuento)+$impuesto_nto // se obtiene sumando subtotal + monto impuesto
                    );
                    array_push($arreglo, $estructura);
            }
            //EL ARRAY PUSH DEBE IR DENTRO TAMBIEN DEL WHILE QUE VIENE DE BASE DE DATOS ES MAS PRACTICO
        }
         if($cabecera->tipo_documento == '01' or $cabecera->tipo_documento == '08'){
        $xml = [
            'ProveedorSistemas'        => $emisor->proveedor_sistema, //Se debe indicar el numero de cedula de identificaciÛn del proveedor de sistemas
            'tipoDocumento' => ''.$cabecera->tipo_documento, // 2 digitos corresponde al tipo de documento generado visualizar anexos y estructuras
            'CodigoActividad' => ''.$codigo_actividad->codigo_actividad, // 6 string nuevo campo para la version 4.3
            'sucursal' => ''.$emisor->sucursal, // 3 digitos sucursal de donde proviene el documento para el armado de clave
            'puntoVenta' => ''.$cabecera->punto_venta, // 5 digitos punto de venta del cual se armo el documento
            'situacionComprobante' => '1', // 1 digito corresponde a la situacion del documento 1 Normal 2 Contingencia y 3 Sin internet
            'sales_id' => ''.$idsale,
            'idconfigfact' => ''.$cabecera->idconfigfact,
            'numeroFactura' => ''.$cabecera->numero_documento, //correspondiente al numero del documento en el sistema
            'fechaEmision' => date('c'), // fecha de emision del documento no mayor a 72 horas, date_default_timezone_set debe ser costa rica
            'Emisor' => array(
                'Nombre' => ''.$emisor->nombre_emisor,
                'Identificacion' => array(
                    'Tipo' => ''.$emisor->tipo_id_emisor, // Verificar Anexos y estructuras NOTA # 4 para saber los tipos
                    'Numero' => ''.$emisor->numero_id_emisor // Maximo 12 valores dependiendo el tipo se valida este campo
                ),
                'NombreComercial' => ''.$emisor->nombre_comercial, // Nombre comercial de la institucion
                'Ubicacion' => array(
                    'Provincia' => ''.$emisor->provincia_emisor, // Valor de 1 digito cuando sea menor a 0
                    'Canton' => ''.$emisor->canton_emisor, // Valor 2 digitos la ubicacion debe ser exactamente la de cuando se creo el certificado *******informacion importante*******
                    'Distrito' => ''.$emisor->distrito_emisor, // valor 2 digitos perteneciente a la ubicacion visitar ubicaciones en la documentacion de hacienda
                    'Barrio' => ''.$emisor->barrio_emisor,// Nodo necesario para 4.3 Mensaje Receptor
                    'OtrasSenas' => ''.$emisor->direccion_emisor // informacion complementaria de la direccion
                ),
                'Telefono' => array(
                    'CodigoPais' => '506', // 3 digitos verifica el codigo pais del telefono
                    'NumTelefono' => ''.$emisor->telefono_emisor //Numero telefonico del emisor
                ),
                'CorreoElectronico' => ''.$emisor->email_emisor // Correo electronico del emisor
            ),
            'EsExtranjero' => '00',
            'Receptor' => array(
                'Nombre' => ''.$cliente->nombre,
                'Identificacion' => array(
                    'Tipo' => ''.$cliente->tipo_id, // Verificar Anexos y estructuras NOTA # 4 para saber los tipos
                    'Numero' => ''.$cliente->num_id // Maximo 12 valores dependiendo el tipo se valida este campo
                ),
                'NombreComercial' => ''.$cliente->nombre_contribuyente, // Nombre comercial de la institucion
                'Ubicacion' => array(
                    'Provincia' => ''.$cliente->provincia, // Valor de 1 digito cuando sea menor a 0
                    'Canton' => ''.$cliente->canton, // Valor 2 digitos la ubicacion debe ser exactamente la de cuando se creo el certificado *******informacion importante*******
                    'Distrito' => ''.$cliente->distrito, // valor 2 digitos perteneciente a la ubicacion visitar ubicaciones en la documentacion de hacienda
                    'OtrasSenas' => ''.$cliente->direccion // informacion complementaria de la direccion
                ),
                'Telefono' => array(
                    'CodigoPais' => '506', // 3 digitos verifica el codigo pais del telefono
                    'NumTelefono' => ''.$cliente->telefono //Numero telefonico del emisor
                ),
                'CorreoElectronico' => ''.$cliente->email // Correo electronico del receptor a donde se envia la factura
            ),
            'TieneExoneracion' => ''.$cabecera->tiene_exoneracion,
            'CondicionVenta' => ''.$cabecera->condicion_venta, //2 digitos Validar Anexos y estructura nota # 5 para verificar los diferentes tipos
            'PlazoCredito' => ''.$cabecera->p_credito, //en caso de la condicion sea credito o igual a 02 debe agregar plazo credito de resto va en 0
            'MedioPago' => $cabecera->medio_pago, //2 digitos solo para factura y tiquete validar la Nota #6 de Anexos y Estructuras
            'DetalleServicio' => $arreglo,
            'ResumenFactura' => [
                'CodigoTipoMoneda' => array(
                    'CodigoMoneda' => $cabecera->tipo_moneda, //validar el documento de Monedas para saber los codigos
                    'TipoCambio' => ''.$cabecera->tipo_cambio //Valor de cambio en caso de que sea una moneda diferente al colon
                ),
                'TotalServGravados' => ''.$cabecera->total_serv_grab, //obligado cuando el servicio tenga IV (IMPUESTO)
                'TotalServExentos' => ''.$cabecera->total_serv_exento, // OBLIGADO cuando el servicio este exento de IV
                'TotalServExonerado' => ''.$cabecera->total_serv_exonerado, // Este campo será de condición obligatoria, cuando el servicio esté gravado y se preste a un cliente que goce de exoneración se debe de indicar el monto equivalente al porcentaje exonerado.
                'TotalMercanciasGravadas' => ''.$cabecera->total_mercancia_grav, //obligado cuando la mercancia tenga IV (IMPUESTO)
                'TotalMercanciasExentas' => ''.$cabecera->total_mercancia_exenta, // OBLIGADO cuando la mercancia este exento de IV
                'TotalMercExonerada' => ''.$cabecera->total_mercancia_exonerada, //Este campo será de condición obligatoria, cuando la mercancía o producto se venda a un cliente que goce de exoneración para la compra de la misma.
                'TotalGravado' => ''.$cabecera->total_mercancia_grav + $cabecera->total_serv_grab,  // se obtiene sumando TotalServGravados + TotalMercanciasGravadas
                'TotalExento' => ''.$cabecera->total_mercancia_exenta + $cabecera->total_serv_exento, // se obtiene sumando TotalServExentos + TotalMercanciasExentas
                'TotalExonerado' => ''.$cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado, //Se obtiene de la suma de los campos “total servicios exonerados de IVA” mas “total de mercancías exoneradas del IVA”.
                'TotalVenta' => ''.(($cabecera->total_mercancia_grav + $cabecera->total_serv_grab) + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento), // se obtiene sumando TotalGravado + TotalExento
                'TotalDescuentos' => ''.$cabecera->total_descuento, // se obtiene sumando todos los valores de MontoDescuento que tenga la factura
                'TotalVentaNeta' => ''.(($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta +  $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento), // se obtiene restando “total venta” “total descuento”.
                'TotalImpuesto' => ''.$cabecera->total_impuesto, // se obtiene sumando todos los montos impuestos del detalle de factura
                'TotalIVADevuelto' => ''.$cabecera->total_iva_devuelto,
                'TotalOtrosCargos'  => ''.$cabecera->total_otros_cargos,
                'TotalComprobante' => ''.(((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto) + $cabecera->total_otros_cargos // se obtiene sumando TotalVentaNeta + TotalImpuesto
            ],

        ];
         }else{
         $xml = [
            'ProveedorSistemas'        => $emisor->proveedor_sistema, //Se debe indicar el numero de cedula de identificaciÛn del proveedor de sistemas
            'tipoDocumento' => ''.$cabecera->tipo_documento, // 2 digitos corresponde al tipo de documento generado visualizar anexos y estructuras
            'CodigoActividad' => ''.$codigo_actividad->codigo_actividad, // 6 string nuevo campo para la version 4.3
            'sucursal' => ''.$emisor->sucursal, // 3 digitos sucursal de donde proviene el documento para el armado de clave
            'puntoVenta' => ''.$cabecera->punto_venta, // 5 digitos punto de venta del cual se armo el documento
            'situacionComprobante' => '1', // 1 digito corresponde a la situacion del documento 1 Normal 2 Contingencia y 3 Sin internet
            'sales_id' => ''.$idsale,
            'idconfigfact' => ''.$cabecera->idconfigfact,
            'numeroFactura' => ''.$cabecera->numero_documento, //correspondiente al numero del documento en el sistema
            'fechaEmision' => date('c'), // fecha de emision del documento no mayor a 72 horas, date_default_timezone_set debe ser costa rica //SI SE NECESITA HACER FACTURA CON FECHA ANTERIOR, COMENTAR EL UPDATE DE FECHA_REENVIO Y CAMBIAR A ''.$cabecera->fecha_reenvio,
           // 'fechaEmision' => ''.$cabecera->fecha_reenvio, // fecha de emision del documento no mayor a 72 horas, date_default_timezone_set debe ser costa rica //SI SE NECESITA HACER FACTURA CON FECHA ANTERIOR, COMENTAR EL UPDATE DE FECHA_REENVIO Y CAMBIAR A ''.$cabecera->fecha_reenvio,
            'Emisor' => array(
                'Nombre' => ''.$emisor->nombre_emisor,
                'Identificacion' => array(
                    'Tipo' => ''.$emisor->tipo_id_emisor, // Verificar Anexos y estructuras NOTA # 4 para saber los tipos
                    'Numero' => ''.$emisor->numero_id_emisor // Maximo 12 valores dependiendo el tipo se valida este campo
                ),
                'NombreComercial' => ''.$emisor->nombre_comercial, // Nombre comercial de la institucion
                'Ubicacion' => array(
                    'Provincia' => ''.$emisor->provincia_emisor, // Valor de 1 digito cuando sea menor a 0
                    'Canton' => ''.$emisor->canton_emisor, // Valor 2 digitos la ubicacion debe ser exactamente la de cuando se creo el certificado *******informacion importante*******
                    'Distrito' => ''.$emisor->distrito_emisor, // valor 2 digitos perteneciente a la ubicacion visitar ubicaciones en la documentacion de hacienda
                    'Barrio' => ''.$emisor->barrio_emisor,// Nodo necesario para 4.3 Mensaje Receptor
                    'OtrasSenas' => ''.$emisor->direccion_emisor // informacion complementaria de la direccion
                ),
                'Telefono' => array(
                    'CodigoPais' => '506', // 3 digitos verifica el codigo pais del telefono
                    'NumTelefono' => ''.$emisor->telefono_emisor //Numero telefonico del emisor
                ),
                'CorreoElectronico' => ''.$emisor->email_emisor // Correo electronico del emisor
            ),
            'EsExtranjero' => '00',

            'TieneExoneracion' => ''.$cabecera->tiene_exoneracion,
            'CondicionVenta' => ''.$cabecera->condicion_venta, //2 digitos Validar Anexos y estructura nota # 5 para verificar los diferentes tipos
            'PlazoCredito' => ''.$cabecera->p_credito, //en caso de la condicion sea credito o igual a 02 debe agregar plazo credito de resto va en 0
            'MedioPago' => $cabecera->medio_pago, //2 digitos solo para factura y tiquete validar la Nota #6 de Anexos y Estructuras
            'DetalleServicio' => $arreglo,
            'ResumenFactura' => [
                'CodigoTipoMoneda' => array(
                    'CodigoMoneda' => $cabecera->tipo_moneda, //validar el documento de Monedas para saber los codigos
                    'TipoCambio' => ''.$cabecera->tipo_cambio //Valor de cambio en caso de que sea una moneda diferente al colon
                ),
                'TotalServGravados' => ''.$cabecera->total_serv_grab, //obligado cuando el servicio tenga IV (IMPUESTO)
                'TotalServExentos' => ''.$cabecera->total_serv_exento, // OBLIGADO cuando el servicio este exento de IV
                'TotalServExonerado' => ''.$cabecera->total_serv_exonerado, // Este campo será de condición obligatoria, cuando el servicio esté gravado y se preste a un cliente que goce de exoneración se debe de indicar el monto equivalente al porcentaje exonerado.
                'TotalMercanciasGravadas' => ''.$cabecera->total_mercancia_grav, //obligado cuando la mercancia tenga IV (IMPUESTO)
                'TotalMercanciasExentas' => ''.$cabecera->total_mercancia_exenta, // OBLIGADO cuando la mercancia este exento de IV
                'TotalMercExonerada' => ''.$cabecera->total_mercancia_exonerada, //Este campo será de condición obligatoria, cuando la mercancía o producto se venda a un cliente que goce de exoneración para la compra de la misma.
                'TotalGravado' => ''.$cabecera->total_mercancia_grav + $cabecera->total_serv_grab,  // se obtiene sumando TotalServGravados + TotalMercanciasGravadas
                'TotalExento' => ''.$cabecera->total_mercancia_exenta + $cabecera->total_serv_exento, // se obtiene sumando TotalServExentos + TotalMercanciasExentas
                'TotalExonerado' => ''.$cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado, //Se obtiene de la suma de los campos “total servicios exonerados de IVA” mas “total de mercancías exoneradas del IVA”.
                'TotalVenta' => ''.(($cabecera->total_mercancia_grav + $cabecera->total_serv_grab) + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento), // se obtiene sumando TotalGravado + TotalExento
                'TotalDescuentos' => ''.$cabecera->total_descuento, // se obtiene sumando todos los valores de MontoDescuento que tenga la factura
                'TotalVentaNeta' => ''.(($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta +  $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento), // se obtiene restando “total venta” “total descuento”.
                'TotalImpuesto' => ''.$cabecera->total_impuesto, // se obtiene sumando todos los montos impuestos del detalle de factura
                'TotalIVADevuelto' => ''.$cabecera->total_iva_devuelto,
                'TotalOtrosCargos'  => ''.$cabecera->total_otros_cargos,
                'TotalComprobante' => ''.(((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto) + $cabecera->total_otros_cargos // se obtiene sumando TotalVentaNeta + TotalImpuesto
            ],

        ];

         }
       if ($cabecera->ref_clave_sale > 0) {

                       $xml['InformacionReferencia'] = [

                            'TipoDoc' => ''.$info_ref[0]->tipodoc, // Tipo de documento referencia V4.3
                            'Numero' => ''.$info_ref[0]->clave,
                            'FechaEmision' => $info_ref[0]->fechahora.'T12:12:28-06:00', // Fecha emision del documento
                            'Codigo' => '04',
                            'Razon' => ''.$cabecera->observaciones
                        ];

                      array_push($xml['InformacionReferencia']);

                   }

                         //dd($xml);
        $sales_item_otrocargo = Otrocargo::where('idsales', $idsale)->get();

        if (count($sales_item_otrocargo) > 0) {
            $xml['OtrosCargos'] = [];
            foreach ($sales_item_otrocargo as $otro) {

                $datail_otrocargo = [];
                $datail_otrocargo['TipoDocumento'] = $otro->tipo_otrocargo;

                if ($otro->tipo_otrocargo == '04') {

                    $datail_otrocargo['NumeroIdentidadTercero'] = $otro->numero_identificacion;
                    $datail_otrocargo['NombreTercero'] = $otro->nombre;

                }
                $datail_otrocargo['Detalle'] = $otro->detalle;
                if (!empty($otro->porcentaje_cargo)) {

                    $datail_otrocargo['Porcentaje'] = $otro->porcentaje_cargo;
                }

                $datail_otrocargo['MontoCargo'] = $otro->monto_cargo;
                array_push($xml['OtrosCargos'], $datail_otrocargo);
            }
        }
//dd($xml);

        return $xml;
    }

        public function actualiarCantFactura(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = $sales_item->costo_utilidad * $input['cantidad'];
        if ($sales_item->descuento_prc > 0) {
            $total_descuento = ($total_neto * $sales_item->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
        $total_neto = $total_neto - $total_descuento;
        $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;

        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['cantidad' => $input['cantidad'], 'valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento]);

        return response()->json(['success'=> $input]);
    }

        public function actualiarDescFactura(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = $sales_item->costo_utilidad * $sales_item->cantidad;
        $mto_descuento = ($total_neto * $input['porcentaje_descuento'])/100;
        $total_neto = $total_neto - $mto_descuento;
        $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;
        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $mto_descuento, 'descuento_prc' => $input['porcentaje_descuento']]);
        return response()->json(['success'=> $input]);

    }

        public function eliminarLineaFactura(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        if ($sales_item->existe_exoneracion === '01') {
            $exoneracion = Items_exonerados::where('idsalesitem',$input['idsalesitem'])->get();
            $exoneracion[0]->delete();
            $sales_item->delete();
        }else{
           $sales_item->delete();
        }

        return response()->json(['success'=> $input]);

    }

    public function eliminarLineaOtrocargo(Request $request)
    {
        $input = $request->all();
        $sales_item_otrocargo = Otrocargo::find($input['idotrocargo']);
        $sales_item_otrocargo->delete();
        return response()->json(['success'=> $input]);
    }

    public function agregarLineaOtrocargo(Request $request)
    {
        $input = $request->all();

        Otrocargo::create([
            'idsales' => $input["idsale"],
            'tipo_otrocargo' => $input["datos"]["tipo_doc_otro_cargo"],
            'numero_identificacion' => $input["datos"]["identificacion_otro_cargo"],
            'nombre' => $input["datos"]["nombre_otro_cargo"],
            'detalle' => $input["datos"]["detalle_otro_cargo"],
            'porcentaje_cargo' => $input["datos"]["porcentaje_otro_cargo"],
            'monto_cargo' => $input["datos"]["monto_otro_cargo"],
            'fecha_creado_cargo' => date("Y-m-d HH:mm:ss"),
        ]);

        return response()->json(['success'=> $input]);

    }
        public function agregarExoneracion(Request $request)
    {
        $datos = $request->all();
        $input = array();
        parse_str($datos['datos'], $input);
        $sales_item = Sales_item::find($input['idsaleitem_exo']);

        $exoneracion = Items_exonerados::create(
                    [
                        'idsalesitem' => $input['idsaleitem_exo'],
                        'tipo_exoneracion' =>  $input['tipo_exoneracion'],
                        'numero_exoneracion' =>  $input['numero_exoneracion'],
                        'institucion' => $input['institucion'],
                        'fecha_exoneracion' => $input['fecha_exoneracion'],
                        'porcentaje_exoneracion' => $input['porcentaje_exoneracion'],
                        'monto_exoneracion' => $input['monto_exoneracion'],
                    ]);
       // $actualizar = Sales_item::where('idsalesitem', $input['idsaleitem_exo'])->update(['existe_exoneracion' => '01']);
       //$actualizar = Sales_item::where('idsalesitem', $input['idsaleitem_exo'])->update(['existe_exoneracion' => '01','exo_monto' => $input['monto_exoneracion']]);
        $actualizar = Sales_item::where('idsalesitem', $input['idsaleitem_exo'])->update(['existe_exoneracion' => '01','exo_monto' => $monto_exoneracion, 'exo'=>$input['numero_exoneracion'], 'fechaex'=>$input['fecha_exoneracion']]);
        $actualizar2 = Sales::where('idsale', $sales_item->idsales)->update(['tiene_exoneracion' => '01']);
        return response()->json(['success'=> $input]);
    }

        public function traerCliente(Request $request)
    {
        $datos = $request->all();
        $cliente = Cliente::find($datos['idcliente']);
        return response()->json(['success'=> $cliente->num_id]);
    }

        public function actualiarCostoFactura(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = $input['costo_utilidad'] * $sales_item->cantidad;
        if ($sales_item->descuento_prc > 0) {
            $total_descuento = ($total_neto * $sales_item->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
        $total_neto = $total_neto - $total_descuento;
        $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;

        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento, 'costo_utilidad' => $input['costo_utilidad']]);

        return response()->json(['success'=> $input]);
    }

         public function modificarFlotante(Request $request)
    {
        $datos = $request->all();
        $input = array();
        parse_str($datos['datos'], $input);
        $sales_item = Sales_item::find($input['idsalesitem_flot']);
        $valor_neto = 0;
        $valor_impuesto = 0;
        $valor_descuento = 0;
        switch ($input['tipo_impuesto']) {
            case '01':
                $porcentaje_imp = 0;
            break;
            case '02':
                $porcentaje_imp = 1;
            break;
            case '03':
                $porcentaje_imp = 2;
            break;
            case '04':
                $porcentaje_imp = 4;
            break;
            case '05':
                $porcentaje_imp = 0;
            break;
            case '06':
                $porcentaje_imp = 4;
            break;
            case '07':
                $porcentaje_imp = 8;
            break;
            case '08':
                $porcentaje_imp = 13;
            break;
        }

        if ($input['descuento'] > 0) {

            $valor_descuento = (($input['costo_utilidad'] * $input['cantidad']) * $input['descuento'])/100;

        }

        $valor_neto = ($input['costo_utilidad'] * $input['cantidad']) - $valor_descuento;

        $valor_impuesto = ($valor_neto * $porcentaje_imp)/100;

        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem_flot'])
        ->update([
            'codigo_producto' => $input['codigo_producto'],
            'nombre_producto' => $input['nombre_producto'],
            'cantidad' => $input['cantidad'],
            'valor_neto' => $valor_neto,
            'valor_impuesto' => $valor_impuesto,
            'valor_descuento' => $valor_descuento,
            'tipo_impuesto' => $input['tipo_impuesto'],
            'descuento_prc' => $input['descuento'],
            'impuesto_prc' => $porcentaje_imp,
            'costo_utilidad' => $input['costo_utilidad']
        ]);
        return response()->json(['success'=> $input]);
    }

        public function buscarProducto(Request $request)
    {
        $datos = $request->all();

        $busqueda = explode('-', $datos['codigo_pos']);

        $producto = DB::table('productos')
        ->leftjoin('unidad_medida', 'productos.idunidadmedida', '=', 'unidad_medida.idunidadmedida')
        ->select('productos.*','unidad_medida.simbolo as usa_kg')
        ->where('productos.codigo_producto', 'like', "%".$busqueda[0]."%")

        ->where('productos.idconfigfact','=', Auth::user()->idconfigfact)
        ->get();
        return response()->json(['success'=> $producto]);
    }

        public function buscarProductoNombre(Request $request)
    {
        $datos = $request->all();
        $producto = Productos::where('nombre_producto', 'like', "%".$datos['nombre_pos']."%")->where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        return response()->json(['success'=> $producto]);
    }

        public function buscarClienteNombre(Request $request)
    {
        $datos = $request->all();
        $cliente = Cliente::where([
            ['nombre', 'like', "%".$datos['nombre_cli']."%"],
            ['tipo_cliente', '=', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return response()->json(['success'=> $cliente]);

    }

        public function editarClienteNombre(Request $request)
    {
        $datos = $request->all();
        $cliente = Cliente::where([
            ['nombre', 'like', "%".$datos['nombre_cli']."%"],
            ['tipo_cliente', '=', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        $sales = Sales::find($datos['idsale']);
        if ($sales->condicion_venta === '02') {
            if (isset($sales->idmovcxcobrar) && $sales->idmovcxcobrar > 0) {
                DB::table('mov_cxcobrar')->select('mov_cxcobrar.*')
                ->where('mov_cxcobrar.idmovcxcobrar', '=', $sales->idmovcxcobrar)->delete();
            }
            $cli_cxcobrar = Cxcobrar::where('idcliente', $cliente[0]->idcliente)->get();
            if (count($cli_cxcobrar) > 0) {
                $mov_cxcobrar = Mov_cxcobrar::create(
                [
                    'idcxcobrar' => $cli_cxcobrar[0]->idcxcobrar,
                    'num_documento_mov' => $sales->numero_documento,
                    'fecha_mov' => date('Y-m-d'),
                    'monto_mov' => '0.00000',
                    'abono_mov' => '0.00000',
                    'saldo_pendiente' => '0.00000',
                    'cant_dias_pendientes' => 0,
                    'estatus_mov' => 1
                ]);
            }else{
                $cxcobrar = Cxcobrar::create(
                [
                    'idcliente' => $cliente[0]->idcliente,
                    'idconfigfact' => $sales->idconfigfact,
                    'saldo_cuenta' => '0.00000',
                    'cantidad_dias' => 0
                ]);

                $mov_cxcobrar = Mov_cxcobrar::create(
                [
                    'idcxcobrar' => $cxcobrar->idcxcobrar,
                    'num_documento_mov' => $sales->numero_documento,
                    'fecha_mov' => date('Y-m-d'),
                    'monto_mov' => '0.00000',
                    'abono_mov' => '0.00000',
                    'saldo_pendiente' => '0.00000',
                    'cant_dias_pendientes' => 0,
                    'estatus_mov' => 1
                ]);
            }
            $updat  = Sales::where('idsale', $sales->idsale)->update([
                'idmovcxcobrar' => $mov_cxcobrar->idmovcxcobrar,
                'idcliente' => $cliente[0]->idcliente
            ]);
        }else{
            Sales::where('idsale', $datos['idsale'])->update(['idcliente' => $cliente[0]->idcliente]);
        }
        return response()->json(['success'=> $cliente]);
    }

        public function autocomplete(Request $request)
    {
        $search = $request->get('term');
        $result = DB::table('productos')
        ->leftjoin('unidad_medida', 'productos.idunidadmedida', '=', 'unidad_medida.idunidadmedida')
        ->select('productos.*','unidad_medida.simbolo as usa_kg')
        ->where('productos.codigo_producto', 'like', "%".$search."%")
        ->where('productos.idconfigfact','=', Auth::user()->idconfigfact)
        ->get();
        if(count($result) > 0){

            return response()->json($result);
        } else {

            $result = Productos::where('nombre_producto', 'LIKE', '%'. $search. '%')->where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
            return response()->json($result);
        }
    }

       public function autocomplete_nombre(Request $request)
    {
        $search = $request->get('term');
        $result = Productos::where('nombre_producto', 'LIKE', '%'. $search. '%')->where([['idconfigfact', '=', Auth::user()->idconfigfact],
        ['productos.codigo_cabys', '!=', 0]])->get();
        return response()->json($result);
    }

        public function autocomplete_cliente(Request $request)
    {
        $search = $request->get('term');
        $result = Cliente::where([
            ['nombre', 'like', "%".$search."%"],
            //['tipo_cliente', '=', 1 or 2],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return response()->json($result);
    }

     public function autocomplete_clientecla(Request $request)
    {
        $search = $request->get('term');
        $result = Facelectron::where([
            ['clave', 'like', "%".$search."%"],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return response()->json($result);
    }

    public function autocomplete_telefono(Request $request)
    {
        $search = $request->get('term');
        $result = Cliente::where([
            ['telefono', 'like', "%".$search."%"],
            ['tipo_cliente', '=', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return response()->json($result);
    }

    public function buscarTelefono(Request $request)
    {
        $datos = $request->all();
        $cliente = Cliente::where([
            ['telefono', 'like', "%".$datos['telefono']."%"],
            ['tipo_cliente', '=', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return response()->json(['success'=> $cliente]);

    }

    public function editarDireccion(Request $request)
    {
        $input = $request->all();
        $find = Sales::find($input['idsale']);
        $actualizar = Cliente::where('idcliente', $find->idcliente)->update(['direccion' => $input['direccion']]);
        return response()->json(['success'=> $find]);
    }

    public function editarObservaciones(Request $request)
    {
        $datos = $request->all();
        Sales::where('idsale', $datos['idsale'])->update(['observaciones' =>  $datos['observacion']]);
        return response()->json(['success'=> $datos]);
    }

        public function modficiarCxc(Request $request)
    {
        $input = $request->all();
        $find = Sales::find($input['idsale']);
        $dias_cxc = Cxcobrar::where('idcliente', $find->idcliente)->get('cantidad_dias');
        $total_dias = $input['dias'] + $dias_cxc[0]->cantidad_dias;
        $actualizar = Sales::where('idsale', $input['idsale'])->update(['p_credito' => $input['dias']]);
        $actualizar2 = Cxcobrar::where('idcliente', $find->idcliente)->update(['cantidad_dias' => $total_dias]);
        $actualizar3 = Mov_cxcobrar::where('idmovcxcobrar', $find->idmovcxcobrar)->update(['cant_dias_pendientes' => $input['dias']]);
        return response()->json('Success');
    }

        public function modficiarCondicion(Request $request)
    {
        $datos = $request->all();
        $sales = Sales::find($datos['idsale']);
        Sales::where('idsale', $datos['idsale'])->update(['condicion_venta' => $datos['condicion']]);
        if ($datos['condicion'] === '02') {
            $cli_cxcobrar = Cxcobrar::where('idcliente', $sales->idcliente)->get();
            if (isset($sales->idmovcxcobrar) && $sales->idmovcxcobrar > 0) {
                DB::table('mov_cxcobrar')->select('mov_cxcobrar.*')
                ->where('mov_cxcobrar.idmovcxcobrar', '=', $sales->idmovcxcobrar)->delete();
            }
            if (count($cli_cxcobrar) > 0) {
                $mov_cxcobrar = Mov_cxcobrar::create(
                [
                    'idcxcobrar' => $cli_cxcobrar[0]->idcxcobrar,
                    'num_documento_mov' => $sales->numero_documento,
                    'fecha_mov' => date('Y-m-d'),
                    'monto_mov' => '0.00000',
                    'abono_mov' => '0.00000',
                    'saldo_pendiente' => '0.00000',
                    'cant_dias_pendientes' => 0,
                    'estatus_mov' => 1
                ]);
            }else{
                $cxcobrar = Cxcobrar::create(
                [
                    'idcliente' => $sales->idcliente,
                    'idconfigfact' => $sales->idconfigfact,
                    'saldo_cuenta' => '0.00000',
                    'cantidad_dias' => 0
                ]);

                $mov_cxcobrar = Mov_cxcobrar::create(
                [
                    'idcxcobrar' => $cxcobrar->idcxcobrar,
                    'num_documento_mov' => $sales->numero_documento,
                    'fecha_mov' => date('Y-m-d'),
                    'monto_mov' => '0.00000',
                    'abono_mov' => '0.00000',
                    'saldo_pendiente' => '0.00000',
                    'cant_dias_pendientes' => 0,
                    'estatus_mov' => 1
                ]);
            }
            $updat  = Sales::where('idsale', $sales->idsale)->update([
                'idmovcxcobrar' => $mov_cxcobrar->idmovcxcobrar
            ]);
        }else{
            if (isset($sales->idmovcxcobrar) && $sales->idmovcxcobrar > 0) {
                DB::table('mov_cxcobrar')->select('mov_cxcobrar.*')
                ->where('mov_cxcobrar.idmovcxcobrar', '=', $sales->idmovcxcobrar)->delete();
            }
        }

        return response()->json(['success'=> $datos]);
    }

        public function modficiarMediopago(Request $request)
    {
        $datos = $request->all();
        Sales::where('idsale', $datos['idsale'])->update(['medio_pago' =>  $datos['medio_pago']]);
        return response()->json(['success'=> $datos]);
    }
    public function clave_ref(Request $request)
    {
        $datos = $request->all();
        Sales::where('idsale', $datos['idsale'])->update(['ref_clave_sale' =>  $datos['ref_clave']]);
        return response()->json(['success'=> $datos]);
    }

        public function modTipocambio(Request $request)
    {
        $datos = $request->all();
        Sales::where('idsale', $datos['idsale'])->update([
            'tipo_moneda' =>  $datos['moneda'],
            'tipo_cambio' =>  $datos['tipocambio']
        ]);
        return response()->json(['success'=> $datos]);
    }

        public function modficiarTipodoc(Request $request)
    {
        $datos = $request->all();
       ///esta parte es nueva del 15-11-2023, se consulta el cnsectivo para actualizarlo en bd
        $consecutivo = DB::table('consecutivos')->where([
            ['idcaja', '=', $datos['idcaja']],
            ['tipo_documento', '=', $datos['tipo_documento']],
        ])->get();
        $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        //fin 15-11-2023
        if($datos['tipo_documento'] != '96'){
            Sales::where('idsale', $datos['idsale'])->update([
                'tipo_documento' =>  $datos['tipo_documento'],
                'numero_documento' =>  $numero_factura,//esto es del 15-11-2023 se inserta el numero de doc.
                'es_op' =>  0
            ]);
        } else {

            if(Auth::user()->config_u[0]->usa_op > 0){

                Sales::where('idsale', $datos['idsale'])->update([
                    'tipo_documento' =>  $datos['tipo_documento'],
                    'es_op' =>  1
                ]);
            } else {

                Sales::where('idsale', $datos['idsale'])->update([
                    'tipo_documento' =>  $datos['tipo_documento'],
                    'es_op' =>  0
                ]);
            }
        }

        return response()->json(['success'=> $datos]);
    }

        public function modficiarConfig(Request $request)
    {
        $datos = $request->all();
        Sales::where('idsale', $datos['idsale'])->update([
            'idconfigfact' =>  $datos['idconfigfact']
        ]);
        return response()->json(['success'=> $datos]);
    }

        public function modficiarCaja(Request $request)
    {
        $datos = $request->all();
        Sales::where('idsale', $datos['idsale'])->update([
            'idcaja' =>  $datos['idcaja']
        ]);
        return response()->json(['success'=> $datos]);
    }

        public function modficiarAct(Request $request)
    {
        $datos = $request->all();
        Sales::where('idsale', $datos['idsale'])->update([
            'idcodigoactv' =>  $datos['actividad']
        ]);
        return response()->json(['success'=> $datos]);
    }
        public function modficiarRef(Request $request)
    {
        $datos = $request->all();
        Sales::where('idsale', $datos['idsale'])->update([
            'referencia_pago' =>  $datos['referencia']
        ]);
        return response()->json(['success'=> $datos]);
    }

        public function jsoncliente(Request $request)
    {
        $datos = $request->all();
        $var_cliente = json_decode($datos['cliente_hacienda']);
        //if (count($var_cliente->{'actividades'}) > 0) {

         //   $razon_social = $var_cliente->{'actividades'}[0]->{'descripcion'};
          //  $codigo_actividad = '112233';
       // } else {
            $razon_social = $var_cliente->{'nombre'};
            $codigo_actividad = '112233';
       // }
        $cliente = Cliente::create([
            'idconfigfact' => Auth::user()->idconfigfact,
            'tipo_id' => $var_cliente->{'tipoIdentificacion'},
            'num_id' => $datos['ced_receptor'],
            'nombre' => $var_cliente->{'nombre'},
            'email' => $datos['email'],
            'telefono' => $datos['telefono'],
            'distrito' => Auth::user()->config_u[0]->distrito_emisor,
            'canton' => Auth::user()->config_u[0]->canton_emisor,
            'provincia' => Auth::user()->config_u[0]->provincia_emisor,
            'direccion' => $datos['direccion'],
            'tipo_cliente' => 1,
            'razon_social' =>$razon_social,
            'nombre_contribuyente' => $var_cliente->{'nombre'},
            'codigo_actividad' => $codigo_actividad,
        ]);
        return redirect()->route('pos.create');
    }

    public function recalcularFactura(Request $request)
    {
        $datos = $request->all();
        $sales = Sales::find($datos['idsale']);
        $lista = Listprice::find($datos['idlista']);
        $sales_item = Sales_item::where('idsales', $datos['idsale'])->get();
        foreach ($sales_item as $item) {

            $sales_item = Sales_item::find($item->idsalesitem);
            $producto = Productos::find($item->idproducto);
            $total_neto = $sales_item->costo_utilidad * $sales_item->cantidad;
            // porcentaje total
            $porc_total = $item->descuento_prc + $lista->porcentaje;
            $mto_descuento = ($total_neto * $porc_total)/100;
            $total_neto = $total_neto - $mto_descuento;
            $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;
            $actualizar = Sales_item::where('idsalesitem', $item->idsalesitem)->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $mto_descuento, 'descuento_prc' => $porc_total]);

        }
        $actualizar2 = Sales::where('idsale', $datos['idsale'])->update(['uso_listaprecio' => 1, 'idlistaprecio' => $datos['idlista']]);

        return response()->json(['success'=> $datos]);
    }
}
