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
use App\Items_exonerados;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Container\Container;
use App\Productos;
use App\Unidades_medidas;
use App\Mov_cxcobrar;
use App\Cxcobrar;
use App\Log_cxcobrar;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Cajas;
use App\Otrocargo;
use Carbon\Carbon;

class NotacController extends Controller
{
	    public function index(Sales $model)
    {
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-t');
        $sales = Sales::where([
            ['tipo_documento', '03'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $fecha_desde],
            ['fecha_creada', '<=', $fecha_hasta]
        ])->orderBy('numero_documento', 'desc')->get();
    	return view('notacredito.index', ['sales' => $sales]);
    }

        public function create($id)
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

        // Validacion de nota de credito y back a la ruta antes de refacturar
        //$ncpedido = Sales::where([
         //   ['referencia_sale', $id],
          //  ['estatus_sale','2'],
          //  ['tipo_devolucion',2]
          //  ])->get();
        $ncpedido = DB::table('sales')->select('sales.*')
        ->join('facelectron','sales.idsale', '=', 'facelectron.idsales')
        ->where([
            ['sales.referencia_sale', $id],
            ['sales.tipo_devolucion', '2'],
            ['sales.estatus_sale', '2'],
            ['facelectron.estatushacienda', 'aceptado']
        ])->get();
           // dd($ncpedido);
        if (count($ncpedido) > 0) {
            return redirect()->route('facturar.index')->withStatus(__('Nota de Credito ya creada para este documento.'));
        }
        $ncpedido_verifica = Sales::where([
            ['referencia_sale', $id],

            ])->get();
        $ncpedido_verifica = DB::table('sales')->select('sales.*')
        ->join('facelectron','sales.idsale', '=', 'facelectron.idsales')
        ->where([
            ['sales.referencia_sale', $id],
            ['facelectron.estatushacienda', 'aceptado']
        ])->get();

          //  dd($ncpedido_verifica);
        $sales = Sales::find($id);
        $sales_item = Sales_item::where('idsales', $id)->get();
        // nueva parte de otro cargos
        $sales_item_otrocargo = Otrocargo::where('idsales', $id)->get();
        $configuracion = Configuracion::find($sales->idconfigfact);
        $cliente = Cliente::find($sales->idcliente);
        $consecutivo = DB::table('consecutivos')->where([
            ['idcaja', '=', $sales->idcaja],
            ['tipo_documento', '=', '03'],
        ])->get();
        if(empty($sales->medio_pago)){
            $medio_pago_nc='01';
        }else{
            $medio_pago_nc =$sales->medio_pago;
        }
        
        $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
           if(count($ncpedido_verifica) > 0){
        $sales = Sales::create(
                [
                    'numero_documento' => $numero_factura,
                    'tipo_documento' => '03',
                    'punto_venta' => $sales->punto_venta,
                    'idcaja' => $sales->idcaja,
                    'idconfigfact' => $sales->idconfigfact,
                    'idcodigoactv' => $sales->idcodigoactv,
                    'idcliente' => $sales->idcliente,
                    'tipo_moneda' => $sales->tipo_moneda,
                    'tipo_cambio' => $sales->tipo_cambio,
                    'condicion_venta' => $sales->condicion_venta,
                    'p_credito' => $sales->p_credito,
                    'medio_pago' => $medio_pago_nc,
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
                    'estatus_sale' => 1,
                    'tipo_devolucion'=> 1,
                    'creado_por' => Auth::user()->email,
                    
                    
                ]);
           }else{
                $sales = Sales::create(
                [
                    'numero_documento' => $numero_factura,
                    'tipo_documento' => '03',
                    'punto_venta' => $sales->punto_venta,
                    'idcaja' => $sales->idcaja,
                    'idconfigfact' => $sales->idconfigfact,
                    'idcodigoactv' => $sales->idcodigoactv,
                    'idcliente' => $sales->idcliente,
                    'tipo_moneda' => $sales->tipo_moneda,
                    'tipo_cambio' => $sales->tipo_cambio,
                    'condicion_venta' => $sales->condicion_venta,
                    'p_credito' => $sales->p_credito,
                    'medio_pago' => $medio_pago_nc,
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
                    'estatus_sale' => 1,
                     'creado_por' => Auth::user()->email,
                    

                ]);
           }


