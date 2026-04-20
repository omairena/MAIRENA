<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Cliente;
use App\Sales;
use App\Receptor;
use App\Sales_item;
use App\Consecutivos;
use App\Facelectron;
use App\Actividad;
use DB;
use Illuminate\Contracts\Container\Container;
use App\Items_exonerados;
use App\Productos;
use App\Unidades_medidas;
use DataTables;
use App\Cxcobrar;
use App\Mov_cxcobrar;
use App\Log_cxcobrar;
use App\Cajas;
use Auth;
use Session;
use Validator;
use App\Otrocargo;
use App\Listprice;
use App\MedioPago;
use Carbon\Carbon;

class FacturacionController extends Controller
{
         public function index()
    {


       app('App\Http\Controllers\PeticionesController')->ajaxConsultar();
       app('App\Http\Controllers\DonwloadController')->envio_masivo();
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-t');
        $sales = Sales::where([
            ['tipo_documento', '!=','96'],
            ['tipo_documento', '!=','95'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $fecha_desde],
            ['fecha_creada', '<=', $fecha_hasta]
        ])->orderBy('numero_documento', 'desc')->get();
          $cxcobrar = Cliente::where([
          ['idconfigfact', '=', Auth::user()->idconfigfact]])->get();
      // dd($cxcobrar);
    	return view('facturacion.index', ['sales' => $sales], ['cxcobrar' => $cxcobrar]);
    }

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
//dd($terminos[0]->status);
        if ($terminos[0]->status == 0){

            Session::flash('message', "CUENTA BLOQUEADA, contacta al administrador tel: 8309-3816");
            return redirect()->route('facturar.index');
        }
          if($terminos[0]->fecha_plan <= $hoy){

            Session::flash('message', "Plan Caducado por fecha final de plan, para renovar contacta al administrador tel: 8309-3816");
            return redirect()->route('cajas.index');

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
        $cajas = DB::table('caja_usuario')
        ->leftjoin('cajas', 'caja_usuario.idcaja', '=', 'cajas.idcaja')
        ->select('cajas.*')
        ->where('caja_usuario.idusuario', '=', Auth::user()->id)
        ->where('cajas.idconfigfact', '=', Auth::user()->idconfigfact)
        ->where('caja_usuario.estado', '=', 1)
        ->get();
       //  dd( Auth::user()->id);
       // dd($cajas);
        $cja = $cajas->count();
       // dd($cajas);
        if ($cja<1){

            Session::flash('message', "Asignar una caja para el Usuario que inicio Sesión");
            return redirect()->route('cajas.index');

        }
        $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $clientes = Cliente::where([
            ['tipo_cliente', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        //$productos = Productos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
            $productos = Productos::where([['productos.idconfigfact', '=', Auth::user()->idconfigfact],
               ['productos.codigo_cabys', '!=', 0],
               ])->get();
        $cajas = Cajas::where([
            ['estatus', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        $contado = Cliente::where([
            ['num_id', '100000000'],
        //['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();

        return view('facturacion.create', ['configuracion'  => $configuracion, 'clientes'  => $clientes, 'productos' => $productos, 'cajas' => $cajas, 'contado' => $contado]);
    }


         public function guardar(Request $request, Sales $model)
    {
        $datos = $request->all();
        
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

        if (isset($datos['email'])) {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                Session::flash('message', "Asignar un Email Valido, para continuar con la factura");
                return redirect()->route('facturar.create');
            }
        }
if (isset($datos['solo_oc'])) {
      $sales = Sales::create(
                    [
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
                        'observaciones' => $observaciones,
                        'estatus_sale' => 1,
                        'creado_por' => Auth::user()->email,
                    ] );
  
   return redirect()->route('facturar.edit', $sales->idsale);
 }
        if (Auth::user()->config_u[0]->es_transporte > 0) {
            if (isset($datos['datos_internos'] )) {
                 if ($datos['datos_internos'] > 0 ) {
                //dd($datos['datos_internos']);
                $sales = Sales::create(
                    [
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
                        'observaciones' => $observaciones,
                        'estatus_sale' => 1,
                        'creado_por' => Auth::user()->email,
                    ]
                );
            }else{
                $var_cliente = json_decode($datos['cliente_hacienda']);
                
                //dd($var_cliente);
               // if (count($var_cliente->{'actividades'}) > 0) {
                  //  $razon_social = $var_cliente->{'actividades'}[0]->{'descripcion'};
                 //   $codigo_actividad = $var_cliente->{'actividades'}[0]->{'codigo'};
               // }else{
                    $razon_social = $var_cliente->{'nombre'};
                    $codigo_actividad = '0';
               // }
                $cliente = Cliente::create(
                    [
                        'idconfigfact' => Auth::user()->idconfigfact,
                        'tipo_id' => $var_cliente->{'tipoIdentificacion'},
                        'num_id' => $datos['ced_receptor'],
                        'nombre' => $var_cliente->{'nombre'},
                        'email' => $datos['email'],
                        'telefono' => Auth::user()->config_u[0]->telefono_emisor,
                        'distrito' => Auth::user()->config_u[0]->distrito_emisor,
                        'canton' => Auth::user()->config_u[0]->canton_emisor,
                        'provincia' => Auth::user()->config_u[0]->provincia_emisor,
                        'direccion' => Auth::user()->config_u[0]->direccion_emisor,
                        'tipo_cliente' => 1,
                        'razon_social' => $razon_social,
                        'nombre_contribuyente' => $var_cliente->{'nombre'},
                        'codigo_actividad' => $codigo_actividad,
                    ]
                );
if($datos['cliente'] != 0){
$cliente_factura=$datos['cliente'];
}
if( !is_null($datos['ced_receptor'])){
$cliente_factura=$cliente->idcliente;
}
//dd($cliente_factura);
$sales = Sales::create(
                    [
                        'numero_documento' => $datos['numero_documento'],
                        'tipo_documento' => $datos['tipo_documento'],
                        'punto_venta' => str_pad($cajas->codigo_unico, 5, "0", STR_PAD_LEFT),
                        'idcaja' => $datos['idcaja'],
                        'idconfigfact' => $datos['idconfigfact'],
                        'idcodigoactv' => $datos['actividad'],
                        'idcliente' => $cliente_factura,
                        'tipo_moneda' => $datos['moneda'],
                        'tipo_cambio' => $datos['tipo_cambio'],
                        'condicion_venta' => $datos['condición_venta'],
                        'p_credito' => $p_credito,
                        'medio_pago' => $datos['medio_pago'],
                        'referencia_pago' => $datos['referencia_pago'],
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
                        'observaciones' => $observaciones,
                        'estatus_sale' => 1,
                        'creado_por' => Auth::user()->email,
                    ]
                );
            }
}
            if(strstr($datos['sales_item'],',') ){
                $valores = explode(',', $datos['sales_item']);
                for ($i=0; $i < count($valores); $i++) {
                    $producto = Productos::find($valores[$i]);
                    $valor_imp = round(($producto->precio_sin_imp * $producto->porcentaje_imp)/100,5);
                    $sales_item = Sales_item::create(
                        [
                            'idsales' => $sales->idsale,
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
                            'existe_exoneracion' => $datos['existe_exoneracion']
                        ]
                    );

                }
                return redirect()->route('facturar.edit',  $sales->idsale);
            }else{
                if (Auth::user()->config_u[0]->es_transporte > 0) {
                    $producto = Productos::find($datos['productos_t']);
                   // dd($datos);
                    if (isset($datos['condicion_iva'])) {
                       $total_neto = round(($datos['precio_t'] / (($producto->porcentaje_imp/100)+1)),5);
                        $total_impuesto = round(($total_neto * $producto->porcentaje_imp)/100,5);

                    }else{

                        $total_neto = $datos['precio_t'];
                     $total_impuesto = round(($total_neto * $producto->porcentaje_imp)/100,5);
                    }
                    $sales_item = Sales_item::create(
                        [
                            'idsales' => $sales->idsale,
                            'idproducto' => $datos['productos_t'],
                            'codigo_producto' =>  $producto->codigo_producto,
                            'nombre_producto' =>  $producto->nombre_producto,
                            'costo_utilidad' => $total_neto,
                            'cantidad' => 1,
                            'valor_neto' => $total_neto,
                            'valor_descuento' => 0,
                            'valor_impuesto' => $total_impuesto,
                            'tipo_impuesto' => $producto->impuesto_iva,
                            'impuesto_prc' => $producto->porcentaje_imp,
                            'descuento_prc' => 0,
                            'existe_exoneracion' => $datos['existe_exoneracion']
                        ]
                    );
                }else{
                    $producto = Productos::find($datos['sales_item']);
                    $valor_imp = round(($producto->precio_sin_imp * $producto->porcentaje_imp)/100,5);
                    $sales_item = Sales_item::create(
                        [
                            'idsales' => $sales->idsale,
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
                            'existe_exoneracion' => $datos['existe_exoneracion']
                        ]
                    );
                }
                return redirect()->route('facturar.edit', $sales->idsale);
            }
        }else{
            $sales = Sales::create(
            [
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
                'observaciones' => $observaciones,
                'estatus_sale' => 1,
                'creado_por' => Auth::user()->email,
            ]);
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
                    'estatus_mov' => 1,
                    'idsale'=> $sales->idsale
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
                    'estatus_mov' => 1,
                    'idsale'=> $sales->idsale
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
                    $valor_imp = round(($producto->precio_sin_imp * $producto->porcentaje_imp)/100,5);
                    $sales_item = Sales_item::create(
                        [
                            'idsales' => $sales->idsale,
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
                            'existe_exoneracion' => $datos['existe_exoneracion']
                        ]
                    );

                }
                return redirect()->route('facturar.edit', $sales->idsale);
            }else{
                //if(isset($datos['sales_item'])){
                //$producto = Productos::find( $datos['sales_item']);
                //}else{
                 //  $producto = Productos::find( $datos['productos_t']);
                //}
                
                              if (isset($datos['sales_item'])) {
        $producto = Productos::find($datos['sales_item']);
    } elseif (isset($datos['productos_t'])) {
        $producto = Productos::find($datos['productos_t']);
    } else {
        // Manejo del caso donde no se encuentra el producto
        session()->flash('error_message', 'Ningún producto encontrado.');
        return redirect()->route('facturar.create'); // Redirigir a la ruta correcta
    }
    

                if (isset($datos['condicion_iva'])) {
                     // dd($datos);
                        $total_neto = $datos['precio_t'] / (($producto->porcentaje_imp/100)+1);
                        $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;


                    }else{


                        if(isset($datos['sales_item'])){
                        $total_neto = $producto->precio_sin_imp ;
                        $total_impuesto = round(($total_neto * $producto->porcentaje_imp)/100,5);
                        }else{
                            $total_neto = $datos['precio_t'] ;
                        $total_impuesto = round(($total_neto * $producto->porcentaje_imp)/100,5);
                        }

                        }


                $valor_imp = ($datos['precio_t'] * $producto->porcentaje_imp)/100;
                $sales_item = Sales_item::create(
                    [
                            'idsales' => $sales->idsale,
                            'idproducto' => $datos['productos_t'],
                            'codigo_producto' =>  $producto->codigo_producto,
                            'nombre_producto' =>  $producto->nombre_producto,
                            'costo_utilidad' => $total_neto,
                            'cantidad' => 1,
                            'valor_neto' => $total_neto,
                            'valor_descuento' => 0,
                            'valor_impuesto' => $total_impuesto,
                            'tipo_impuesto' => $producto->impuesto_iva,
                            'impuesto_prc' => $producto->porcentaje_imp,
                            'descuento_prc' => 0,
                            'existe_exoneracion' => $datos['existe_exoneracion']
                    ]
                );
                return redirect()->route('facturar.edit', $sales->idsale);
            }
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
            $clientes = Cliente::where([
                ['tipo_cliente', 1],
                //['idconfigfact', '=', Auth::user()->idconfigfact]
            ])->get();
            $sales = Sales::find($id);
            $cajas = Cajas::where([
                ['estatus', 1],
                ['idconfigfact', '=', Auth::user()->idconfigfact]
            ])->get();
            $sales_item = Sales_item::where('idsales', $id)->get();
            $sales_item_otrocargo = Otrocargo::where('idsales', $id)->get();

          //  if (count($sales_item) > 0) {
           ///     $productos = \DB::table('productos')->select('productos.*')
           //     ->whereNotExists( function ($query) use ($sales_item) {
           //     $query->select(DB::raw(1))
           //     ->from('sales_item')
            //    ->whereRaw('sales_item.idproducto = productos.idproducto')
            //    ->where('sales_item.idsales', '=', $sales_item[0]->idsales);
            //    })->where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
          //  }else{
 $usuario = Cliente::find($sales->idcliente);
               $productos = Productos::where([['productos.idconfigfact', '=', Auth::user()->idconfigfact],
               ['productos.codigo_cabys', '!=', 0],
               ])->get();
         //   }
            return view('facturacion.edit', ['clientes'  => $clientes, 'productos' => $productos, 'sales' => $sales, 'usuario' => $usuario, 'sales_item' => $sales_item, 'cajas' => $cajas, 'sales_item_otrocargo' => $sales_item_otrocargo]);
        }
    }
 public function agregarcorreoFactura(Request $request)
{
    // Debug opcional (desactívalo en producción)
    // $datos = $request->all();
    // dd($datos);

    // Validación básica de parámetros esperados
    $request->validate([
        'idsale' => 'required|integer',
        'correos' => 'required|string', // puede ser vacío si quieres permitirlo, ajusta según necesidad
    ]);

    // Extraer datos con seguridad
    $idsale = $request->input('idsale');
    $correos = $request->input('correos'); // cadena CSV, por ejemplo: "a@dom.com,b@dom.com"

    try {
        $updated = Sales::where('idsale', $idsale)
                    ->update(['cc_correo' => $correos]);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Correos guardados correctamente.']);
        } else {
            return response()->json(['success' => false, 'message' => 'No se pudo actualizar.'], 500);
        }
    } catch (\Exception $e) {
        // Registro del error para depurar
        \Log::error('Error al guardar correos de factura: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error del servidor.'], 500);
    }
}

        public function agregarLineaFactura(Request $request)
    {
        $datos = $request->all();

        $producto_ml = Productos::find($datos['sales_item']);
       

          if (!isset($datos['monto_total'])) {
          $monto_linea = $producto_ml->precio_sin_imp;


         }else{
              $monto_linea = $datos['monto_total'];
         }

        $sales = Sales::find($datos['idsale']);

        if(strstr($datos['sales_item'],',') ){
            $valores = explode(',', $datos['sales_item']);
            for ($i=0; $i < count($valores); $i++) {
                $producto = Productos::find($valores[$i]);
                $valor_descuento = 0;
                $descuento_prc = 0;
                if($sales->uso_listaprecio > 0){
                    $cantidad = 1;
                    $lista = Listprice::find($sales->idlistaprecio);
                    $total_neto = $producto->precio_sin_imp * $cantidad;
                    // porcentaje total
                    $mto_descuento = ($total_neto * $lista->porcentaje)/100;
                    $total_neto = $total_neto - $mto_descuento;
                    $descuento_prc += $lista->porcentaje;
                    $valor_descuento += $mto_descuento;
                    $valor_imp = round(($total_neto * $producto->porcentaje_imp)/100,5);


                } else {

                    $valor_imp = round(($producto->precio_sin_imp * $producto->porcentaje_imp)/100,5);
                }
                $sales_item = Sales_item::create(
                    [
                        'idsales' => $datos['idsale'],
                        'idproducto' => $producto->idproducto,
                        'codigo_producto' =>  $producto->codigo_producto,
                        'nombre_producto' =>  $producto->nombre_producto,
                        'costo_utilidad' => $producto->precio_sin_imp,
                        'cantidad' => 1,
                        'valor_neto' => $producto->precio_sin_imp,
                        'valor_descuento' => $valor_descuento,
                        'valor_impuesto' => $valor_imp,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'descuento_prc' => $descuento_prc,
                        'existe_exoneracion' => '00'
                    ]);
            }
            return response()->json(['success'=> $datos]);
        }else{
          if (!isset($datos['monto_total'])) {
            if (is_null($datos['cantidad'])) {
                $cantidad = 1;
                $producto = Productos::find($datos['sales_item']);
                $valor_descuento = 0;
                $descuento_prc = 0;
                if($sales->uso_listaprecio > 0){

                    $lista = Listprice::find($sales->idlistaprecio);
                    $total_neto = $producto->precio_sin_imp * $cantidad;
                    // porcentaje total
                    $mto_descuento = ($total_neto * $lista->porcentaje)/100;
                    $total_neto = $total_neto - $mto_descuento;
                    $descuento_prc += $lista->porcentaje;
                    $valor_descuento += $mto_descuento;
                    $valor_imp = round(($total_neto * $producto->porcentaje_imp)/100,5);

                } else {

                    $valor_imp = round(($producto->precio_sin_imp * $producto->porcentaje_imp)/100,5);
                }

                $sales_item = Sales_item::create(
                    [
                    'idsales' => $datos['idsale'],
                    'idproducto' => $producto->idproducto,
                    'codigo_producto' =>  $producto->codigo_producto,
                    'nombre_producto' =>  $producto->nombre_producto,
                    'costo_utilidad' => $producto->precio_sin_imp,
                    'cantidad' => $cantidad,
                    'valor_neto' => $producto->precio_sin_imp,
                    'valor_descuento' => $valor_descuento,
                    'valor_impuesto' => $valor_imp,
                    'tipo_impuesto' => $producto->impuesto_iva,
                    'impuesto_prc' => $producto->porcentaje_imp,
                    'descuento_prc' => $descuento_prc,
                    'existe_exoneracion' => '00'
                    ]
                );
                return response()->json(['success'=> $datos]);
            }else{
                $cantidad = $datos['cantidad'];
                $producto = Productos::find($datos['sales_item']);
                $valor_descuento = 0;
                $descuento_prc = 0;
                if($sales->uso_listaprecio > 0){

                    $lista = Listprice::find($sales->idlistaprecio);
                    $valor_neto = round(($producto->precio_sin_imp * $cantidad),5);
                    // porcentaje total
                    $mto_descuento = ($valor_neto * $lista->porcentaje)/100;
                    $valor_neto = round(($valor_neto - $mto_descuento),5);
                    $descuento_prc += $lista->porcentaje;
                    $valor_descuento += $mto_descuento;
                    $valor_impuesto = round(($valor_neto * $producto->porcentaje_imp)/100,5);

                } else {

                    $valor_neto = round(($producto->precio_sin_imp * $datos['cantidad']),5);
                    $valor_impuesto = round(($valor_neto * $producto->porcentaje_imp)/100,5);
                }

                //dd($valor_neto);
                $sales_item = Sales_item::create(
                    [
                        'idsales' => $datos['idsale'],
                        'idproducto' => $producto->idproducto,
                        'codigo_producto' =>  $producto->codigo_producto,
                        'nombre_producto' =>  $producto->nombre_producto,
                        'costo_utilidad' => $producto->precio_sin_imp,
                        'cantidad' => $cantidad,
                        'valor_neto' => $valor_neto,
                        'valor_descuento' => $valor_descuento,
                        'valor_impuesto' => $valor_impuesto,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'descuento_prc' => $descuento_prc,
                        'existe_exoneracion' => '00'
                    ]
                );
                 return response()->json(['success'=> $datos]);
            }
        }else{

            // $costo_uni_civa = round($input['costo_con_iva_u'] / (1+($producto->porcentaje_imp/100)),5);


                $cantidad = $datos['cantidad'];
                $monto_linea = $datos['monto_total'];
                $producto = Productos::find($datos['sales_item']);
                $valor_descuento = 0;
                $descuento_prc = 0;
                if($sales->uso_listaprecio > 0){

                    $lista = Listprice::find($sales->idlistaprecio);
                    $valor_neto = round(($producto->precio_sin_imp * $cantidad),5);
                    // porcentaje total
                    $mto_descuento = ($valor_neto * $lista->porcentaje)/100;
                    $valor_neto = $valor_neto - $mto_descuento;
                    $descuento_prc += $lista->porcentaje;
                    $valor_descuento += $mto_descuento;
                    $valor_impuesto = round(($valor_neto * $producto->porcentaje_imp)/100,5);

                } else {
                     //$total_neto = round($input['costo_con_iva'] / (1+($producto->porcentaje_imp/100)),5);
                    if ($datos['es_sin_impuesto'] == 0) {
            $precio_sin_iva = $datos['monto_total'];
        }else{
            $precio_unitario = $monto_linea ;
                    $precio_sin_iva = round($precio_unitario / (1+($producto->porcentaje_imp/100)),5);

        }

                    $valor_neto = round(($precio_sin_iva * $datos['cantidad']),5);
                    $valor_impuesto = round(($valor_neto * $producto->porcentaje_imp)/100,5);
                }
                $sales_item = Sales_item::create(
                    [
                        'idsales' => $datos['idsale'],
                        'idproducto' => $producto->idproducto,
                        'codigo_producto' =>  $producto->codigo_producto,
                        'nombre_producto' =>  $producto->nombre_producto,
                        'costo_utilidad' => $precio_sin_iva,
                        'cantidad' => $cantidad,
                        'valor_neto' => $valor_neto,
                        'valor_descuento' => $valor_descuento,
                        'valor_impuesto' => $valor_impuesto,
                        'tipo_impuesto' => $producto->impuesto_iva,
                        'impuesto_prc' => $producto->porcentaje_imp,
                        'descuento_prc' => $descuento_prc,
                        'existe_exoneracion' => '00'
                    ]
                );
                 return response()->json(['success'=> $datos]);
        }

        }
    }

            public function guardarTransporte(Request $request)
    {
        $datos = $request->all();
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
    }



        public function update(Request $request, Sales $model)
    {
        $datos = $request->all();
//dd($datos);
            if ($datos['tipo_documento'] == '01') {
    $Cliente_01 = Cliente::find($datos['cliente']);

    // Verificar que el cliente existe y tiene código de actividad válido
    $codigoActividad = isset($Cliente_01->codigo_actividad) ? $Cliente_01->codigo_actividad : null;

    if ($codigoActividad === 0 || strlen($codigoActividad) < 5) {
        Session::flash('message', "El código de la actividad ligado al cliente no es válido, por favor revisar.");
        return redirect()->route('facturar.create');
    }
}

        $consulta_fac = Facelectron::where([
            ['idsales', '=', $datos['idsale']]
        ])->get();
        if (count($consulta_fac) > 0) {
            $message = 'Por favor realizar nuevamente la factura.';
            return redirect()->route('facturar.index')->withStatus(__(''.$message));
        }else{
            $cajas = Cajas::find($datos['idcaja']);
            if (!is_null($datos['observaciones'])) {
                $observaciones = $datos['observaciones'];
            }else{
                $observaciones = '';
            }
            $tipo_doc_prev = Sales::where([
                ['idsale', '=', $datos['idsale']]
            ])->first();

            $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $datos['idcaja']],
                ['tipo_documento', '=', $tipo_doc_prev->tipo_documento ],
            ])->get();

             //nuevo mandejo de consecutivos
             // Paso 1: Obtener el rango de números de documento
$consecutivo = DB::table('consecutivos')->where('idcaja', $datos['idcaja'])
    ->where('tipo_documento', $tipo_doc_prev->tipo_documento)
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
    ->where('tipo_documento', $tipo_doc_prev->tipo_documento)
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

            if($datos['tipo_documento']!='03'){
                
            $sales  = Sales::where('idsale', $datos['idsale'])->update([
                    'numero_documento' => $numero_factura,
                    'tipo_documento' => $tipo_doc_prev->tipo_documento,
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
                    'fecha_creada' => date('Y-m-d'),
                    'observaciones' => $observaciones,
                    'estatus_sale' => 2,
                     'situacion' => $datos['condicion_comprobante'],
                ]);
    }else{
                $sales  = Sales::where('idsale', $datos['idsale'])->update([
                    'numero_documento' => $numero_factura,
                    'tipo_documento' => $tipo_doc_prev->tipo_documento,
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
                    'fecha_creada' => date('Y-m-d'),
                    'observaciones' => $observaciones,
                    'estatus_sale' => 2,
                    'clave_sale'=> $datos['clave_sale'],
                    'fecha_emision'=>$datos['fecha_emision'],
                    'tipo_doc_ref'=>$datos['tipo_doc_ref'],
                    'tipo_devolucion'=>$datos['tipo_devolucion'],
                    'razon'=>$datos['razon'],
                    'situacion' => $datos['condicion_comprobante'],
                ]);
    }  
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
            $total_exento = $total_serv_exento + $total_mercancia_exenta;
            $total_comprobante = ((($total_mercancia_grav + $total_mercancia_exonerada + $total_mercancia_exenta + $total_serv_grab+ $total_serv_exonerado + $total_serv_exento)-$total_descuento) + $total_impuesto) - $total_iva_devuelto + $total_otros_cargos + $total_mercancia_no_sujeto + $total_serv_no_sujeto;
            $sales  = Sales::where('idsale', $datos['idsale'])->update([
                    'fecha_reenvio' => date('c'),
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
            $seguridad = $this->armarSeguridad($datos['idconfigfact']);
           // $xml =  $this->armarXml($datos['idsale']);
           //dd($datos);
           if($datos['tipo_documento'] == '03'){
          $pos=new NotacController();
           $xml=$pos->armarXmlCredito($datos['idsale'], $datos['clave_sale']);
            include_once(public_path(). '/funcionFacturacion506.php');
            $facturar = Timbrar_documentos($xml, $seguridad);
            $new = $datos['numero_documento'] + 1;
            //$consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$tipo_doc_prev->tipo_documento.' and idcaja = '.$datos['idcaja']);
            return redirect()->action('DonwloadController@correoXml', ['id' => $datos['idsale']])->withStatus(__('Factura Agregada Correctamente.'));
           }else{
           $pos=new PosController();
           $xml=$pos->armarXml($datos['idsale']);
            include_once(public_path(). '/funcionFacturacion506.php');
            $facturar = Timbrar_documentos($xml, $seguridad);
            $new = $datos['numero_documento'] + 1;
            //$consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$tipo_doc_prev->tipo_documento.' and idcaja = '.$datos['idcaja']);
            return redirect()->action('DonwloadController@correoXml', ['id' => $datos['idsale']])->withStatus(__('Factura Agregada Correctamente.'));
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
        $arreglo = [];
        foreach ($detalle as $cuerpo) {
            $producto = Productos::find($cuerpo->idproducto);
            $unidad_medida = Unidades_medidas::find($producto->idunidadmedida);
            $monto_total = round(($cuerpo->costo_utilidad * $cuerpo->cantidad),5) ;//$cuerpo->costo_utilidad * $cuerpo->cantidad;
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

        'RegistroMedicamento' => '' . $producto->forma, // detalle del producto
        'FormaFarmaceutica' => '' . $producto->cod_reg_med, // detalle del producto
    );
}

$estructura += array(
                        'PrecioUnitario' => ''.$cuerpo->costo_utilidad, // precio neto del producto por unidad
                        'MontoTotal' => ''.$monto_total,//round(($cuerpo->costo_utilidad * $cuerpo->cantidad),5) ,//$cuerpo->costo_utilidad * $cuerpo->cantidad, //Valor neto * cantidad
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
                    );
                    if ($cuerpo->tipo_impuesto != '99') {

                        $estructura['Impuesto'] = [
                                array(
                                    'Codigo' => '01', //Es un campo fijo de dos posiciones. Ver notas 7 y 8  en Anexos y Estructuras
                                    'CodigoTarifa' => ''.$cuerpo->tipo_impuesto, // Debe de expresarse en porcentaje.  decimal 4,2
                                    'Tarifa' => ''.$porcentaje_imp,
                                        //'FactorIVA'=>'0.00000',
                                    'Monto' => ''.$cuerpo->valor_impuesto // se obtiene “subtotal” * tarifa del impuesto
                                )
                            ];
                        $estructura['MontoTotalLinea'] = $cuerpo->valor_neto+$cuerpo->valor_impuesto;
                    } else {

                        $estructura['MontoTotalLinea'] = $cuerpo->valor_neto+$cuerpo->valor_impuesto;
                    }
                    array_push($arreglo, $estructura);
            } else {

                $items_exonerados = Items_exonerados::where('idsalesitem', $cuerpo->idsalesitem)->get();
                $impuesto_nto = $cuerpo->valor_impuesto - $items_exonerados[0]->monto_exoneracion;
                $estructura =
                    array(
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

        'RegistroMedicamento' => '' . $producto->forma, // detalle del producto
        'FormaFarmaceutica' => '' . $producto->cod_reg_med, // detalle del producto
    );
}

$estructura += array(
                        'PrecioUnitario' => ''.$cuerpo->costo_utilidad, // precio neto del producto por unidad
                        'MontoTotal' => ''.$monto_total,//.$cuerpo->costo_utilidad * $cuerpo->cantidad, //Valor neto * cantidad
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
                        'MontoTotalLinea' => ($monto_total-$cuerpo->valor_descuento)+$impuesto_nto // se obtiene sumando subtotal + monto impuesto
                    );
                    array_push($arreglo, $estructura);
            }
            //EL ARRAY PUSH DEBE IR DENTRO TAMBIEN DEL WHILE QUE VIENE DE BASE DE DATOS ES MAS PRACTICO
        }
          if($cabecera->tipo_documento != '04'){
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
                        //si es tiquete no lleva este nodo

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

                        //si es tiquete no lleva este nodo
                        'TieneExoneracion' => ''.$cabecera->tiene_exoneracion,
                        'CondicionVenta' => ''.$cabecera->condicion_venta, //2 digitos Validar Anexos y estructura nota # 5 para verificar los diferentes tipos
                        'PlazoCredito' => ''.$cabecera->p_credito, //en caso de la condicion sea credito o igual a 02 debe agregar plazo credito de resto va en 0

                        'MedioPago' => $cabecera->medio_pago, //2 digitos solo para factura y tiquete validar la Nota #6 de Anexos y Estructuras

                        'DetalleServicio' => $arreglo,
                        'ResumenFactura' => [
                            'CodigoTipoMoneda' =>
                                array(
                                    'CodigoMoneda' => $cabecera->tipo_moneda, //validar el documento de Monedas para saber los codigos
                                    'TipoCambio' => ''.$cabecera->tipo_cambio //Valor de cambio en caso de que sea una moneda diferente al colon
                                ),
                            'TotalServGravados' => ''.$cabecera->total_serv_grab, //obligado cuando el servicio tenga IV (IMPUESTO)
                            'TotalServExentos' => ''.$cabecera->total_serv_exento, // OBLIGADO cuando el servicio este exento de IV
                            'TotalServExonerado' => ''.$cabecera->total_serv_exonerado, // Este campo será de condición obligatoria, cuando el servicio esté gravado y se preste a un cliente que goce de exoneración se debe de indicar el monto equivalente al porcentaje exonerado.
                            'TotalMercanciasGravadas' => ''.$cabecera->total_mercancia_grav, //obligado cuando la mercancia tenga IV (IMPUESTO)
                            'TotalMercanciasExentas' => ''.$cabecera->total_mercancia_exenta, // OBLIGADO cuando la mercancia este exento de IV
                            'TotalMercExonerada' => ''.$cabecera->total_mercancia_exonerada, //Este campo será de condición obligatoria, cuando la mercancía o producto se venda a un cliente que goce de exoneración para la compra de la misma.
                            'TotalGravado' => $cabecera->total_mercancia_grav + $cabecera->total_serv_grab,  // se obtiene sumando TotalServGravados + TotalMercanciasGravadas
                            'TotalExento' => $cabecera->total_mercancia_exenta + $cabecera->total_serv_exento, // se obtiene sumando TotalServExentos + TotalMercanciasExentas
                            'TotalExonerado' => $cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado, //Se obtiene de la suma de los campos “total servicios exonerados de IVA” mas “total de mercancías exoneradas del IVA”.
                            'TotalVenta' => (($cabecera->total_mercancia_grav + $cabecera->total_serv_grab) + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento), // se obtiene sumando TotalGravado + TotalExento
                            'TotalDescuentos' => $cabecera->total_descuento, // se obtiene sumando todos los valores de MontoDescuento que tenga la factura
                            'TotalVentaNeta' => (($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta +  $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento), // se obtiene restando “total venta” “total descuento”.
                            'TotalImpuesto' => $cabecera->total_impuesto, // se obtiene sumando todos los montos impuestos del detalle de factura
                            'TotalIVADevuelto' => ''.$cabecera->total_iva_devuelto,
                            'TotalOtrosCargos'  => ''.$cabecera->total_otros_cargos,
                            'TotalComprobante' => (((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto) + $cabecera->total_otros_cargos // se obtiene sumando TotalVentaNeta + TotalImpuesto
                        ]
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

                        'TieneExoneracion' => ''.$cabecera->tiene_exoneracion,
                        'CondicionVenta' => ''.$cabecera->condicion_venta, //2 digitos Validar Anexos y estructura nota # 5 para verificar los diferentes tipos
                        'PlazoCredito' => ''.$cabecera->p_credito, //en caso de la condicion sea credito o igual a 02 debe agregar plazo credito de resto va en 0

                        'MedioPago' => $cabecera->medio_pago, //2 digitos solo para factura y tiquete validar la Nota #6 de Anexos y Estructuras

                        'DetalleServicio' => $arreglo,
                        'ResumenFactura' => [
                            'CodigoTipoMoneda' =>
                                array(
                                    'CodigoMoneda' => $cabecera->tipo_moneda, //validar el documento de Monedas para saber los codigos
                                    'TipoCambio' => ''.$cabecera->tipo_cambio //Valor de cambio en caso de que sea una moneda diferente al colon
                                ),
                            'TotalServGravados' => ''.$cabecera->total_serv_grab, //obligado cuando el servicio tenga IV (IMPUESTO)
                            'TotalServExentos' => ''.$cabecera->total_serv_exento, // OBLIGADO cuando el servicio este exento de IV
                            'TotalServExonerado' => ''.$cabecera->total_serv_exonerado, // Este campo será de condición obligatoria, cuando el servicio esté gravado y se preste a un cliente que goce de exoneración se debe de indicar el monto equivalente al porcentaje exonerado.
                            'TotalMercanciasGravadas' => ''.$cabecera->total_mercancia_grav, //obligado cuando la mercancia tenga IV (IMPUESTO)
                            'TotalMercanciasExentas' => ''.$cabecera->total_mercancia_exenta, // OBLIGADO cuando la mercancia este exento de IV
                            'TotalMercExonerada' => ''.$cabecera->total_mercancia_exonerada, //Este campo será de condición obligatoria, cuando la mercancía o producto se venda a un cliente que goce de exoneración para la compra de la misma.
                            'TotalGravado' => $cabecera->total_mercancia_grav + $cabecera->total_serv_grab,  // se obtiene sumando TotalServGravados + TotalMercanciasGravadas
                            'TotalExento' => $cabecera->total_mercancia_exenta + $cabecera->total_serv_exento, // se obtiene sumando TotalServExentos + TotalMercanciasExentas
                            'TotalExonerado' => $cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado, //Se obtiene de la suma de los campos “total servicios exonerados de IVA” mas “total de mercancías exoneradas del IVA”.
                            'TotalVenta' => (($cabecera->total_mercancia_grav + $cabecera->total_serv_grab) + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento), // se obtiene sumando TotalGravado + TotalExento
                            'TotalDescuentos' => $cabecera->total_descuento, // se obtiene sumando todos los valores de MontoDescuento que tenga la factura
                            'TotalVentaNeta' => (($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta +  $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento), // se obtiene restando “total venta” “total descuento”.
                            'TotalImpuesto' => ''.$cabecera->total_impuesto, // se obtiene sumando todos los montos impuestos del detalle de factura
                            'TotalIVADevuelto' => ''.$cabecera->total_iva_devuelto,
                            'TotalOtrosCargos'  => ''.$cabecera->total_otros_cargos,
                            'TotalComprobante' => (((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto) + $cabecera->total_otros_cargos // se obtiene sumando TotalVentaNeta + TotalImpuesto
                        ]
                    ];
          }
                   // $sales_item_otrocargo = Otrocargo::where('idsales', $idsale)->get();//30-01-2025
//dd($sales_item_otrocargo);
                  //  if (count($sales_item_otrocargo) > 0) {
                      //  $xml['OtrosCargos'] = [];
                      //  foreach ($sales_item_otrocargo as $key => $otro){

                         //   $datail_otrocargo = [];
                         //   $datail_otrocargo['OtrosCargos'][$key]=array();
                         //   $datail_otrocargo['OtrosCargos'][$key]['TipoDocumento'] = $otro->tipo_otrocargo;

                          //  if ($otro->tipo_otrocargo == '04') {

                          //      $datail_otrocargo['OtrosCargos'][$key]['NumeroIdentidadTercero'] = $otro->numero_identificacion;
                          //      $datail_otrocargo['OtrosCargos'][$key]['NombreTercero'] = $otro->nombre;

                          //  }
                          //  $datail_otrocargo['OtrosCargos'][$key]['Detalle'] = $otro->detalle;
                          //  if (!empty($otro->porcentaje_cargo)) {

                          //      $datail_otrocargo['OtrosCargos'][$key]['Porcentaje'] = $otro->porcentaje_cargo;
                         //   }

                         //   $datail_otrocargo['OtrosCargos'][$key]['MontoCargo'] = $otro->monto_cargo;
                         //   array_push($xml['OtrosCargos'], $datail_otrocargo);
                        //}
                   // }

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

                    //dd($xml['OtrosCargos']);
        return $xml;
    }

        public function hacienda(Request $request)
    {
        $input = $request->all();
        $sales = Facelectron::where('idsales',$input['idsales'])->get();
        return response()->json(['success'=> $sales]);
    }

        public function actualiarCantFactura(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = round(($sales_item->costo_utilidad * $input['cantidad']),5);
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
    //descripcion pos
     public function actualiarDescripFactura(Request $request)
    {
        $input = $request->all();

        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['nombre_producto' => $input['nombre_producto_pos']]);

        return response()->json(['success'=> $input]);
    }


    ///descep pos

        public function actualiarDescFactura(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = round(($sales_item->costo_utilidad * $sales_item->cantidad),5);
        $mto_descuento = round((($total_neto * $input['porcentaje_descuento'])/100),5);
        $total_neto = $total_neto - $mto_descuento;
        $total_impuesto = round((($total_neto * $producto->porcentaje_imp)/100),5);
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

        public function agregarExoneracion(Request $request)
    {
        $datos = $request->all();
        $input = array();
        parse_str($datos['datos'], $input);
        \Log::info(json_encode($input));
        $sales_item        = Sales_item::find($input['idsaleitem_exo']);
        $monto_exoneracion = round(($sales_item->valor_neto * $input['porcentaje_exoneracion'])/100,5);
        $exoneracion       = Items_exonerados::create([
            'idsalesitem'            => $input['idsaleitem_exo'],
            'tipo_exoneracion'       => $input['tipo_exoneracion'],
            'numero_exoneracion'     => $input['numero_exoneracion'],
            'institucion'            => $input['institucion'],
            'fecha_exoneracion'      => $input['fecha_exoneracion'],
            'porcentaje_exoneracion' => $input['porcentaje_exoneracion'],
            'articulo'               => $input['articulo'],
            'inciso'                 => $input['inciso'],
            'monto_exoneracion'      => $monto_exoneracion
        ]);

        if ($exoneracion->tipo_exoneracion == '99') {
            $exoneracion->tipo_exoneracion_otro = $input['tipo_exoneracion_otro'];
            $exoneracion->save();
        }

        if ($exoneracion->institucion == '99') {
            $exoneracion->institucion_otro = $input['institucion_otro'];
            $exoneracion->save();
        }
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

       public function numero_factura(Request $request)
    {
        $datos = $request->all();
//dd($datos);
 $cxcobrar = Cliente::where([
          ['idconfigfact', '=', Auth::user()->idconfigfact]])->get();
    //
           $datos = $request->all();
   // Asegúrate de que el valor existe
if (!empty($datos['numero_factura'])) {
    $numeroFactura = str_pad($datos['numero_factura'], 10, '0', STR_PAD_LEFT);
    
    // Ejecuta la consulta con el número formateado
    $sales = Sales::where('numero_documento', '=', $numeroFactura)
                   ->where('idconfigfact', '=', Auth::user()->idconfigfact)
                   ->get();
 //dd($sales);
    // Manejar el caso cuando no hay resultados
}
   	return view('facturacion.index', ['sales' => $sales], ['cxcobrar' => $cxcobrar]);

    

    
    }
       public function filtrarFacturas(Request $request)
    {
        $datos = $request->all();


      if ($datos['estado'] == 0 && $datos['cliente'] == 0 && $datos['tipo_doc'] == 0) {
           $datos = $request->all();
    $sales = Sales::where([
        ['tipo_documento', '!=','96'],
        ['tipo_documento', '!=','95'],
        ['fecha_creada', '>=', $datos['fecha_desde']],
        ['fecha_creada', '<=', $datos['fecha_hasta']],
        ['idconfigfact', '=', Auth::user()->idconfigfact],
    ])->get();
}else{
        if($datos['estado'] == 0){

           if($datos['cliente'] > 0){
           $sales = Sales::where([
            ['tipo_documento', '=', $datos['tipo_doc']],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
            ['idcliente', '=', $datos['cliente']],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
       }else{


        $sales = Sales::where([
            ['tipo_documento', '=', $datos['tipo_doc']],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
       }
        }
        if($datos['estado'] == 1){
           if($datos['cliente'] > 0){
           $sales = Sales::where([
            ['tipo_documento', '=', $datos['tipo_doc']],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
            ['idcliente', '=', $datos['cliente']],
            ['referencia_sale', '>', 0],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
       }else{


        $sales = Sales::where([
            ['tipo_documento', '=', $datos['tipo_doc']],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
            ['referencia_sale', '>', 0],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
       }
        }
       if($datos['estado'] == 2){
           if($datos['cliente'] > 0){
           $sales = Sales::where([
            ['tipo_documento', '=', $datos['tipo_doc']],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
            ['idcliente', '=', $datos['cliente']],
            ['referencia_sale', '=', 0],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
       }else{


        $sales = Sales::where([
            ['tipo_documento', '=', $datos['tipo_doc']],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
            ['referencia_sale', '=', 0],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
       }
       }
}

        $cxcobrar = Cliente::where([
          ['idconfigfact', '=', Auth::user()->idconfigfact]])->get();
      // dd($cxcobrar);
    	return view('facturacion.index', ['sales' => $sales], ['cxcobrar' => $cxcobrar]);

    }

        public function actualiarCostoFactura(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = round(($input['costo_utilidad'] * $sales_item->cantidad),5);
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
    //por omairena
    public function actualiariva(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $producto = Productos::find($input['idproducto']);
        //$total_neto = $costo_uni_civa * $sales_item->cantidad;
        $total_neto = round($input['costo_con_iva'] / (1+($producto->porcentaje_imp/100)),5);


        //$costo_uni_civa = ($input['costo_con_iva'] / $sales_item->cantidad) - $total_impuesto;
        //$costo_uni_civa = ($input['costo_con_iva'] / $sales_item->cantidad) - $total_impuesto;
        //$costo_uni_civa = ($total_neto / $sales_item->cantidad);
          $costo_uni_civa = round(($total_neto / $sales_item->cantidad),5);
          $total_neto = $costo_uni_civa * $sales_item->cantidad;
          $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;
        if ($sales_item->descuento_prc > 0) {
           $total_descuento = ($total_neto * $sales_item->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
       // $total_neto = $total_neto - $total_descuento;
       $total_neto = $total_neto - $total_descuento;
        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento, 'costo_utilidad' =>  $costo_uni_civa]);

        return response()->json(['success'=> $input]);
    }
    //omairena 01-02-2023
     public function actualiariva_u(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $producto = Productos::find($input['idproducto']);
        //$total_neto = $costo_uni_civa * $sales_item->cantidad;
        $costo_uni_civa = round($input['costo_con_iva_u'] / (1+($producto->porcentaje_imp/100)),5);
        //$total_neto = round($input['costo_con_iva'] / (1+($producto->porcentaje_imp/100)),5);


        //$costo_uni_civa = ($input['costo_con_iva'] / $sales_item->cantidad) - $total_impuesto;
        //$costo_uni_civa = ($input['costo_con_iva'] / $sales_item->cantidad) - $total_impuesto;
        //$costo_uni_civa = ($total_neto / $sales_item->cantidad);
         // $costo_uni_civa = round(($total_neto / $sales_item->cantidad),5);
          $total_neto = $costo_uni_civa * $sales_item->cantidad;
          $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;
        if ($sales_item->descuento_prc > 0) {
           $total_descuento = ($total_neto * $sales_item->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
       // $total_neto = $total_neto - $total_descuento;
       $total_neto = $total_neto - $total_descuento;
        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento, 'costo_utilidad' =>  $costo_uni_civa]);

        return response()->json(['success'=> $input]);
    }

//omairena 02-02-2023
     public function actualiarsiva_u(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $producto = Productos::find($input['idproducto']);
        //$total_neto = $costo_uni_civa * $sales_item->cantidad;
        $costo_uni_civa = round($input['costo_sin_iva_u'] ,5);
        //$total_neto = round($input['costo_con_iva'] / (1+($producto->porcentaje_imp/100)),5);


        //$costo_uni_civa = ($input['costo_con_iva'] / $sales_item->cantidad) - $total_impuesto;
        //$costo_uni_civa = ($input['costo_con_iva'] / $sales_item->cantidad) - $total_impuesto;
        //$costo_uni_civa = ($total_neto / $sales_item->cantidad);
         // $costo_uni_civa = round(($total_neto / $sales_item->cantidad),5);
          $total_neto = $costo_uni_civa * $sales_item->cantidad;
          $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;
        if ($sales_item->descuento_prc > 0) {
           $total_descuento = ($total_neto * $sales_item->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
       // $total_neto = $total_neto - $total_descuento;
       $total_neto = $total_neto - $total_descuento;
        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento, 'costo_utilidad' =>  $costo_uni_civa]);

        return response()->json(['success'=> $input]);
    }

    //26-05-2021
//por omairena 13-06-2021 convertir factura
public function convertirfr($id)
    {
        // Validacion de nota de credito y back a la ruta antes de refacturar
        $ncpedido = sales::where('referencia_sale', $id)->get();
        if (count($ncpedido) > 0) {
           // return redirect()->route('simplificado.index')->withStatus(__('Nota de Credito ya creada para este documento.'));
        }
        $pedido = sales::find($id);
        $sales_item = sales_item::where('idsales', $id)->get();
        $cajas = Cajas::where([
            ['idcaja','=', $pedido->idcaja],
            ['estatus', '=', 1]
        ])->get();
        $observaciones = 'Documento de Referencia # '.$pedido->numero_documento.' ';


        if (count($cajas) <= 0) {
            return redirect()->route('pedidos.index')->withStatus(__('Verifique que la caja este abierta.'));
        }
        $p_credito = '00';
        $actividades = Actividad::where('idconfigfact', $pedido->idconfigfact)->get();
        if (count($actividades) <= 0) {
            return redirect()->route('pedidos.index')->withStatus(__('Verifique que exista alguna actividad en la empresa.'));
        }
        //$consecutivo = DB::table('consecutivos')->where([
           // ['idcaja', '=', $pedido->idcaja],
         //   ['tipo_documento', '=',  '01'],
       // ])->get();
      //  $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        // omairena
          $cliente = Cliente::find($pedido->idcliente);
           if ($cliente->es_contado > 0) {

            $tipo_documento = '04';
            $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $pedido->idcaja],
                ['tipo_documento', '=', $tipo_documento],
            ])->get();
            $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);

        } else {
            $tipo_documento = '01';
            $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $pedido->idcaja],
                ['tipo_documento', '=', $tipo_documento],
            ])->get();
            $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        }
        //

        $sales = Sales::create(
            [
                //'numero_documento' => $numero_factura,
                  // 'tipo_documento' => '01',
                  'numero_documento' => $numero_factura,
                'tipo_documento' => $tipo_documento,

                'punto_venta' => str_pad($cajas[0]->codigo_unico, 5, "0", STR_PAD_LEFT),
                'idcaja' => $pedido->idcaja,
                'idconfigfact' => $pedido->idconfigfact,
                'idcodigoactv' => $actividades[0]->codigo_actividad,
                'idcliente' => $pedido->idcliente,
                'tipo_moneda' => 'CRC',
                'condicion_venta' => '01',
                'p_credito' => $p_credito,
                //'medio_pago' => '01',
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
                'tiene_exoneracion' => '00',
                'fecha_creada' => date('Y-m-d'),
                'estatus_sale' => 1,
                'total_abonos_op' => $pedido->total_abonos_op,
                'viene_de_op' => $id,
            ]
        );
        foreach ($sales_item as $item) {
            $producto = Productos::find($item->idproducto);
            $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
            $sales_item = Sales_item::create([
                'idsales' => $sales->idsale,
                'idproducto' => $producto->idproducto,
                'codigo_producto' =>  $producto->codigo_producto,
                'nombre_producto' =>  $item->nombre_producto,
                'costo_utilidad' => $item->costo_utilidad,
                'cantidad' => $item->cantidad,
                'valor_neto' => $item->valor_neto,
                'valor_descuento' => $item->valor_descuento,
                'valor_impuesto' => $item->valor_impuesto,
                'tipo_impuesto' => $item->tipo_impuesto,
                'impuesto_prc' => $item->impuesto_prc,
                'descuento_prc' => $item->descuento_prc,
                'existe_exoneracion' => '00'
            ]);
        }

       // $actualizar = Pedidos::where('idpedido', $pedido->idpedido)->update(['estatus_doc' => 3]);

        return redirect()->route('pos.edit', $sales->idsale);
    }
    
   public function ver_fact($id)
{
    // Verificar el ID
    \Log::info("Verificando la venta con ID: $id");
    $sales = Sales::find($id);
    
    if (!$sales) {
        \Log::info("No se encontró la venta con ID: $id");
        return redirect()->route('pos.index')->with('error', 'Venta no encontrada.');
    }

    $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    $sales_item = Sales_item::where('idsales', $id)->get();
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

    $productos = Productos::where([
        ['productos.idconfigfact', '=', Auth::user()->idconfigfact],
        ['productos.codigo_cabys', '!=', 0],
    ])->get();

    if ($configuracion[0]->usa_listaprecio > 0) {
        $lista_cli = DB::table('clientes_list_price')
            ->leftJoin('list_price', 'clientes_list_price.idlist', '=', 'list_price.idlist')
            ->select('list_price.*')
            ->where('clientes_list_price.idcliente', '=', $sales->idcliente)
            ->get();
    } else {
        $lista_cli = [];
    }
    
    $medio_pagos = MedioPago::where('activo', 1)->get();
    $selectedMediosPago = $sales->medioPagos;

    // Devuelve la vista
    return view('pos.ver', [
        'configuracion' => $configuracion,
        'clientes' => $clientes,
        'productos' => $productos,
        'sales' => $sales,
        'sales_item' => $sales_item,
        'cajas' => $cajas,
        'usuario' => $usuario,
        'sales_item_otrocargo' => $sales_item_otrocargo,
        'lista_cli' => $lista_cli,
        'medio_pagos' => $medio_pagos,
        'selectedMediosPago' => $selectedMediosPago
    ]);
}
    

    public function convertirAutomaticafr($id)
    {
        // Validacion de nota de credito y back a la ruta antes de refacturar
        $ncpedido = sales::where('referencia_sale', $id)->get();
        if (count($ncpedido) > 0) {
            return redirect()->route('simplificado.index')->withStatus(__('Nota de Credito ya creada para este documento.'));
        }
        $pedido = sales::find($id);
        $sales_item = sales_item::where('idsales', $id)->get();
        $cajas = Cajas::where([
            ['idcaja','=', $pedido->idcaja],
            ['estatus', '=', 1]
        ])->get();
        $observaciones = 'Documento de Referencia # '.$pedido->numero_documento.' ';
        if (count($cajas) <= 0) {
            return redirect()->route('pedidos.index')->withStatus(__('Verifique que la caja este abierta.'));
        }
        $p_credito = '00';
        $actividades = Actividad::where('idconfigfact', $pedido->idconfigfact)->get();
        if (count($actividades) <= 0) {
            return redirect()->route('pedidos.index')->withStatus(__('Verifique que exista alguna actividad en la empresa.'));
        }
        //Nuevo codigo y validacion para verificar el tipo de documento cuando sea cliente contado
        $cliente = Cliente::find($pedido->idcliente);
        if ($cliente->es_contado > 0) {

            $tipo_documento = '04';
            $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $pedido->idcaja],
                ['tipo_documento', '=', $tipo_documento],
            ])->get();
            $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);

        } else {
            $tipo_documento = '01';
            $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $pedido->idcaja],
                ['tipo_documento', '=', $tipo_documento],
            ])->get();
            $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        }

        $sales = Sales::create(
            [
                'numero_documento' => $numero_factura,
                'tipo_documento' => $tipo_documento,
                'punto_venta' => str_pad($cajas[0]->codigo_unico, 5, "0", STR_PAD_LEFT),
                'idcaja' => $pedido->idcaja,
                'idconfigfact' => $pedido->idconfigfact,
                'idcodigoactv' => $actividades[0]->idcodigoactv,
                'idcliente' => $pedido->idcliente,
                'tipo_moneda' => $pedido->tipo_moneda,
                'condicion_venta' => $pedido->condicion_venta,
                'p_credito' => $p_credito,
                'medio_pago' => $pedido->medio_pago,
                'observaciones' => $observaciones,
                'total_serv_grab' =>  $pedido->total_serv_grab,
                'total_serv_exento' =>  $pedido->total_serv_exento,
                'total_serv_exonerado' =>  $pedido->total_serv_exonerado,
                'total_mercancia_grav' => $pedido->total_mercancia_grav,
                'total_mercancia_exenta' => $pedido->total_mercancia_exenta,
                'total_mercancia_exonerada' => $pedido->total_mercancia_exonerada,
                'total_exento' => $pedido->total_exento,
                'total_exonerado' => $pedido->total_exonerado,
                'total_neto' => $pedido->total_neto,
                'total_descuento' => $pedido->total_descuento,
                'total_impuesto' => $pedido->total_impuesto,
                'total_otros_cargos' => $pedido->total_otros_cargos,
                'total_iva_devuelto' => $pedido->total_iva_devuelto,
                'total_comprobante' => $pedido->total_comprobante,
                'tiene_exoneracion' => $pedido->tiene_exoneracion,
                'fecha_creada' => date('Y-m-d'),
                'estatus_sale' => 2,
                'total_abonos_op' => $pedido->total_abonos_op,
                'estatus_op' => 1,
            ]
        );
        foreach ($sales_item as $item) {
            $producto = Productos::find($item->idproducto);
            $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
            $sales_item = Sales_item::create([
                'idsales' => $sales->idsale,
                'idproducto' => $producto->idproducto,
                'codigo_producto' =>  $producto->codigo_producto,
                'nombre_producto' =>  $producto->nombre_producto,
                'costo_utilidad' => $item->costo_utilidad,
                'cantidad' => $item->cantidad,
                'valor_neto' => $item->valor_neto,
                'valor_descuento' => $item->valor_descuento,
                'valor_impuesto' => $item->valor_impuesto,
                'tipo_impuesto' => $item->tipo_impuesto,
                'impuesto_prc' => $item->impuesto_prc,
                'descuento_prc' => $item->descuento_prc,
                'existe_exoneracion' => '00'
            ]);
        }
        $seguridad = $this->armarSeguridad($pedido->idconfigfact);
        $xml =  $this->armarXml($sales->idsale);
        include_once(public_path(). '/funcionFacturacion506.php');
        $facturar = Timbrar_documentos($xml, $seguridad);
        $new = $numero_factura + 1;
        $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$tipo_documento.' and idcaja = '.$pedido->idcaja);
        //Actualizo el numero del documento al orden del pedido
        $orden_update = DB::update('update sales set num_documento_convertido = '.$numero_factura.' where idsale = '.$pedido->idsale);
        $orden_update2 = DB::update('update sales set estatus_op =1 where idsale = '.$pedido->idsale);
        //Actualizo el nuevo documento factura con el id de la OP
        $update_factura_op = DB::update('update sales set viene_de_op ='.$pedido->idsale.' where idsale = '.$sales->idsale);

        // $actualizar = Pedidos::where('idpedido', $pedido->idpedido)->update(['estatus_doc' => 3]);
        $NC = $this->armarNcOrdenPedido($id);
        if ($NC > 0) {

            return redirect()->action('DonwloadController@correoXml', ['id' => $sales->idsale])->withStatus(__('Factura y Nota de Credito Agregada Correctamente.'));
        } else {

            return redirect()->action('DonwloadController@correoXml', ['id' => $sales->idsale])->withStatus(__('Factura Agregada Correctamente.'));
        }
    }
    public function armarNcOrdenPedido($id)
    {
        try {

            $sales = Sales::find($id);
            $sales_item = Sales_item::where('idsales', $id)->get();
            $configuracion = Configuracion::find($sales->idconfigfact);
            $cliente = Cliente::find($sales->idcliente);
            $consecutivo = DB::table('consecutivos')->where([
                ['idcaja', '=', $sales->idcaja],
                ['tipo_documento', '=', '95'],
            ])->get();
            $observaciones = 'Documento de Referencia Orden De Pedido # '.$sales->numero_documento.' ';

            $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
            $sales = Sales::create([
                'numero_documento' => $numero_factura,
                'tipo_documento' => '95',
                'punto_venta' => $sales->punto_venta,
                'idcaja' => $sales->idcaja,
                'idconfigfact' => $sales->idconfigfact,
                'idcodigoactv' => $sales->idcodigoactv,
                'idcliente' => $sales->idcliente,
                'tipo_moneda' => $sales->tipo_moneda,
                'tipo_cambio' => $sales->tipo_cambio,
                'condicion_venta' => $sales->condicion_venta,
                'p_credito' => $sales->p_credito,
                'medio_pago' => $sales->medio_pago,
                'referencia_pago' => $sales->referencia_pago,
                'total_serv_grab' => $sales->total_serv_grab,
                'total_serv_exento' => $sales->total_serv_exento,
                'total_serv_exonerado' => $sales->total_serv_exonerado,
                'total_mercancia_grav' => $sales->total_mercancia_grav,
                'total_mercancia_exenta' => $sales->total_mercancia_exenta,
                'total_mercancia_exonerada' => $sales->total_mercancia_exonerada,
                'total_exento' => $sales->total_exento,
                'total_exonerado' => $sales->total_exonerado,
                'total_neto' => $sales->total_neto,
                'total_descuento' => $sales->total_descuento,
                'total_impuesto' => $sales->total_impuesto,
                'total_otros_cargos' => $sales->total_otros_cargos,
                'total_iva_devuelto' => $sales->total_iva_devuelto,
                'total_comprobante' => $sales->total_comprobante,
                'tiene_exoneracion' => $sales->tiene_exoneracion,
                'referencia_sale' => $id,
                'fecha_creada' => date('Y-m-d'),
                'tipo_devolucion' => 2,
                'observaciones' => $observaciones,
                'estatus_sale' => 2
            ]);

            foreach ($sales_item as $item ) {

                $sal_item = Sales_item::create([
                    'idsales' => $sales->idsale,
                    'idproducto' =>  $item->idproducto,
                    'codigo_producto' =>  $item->codigo_producto,
                    'codigo_cabys' =>  $item->codigo_cabys,
                    'nombre_producto' =>  $item->nombre_producto,
                    'cantidad' => $item->cantidad,
                    'valor_neto' =>  $item->valor_neto,
                    'valor_descuento' =>  $item->valor_descuento,
                    'valor_impuesto' =>  $item->valor_impuesto,
                    'tipo_impuesto' => $item->tipo_impuesto,
                    'impuesto_prc' => $item->impuesto_prc,
                    'descuento_prc' =>  $item->descuento_prc,
                    'existe_exoneracion' =>  $item->existe_exoneracion,
                    'costo_utilidad' =>  $item->costo_utilidad
                ]);

            }
            $nota_cre_cons = Consecutivos::where([
                ['idcaja', '=', $sales->idcaja],
                ['tipo_documento', '=', '95']
            ])->get();
            $new = $nota_cre_cons[0]->numero_documento + 1;
            $new2 = str_pad($new, 10, "0", STR_PAD_LEFT);
            $consecutivo = DB::update('update consecutivos set numero_documento = '.$new2.' where tipo_documento = "95"  and idcaja = '.$sales->idcaja);

            if ($sales->condicion_venta === '02') {
                $cli_cxcobrar = Cxcobrar::where('idcliente', $sales->idcliente)->get();
                if (count($cli_cxcobrar) > 0) {
                    $restando = $cli_cxcobrar[0]->saldo_cuenta - $sales->total_comprobante;
                    $mcxcobrar = Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $restando]);
                    DB::table('mov_cxcobrar')->select('mov_cxcobrar.*')
                    ->where('mov_cxcobrar.idmovcxcobrar', '=', $sales->idmovcxcobrar)->delete();
                }
            }
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }
        public function actualiarActividad(Request $request)
    {
        $input = $request->all();
        $actualizar = Sales::where('idsale', $input['idsale'])->update(['idcodigoactv' => $input['actividad']]);
        return response()->json(['success'=> $input]);
    }
    
public function actualiarActividadclienteold(Request $request)
{
    $input = $request->only(['cliente', 'codigo_actividad']);

    if (empty($input['cliente']) || empty($input['codigo_actividad'])) {
        return response()->json(['error' => 'datos incompletos'], 422);
    }

    $actualizar = Cliente::where('idcliente', $input['cliente'])
                       ->update(['codigo_actividad' => $input['codigo_actividad']]);

    return response()->json(['success' => true, 'actualizado' => $actualizar]);
}

   public function actualiarActividadcliente(Request $request)
    {
        $input = $request->all();
        $actualizar = Cliente::where('idcliente', $input['cliente'])->update(['codigo_actividad' => $input['codigo_actividad']]);
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

            $Costo_ivai=  ($input['costo_utilidad'] );
            $Costo_sivai= round(($Costo_ivai/(1+($porcentaje_imp/100))),5);
        if ($input['descuento'] > 0) {

            $valor_descuento = (($input['costo_utilidad'] * $input['cantidad']) * $input['descuento'])/100;

        }

        $valor_neto = ($Costo_sivai * $input['cantidad']) - $valor_descuento;

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
            'costo_utilidad' => $Costo_sivai
        ]);
        return response()->json(['success'=> $input]);
    }


        public function reenvioXml($idsale)
    {
        $cabecera = Sales::find($idsale);
        $detalle = Sales_item::where('idsales', $idsale)->get();
        $emisor = Configuracion::find($cabecera->idconfigfact);
        $codigo_actividad = Actividad::find($cabecera->idcodigoactv);
        $cliente = Cliente::find($cabecera->idcliente);
        $arreglo = [];
        foreach ($detalle as $cuerpo) {
            $producto = Productos::find($cuerpo->idproducto);
            $unidad_medida = Unidades_medidas::find($producto->idunidadmedida);
            $monto_total = round(($cuerpo->costo_utilidad * $cuerpo->cantidad),5);
                switch ($cuerpo->tipo_impuesto) {
                case '01':
                case '11':
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
                case '10':
                    $porcentaje_imp = 0;
                break;
                case '99':
                    $porcentaje_imp = 0;
                break;
            }
            if ($cuerpo->existe_exoneracion === '00') {
                $estructura =
                    array(
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
                    );
                    if ($cuerpo->tipo_impuesto != '99') {

                        $estructura['Impuesto'] = [
                                array(
                                    'Codigo' => '01', //Es un campo fijo de dos posiciones. Ver notas 7 y 8  en Anexos y Estructuras
                                    'CodigoTarifa' => ''.$cuerpo->tipo_impuesto, // Debe de expresarse en porcentaje.  decimal 4,2
                                    'Tarifa' => ''.$porcentaje_imp,
                                        //'FactorIVA'=>'0.00000',
                                    'Monto' => ''.$cuerpo->valor_impuesto // se obtiene “subtotal” * tarifa del impuesto
                                )
                            ];
                        $estructura['MontoTotalLinea'] = $cuerpo->valor_neto+$cuerpo->valor_impuesto;
                    } else {

                        $estructura['MontoTotalLinea'] = $cuerpo->valor_neto+$cuerpo->valor_impuesto;
                    }
                    array_push($arreglo, $estructura);
            }else{
                $items_exonerados = Items_exonerados::where('idsalesitem', $cuerpo->idsalesitem)->get();
                $impuesto_nto = $cuerpo->valor_impuesto - $items_exonerados[0]->monto_exoneracion;
                $estructura =
                    array(
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
                                    'Monto' => ''.$cuerpo->valor_impuesto // se obtiene “subtotal” * tarifa del impuesto
                                )
                            ],
                        'Exoneracion' =>
                            array(
                                'TipoDocumento' => ''.$items_exonerados[0]->tipo_exoneracion, //Es un campo fijo de dos posiciones. Ver notas 10.1 y 7  en Anexos y Estructuras
                                'NumeroDocumento' => ''.$items_exonerados[0]->numero_exoneracion, // campo obligado cuando exista exoeracion cantidad maxima 17 digitos string
                                'NombreInstitucion' => ''.$items_exonerados[0]->institucion, // Obligado cuando es exoneracion string 100 , nombre de la institucion a exonerar
                                'FechaEmision' => $items_exonerados[0]->fecha_exoneracion.'T00:00:00', // Formato: YYYY-MM-DDThh:mi:ss[Z|(+|-)hh:mm] Ejemplo: 2016-09-26T13:00:00+06:00
                                'MontoExoneracion' => ''.$items_exonerados[0]->monto_exoneracion, //Es un número decimal compuesto por 13 enteros y 5 decimales
                                'PorcentajeExoneracion' => ''.$items_exonerados[0]->porcentaje_exoneracion //Es un número entero 3 caracteres obligado porcentaje de exoneracion
                            ),
                        'ImpuestoNeto' => ''.$impuesto_nto, // se obtiene sumando subtotal + monto impuesto
                        'MontoTotalLinea' => ($monto_total-$cuerpo->valor_descuento)+$impuesto_nto // se obtiene sumando subtotal + monto impuesto
                    );
                    array_push($arreglo, $estructura);
            }
            //EL ARRAY PUSH DEBE IR DENTRO TAMBIEN DEL WHILE QUE VIENE DE BASE DE DATOS ES MAS PRACTICO
        }
        $xml = [
                'ProveedorSistemas'        => $emisor->proveedor_sistema, //Se debe indicar el numero de cedula de identificaciÛn del proveedor de sistemas
                'tipoDocumento' => ''.$cabecera->tipo_documento, // 2 digitos corresponde al tipo de documento generado visualizar anexos y estructuras
                'CodigoActividadEmisor' => ''.$codigo_actividad->codigo_actividad, // 6 string nuevo campo para la version 4.3
                'sucursal' => ''.$emisor->sucursal, // 3 digitos sucursal de donde proviene el documento para el armado de clave
                'puntoVenta' => ''.$cabecera->punto_venta, // 5 digitos punto de venta del cual se armo el documento
                'situacionComprobante' => '1', // 1 digito corresponde a la situacion del documento 1 Normal 2 Contingencia y 3 Sin internet
                'sales_id' => ''.$idsale,
                'idconfigfact' => ''.$cabecera->idconfigfact,
                'numeroFactura' => ''.$cabecera->numero_documento, //correspondiente al numero del documento en el sistema
                'fechaEmision' => ''.$cabecera->fecha_reenvio, // fecha de emision del documento no mayor a 72 horas, date_default_timezone_set debe ser costa rica
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
                            'CodigoTipoMoneda' =>
                                array(
                                    'CodigoMoneda' => $cabecera->tipo_moneda, //validar el documento de Monedas para saber los codigos
                                    'TipoCambio' => ''.$cabecera->tipo_cambio //Valor de cambio en caso de que sea una moneda diferente al colon
                                ),
                            'TotalServGravados' => ''.$cabecera->total_serv_grab, //obligado cuando el servicio tenga IV (IMPUESTO)
                            'TotalServExentos' => ''.$cabecera->total_serv_exento, // OBLIGADO cuando el servicio este exento de IV
                            'TotalServExonerado' => ''.$cabecera->total_serv_exonerado, // Este campo será de condición obligatoria, cuando el servicio esté gravado y se preste a un cliente que goce de exoneración se debe de indicar el monto equivalente al porcentaje exonerado.
                            'TotalMercanciasGravadas' => ''.$cabecera->total_mercancia_grav, //obligado cuando la mercancia tenga IV (IMPUESTO)
                            'TotalMercanciasExentas' => ''.$cabecera->total_mercancia_exenta, // OBLIGADO cuando la mercancia este exento de IV
                            'TotalMercExonerada' => ''.$cabecera->total_mercancia_exonerada, //Este campo será de condición obligatoria, cuando la mercancía o producto se venda a un cliente que goce de exoneración para la compra de la misma.
                            'TotalGravado' => $cabecera->total_mercancia_grav + $cabecera->total_serv_grab,  // se obtiene sumando TotalServGravados + TotalMercanciasGravadas
                            'TotalExento' => $cabecera->total_mercancia_exenta + $cabecera->total_serv_exento, // se obtiene sumando TotalServExentos + TotalMercanciasExentas
                            'TotalExonerado' => ''.$cabecera->total_mercancia_exonerada, //Se obtiene de la suma de los campos “total servicios exonerados de IVA” mas “total de mercancías exoneradas del IVA”.
                            'TotalVenta' => (($cabecera->total_mercancia_grav + $cabecera->total_serv_grab) + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento), // se obtiene sumando TotalGravado + TotalExento
                            'TotalDescuentos' => ''.$cabecera->total_descuento, // se obtiene sumando todos los valores de MontoDescuento que tenga la factura
                            'TotalVentaNeta' => (($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta +  $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento), // se obtiene restando “total venta” “total descuento”.
                            'TotalImpuesto' => ''.$cabecera->total_impuesto, // se obtiene sumando todos los montos impuestos del detalle de factura
                            'TotalIVADevuelto' => ''.$cabecera->total_iva_devuelto,
                            'TotalOtrosCargos'  => ''.$cabecera->total_otros_cargos,
                            'TotalComprobante' => ((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto // se obtiene sumando TotalVentaNeta + TotalImpuesto
                        ]
                    ];
        return $xml;
    }

        public function reenviarDoc($id)
    {
        $sales = Sales::find($id);
        $seguridad = $this->armarSeguridad($sales->idconfigfact);
        //$xml =  $this->reenvioXml($id);
        $pos=new PosController();
        $xml=$pos->armarXml($id);
        include_once(public_path(). '/funcionFacturacion506.php');
        $facturar = Timbrar_documentos($xml, $seguridad);
        return redirect()->action('DonwloadController@correoXml', ['id' => $id])->withStatus(__('Factura Reenviada Correctamente.'));
    }


public function reenviarDocrecp($id)
    {
   // Calcula las fechas
$fechaActual = Carbon::now()->toDateString();
$fechaDosMesesAtras = Carbon::now()->subMonths(2)->toDateString();

// Obtén los registros que deseas copiar
$registros = Receptor::where('idreceptor','=', $id)
    ->where('estatus_hacienda', 'rechazado')
    ->whereBetween('fecha', [$fechaDosMesesAtras, $fechaActual])
    ->get();

// Crea un nuevo array para almacenar los registros a insertar
$registrosACopiar = [];

foreach ($registros as $registro) {
    // Crea un nuevo registro exceptuando xml_envio y xml_respuesta
    $nuevoRegistro = $registro->replicate(); // Clona el modelo original

    // Establece los campos que se deben modificar
    $nuevoRegistro->pendiente = 0;
    $nuevoRegistro->estatus_hacienda = '';
    $nuevoRegistro->numero_documento_receptor = 9999999999;
    $nuevoRegistro->consecutivo = '';
    
    // Elimina los campos que no deseas copiar
    unset($nuevoRegistro->xml_envio, $nuevoRegistro->xml_respuesta); 

    // Agrega el nuevo registro al array
    $registrosACopiar[] = $nuevoRegistro->toArray(); // Convierte a array
}

// Inserta los nuevos registros en la base de datos, si hay registros para copiar
if (count($registrosACopiar) > 0) {
    Receptor::insert($registrosACopiar);
}
           //DD($updat);
return redirect()->route('receptor.automatica');

    }

        public function ImprimirTicket($id)
    {
        $sales = Sales::find($id);
       // dd($sales);
        $sales_item = Sales_item::where('idsales',$id)->get();
        if ($sales->tipo_documento != '96') {
            $facelectron = Facelectron::where('idsales', $id)->get();
        } else {
            $facelectron = [];
        }
        $configuracion = Configuracion::find($sales->idconfigfact);
        $consulta_fac = Facelectron::where([ ['idsales', '=', $sales->referencia_sale] ])->get(); ///para obtener refencia de la NC de la que viene
        //DD($consulta_fac);
        $cliente = Cliente::find($sales->idcliente);
        $caja = Cajas::find($sales->idcaja);
        return view('facturacion.imprimir', ['sales' => $sales, 'sales_item' => $sales_item, 'facelectron' => $facelectron, 'consulta_fac' => $consulta_fac, 'configuracion' => $configuracion, 'cliente' => $cliente, 'caja' => $caja]);
    }

        public function limpiarFacturas(Request $request)
    {
        $borrado2 = DB::table('sales')->select('sales.*', 'sales_item.*')
        ->join('sales_item','sales.idsale','=','sales_item.idsales')
        ->where([
            ['sales.estatus_sale','=', '1'],
            ['sales.idconfigfact','=', Auth::user()->idconfigfact],
        ])->delete();
        $borrado = DB::select('DELETE sales.*, sales_item.* FROM sales JOIN sales_item ON sales.idsale = sales_item.idsales WHERE sales.estatus_sale = 1 and sales.idconfigfact = '.Auth::user()->idconfigfact);
        $borrado_sales = DB::select('DELETE sales.* FROM sales WHERE sales.estatus_sale = 1 and sales.idconfigfact = '.Auth::user()->idconfigfact);

        return redirect()->route('facturar.index')->withStatus(__('Base de Datos Limpiada correctamente.'));
    }
    public function deletefac($id)
{
    // Verificar si el id existe en la tabla Facelectron
    $existsInFacelectron = DB::table('facelectron')->where('idsales', $id)->exists();

    if ($existsInFacelectron) {
        return redirect()->route('facturar.index')->withErrors(__('No se puede eliminar la venta porque esta emitida ante el MH.'));
    }

    // Verificar si existen ítems de la venta
    $itemsExistentes = DB::table('sales_item')->where('idsales', $id)->exists();

    // Primero, eliminamos los ítems de la venta si existen
    if ($itemsExistentes) {
        $borradoItems = DB::table('sales_item')->where('idsales', $id)->delete();
    } else {
        $borradoItems = 0; // Definimos cero si no se encontraron ítems
    }

    // Luego, eliminamos la venta solo si el estatus es '1' y pertenece al usuario
    $borradoSale = DB::table('sales')
        ->where([
           // ['estatus_sale', '=', '1'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['idsale', '=', $id]
        ])
        ->delete();

    // Verificar si se eliminaron registros
    if ($borradoItems > 0 || $borradoSale > 0) {
        return redirect()->route('facturar.index')->withStatus(__('Base de Datos limpiada correctamente.'));
    } else {
        return redirect()->route('facturar.index')->withErrors(__('No se encontraron registros para eliminar.'));
    }
}

//omairena 9 enero 22
        public function jsoncliente(Request $request)
    {
        $datos = $request->all();
        $var_cliente = json_decode($datos['cliente_hacienda']);
        if (count($var_cliente->{'actividades'}) > 0) {

            $razon_social = $var_cliente->{'actividades'}[0]->{'descripcion'};
            $codigo_actividad = $var_cliente->{'actividades'}[0]->{'codigo'};

        } else {
            $razon_social = $var_cliente->{'nombre'};
            $codigo_actividad = '112233';
        }
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
            'razon_social' => $razon_social,
            'nombre_contribuyente' => $var_cliente->{'nombre'},
            'codigo_actividad' => $codigo_actividad,
        ]);
        return redirect()->route('facturar.create');
    }

}
