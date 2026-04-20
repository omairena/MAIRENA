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
use DB;
use App\Productos;
use App\Unidades_medidas;
use DataTables;
use App\Cajas;
use Auth;

class FeeController extends Controller
{
        public function index()
    {
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-t');
        $sales = Sales::where([
            ['tipo_documento', '09'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $fecha_desde],
            ['fecha_creada', '<=', $fecha_hasta]
        ])->orderBy('numero_documento', 'desc')->get();
    	return view('fee.index', ['sales' => $sales]);
    }



     public function filtrarFee(Request $request)
    {
        $datos = $request->all();
        $sales = Sales::where([
            ['tipo_documento', '=', '09'],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return view('fee.index', ['sales' => $sales]);
    }


        public function create()
    {
        $clientes = Cliente::where([
            ['tipo_cliente', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['num_id', '!=', '100000000']
        ])->get();
        $productos = Productos::where([
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['exportable', '=', 1],
            ])->get();
        $cajas = Cajas::where([
            ['estatus', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        return view('fee.create', ['cajas'  => $cajas, 'clientes'  => $clientes, 'productos' => $productos]);
    }

            public function guardar(Request $request, Sales $model)
    {
        $datos = $request->all();
        $cajas = Cajas::find($datos['idcaja']);
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
                'p_credito' => $datos['p_credito'],
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
                'estatus_sale' => 1,
                'creado_por' => Auth::user()->email,
            ]
        );
        if(strstr($datos['sales_item'],',') ){
            $valores = explode(',', $datos['sales_item']);
            for ($i=0; $i < count($valores); $i++) {
                $producto = Productos::find($valores[$i]);
                $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
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
            return redirect()->route('fee.edit', $sales->idsale);
        }else{
            $producto = Productos::find($datos['sales_item']);
            $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
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
            return redirect()->route('fee.edit', $sales->idsale);
        }
    }
        public function edit($id)
    {
        $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $clientes = Cliente::where([
            ['tipo_cliente', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['num_id', '!=', '100000000']
        ])->get();
        $sales = Sales::find($id);
        $cajas = Cajas::where('estatus', 1)->get();
        $sales_item = Sales_item::where('idsales', $id)->get();
        $productos = \DB::table('productos')->select('productos.*')
        ->whereNotExists( function ($query) use ($sales_item) {
        $query->select(DB::raw(1))
        ->from('sales_item')
        ->whereRaw('sales_item.idproducto = productos.idproducto')
        ->where('sales_item.idsales', '=', $sales_item[0]->idsales);
        })->where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();

        return view('fee.edit', ['configuracion'  => $configuracion, 'clientes'  => $clientes, 'productos' => $productos, 'sales' => $sales, 'sales_item' => $sales_item, 'cajas' => $cajas]);
    }

        public function update(Request $request, Sales $model)
    {
        $datos = $request->all();
        $cajas = Cajas::find($datos['idcaja']);
        $sales  = Sales::where('idsale', $datos['idsale'])->update([
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
                    'p_credito' => $datos['p_credito'],
                    'medio_pago' => $datos['medio_pago'],
                    'referencia_pago' => $datos['referencia_pago'],
                    'tiene_exoneracion' => $datos['existe_exoneracion'],
                    'fecha_creada' => date('Y-m-d'),
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
        $total_serv_no_sujeto =0;
        $total_mercancia_exenta = 0;
        $total_mercancia_no_sujeto = 0;
        $total_IVA_ex=0;
        $total_exento=0;
        $total_otros_cargos=0;
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
                }else{
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
            }else{
                if ($producto->tipo_producto === 2) {
                    $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                    $total_impuesto = $total_impuesto + ($items_exonerados[0]->monto_exoneracion - $s_i->valor_impuesto);
                    $total_exonerado = $total_exonerado + $items_exonerados[0]->monto_exoneracion;
                    $total_serv_exonerado =  $total_serv_exonerado + ($s_i->costo_utilidad * $s_i->cantidad);
                    $total_serv_grab = $total_serv_grab + 0;
                    $total_neto = $total_neto + $s_i->valor_neto;
                    $total_descuento = $total_descuento + $s_i->valor_descuento;
                    if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00') {
                        $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                    }
                }else{
                    $items_exonerados = Items_exonerados::where('idsalesitem', $s_i->idsalesitem)->get();
                    $total_impuesto = $total_impuesto + ($items_exonerados[0]->monto_exoneracion - $s_i->valor_impuesto);
                    $total_exonerado = $total_exonerado + $items_exonerados[0]->monto_exoneracion;
                    $total_mercancia_exonerada =  $total_mercancia_exonerada + ($s_i->costo_utilidad * $s_i->cantidad);
                    $total_mercancia_grav = $total_mercancia_grav + 0;
                    $total_neto = $total_neto + $s_i->valor_neto;
                    $total_descuento = $total_descuento + $s_i->valor_descuento;
                    if ($datos['medio_pago'] === '02' and $producto->porcentaje_imp == '4.00') {
                        $total_iva_devuelto = $total_iva_devuelto + $s_i->valor_impuesto;
                    }
                }
            }
        }
        $total_comprobante = $total_comprobante + ((($total_mercancia_grav + $total_mercancia_exonerada + $total_serv_grab + $total_serv_exonerado)-$total_descuento) + $total_impuesto);
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
                    'total_neto' => $total_neto,
                    'total_descuento' => $total_descuento,
                    'total_impuesto' => $total_impuesto,
                    'total_comprobante' => $total_comprobante,
                    'total_IVA_ex' => $total_IVA_ex,
                    'total_otros_cargos' => $total_otros_cargos
        ]);
        $seguridad = $this->armarSeguridad($datos['idconfigfact']);
        //$xml =  $this->armarXml($datos['idsale']);
         $pos=new PosController();
           $xml=$pos->armarXml($datos['idsale']);
        include_once(public_path(). '/funcionFacturacion506.php');
        $facturar = Timbrar_documentos($xml, $seguridad);
        $new = $datos['numero_documento'] + 1;
        $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['tipo_documento'].' and idcaja = '.$datos['idcaja']);
        return redirect()->action('DonwloadController@correoXml', ['id' => $datos['idsale']])->withStatus(__('Factura Agregada Correctamente.'));
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
        $desglose_impuesto= [];
        $arreglo = [];
        foreach ($detalle as $cuerpo) {
            $producto = Productos::find($cuerpo->idproducto);
            $unidad_medida = Unidades_medidas::find($producto->idunidadmedida);
            $monto_total = $cuerpo->costo_utilidad * $cuerpo->cantidad;
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
            }
            if ($cuerpo->existe_exoneracion === '00') {
                array_push($desglose_impuesto, [  
                'Codigo'             => '01',  
                'CodigoTarifaIVA'    => ''.$cuerpo->tipo_impuesto,  
                'TotalMontoImpuesto' => ''.$cuerpo->valor_impuesto  
                ]); 
                $estructura =
                    array(
                    	'PartidaArancelaria' => ''.$producto->partida_arancelaria,
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
                        'BaseImponible' => '00', //Importante para saber si es exoneracion o no, en caso de ser exoneracion 01, de lo contrario 00
                        'EsExoneracion' => ''.$cuerpo->existe_exoneracion, //Importante para saber si es exoneracion o no, en caso de ser exoneracion 01, de lo contrario 00
                        'Impuesto' => [
                            array(
                                'Codigo' => '01', //Es un campo fijo de dos posiciones. Ver notas 7 y 8  en Anexos y Estructuras
                                'CodigoTarifaIVA' => ''.$cuerpo->tipo_impuesto, // Debe de expresarse en porcentaje.  decimal 4,2
                                'Tarifa' => ''.$porcentaje_imp,
                                        //'FactorIVA'=>'0.00000',
                                'Monto' => ''.$cuerpo->valor_impuesto, // se obtiene “subtotal” * tarifa del impuesto
                                'MontoExportacion' => ''.$cuerpo->valor_impuesto
                            )
                        ],
                        'MontoTotalLinea' => ''.$cuerpo->valor_neto+$cuerpo->valor_impuesto // se obtiene sumando subtotal + monto impuesto
                    );
                    array_push($arreglo, $estructura);
            }else{
                $items_exonerados = Items_exonerados::where('idsalesitem', $cuerpo->idsalesitem)->get();
                $impuesto_nto = $cuerpo->valor_impuesto - $items_exonerados[0]->monto_exoneracion;
                $estructura =
                    array(
                    	'PartidaArancelaria' => ''.$producto->partida_arancelaria,
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
                        'BaseImponible' => '00', //Importante para saber si es exoneracion o no, en caso de ser exoneracion 01, de lo contrario 00
                        'EsExoneracion' => ''.$cuerpo->existe_exoneracion, //Importante para saber si es exoneracion o no, en caso de ser exoneracion 01, de lo contrario 00
                        'Impuesto' =>
                            [
                                array(
                                    'Codigo' => '01', //Es un campo fijo de dos posiciones. Ver notas 7 y 8  en Anexos y Estructuras
                                    'CodigoTarifaIVA' => ''.$cuerpo->tipo_impuesto, // Debe de expresarse en porcentaje.  decimal 4,2
                                    'Tarifa' => ''.$porcentaje_imp,
                                    //'FactorIVA'=>'0.00000',
                                    'Monto' => ''.$cuerpo->valor_impuesto, // se obtiene “subtotal” * tarifa del impuesto
                                    'MontoExportacion' => ''.$cuerpo->valor_impuesto,
                                    
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
                        'MontoTotalLinea' => ''.($monto_total-$cuerpo->valor_descuento)+$impuesto_nto, // se obtiene sumando subtotal + monto impuesto
                       
                        
                    );
                    array_push($arreglo, $estructura);
            }
            //EL ARRAY PUSH DEBE IR DENTRO TAMBIEN DEL WHILE QUE VIENE DE BASE DE DATOS ES MAS PRACTICO
        }
        $medio_pago = [];
        $medios = [
            'TipoMedioPago'  => (string) $cabecera->medio_pago,
            'TotalMedioPago' => (string)(((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + $cabecera->total_serv_grab + $cabecera->total_serv_exonerado + $cabecera->total_serv_exento)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto) + $cabecera->total_otros_cargos + $cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta,
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
                            'CorreoElectronico' => [$emisor->email_emisor] // Correo electronico del emisor
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
                            'TotalServExentos' => '0.00000', // OBLIGADO cuando el servicio este exento de IV
                            'TotalServExonerado' => ''.$cabecera->total_serv_exonerado, // Este campo será de condición obligatoria, cuando el servicio esté gravado y se preste a un cliente que goce de exoneración se debe de indicar el monto equivalente al porcentaje exonerado.
                            'TotalServNoSujeto'        => (string) $cabecera->TotalServNoSujeto,
                            'TotalMercanciasGravadas' => ''.$cabecera->total_mercancia_grav, //obligado cuando la mercancia tenga IV (IMPUESTO)
                            'TotalMercanciasExentas' => ''.$cabecera->total_mercancia_exenta, // OBLIGADO cuando la mercancia este exento de IV
                            'TotalMercExonerada' => ''.$cabecera->total_mercancia_exonerada, //Este campo será de condición obligatoria, cuando la mercancía o producto se venda a un cliente que goce de exoneración para la compra de la misma.
                            'TotalMercNoSujeta'        => (string) $cabecera->TotalMercNoSujeta,
                            'TotalGravado' => ''.$cabecera->total_mercancia_grav + $cabecera->total_serv_grab,  // se obtiene sumando TotalServGravados + TotalMercanciasGravadas
                            'TotalExento' => ''.$cabecera->total_mercancia_exenta, // se obtiene sumando TotalServExentos + TotalMercanciasExentas
                            'TotalExonerado' => ''.$cabecera->total_mercancia_exonerada, //Se obtiene de la suma de los campos “total servicios exonerados de IVA” mas “total de mercancías exoneradas del IVA”.
                            'TotalVenta' => ''.($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada + + $cabecera->total_serv_grab), // se obtiene sumando TotalGravado + TotalExento
                            'TotalNoSujeto'            => (string) $cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta,
                            'TotalDescuentos' => ''.$cabecera->total_descuento, // se obtiene sumando todos los valores de MontoDescuento que tenga la factura
                            'TotalVentaNeta' => ''.(($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta +  $cabecera->total_mercancia_exonerada)-$cabecera->total_descuento +  $cabecera->total_serv_grab), // se obtiene restando “total venta” “total descuento”.
                            'TotalImpuesto' => ''.$cabecera->total_impuesto, // se obtiene sumando todos los montos impuestos del detalle de factura
                            'TotalImpAsumEmisorFabrica'=> '0.00000', // Nuevo campo para 4.4
                            'TotalIVADevuelto' => ''.$cabecera->total_iva_devuelto,
                            'TotalOtrosCargos'  => ''.$cabecera->total_otros_cargos,
                            'MedioPago'                => $medio_pago,
                            'TotalDesgloseImpuesto'    => $desglose_impuesto,
                            'TotalComprobante' => ''.((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto + + $cabecera->total_serv_grab + $cabecera->TotalServNoSujeto + $cabecera->TotalMercNoSujeta // se obtiene sumando TotalVentaNeta + TotalImpuesto
                        ]
                    ];
        return $xml;
    }
}
