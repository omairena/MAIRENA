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
use App\Cajas;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class FecController extends Controller
{
	    public function index()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-t');
        $sales = Sales::where([
            ['tipo_documento', '08'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $fecha_desde],
            ['fecha_creada', '<=', $fecha_hasta]
        ])->orderBy('numero_documento', 'desc')->get();
    	return view('fec.index', ['sales' => $sales]);
    }
    //omairena 19-04-2023
    public function buscarClienteNombrefe(Request $request)
    {
        $datos = $request->all();
        $cliente = Cliente::where([
            ['nombre', 'like', "%".$datos['nombre_cli']."%"],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return response()->json(['success'=> $cliente]);

    }

    public function autocomplete_clientefec(Request $request)
    {
        $search = $request->get('term');
        $result = Cliente::where([
            ['nombre', 'like', "%".$search."%"],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return response()->json($result);
    }
    //fin

        public function create()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
       $clientes = Cliente::where([
    // ['tipo_cliente', 2],
    ['idconfigfact', '=', Auth::user()->idconfigfact]
])->get();
        
if ($clientes->isEmpty()) {
    $clientes = Cliente::where([
        ['idcliente', '=', 1] // Cambié '==' por '=', ya que es correcto para Eloquent.
    ])->get();
}
        // dd($clientes);
        $productos = Productos::where(
        [  ['idconfigfact', '=', Auth::user()->idconfigfact],
        ['impuesto_iva','!=',99],
        //['porcentaje_imp','<=',0]

        ]

        )->get();
        $cajas = Cajas::where([
            ['estatus', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        return view('fec.create', ['cajas'  => $cajas, 'clientes'  => $clientes, 'productos'  => $productos]);
          $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $contadfe = Cliente::where('num_id', '100000000')->get();
        return view('fec.create', ['configuracion'  => $configuracion, 'clientes'  => $clientes, 'productos' => $productos, 'cajas' => $cajas, 'contado' => $contado]);
    }

        public function guardar(Request $request, Sales $model)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
        $valores = explode(',', $datos['totales_fact'][0]);
        $cajas = Cajas::find($datos['idcaja']);
         if (!is_null($datos['observaciones'])) {
            $observaciones = $datos['observaciones'];
        }else{
            $observaciones = '';
        }
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
                'total_neto' => '0.00000',
                'total_descuento' => '0.00000',
                'total_impuesto' => '0.00000',
                'total_otros_cargos' => '0.00000',
                'total_iva_devuelto' => '0.00000',
                'total_comprobante' => '0.00000',
                'estatus_sale' => 1,
                'fecha_creada' => date('Y-m-d'),
                'observaciones' => $observaciones,
                'referencia_compra' => $datos['referencia_compra'],
                'tiene_exoneracion' => '00',
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
                        'existe_exoneracion' => '00'
                    ]
                );

            }
        }else{
             $cantidad = 1;
            $producto = Productos::find($datos['sales_item']);
            $valor_imp = ($producto->precio_sin_imp * $producto->porcentaje_imp)/100;
            $sales_item = Sales_item::create(
                [
                    'idsales' => $sales->idsale,
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
        }
        return redirect()->route('fec.edit', $sales->idsale);
    }

        public function edit($id)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $clientes = Cliente::where([
           // ['tipo_cliente', 2],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        $sales = Sales::find($id);
        $sales_item = Sales_item::where('idsales', $id)->get();
        $cajas = Cajas::where([
            ['estatus', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        if (count($sales_item) > 0) {
            $productos = \DB::table('productos')->select('productos.*')
                ->whereNotExists( function ($query) use ($sales_item) {
                $query->select(DB::raw(1))
                ->from('sales_item')
                ->whereRaw('sales_item.idproducto = productos.idproducto')
                ->where('sales_item.idsales', '=', $sales_item[0]->idsales);
                })->where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
        }else{
            $productos = Productos::where('productos.idconfigfact', '=', Auth::user()->idconfigfact)->get();
        }
        return view('fec.edit', ['cajas'  => $cajas, 'clientes'  => $clientes, 'sales' => $sales, 'sales_item' => $sales_item, 'productos' => $productos]);
    }

        public function agregarLineaFec(Request $request)
    {
        $datos = $request->all();
        return response()->json(['success'=> $datos]);
        $input = array();
        parse_str($datos['submit_producto'], $input);
        $valor_neto = $input['precio_unitario'] * $input['cantidad'];
        $valor_descuento = ($valor_neto * $input['descuento'])/100;
        if ($valor_descuento > 0) {
            $valor_neto = $valor_neto - $valor_descuento;
        }
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
        $valor_impuesto = ($valor_neto * $porcentaje_imp)/100;
        $sales_item = Sales_item::create(
            [
                'idsales' => $datos['idsale'],
                'codigo_producto' =>  $input['codigo_producto'],
                'idproducto' =>  0,
                'nombre_producto' =>  $input['nombre_producto'],
                'cantidad' => $input['cantidad'],
                'valor_neto' => $valor_neto,
                'valor_descuento' => $valor_descuento,
                'valor_impuesto' => $valor_impuesto,
                'tipo_impuesto' => $input['tipo_impuesto'],
                'impuesto_prc' => $porcentaje_imp,
                'descuento_prc' => $input['descuento'],
                'costo_utilidad' => $input['precio_unitario'],
                'existe_exoneracion' => '00'
            ]
        );
        return response()->json(['success'=> $input]);
    }

    public function update(Request $request, Sales $model)
        {
            if (Auth::user()->es_vendedor == 1){

                Session::flash('message', "Tu usuario no permite ver configuraciones");
                return redirect()->route('facturar.index');
            }
            $datos = $request->all();
           
           // return redirect()->route('fec.index')->withStatus(__('Factura Electronica de Compra Agregada Correctamente.'));
            $cajas = Cajas::find($datos['idcaja']);
            if (!is_null($datos['observaciones'])) {
                $observaciones = $datos['observaciones'];
            }else{
                $observaciones = '';
            }

             $consecutivo = DB::table('consecutivos')->where([
            ['idcaja', '=', $datos['idcaja']],
            ['tipo_documento', '=', $datos['tipo_documento']],
        ])->get();
        $numero_factura = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
        
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
                    'tiene_exoneracion' => '00',
                    'observaciones' => $observaciones,
                    'referencia_compra' => $datos['referencia_compra'],
                    'estatus_sale' => 2,
                    'TipoDocIR'=>$datos['TipoDocIR'],
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
            $total_otros_cargos=0;
            foreach ($sales_item as $s_i) {
                  $producto = Productos::find($s_i->idproducto);
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
            }
            $total_exento = $total_serv_exento + $total_mercancia_exenta;
            $total_comprobante = $total_comprobante + ((($total_mercancia_grav + $total_mercancia_exonerada)-$total_descuento) + $total_impuesto) + $total_exento +$total_mercancia_no_sujeto ;
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
                    'total_otros_cargos' => $total_otros_cargos
            ]);
           //dd($total_comprobante);
            if($total_comprobante > 0){
                
            $seguridad = $this->armarSeguridad($datos['idconfigfact']);
           // $xml =  $this->armarXml($datos['idsale']);
             $pos=new PosController();
           $xml=$pos->armarXml($datos['idsale']);
           
            include_once(public_path(). '/funcionFacturacion506.php');
            $facturar = Timbrar_documentos($xml, $seguridad);
            $new = $numero_factura + 1;
            $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = '.$datos['tipo_documento'].' and idcaja = '.$datos['idcaja']);
            app('App\Http\Controllers\PeticionesController')->ajaxConsultarFec();
            return redirect()->route('fec.index')->withStatus(__('Factura Electronica de Compra Agregada Correctamente.'));
        }else{
         //  dd(session()->all());
            return redirect()->route('fec.edit',$datos['idsale'])->with(__('Factura no puede ser 0'));
            
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
            //$unidad_medida = Unidades_medidas::find($producto->idunidadmedida);
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
                case '99':
                    $porcentaje_imp = 0;
                break;
            }
                $estructura =
                    array(
                        'Codigo' => ''.$producto->codigo_cabys,
                        'CodigoComercial' => array(
                            'Tipo' => '04', //valor 2 digitos tipo de producto Validar Anexos y estructuras NOTA # 12
                            'Codigo' => ''.str_pad($cuerpo->codigo_producto, 10, "0", STR_PAD_LEFT) //Corresponde al codigo del poducto en el sistema 10 digitos
                        ),
                        'Cantidad' => ''.$cuerpo->cantidad,
                        'UnidadMedida' => 'Unid', // Debe ser una de las medidas expresadas en los codigos de Medidas de Hacienda
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
                                'CodigoTarifa' => ''.$cuerpo->tipo_impuesto, // Debe de expresarse en porcentaje.  decimal 4,2
                                'Tarifa' => ''.$porcentaje_imp,
                                        //'FactorIVA'=>'0.00000',
                                'Monto' => ''.$cuerpo->valor_impuesto // se obtiene “subtotal” * tarifa del impuesto
                            )
                        ],
                        'MontoTotalLinea' => ''.$cuerpo->valor_neto+$cuerpo->valor_impuesto // se obtiene sumando subtotal + monto impuesto
                    );
                    array_push($arreglo, $estructura);
            //EL ARRAY PUSH DEBE IR DENTRO TAMBIEN DEL WHILE QUE VIENE DE BASE DE DATOS ES MAS PRACTICO
        }

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
                                'Barrio' => '01',// Nodo necesario para 4.3 Mensaje Receptor
                                'OtrasSenas' => ''.$cliente->direccion // informacion complementaria de la direccion
                            ),
                            'Telefono' => array(
                                'CodigoPais' => '506', // 3 digitos verifica el codigo pais del telefono
                                'NumTelefono' => ''.$cliente->telefono //Numero telefonico del emisor
                            ),
                            'CorreoElectronico' => ''.$cliente->email // Correo electronico del emisor
                        ),
                        'EsExtranjero' => '00',
                       'Receptor' => array(
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
                                'OtrasSenas' => ''.$emisor->direccion_emisor // informacion complementaria de la direccion
                            ),
                            'Telefono' => array(
                               'CodigoPais' => '506', // 3 digitos verifica el codigo pais del telefono
                              'NumTelefono' => ''.$emisor->telefono_emisor //Numero telefonico del emisor
                           ),
                           'CorreoElectronico' => ''.$emisor->email_emisor // Correo electronico del receptor a donde se envia la factura
                        ),
                        'TieneExoneracion' => '00',
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
                            'TotalServGravados' => '0.00000', //obligado cuando el servicio tenga IV (IMPUESTO)
                            'TotalServExentos' => '0.00000', // OBLIGADO cuando el servicio este exento de IV
                            'TotalServExonerado' => '0.00000', // Este campo será de condición obligatoria, cuando el servicio esté gravado y se preste a un cliente que goce de exoneración se debe de indicar el monto equivalente al porcentaje exonerado.
                            'TotalMercanciasGravadas' => ''.$cabecera->total_mercancia_grav, //obligado cuando la mercancia tenga IV (IMPUESTO)
                            'TotalMercanciasExentas' => ''.$cabecera->total_mercancia_exenta, // OBLIGADO cuando la mercancia este exento de IV
                            'TotalMercExonerada' => ''.$cabecera->total_mercancia_exonerada, //Este campo será de condición obligatoria, cuando la mercancía o producto se venda a un cliente que goce de exoneración para la compra de la misma.
                            'TotalGravado' => ''.$cabecera->total_mercancia_grav,  // se obtiene sumando TotalServGravados + TotalMercanciasGravadas
                            'TotalExento' => ''.$cabecera->total_mercancia_exenta, // se obtiene sumando TotalServExentos + TotalMercanciasExentas
                            'TotalExonerado' => ''.$cabecera->total_mercancia_exonerada, //Se obtiene de la suma de los campos “total servicios exonerados de IVA” mas “total de mercancías exoneradas del IVA”.
                            'TotalVenta' => ''.($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada), // se obtiene sumando TotalGravado + TotalExento
                            'TotalDescuentos' => ''.$cabecera->total_descuento, // se obtiene sumando todos los valores de MontoDescuento que tenga la factura
                            'TotalVentaNeta' => ''.(($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta +  $cabecera->total_mercancia_exonerada)-$cabecera->total_descuento), // se obtiene restando “total venta” “total descuento”.
                            'TotalImpuesto' => ''.$cabecera->total_impuesto, // se obtiene sumando todos los montos impuestos del detalle de factura
                            'TotalIVADevuelto' => ''.$cabecera->total_iva_devuelto,
                            'TotalOtrosCargos'  => ''.$cabecera->total_otros_cargos,
                            'TotalComprobante' => ''.((($cabecera->total_mercancia_grav + $cabecera->total_mercancia_exenta + $cabecera->total_mercancia_exonerada)-$cabecera->total_descuento)+$cabecera->total_impuesto)-$cabecera->total_iva_devuelto // se obtiene sumando TotalVentaNeta + TotalImpuesto
                        ],
                        'InformacionReferencia' => [
                            'TipoDoc' => '14', // Tipo de documento referencia V4.3
                            'Numero' => ''.$cabecera->referencia_compra,
                            'FechaEmision' => $cabecera->fecha_creada.'T12:12:28-06:00', // Fecha emision del documento
                            'Codigo' => '04',
                            'Razon' => 'Compras realizadas a proveedor del Régimen Simplificado'
                        ],
                        'Otros' =>  [
                            'OtroTexto' => ''.$cabecera->observaciones
                        ],
                    ];
        return $xml;
    }

        public function actualiarCantFec(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $total_neto = $sales_item->costo_utilidad * $input['cantidad'];
        if ($sales_item->descuento_prc > 0) {
            $total_descuento = ($total_neto * $sales_item->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
        $total_neto = $total_neto - $total_descuento;
        $total_impuesto = ($total_neto * $sales_item->impuesto_prc)/100;

        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['cantidad' => $input['cantidad'], 'valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento]);

        return response()->json(['success'=> $input]);
    }
    //omairena10-03-2023
      public function actualiarCostFec(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $total_neto = $input['costo'] ;
        if ($sales_item->descuento_prc > 0) {
            $total_descuento = ($total_neto * $sales_item->descuento_prc)/100;
        }else{
            $total_descuento = 0.00000;
        }
        $total_neto = $total_neto - $total_descuento;
        $total_impuesto = ($total_neto * $sales_item->impuesto_prc)/100;

        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['costo_utilidad' => $input['costo'], 'valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $total_descuento]);

        return response()->json(['success'=> $input]);
    }


        public function actualiarDescFec(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $total_neto = $sales_item->costo_utilidad * $sales_item->cantidad;
        $mto_descuento = ($total_neto * $input['porcentaje_descuento'])/100;
        $total_neto = $total_neto - $mto_descuento;
        $total_impuesto = ($total_neto * $sales_item->impuesto_prc)/100;
        $actualizar = Sales_item::where('idsalesitem', $input['idsalesitem'])->update(['valor_neto' => $total_neto, 'valor_impuesto' => $total_impuesto, 'valor_descuento' => $mto_descuento, 'descuento_prc' => $input['porcentaje_descuento']]);
        return response()->json(['success'=> $input]);

    }
         public function editarObservacionesfec(Request $request)
    {
        $datos = $request->all();
        Sales::where('idsale', $datos['idsale'])->update(['observaciones' =>  $datos['observacion']]);
        return response()->json(['success'=> $datos]);
    }
    public function editarrefcompra(Request $request)
    {
        $datos = $request->all();
        Sales::where('idsale', $datos['idsale'])->update(['referencia_compra' =>  $datos['ref_compra']]);
        return response()->json(['success'=> $datos]);
    }
        public function eliminarLineaFec(Request $request)
    {
        $input = $request->all();
        $sales_item = Sales_item::find($input['idsalesitem']);
        $sales_item->delete();
        return response()->json(['success'=> $input]);

    }

        public function filtrarFEC(Request $request)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
        $sales = Sales::where([
            ['tipo_documento', '=', '08'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
        ])->paginate(15);
        return view('fec.index', ['sales' => $sales]);
    }

    public function modificarProducto(Request $request)
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
        $valor_neto = $input['costo_utilidad'] * $input['cantidad'];
        if ($input['descuento'] > 0) {

            $valor_descuento = ($valor_neto * $input['descuento'])/100;
        }
        $valor_impuesto = (($valor_neto - $valor_descuento) * $porcentaje_imp)/100;

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



   public function jsonclienfc (Request $request)
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
        return redirect()->route('fec.create');
    }
 }

