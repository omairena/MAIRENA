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
use DB;
use Auth;

class Ing_manual extends Controller
{
        public function index(Sales $model)
    {
          $bancos = Bancos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
          
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-t');
        $sales = Sales::where([
            ['tipo_documento', '04'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $fecha_desde],
            ['fecha_creada', '<=', $fecha_hasta]
        ])->orderBy('numero_documento', 'desc')->get();
    	return view('ing_manual.index', ['sales' => $sales , 'bancos'=>$bancos,]);
    }

        	public function store(Request $request)
    {
         $request->validate([
           
            'obs' => 'required|max:100',
        ]);
       
    	$datos = $request->all();
       
            $tr_ingresos = tr_bancos::create(
                [
                    'id_bancos' => $datos['banco'],
                    'monto' => $datos['monto_abono'],
                    'obs' => $datos['obs'],
                    'idcliente' => $datos['cliente'],
                    'clasificacion'=> 20,
                    'referencia' => $datos['referencia'],
                    'idsales' => 0,
                    'fecha' => date('Y-m-d'),
                    'idconfigfact'=> Auth::user()->idconfigfact,
                    'factura' => $datos['referencia'],
                    'signo' => '+',
                    'user'=> Auth::user()->email,
                    ]
            );
            
             $bancos = Bancos::where('id_bancos', '=', $datos['banco'])->get();
             
             
            // dd($bancos);
             $update_banco  = Bancos::where('id_bancos', $datos['banco'])->update([
                    'saldo' => ($bancos[0]->saldo + $datos['monto_abono']),
                    
                ]);
            
           	
           	return redirect()->route('trans.index')->withStatus(__('Registrado correctamente.'));
        }
}
