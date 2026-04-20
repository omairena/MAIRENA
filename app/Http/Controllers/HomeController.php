<?php

namespace App\Http\Controllers;
use Illuminate\Support\Arr;
use Auth;
use DB;
use App\Actividad;
use App\App_settings;
use Session;
use App\Configuracion;
use  App\Sales;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     
      
        
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $terminos = DB::table('configuracion')->select('configuracion.*')
        ->join('users','configuracion.idconfigfact', '=', 'users.idconfigfact')
        ->where([
            ['users.id', '=', Auth::user()->id]
        ])->first();
         $hoy = date("Y-m-d");
      
        $contrato = App_settings::where('name', '=', 'terms_conditions')->first();
        return view('dashboard', ['terminos' => $terminos, 'contrato' => $contrato]);
        
        if (Auth::user()->estatus === 0){

            Session::flash('message', "Cuenta Bloqueada, contacta al administrador tel: 8309-3816");
            return redirect()->route('cajas.index');
            
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
    }

        public function inicio()
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
            if (Auth::user()->estatus === 0){

            Session::flash('message', "Cuenta Bloqueada, contacta al administrador tel: 8309-3816");
            return redirect()->route('cajas.index');
            
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
   // dd($i);
        if($terminos[0]->docs < $i){

            Session::flash('message', "Plan Caducado por cantidad de documentos, para renovar contacta al administrador tel: 8309-3816");
            return redirect()->route('cajas.index');
            
        } 
        $buscar = DB::table('user_config')->select('user_config.*')->where('idconfigfact', Auth::user()->idconfigfact)->first();
        app('App\Http\Controllers\DonwloadController')->envio_masivo();
        app('App\Http\Controllers\PeticionesController')->ajaxConsultarReceptor();
 
 
       $userc = DB::table('users')->select('users.*')->where('id', Auth::user()->id)->first();
     
       if($userc->super_admin>0){
 
             if($userc->id==122){
 
         return redirect()->route('config.index');
       }else{
    
    
       }
       }
       
      if($userc->status==0){
       //Auth::logout();
     //  return redirect('login');  
        Session::flash('message', "Cuenta Bloqueada, contacta al administrador tel: 8309-3816");
            return redirect()->route('cajas.index');
          //  sleep(5);
       
        }
       
    
        
       
 
        if(!empty($buscar->usa_pos)){
        if($buscar->usa_pos > 0){
           app('App\Http\Controllers\DonwloadController')->envio_masivo();
           app('App\Http\Controllers\PeticionesController')->ajaxConsultarReceptor();
            return redirect()->route('punto.create');
        
        } else{
         $terminos = DB::table('configuracion')->select('configuracion.*')
        ->join('users','configuracion.idconfigfact', '=', 'users.idconfigfact')
        ->where([
            ['users.id', '=', Auth::user()->id]
        ])->first();
        $contrato = App_settings::where('name', '=', 'terms_conditions')->first();

        //fecha final e inicial dinamicas
        $month_start = strtotime('first day of this month', time());
        //echo date('d/m/Y', $month_start);
        $month_end = strtotime('last day of this month', time());
        //echo date('d/m/Y', $month_end);
 
       app('App\Http\Controllers\DonwloadController')->envio_masivo();
       app('App\Http\Controllers\PeticionesController')->ajaxConsultarReceptor();
       app('App\Http\Controllers\PeticionesController')->ajaxConsultar();
        $fecha_desde = date('Y-m-01');
         $fecha_hasta = date('Y-m-t');
         
     
     $query = \DB::table('sales_item')->select('sales_item.*', 'sales.tipo_documento',  'configuracion.factor_receptor', 'codigo_actividad.codigo_actividad', 'sales.total_iva_devuelto', 'sales.total_otros_cargos' )
              ->join('sales', 'sales_item.idsales', '=', 'sales.idsale')
              ->join('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
              ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
              ->join('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
                ->where([
                  ['sales.fecha_creada', '>=', $fecha_desde],
                  ['sales.fecha_creada', '<=', $fecha_hasta],
                  ['sales.idconfigfact', '=', Auth::user()->idconfigfact],
                  ['facelectron.estatushacienda', '=', 'aceptado'],
                  ['sales.estatus_sale', '=', 2],
                ])->get();

                $datos = [
            'tipo_impuesto' => []
        ];
        if (count($query) > 0) {
          $fcompra = [
            'fcompra' => []
          ];
          $totalmto[1] = 0;
          $totaliva[1] = 0;
          $totalivax[1] = 0;
          $totalmto[2] = 0;
          $totaliva[2] = 0;
          $totalivax[2] = 0;
          $totalmto[3] = 0;
          $totaliva[3] = 0;
          $totalivax[3] = 0;
          $totalmto[4] = 0;
          $totaliva[4] = 0;
          $totalivax[4] = 0;
          $totalmto[5] = 0;
          $totaliva[5] = 0;
          $totalivax[5] = 0;
          $totalmto[6] = 0;
          $totaliva[6] = 0;
          $totalivax[6] = 0;
          $totalmto[7] = 0;
          $totaliva[7] = 0;
          $totalivax[7] = 0;
          $totalmto[8] = 0;
          $totaliva[8] = 0;
          $totalivax[8] = 0;
          $totalmto[9] = 0;
          $totaliva[9] = 0;
          $totalivax[9] = 0;
          $totalmto[10] = 0;
          $totaliva[10] = 0;
          $totalivax[10] = 0;
           $totalmto[11] = 0;
          $totaliva[11] = 0;
          $totalivax[11] = 0;
          $otros_cargos=0;
          foreach ($query as $qry) {
            for ($i=1; $i < 12; $i++) {
              for ($x=1; $x < 10; $x++) {
                if ($qry->tipo_impuesto === '0'.$i) {
                  if ($qry->tipo_documento === '0'.$x) {
                    if ($qry->tipo_documento === '08') {
                     // if (Arr::has($fcompra['fcompra'].'.monto_neto')) {
                       // $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                       // $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;
                     // }else{
                          $fcompra['fcompra'] = [
                            'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                            'monto_iva' => $qry->valor_impuesto
                          ];
                     // }
                    }else{
                      if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {
                        $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                        $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ($qry->valor_impuesto);
                        $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                        $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                        $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                        $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];
                      }else{
                          $datos['tipo_impuesto'][$i][$x] = [
                            'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                            'monto_iva' => $qry->valor_impuesto ,
                            'monto_ivax' =>  $qry->exo_monto,
                          ];
                          $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                          $otros_cargos = $otros_cargos + $qry->total_otros_cargos;
                          $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                          $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                      }
                    }
                  }
                } else {

                  if ($i === 9) {

                    if ($qry->tipo_impuesto === '99') {

                      if ($qry->tipo_documento === '0'.$x ) {

                        if ($qry->tipo_documento === '08') {

                          if (Arr::has($fcompra['fcompra'].'.monto_neto')) {

                            $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;

                          } else {

                            $fcompra['fcompra'] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto
                            ];
                          }

                        } else {

                          if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {

                            $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ($qry->valor_impuesto);
                            $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                            $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                            $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                            $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];

                          } else {

                            $datos['tipo_impuesto'][$i][$x] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto ,
                              'monto_ivax' =>  $qry->exo_monto,
                            ];

                            $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                               $otros_cargos = $otros_cargos + $qry->total_otros_cargos;
                            $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                            $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                          }
                        }
                      }
                    }
                  }
                  //KK
                   if ($i == 10) {

                    if ($qry->tipo_impuesto === '99') {

                      if ($qry->tipo_documento === '96') {

                        if ($qry->tipo_documento === '08') {

                          if (Arr::has($fcompra['fcompra'].'.monto_neto')) {

                            $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;

                          } else {

                            $fcompra['fcompra'] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto
                            ];
                          }

                        } else {

                          if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {

                            $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ($qry->valor_impuesto);
                            $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                            $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                            $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                            $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];

                          } else {

                            $datos['tipo_impuesto'][$i][$x] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto ,
                              'monto_ivax' =>  $qry->exo_monto,
                            ];

                            $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                            $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                            $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                          }
                        }
                      }
                    }
                  }
                  //FIN

                   //KK
                   if ($i == 11) {

                    if ($qry->tipo_impuesto === '99') {

                      if ($qry->tipo_documento === '95') {

                        if ($qry->tipo_documento === '08') {

                          if (Arr::has($fcompra['fcompra'].'.monto_neto')) {

                            $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;

                          } else {

                            $fcompra['fcompra'] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto
                            ];
                          }

                        } else {

                          if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {

                            $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ($qry->valor_impuesto);
                            $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                            $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                            $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                            $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];

                          } else {

                            $datos['tipo_impuesto'][$i][$x] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto ,
                              'monto_ivax' =>  $qry->exo_monto,
                            ];

                            $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                            $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                            $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                          }
                        }
                      }
                    }
                  }
                  //FIN

                }

              }
            }
          }

