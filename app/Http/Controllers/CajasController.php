<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cajas;
use App\Cajas_user;
use App\Configuracion;
use App\Http\Requests\CajasRequest;
use App\Log_cajas;
use App\Sales;
use App\Mov_cxcobrar;
use App\Consecutivos;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CajasController extends Controller
{
        public function index(Cajas $model)
    {
        $cajas = Cajas::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();

        //dd($consult);
    	return view('cajas.index', ['cajas' => $cajas]);
    }

    	public function create()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite crear cajas");
            return redirect()->route('cajas.index');
        }
    	$configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        return view('cajas.create', ['configuracion' => $configuracion]);
    }

        public function store(CajasRequest $request, Cajas $model)
    {
    	$datos = $request->all();
    	$caja=$model->create($datos);
        for ($i=1; $i < 10; $i++) {
            $consecutivo = Consecutivos::create(
                [
                    'idcaja' => $caja->idcaja,
                    'tipo_documento' => '0'.$i,
                    'numero_documento' => 1,
                    'doc_desde' => 1,
                    'doc_hasta' => 1000,
                    'tipo_compra' => 1
                ]
            );
        }
        //para la parte de CXP y CXC
        $consecutivo = Consecutivos::create(
                [
                    'idcaja' => $caja->idcaja,
                    'tipo_documento' => '99',
                    'numero_documento' => 1,
                    'doc_desde' => 1,
                    'doc_hasta' => 1000,
                    'tipo_compra' => 1
                ]
            );
        $consecutivo = Consecutivos::create(
                [
                    'idcaja' => $caja->idcaja,
                    'tipo_documento' => '98',
                    'numero_documento' => 1,
                    'doc_desde' => 1,
                    'doc_hasta' => 1000,
                    'tipo_compra' => 1
                ]
            );
        //Cotizacion
        $consecutivo = Consecutivos::create(
                [
                    'idcaja' => $caja->idcaja,
                    'tipo_documento' => '97',
                    'numero_documento' => 1,
                    'doc_desde' => 1,
                    'doc_hasta' => 1000,
                    'tipo_compra' => 1
                ]
            );

        // documentos de regimen simplificado Factura 96 NC 95 se crea si en la configuracion tiene el combo activo
        $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->first();
        if ($configuracion->es_simplificado == 1) {

            $consecutivo = Consecutivos::create(
                [
                    'idcaja' => $caja->idcaja,
                    'tipo_documento' => '96',
                    'numero_documento' => 1,
                    'doc_desde' => 1,
                    'doc_hasta' => 1000,
                    'tipo_compra' => 1
                ]
            );
            $consecutivo = Consecutivos::create(
                [
                    'idcaja' => $caja->idcaja,
                    'tipo_documento' => '95',
                    'numero_documento' => 1,
                    'doc_desde' => 1 ,
                    'doc_hasta' => 1000,
                    'tipo_compra' => 1
                ]
            );
        }

        return redirect()->route('cajas.index')->withStatus(__('Caja creada correctamente.'));
    }

        public function edit($id)
    {
        $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    	$caja = Cajas::find($id);
        return view('cajas.edit', ['configuracion' => $configuracion, 'caja' => $caja]);
    }

        public function update(Request $request, $id)
    {
        $datos = $request->except(['_token', '_method']);
        $exists = Cajas::where('idcaja','!=', $id)
        ->where('codigo_unico', $request->codigo_unico)
        ->where('idconfigfact', '=', Auth::user()->idconfigfact)
        ->count();

        if($exists) {
            return Redirect::back()->withErrors('Codigo Unico ya existe coloque otro');
        }
        $validator = Validator::make($request->all(), [
            'nombre_caja' => 'required|min:3',
            'monto_fondo' => 'required'
        ]);

        $errors = array();
        if ($validator->fails()) {

            $e_index = 0;
            foreach($validator->errors()->messages() as $key=>$errorsmsges) {

                $errors[$e_index++] = $errorsmsges[0];
            }
        }

        if (count($errors) > 0) {

            return Redirect::back()->withErrors($errors);

        } else {
            Cajas::where('idcaja',$id)->update($datos);
            return redirect()->route('cajas.index')->withStatus(__('Caja actualizada correctamente.'));
        }
    }

        public function abrir($id)
    {
    	$caja = Cajas::where('idcaja',$id)->update(['estatus' => 1, 'fecha_apertura' => date('Y-m-d')]);
        return redirect()->route('cajas.index')->withStatus(__('Caja abierta correctamente.'));
    }

        public function ver()
    {
    	$cajas = Cajas::where('estatus', 1)->get();
        $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        return view('cajas.ver', ['configuracion' => $configuracion, 'cajas' => $cajas]);
    }

        public function cerrar($id)
    {
    	$caja = Cajas::where('idcaja',$id)->get();
        $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $pagos = 0;
        $abono = 0;
        $sales = DB::table('sales')->select('sales.*', 'sales_item.*')
        ->join('sales_item','sales.idsale','=','sales_item.idsales')
        ->where([
            ['sales.estatus_sale','=', '1'],
            ['sales.idconfigfact','=', Auth::user()->idconfigfact],
        ])->get();
        foreach ($sales as $venta) {
            if ($venta->condicion_venta === '02') {
                DB::table('mov_cxcobrar')->select('mov_cxcobrar.*')
                ->where('mov_cxcobrar.idmovcxcobrar', '=', $venta->idmovcxcobrar)->delete();
            }
        }
        $borrado2 = DB::table('sales')->select('sales.*', 'sales_item.*')
        ->join('sales_item','sales.idsale','=','sales_item.idsales')
        ->where([
            ['sales.estatus_sale','=', '1'],
            ['sales.idconfigfact','=', Auth::user()->idconfigfact],
        ])->delete();
        $borrado = DB::select('DELETE sales.*, sales_item.* FROM sales JOIN sales_item ON sales.idsale = sales_item.idsales WHERE sales.estatus_sale = 1 and sales.idconfigfact = '.Auth::user()->idconfigfact);
        $borrado_sales = DB::select('DELETE sales.* FROM sales WHERE sales.estatus_sale = 1 and sales.idconfigfact = '.Auth::user()->idconfigfact);
    	$data = $this->calcular_cierre($id, $pagos, $abono);
    	//dd($data);
        return view('cajas.cerrar', ['configuracion' => $configuracion, 'caja' => $caja[0], 'data' => $data, 'id' => $id]);
    }

        public function Storecierredia(Request $request, $id)
    {
        $inputs = $request->all();

        $data = $this->calcular_cierre($id, $inputs['pagos_dia'], $inputs['abonos_tarjeta']);
        $info_caja = Cajas::find($id);

if(Auth::user()->super_admin == 0){
        $consulta = DB::table('caja_usuario')
        ->select('caja_usuario.*')
        ->where('idcaja', '=', $id)
        ->where('idusuario', '=', Auth::user()->id)
        ->where('estado', '=', 1)
        ->first();

    }else{
        $consulta = DB::table('caja_usuario')
        ->select('caja_usuario.*')
        ->where('idcaja', '=', $id)
        ->where('idusuario', '=', Auth::user()->id)
       // ->where('estado', '=', 1)
        ->first();
        //dd($consulta);
    }

    //  dd($consulta);
if(is_null($consulta)){
  //  return redirect()->route('cajas.index');
     return redirect()->route('cajas.index')->withStatus(__('El usuario que intenta cerrar caja, no tiene una caja activa.'));
}else{


        $cierre_caja = Log_cajas::create(
            [
                'idcaja' => $id,
                'idusuario' => $consulta->idusuario,
                'fecha_apertura_caja' => $info_caja->fecha_apertura,
                'fecha_cierre_caja' => date('Y-m-d'),
                'fondo_caja' => $info_caja->monto_fondo,
                'ventas_contado' => $data['contado'],
                'ventas_credito' => $data['credito'],
                'recibo_dinero' => $data['recibos_dinero'],
                't_efectivo_entrante' => $data['efectivo_entrante'],
                'cobro_tarjeta' => $data['cobro_tarjeta'],
                'pago_del_dia' => $data['pagos_dia'],
                't_efectivo_caja' => $data['total_efectivo'],
                't_efectivo_depositar' => $data['efectivo_depositar'],
                't_tarjeta_abono' => $data['abonos_tarjeta'],
                'ruta_reporte' => 'vacio'
            ]
        );


if(Auth::user()->super_admin == 0){
        $consulta = DB::table('caja_usuario')
        ->select('caja_usuario.*')
        ->where('idcaja', '=', $id)
        ->where('idusuario', '=', Auth::user()->id)
        ->where('estado', '=', 1)
        ->get();

    }else{
        $consulta = DB::table('caja_usuario')
        ->select('caja_usuario.*')
        ->where('idcaja', '=', $id)
        //->where('idusuario', '=', Auth::user()->id)
        ->where('estado', '=', 1)
        ->get();
        //dd($consulta);
    }
    // dd($consulta);
    foreach ($consulta as $item) {
        DB::table('caja_usuario')->where([
            ['idcajausuario', $item->idcajausuario]])->update([
            'estado' => 0
        ]);
}
        $cierre  = Cajas::where('idcaja', $id)->update([
            'fecha_cierre' => date('Y-m-d'),
            'estatus' => 0
        ]);
        return redirect()->route('cajas.index');
}
    }

        public function show()
    {
        $cajas = DB::table('log_cajas')->select('log_cajas.*')
        ->join('cajas', 'log_cajas.idcaja', '=', 'cajas.idcaja')
        ->where([
            ['cajas.idconfigfact','=', Auth::user()->idconfigfact],
        ])
        ->Paginate(10);
        return view('cajas.cajas', ['cajas' => $cajas]);

    }

        public function ajaxCajas(Request $request)
    {
        $input = $request->all();
        $data = $this->calcular_cierre($input['idcaja'], $input['pago_dia'], $input['abonos_tarjeta']);
        return response()->json(['success'=> $data]);
    }

        public function ajaxAbonos(Request $request)
    {
        $input = $request->all();
        $data = $this->calcular_cierre($input['idcaja'], $input['pago_dia'], $input['abonos_tarjeta']);
        return response()->json(['success'=> $data]);
    }

  public function calcular_cierre($id, $pago_dia, $abono_tarjeta) {  
    $caja = Cajas::find($id);  
    $now = date('Y-m-d');  
    
    // Variable en caso de que se necesite contar efectivo  
    $contado = 0; // Asigna el valor real según sea necesario  
// Define las condiciones adicionales que se utilizarán en la consulta  
    $additionalConditions = [  
        ['sales.condicion_venta', '=', '01'], // Ejemplo de condición  
        ['sales.condicion_venta', '=', '02']  // Otra condición  
    ];  

    // Sumatorias de ventas y notas usando las condiciones  
    $total_comprobante_contado = $this->calcularSumatoria($id,$caja,$now,['01', '04', '09'],[], ['01'] );  
    $total_comprobante_credito = $this->calcularSumatoria($id,$caja,$now,['01', '04', '09'],[], ['02'] );  
    $nota_credito_contado = $this->calcularSumatoria($id, $caja, $now, ['03'], [], ['01']); 
    $nota_credito_credito = $this->calcularSumatoria($id, $caja, $now, ['03'], [], ['02']); 
    $nota_debito_contado = $this->calcularSumatoria($id, $caja, $now, ['02'], [], ['01']); 
    $nota_debito_credito = $this->calcularSumatoria($id, $caja, $now, ['02'], [], ['02']);
    $total_comprobante_rs_contado = $this->calcularSumatoria($id, $caja, $now, ['96'], [], ['01']);  
    $total_comprobante_rs_credito = $this->calcularSumatoria($id, $caja, $now, ['96'], [], ['02']);  
    $nota_credito_rs_contado = $this->calcularSumatoria($id, $caja, $now, ['95'],  [], ['01']); 
    $nota_credito_rs_credito = $this->calcularSumatoria($id, $caja, $now, ['95'],  [], ['02']);

  
    // Cálculo de contado  
    $calculo_total_comprobante_contado = $total_comprobante_contado + $total_comprobante_rs_contado;  
    $calculo_nota_credito_contado= $nota_credito_contado + $nota_credito_rs_contado;  
    $contado = ($calculo_total_comprobante_contado - $calculo_nota_credito_contado) + $nota_debito_contado;  
    // Cálculo de credito 
    $calculo_total_comprobante_credito = $total_comprobante_credito + $total_comprobante_rs_credito;  
    $calculo_nota_credito_credito= $nota_credito_credito + $nota_credito_rs_credito;  
    $credito = ($calculo_total_comprobante_credito - $calculo_nota_credito_credito) + $nota_debito_credito;  

    // Recibos de dinero  
    $recibos_dinero = $this->calcularRecibosDinero($id, $caja, $now, '01');  

    // Cobros con tarjeta  
    $total_comprobante3 = $this->calcularCobrosConTarjeta($id, $caja, $now, ['01', '04', '09']);  
    $nota_credito3 = $this->calcularCobrosConTarjeta($id, $caja, $now, ['03'], '02');  
    $nota_debito3 = $this->calcularCobrosConTarjeta($id, $caja, $now, ['02'], '02');  
    $total_comprobante3_rs = $this->calcularCobrosConTarjeta($id, $caja, $now, ['96']);  
    $nota_credito3_rs = $this->calcularCobrosConTarjeta($id, $caja, $now, ['95']);  

    // Resúmenes de cobros con tarjeta  
    $t_total_comprobante3 = $total_comprobante3 + $total_comprobante3_rs;  
    $t_nota_credito3 = $nota_credito3 + $nota_credito3_rs;  
    $cobro_tarjeta = ($t_total_comprobante3 - $t_nota_credito3) + $nota_debito3;  

    // Abonos con tarjeta  
    $abn_tarjeta = $this->calcularAbonosTarjeta($id, $caja, $now);  

    // Efectivo entrante  
    $efectivo_entrante = $contado + $recibos_dinero;  
    $total_efectivo = (((($efectivo_entrante - $cobro_tarjeta) + $caja->monto_fondo) - $pago_dia));  
    $efectivo_depositar = $total_efectivo - $caja->monto_fondo;  

    // Otros medios  
    $cheque = $this->calcularOtrosMedios($id, $caja, $now, ['03', '04'], ['01', '04', '09'], ['3']);  
    $transferencia = $this->calcularOtrosMedios($id, $caja, $now, ['04'], ['01', '04', '09'], ['4']);  

    // Régimen Simplificado  
    $transferenciaRS = $this->calcularOtrosMedios($id, $caja, $now, ['04'], [['sales.condicion_venta', '=', '01']], ['4']);  
    $chequers = $this->calcularOtrosMedios($id, $caja, $now, ['03'], [['sales.condicion_venta', '=', '01']], ['3']);  
    $sinpe = $this->calcularOtrosMedios($id, $caja, $now, ['03'], [['sales.condicion_venta', '=', '01']], ['5']); 
    $plataformas = $this->calcularOtrosMedios($id, $caja, $now, ['03'], [['sales.condicion_venta', '=', '01']], ['6']); 
    // Total de otros medios  
    $otros = $cheque + $transferencia + $transferenciaRS + $chequers+$sinpe+$plataformas;  

    // Retornar resultados  
    return [  
        'contado' => $contado,  
        'credito' => $credito,  
        'recibos_dinero' => $recibos_dinero,  
        'efectivo_entrante' => $efectivo_entrante,  
        'cobro_tarjeta' => $cobro_tarjeta,  
        'pagos_dia' => $pago_dia,  
        'abonos_tarjeta' => $abn_tarjeta,  
        'total_efectivo' => $total_efectivo,  
        'efectivo_depositar' => $efectivo_depositar - $otros,  
        'otrosmedios' => $otros  
    ];  
}  

    private function calcularRecibosDinero($id, $caja, $now, $medio_pago) {
        return DB::table('log_cxcobrar')->where([
                ['idcaja', $id],
                ['tipo_mov', '=', 1],
                ['medio_pago', '=', $medio_pago],
            ])
            ->whereBetween('fecha_rec_mov', [$caja->fecha_apertura, $now])
            ->sum('monto_abono');
    }

    private function calcularCobrosConTarjeta($id, $caja, $now, $tipoDocumentos, $medio_pago = null) {
        $query = DB::table('sales')->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
            ->where([
                ['sales.idcaja', $id],
                ['sales.estatus_sale', '=', '2'],
                ['sales.medio_pago', '=', '02'],
                ['facelectron.estatushacienda', '!=', 'rechazado'],
            ])
            ->whereIn('tipo_documento', $tipoDocumentos)
            ->whereBetween('fecha_creada', [$caja->fecha_apertura, $now]);

        // Si hay un medio de pago adicional, agregarlo a las condiciones
        if ($medio_pago) {
            $query->where('sales.tipo_documento', $medio_pago);
        }

        return $query->sum('total_comprobante');
    }

    private function calcularAbonosTarjeta($id, $caja, $now) {
        return DB::table('log_cxcobrar')->where([
                ['idcaja', $id],
                ['tipo_mov', '=', 1],
                ['medio_pago', '!=', '01'],
            ])
            ->whereBetween('fecha_rec_mov', [$caja->fecha_apertura, $now])
            ->sum('monto_abono');
    }

    private function calcularOtrosMedios($id, $caja, $now, $tipoDocumentos, $additionalConditions = [], $medio_pago) {
        $query = DB::table('sales')
            ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
            ->join('medio_pago_sale', 'sales.idsale', '=', 'medio_pago_sale.sale_id')
            ->join('medio_pagos', 'medio_pago_sale.medio_pago_id', '=', 'medio_pagos.id')
            ->where([
                ['sales.idcaja', $id],
                ['sales.estatus_sale', '=', '2'],
                ['facelectron.estatushacienda', '!=', 'rechazado'],
            ])
            ->whereIn('tipo_documento', $tipoDocumentos)
            ->whereBetween('fecha_creada', [$caja->fecha_apertura, $now])
            // Filtro para medios de pago, que ahora es múltiple
            ->whereIn('medio_pagos.codigo', $medio_pago); // Aquí se cambió para usar un código de medio de pago

        if (sizeof($additionalConditions) > 0) {
            foreach ($additionalConditions as $condition) {
                if (is_array($condition) && count($condition) === 3) {
                    // Descomponer el arreglo en variables
                    list($column, $operator, $value) = $condition;
                    $query->where($column, $operator, $value);
                }
            }
        }

        return $query->sum('total_comprobante');
    }

 public function calcularSumatoria($id, $caja, $now, $tipoDocumentos, $additionalConditions = [], $condicionesVenta = []) {  
    // Inicia la consulta en la tabla 'sales' con el join adecuado  
    $query = DB::table('sales')->select('sales.*')  
        ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')  
        ->where([  
            ['sales.idcaja', $id],  
            ['sales.estatus_sale', '=', 2],  
            ['facelectron.estatushacienda', '!=', 'rechazado']  
        ])  
        ->whereIn('tipo_documento', $tipoDocumentos)  
        ->whereBetween('fecha_creada', [$caja->fecha_apertura, $now]);  

    // Agregar condiciones de venta si existen  
    if (!empty($condicionesVenta)) {  
        $query->whereIn('sales.condicion_venta', $condicionesVenta);  
    }  

    // Agregar condiciones adicionales si existen  
    foreach ($additionalConditions as $condition) {  
        if (count($condition) === 3) {  
            $query->where($condition[0], $condition[1], $condition[2]);  
        }  
    }  

    return $query->sum('total_comprobante');  
}  

    public function consultaCajeros(Request $request)
    {
        $input = $request->all();
        $users_config = DB::table('users')->select('users.*')->where('users.idconfigfact','=', Auth::user()->idconfigfact)->get();
        $resultado = [];


        foreach ($users_config as $usuarios) {
            $qry = DB::table('caja_usuario')
            ->leftJoin('cajas', 'cajas.idcaja', '=', 'caja_usuario.idcaja')
            ->select('caja_usuario.*', 'cajas.*')
            ->where('caja_usuario.idusuario', '=', $usuarios->id)
            ->where('caja_usuario.estado', '=', '1')
            ->get();

            //if (count($qry) == 0) {
                array_push($resultado, $usuarios);
           //}
        }

        return response()->json(['success'=> $resultado]);
    }

       public function guardarCajeros(Request $request)
{
    Log::info('Inicio del método guardarCajeros.');

    $validator = Validator::make($request->all(), [
        'idcaja' => 'required',
        'idusuario' => 'required',
    ]);

    if ($validator->fails()) {
        Log::error('Validación fallida.', $validator->errors()->toArray());
        return Redirect::back()->withErrors($validator);
    }

    Log::info('Validación exitosa.');

    $idcaja = $request->input('idcaja');
    $idusuario = $request->input('idusuario');

    // Verificar si el usuario ya tiene una caja abierta
    $exists = DB::table('caja_usuario')
        ->where('idcaja', $idcaja)
        ->where('idusuario', $idusuario)
        ->where('estado', 1)
        ->exists();
//dd($exists);
Log::info('Caja existente ' . $exists);
    if ($exists) {
    Session::flash('error', "El usuario ya tiene una caja abierta.");
    //return redirect()->route('cajas.index');
}

    // Insertar nueva caja de usuario
    $caja_data = [
        'idusuario' => $idusuario,
        'idcaja' => $idcaja,
        'estado' => 1,
    ];

    $caja_id = DB::table('caja_usuario')->insertGetId($caja_data);

    Log::info('Caja creada con ID ' . $caja_id);

    $this->abrir($idcaja);

    return response()->json(['success' => $caja_id]);
}

    public function resumendia()
    {
        $now = Carbon::now()->toDateTimeString();
        $valores = explode(' ', $now);
        $fecha = [];
        $fecha['desde'] = $valores[0];
        $fecha['hasta'] = $valores[0];
        $fecha['caja'] = 0;
        $cajas = DB::table('cajas')->select('cajas.*')
        ->where([
            ['cajas.idconfigfact','=', Auth::user()->idconfigfact],
        ])
        ->get();
        return view('cajas.resumen', ['fecha' => $fecha, 'cajas' => $cajas, 'callback' => 0]);
    }
    public function filtrodaily(Request $request)
    {
        $datos = $request->all();

        if ($datos['idcaja'] > 0) {

            $callback = $this->calcular_resumen_dia($datos['fecha_desde'],$datos['fecha_hasta'], $datos['idcaja']);
            $fecha = [];
            $fecha['desde'] = $datos['fecha_desde'];
            $fecha['hasta'] = $datos['fecha_hasta'];
            $fecha['caja'] = $datos['idcaja'];
            $cajas = DB::table('cajas')->select('cajas.*')
            ->where([
                ['cajas.idconfigfact','=', Auth::user()->idconfigfact],
            ])
            ->get();
            return view('cajas.resumen', ['fecha' => $fecha, 'cajas' => $cajas, 'callback' => $callback]);
        } else {

            Session::flash('message', "Asignar una caja para la consulta");
            return redirect()->route('cajas.resumen');
        }

    }
    public function calcular_resumen_dia($fecha_desde, $fecha_hasta, $idcaja)
    {
        //declaro variables
        $callback = [];
        $cajas = DB::table('cajas')->select('cajas.*')
        ->where([
            ['cajas.idcaja', '=', $idcaja]
        ])
        ->first();
        $callback['dia'] = "Desde: ". $fecha_desde . " -  Hasta: ". $fecha_hasta;
        $callback['caja'] = $cajas->nombre_caja;
        // Se arman en consulta diferente
        //QRY para armar ventas contado
        $qry_contado = DB::table('sales')->select('sales.*')
        ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
        ->where([
            ['sales.idcaja', $idcaja],
            ['sales.estatus_sale','=', '2'],
            ['sales.condicion_venta','=', '01'],
            ['facelectron.estatushacienda','=', 'aceptado'],
            ['sales.tipo_documento','!=', '03'],
        ])
        ->whereBetween('fecha_creada', [$fecha_desde, $fecha_hasta])
        ->sum('total_comprobante');

        // sumatoria de las ventas del regimen simplificado
        $total_comprobante_rs = DB::table('sales')->select('sales.*')
        ->where([
            ['sales.idcaja', $idcaja],
            ['sales.estatus_sale','=', '2'],
            ['sales.condicion_venta','=', '01'],
            ['sales.tipo_documento','=', '96'],
        ])
        ->whereBetween('fecha_creada', [$fecha_desde, $fecha_hasta])
        ->sum('total_comprobante');

        $nota_credito = DB::table('sales')->select('sales.*')
        ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
        ->where([
            ['sales.idcaja', $idcaja],
            ['sales.estatus_sale','=', '2'],
            ['sales.condicion_venta','=', '01'],
            ['sales.tipo_documento','=', '03'],
            ['facelectron.estatushacienda','=', 'aceptado'],
        ])
        ->whereBetween('fecha_creada', [$fecha_desde, $fecha_hasta])
        ->sum('total_comprobante');
        //sumatoria de las notas de credito de RS
        $nota_credito_rs = DB::table('sales')->select('sales.*')
        ->where([
            ['sales.idcaja', $idcaja],
            ['sales.estatus_sale','=', '2'],
            ['sales.condicion_venta','=', '01'],
            ['sales.tipo_documento','=', '95'],
        ])
        ->whereBetween('fecha_creada', [$fecha_desde, $fecha_hasta])
        ->sum('total_comprobante');
        // sumatoria ambos totales - las notas de credito
        $callback['ventas_contado'] = ($qry_contado + $total_comprobante_rs) - ($nota_credito + $nota_credito_rs); // todas las ventas de tipo contado - las nc

        // QRY para armar los abonos
        $recibos_dinero = DB::table('log_cxcobrar')->select('log_cxcobrar.*')
        ->where([
            ['idcaja', $idcaja],
            ['tipo_mov','=', 1],
        ])
        ->whereBetween('fecha_rec_mov', [$fecha_desde, $fecha_hasta])
        ->sum('monto_abono');
        $callback['recibos_dinero'] = $recibos_dinero; // todos los abonos cxc
        $callback['total_efectivo_dia'] = $callback['ventas_contado'] + $callback['recibos_dinero'];
        //QRY para armar ventas credito
        $qry_credito = DB::table('sales')->select('sales.*')
        ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
        ->where([
            ['sales.idcaja', $idcaja],
            ['sales.estatus_sale','=', '2'],
            ['sales.condicion_venta','=', '02'],
            ['facelectron.estatushacienda','=', 'aceptado'],
            ['sales.tipo_documento','!=', '03'],
        ])
        ->whereBetween('fecha_creada', [$fecha_desde, $fecha_hasta])
        ->sum('total_comprobante');

        // sumatoria de las ventas del regimen simplificado
        $total_comprobante_rs_credito = DB::table('sales')->select('sales.*')
        ->where([
            ['sales.idcaja', $idcaja],
            ['sales.estatus_sale','=', '2'],
            ['sales.condicion_venta','=', '02'],
            ['sales.tipo_documento','=', '96'],
        ])
        ->whereBetween('fecha_creada', [$fecha_desde, $fecha_hasta])
        ->sum('total_comprobante');

        $nota_credito_2 = DB::table('sales')->select('sales.*')
        ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
        ->where([
            ['sales.idcaja', $idcaja],
            ['sales.estatus_sale','=', '2'],
            ['sales.condicion_venta','=', '02'],
            ['sales.tipo_documento','=', '03'],
            ['facelectron.estatushacienda','=', 'aceptado'],
        ])
        ->whereBetween('fecha_creada', [$fecha_desde, $fecha_hasta])
        ->sum('total_comprobante');
        //sumatoria de las notas de credito de RS
        $nota_credito_rs_2 = DB::table('sales')->select('sales.*')
        ->where([
            ['sales.idcaja', $idcaja],
            ['sales.estatus_sale','=', '2'],
            ['sales.condicion_venta','=', '02'],
            ['sales.tipo_documento','=', '95'],
        ])
        ->whereBetween('fecha_creada', [$fecha_desde, $fecha_hasta])
        ->sum('total_comprobante');
        // sumatoria ambos totales - las notas de credito
        $callback['ventas_credito'] = ($qry_credito + $total_comprobante_rs_credito) - ($nota_credito_2 + $nota_credito_rs_2);
        $callback['total_venta_dia'] = $callback['total_efectivo_dia'] + $callback['ventas_credito'];
        return $callback;
    }
}
