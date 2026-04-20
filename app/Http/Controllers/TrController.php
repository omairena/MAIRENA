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
use DB;
use Auth;

class TrController extends Controller
{
        public function index(Sales $model)
    {
        
        $sales = Tr_bancos::where([
          
            ['idconfigfact', '=', Auth::user()->idconfigfact],
             ['clasificacion', '=', 20],
           
        ])->orderBy('idsales', 'desc')->get();
        if(!empty($sales[0]->idsales)){
        $factura = Facelectron::where([
                   ['idsales', '=', $sales[0]->idsales],
                                  ])-> get();
                                  
        return view('trans.index', ['sales' => $sales, 'factura'=>$factura]);
        }else{
            return view('trans.index', ['sales' => $sales]);
        }                           
    
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
     public function deleted ($id)
    {
        $tr_banco = Tr_bancos::find($id);
        
        if($tr_banco->signo == '+'){
       
        if($tr_banco->idsales>0){
        $update_sales  = Sales::where('idsale', $tr_banco->idsales)->update([
                    'bancos' => 0,]);
        }
        $bancos = Bancos::where('id_bancos', '=', $tr_banco->id_bancos)->get();
             
             $update_banco  = Bancos::where('id_bancos', $tr_banco->id_bancos)->update([
                    'saldo' => ($bancos[0]->saldo - $tr_banco->monto),
                    
                ]);
         $tr_banco->delete();
                    
        return redirect()->route('ingresos.index')->withStatus(__('Eliminado correctamente.'));
    }else{
        if($tr_banco->idsales>0){
        $update_sales  = Sales::where('idsale', $tr_banco->idsales)->update([
                    'bancos' => 0,]);
        }
        $bancos = Bancos::where('id_bancos', '=', $tr_banco->id_bancos)->get();
             
             $update_banco  = Bancos::where('id_bancos', $tr_banco->id_bancos)->update([
                    'saldo' => ($bancos[0]->saldo + $tr_banco->monto),
                    
                ]);
         $tr_banco->delete();
                    
        return redirect()->route('ingresos.index')->withStatus(__('Eliminado correctamente.'));
        
    }
        
    }
    
   
  
}
