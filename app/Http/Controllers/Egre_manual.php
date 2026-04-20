<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Cliente;
use App\Sales;
use App\Sales_item;
use App\Consecutivos;
use App\Facelectron;
use App\Bancos;
use App\Tr_bancos;
use App\Receptor;
use DB;
use Auth;

class Egre_manual extends Controller
{
     
 public function index(Sales $model)
    {
        $bancos = Bancos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $sales = Tr_bancos::where([
          
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['clasificacion', '=', 99],
           
        ])->orderBy('idsales', 'desc')->get();
        
        
          // dd($sales) ;                        
    	return view('t_egresos.index', ['sales' => $sales, 'bancos'=>$bancos]);
    }
     public function filtrarTiquetes(Request $request)
    {
        $datos = $request->all();
        
        $sales = Tr_bancos::where([
           
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
        ])->get();
       
            
           
        return view('trans.index', ['sales' => $sales]);
    }
    
        	public function store(Request $request)
    {
         $request->validate([
           
            'obs' => 'required|max:100',
        ]);
       
    	$datos = $request->all();
       
        $bancoso = Bancos::where('id_bancos', '=', $datos['banco'])->get();
             
             if($bancoso[0]->saldo >= $datos['monto_abono']){
            $tr_ingresos = tr_bancos::create(
                [
                    'id_bancos' => $datos['banco'],
                    'monto' => $datos['monto_abono'],
                    'obs' => $datos['obs'],
                    'idcliente' => $datos['cliente'],
                    'clasificacion'=> 99,
                    'clasificacion_recep'=> $datos['cliente_serch'],
                    'referencia' => $datos['referencia'],
                    'idsales' => 0,
                    'fecha' => date('Y-m-d'),
                    'idconfigfact'=> Auth::user()->idconfigfact,
                    'factura' => $datos['id_referencia'],
                     'signo' => '-',
                     'user'=> Auth::user()->email,
                    ]
            );
            
           
            
             $bancos = Bancos::where('id_bancos', '=', $datos['banco'])->get();
             
             
            // dd($bancos);
             $update_banco  = Bancos::where('id_bancos', $datos['banco'])->update([
                    'saldo' => ($bancos[0]->saldo - $datos['monto_abono']),
                    
                ]);
            
           	
           	return redirect()->route('trans.index')->withStatus(__('Registrado correctamente.'));
             }else{
                	return redirect()->route('trans.index')->withStatus(__('Fondos Insuficientes en ela Cuenta.')); 
             }
        }
        
        public function deleted ($id)
    {
        $tr_banco = Tr_bancos::find($id);
        
       if($tr_banco->signo == '-'){   //si signo es -
       
        if($tr_banco->idreceptor>0){
                   $update_sales  = Receptor::where('idreceptor', $tr_banco->idreceptor)->update([
                    'bancos' => 0,
                    
                ]);
        }
        $bancos = Bancos::where('id_bancos', '=', $tr_banco->id_bancos)->get();
             
             $update_banco  = Bancos::where('id_bancos', $tr_banco->id_bancos)->update([
                    'saldo' => ($bancos[0]->saldo + $tr_banco->monto),
                    
                ]);
         $tr_banco->delete();
                    
        return redirect()->route('egresos.index')->withStatus(__('Eliminado correctamente.'));
        
       }else{     //si signo es +
           
           if($tr_banco->idreceptor>0){
                   $update_sales  = Receptor::where('idreceptor', $tr_banco->idreceptor)->update([
                    'bancos' => 0,
                    
                ]);
        }
        $bancos = Bancos::where('id_bancos', '=', $tr_banco->id_bancos)->get();
             
             $update_banco  = Bancos::where('id_bancos', $tr_banco->id_bancos)->update([
                    'saldo' => ($bancos[0]->saldo - $tr_banco->monto),
                    
                ]);
         $tr_banco->delete();
                    
        return redirect()->route('egresos.index')->withStatus(__('Eliminado correctamente.')); 
       }
        
        } 
       
    
     public function autocomplete_clasificaciontr(Request $request)
    {
        $search = $request->get('term');
        $result = Clasificaciones::where([
            ['descripcion', 'like', "%".$search."%"],
            //['tipo_cliente', '=', 1 or 2],
           // ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return response()->json($result);
    }
}
