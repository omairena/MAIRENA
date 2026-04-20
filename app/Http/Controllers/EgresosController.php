<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Cliente;
use App\Sales;
use App\Sales_item;
use App\Consecutivos;
use App\Facelectron;
use App\Receptor;
use App\Actividad;
use DB;
use Input;
use App\Http\Requests\ReceptorRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Cajas;
use Redirect;
use Carbon\Carbon;
use App\Clasificaciones;
use App\Jobs\RecepcionAutomatica;
use Artisan;
use App\Http\Controllers\CronController;
use App\Bancos;
use App\Tr_bancos;
use App\Proveedor;
class EgresosController extends Controller
{

        public function index(Receptor $model)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
       
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-t');
        $receptor = Receptor::where([
           
            ['idconfigfact', '=', Auth::user()->idconfigfact],
          
            ['estatus_hacienda', '=', 'aceptado'],
            ['bancos', '=', '0']
        ])->orderBy('idreceptor', 'desc')->get();
        //$new_recepcion = Receptor::where('numero_documento_receptor', '=', '9999999999')->where('idconfigfact', '=', Auth::user()->idconfigfact)->count();
        //$clasificaciones = Clasificaciones::where('estatus', '=', 1)->get();
        // validacion para ejecutar la consulta dinamica
        
    	return view('egresos.index', ['sales' => $receptor]);
    }



//rechazar
public function rechaza($id)
    {
     
       
     $update_sales  = Receptor::where('idreceptor', $datos['id_referencia'])->update([
                    'bancos' => 1,
                    
                ]);
      $receptor = Receptor::where([
           
            ['idconfigfact', '=', Auth::user()->idconfigfact],
          
            ['estatus_hacienda', '=', 'aceptado'],
            ['bancos', '=', '0']
        ])->orderBy('idreceptor', 'desc')->get();
       
        
    	return view('egresos.index', ['sales' => $receptor]);
    	           
           
        }



//





	public function store(Request $request)
    {
         $request->validate([
           
            'obs' => 'required|max:100',
        ]);
       
    	$datos = $request->all();
       
       $bancoso = Bancos::where('id_bancos', '=', $datos['banco'])->get();
             
              if( $datos['id_t_referencia'] !='03'){   //si es fact o nd
             if($bancoso[0]->saldo >= $datos['monto_abono']){
            $tr_ingresos = tr_bancos::create(
                [
                    'id_bancos' => $datos['banco'],
                    'monto' => $datos['monto_abono'],
                    'obs' => $datos['obs'],
                    'idcliente' => $datos['idcliente'],
                    'clasificacion'=> 99,
                    'clasificacion_recep'=> $datos['cliente_serch'],
                    'referencia' => $datos['referencia'],
                    'idsales' => 0,
                    'fecha' => date('Y-m-d'),
                    'idconfigfact'=> Auth::user()->idconfigfact,
                    'idreceptor' => $datos['id_referencia'],
                    'factura' => $datos['num_factura'],
                    'signo' => '-',
                    'user'=> Auth::user()->email,
                    
                    ]
            );
           
                $update_sales  = Receptor::where('idreceptor', $datos['id_referencia'])->update([
                    'bancos' => 1,
                    
                ]);
             $bancos = Bancos::where('id_bancos', '=', $datos['banco'])->get();
             
             
          
             $update_banco  = Bancos::where('id_bancos', $datos['banco'])->update([
                    'saldo' => ($bancos[0]->saldo - $datos['monto_abono']),
                    
                ]);
                
           
         $validacion = Proveedor::where('idconfigfact', '=', Auth::user()->idconfigfact)
        ->where('cedula', '=', $datos['num_id'])
        ->count();
        
         if ($validacion > 0) {
                $update_proveedor  = Proveedor::where('cedula', $datos['num_id'])->update([
                   'clasificacion' => ( $datos['cliente_serch']),
                    
               ]);
         }else{
             
              $proveedor = Proveedor::create(
                [
                    'cedula' => $datos['num_id'],
                    'nombre' => $datos['nombre'],
                    'clasificacion' => $datos['cliente_serch'],
                    'idconfigfact' => Auth::user()->idconfigfact,
                    
                    ]
            );
         }
                
         
           	
           	return redirect()->route('egresos.index')->withStatus(__('Registrado correctamente.'));
             }else{
                 	return redirect()->route('egresos.index')->withStatus(__('Fondos Insuficientes en ela Cuenta.')); 
             }
             
             //
              }else{                //si es nc
                  // if($bancoso[0]->saldo >= $datos['monto_abono']){
            $tr_ingresos = tr_bancos::create(
                [
                    'id_bancos' => $datos['banco'],
                    'monto' => $datos['monto_abono'],
                    'obs' => $datos['obs'],
                    'idcliente' => $datos['idcliente'],
                    'clasificacion'=> 99,
                    'clasificacion_recep'=> $datos['cliente_serch'],
                    'referencia' => $datos['referencia'],
                    'idsales' => 0,
                    'fecha' => date('Y-m-d'),
                    'idconfigfact'=> Auth::user()->idconfigfact,
                    'idreceptor' => $datos['id_referencia'],
                    'factura' => $datos['num_factura'],
                     'signo' => '+',
                     'user'=> Auth::user()->email,
                    ]
            );
           
                $update_sales  = Receptor::where('idreceptor', $datos['id_referencia'])->update([
                    'bancos' => 1,
                    
                ]);
             $bancos = Bancos::where('id_bancos', '=', $datos['banco'])->get();
             
             
          
             $update_banco  = Bancos::where('id_bancos', $datos['banco'])->update([
                    'saldo' => ($bancos[0]->saldo + $datos['monto_abono']),
                    
                ]);
                
           
         $validacion = Proveedor::where('idconfigfact', '=', Auth::user()->idconfigfact)
        ->where('cedula', '=', $datos['num_id'])
        ->count();
        
         if ($validacion > 0) {
                $update_proveedor  = Proveedor::where('cedula', $datos['num_id'])->update([
                   'clasificacion' => ( $datos['cliente_serch']),
                    
               ]);
         }else{
             
              $proveedor = Proveedor::create(
                [
                    'cedula' => $datos['num_id'],
                    'nombre' => $datos['nombre'],
                    'clasificacion' => $datos['cliente_serch'],
                    'idconfigfact' => Auth::user()->idconfigfact,
                    
                    ]
            );
         }
                
         
           	
           	return redirect()->route('egresos.index')->withStatus(__('Registrado correctamente.'));
           //  }else{
             //    	return redirect()->route('egresos.index')->withStatus(__('Fondos Insuficientes en ela Cuenta.')); 
           //  }
              }
        }
        
}
