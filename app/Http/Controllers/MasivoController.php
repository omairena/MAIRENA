<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Cliente;
use App\Productos;
use App\Cajas;
use App\Log_masivo;
use App\Config_masivo;
use App\Items_masivo;
use DB;
use App\Cxcobrar;
use App\Mov_cxcobrar;
use App\Log_cxcobrar;
use App\Sales;
use App\Sales_item;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class MasivoController extends Controller
{
              //	public function index()
   // {
    //    if (Auth::user()->es_vendedor == 1){

      //      Session::flash('message', "Tu usuario no permite ver configuraciones");
       //     return redirect()->route('facturar.index');
       // }
       // $qy = Log_masivo::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
       // $consulta = \DB::table('log_masivo')->select('log_masivo.*')
       //     ->whereNotExists( function ($query) use ($qy) {
       //     $query->select(DB::raw(1))
       //     ->from('config_masivo')
       //     ->whereRaw('config_masivo.idlogmasivo = log_masivo.idlogmasivo');
       //     })->where('idconfigfact', '=', Auth::user()->idconfigfact)->delete();
       // $log_masivo = Log_masivo::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
       // return view('masivo.index', ['log_masivo' => $log_masivo]);
 //   }
public function index()  
{  
    // Verifica si el usuario autenticado es un vendedor.  
    if (Auth::user()->es_vendedor == 1) {  
        // Si el usuario es vendedor, muestra un mensaje y redirige a otra ruta.  
        Session::flash('message', "Tu usuario no permite ver configuraciones");  
        return redirect()->route('facturar.index');  
    }  
    
    // Obtiene todos los registros de Log_masivo para el usuario autenticado.  
    $log_masivo = Log_masivo::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();  
    
    // Retorna la vista 'masivo.index' pasando los registros log_masivo.  
    return view('masivo.index', ['log_masivo' => $log_masivo]);  
}

public function show($id)
{
    // Puedes retornar una vista, un JSON o solo un mensaje
    return response()->json(['message' => 'Método show no implementado.']);
}


public function mostrarFacturasAjax()
{
    $log_masivo = config_masivo::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    $result = [];
    foreach ($log_masivo as $lm) {
        $cliente = Cliente::find($lm->idclientes);
        $result[] = [
            'idconfigmasivo' => $lm->idconfigmasivo,
            'cliente' => $cliente ? $cliente->nombre : '',
            'total_comprobante' => number_format($lm->total_comprobante, 2, ',', '.')
        ];
    }
    return response()->json($result);
}

public function store(Request $request)
{
    // Aquí va la lógica para crear el documento/factura
    // Puedes acceder a los datos así:
    // $idconfigmasivo = $request->input('idconfigmasivo');
    // $cliente = $request->input('cliente');
    // $total_comprobante = $request->input('total_comprobante');

    // Por ahora, solo retorna un mensaje de éxito
    return redirect()->back()->with('success', 'Documento creado correctamente.');
}
 public function mostrarFacturas()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
   
        $log_masivo = config_masivo::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        return view('masivo.lista', ['log_masivo' => $log_masivo]);
    }
    
    
  public function ajaxeliminarConfig(Request $request)
{
    try {
        // Validar que el idlogmasivo esté presente
        $request->validate([
            'idlogmasivo' => 'required|integer',
        ]);

        // Obtener el idlogmasivo del request
        $idlogmasivo = $request->idlogmasivo;

        // Iniciar transacción para asegurar atomicidad
        \DB::transaction(function () use ($idlogmasivo) {
            // 1) Obtener los idconfigmasivo relacionados antes de eliminar
            $configIds = Config_masivo::where('idlogmasivo', $idlogmasivo)
                            ->pluck('idconfigmasivo')
                            ->toArray();

            // 2) Eliminar registros en Config_masivo
            $deletedConfigCount = Config_masivo::where('idlogmasivo', $idlogmasivo)->delete();

            // 3) Eliminar registros en items_masivo que referencian a esos idconfigmasivo
            $deletedItemsCount = 0;
            if (!empty($configIds)) {
                $deletedItemsCount = Items_masivo::whereIn('idconfigmasivo', $configIds)->delete();
            }

            // Opcional: devolver conteos para la respuesta
            return [
                'configDeleted' => $deletedConfigCount,
                'itemsDeleted' => $deletedItemsCount,
                'relatedIds' => $configIds,
            ];
        });

        // Retornar respuesta exitosa
        return response()->json([
            'success' => true,
            'url' => route('masivo.index'),
            'message' => 'Registros eliminados correctamente.',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['success' => false, 'message' => $e->validator->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


        public function create()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
    	$crear_masivo = Log_masivo::create([
    		'idconfigfact'  => Auth::user()->idconfigfact,
    		'fecha_masivo' => date('Y-m-d'),
    		'estatus_masivo' => 0,
    		'nombre_masivo' => 'Nombre por Defecto',
    	]);
    	$find_masivo = Log_masivo::find($crear_masivo->idlogmasivo);
        $clientes = Cliente::where([
            ['tipo_cliente', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        $productos = Productos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $cajas = Cajas::where([
            ['estatus', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        return view('masivo.create', ['productos' => $productos, 'cajas' => $cajas, 'crear_masivo' => $find_masivo, 'clientes' => $clientes]);
    }

    	public function ajaxGuardarCliente(Request $request)
    {
    	$datos = $request->all();
    
    	if (!is_null($datos['nombre_masivo'])) {
    		$actualizar_name = Log_masivo::where('idlogmasivo', $datos['idlogmasivo'])->update([
                'nombre_masivo' => $datos['nombre_masivo']
            ]);
    	}

        $input = array();
        parse_str($datos['datos_form'], $input);
        if ($input['condicion_venta_mod'] === '01') {
        	$p_credito = 0;
        }else{
        	$p_credito = $input['p_credito_mod'];
        }
        $crear_configuracion_masiva = Config_masivo::create([
        	'idlogmasivo'  => $datos['idlogmasivo'],
        	'idclientes'  => $input['clientes'],
        	'idcaja'  => $datos['idcaja'],
        	'idconfigfact'  => Auth::user()->idconfigfact,
        	'idcodigoactv'  => $datos['idcodigoactv'],
        	'tipo_documento_mas'  => $datos['tipo_documento'],
        	'condicion_venta'  => $input['condicion_venta_mod'],
        	'p_credito'  => $p_credito,
        	'medio_pago'  => $input['medio_pago'],
        	'tipo_moneda' =>  'CRC',
            'tipo_cambio' =>  1,
        ]);

        return response()->json(['success'=>true, 'envio' => $input, 'result'=> $crear_configuracion_masiva,'url'=> route('masivo.edit', ['id' =>  $datos['idlogmasivo'], 'cliente' =>  $input['clientes']] )]);
    }

    	public function edit($id, $cliente)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
    	$configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    	$config_cliente = Config_masivo::where([
        	['idlogmasivo', $id],
            ['idclientes', '=', $cliente]
        ])->get();
        $config_masivo = Config_masivo::where([
        	['idlogmasivo', $id],
        ])->get();
        $cajas = Cajas::where([
            ['estatus', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        $usuario = Cliente::find($config_cliente[0]->idclientes);
        $items_masivo = Items_masivo::where('idconfigmasivo', '=', $config_cliente[0]->idconfigmasivo)->get();
        if (count($config_cliente) > 0) {
        	$clientes = \DB::table('clientes')->select('clientes.*')
            ->whereNotExists( function ($query) use ($config_cliente) {
            $query->select(DB::raw(1))
            ->from('config_masivo')
            ->whereRaw('config_masivo.idclientes = clientes.idcliente')
            ->where('config_masivo.idlogmasivo', '=', $config_cliente[0]->idlogmasivo);
            })->where('clientes.idconfigfact', '=', Auth::user()->idconfigfact)->get();
        }else{
        	$clientes = Cliente::where([
            	['tipo_cliente', 1],
            	['idconfigfact', '=', Auth::user()->idconfigfact]
        	])->get();
        }
        if (count($items_masivo) > 0) {
            $productos = \DB::table('productos')->select('productos.*')
            ->whereNotExists( function ($query) use ($items_masivo) {
            $query->select(DB::raw(1))
            ->from('items_masivo')
            ->whereRaw('items_masivo.idproducto = productos.idproducto')
            ->where('items_masivo.idconfigmasivo', '=', $items_masivo[0]->idconfigmasivo);
            })->where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
        }else{
            $productos = Productos::where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
        }
    	return view('masivo.edit', ['configuracion'  => $configuracion, 'clientes'  => $clientes, 'productos' => $productos, 'config_masivo' => $config_masivo, 'items_masivo' => $items_masivo, 'cajas' => $cajas, 'usuario' => $usuario, 'idlogmasivo' => $id, 'config_cliente' => $config_cliente[0]]);
    }

    public function update_lista(Request $request, Log_masivo $model)  
{  
    // Verifica si el usuario es vendedor y no puede acceder a la configuración  
    if (Auth::user()->es_vendedor == 1) {  
        return response()->json([  
            'success' => false,  
            'message' => "Tu usuario no permite ver configuraciones"  
        ], 403); // Código de acceso denegado  
    }  

    // Obtiene todos los datos de la solicitud  
    $datos = $request->all();  

    // Inicia el bloque de manejo de excepciones  
    try {  
        // Obtiene la configuración masiva relacionada al ID proporcionado  
        $configuracion_masiva = Config_masivo::where('idconfigmasivo', '=', $datos['idlogmasivo'])->get();  

        // Verifica que se hayan recuperado configuraciones masivas  
        if ($configuracion_masiva->isEmpty()) {  
            return response()->json(['success' => false, 'message' => 'No se encontró la configuración masiva.'], 404);  
        }  

        // Procesa cada configuración recibida  
        foreach ($configuracion_masiva as $conf_mas) {  
            // Obtiene el consecutivo para el documento  
            $consecutivo = DB::table('consecutivos')->where([  
                ['idcaja', '=', $conf_mas->idcaja],  
                ['tipo_documento', '=', $conf_mas->tipo_documento_mas],  
            ])->get();  

            if ($consecutivo->isEmpty()) {  
                throw new \Exception('No se encontró el consecutivo para el documento.');  
            }  

            // Formatea el número de factura  
            $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);  
            $cajas = Cajas::find($conf_mas->idcaja); // Encuentra la caja  
            if (!$cajas) {  
                throw new \Exception('No se encontró la caja con el ID especificado.');  
            }  

            // Manejo de observaciones  
            $observaciones = !empty($datos['observaciones_masivo']) ? $datos['observaciones_masivo'] : '';  

            // Maneja el porcentaje de crédito  
            $p_credito = empty($conf_mas->p_credito) ? '00' : $conf_mas->p_credito;  

            // Crea un nuevo registro de venta  
            $sales = Sales::create([  
                'numero_documento' => $numero_factura,  
                'tipo_documento' => $conf_mas->tipo_documento_mas,  
                'punto_venta' => str_pad($cajas->codigo_unico, 5, "0", STR_PAD_LEFT),  
                'idcaja' => $conf_mas->idcaja,  
                'idconfigfact' => $conf_mas->idconfigfact,  
                'idcodigoactv' => $conf_mas->idcodigoactv,  
                'idcliente' => $conf_mas->idclientes,  
                'tipo_moneda' => $conf_mas->tipo_moneda,  
                'tipo_cambio' => $conf_mas->tipo_cambio,  
                'condicion_venta' => $conf_mas->condicion_venta,  
                'p_credito' => $p_credito,  
                'medio_pago' => $conf_mas->medio_pago ?? '00',  // Obtener medio_pago desde configuración  
                'referencia_pago' => '0',  
                'observaciones' => $observaciones,  
                'total_serv_grab' => $conf_mas->total_serv_grab,  
                'total_serv_exento' => $conf_mas->total_serv_exento,  
                'total_serv_exonerado' => $conf_mas->total_serv_exonerado,  
                'total_mercancia_grav' => $conf_mas->total_mercancia_grav,  
                'total_mercancia_exenta' => $conf_mas->total_mercancia_exenta,  
                'total_mercancia_exonerada' => $conf_mas->total_mercancia_exonerada,  
                'total_exento' => $conf_mas->total_exento,  
                'total_exonerado' => $conf_mas->total_exonerado,  
                'total_neto' => $conf_mas->total_neto,  
                'total_descuento' => $conf_mas->total_descuento,  
                'total_impuesto' => $conf_mas->total_impuesto,  
                'total_otros_cargos' => $conf_mas->total_otros_cargos,  
                'total_iva_devuelto' => $conf_mas->total_iva_devuelto,  
                'total_comprobante' => $conf_mas->total_comprobante,  
                'tiene_exoneracion' => '00',  
                'fecha_creada' => date('Y-m-d'),  
                'estatus_sale' => 1,  
                'fecha_reenvio' => date('c'),  
                'creado_por' => Auth::user()->email,
            ]);  

            // Actualizar la configuración masiva para asociar la venta  
            Config_masivo::where('idconfigmasivo', $conf_mas->idconfigmasivo)->update([  
                'sales_masivo' => $sales->idsale  
            ]);  

            // Manejo de cuentas por cobrar si la condición de venta es crédito  
            if ($conf_mas->condicion_venta === '02') {  
                $cli_cxcobrar = Cxcobrar::where('idcliente', $conf_mas->idclientes)->get();  
                if (count($cli_cxcobrar) > 0) {  
                    $mov_cxcobrar = Mov_cxcobrar::create([  
                        'idcxcobrar' => $cli_cxcobrar[0]->idcxcobrar,  
                        'num_documento_mov' => $numero_factura,  
                        'fecha_mov' => date('Y-m-d'),  
                        'monto_mov' => '0.00000',  
                        'abono_mov' => '0.00000',  
                        'saldo_pendiente' => '0.00000',  
                        'cant_dias_pendientes' => $p_credito,  
                        'estatus_mov' => 1  
                    ]);  
                } else {  
                    $cxcobrar = Cxcobrar::create([  
                        'idcliente' => $conf_mas->idclientes,  
                        'idconfigfact' => $conf_mas->idconfigfact,  
                        'saldo_cuenta' => '0.00000',  
                        'cantidad_dias' => $p_credito  
                    ]);  

                    $mov_cxcobrar = Mov_cxcobrar::create([  
                        'idcxcobrar' => $cxcobrar->idcxcobrar,  
                        'num_documento_mov' => $numero_factura,  
                        'fecha_mov' => date('Y-m-d'),  
                        'monto_mov' => '0.00000',  
                        'abono_mov' => '0.00000',  
                        'saldo_pendiente' => '0.00000',  
                        'cant_dias_pendientes' => $p_credito,  
                        'estatus_mov' => 1  
                    ]);  
                }  

                // Actualiza la venta con el movimiento de cuentas por cobrar  
                Sales::where('idsale', $sales->idsale)->update(['idmovcxcobrar' => $mov_cxcobrar->idmovcxcobrar]);  
            }  

            // Maneja los items de la venta  
            $items_masivo = Items_masivo::where('idconfigmasivo', '=', $conf_mas->idconfigmasivo)->get();  
            foreach ($items_masivo as $item_sale) {  
                Sales_item::create([  
                    'idsales' => $sales->idsale,  
                    'idproducto' => $item_sale->idproducto,  
                    'codigo_producto' => $item_sale->codigo_producto,  
                    'nombre_producto' => $item_sale->nombre_producto,  
                    'costo_utilidad' => $item_sale->costo_utilidad,  
                    'cantidad' => $item_sale->cantidad_masivo,  
                    'valor_neto' => $item_sale->valor_neto,  
                    'valor_descuento' => $item_sale->valor_descuento,  
                    'valor_impuesto' => $item_sale->valor_impuesto,  
                    'tipo_impuesto' => $item_sale->tipo_impuesto,  
                    'impuesto_prc' => $item_sale->impuesto_prc,  
                    'descuento_prc' => $item_sale->descuento_prc,  
                    'existe_exoneracion' => $item_sale->existe_exoneracion  
                ]);  
            }  

            // Calcular totales  
            $sales_item = Sales_item::where('idsales', $sales->idsale)->get();  
            $totales = [  
                'total_neto' => 0,  
                'total_descuento' => 0,  
                'total_impuesto' => 0,  
                'total_comprobante' => 0,  
                'total_mercancia_grav' => 0,  
                'total_exonerado' => 0,  
                'total_mercancia_exonerada' => 0,  
                'total_iva_devuelto' => 0,  
                'total_serv_grab' => 0,  
                'total_serv_exento' => 0,  
                'total_serv_exonerado' => 0  
            ];  

            // Procesa cada ítem para calcular totales  
            foreach ($sales_item as $s_i) {  
                $producto = Productos::find($s_i->idproducto);  
                $cantidad_stock = $producto->cantidad_stock - $s_i->cantidad;  
                // Actualiza el stock del producto  
                Productos::where('idproducto', $s_i->idproducto)->update(['cantidad_stock' => $cantidad_stock]);  

                // Calcular los totales según el tipo de producto y condiciones  
                if ($s_i->existe_exoneracion === '00') {  
                    if ($producto->tipo_producto === 2) { // Servicio  
                        $totales['total_serv_grab'] += ($s_i->costo_utilidad * $s_i->cantidad);  
                        $totales['total_neto'] += $s_i->valor_neto;  
                        $totales['total_descuento'] += $s_i->valor_descuento;  
                        $totales['total_impuesto'] += $s_i->valor_impuesto;  
                        if ($conf_mas->medio_pago === '02' && $producto->porcentaje_imp == '4.00') {  
                            $totales['total_iva_devuelto'] += $s_i->valor_impuesto;  
                        }  
                    } else { // Mercancía  
                        $totales['total_mercancia_grav'] += ($s_i->costo_utilidad * $s_i->cantidad);  
                        $totales['total_neto'] += $s_i->valor_neto;  
                        $totales['total_descuento'] += $s_i->valor_descuento;  
                        $totales['total_impuesto'] += $s_i->valor_impuesto;  
                        if ($conf_mas->medio_pago === '02' && $producto->porcentaje_imp == '4.00') {  
                            $totales['total_iva_devuelto'] += $s_i->valor_impuesto;  
                        }  
                    }  
                } else {  
                    // Manejar la exoneración  
                    $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();  
                    $totales['total_impuesto'] += ($items_exonerados[0]->monto_exoneracion - $s_i->valor_impuesto);  
                    $totales['total_exonerado'] += $items_exonerados[0]->monto_exoneracion;  

                    if ($producto->tipo_producto === 2) {  
                        $totales['total_serv_exonerado'] += ($s_i->costo_utilidad * $s_i->cantidad);  
                    } else {  
                        $totales['total_mercancia_exonerada'] += ($s_i->costo_utilidad * $s_i->cantidad);  
                    }  
                    $totales['total_neto'] += $s_i->valor_neto;  
                    $totales['total_descuento'] += $s_i->valor_descuento;  
                    if ($conf_mas->medio_pago === '02' && $producto->porcentaje_imp == '4.00') {  
                        $totales['total_iva_devuelto'] += $s_i->valor_impuesto;  
                    }  
                }  
            }  

            // Calcular el total del comprobante  
            $totales['total_comprobante'] = (  
                ($totales['total_mercancia_grav'] + $totales['total_mercancia_exonerada'] + $totales['total_serv_grab'] - $totales['total_descuento']) + $totales['total_impuesto'] - $totales['total_iva_devuelto']  
            );  

            // Actualiza la venta con los totales calculados  
            Sales::where('idsale', $sales->idsale)->update($totales);  

            // Actualiza la configuración masiva con los totales  
            Config_masivo::where('idconfigmasivo', $conf_mas->idconfigmasivo)->update($totales);  

            // Maneja cuentas por cobrar si la condición es crédito  
            if ($conf_mas->condicion_venta === '02') {  
                $cli_cxcobrar = Cxcobrar::where('idcliente', $conf_mas->idclientes)->get();  
                if (count($cli_cxcobrar) > 0) {  
                    $sumando = $cli_cxcobrar[0]->saldo_cuenta + $totales['total_comprobante'];  
                    Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $sumando]);  
                }  
            }  

            // Actualiza el siguiente número de factura  
            $new = $numero_factura + 1;  
            DB::update('update consecutivos set numero_documento = ? where tipo_documento = ? and idcaja = ?', [$new, $conf_mas->tipo_documento_mas, $conf_mas->idcaja]);  
        }  

        // Actualiza el estatus del registro log masivo  
        Log_masivo::where('idlogmasivo', '=', $datos['idlogmasivo'])->update(['estatus_masivo' => 1]);  

        // Redirige a la ruta 'facturar.edit' después de un flujo correcto  
        \Log::info('Idsale para redirección: ' . $sales->idsale);
       
           return response()->json(['success' => true, 'url' => route('pos.edit', $sales->idsale)]);  
    } catch (\Exception $e) {  
        // Maneja excepciones y registra el error  
        \Log::error('Error en update_lista: ' . $e->getMessage());  
        return response()->json(['success' => false, 'message' => 'Ocurrió un error: ' . $e->getMessage()], 500);  
    }  
}


    	public function update(Request $request, Log_masivo $model)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
    	$datos = $request->all();
    	$configuracion_masiva = Config_masivo::where('idlogmasivo', '=', $datos['idlogmasivo'])->get();
    	foreach($configuracion_masiva as $conf_mas){
    		$consecutivo = DB::table('consecutivos')->where([
            	['idcaja', '=', $conf_mas->idcaja],
            	['tipo_documento', '=', $conf_mas->tipo_documento_mas],
        	])->get();
        	$numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
    		$cajas = Cajas::find($conf_mas->idcaja);
    		if (!is_null($datos['observaciones_masivo'])) {
            	$observaciones = $datos['observaciones_masivo'];
        	}else{
            	$observaciones = '';
        	}
			if (empty($conf_mas->p_credito)) {
            	$p_credito = '00';
        	}else{
            	$p_credito = $conf_mas->p_credito;
        	}
        	$sales = Sales::create(
            [
                'numero_documento' => $numero_factura,
                'tipo_documento' => $conf_mas->tipo_documento_mas,
                'punto_venta' => str_pad($cajas->codigo_unico, 5, "0", STR_PAD_LEFT),
                'idcaja' => $conf_mas->idcaja,
                'idconfigfact' => $conf_mas->idconfigfact,
                'idcodigoactv' => $conf_mas->idcodigoactv,
                'idcliente' => $conf_mas->idclientes,
                'tipo_moneda' => $conf_mas->tipo_moneda,
                'tipo_cambio' => $conf_mas->tipo_cambio,
                'condicion_venta' => $conf_mas->condicion_venta,
                'p_credito' => $p_credito,
                'medio_pago' => $conf_mas->medio_pago,
                'referencia_pago' => '0',
                'observaciones' => $observaciones,
                'total_serv_grab' => $conf_mas->total_serv_grab,
                'total_serv_exento' => $conf_mas->total_serv_exento,
                'total_serv_exonerado' => $conf_mas->total_serv_exonerado,
                'total_mercancia_grav' => $conf_mas->total_mercancia_grav,
                'total_mercancia_exenta' => $conf_mas->total_mercancia_exenta,
                'total_mercancia_exonerada' => $conf_mas->total_mercancia_exonerada,
                'total_exento' => $conf_mas->total_exento,
                'total_exonerado' => $conf_mas->total_exonerado,
                'total_neto' => $conf_mas->total_neto,
                'total_descuento' => $conf_mas->total_descuento,
                'total_impuesto' => $conf_mas->total_impuesto,
                'total_otros_cargos' => $conf_mas->total_otros_cargos,
                'total_iva_devuelto' => $conf_mas->total_iva_devuelto,
                'total_comprobante' => $conf_mas->total_comprobante,
                'tiene_exoneracion' => '00',
                'fecha_creada' => date('Y-m-d'),
                'estatus_sale' => 2,
                //'fecha_reenvio' => date('c'),//comentar para cambiar fecha del doc
                'creado_por' => Auth::user()->email,
            ]
        	);
        	$actualizar_sale = Config_masivo::where('idconfigmasivo', $conf_mas->idconfigmasivo)->update([
                    'sales_masivo' => $sales->idsale
                ]);
        	if ($conf_mas->condicion_venta === '02') {
            	$cli_cxcobrar = Cxcobrar::where('idcliente', $conf_mas->idclientes)->get();
            	if (count($cli_cxcobrar) > 0) {
                	$mov_cxcobrar = Mov_cxcobrar::create(
                	[
                    	'idcxcobrar' => $cli_cxcobrar[0]->idcxcobrar,
                    	'num_documento_mov' => $numero_factura,
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
                    	'idcliente' => $conf_mas->idclientes,
                    	'idconfigfact' => $conf_mas->idconfigfact,
                    	'saldo_cuenta' => '0.00000',
                    	'cantidad_dias' => $p_credito
                	]);

                	$mov_cxcobrar = Mov_cxcobrar::create(
                	[
                    	'idcxcobrar' => $cxcobrar->idcxcobrar,
                    	'num_documento_mov' => $numero_factura,
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

        	$items_masivo = Items_masivo::where('idconfigmasivo', '=', $conf_mas->idconfigmasivo)->get();
        	foreach($items_masivo as $item_sale){
        		$sales_item = Sales_item::create(
                    [
                        'idsales' => $sales->idsale,
                        'idproducto' => $item_sale->idproducto,
                        'codigo_producto' =>  $item_sale->codigo_producto,
                        'nombre_producto' =>  $item_sale->nombre_producto,
                        'costo_utilidad' => $item_sale->costo_utilidad,
                        'cantidad' => $item_sale->cantidad_masivo,
                        'valor_neto' => $item_sale->valor_neto,
                        'valor_descuento' => $item_sale->valor_descuento,
                        'valor_impuesto' => $item_sale->valor_impuesto,
                        'tipo_impuesto' => $item_sale->tipo_impuesto,
                        'impuesto_prc' => $item_sale->impuesto_prc,
                        'descuento_prc' => $item_sale->descuento_prc,
                        'existe_exoneracion' => $item_sale->existe_exoneracion
                    ]
                );
        	}

        	$sales_item = Sales_item::where('idsales', $sales->idsale)->get();
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
        	$total_serv_no_sujeto = 0;
        	$total_mercancia_exenta = 0;
        	$total_otros_cargos = 0;
        	$total_mercancia_no_sujeto = 0;
        	$total_IVA_ex = 0;
        	foreach ($sales_item as $s_i) {
            	$producto = Productos::find($s_i->idproducto);
            	$cantidad_stock = $producto->cantidad_stock - $s_i->cantidad;
            	$actualizar = Productos::where('idproducto', $s_i->idproducto)->update(['cantidad_stock' => $cantidad_stock]);
           	 //	if ($s_i->existe_exoneracion === '00') {
                //	if ($producto->tipo_producto === 2) {
                    //	$total_serv_grab = $total_serv_grab + ($s_i->costo_utilidad * $s_i->cantidad);
                    //	$total_neto = $total_neto + $s_i->valor_neto;
                    //	$total_descuento = $total_descuento + $s_i->valor_descuento;
                    //	$total_impuesto = $total_impuesto + $s_i->valor_impuesto;
                    //	if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00') {
                        //	$total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                    //	}
                //	}else{
                    //	$total_mercancia_grav = $total_mercancia_grav + ($s_i->costo_utilidad * $s_i->cantidad);
                    //	$total_neto = $total_neto + $s_i->valor_neto;
                    //	$total_descuento = $total_descuento + $s_i->valor_descuento;
                    //	$total_impuesto = $total_impuesto + $s_i->valor_impuesto;
                    //	if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00') {
                        //	$total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                    //	}
                //	}
            //	}else{
             //   if ($producto->tipo_producto === 2) {
                  //  $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                  //  $total_impuesto = $total_impuesto + ($items_exonerados[0]->monto_exoneracion - $s_i->valor_impuesto);
                  //  $total_exonerado = $total_exonerado + $items_exonerados[0]->monto_exoneracion;
                  //  $total_serv_exonerado =  $total_serv_exonerado + ($s_i->costo_utilidad * $s_i->cantidad);
                   // $total_serv_grab = $total_serv_grab + 0;
                  //  $total_neto = $total_neto + $s_i->valor_neto;
                  //  $total_descuento = $total_descuento + $s_i->valor_descuento;
                  //  if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00') {
                  //      $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                //    }
               // }else{
                  //  $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                  //  $total_impuesto = $total_impuesto + ($items_exonerados[0]->monto_exoneracion - $s_i->valor_impuesto);
                  //  $total_exonerado = $total_exonerado + $items_exonerados[0]->monto_exoneracion;
                 //   $total_mercancia_exonerada =  $total_mercancia_exonerada + ($s_i->costo_utilidad * $s_i->cantidad);
                 //   $total_mercancia_grav = $total_mercancia_grav + 0;
                //    $total_neto = $total_neto + $s_i->valor_neto;
                ////    $total_descuento = $total_descuento + $s_i->valor_descuento;
                //    if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00') {
               //         $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
              //      }
             //   }
           // }
           
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
        $total_exento = $total_serv_exento + $total_mercancia_exenta;
        $total_comprobante = ((($total_mercancia_grav + $total_mercancia_exonerada + $total_mercancia_exenta + $total_serv_grab+ $total_serv_exonerado + $total_serv_exento)-$total_descuento) + $total_impuesto) - $total_iva_devuelto + $total_otros_cargos + $total_mercancia_no_sujeto + $total_serv_no_sujeto;
       // $total_comprobante = $total_comprobante + (((($total_mercancia_grav + $total_mercancia_exonerada + $total_serv_grab)-$total_descuento) + $total_impuesto) - $total_iva_devuelto);
       
      //dd($total_comprobante);
        $sales  = Sales::where('idsale', $sales->idsale)->update([
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
                    'total_otros_cargos' => $total_otros_cargos,
        ]);

        $config_actualizar  = Config_masivo::where('idconfigmasivo', $conf_mas->idconfigmasivo)->update([
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
                    'total_otros_cargos' => $total_otros_cargos,
        ]);

        if ($conf_mas->condicion_venta === '02') {
            $cli_cxcobrar = Cxcobrar::where('idcliente', $conf_mas->idclientes)->get();
            if (count($cli_cxcobrar) > 0) {
                $sumando = $cli_cxcobrar[0]->saldo_cuenta + $total_comprobante;
                $mcxcobrar = Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $sumando]);
                $upd_mov_cxcobrar = Mov_cxcobrar::where('idmovcxcobrar', $mov_cxcobrar->idmovcxcobrar)->update([
                    'monto_mov' => $total_comprobante,
                    'saldo_pendiente' => $total_comprobante
                ]);
            }
        }
        $new = $numero_factura + 1;
        $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$conf_mas->tipo_documento_mas.' and idcaja = '.$conf_mas->idcaja);
        }
    	Log_masivo::where([
    		['idlogmasivo', '=', $datos['idlogmasivo']],
    	])->update(['estatus_masivo' =>  1]);
    	return redirect()->route('masivo.index')->withStatus(__('Configuracion Guardada Correctamente.'));
    }

    	public function ajaxInfocliente(Request $request)
    {
    	$datos = $request->all();
    	$config_masivo = Config_masivo::where([
    		['idlogmasivo', '=', $datos['idlogmasivo']],
    		['idclientes', '=', $datos['cliente']],
    	])->get();

        return response()->json(['success'=> true, 'datos' => $config_masivo]);
    }

    //Seccion de edicion masiva
       	public function modficiarMediopago(Request $request)
    {
        $datos = $request->all();
        Config_masivo::where([
    		['idlogmasivo', '=', $datos['idlogmasivo']],
    		['idclientes', '=', $datos['idclientes']],
    	])->update(['medio_pago' =>  $datos['medio_pago']]);
        return response()->json(['success'=> $datos]);
    }

        public function modficiarTipodoc(Request $request)
    {
        $datos = $request->all();
        Config_masivo::where([
    		['idlogmasivo', '=', $datos['idlogmasivo']],
    		['idclientes', '=', $datos['idclientes']],
    	])->update(['tipo_documento_mas' =>  $datos['tipo_documento_mas']]);
        return response()->json(['success'=> $datos]);
    }

        public function modficiarCaja(Request $request)
    {
        $datos = $request->all();
        Config_masivo::where([
    		['idlogmasivo', '=', $datos['idlogmasivo']],
    		['idclientes', '=', $datos['idclientes']],
    	])->update(['idcaja' =>  $datos['idcaja']]);
        return response()->json(['success'=> $datos]);
    }

        public function modficiarCondicion(Request $request)
    {
        $datos = $request->all();
         Config_masivo::where([
    		['idlogmasivo', '=', $datos['idlogmasivo']],
    		['idclientes', '=', $datos['idclientes']],
    	])->update(['condicion_venta' =>  $datos['condicion']]);
        return response()->json(['success'=> $datos]);
    }
     public function modficiarmoneda(Request $request)
    {
      //  $datos = $request->all();
        // Config_masivo::where([
    	//	['idlogmasivo', '=', $datos['idlogmasivo']],
    	//	['idclientes', '=', $datos['idclientes']],
    //	])->update([
    	//    'tipo_moneda' =>  $datos['moneda'],
    	//    'tipo_cambio' =>  $datos['tipo_cambio'],
    //	    ]);
      //  return response()->json(['success'=> $datos]);
        
      //   public function modTipocambio(Request $request)
   // {
        $datos = $request->all();
        Config_masivo::where([
        ['idlogmasivo', '=', $datos['idlogmasivo']],
        ['idclientes', '=', $datos['idclientes']],
        ])->update([
       // Sales::where('idsale', $datos['idsale'])->update([
            'tipo_moneda' =>  $datos['moneda'],
            'tipo_cambio' =>  $datos['tipocambio']
        ]);
        return response()->json(['success'=> $datos]);
  //  }
    
    
    }

        public function modficiarObservacion(Request $request)
    {
        $datos = $request->all();
         Config_masivo::where([
    		['idlogmasivo', '=', $datos['idlogmasivo']],
    		['idclientes', '=', $datos['idclientes']],
    	])->update(['observacion_masivo' =>  $datos['observacion']]);
        return response()->json(['success'=> $datos]);
    }

        public function modficiarCxc(Request $request)
    {
        $datos = $request->all();
        Config_masivo::where([
    		['idlogmasivo', '=', $datos['idlogmasivo']],
    		['idclientes', '=', $datos['idclientes']],
    	])->update(['p_credito' =>  $datos['dias']]);
        return response()->json(['success'=> $datos]);
    }

        public function agregarLineaFactura(Request $request)
    {
        $datos = $request->all();
        if(strstr($datos['sales_item'],',') ){
            $valores = explode(',', $datos['sales_item']);
            for ($i=0; $i < count($valores); $i++) {
                $producto = Productos::find($valores[$i]);
                $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
                $items_masivo = Items_masivo::create(
                    [
                        'idconfigmasivo' => $datos['idconfigmasivo'],
                        'idproducto' => $producto->idproducto,
                        'codigo_producto' =>  $producto->codigo_producto,
                        'nombre_producto' =>  $producto->nombre_producto,
                        'costo_utilidad' => $producto->precio_sin_imp,
                        'cantidad_masivo' => 1,
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
                $items_masivo = Items_masivo::create(
                    [
                    'idconfigmasivo' => $datos['idconfigmasivo'],
                    'idproducto' => $producto->idproducto,
                    'codigo_producto' =>  $producto->codigo_producto,
                    'nombre_producto' =>  $producto->nombre_producto,
                    'costo_utilidad' => $producto->precio_sin_imp,
                    'cantidad_masivo' => $cantidad,
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
                $valor_neto = $producto->precio_sin_imp * $datos['cantidad'];
                $valor_impuesto = ($valor_neto * $producto->porcentaje_imp)/100;
                $items_masivo = Items_masivo::create(
                    [
                        'idconfigmasivo' => $datos['idconfigmasivo'],
                        'idproducto' => $producto->idproducto,
                        'codigo_producto' =>  $producto->codigo_producto,
                        'nombre_producto' =>  $producto->nombre_producto,
                        'costo_utilidad' => $producto->precio_sin_imp,
                        'cantidad_masivo' => $cantidad,
                        'valor_neto' => $valor_neto,
                        'valor_descuento' => 0,
                        'valor_impuesto' => $valor_impuesto,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'descuento_prc' => 0,
                        'existe_exoneracion' => '00'
                    ]
                );
                 return response()->json(['success'=> $datos]);
            }
        }
    }

        public function eliminarLineaFactura(Request $request)
    {
        $input = $request->all();
        $items_masivo = Items_masivo::find($input['iditemsmasivo']);
        $items_masivo->delete();
        return response()->json(['success'=> $input]);

    }

        public function actualiarCantFactura(Request $request)
    {
        $input = $request->all();
        $items_masivo = Items_masivo::find($input['iditemsmasivo']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = $items_masivo->costo_utilidad * $input['cantidad'];
        if ($items_masivo->descuento_prc > 0) {
            $total_descuento = ($total_neto * $items_masivo->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
        $total_neto = $total_neto - $total_descuento;
        $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;

        $actualizar = Items_masivo::where('iditemsmasivo', $input['iditemsmasivo'])->update(['cantidad_masivo' => $input['cantidad'], 'valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento]);

        return response()->json(['success'=> $input]);
    }

        public function actualiarDescFactura(Request $request)
    {
        $input = $request->all();
        $items_masivo = Items_masivo::find($input['iditemsmasivo']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = $items_masivo->costo_utilidad * $items_masivo->cantidad_masivo;
        $mto_descuento = ($total_neto * $input['porcentaje_descuento'])/100;
        $total_neto = $total_neto - $mto_descuento;
        $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;
        $actualizar = Items_masivo::where('iditemsmasivo', $input['iditemsmasivo'])->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $mto_descuento, 'descuento_prc' => $input['porcentaje_descuento']]);
        return response()->json(['success'=> $input]);

    }

        public function actualiarCostoFactura(Request $request)
    {
        $input = $request->all();
        $items_masivo = Items_masivo::find($input['iditemsmasivo']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = $input['costo_utilidad'] * $items_masivo->cantidad;
        if ($items_masivo->descuento_prc > 0) {
            $total_descuento = ($total_neto * $items_masivo->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
        $total_neto = $total_neto - $total_descuento;
        $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;

        $actualizar = Items_masivo::where('iditemsmasivo', $input['iditemsmasivo'])->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento, 'costo_utilidad' => $input['costo_utilidad']]);

        return response()->json(['success'=> $input]);
    }

    	public function ajaxEditarConfig(Request $request)
    {
    	$datos = $request->all();
        return response()->json(['success'=>true, 'url'=> route('masivo.edit', ['id' =>  $datos['idlogmasivo'], 'cliente' =>  $datos['cliente']] )]);
    }

    	public function ajaxEnviarConfig(Request $request)
    {
    	$datos = $request->all();
    
    	//$config_masivo = Config_masivo::where('idlogmasivo', '=', $datos['idlogmasivo'])->get();
    	 $config_masivo = Config_masivo::where('idlogmasivo', '=', $datos['idlogmasivo'])
                                ->where('estado', '=', 0)
                                ->get();
    	
    	foreach($config_masivo as $config){
    		$seguridad = app('App\Http\Controllers\PosController')->armarSeguridad($config->idconfigfact);
    		$xml = app('App\Http\Controllers\PosController')->armarXml($config->sales_masivo);
    		include_once(public_path(). '/funcionFacturacion506.php');
        	$facturar = Timbrar_documentos($xml, $seguridad);
        	$descargar= app('App\Http\Controllers\PosController')->armarXml($config->sales_masivo);
        	$envio_mail = app('App\Http\Controllers\DonwloadController')->reenviarCorreoXml($config->sales_masivo);
        	
        	$config->estado = 1;
            $config->save();
        

    	}
        return response()->json(['success'=>true, 'url'=> route('masivo.index'), 'data' => $datos]);
    }

        public function modificarFlotanteMas(Request $request)
    {
        $datos = $request->all();
        $input = array();
        parse_str($datos['datos'], $input);
        $items_masivo = Items_masivo::find($input['idsalesitem_flot']);
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
        $valor_neto = $input['costo_utilidad'] * $input['cantidad'];
        if ($input['descuento'] > 0) {
            $valor_descuento = ($valor_neto * $input['descuento'])/100;
        }
        $valor_impuesto = (($valor_neto - $valor_descuento) * $porcentaje_imp)/100;

        $actualizar = Items_masivo::where('iditemsmasivo', $input['idsalesitem_flot'])
        ->update([
            'codigo_producto' => $input['codigo_producto'],
            'nombre_producto' => $input['nombre_producto'],
            'cantidad_masivo' => $input['cantidad'],
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

    	public function borrarConfig(Request $request)
    {
    	$input = $request->all();
    	$config_masivo = Config_masivo::find($input['idconfigmasivo']);
        $items_masivo = Items_masivo::where('idconfigmasivo', '=', $input['idconfigmasivo']);
        $items_masivo->delete();
        $config_masivo->delete();
        return response()->json(['success'=>true, 'url'=> route('masivo.index')]);
    }

        public function ajaxBorrarMasivo(Request $request)
    {
        $datos = $request->all();
        $log_masivo = Log_masivo::find($datos['idlogmasivo']);
        $log_masivo->delete();
        return response()->json(['success'=>true, 'url'=> route('masivo.index')]);
    }

        public function infoFlotanteMas(Request $request)
    {
        $input = $request->all();
        $item = Items_masivo::find($input['id']);
        $producto = Productos::find($item->idproducto);
        return response()->json(['success'=> $producto]);
    }
}
