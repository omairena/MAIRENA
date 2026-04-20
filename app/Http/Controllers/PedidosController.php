<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pedidos;
use App\Pedidos_item;
use App\Productos;
use App\Cliente;
use Auth;
use DB;
use App\Cajas;
use App\Actividad;
use App\Sales;
use App\Sales_item;


class PedidosController extends Controller
{
	 	public function index()
    {
        $pedidos = Pedidos::where([
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        $cajas = Cajas::where([
            ['estatus', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
    	return view('pedidos.index', ['pedidos' => $pedidos]);
    }

        public function create()
    {
        $productos = Productos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $contado = Cliente::where('num_id', '100000000')->get();
        $cajas = Cajas::where([
            ['estatus', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        return view('pedidos.create', ['productos' => $productos, 'contado' => $contado, 'cajas' => $cajas]);
    }




    	public function store(Request $request, Pedidos $model)
    {
    	$datos = $request->all();
    	$pedido = Pedidos::create(
            [
                'numero_documento' => $datos['numero_documento'],
                'idcaja' => $datos['idcaja'],
                'idconfigfact' => Auth::user()->idconfigfact,
                'idcliente' => $datos['cliente'],
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
                'fecha_doc' => date('Y-m-d'),
                'estatus_doc' => 1
            ]
        );

        if(strstr($datos['sales_item'],',') ){
            $valores = explode(',', $datos['sales_item']);
            for ($i=0; $i < count($valores); $i++) {
                $producto = Productos::find($valores[$i]);
                $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
                $pedidos_item = Pedidos_item::create(
                    [
                        'idpedido' => $pedido->idpedido,
                        'idproducto' => $producto->idproducto,
                        'nombre_proc' => $producto->nombre_producto,
                        'cantidad_ped' => 1,
                        'valor_neto' => $producto->precio_sin_imp,
                		'valor_impuesto' => $valor_imp,
                		'valor_descuento' => 0,
                		'tipo_impuesto' => $producto->impuesto_iva,
                		'descuento_prc' => 0,
                		'impuesto_prc' => $producto->porcentaje_imp,
                		'costo_utilidad' => $producto->precio_sin_imp
                    ]
                );

            }
            return redirect()->route('pedidos.edit', $pedido->idpedido);
        }else{
            if (is_null($datos['cantidad_pos_envia'])) {
                $producto = Productos::find($datos['sales_item']);
                $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
                $pedidos_item = Pedidos_item::create(
                    [
                        'idpedido' => $pedido->idpedido,
                        'idproducto' => $producto->idproducto,
                        'nombre_proc' => $producto->nombre_producto,
                        'cantidad_ped' => 1,
                        'valor_neto' => $producto->precio_sin_imp,
               		   'valor_impuesto' => $valor_imp,
               		   'valor_descuento' => 0,
                	   'tipo_impuesto' => $producto->impuesto_iva,
                	   'descuento_prc' => 0,
                	   'impuesto_prc' => $producto->porcentaje_imp,
                	   'costo_utilidad' => $producto->precio_sin_imp
                    ]
                 );
                return redirect()->route('pedidos.edit', $pedido->idpedido);
            }else{
                $cantidad = $datos['cantidad_pos_envia'];
                $producto = Productos::find($datos['sales_item']);
                $valor_neto = $producto->precio_sin_imp * $datos['cantidad_pos_envia'];
                $valor_imp = ($valor_neto  * $producto->porcentaje_imp)/100;

                $pedidos_item = Pedidos_item::create(
                    [
                        'idpedido' => $pedido->idpedido,
                        //'observaciones' =>$pedidos->observaciones,
                        'idproducto' => $producto->idproducto,
                          'nombre_proc' => $producto->nombre_producto,
                        'cantidad_ped' => $cantidad,
                        'valor_neto' => $valor_neto,
                        'valor_impuesto' => $valor_imp,
                        'valor_descuento' => 0,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'descuento_prc' => 0,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'costo_utilidad' => $producto->precio_sin_imp
                    ]
                 );
                return redirect()->route('pedidos.edit', $pedido->idpedido);
            }
        }
    }

        public function edit($id)
    {
        $clientes = Cliente::where([
            ['tipo_cliente', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        $pedido = Pedidos::find($id);
        $pedidos_item = Pedidos_item::where('idpedido', $id)->get();
        $cajas = Cajas::where([
            ['estatus', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        if (count($pedidos_item) > 0) {
            $productos = \DB::table('productos')->select('productos.*')
            ->whereNotExists( function ($query) use ($pedidos_item) {
            $query->select(DB::raw(1))
            ->from('pedidos_item')
            ->whereRaw('pedidos_item.idproducto = productos.idproducto')
            ->where('pedidos_item.idpedido', '=', $pedidos_item[0]->idpedido);
            })->where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
        }else{
            $productos = Productos::where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
        }
        $usuario = Cliente::find($pedido->idcliente);
        return view('pedidos.edit', ['clientes'  => $clientes, 'productos' => $productos, 'pedido' => $pedido, 'pedidos_item' => $pedidos_item, 'usuario' => $usuario, 'cajas' => $cajas]);
    }

        public function agregarLineaPedido(Request $request)
    {
        $datos = $request->all();
        if(strstr($datos['sales_item'],',') ){
            $valores = explode(',', $datos['sales_item']);
            for ($i=0; $i < count($valores); $i++) {
                $producto = Productos::find($valores[$i]);
                $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
                $pedidos_item = Pedidos_item::create(
                    [
                        'idpedido' => $datos['idpedido'],
                        'idproducto' => $producto->idproducto,
                            'cantidad_ped' => 1,
                        'valor_neto' => $producto->precio_sin_imp,
                        'valor_impuesto' => $valor_imp,
                        'valor_descuento' => 0,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'descuento_prc' => 0,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'costo_utilidad' => $producto->precio_sin_imp,
                        'nombre_proc' => $producto->nombre_producto
                    ]
                );
            }
            return response()->json(['success'=> $datos]);
        }else{
            if (is_null($datos['cantidad'])) {
                $cantidad = 1;
                $producto = Productos::find($datos['sales_item']);
                $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
                $pedidos_item = Pedidos_item::create(
                    [
                        'idpedido' => $datos['idpedido'],
                        'idproducto' => $producto->idproducto,

                        'cantidad_ped' => 1,
                        'valor_neto' => $producto->precio_sin_imp,
                        'valor_impuesto' => $valor_imp,
                        'valor_descuento' => 0,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'descuento_prc' => 0,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'costo_utilidad' => $producto->precio_sin_imp,
                        'nombre_proc' => $producto->nombre_producto,
                    ]
                );
                return response()->json(['success'=> $datos]);
            }else{
                $cantidad = $datos['cantidad'];
				 $descripcioncot = $datos['descripcioncot'];
                $producto = Productos::find($datos['sales_item']);
                $valor_neto = $producto->precio_sin_imp * $datos['cantidad'];
                $valor_imp = ($valor_neto * $producto->porcentaje_imp)/100;
                $pedidos_item = Pedidos_item::create(
                    [
                        'idpedido' => $datos['idpedido'],
                        'idproducto' => $producto->idproducto,
                            'cantidad_ped' => $cantidad,
                        'valor_neto' => $valor_neto,
                        'valor_impuesto' => $valor_imp,
                        'valor_descuento' => 0,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'descuento_prc' => 0,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'costo_utilidad' => $producto->precio_sin_imp,
                        'nombre_proc' => $descripcioncot,
                    ]
                );
                return response()->json(['success'=> $datos]);
            }
        }
    }

       public function eliminarLineaPedido(Request $request)
    {
        $input = $request->all();
        $pedidos_item = Pedidos_item::find($input['idpedidositem']);
        $pedidos_item->delete();
        return response()->json(['success'=> $input]);

    }

        public function actualiarCantPedido(Request $request)
    {
        $input = $request->all();
        $sales_item = Pedidos_item::find($input['idpedidositem']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = $sales_item->costo_utilidad * $input['cantidad'];
        if ($sales_item->descuento_prc > 0) {
            $total_descuento = ($total_neto * $sales_item->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
        $total_neto = $total_neto - $total_descuento;
        $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;

        $actualizar = Pedidos_item::where('idpedidositem', $input['idpedidositem'])->update(['cantidad_ped' => $input['cantidad'], 'valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento]);

        return response()->json(['success'=> $input]);
    }


//por omairena 12-01-2022
    public function actualiarivap(Request $request)
    {
        $input = $request->all();
        $sales_item = Pedidos_item::find($input['idpedidositem']);
        $producto = Productos::find($input['idproducto']);
        //$total_neto = $costo_uni_civa * $sales_item->cantidad;
        $total_neto = round($input['costo_con_iva'] / (1+($producto->porcentaje_imp/100)),5);


        //$costo_uni_civa = ($input['costo_con_iva'] / $sales_item->cantidad) - $total_impuesto;
        //$costo_uni_civa = ($input['costo_con_iva'] / $sales_item->cantidad) - $total_impuesto;
        //$costo_uni_civa = ($total_neto / $sales_item->cantidad);
          $costo_uni_civa = round(($total_neto / $sales_item->cantidad_ped),5);
          $total_neto = $costo_uni_civa * $sales_item->cantidad_ped;
          $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;
        if ($sales_item->descuento_prc > 0) {
           $total_descuento = ($total_neto * $sales_item->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
        
       // $total_neto = $total_neto - $total_descuento;
       $total_neto = $total_neto - $total_descuento;
        //$actualizar = Pedidos_item::where('idpedidositem', $input['idpedidositem'])->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $mto_descuento, 'descuento_prc' => $input['porcentaje_descuento']]);
        $actualizar = Pedidos_item::where('idpedidositem', $input['idpedidositem'])->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento, 'costo_utilidad' =>  $costo_uni_civa]);

        return response()->json(['success'=> $input]);
    }

    //26-05-2021


        public function actualiarDescPedido(Request $request)
    {
        $input = $request->all();
        $sales_item = Pedidos_item::find($input['idpedidositem']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = $sales_item->costo_utilidad * $sales_item->cantidad_ped;
        $mto_descuento = ($total_neto * $input['porcentaje_descuento'])/100;
        $total_neto = $total_neto - $mto_descuento;
        $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;
        $actualizar = Pedidos_item::where('idpedidositem', $input['idpedidositem'])->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $mto_descuento, 'descuento_prc' => $input['porcentaje_descuento']]);
        return response()->json(['success'=> $input]);

    }

        public function update(Request $request, Pedidos $model)
    {
        $datos = $request->all();
        $pedidos  = Pedidos::where('idpedido', $datos['idpedido'])->update(
            [
                'numero_documento' => $datos['numero_documento'],
                'idcliente' => $datos['cliente'],
                'idcaja' => $datos['idcaja'],
                'observaciones' => $datos['observaciones'],
                'estatus_doc' => 2,
                'fecha_doc' => date('c')
            ]
        );
        $pedidos_item = Pedidos_item::where('idpedido', $datos['idpedido'])->get();
        $total_neto_ped = 0;
        $total_descuento_ped = 0;
        $total_impuesto_ped = 0;
        $total_comprobante_ped = 0;
        $total_mercancia_grav_ped = 0;
        $total_mercancia_exenta_ped = 0;
        $total_exonerado_ped = 0;
        $total_mercancia_exonerada_ped = 0;
        $total_exento_ped = 0;
        $total_iva_devuelto_ped = 0;
        $total_serv_grab_ped = 0;
        $total_serv_exento_ped = 0;
        $total_serv_exonerado_ped = 0;
        $total_otros_cargos_ped = 0;
        foreach ($pedidos_item as $s_i) {
            $producto = Productos::find($s_i->idproducto);
            $cantidad_stock = $producto->cantidad_stock - $s_i->cantidad_ped;
            if ($producto->tipo_producto === 2) {
                $total_serv_grab_ped = $total_serv_grab_ped + ($s_i->costo_utilidad * $s_i->cantidad_ped);
                $total_neto_ped = $total_neto_ped + $s_i->valor_neto;
                $total_descuento_ped = $total_descuento_ped + $s_i->valor_descuento;
                $total_impuesto_ped = $total_impuesto_ped + $s_i->valor_impuesto;
            }else{
                $total_mercancia_grav_ped = $total_mercancia_grav_ped + ($s_i->costo_utilidad * $s_i->cantidad_ped);
                $total_neto_ped = $total_neto_ped + $s_i->valor_neto;
                $total_descuento_ped = $total_descuento_ped + $s_i->valor_descuento;
                $total_impuesto_ped = $total_impuesto_ped + $s_i->valor_impuesto;
            }
        }
        $total_comprobante_ped = $total_comprobante_ped + (((($total_mercancia_grav_ped + $total_mercancia_exonerada_ped + $total_serv_grab_ped)-$total_descuento_ped) + $total_impuesto_ped) - $total_iva_devuelto_ped);
        $act_pedido  = Pedidos::where('idpedido', $datos['idpedido'])->update([
                    'total_serv_grab_ped' => $total_serv_grab_ped,
                    'total_serv_exonerado_ped' => $total_serv_exonerado_ped,
                    'total_mercancia_grav_ped' => $total_mercancia_grav_ped,
                    'total_mercancia_exonerada_ped' => $total_mercancia_exonerada_ped,
                    'total_iva_devuelto_ped' => $total_iva_devuelto_ped,
                    'total_neto_ped' => $total_neto_ped,
                    'total_descuento_ped' => $total_descuento_ped,
                    'total_impuesto_ped' => $total_impuesto_ped,
                    'total_comprobante_ped' => $total_comprobante_ped
        ]);

        $new = $datos['numero_documento'] + 1;
        $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['tipo_documento'].' and idcaja = '.$datos['idcaja']);

        return redirect()->action('DonwloadController@correoCotizacion', ['id' => $datos['idpedido']])->withStatus(__('Cotización Agregada Correctamente.'));
    }

        public function convertirPedido($id)
    {
        $pedido = Pedidos::find($id);
        $pedido_item = Pedidos_item::where('idpedido', $id)->get();
        $cajas = Cajas::where([
            ['idcaja','=', $pedido->idcaja],
            ['estatus', '=', 1]
        ])->get();

        if (count($cajas) <= 0) {
            return redirect()->route('pedidos.index')->withStatus(__('Verifique que la caja este abierta.'));
        }
        $p_credito = '00';
        $actividades = Actividad::where('idconfigfact', $pedido->idconfigfact)->get();
        if (count($actividades) <= 0) {
            return redirect()->route('pedidos.index')->withStatus(__('Verifique que exista alguna actividad en la empresa.'));
        }
        $consecutivo = DB::table('consecutivos')->where([
            ['idcaja', '=', $pedido->idcaja],
            ['tipo_documento', '=', '01'],
        ])->get();
        $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        if (Auth::user()->config_u[0]->usa_cotizacion_adicional > 0) {
            $observaciones = $pedido->value_label_aditional_1. ' / '. $pedido->value_label_aditional_2 .' / '.$pedido->value_label_aditional_3;
        } else {
            $observaciones = '';
        }
        $sales = Sales::create(
            [
                'numero_documento' => $numero_factura,
                'tipo_documento' => '01',
                'punto_venta' => str_pad($cajas[0]->codigo_unico, 5, "0", STR_PAD_LEFT),
                'idcaja' => $pedido->idcaja,
                'idconfigfact' => $pedido->idconfigfact,
                'idcodigoactv' => $actividades[0]->codigo_actividad,
                'idcliente' => $pedido->idcliente,
                'tipo_moneda' => 'CRC',
                'condicion_venta' => '01',
                'p_credito' => $p_credito,
                'medio_pago' => '01',
                'observaciones' => $pedido->observaciones,
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
                'tiene_exoneracion' => '00',
                'fecha_creada' => date('Y-m-d'),
                'observaciones' => $observaciones,
                'estatus_sale' => 1
            ]
        );
        foreach ($pedido_item as $item) {
            $producto = Productos::find($item->idproducto);
            $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
            $sales_item = Sales_item::create([
                'idsales' => $sales->idsale,
                'idproducto' => $producto->idproducto,
                'codigo_producto' =>  $producto->codigo_producto,
                'nombre_producto' =>  $item->nombre_proc,
                'costo_utilidad' => $item->costo_utilidad,
                'cantidad' => $item->cantidad_ped,
                'valor_neto' => $item->valor_neto,
                'valor_descuento' => $item->valor_descuento,
                'valor_impuesto' => $item->valor_impuesto,
                'tipo_impuesto' => $item->tipo_impuesto,
                'impuesto_prc' => $item->impuesto_prc,
                'descuento_prc' => $item->descuento_prc,
                'existe_exoneracion' => '00'
            ]);
        }

        $actualizar = Pedidos::where('idpedido', $pedido->idpedido)->update(['estatus_doc' => 3]);

        return redirect()->route('pos.edit', $sales->idsale);
    }

        public function modificarFlotantePed(Request $request)
    {
        $datos = $request->all();
        $input = array();
        parse_str($datos['datos'], $input);
        $sales_item = Pedidos_item::find($input['idsalesitem_flot']);
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

        $actualizar = Pedidos_item::where('idpedidositem', $input['idsalesitem_flot'])
        ->update([
            'cantidad_ped' => $input['cantidad'],
            'valor_neto' => $valor_neto,
            'valor_impuesto' => $valor_impuesto,
            'valor_descuento' => $valor_descuento,
            'tipo_impuesto' => $input['tipo_impuesto'],
            'descuento_prc' => $input['descuento'],
            'impuesto_prc' => $porcentaje_imp,
            'costo_utilidad' => $input['costo_utilidad'],
            'nombre_proc'=> $input['nombre_producto']
        ]);
        return response()->json(['success'=> $input]);
    }

    public function actualiarLabel1Pedido(Request $request)
    {
        $input = $request->all();

        switch ($input['type']) {
            case '1':
                $actualizar = Pedidos::where('idpedido', $input['idpedido'])->update(['value_label_aditional_1' => $input['label']]);
            break;
            case '2':
                $actualizar = Pedidos::where('idpedido', $input['idpedido'])->update(['value_label_aditional_2' => $input['label']]);
            break;
            case '3':
                $actualizar = Pedidos::where('idpedido', $input['idpedido'])->update(['value_label_aditional_3' => $input['label']]);
            break;
        }
        return response()->json(['success'=> $input]);

    }
}