                foreach ($sales_item as $item ) {
                    $sal_item = Sales_item::create(
                        [
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
                        ]
                    );

                    if ($item->existe_exoneracion == '01') {
                        $consulta_exo = Items_exonerados::where('idsalesitem', $item->idsalesitem)->get();
                        $exoneracion = Items_exonerados::create([
                            'idsalesitem' => $sal_item->idsalesitem,
                            'tipo_exoneracion' =>  $consulta_exo[0]->tipo_exoneracion,
                            'numero_exoneracion' => $consulta_exo[0]->numero_exoneracion,
                            'institucion' => $consulta_exo[0]->institucion,
                            'fecha_exoneracion' => $consulta_exo[0]->fecha_exoneracion,
                            'porcentaje_exoneracion' => $consulta_exo[0]->porcentaje_exoneracion,
                            'monto_exoneracion' => $consulta_exo[0]->monto_exoneracion
                        ]);
                    }
                }
        foreach ($sales_item_otrocargo as $cargo) {

            Otrocargo::create([
                'idsales' => $sales->idsale,
                'tipo_otrocargo' => $cargo->tipo_otrocargo,
                'numero_identificacion' =>  $cargo->numero_identificacion,
                'nombre' => $cargo->nombre,
                'detalle' => $cargo->detalle,
                'porcentaje_cargo' => $cargo->porcentaje_cargo,
                'monto_cargo' => $cargo->monto_cargo,
                'fecha_creado_cargo' => Carbon::now()->toDateTimeString(),
            ]);

        }
       // dd($ncpedido_verifica);
          if(count($ncpedido_verifica) > 0){
        if($ncpedido_verifica[0]->tipo_devolucion == '1'){
         Session::flash('message', "Para el documento seleccionado, ya existe una NC Parcial generada previamente." );
         return redirect()->route('notacredito.edit', $sales->idsale);

        }else{
        return redirect()->route('notacredito.edit', $sales->idsale);
        }
          }else{
             return redirect()->route('notacredito.edit', $sales->idsale);
          }
    }

        public function edit($id)
    {
        $sales = Sales::find($id);
        $sales_item = Sales_item::where('idsales', $id)->get();
        $sales_item_otrocargo = Otrocargo::where('idsales', $id)->get();
        $info_ref = Facelectron::where('idsales', $sales->referencia_sale)->first();
        $configuracion = Configuracion::find($sales->idconfigfact);
        $cliente = Cliente::find($sales->idcliente);
         if (count($sales_item) > 0) {
            $productos = DB::table('productos')->select('productos.*')
            ->whereNotExists( function ($query) use ($sales_item) {
            $query->select(DB::raw(1))
            ->from('sales_item')
            ->whereRaw('sales_item.idproducto = productos.idproducto')
            ->where('sales_item.idsales', '=', $sales_item[0]->idsales);
            })->where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
        }else{
            $productos = Productos::where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
        }
        return view('notacredito.edit', ['configuracion'  => $configuracion, 'cliente'  => $cliente, 'productos' => $productos, 'sales' => $sales, 'sales_item' => $sales_item, 'sales_item_otrocargo' => $sales_item_otrocargo, 'info_ref' =>$info_ref ]);
    }

        public function actualizarCant(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = $producto->precio_sin_imp * $input['cantidad'];
        if ($sales_item->descuento_prc > 0) {
            $total_descuento = ($total_neto * $sales_item->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
        $total_neto = $total_neto - $total_descuento;
        $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;
        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['cantidad' => $input['cantidad'], 'valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento]);

        $actualizar2 = Sales::where('idsale', $sales_item->idsales)->update(['tipo_devolucion' => $input['tipo_devolucion']]);

        return response()->json(['success'=> $input]);
    }

 public function actualizarcosto(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $producto = Productos::find($input['idproducto']);
        $total_neto = $input['costo_dev'] * $sales_item->cantidad;
        if ($sales_item->descuento_prc > 0) {
            $total_descuento = ($total_neto * $sales_item->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
        $total_neto = $total_neto - $total_descuento;
        $total_impuesto = ($total_neto * $producto->porcentaje_imp)/100;
        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['costo_utilidad' => $input['costo_dev'], 'valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento]);

        $actualizar2 = Sales::where('idsale', $sales_item->idsales)->update(['tipo_devolucion' => $input['tipo_devolucion']]);

        return response()->json(['success'=> $input]);
    }


    public function update(Request $request, Sales $model)
    {
        $datos = $request->all();
        $sales  = Sales::where('idsale', $datos['idsale'])->update([
                    'tipo_devolucion' => $datos['tipo_devolucion'],
                    'estatus_sale' => 2
                ]);
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
        $total_serv_no_sujeto =0;
        $total_mercancia_exenta = 0;
        $total_mercancia_no_sujeto = 0;
        $total_IVA_ex=0;
        $v_ref = Sales::find($datos['idsale']);


        if (!is_null($datos['observaciones'])) {
            $observaciones = $datos['observaciones'];
        }else{
            $observaciones = 'Nota de Credito generada por defecto';
        }
        foreach ($sales_item as $s_i) {
            $producto = Productos::find($s_i->idproducto);
            if ($s_i->cantidad != 0) {
                $cantidad_stock = $producto->cantidad_stock + $s_i->cantidad;
                $actualizar = Productos::where('idproducto', $s_i->idproducto)->update(['cantidad_stock' => $cantidad_stock]);
            }else{
                $borrado = Sales_item::where('idsalesitem', $s_i->idsalesitem)->delete();
            }

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

                        
                }

            }else{
                if ($producto->tipo_producto === 2) {
                    $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                    $imps_nto = $s_i->valor_impuesto - $items_exonerados[0]->monto_exoneracion;
                    if ($imps_nto < 0) {
                        $total_impuesto = $total_impuesto + (-1 * $imps_nto);
                    }else{
                        $total_impuesto = $total_impuesto + $imps_nto;
                    }
                    $total_exonerado = $total_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                    $total_serv_exonerado =  $total_serv_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                    $total_serv_grab = $total_serv_grab + ((1-($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc)) * $s_i->valor_neto);
                    $total_neto = $total_neto + $s_i->valor_neto;
                    $total_descuento = $total_descuento + $s_i->valor_descuento;
                    $total_IVA_ex = $total_IVA_ex  + $s_i->exo_monto;
                     if ($v_ref->medio_pago === '02' and $producto->porcentaje_imp == '4.00'){

                            $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                        }

                }else{
                    $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                    $imps_nto = $s_i->valor_impuesto - $items_exonerados[0]->monto_exoneracion;
                    if ($imps_nto < 0) {
                        $total_impuesto = $total_impuesto + (-1 * $imps_nto);
                    }else{
                        $total_impuesto = $total_impuesto + $imps_nto;
                    }

                    $total_exonerado = $total_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                    $total_mercancia_exonerada =  $total_mercancia_exonerada + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                    $total_mercancia_grav = $total_mercancia_grav + ((1-($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc)) * $s_i->valor_neto);
                    $total_neto = $total_neto + $s_i->valor_neto;
                    $total_descuento = $total_descuento + $s_i->valor_descuento;
                     $total_IVA_ex = $total_IVA_ex  + $s_i->exo_monto;
                         if ($v_ref->medio_pago === '02' and $producto->porcentaje_imp == '4.00'){

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
        // $total_comprobante = (($total_mercancia_grav + $total_mercancia_exonerada + $total_serv_grab+ $total_serv_exonerado)-$total_descuento) + $total_impuesto;
        $total_exento = $total_serv_exento + $total_mercancia_exenta;
        $total_comprobante = (((($total_mercancia_grav + $total_mercancia_exonerada + $total_mercancia_exenta + $total_serv_grab+ $total_serv_exonerado + $total_serv_exento)-$total_descuento) + $total_impuesto + $total_serv_no_sujeto + $total_mercancia_no_sujeto ) - $total_iva_devuelto) + $total_otros_cargos;
        $nota_cre_cons = Consecutivos::where([
            ['idcaja', '=', $v_ref->idcaja],
            ['tipo_documento', '=', '03']
        ])->get();
        
        
       // $new = $nota_cre_cons[0]->numero_documento + 1;
       // $new2 = str_pad($new, 10, "0", STR_PAD_LEFT);
        //$consecutivo = DB::update('update consecutivos set numero_documento = '.$new2.' where tipo_documento = "03"  and idcaja = '.$v_ref->idcaja);
                     // Paso 1: Obtener el rango de números de documento
$consecutivo = DB::table('consecutivos')->where('idcaja', $v_ref->idcaja)
    ->where('tipo_documento', '03')
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
    ->where('idcaja', $v_ref->idcaja)
    ->where('tipo_documento', '03')
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
                ['idcaja', '=', $v_ref->idcaja],
                ['tipo_documento', '=', '03'],
            ])->get();
            $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);

          $consecutivo_fac = DB::table('sales')->where([
    ['idcaja', '=', $v_ref->idcaja],
    ['tipo_documento', '=', '03'],
    ['numero_documento', '=', $numero_factura],
    ['idsale', '!=', $datos['idsale']],
    ['estatus_sale', '=' ,2]
])->get();


if ($consecutivo_fac->isEmpty()) {
                $new = $numero_factura + 1;

                $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = "03" and idcaja = '.$v_ref->idcaja);


}else{

    $new = $numero_factura +1;
    $numero_factura=str_pad($new, 10, "0", STR_PAD_LEFT);;
    $new=$numero_factura+1;

    $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = "03" and idcaja = '.$v_ref->idcaja);

    //Session::flash('message', "Hay un problema con el consecutivo de la factura, SOLO SE PUEDE FACTURAR CON UNA PANTALLA ACTIVA, ve a VER DOC ELEC y LIMPA FACTURA EN PROCESO o Elimina alguna de los Documentos duplicados para continuar");
    //return redirect()->back();
    // }
}
} else {


    $numero_factura=$huecos[0];

    }
        
        
        
        
        $nota_cre = Sales::find($v_ref->referencia_sale);
        $sales  = Sales::where('idsale', $datos['idsale'])->update([
                    'numero_documento' => $numero_factura,//'numero_documento' => str_pad($nota_cre_cons[0]->numero_documento, 10, "0", STR_PAD_LEFT),
                    'fecha_creada' => date('Y-m-d'),
                    'fecha_reenvio' => date('c'),
                    'observaciones' => $observaciones,
                    'total_serv_grab' => $total_serv_grab,
                    'total_serv_exonerado' => $total_serv_exonerado,
                    'total_serv_exento' => $total_serv_exento,
                    'TotalServNoSujeto' => $total_serv_no_sujeto,
                    'total_mercancia_grav' => $total_mercancia_grav,
                    'total_mercancia_exonerada' => $total_mercancia_exonerada,
                    'total_mercancia_exenta' => $total_mercancia_exenta,
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
                    'clave_sale'=> $datos['clave_sale'],
                    'fecha_emision'=>$datos['fecha_emision'],
                    'tipo_doc_ref'=>$datos['tipo_doc_ref'],
                    'razon'=> $observaciones,
        ]);
        $update_referencia  = Sales::where('idsale', $nota_cre->idsale)->update([
            'estatus_op' => 1
        ]);
       // dd($v_ref->idsale );
        $update_referencia_sales  = Sales::where('idsale', $v_ref->referencia_sale)->update([
            'referencia_sale' => $v_ref->idsale]);

        if ($nota_cre->condicion_venta === '02') {
            $cli_cxcobrar = Cxcobrar::where('idcliente', $nota_cre->idcliente)->get();
            if (count($cli_cxcobrar) > 0) {
                $restando = $cli_cxcobrar[0]->saldo_cuenta - $total_comprobante;
                $mcxcobrar = Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $restando]);
                DB::table('mov_cxcobrar')->select('mov_cxcobrar.*')
                ->where('mov_cxcobrar.idmovcxcobrar', '=', $nota_cre->idmovcxcobrar)->delete();
            }
        }
        $venta = Sales::find($datos['idsale']);
        $seguridad = $this->armarSeguridad($venta->idconfigfact);
        $xml =  $this->armarXmlCredito($datos['idsale'], $datos['referencia_sale']);
        //$pos=new PosController();
           //$xml=$pos->armarXml($datos['idsale']);
        include_once(public_path(). '/funcionFacturacion506.php');
        $facturar = Timbrar_documentos($xml, $seguridad);
        $caja = Cajas::find($v_ref->idcaja);
        //if ($caja->usa_impresion === 1) {
            //app('App\Http\Controllers\ReportesController')->imprimirFactura($datos['idsale'], $caja->idcaja);
        //}

        return redirect()->action('DonwloadController@correoXml', $datos['idsale'])->withStatus(__('Nota de Crédito Creada Correctamente.'));
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

        public function armarXmlCredito($idsale, $referencia_sale)
    {
        $cabecera = Sales::find($idsale);
        $detalle = Sales_item::where('idsales', $idsale)->get();
        $emisor = Configuracion::find($cabecera->idconfigfact);
        $codigo_actividad = Actividad::find($cabecera->idcodigoactv);
        $cliente = Cliente::find($cabecera->idcliente);
        $info_ref = Facelectron::where('idsales', $referencia_sale)->get();
        if(empty($info_ref[0]->tipodoc)){
            $tipo_doc_ref='01';
        }else{
            $tipo_doc_ref=$info_ref[0]->tipodoc;
        }
        $desglose_impuesto= [];
        switch ($cabecera->tipo_devolucion) {
            case '1':
                 $codigo_devolucion = '02';
            break;
            case '2':
                 $codigo_devolucion = '01';
            break;
        }
        $arreglo = [];
        foreach ($detalle as $cuerpo) {
            $producto = Productos::find($cuerpo->idproducto);
            $unidad_medida = Unidades_medidas::find($producto->idunidadmedida);
            $monto_total = $cuerpo->costo_utilidad * $cuerpo->cantidad;
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
                array_push($desglose_impuesto, [  
                'Codigo'             => '01',  
                'CodigoTarifaIVA'    => (string)$cuerpo->tipo_impuesto,  
                'TotalMontoImpuesto' => (string)$cuerpo->valor_impuesto  
                ]);  
                
                $estructura =
                    array(
                        'CodigoCABYS' => ''.$producto->codigo_cabys,
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
                        'MontoTotal' => ''.$cuerpo->costo_utilidad * $cuerpo->cantidad, //Valor neto * cantidad
                        'Descuento'=>
                            [
                                array(
                                    'CodigoDescuento'     => '09',
                                    'MontoDescuento' => ''.$cuerpo->valor_descuento,// valor descuento concedido si existe descuento existe naturaleza del descuento
                                    'NaturalezaDescuento' => 'Descuento por parte del operador' //si existe descuento debe especificar porque 80 Strig
                                )
                            ],
                        'SubTotal' => ''.$cuerpo->valor_neto, // MontoTotal - MontoDescuento es la nomenclatura del nodo
                        'BaseImponible' => ''.$cuerpo->valor_neto, //Importante para saber si es exoneracion o no, en caso de ser exoneracion 01, de lo contrario 00
                        'EsExoneracion' => ''.$cuerpo->existe_exoneracion, //Importante para saber si es exoneracion o no, en caso de ser exoneracion 01, de lo contrario 00
                    );
                if ($cuerpo->tipo_impuesto != '99') {
                   
                    if ($tipo_doc_ref == '09') {

                        $estructura['Impuesto'] = [
                            array(
                                'Codigo' => '01', //Es un campo fijo de dos posiciones. Ver notas 7 y 8  en Anexos y Estructuras
                                'CodigoTarifaIVA' => ''.$cuerpo->tipo_impuesto, // Debe de expresarse en porcentaje.  decimal 4,2
                                'Tarifa' => ''.$porcentaje_imp,//'FactorIVA'=>'0.00000',
                                'Monto' => ''.$cuerpo->valor_impuesto, // se obtiene “subtotal” * tarifa del impuesto
                                'MontoExportacion' => ''.$cuerpo->valor_impuesto
                            )
                        ];
                    } else {
                        $estructura['Impuesto'] = [
                            array(
                                'Codigo' => '01', //Es un campo fijo de dos posiciones. Ver notas 7 y 8  en Anexos y Estructuras
                                'CodigoTarifaIVA' => ''.$cuerpo->tipo_impuesto, // Debe de expresarse en porcentaje.  decimal 4,2
                                'Tarifa' => ''.$porcentaje_imp,//'FactorIVA'=>'0.00000',
                                'Monto' => ''.$cuerpo->valor_impuesto // se obtiene “subtotal” * tarifa del impuesto
                            )
                        ];
                    }

                    $estructura['MontoTotalLinea'] = ''.$cuerpo->valor_neto+$cuerpo->valor_impuesto;
                    $estructura['ImpuestoNeto'] = ''.$cuerpo->valor_impuesto; 
                } else {

                    $estructura['MontoTotalLinea'] = ''.$cuerpo->valor_neto+$cuerpo->valor_impuesto;
                    $estructura['ImpuestoNeto'] = ''.$cuerpo->valor_impuesto; 
                }
                    array_push($arreglo, $estructura);
            }else {  
    $items_exonerados = Items_exonerados::where('idsalesitem', $cuerpo->idsalesitem)->get();  
    $impuesto_nto = $cuerpo->valor_impuesto - $items_exonerados[0]->monto_exoneracion;  

    // Inicializar la estructura  
    $estructura = array(  
        'CodigoCABYS' => (string)$producto->codigo_cabys,  
        'CodigoComercial' => array(  
            'Tipo' => '04', // valor 2 dígitos tipo de producto  
            'Codigo' => str_pad((string)$cuerpo->codigo_producto, 10, "0", STR_PAD_LEFT) // Código del producto en el sistema  
        ),  
        'Cantidad' => (string)$cuerpo->cantidad,  
        'UnidadMedida' => (string)$unidad_medida->simbolo, // Medida expresada en los códigos de Hacienda  
        'Detalle' => (string)$cuerpo->nombre_producto, // Detalle del producto  
        'reg_med' => (string)$producto->reg_med,  
    );  

    // Verificar si reg_med es mayor que 0  
    if ($producto->reg_med > 0) {  
        $estructura += array(  
            'RegistroMedicamento' => (string)$producto->forma,  
            'FormaFarmaceutica' => (string)$producto->cod_reg_med,  
        );  
    }  

    // Agregar información adicional a la estructura  
    $estructura += array(  
        'PrecioUnitario' => (string)$cuerpo->costo_utilidad, // Precio neto del producto por unidad  
        'MontoTotal' => (string)($cuerpo->costo_utilidad * $cuerpo->cantidad), // Valor neto * cantidad  
        'Descuento' => array(  
            array(  
                'CodigoDescuento' => '09',  
                'MontoDescuento' => (string)$cuerpo->valor_descuento, // Valor descuento concedido  
                'NaturalezaDescuento' => 'Descuento por parte del operador' // Naturaleza del descuento  
            )  
        ),  
        'SubTotal' => (string)$cuerpo->valor_neto, // MontoTotal - MontoDescuento  
        'BaseImponible' => (string)$cuerpo->valor_neto, // 01 si es exoneración, 00 de lo contrario  
        'EsExoneracion' => (string)$cuerpo->existe_exoneracion, // 01 si es exoneración, 00 de lo contrario  
        'Impuesto' => array(  
            array(  
                'Codigo' => '01', // Campo fijo de dos posiciones  
                'CodigoTarifaIVA' => (string)$cuerpo->tipo_impuesto, // Porcentaje en decimal  
                'Tarifa' => (string)$porcentaje_imp,  
                'Monto' => (string)$cuerpo->valor_impuesto // Subtotal * tarifa del impuesto  
            )  
        ),  
        'Exoneracion' => array(  
            'TipoDocumentoEX1' => (string)$items_exonerados[0]->tipo_exoneracion, // Campo fijo de dos posiciones  
            'NumeroDocumento' => (string)$items_exonerados[0]->numero_exoneracion, // Máximo 17 dígitos  
            'Inciso' => (string)$items_exonerados[0]->inciso,  
            'NombreInstitucion' => (string)$items_exonerados[0]->institucion, // Nombre de la institución a exonerar  
            'FechaEmisionEX' => $items_exonerados[0]->fecha_exoneracion . 'T00:00:00', // Formato: YYYY-MM-DDThh:mi:ss  
            'MontoExoneracion' => (string)$items_exonerados[0]->monto_exoneracion, // Número decimal 13 enteros y 5 decimales  
            'TarifaExonerada' => (string)$items_exonerados[0]->porcentaje_exoneracion // Número entero 3 caracteres  
        )  
    );  

    // Validaciones para el subarray Exoneracion  
    $codigos_exoneracion_permitidos = ['02', '03', '06', '07', '08'];  
    if (in_array($cuerpo->exoneracion->tipo_exoneracion, $codigos_exoneracion_permitidos)) {  
        $estructura['Exoneracion']['Articulo'] = (string)$items_exonerados[0]->articulo; // Número entero 3 caracteres  
    }  

    // Validación para tipo de exoneración 99  
    if ($cuerpo->exoneracion->tipo_exoneracion == '99') {  
        $estructura['Exoneracion']['TipoDocumentoOTRO'] = (string)$items_exonerados[0]->tipo_exoneracion_otro; // Número entero 3 caracteres  
    }  

    // Validación para institución 99  
    if ($cuerpo->exoneracion->institucion == '99') {  
        $estructura['Exoneracion']['NombreInstitucionOtros'] = (string)$items_exonerados[0]->institucion_otro; // Número entero 3 caracteres  
    }  

    $estructura['MontoTotalLinea'] = (string)(($monto_total - $cuerpo->valor_descuento) + $impuesto_nto); // Subtotal + monto impuesto  
    $estructura['ImpuestoNeto'] = (string)$impuesto_nto; // Subtotal + monto impuesto  

    array_push($arreglo, $estructura);  
}
            //EL ARRAY PUSH DEBE IR DENTRO TAMBIEN DEL WHILE QUE VIENE DE BASE DE DATOS ES MAS PRACTICO
        }
$medio_pago = [];
        $medios = [
            'TipoMedioPago'  => ''.$cabecera->medio_pago,
            'TotalMedioPago' => ''.(((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto) + $cabecera->total_otros_cargos + $cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta,
        ];
        array_push($medio_pago,$medios);
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
                'fechaEmision' => date('c')]; // fecha de emision del documento no mayor a 72 horas, date_default_timezone_set debe ser costa rica  ///PARA EMITIR CON FECHA IGUAL AL DOC ORIGINAL $info_ref[0]->fechahora.'T12:12:28-06:00',
                if ($cabecera->tipo_doc_ref != '17') {
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
                if($cabecera->tipo_doc_ref == '01' or $cabecera->tipo_doc_ref == '17' or $cabecera->tipo_doc_ref == '02'){
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
            };
             $xml += [
                        'TieneExoneracion' => ''.$cabecera->tiene_exoneracion,
                        'CondicionVenta' => ''.$cabecera->condicion_venta, //2 digitos Validar Anexos y estructura nota # 5 para verificar los diferentes tipos
                        'PlazoCredito' => ''.$cabecera->p_credito, //en caso de la condicion sea credito o igual a 02 debe agregar plazo credito de resto va en 0

                        'MedioPago' => $cabecera->medio_pago, //2 digitos solo para factura y tiquete validar la Nota #6 de Anexos y Estructuras

                        'DetalleServicio' => $arreglo,
                      
                        'ResumenFactura' => [
                            'CodigoTipoMoneda' =>
                                array(  
    'CodigoMoneda' => $cabecera->tipo_moneda, // validar el documento de Monedas para saber los códigos  
    'TipoCambio' => ($cabecera->tipo_moneda == 'CRC') ? '1' : (string)$cabecera->tipo_cambio // Valor de cambio en caso de que sea una moneda diferente al colón  
), 
                            'TotalServGravados' => ''.$cabecera->total_serv_grab, //obligado cuando el servicio tenga IV (IMPUESTO)
                            'TotalServExentos' => ''.$cabecera->total_serv_exento, // OBLIGADO cuando el servicio este exento de IV
                            'TotalServExonerado' => ''.$cabecera->total_serv_exonerado, // Este campo será de condición obligatoria, cuando el servicio esté gravado y se preste a un cliente que goce de exoneración se debe de indicar el monto equivalente al porcentaje exonerado.
                            'TotalServNoSujeto'        => ''.$cabecera->TotalServNoSujeto,
                            'TotalMercanciasGravadas' => ''.$cabecera->total_mercancia_grav, //obligado cuando la mercancia tenga IV (IMPUESTO)
                            'TotalMercanciasExentas' => ''.$cabecera->total_mercancia_exenta, // OBLIGADO cuando la mercancia este exento de IV
                            'TotalMercExonerada' => ''.$cabecera->total_mercancia_exonerada, //Este campo será de condición obligatoria, cuando la mercancía o producto se venda a un cliente que goce de exoneración para la compra de la misma.
                            'TotalMercNoSujeta'        => ''.$cabecera->TotalMercNoSujeta,
                            'TotalGravado' => ''.$cabecera->total_mercancia_grav + $cabecera->total_serv_grab,  // se obtiene sumando TotalServGravados + TotalMercanciasGravadas
                            'TotalExento' => ''.$cabecera->total_mercancia_exenta + $cabecera->total_serv_exento, // se obtiene sumando TotalServExentos + TotalMercanciasExentas
                            'TotalExonerado' => ''.$cabecera->total_mercancia_exonerada + $cabecera->total_serv_exonerado, //Se obtiene de la suma de los campos “total servicios exonerados de IVA” mas “total de mercancías exoneradas del IVA”.
                            'TotalVenta' => ''.(($cabecera->total_mercancia_grav + $cabecera->total_serv_grab) + $cabecera->total_mercancia_exenta +  $cabecera->total_serv_exonerado + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_exento + $cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta ), // se obtiene sumando TotalGravado + TotalExento
                            'TotalNoSujeto'            => ''.$cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta,
                            'TotalDescuentos' => ''.$cabecera->total_descuento, // se obtiene sumando todos los valores de MontoDescuento que tenga la factura
                            'TotalVentaNeta' => ''.(($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_serv_exonerado + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exento + $cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta)-$cabecera->total_descuento), // se obtiene restando “total venta” “total descuento”.
                            'TotalImpuesto' => ''.$cabecera->total_impuesto, // se obtiene sumando todos los montos impuestos del detalle de factura
                            'TotalImpAsumEmisorFabrica'=> '0.00000', // Nuevo campo para 4.4
                            'TotalIVADevuelto' => ''.$cabecera->total_iva_devuelto,
                            'TotalOtrosCargos'  => ''.$cabecera->total_otros_cargos,
                            'MedioPago'                => $medio_pago,
                            'TotalDesgloseImpuesto'    => $desglose_impuesto,
                            'TotalComprobante' => ''.(((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto)+ $cabecera->total_otros_cargos + $cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta // se obtiene sumando TotalVentaNeta + TotalImpuesto
                        ],
                        'InformacionReferencia' => [
                            'TipoDocIR' => ''.$cabecera->tipo_doc_ref, // Tipo de documento referencia V4.3
                            'Numero' => ''.$cabecera->clave_sale,
                            'FechaEmisionIR' => $cabecera->fecha_emision.'T12:12:28-06:00', // Fecha emision del documento
                            'Codigo' => ''.$codigo_devolucion,
                            'Razon' => ''.mb_substr($cabecera->razon, 0, 178, 'UTF-8'),//$cabecera->razon 
                        ],
                        'Otros' => [
                            'OtroTexto' => ''.mb_substr($cabecera->observaciones, 0, 178, 'UTF-8'),//$info_ref[0]->tipodoc, // Tipo de documento referencia V4.3
                            
                            
                        ]
                        
                    ];
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
                        $datail_otrocargo['TipoDocumentoOTROS']          = $otro->detalle;
                    }
                    
                    $datail_otrocargo['Detalle']          = $otro->detalle;
                    if (!empty($otro->porcentaje_cargo)) {
                        $datail_otrocargo['PorcentajeOC'] = $otro->porcentaje_cargo;
                    }else{
                        $datail_otrocargo['PorcentajeOC'] = 0;
                    }
                    $datail_otrocargo['MontoCargo']       = $otro->monto_cargo;
                    array_push($xml['OtrosCargos'], $datail_otrocargo);
                }
            }
                   //dd($xml);
        return $xml;
    }

        public function filtrarNC(Request $request)
    {
        $datos = $request->all();
        $sales = Sales::where([
            ['tipo_documento', '=', '03'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
        ])->orderBy('numero_documento', 'desc')->get();
        return view('notacredito.index', ['sales' => $sales]);
    }
///filtro rs nc
     public function filtroRegimennc(Request $request)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
        $sales = Sales::where([
            ['tipo_documento', '=', '95'],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return view('notacredito.regimen', ['sales' => $sales]);
    }


        public function index_regimen(Sales $model)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-t');
        $sales = Sales::where([
            ['tipo_documento', '95'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $fecha_desde],
            ['fecha_creada', '<=', $fecha_hasta]
        ])->orderBy('numero_documento', 'desc')->get();
        return view('notacredito.regimen', ['sales' => $sales]);
    }
         public function create_regimen($id)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $sales = Sales::find($id);
        $sales_item = Sales_item::where('idsales', $id)->get();
        $configuracion = Configuracion::find($sales->idconfigfact);
        $cliente = Cliente::find($sales->idcliente);
        $consecutivo = DB::table('consecutivos')->where([
            ['idcaja', '=', $sales->idcaja],
            ['tipo_documento', '=', '95'],
        ])->get();
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
            'estatus_sale' => 1
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
        return redirect()->route('notacredito.edit_regimen', $sales->idsale);
    }

        public function edit_regimen($id)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $sales = Sales::find($id);
        $sales_item = Sales_item::where('idsales', $id)->get();
        $configuracion = Configuracion::find($sales->idconfigfact);
        $cliente = Cliente::find($sales->idcliente);
         if (count($sales_item) > 0) {
            $productos = DB::table('productos')->select('productos.*')
            ->whereNotExists( function ($query) use ($sales_item) {
            $query->select(DB::raw(1))
            ->from('sales_item')
            ->whereRaw('sales_item.idproducto = productos.idproducto')
            ->where('sales_item.idsales', '=', $sales_item[0]->idsales);
            })->where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
        }else{
            $productos = Productos::where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
        }
        return view('regimen.edit', ['configuracion'  => $configuracion, 'cliente'  => $cliente, 'productos' => $productos, 'sales' => $sales, 'sales_item' => $sales_item]);
    }

    public function update_regimen(Request $request, Sales $model)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
        $sales  = Sales::where('idsale', $datos['idsale'])->update([
                    'tipo_devolucion' => $datos['tipo_devolucion'],
                    'estatus_sale' => 2
                ]);
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
        $v_ref = Sales::find($datos['idsale']);
        if (!is_null($datos['observaciones'])) {
            $observaciones = $datos['observaciones'];
        }else{
            $observaciones = 'Nota de Credito generada por defecto';
        }
        foreach ($sales_item as $s_i) {
            $producto = Productos::find($s_i->idproducto);
            if ($s_i->cantidad != 0) {
                $cantidad_stock = $producto->cantidad_stock + $s_i->cantidad;
                $actualizar = Productos::where('idproducto', $s_i->idproducto)->update(['cantidad_stock' => $cantidad_stock]);
            }else{
                $borrado = Sales_item::where('idsalesitem', $s_i->idsalesitem)->delete();
            }

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
                    if ($v_ref->medio_pago  === '02' and $producto->porcentaje_imp == '4.00'){

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

                    if ($v_ref->medio_pago === '02' and $producto->porcentaje_imp == '4.00'){

                        $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                    }
                }

            }else{
                if ($producto->tipo_producto === 2) {
                    $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                    $imps_nto = $s_i->valor_impuesto - $items_exonerados[0]->monto_exoneracion;
                    if ($imps_nto < 0) {
                        $total_impuesto = $total_impuesto + (-1 * $imps_nto);
                    }else{
                        $total_impuesto = $total_impuesto + $imps_nto;
                    }
                    $total_exonerado = $total_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                    $total_serv_exonerado =  $total_serv_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                    $total_serv_grab = $total_serv_grab + ((1-($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc)) * $s_i->valor_neto);
                    $total_neto = $total_neto + $s_i->valor_neto;
                    $total_descuento = $total_descuento + $s_i->valor_descuento;

                }else{
                    $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                    $imps_nto = $s_i->valor_impuesto - $items_exonerados[0]->monto_exoneracion;
                    if ($imps_nto < 0) {
                        $total_impuesto = $total_impuesto + (-1 * $imps_nto);
                    }else{
                        $total_impuesto = $total_impuesto + $imps_nto;
                    }

                    $total_exonerado = $total_exonerado + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                    $total_mercancia_exonerada =  $total_mercancia_exonerada + (($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc) * $s_i->valor_neto);
                    $total_mercancia_grav = $total_mercancia_grav + ((1-($items_exonerados[0]->porcentaje_exoneracion/$s_i->impuesto_prc)) * $s_i->valor_neto);
                    $total_neto = $total_neto + $s_i->valor_neto;
                    $total_descuento = $total_descuento + $s_i->valor_descuento;

                }
            }
        }
        // $total_comprobante = (($total_mercancia_grav + $total_mercancia_exonerada + $total_serv_grab+ $total_serv_exonerado)-$total_descuento) + $total_impuesto;
        $total_exento = $total_serv_exento + $total_mercancia_exenta;
        $total_comprobante = ((($total_mercancia_grav + $total_mercancia_exonerada + $total_mercancia_exenta + $total_serv_grab+ $total_serv_exonerado + $total_serv_exento)-$total_descuento) + $total_impuesto) - $total_iva_devuelto;
        $nota_cre_cons = Consecutivos::where([
            ['idcaja', '=', $v_ref->idcaja],
            ['tipo_documento', '=', '95']
        ])->get();
        $new = $nota_cre_cons[0]->numero_documento + 1;
        $new2 = str_pad($new, 10, "0", STR_PAD_LEFT);
        $consecutivo = DB::update('update consecutivos set numero_documento = '.$new2.' where tipo_documento = "95"  and idcaja = '.$v_ref->idcaja);
        $nota_cre = Sales::find($v_ref->referencia_sale);
        $sales  = Sales::where('idsale', $datos['idsale'])->update([
                    'numero_documento' => str_pad($nota_cre_cons[0]->numero_documento, 10, "0", STR_PAD_LEFT),
                    'fecha_creada' => date('Y-m-d'),
                    'fecha_reenvio' => date('c'),
                    'observaciones' => $observaciones,
                    'total_serv_grab' => $total_serv_grab,
                    'total_serv_exonerado' => $total_serv_exonerado,
                    'total_serv_exento' => $total_serv_exento,
                    'total_mercancia_grav' => $total_mercancia_grav,
                    'total_mercancia_exonerada' => $total_mercancia_exonerada,
                    'total_mercancia_exenta' => $total_mercancia_exenta,
                    'total_exento' => $total_exento,
                    'total_iva_devuelto' => $total_iva_devuelto,
                    'total_exonerado' => $total_exonerado,
                    'total_neto' => $total_neto,
                    'total_descuento' => $total_descuento,
                    'total_impuesto' => $total_impuesto,
                    'total_comprobante' => $total_comprobante,
                    'estatus_sale' => 2
        ]);
        $update_referencia  = Sales::where('idsale', $nota_cre->idsale)->update([
            'estatus_op' => 1
        ]);
        if ($nota_cre->condicion_venta === '02') {
            $cli_cxcobrar = Cxcobrar::where('idcliente', $nota_cre->idcliente)->get();
            if (count($cli_cxcobrar) > 0) {
                $restando = $cli_cxcobrar[0]->saldo_cuenta - $total_comprobante;
                $mcxcobrar = Cxcobrar::where('idcxcobrar', $cli_cxcobrar[0]->idcxcobrar)->update(['saldo_cuenta' => $restando]);
                DB::table('mov_cxcobrar')->select('mov_cxcobrar.*')
                ->where('mov_cxcobrar.idmovcxcobrar', '=', $nota_cre->idmovcxcobrar)->delete();
            }
        }
        return redirect()->route('notacredito.regimen');
    }
}
