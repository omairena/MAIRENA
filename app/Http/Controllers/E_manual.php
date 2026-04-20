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

class E_manual extends Controller
{
     
 public function index(Sales $model)
    {
        $bancos = Bancos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $sales = Tr_bancos::where([
          
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['clasificacion', '=', 99],
           
        ])->orderBy('idsales', 'desc')->get();
        
        
          // dd($sales) ;                        
    	return view('Egreso_manual.index', ['sales' => $sales, 'bancos'=>$bancos]);
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
            
           	
           	return redirect()->route('t_egresos.index')->withStatus(__('Registrado correctamente.'));
             }else{
                 	return redirect()->route('t_egresos.index')->withStatus(__('Fondos Insuficientes en ela Cuenta.')); 
             }
        }
        
         public function jsoncliente(Request $request)
    {
        $datos = $request->all();
        $var_cliente = json_decode($datos['cliente_hacienda']);
       
            $razon_social = $var_cliente->{'nombre'};
            $codigo_actividad = '112233';
        
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
        return redirect()->route('Egreso_manual.index')->withStatus(__('Cliente Creado Satisfactoriamente.'));
        
    }
    
}