//ff
 $query4 = \DB::table('receptor')->select('receptor.*')
          ->where([
            ['receptor.fecha_xml_envio', '>=', $fecha_desde],
            ['receptor.fecha_xml_envio', '<=', $fecha_hasta],
            ['receptor.estatus_hacienda', '=', 'aceptado'],
             ['receptor.tipo_documento_recibido', '=', '03'],
              ['receptor.tipo_documento', '=', '05'],
            ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],
          ])->get();
        $iva_recepcionado = $query4->sum('total_impuesto');
        $total_receptor = $query4->sum('total_comprobante');
        $total_iva_devuelto = $query->sum('total_iva_devuelto');
        $datos_receptor4 = [
          'clasifica_d151' => []
        ];
        foreach ($query4 as $qry4) {
          for ($i=1; $i < 8; $i++) {
            if ($qry4->clasifica_d151 === ''.$i) {
                if (Arr::has($datos_receptor4['clasifica_d151'], $i.'.total_impuesto')) {
                   $datos_receptor4['clasifica_d151'][$i]['total_impuesto'] = $datos_receptor4['clasifica_d151'][$i]['total_impuesto'] + $qry4->total_impuesto;
                    $datos_receptor4['clasifica_d151'][$i]['total_comprobante'] = $datos_receptor4['clasifica_d151'][$i]['total_comprobante'] + $qry4->total_comprobante;
                    $datos_receptor4['clasifica_d151'][$i]['imp_creditar'] = $datos_receptor4['clasifica_d151'][$i]['imp_creditar'] + $qry4->imp_creditar;
                    $datos_receptor4['clasifica_d151'][$i]['gasto_aplica'] = $datos_receptor4['clasifica_d151'][$i]['gasto_aplica'] + $qry4->gasto_aplica;
                    $datos_receptor4['clasifica_d151'][$i]['hacienda_imp_creditar'] = $datos_receptor4['clasifica_d151'][$i]['hacienda_imp_creditar'] + $qry4->hacienda_imp_creditar;
                    $datos_receptor4['clasifica_d151'][$i]['hacienda_gasto_aplica'] = $datos_receptor4['clasifica_d151'][$i]['hacienda_gasto_aplica'] + $qry4->hacienda_gasto_aplica;
                }else{
                    $datos_receptor4['clasifica_d151'][$i] = [
                      'total_impuesto' => $qry4->total_impuesto,
                      'total_comprobante' => $qry4->total_comprobante,
                      'imp_creditar' => $qry4->imp_creditar,
                      'gasto_aplica' => $qry4->gasto_aplica,
                      'hacienda_imp_creditar' => $qry4->hacienda_imp_creditar,
                      'hacienda_gasto_aplica' => $qry4->hacienda_gasto_aplica,
                    ];
                }
              }
            }
          }
          //gg
        $query2 = \DB::table('receptor')->select('receptor.*')
          ->where([
            ['receptor.fecha_xml_envio', '>=', $fecha_desde],
            ['receptor.fecha_xml_envio', '<=', $fecha_hasta],
            ['receptor.estatus_hacienda', '=', 'aceptado'],
            ['receptor.tipo_documento_recibido', '=', '01'],
             ['receptor.tipo_documento', '=', '05'],
            ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],
          ])->get();
        $iva_recepcionado = $query2->sum('total_impuesto');
        $total_receptor = $query2->sum('total_comprobante');
        $total_iva_devuelto = $query->sum('total_iva_devuelto');
        $datos_receptor = [
          'clasifica_d151' => []
        ];
        foreach ($query2 as $qry2) {
          for ($i=1; $i < 8; $i++) {
            if ($qry2->clasifica_d151 === ''.$i) {
                if (Arr::has($datos_receptor['clasifica_d151'], $i.'.total_impuesto')) {
                   $datos_receptor['clasifica_d151'][$i]['total_impuesto'] = $datos_receptor['clasifica_d151'][$i]['total_impuesto'] + $qry2->total_impuesto;
                    $datos_receptor['clasifica_d151'][$i]['total_comprobante'] = $datos_receptor['clasifica_d151'][$i]['total_comprobante'] + $qry2->total_comprobante;
                    $datos_receptor['clasifica_d151'][$i]['imp_creditar'] = $datos_receptor['clasifica_d151'][$i]['imp_creditar'] + $qry2->imp_creditar;
                    $datos_receptor['clasifica_d151'][$i]['gasto_aplica'] = $datos_receptor['clasifica_d151'][$i]['gasto_aplica'] + $qry2->gasto_aplica;
                    $datos_receptor['clasifica_d151'][$i]['hacienda_imp_creditar'] = $datos_receptor['clasifica_d151'][$i]['hacienda_imp_creditar'] + $qry2->hacienda_imp_creditar;
                    $datos_receptor['clasifica_d151'][$i]['hacienda_gasto_aplica'] = $datos_receptor['clasifica_d151'][$i]['hacienda_gasto_aplica'] + $qry2->hacienda_gasto_aplica;
                }else{
                    $datos_receptor['clasifica_d151'][$i] = [
                      'total_impuesto' => $qry2->total_impuesto,
                      'total_comprobante' => $qry2->total_comprobante,
                      'imp_creditar' => $qry2->imp_creditar,
                      'gasto_aplica' => $qry2->gasto_aplica,
                      'hacienda_imp_creditar' => $qry2->hacienda_imp_creditar,
                      'hacienda_gasto_aplica' => $qry2->hacienda_gasto_aplica,
                    ];
                }
              }
            }
          }
          $data = [
            'datos' => collect($datos),
            'totalmto' => $totalmto,
            'totaliva' => $totaliva,
             'otros_cargos' => $otros_cargos,
            'query' => $query,
            'fcompra' => $fcompra,
            'iva_recepcionado' => $iva_recepcionado,
            'total_receptor' => $total_receptor,
            'datos_receptor' => $datos_receptor,
            'receptor' => $query2,
             'datos_receptor4' => $datos_receptor4,
            'receptor4' => $query4,
            'total_iva_devuelto' => $total_iva_devuelto
          ];
          return view('dashboard1', ['data' => $data, 'terminos' => $terminos, 'contrato' => $contrato]);
      }else{
        $data = [];
        return view('dashboard1', ['data' => $data, 'terminos' => $terminos, 'contrato' => $contrato]);
      }
      
    }
       
    }



 
    
}

 public function resumen()
       {
           
          
           
       
         $terminos = DB::table('configuracion')->select('configuracion.*')
        ->join('users','configuracion.idconfigfact', '=', 'users.idconfigfact')
        ->where([
            ['users.id', '=', Auth::user()->id]
        ])->first();
        $contrato = App_settings::where('name', '=', 'terms_conditions')->first();

        //fecha final e inicial dinamicas
        $month_start = strtotime('first day of this month', time());
        //echo date('d/m/Y', $month_start);
        $month_end = strtotime('last day of this month', time());
        //echo date('d/m/Y', $month_end);
 
       app('App\Http\Controllers\DonwloadController')->envio_masivo();
       app('App\Http\Controllers\PeticionesController')->ajaxConsultarReceptor();
       app('App\Http\Controllers\PeticionesController')->ajaxConsultar();
        $fecha_desde = date('Y-m-01');
         $fecha_hasta = date('Y-m-t');
         
     
     $query = \DB::table('sales_item')->select('sales_item.*', 'sales.tipo_documento',  'configuracion.factor_receptor', 'codigo_actividad.codigo_actividad', 'sales.total_iva_devuelto', 'sales.total_otros_cargos' )
              ->join('sales', 'sales_item.idsales', '=', 'sales.idsale')
              ->join('configuracion', 'sales.idconfigfact', '=', 'configuracion.idconfigfact')
              ->join('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
              ->join('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
                ->where([
                  ['sales.fecha_creada', '>=', $fecha_desde],
                  ['sales.fecha_creada', '<=', $fecha_hasta],
                  ['sales.idconfigfact', '=', Auth::user()->idconfigfact],
                  ['facelectron.estatushacienda', '=', 'aceptado'],
                  ['sales.estatus_sale', '=', 2],
                ])->get();

                $datos = [
            'tipo_impuesto' => []
        ];
        if (count($query) > 0) {
          $fcompra = [
            'fcompra' => []
          ];
          $totalmto[1] = 0;
          $totaliva[1] = 0;
          $totalivax[1] = 0;
          $totalmto[2] = 0;
          $totaliva[2] = 0;
          $totalivax[2] = 0;
          $totalmto[3] = 0;
          $totaliva[3] = 0;
          $totalivax[3] = 0;
          $totalmto[4] = 0;
          $totaliva[4] = 0;
          $totalivax[4] = 0;
          $totalmto[5] = 0;
          $totaliva[5] = 0;
          $totalivax[5] = 0;
          $totalmto[6] = 0;
          $totaliva[6] = 0;
          $totalivax[6] = 0;
          $totalmto[7] = 0;
          $totaliva[7] = 0;
          $totalivax[7] = 0;
          $totalmto[8] = 0;
          $totaliva[8] = 0;
          $totalivax[8] = 0;
          $totalmto[9] = 0;
          $totaliva[9] = 0;
          $totalivax[9] = 0;
          $totalmto[10] = 0;
          $totaliva[10] = 0;
          $totalivax[10] = 0;
           $totalmto[11] = 0;
          $totaliva[11] = 0;
          $totalivax[11] = 0;
          $otros_cargos=0;
          foreach ($query as $qry) {
            for ($i=1; $i < 12; $i++) {
              for ($x=1; $x < 10; $x++) {
                if ($qry->tipo_impuesto === '0'.$i) {
                  if ($qry->tipo_documento === '0'.$x) {
                    if ($qry->tipo_documento === '08') {
                     // if (Arr::has($fcompra['fcompra'].'.monto_neto')) {
                       // $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                       // $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;
                     // }else{
                          $fcompra['fcompra'] = [
                            'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                            'monto_iva' => $qry->valor_impuesto
                          ];
                     // }
                    }else{
                      if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {
                        $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                        $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ($qry->valor_impuesto);
                        $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                        $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                        $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                        $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];
                      }else{
                          $datos['tipo_impuesto'][$i][$x] = [
                            'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                            'monto_iva' => $qry->valor_impuesto ,
                            'monto_ivax' =>  $qry->exo_monto,
                          ];
                          $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                          $otros_cargos = $otros_cargos + $qry->total_otros_cargos;
                          $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                          $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                      }
                    }
                  }
                } else {

                  if ($i === 9) {

                    if ($qry->tipo_impuesto === '99') {

                      if ($qry->tipo_documento === '0'.$x ) {

                        if ($qry->tipo_documento === '08') {

                          if (Arr::has($fcompra['fcompra'].'.monto_neto')) {

                            $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;

                          } else {

                            $fcompra['fcompra'] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto
                            ];
                          }

                        } else {

                          if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {

                            $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ($qry->valor_impuesto);
                            $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                            $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                            $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                            $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];

                          } else {

                            $datos['tipo_impuesto'][$i][$x] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto ,
                              'monto_ivax' =>  $qry->exo_monto,
                            ];

                            $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                               $otros_cargos = $otros_cargos + $qry->total_otros_cargos;
                            $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                            $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                          }
                        }
                      }
                    }
                  }
                  //KK
                   if ($i == 10) {

                    if ($qry->tipo_impuesto === '99') {

                      if ($qry->tipo_documento === '96') {

                        if ($qry->tipo_documento === '08') {

                          if (Arr::has($fcompra['fcompra'].'.monto_neto')) {

                            $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;

                          } else {

                            $fcompra['fcompra'] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto
                            ];
                          }

                        } else {

                          if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {

                            $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ($qry->valor_impuesto);
                            $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                            $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                            $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                            $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];

                          } else {

                            $datos['tipo_impuesto'][$i][$x] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto ,
                              'monto_ivax' =>  $qry->exo_monto,
                            ];

                            $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                            $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                            $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                          }
                        }
                      }
                    }
                  }
                  //FIN

                   //KK
                   if ($i == 11) {

                    if ($qry->tipo_impuesto === '99') {

                      if ($qry->tipo_documento === '95') {

                        if ($qry->tipo_documento === '08') {

                          if (Arr::has($fcompra['fcompra'].'.monto_neto')) {

                            $fcompra['fcompra']['monto_neto'] = $fcompra['fcompra']['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $fcompra['fcompra']['monto_iva'] = $fcompra['fcompra']['monto_iva'] + $qry->valor_impuesto;

                          } else {

                            $fcompra['fcompra'] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto
                            ];
                          }

                        } else {

                          if (Arr::has($datos['tipo_impuesto'], $i.'.'.$x.'.monto_neto')) {

                            $datos['tipo_impuesto'][$i][$x]['monto_neto'] = $datos['tipo_impuesto'][$i][$x]['monto_neto'] + ($qry->valor_neto - $qry->valor_descuento);
                            $datos['tipo_impuesto'][$i][$x]['monto_iva'] = $datos['tipo_impuesto'][$i][$x]['monto_iva'] + ($qry->valor_impuesto);
                            $datos['tipo_impuesto'][$i][$x]['monto_ivax'] = $datos['tipo_impuesto'][$i][$x]['monto_ivax'] + $qry->exo_monto;
                            $totalmto[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_neto'];
                            $totaliva[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_iva'];
                            $totalivax[$i] =  $datos['tipo_impuesto'][$i][$x]['monto_ivax'];

                          } else {

                            $datos['tipo_impuesto'][$i][$x] = [
                              'monto_neto' => $qry->valor_neto - $qry->valor_descuento,
                              'monto_iva' => $qry->valor_impuesto ,
                              'monto_ivax' =>  $qry->exo_monto,
                            ];

                            $totalmto[$i] =  $totalmto[$i] +  $qry->valor_neto - $qry->valor_descuento;
                            $totaliva[$i] = $totaliva[$i] + $qry->valor_impuesto;
                            $totalivax[$i] = $totalivax[$i] +  $qry->exo_monto;
                          }
                        }
                      }
                    }
                  }
                  //FIN

                }

              }
            }
          }

