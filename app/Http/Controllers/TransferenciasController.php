<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Cliente;
use App\Sales;
use App\Sales_item;
use App\Consecutivos;
use App\Facelectron;
use App\Tr_bancos;
use App\Bancos;
use App\Transferencias;
use DB;
use Auth;

class transferenciasController extends Controller
{
        public function index(Sales $model)
    {
         $bancos = Bancos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
         
        $sales = Transferencias::where([
          
            ['idconficfact', '=', Auth::user()->idconfigfact],
            
           
        ])->get();
        
        
                                      
    	return view('transferencias.index', ['sales' => $sales,  'bancos'=>$bancos]);
    }

        public function filtrarTiquetes(Request $request)
    {
        $datos = $request->all();
        
        $sales = Tr_bancos::where([
           
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
        ])->get();
       
            
           
        return view('transferencias.index', ['sales' => $sales]);
    }
     public function deleted ($id)
    {
        $tr_banco = Transferencias::find($id);
        
        
       
        
      
                
                $bancoso = Bancos::where('id_bancos', '=', $tr_banco->origen)->get();
             $bancosd = Bancos::where('id_bancos', '=', $tr_banco->destino)->get();
             
            // dd($bancos);
             $update_banc  = Bancos::where('id_bancos', $tr_banco->origen)->update([
                    'saldo' => ($bancoso[0]->saldo + $tr_banco->monto),
                    
                ]);
                $update_bancd  = Bancos::where('id_bancos', $tr_banco->destino)->update([
                    'saldo' => ($bancosd[0]->saldo - $tr_banco->monto),
                    
                ]);
                
         $tr_banco->delete();
                    
        return redirect()->route('transferencias.index')->withStatus(__('Eliminado correctamente.'));
        
    }
    
   public function store(Request $request)
    {
        
         $request->validate([
           
            'obs' => 'required|max:100',
        ]);
       
    	$datos = $request->all();
       if($datos['banco_o'] == $datos['banco_d']){
           return redirect()->route('transferencias.index')->withStatus(__('No se puede transferir en un misma cuenta.'));
       }else{
            
           
            
             $bancoso = Bancos::where('id_bancos', '=', $datos['banco_o'])->get();
             
             if($bancoso[0]->saldo >= $datos['monto_abono']){
                 
                 $tr_ingresos = Transferencias::create(
                [
                    'idconficfact'=> Auth::user()->idconfigfact,
                    'origen' => $datos['banco_o'],
                    'destino' => $datos['banco_d'],
                    'monto' => $datos['monto_abono'],
                    'obs' => $datos['obs'],
                    'referencia' => $datos['referencia'],
                    'fecha' => date('Y-m-d'),
                    'user'=> Auth::user()->email,
                    ]
            );
            
            
             $bancosd = Bancos::where('id_bancos', '=', $datos['banco_d'])->get();
             
            // dd($bancos);
             $update_banc  = Bancos::where('id_bancos', $datos['banco_o'])->update([
                    'saldo' => ($bancoso[0]->saldo - $datos['monto_abono']),
                    
                ]);
                $update_bancd  = Bancos::where('id_bancos', $datos['banco_d'])->update([
                    'saldo' => ($bancosd[0]->saldo + $datos['monto_abono']),
                    
                ]);
            return redirect()->route('transferencias.index')->withStatus(__('Registrado correctamente.'));
             }else{
                 return redirect()->route('transferencias.index')->withStatus(__('Saldo en Cuenta Origen es MENOR al monto al transferir.'));
             }
           	
        }
    }
  
}
