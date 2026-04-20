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
use App\MedioPago;
use App\User_config;
use App\Jobs\TimbrarPosSaleJob;



class PosController extends Controller
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
       // DD($terminos[0]->barrio_emisor);
        $barrio = $terminos[0]->barrio_emisor;
        if (strlen($barrio) < 5) {
    return redirect()->route('config.edit', $terminos[0]->idconfigfact)
                     ->with('alerta', 'Por favor configure correctamente el BARRIO en esta pantalla para poder facturar, el dato por defecto es 01, escriba su barrio, dato necesario para version 4.4');
}
         $valor = Configuracion::where('idconfigfact',  $terminos[0]->idconfigfact)->get();


        $i=0;
        foreach( $valor as $val  ){
             $valo = Facelectron::where([
        ['idconfigfact', $val->idconfigfact],
        ['estatushacienda', 'aceptado']
        ])->get();  
            
            
       // $valo = Sales::where('idconfigfact',$val->idconfigfact)->get();

        $i=$i+count($valo);
        }

        if($terminos[0]->docs < $i){

            Session::flash('message', "Plan Caducado por cantidad de documentos emitidos, para renovar contacta al administrador tel: 8309-3816");
            return redirect()->route('cajas.index');

        }
         $cajas = DB::table('caja_usuario')
        ->leftjoin('cajas', 'caja_usuario.idcaja', '=', 'cajas.idcaja')
        ->select('cajas.*')
        ->where('caja_usuario.idusuario', '=', Auth::user()->id)
        ->where('cajas.idconfigfact', '=', Auth::user()->idconfigfact)
        ->where('caja_usuario.estado', '=', 1)
        ->get();
 // dd(Auth::user()->id);
      //  $cajas = DB::table('caja_usuario')
        //->leftjoin('cajas', 'caja_usuario.idcaja', '=', 'cajas.idcaja')
       // ->select('cajas.*')
       // ->where('caja_usuario.idusuario', '=', Auth::user()->id)
       // ->where('caja_usuario.estado', '=', 1)
       // ->get();
        $cja = $cajas->count();