//ff
 $query4 = \DB::table('receptor')->select('receptor.*')
          ->where([
            ['receptor.fecha_xml_envio', '>=', $fecha_desde],
            ['receptor.fecha_xml_envio', '<=', $fecha_hasta],
            ['receptor.estatus_hacienda', '=', 'aceptado'],
             ['receptor.tipo_documento_recibido', '=', '03'],
              ['receptor.tipo_documento', '=', '05'],
            ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],
          ])->get();
        $iva_recepcionado = $query4->sum('total_impuesto');
        $total_receptor = $query4->sum('total_comprobante');
        $total_iva_devuelto = $query->sum('total_iva_devuelto');
        $datos_receptor4 = [
          'clasifica_d151' => []
        ];
        foreach ($query4 as $qry4) {
          for ($i=1; $i < 8; $i++) {
            if ($qry4->clasifica_d151 === ''.$i) {
                if (Arr::has($datos_receptor4['clasifica_d151'], $i.'.total_impuesto')) {
                   $datos_receptor4['clasifica_d151'][$i]['total_impuesto'] = $datos_receptor4['clasifica_d151'][$i]['total_impuesto'] + $qry4->total_impuesto;
                    $datos_receptor4['clasifica_d151'][$i]['total_comprobante'] = $datos_receptor4['clasifica_d151'][$i]['total_comprobante'] + $qry4->total_comprobante;
                    $datos_receptor4['clasifica_d151'][$i]['imp_creditar'] = $datos_receptor4['clasifica_d151'][$i]['imp_creditar'] + $qry4->imp_creditar;
                    $datos_receptor4['clasifica_d151'][$i]['gasto_aplica'] = $datos_receptor4['clasifica_d151'][$i]['gasto_aplica'] + $qry4->gasto_aplica;
                    $datos_receptor4['clasifica_d151'][$i]['hacienda_imp_creditar'] = $datos_receptor4['clasifica_d151'][$i]['hacienda_imp_creditar'] + $qry4->hacienda_imp_creditar;
                    $datos_receptor4['clasifica_d151'][$i]['hacienda_gasto_aplica'] = $datos_receptor4['clasifica_d151'][$i]['hacienda_gasto_aplica'] + $qry4->hacienda_gasto_aplica;
                }else{
                    $datos_receptor4['clasifica_d151'][$i] = [
                      'total_impuesto' => $qry4->total_impuesto,
                      'total_comprobante' => $qry4->total_comprobante,
                      'imp_creditar' => $qry4->imp_creditar,
                      'gasto_aplica' => $qry4->gasto_aplica,
                      'hacienda_imp_creditar' => $qry4->hacienda_imp_creditar,
                      'hacienda_gasto_aplica' => $qry4->hacienda_gasto_aplica,
                    ];
                }
              }
            }
          }
          //gg
        $query2 = \DB::table('receptor')->select('receptor.*')
          ->where([
            ['receptor.fecha_xml_envio', '>=', $fecha_desde],
            ['receptor.fecha_xml_envio', '<=', $fecha_hasta],
            ['receptor.estatus_hacienda', '=', 'aceptado'],
            ['receptor.tipo_documento_recibido', '=', '01'],
             ['receptor.tipo_documento', '=', '05'],
            ['receptor.idconfigfact', '=', Auth::user()->idconfigfact],
          ])->get();
        $iva_recepcionado = $query2->sum('total_impuesto');
        $total_receptor = $query2->sum('total_comprobante');
        $total_iva_devuelto = $query->sum('total_iva_devuelto');
        $datos_receptor = [
          'clasifica_d151' => []
        ];
        foreach ($query2 as $qry2) {
          for ($i=1; $i < 8; $i++) {
            if ($qry2->clasifica_d151 === ''.$i) {
                if (Arr::has($datos_receptor['clasifica_d151'], $i.'.total_impuesto')) {
                   $datos_receptor['clasifica_d151'][$i]['total_impuesto'] = $datos_receptor['clasifica_d151'][$i]['total_impuesto'] + $qry2->total_impuesto;
                    $datos_receptor['clasifica_d151'][$i]['total_comprobante'] = $datos_receptor['clasifica_d151'][$i]['total_comprobante'] + $qry2->total_comprobante;
                    $datos_receptor['clasifica_d151'][$i]['imp_creditar'] = $datos_receptor['clasifica_d151'][$i]['imp_creditar'] + $qry2->imp_creditar;
                    $datos_receptor['clasifica_d151'][$i]['gasto_aplica'] = $datos_receptor['clasifica_d151'][$i]['gasto_aplica'] + $qry2->gasto_aplica;
                    $datos_receptor['clasifica_d151'][$i]['hacienda_imp_creditar'] = $datos_receptor['clasifica_d151'][$i]['hacienda_imp_creditar'] + $qry2->hacienda_imp_creditar;
                    $datos_receptor['clasifica_d151'][$i]['hacienda_gasto_aplica'] = $datos_receptor['clasifica_d151'][$i]['hacienda_gasto_aplica'] + $qry2->hacienda_gasto_aplica;
                }else{
                    $datos_receptor['clasifica_d151'][$i] = [
                      'total_impuesto' => $qry2->total_impuesto,
                      'total_comprobante' => $qry2->total_comprobante,
                      'imp_creditar' => $qry2->imp_creditar,
                      'gasto_aplica' => $qry2->gasto_aplica,
                      'hacienda_imp_creditar' => $qry2->hacienda_imp_creditar,
                      'hacienda_gasto_aplica' => $qry2->hacienda_gasto_aplica,
                    ];
                }
              }
            }
          }
          $data = [
            'datos' => collect($datos),
            'totalmto' => $totalmto,
            'totaliva' => $totaliva,
             'otros_cargos' => $otros_cargos,
            'query' => $query,
            'fcompra' => $fcompra,
            'iva_recepcionado' => $iva_recepcionado,
            'total_receptor' => $total_receptor,
            'datos_receptor' => $datos_receptor,
            'receptor' => $query2,
             'datos_receptor4' => $datos_receptor4,
            'receptor4' => $query4,
            'total_iva_devuelto' => $total_iva_devuelto
          ];
          return view('dashboard1', ['data' => $data, 'terminos' => $terminos, 'contrato' => $contrato]);
      }else{
        $data = [];
        return view('dashboard1', ['data' => $data, 'terminos' => $terminos, 'contrato' => $contrato]);
      }
      
    }
       
    }



 
    