//dd($cja);
        if ($cja<1){

            Session::flash('message', "Asignar una caja para el Usuario que inicio Sesión");
            return redirect()->route('cajas.index');

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

        $contado     = Cliente::where('num_id', '100000000')->get();
        $medio_pagos = MedioPago::where('activo', 1)->get();
      
        return view('pos.create', ['configuracion'  => $configuracion, 'clientes'  => $clientes, 'productos' => $productos, 'cajas' => $cajas, 'contado' => $contado, 'medio_pagos' => $medio_pagos]);
    }

        public function editarCcCorreo(Request $request)
    {
        $datos = $request->all();

        if (!array_key_exists('idsale', $datos)) {
            return response()->json(['success' => false, 'message' => 'idsale requerido'], 422);
        }

        $rawCorreos = '';
        if (array_key_exists('cc_correo', $datos)) {
            $rawCorreos = (string) $datos['cc_correo'];
        }

        // Acepta correos separados por coma, punto y coma o espacios.
        $partes = preg_split('/[\s,;]+/', $rawCorreos, -1, PREG_SPLIT_NO_EMPTY);
        $normalizados = [];

        foreach ((array) $partes as $item) {
            $email = trim($item);
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                $normalizados[] = $email;
            }
        }

        $normalizados = array_values(array_unique($normalizados));
        $ccCorreoParaBD = implode(',', $normalizados);

        Sales::where('idsale', $datos['idsale'])->update(['cc_correo' => $ccCorreoParaBD]);

        return response()->json([
            'success' => true,
            'idsale' => $datos['idsale'],
            'cc_correo' => $ccCorreoParaBD
        ]);
    }
    
        public function guardar(Request $request, Sales $model)
    {
        $datos = $request->all();
        $producto_ml = Productos::find($datos['sales_item']);
          if (!isset($datos['monto_linea'])) {
          $monto_linea = $producto_ml->precio_sin_imp;


         }else{
              $monto_linea = $datos['monto_linea'];
         }

       // Validar que el cliente no sea null
    if (is_null($datos['cliente']) || $datos['cliente'] == '0') {
        Session::flash('message', "No se ha seleccionado un cliente. Por favor, selecciona un cliente válido.");
        return redirect()->route('pos.create'); // Redirige a la ruta deseada
    }
    
$cliente = \App\Cliente::find($datos['cliente']);

if ($cliente && $cliente->tipo_id == '05' && $datos['tipo_documento'] == '01') {
    Session::flash('message', 'Este cliente no se puede usar en factura electrónica normal (01)');
    return redirect()->route('pos.create');
}

        //dd($datos);
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
                'idcodigoactv' => $datos['actividad'] ?? 1,
                'idcliente' => $datos['cliente'],
                'tipo_moneda' => $datos['moneda'],
                'tipo_cambio' => $datos['tipo_cambio'],
                'condicion_venta' => $datos['condición_venta'],
                'p_credito' => $p_credito,
                //'medio_pago' => $datos['medio_pago'],
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
            
            //dd($datos['medio_pago']);
            if (!isset($datos['medio_pago'])) {
                $datos['medio_pago'] = [1];
            }
            $medioPagoSeleccionado = $datos['medio_pago']; // Obtener el medio de pago seleccionado

if ($medioPagoSeleccionado) {
    $sales->medioPagos()->attach($medioPagoSeleccionado); // Adjuntar el medio de pago seleccionado
}
        } else {
            Session::flash('message', "Asignar un Cliente Valido, para continuar con la factura");
            return redirect()->route('pos.create');
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

        if(strstr($datos['sales_item'],',') ){
            $valores = explode(',', $datos['sales_item']);



            for ($i=0; $i < count($valores); $i++) {
                $producto = Productos::find($valores[$i]);
                      //calculo de montos
         if (!isset($datos['es_sin_impuesto'])) {
        if (!isset($datos['monto_total'])) {
                 $precio_sin_iva = $monto_linea;
             }else{
         $precio_sin_iva = $datos['monto_total'];
             }

         } else if ($datos['es_sin_impuesto'] === 'on') {
        $precio_unitario = $monto_linea;
        $precio_sin_iva = round($precio_unitario / (1 + ($producto->porcentaje_imp / 100)), 5);
        }
                $valor_imp = ($precio_sin_iva * $producto->porcentaje_imp)/100;
                $sales_item = Sales_item::create(
                    [
                        'idsales' => $sales->idsale,
                        'idproducto' => $producto->idproducto,
                        'codigo_producto' =>  $producto->codigo_producto,
                        'nombre_producto' =>  $producto->nombre_producto,
                        'costo_utilidad' => $precio_sin_iva,
                        'cantidad' => 1,
                        'valor_neto' => $precio_sin_iva,
                        'valor_descuento' => 0,
                        'valor_impuesto' => $valor_imp,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'descuento_prc' => 0,
                        'existe_exoneracion' => $datos['existe_exoneracion']
                    ]
                );

            }
            return redirect()->route('pos.edit', $sales->idsale);
        }else{
            if (is_null($datos['cantidad_pos_envia'])) {


                $cantidad = 1;
                $producto = Productos::find($datos['sales_item']);
                      //calculo de montos
         if (!isset($datos['es_sin_impuesto'])) {
             if (!isset($datos['monto_total'])) {
                 $precio_sin_iva = $monto_linea;
             }else{
         $precio_sin_iva = $datos['monto_total'];
             }
         } else if ($datos['es_sin_impuesto'] === 'on') {
        $precio_unitario = $monto_linea;
        $precio_sin_iva = round($precio_unitario / (1 + ($producto->porcentaje_imp / 100)), 5);
        }
                $valor_imp = ($precio_sin_iva * $producto->porcentaje_imp)/100;
                $sales_item = Sales_item::create(
                    [
                        'idsales' => $sales->idsale,
                        'idproducto' => $producto->idproducto,
                        'codigo_producto' =>  $producto->codigo_producto,
                        'nombre_producto' =>  $producto->nombre_producto,
                        'costo_utilidad' => $precio_sin_iva,
                        'cantidad' => $cantidad,
                        'valor_neto' => $precio_sin_iva,
                        'valor_descuento' => 0,
                        'valor_impuesto' => $valor_imp,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'descuento_prc' => 0,
                        'existe_exoneracion' => $datos['existe_exoneracion']
                    ]
                );
                return redirect()->route('pos.edit', $sales->idsale);
            }else{
                $cantidad = $datos['cantidad_pos_envia'];
                $producto = Productos::find($datos['sales_item']);
                      //calculo de montos
         if (!isset($datos['es_sin_impuesto'])) {
        if (!isset($datos['monto_total'])) {
                 $precio_sin_iva = $monto_linea;
             }else{
         $precio_sin_iva = $datos['monto_total'];
             }

         } else if ($datos['es_sin_impuesto'] === 'on') {
        $precio_unitario = $monto_linea;
        $precio_sin_iva = round($precio_unitario / (1 + ($producto->porcentaje_imp / 100)), 5);
        }
                $valor_neto = round(($precio_sin_iva * $datos['cantidad_pos_envia']),5);
                $valor_impuesto = ($valor_neto * $producto->porcentaje_imp)/100;
                $sales_item = Sales_item::create(
                    [
                        'idsales' => $sales->idsale,
                        'idproducto' => $producto->idproducto,
                        'codigo_producto' =>  $producto->codigo_producto,
                        'nombre_producto' =>  $producto->nombre_producto,
                        'costo_utilidad' => $precio_sin_iva,
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
                return redirect()->route('pos.edit', $sales->idsale);

            }

        }

    }

    public function getMediosPago()
    {
        $mediosPago = MedioPago::all();
        return response()->json($mediosPago);
    }

    public function procesarFactura(Request $request)
    {
        $saleId = $request->input('sale_id');
        $mediosPagoMontos = $request->input('medios_pago'); // Array con medio_pago_id => monto

        foreach ($mediosPagoMontos as $medioPagoId => $monto) {
            DB::table('medio_pago_sale')
                ->where('sale_id', $saleId)
                ->where('medio_pago_id', $medioPagoId)
                ->update(['monto' => $monto]);
        }

        return response()->json(['message' => 'Factura procesada correctamente']);
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
            $medio_pagos = MedioPago::where('activo', 1)->get();
            $selectedMediosPago = $sales->medioPagos; // Obtén los medios de pago seleccionados para esta venta

            return view('pos.edit', ['configuracion'  => $configuracion, 'clientes'  => $clientes, 'productos' => $productos, 'sales' => $sales, 'sales_item' => $sales_item, 'cajas' => $cajas, 'usuario' => $usuario, 'sales_item_otrocargo' => $sales_item_otrocargo, 'lista_cli' => $lista_cli, 'medio_pagos' => $medio_pagos, 'selectedMediosPago' => $selectedMediosPago]);

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
       //dd ($datos['codigo_actividad']);
       
       $cliente = \App\Cliente::find($datos['cliente']);

if ($cliente && $cliente->tipo_id == '05' && $datos['tipo_documento'] == '01') {
    Session::flash('message', 'Este cliente no se puede usar en factura electrónica normal (01)');
    return redirect()->route('pos.create');
}

        
         if (!in_array($datos['tipo_documento'], ['04', '09'])) {
            
           if(is_null($datos['codigo_actividad'])){
            Session::flash('message', "El código de la actividad ligado al cliente no es válido, por favor revisar.");
        return redirect()->route('pos.edit', $datos['idsale']);
        }
        
        if ($datos['tipo_documento'] == '01') {
    $Cliente_01 = Cliente::find($datos['cliente']);

    // Verificar que el cliente existe y tiene código de actividad válido
    $codigoActividad = isset($Cliente_01->codigo_actividad) ? $Cliente_01->codigo_actividad : null;

    
}

       $codigoActividad = (string) $codigoActividad; // normalizar a cadena

$invalidCodes = ['0', '112233']; // u otros códigos prohibidos

if (strlen($codigoActividad) < 5 || in_array($codigoActividad, $invalidCodes, true)) {
    Session::flash('message', "El código de la actividad ligado al cliente no es válido, por favor revisar.");
    return redirect()->route('pos.edit', $datos['idsale']);
}
    
    
        }
        
        
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

            $ventaActual = Sales::select('numero_documento', 'estatus_sale')->where('idsale', $datos['idsale'])->first();

            if (!empty($ventaActual) && (int) $ventaActual->estatus_sale === 2 && !empty($ventaActual->numero_documento)) {
                // Reutiliza el consecutivo actual para evitar huecos en reintentos de la misma venta.
                $numero_factura = $ventaActual->numero_documento;
            } else {
                // Asignacion de consecutivo con bloqueo para evitar colisiones entre sesiones concurrentes.
                $numero_factura = null;
                try {
                    DB::transaction(function () use (&$numero_factura, $datos) {
                        $consecutivo = DB::table('consecutivos')
                            ->where('idcaja', $datos['idcaja'])
                            ->where('tipo_documento', $datos['tipo_documento'])
                            ->lockForUpdate()
                            ->first();

                        if (!$consecutivo) {
                            throw new \RuntimeException('No se encontró el consecutivo.');
                        }

                        $actual = (int) $consecutivo->numero_documento;

                        // Evita reutilizar numero si ya existe un documento emitido con ese consecutivo.
                        while (true) {
                            $candidate = str_pad($actual, 10, '0', STR_PAD_LEFT);
                            $existeEmitido = DB::table('sales')
                                ->where('idcaja', $datos['idcaja'])
                                ->where('tipo_documento', $datos['tipo_documento'])
                                ->where('numero_documento', $candidate)
                                ->where('idsale', '!=', $datos['idsale'])
                                ->where('estatus_sale', 2)
                                ->exists();

                            if (!$existeEmitido) {
                                $numero_factura = $candidate;
                                break;
                            }

                            $actual++;
                        }

                        DB::table('consecutivos')
                            ->where('idconsecutivo', $consecutivo->idconsecutivo)
                            ->update(['numero_documento' => $actual + 1]);
                    });
                } catch (\Throwable $e) {
                    Session::flash('message', 'No se pudo reservar el consecutivo. Intente nuevamente.');
                    return redirect()->back();
                }
            }

            $hasExoneracion = Sales_item::where('idsales', $datos['idsale'])
                ->where('existe_exoneracion', '01')
                ->exists();

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
                    //'medio_pago' => $datos['medio_pago'],
                    'referencia_pago' => $datos['referencia_pago'],
                    'tiene_exoneracion' => $hasExoneracion ? '01' : '00',
                    'observaciones' => $observaciones,
                    //'fecha_creada' => date('Y-m-d'),//comentar para cambiar fecha del doc
                    'estatus_sale' => 2,
                    'fecha_reenvio' => date('c'),//comentar para cambiar fecha del doc
                    'total_abonos_op' => $datos['abono_op'] ?? 0.00000,
                ]);
                // $sales->medioPagos()->sync($request->input('medio_pago'));
                //dd($numero_factura);
                $mediosPagoMontos = json_decode($request->input('medios_pago'), true);
                if (!is_array($mediosPagoMontos)) {
                    return response()->json(['error' => 'Invalid payment data.'], 400);
                }

                // Preparar un arreglo para sync
                $syncData = [];

                foreach ($mediosPagoMontos as $medioPagoId => $data) {
                    $monto = $data['monto'] ?? null;
                    $referencia = $data['referencia'] ?? null;

                    // Validar que el monto es correcto
                    if (is_null($monto) || !is_numeric($monto)) {
                        return response()->json(['error' => 'Invalid amount for payment method ' . $medioPagoId], 400);
                    }

                    // Construir el arreglo para sincronizar
                    $syncData[$medioPagoId] = [
                        'monto' => $monto,
                        'referencia' => $referencia, // Si quieres almacenar referencia también
                    ];
                }

                // Usar el método sync para actualizar la relación
                $update_sale = Sales::find($datos['idsale']); // Asegúrate de que $saleId sea válido  
                $update_sale->medioPagos()->sync($syncData); // Sincroniza los medios de pago

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
            $total_serv_no_sujeto =0;
            $total_mercancia_exenta = 0;
            $total_mercancia_no_sujeto = 0;
            $total_IVA_ex=0;
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
            // fin del proceso
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

                    $idMovCxcobrar = isset($datos['idmovcxcobrar']) ? $datos['idmovcxcobrar'] : null;
                    if (empty($idMovCxcobrar)) {
                        $ventaActual = Sales::find($datos['idsale']);
                        if (!empty($ventaActual) && !empty($ventaActual->idmovcxcobrar)) {
                            $idMovCxcobrar = $ventaActual->idmovcxcobrar;
                        }
                    }

                    if (empty($idMovCxcobrar)) {
                        $nuevoMov = Mov_cxcobrar::create([
                            'idcxcobrar' => $cli_cxcobrar[0]->idcxcobrar,
                            'num_documento_mov' => $numero_factura,
                            'fecha_mov' => date('Y-m-d'),
                            'monto_mov' => '0.00000',
                            'abono_mov' => '0.00000',
                            'saldo_pendiente' => '0.00000',
                            'cant_dias_pendientes' => !empty($datos['p_credito']) ? $datos['p_credito'] : 0,
                            'estatus_mov' => 1
                        ]);

                        $idMovCxcobrar = $nuevoMov->idmovcxcobrar;
                        Sales::where('idsale', $datos['idsale'])->update(['idmovcxcobrar' => $idMovCxcobrar]);
                    }

                    $sumando = $cli_cxcobrar[0]->saldo_cuenta + $total_comprobante;
                    $mcxcobrar = Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $sumando]);
                    $mov_cxcobrar = Mov_cxcobrar::where('idmovcxcobrar', $idMovCxcobrar)->update([
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

                TimbrarPosSaleJob::dispatch($datos['idsale'], $datos['idconfigfact']);
                //$consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['tipo_documento'].' and idcaja = '.$datos['idcaja']);

                if (empty($datos['cc_correo'][0])) {

                    return redirect()->action('DonwloadController@correoPos', ['idsale' => $datos['idsale']])->withStatus(__('Factura Agregada Correctamente.'));
                } else {

                    return redirect()->action('DonwloadController@correoCXml', ['idsale' => $datos['idsale'], 'copias' => $datos['cc_correo']])->withStatus(__('Factura Agregada Correctamente.'));
                }

            } else {

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

    public function armarXml(int $idsale)
    {
        $cabecera         = Sales::with('medioPagos')->find($idsale);
        $detalle          = $cabecera->items()->get();
        $emisor           = $cabecera->configuracion;
        $codigo_actividad = $cabecera->actividad;
        $cliente          = $cabecera->cliente;
        $info_ref         = Facelectron::where('clave', $cabecera->ref_clave_sale)->get();
        $desglose_impuesto= [];
        $arreglo = [];
        foreach ($detalle as $cuerpo) {
            $producto       = $cuerpo->producto;
            $unidad_medida  = $producto->unidad;
            $monto_total    = round(($cuerpo->costo_utilidad * $cuerpo->cantidad),5);
            $porcentaje_imp = $cuerpo->porcentaje_impuesto;
            $estructura = [
                'CodigoCABYS'        => (string)$producto->codigo_cabys,
                'CodigoComercial'    => [
                    'Tipo'           => '04',
                    'Codigo'         => $cuerpo->codigo_producto
                ],
                'Cantidad'           => (string)$cuerpo->cantidad,
                'UnidadMedida'       => (string)$unidad_medida->simbolo,
                'Detalle'            => (string)$cuerpo->nombre_producto,
                'reg_med'            => (string)$producto->reg_med,
            ];
            if (!is_null($producto->partida_arancelaria)) {
                if ($producto->tipo_producto === 2) {
                $estructura['PartidaArancelaria'] = 0;
                }else{
                   $estructura['PartidaArancelaria'] = (string)$producto->partida_arancelaria;
                }
            }
            // Verificar si reg_med es mayor que 0
            if ($producto->reg_med > 0) {
                $estructura['RegistroMedicamento'] = (string)$producto->forma;
                $estructura['FormaFarmaceutica']   = (string)$producto->cod_reg_med;
            }
            $estructura['PrecioUnitario'] = (string)$cuerpo->costo_utilidad;
            $estructura['MontoTotal']     = (string)$monto_total;

            //Si existe descuento agrego el nodo
            if ($cuerpo->valor_descuento > 0) {
                $estructura['Descuento']      = [
                    [
                        'CodigoDescuento'     => '09',
                        'MontoDescuento'      => (string)$cuerpo->valor_descuento,
                        'NaturalezaDescuento' => 'Descuento por parte del operador'
                    ]
                ];
            }

            $estructura['SubTotal']      = (string)$cuerpo->valor_neto;
            if ($cabecera->tipo_documento != '09') {
                $estructura['BaseImponible'] = (string)$cuerpo->valor_neto;
            }
            $estructura['EsExoneracion'] = (string)$cuerpo->existe_exoneracion;
            if ($cuerpo->tipo_impuesto != '99') {
                $estructura['Impuesto'] = [
                    [
                        'Codigo' => '01',
                        'CodigoTarifaIVA' => (string)$cuerpo->tipo_impuesto,
                        'Tarifa' => $porcentaje_imp . '.00',
                        'Monto' => (string)$cuerpo->valor_impuesto,
                    ]
                ];

                // Agregar 'MontoExportacion' solo si se cumple la condición
                if ($cabecera->tipo_documento == '09') {
                    $estructura['Impuesto'][0]['MontoExportacion'] = (string)$cuerpo->valor_impuesto;
                }
                if ($cuerpo->existe_exoneracion == '00') {
                    array_push($desglose_impuesto, [
                    'Codigo'             => '01',
                    'CodigoTarifaIVA'    => (string)$cuerpo->tipo_impuesto,
                    'TotalMontoImpuesto' => (string)$cuerpo->valor_impuesto
                    ]);
                }else{
                     array_push($desglose_impuesto, [
                    'Codigo'             => '01',
                    'CodigoTarifaIVA'    => (string)$cuerpo->tipo_impuesto,
                    'TotalMontoImpuesto' => $cuerpo->valor_impuesto - ($cuerpo->exoneracion ? $cuerpo->exoneracion->monto_exoneracion : 0)
                    ]);
                    
                }
            }

            if ($cuerpo->existe_exoneracion != '00') {
                // Calcular el impuesto neto
                $impuesto_nto = $cuerpo->valor_impuesto - ($cuerpo->exoneracion ? $cuerpo->exoneracion->monto_exoneracion : 0);

                // Estructura de exoneración
                $estructura['Exoneracion'] = [
                    'TipoDocumentoEX1'      => (string) $cuerpo->exoneracion->tipo_exoneracion,
                    'NumeroDocumento'       => (string) $cuerpo->exoneracion->numero_exoneracion,
                    'Inciso'                => (string) $cuerpo->exoneracion->inciso,
                    'NombreInstitucion'     => (string) $cuerpo->exoneracion->institucion,
                    'FechaEmisionEX'        => (string) $cuerpo->exoneracion->fecha_exoneracion . 'T00:00:00-06:00', // Formato de fecha
                    'MontoExoneracion'      => (string) $cuerpo->exoneracion->monto_exoneracion,
                    'TarifaExonerada'       => (string) $cuerpo->exoneracion->porcentaje_exoneracion
                ];

                $codigos_exoneracion_permitidos = ['02','03', '06', '07', '08'];
				if (in_array($cuerpo->exoneracion->tipo_exoneracion, $codigos_exoneracion_permitidos)) {
                    $estructura['Exoneracion']['Articulo'] = (string) $cuerpo->exoneracion->articulo;
                }

                //Validacion para cuando es 99 el tipo de exoneracion
                if ($cuerpo->exoneracion->tipo_exoneracion == '99') {
                    $estructura['Exoneracion']['TipoDocumentoOTRO'] = $cuerpo->exoneracion->tipo_exoneracion_otro;
                }

                //Validacion para cuando institucion es 99
                if ($cuerpo->exoneracion->institucion == '99') {
                    $estructura['Exoneracion']['NombreInstitucionOtros'] = $cuerpo->exoneracion->institucion_otro;
                }
                // Cálculo del Monto Total
                $estructura['MontoTotalLinea']  = (string)(($monto_total - $cuerpo->valor_descuento) + $impuesto_nto);
                $estructura['ImpuestoNeto']     = (string) $impuesto_nto;

            } else {
                // Cálculo para MontoTotalLinea
                $estructura['MontoTotalLinea'] = (string)($cuerpo->valor_neto + $cuerpo->valor_impuesto);
                $estructura['ImpuestoNeto']    = (string) $cuerpo->valor_impuesto;
            }
            if ($cabecera->tipo_documento != '09' && $cabecera->tipo_documento != '08') {
                $estructura['ImpuestoAsumidoEmisorFabrica'] = '0.00000';
            }
            array_push($arreglo, $estructura);
        }

        //Hay que modificar por la interaccion de medio de pago #TODO:falta por hacer dinamico
        $medio_pago = [];
        if($cabecera->medioPagos->count()>0){
        $cantidad_medio_pago = $cabecera->medioPagos->count();
        
        $monto_total_medio   = (((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto) + $cabecera->total_otros_cargos + $cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta;
        //$monto_mp            = $monto_total_medio / $cantidad_medio_pago;
        foreach ($cabecera->medioPagos()->get() as $medioPago) {
            $medios = [
                'TipoMedioPago'  => (string) $medioPago->codigo,
                'TotalMedioPago' => (string) $medioPago->pivot->monto,
            ];
            array_push($medio_pago,$medios);
        }
        }else{
             $medios = [
            'TipoMedioPago'  => ''.$cabecera->medio_pago,
            'TotalMedioPago' => ''.(((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto) + $cabecera->total_otros_cargos + $cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta,
        ];
        array_push($medio_pago,$medios);
        }

        //fin de medios de pago
        $xml = [
            'ProveedorSistemas'        => (string) $emisor->proveedor_sistema, // Se debe indicar el numero de cedula de identificacion del proveedor de sistemas
        ];

        // Agregar el código de actividad condicionalmente
        if ($cabecera->tipo_documento == '08') {
            $xml['CodigoActividadReceptor'] = (string) $codigo_actividad->codigo_actividad; // Modificacion en la version 4.4 de etiqueta
        } else {
            $xml['CodigoActividadEmisor'] = (string) $codigo_actividad->codigo_actividad; // Modificacion en la version 4.4 de etiqueta
             $xml['CodigoActividadReceptor'] = (string) $cliente->codigo_actividad; // Modificacion en la version 4.4 de etiqueta
        }

        // Continuar agregando otros elementos al mismo array
        $xml += [
            'tipoDocumento'            => (string) $cabecera->tipo_documento,
            'sucursal'                 => (string) $emisor->sucursal,
            'puntoVenta'               => (string) $cabecera->punto_venta,
            'situacionComprobante'     => (string) $cabecera->situacion, // 1 digito corresponde a la situacion del documento 1 Normal 2 Contingencia y 3 Sin internet
            'sales_id'                 => (string) $idsale,
            'idconfigfact'             => (string) $cabecera->idconfigfact,
            'numeroFactura'            => (string) $cabecera->numero_documento,
            'fechaEmision'             => (string) $cabecera->fecha_reenvio,];
            if ($cabecera->tipo_documento != '08') {
                $xml += [
                    'Emisor'                   => [
                        'Nombre'               => (string) $emisor->nombre_emisor,
                        'Identificacion'       => [
                            'Tipo'             => (string) $emisor->tipo_id_emisor,
                            'Numero'           => (string) $emisor->numero_id_emisor
                        ],
                        'NombreComercial'      => (string) $emisor->nombre_comercial,
                        'Ubicacion'            => [
                            'Provincia'        => (string) $emisor->provincia_emisor,
                            'Canton'           => (string) $emisor->canton_emisor,
                            'Distrito'         => (string) $emisor->distrito_emisor,
                            'Barrio'           => (string) $emisor->barrio_emisor,
                            'OtrasSenas'       => (string) $emisor->direccion_emisor
                        ],
                        'Telefono'             => [
                            'CodigoPais'       => '506',
                            'NumTelefono'      => (string) $emisor->telefono_emisor
                        ],
                        'CorreoElectronico'    => [$emisor->email_emisor]
                    ]
                ];
                $xml['EsExtranjero']       ='00';
                //Comienzo de validacion para documentos
                if($cabecera->tipo_documento == '01' or $cabecera->tipo_documento == '08' or $cabecera->tipo_documento == '09'){
                    $xml['Receptor' ]          = [
                            'Nombre'               => (string) $cliente->nombre,
                            'Identificacion'       => [
                                'Tipo'             => (string) $cliente->tipo_id,
                                'Numero'           => (string) $cliente->num_id
                            ],
                            'NombreComercial'      => (string) $cliente->nombre_contribuyente,
                            'Telefono' => [
                                'CodigoPais'       => '506',
                                'NumTelefono'      => (string) $cliente->telefono
                            ],
                            'CorreoElectronico'    => (string) $cliente->email
                    ];
                    // Extranjero No domiciliado
                    if ($cliente->tipo_id !== '05') {
                        $xml['Receptor']['Ubicacion'] = [
                            'Provincia'        => (string) $cliente->provincia,
                            'Canton'           => (string) $cliente->canton,
                            'Distrito'         => (string) $cliente->distrito,
                            'OtrasSenas'       => (string) $cliente->direccion
                        ];
                    } else {
                        $xml['Receptor']['OtrasSenasExtranjero'] = (string) $cliente->direccion;
                    }
                }
            } else {//si es tipo doc 08
                $xml['Emisor' ]          = [
                    'Nombre'               => (string) $cliente->nombre,
                    'Identificacion'       => [
                        'Tipo'             => (string) $cliente->tipo_id,
                        'Numero'           => (string) $cliente->num_id
                    ],
                    'NombreComercial'      => (string) $cliente->nombre_contribuyente,
                    'Telefono' => [
                        'CodigoPais'       => '506',
                        'NumTelefono'      => (string) $cliente->telefono
                    ],
                    'CorreoElectronico'    => [$emisor->email_emisor]
                ];
                if ($cliente->tipo_id !== '05') {
                        $xml['Emisor']['Ubicacion'] = [
                            'Provincia'        => (string) $cliente->provincia,
                            'Canton'           => (string) $cliente->canton,
                            'Distrito'         => (string) $cliente->distrito,
                            'OtrasSenas'       => (string) $cliente->direccion
                        ];
                    } else {
                        $xml['Emisor']['OtrasSenasExtranjero'] = (string) $cliente->direccion;
                    }
                    
                $xml += [
                    'Receptor'                   => [
                        'Nombre'               => (string) $emisor->nombre_emisor,
                        'Identificacion'       => [
                            'Tipo'             => (string) $emisor->tipo_id_emisor,
                            'Numero'           => (string) $emisor->numero_id_emisor
                        ],
                        'NombreComercial'      => (string) $emisor->nombre_comercial,
                        'Ubicacion'            => [
                            'Provincia'        => (string) $emisor->provincia_emisor,
                            'Canton'           => (string) $emisor->canton_emisor,
                            'Distrito'         => (string) $emisor->distrito_emisor,
                            'Barrio'           => (string) $emisor->barrio_emisor,
                            'OtrasSenas'       => (string) $emisor->direccion_emisor
                        ],
                        'Telefono'             => [
                            'CodigoPais'       => '506',
                            'NumTelefono'      => (string) $emisor->telefono_emisor
                        ],
                        'CorreoElectronico'    => $emisor->email_emisor
                    ]

                ];
                $xml['EsExtranjero']       ='00';

            }
            $xml['TieneExoneracion']    = (string) $cabecera->tiene_exoneracion;
            $xml['CondicionVenta']      = (string) $cabecera->condicion_venta;
            $xml['PlazoCredito']        = (string) $cabecera->p_credito;
            $xml['DetalleServicio']     = $arreglo;
            $xml['ResumenFactura'] = [
                'CodigoTipoMoneda' => [
                    'CodigoMoneda' => (string) $cabecera->tipo_moneda,
                    'TipoCambio' => $cabecera->tipo_moneda == 'CRC' ? '1.00' : (string) $cabecera->tipo_cambio
                ],
                'TotalServGravados' => (string) $cabecera->total_serv_grab,
                'TotalServExentos' => (string) $cabecera->total_serv_exento,
                'TotalServExonerado' => (string) $cabecera->total_serv_exonerado,
            ];

            // Validación TotalServNoSujeto
            if ($cabecera->tipo_documento != '09') {
                $xml['ResumenFactura']['TotalServNoSujeto']   = (string) $cabecera->TotalServNoSujeto;
            }

            $xml['ResumenFactura']['TotalMercanciasGravadas'] = (string) $cabecera->total_mercancia_grav;
            $xml['ResumenFactura']['TotalMercanciasExentas']  = (string) $cabecera->total_mercancia_exenta;
            $xml['ResumenFactura']['TotalMercExonerada']      = (string) $cabecera->total_mercancia_exonerada;

            // Validación TotalMercNoSujeta
            if ($cabecera->tipo_documento != '09') {
                $xml['ResumenFactura']['TotalMercNoSujeta'] = (string) $cabecera->TotalMercNoSujeta;
            }

            $xml['ResumenFactura']['TotalGravado'] = (string) ($cabecera->total_mercancia_grav + $cabecera->total_serv_grab);
            $xml['ResumenFactura']['TotalExento'] = (string) ($cabecera->total_mercancia_exenta + $cabecera->total_serv_exento);

            if ($cabecera->tipo_documento != '09') {
                $xml['ResumenFactura']['TotalExonerado'] = (string) ($cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado);
                $xml['ResumenFactura']['TotalNoSujeto'] = (string) ($cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta);

            }
            if ($cabecera->tipo_documento != '09' && $cabecera->tipo_documento != '08') {
                $xml['ResumenFactura']['TotalImpAsumEmisorFabrica'] = '0.00000'; // Nuevo campo para 4.4
            }

            $xml['ResumenFactura']['TotalVenta'] = (string) (
                ($cabecera->total_mercancia_grav + $cabecera->total_serv_grab) +
                ($cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento + $cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta)
            );

            $xml['ResumenFactura']['TotalDescuentos'] = (string) $cabecera->total_descuento;
            $xml['ResumenFactura']['TotalVentaNeta'] = (string) (
                ($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento) -
                $cabecera->total_descuento +
                $cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta
            );

            $xml['ResumenFactura']['TotalImpuesto'] = (string) $cabecera->total_impuesto;
            $xml['ResumenFactura']['TotalIVADevuelto'] = (string) $cabecera->total_iva_devuelto;
            $xml['ResumenFactura']['TotalOtrosCargos'] = (string) $cabecera->total_otros_cargos;
            $xml['ResumenFactura']['MedioPago'] = $medio_pago;
            $xml['ResumenFactura']['TotalDesgloseImpuesto'] = $desglose_impuesto;

            $xml['ResumenFactura']['TotalComprobante'] = (string) (
                (($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento) - $cabecera->total_descuento) +
                $cabecera->total_impuesto -
                $cabecera->total_iva_devuelto +
                $cabecera->total_otros_cargos +
                $cabecera->TotalServNoSujeto +
                $cabecera->TotalMercNoSujeta
            );
            if ($cabecera->ref_clave_sale > 0) {

                $xml['InformacionReferencia'] = [
                    'TipoDocIR' => ''.$info_ref[0]->tipodoc, // Tipo de documento referencia V4.3
                    'Numero' => ''.$info_ref[0]->clave,
                    'FechaEmisionIR' => $info_ref[0]->fechahora.'T12:12:28-06:00', // Fecha emision del documento
                    'Codigo' => '04',
                    'Razon' => ''.$cabecera->observaciones
                ];
                array_push($xml['InformacionReferencia']);
            }
            if ($cabecera->tipo_documento == '08') {
                 if ($cliente->tipo_id !== '05') {
                $xml['InformacionReferencia'] = [
                    'TipoDocIR' => ''.$cabecera->TipoDocIR, // Tipo de documento referencia V4.3
                    'Numero' => ''.$cabecera->referencia_compra,
                    'FechaEmisionIR' => $cabecera->fecha_creada.'T12:12:28-06:00', // Fecha emision del documento
                    'Codigo' => '04',
                    'Razon' => 'Compras realizadas a proveedor del Régimenes Especiales'
                ];
                $xml['Otros'] = ['OtroTexto' => mb_substr($cabecera->observaciones, 0, 178, 'UTF-8')];
            }else{
                 $xml['InformacionReferencia'] = [
                    'TipoDocIR' => '16', // Tipo de documento referencia V4.3
                    'Numero' => ''.$cabecera->referencia_compra,
                    'FechaEmisionIR' => $cabecera->fecha_creada.'T12:12:28-06:00', // Fecha emision del documento
                    'Codigo' => '04',
                    'Razon' => 'Compras realizadas a en el extranjero.'
                ];
                 $xml['Otros'] = ['OtroTexto' => mb_substr($cabecera->observaciones, 0, 178, 'UTF-8')];
            }
            
            }

             
                $sales_item_otrocargo = Otrocargo::where('idsales', $idsale)->get();
            if (count($sales_item_otrocargo) > 0) {
                $xml['OtrosCargos'] = [];
                foreach ($sales_item_otrocargo as $otro) {
                    $datail_otrocargo = [];
                    $datail_otrocargo['TipoDocumentoOC']  = $otro->tipo_otrocargo;
                    if ($otro->tipo_otrocargo == '04') {
                        $datail_otrocargo['IdentificacionTercero']['Tipo']   = '01';
                        $datail_otrocargo['IdentificacionTercero']['Numero'] = $otro->numero_identificacion;
                    }
                    $datail_otrocargo['NombreTercero']    = $otro->nombre;
                     if ($otro->tipo_otrocargo == '99') {
                        $datail_otrocargo['TipoDocumentoOTROS'] = $otro->detalle;
                    }
                    
                    $datail_otrocargo['Detalle'] = $otro->detalle;
                    if (!empty($otro->porcentaje_cargo)) {
                        $datail_otrocargo['PorcentajeOC'] = $otro->porcentaje_cargo;
                    }else{
                        $datail_otrocargo['PorcentajeOC'] = 0;
                    }
                    $datail_otrocargo['MontoCargo']       = $otro->monto_cargo;
                    array_push($xml['OtrosCargos'], $datail_otrocargo);
                }
            }
           // dd($xml);
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
            $idsale = $sales_item ? $sales_item->idsales : null;
        if ($sales_item->existe_exoneracion === '01') {
            $exoneracion = Items_exonerados::where('idsalesitem',$input['idsalesitem'])->get();
            $exoneracion[0]->delete();
            $sales_item->delete();
        }else{
           $sales_item->delete();
        }

            if ($idsale) {
                $hasExoneracion = Sales_item::where('idsales', $idsale)
                    ->where('existe_exoneracion', '01')
                    ->exists();

                Sales::where('idsale', $idsale)->update([
                    'tiene_exoneracion' => $hasExoneracion ? '01' : '00'
                ]);
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

            if (!$sales_item) {
                return response()->json(['error' => 'Linea de venta no encontrada'], 404);
            }

            $monto_exoneracion = isset($input['monto_exoneracion']) ? $input['monto_exoneracion'] : 0;

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
            //['tipo_cliente', '=', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return response()->json(['success'=> $cliente]);

    }

        public function editarClienteNombre(Request $request)
    {
        $datos = $request->all();
       // dd($datos);
        $cliente = Cliente::where([
            ['nombre', 'like', "%".$datos['nombre_cli']."%"],
            //['tipo_cliente', '=', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        $sales = isset($datos['idsale']) ? Sales::find($datos['idsale']) : null;
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

        public function autocomplete_clientess(Request $request)
    {
        $search = $request->get('term');
        $result = Cliente::where([
            ['nombre', 'like', "%".$search."%"],
            //['tipo_cliente', '=', 1 or 2],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return response()->json($result);
    }
public function autocomplete_cliente(Request $request)
{
    $search = $request->get('term', '');

    if (trim($search) === '') {
        return response()->json([]);
    }

    $result = Cliente::where(function ($q) use ($search) {
            $q->where('nombre', 'like', "%{$search}%")
              ->orWhere('num_id', 'like', "%{$search}%");
        })
        ->where('idconfigfact', '=', Auth::user()->idconfigfact)
        ->take(20)
        ->get(); // devuelve todas las columnas

    // Formateo opcional si tu frontend espera un formato específico
    $formatted = $result->map(function ($cliente) {
        return (array) $cliente->getAttributes(); // convierte a array asociativo
    });

    return response()->json($formatted);
}
        public function autocomplete_clientefac(Request $request)
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
      $sales = isset($datos['idsale']) ? Sales::find($datos['idsale']) : null;
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

            Sales::where('idsale', $sales->idsale)->update(['idmovcxcobrar' => null]);
        }

        $salesActualizada = Sales::find($sales->idsale);
        return response()->json([
            'success'=> $datos,
            'idmovcxcobrar' => $salesActualizada ? $salesActualizada->idmovcxcobrar : null,
            'condicion_venta' => $salesActualizada ? $salesActualizada->condicion_venta : null
        ]);
    }

    public function modficiarMediopago(Request $request)
    {
        $datos = $request->all();
        Sales::where('idsale', $datos['idsale'])->update(['medio_pago' =>  $datos['medio_pago']]);
        return response()->json(['success'=> $datos]);
    }

    public function modficiarMediopagoNuevo(Request $request)
    {
        $sales = Sales::find($request->idsale);
        $sales->medioPagos()->sync($request->input('medio_pago'));
        return response()->json(['success'=> 'Actualizado correctamente']);
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
        //dd($datos);
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
                'es_op' =>  0,
                'idcliente' => $datos['idcliente']
            ]);
        } else {

            if(Auth::user()->config_u[0]->usa_op > 0){

                Sales::where('idsale', $datos['idsale'])->update([
                    'tipo_documento' =>  $datos['tipo_documento'],
                    'es_op' =>  1,
                    'idcliente' => $datos['idcliente']
                ]);
            } else {

                Sales::where('idsale', $datos['idsale'])->update([
                    'tipo_documento' =>  $datos['tipo_documento'],
                    'es_op' =>  0,
                    'idcliente' => $datos['idcliente']
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
            $razon_social = $datos['cliente_serch_modal'];
           $codigo_actividad = $datos['codigo_actividad_modal'];
       // }
        $cliente = Cliente::create([
            'idconfigfact' => Auth::user()->idconfigfact,
            'tipo_id' => $datos['tipo_id_modal'],
            'num_id' => $datos['ced_receptor'],
            'nombre' => $datos['cliente_serch_modal'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'],
            'distrito' => Auth::user()->config_u[0]->distrito_emisor,
            'canton' => Auth::user()->config_u[0]->canton_emisor,
            'provincia' => Auth::user()->config_u[0]->provincia_emisor,
            'direccion' => $datos['direccion'],
            'tipo_cliente' => 1,
            'razon_social' =>$razon_social,
            'nombre_contribuyente' => $datos['cliente_serch_modal'],
            'codigo_actividad' => $codigo_actividad,
        ]);


                    $buscar_usapos = User_config::where('idconfigfact', Auth::user()->config_u[0]->idconfigfact)->get();
                    //dd($buscar);
                    if($buscar_usapos[0]->usa_pos === 0){
             return redirect()->route('punto.create');
                  }else{

                  return redirect()->route('pos.create');
              }

    }

    public function recalcularFactura(Request $request)
    {
        $datos = $request->all();
        $sales = isset($datos['idsale']) ? Sales::find($datos['idsale']) : null;
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
