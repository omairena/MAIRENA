<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Cliente;
use App\Sales;
use App\Sales_item;
use App\Consecutivos;
use App\Facelectron;
use App\Clasificaciones;
use App\Bancos;
use App\Tr_bancos;
use App\Receptor;
use App\Proveedor;
use DB;
use Auth;

class EgresospController extends Controller
{
       

public function show($id)
    {
     
       $bancos = Bancos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    
    
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver las cuentas");
            return redirect()->route('facturar.index');
        }

           $receptor = Receptor::where([
           
            ['idconfigfact', '=', Auth::user()->idconfigfact],
          
            ['estatus_hacienda', '=', 'aceptado'],
            ['bancos', '=', '0'],
            ['idreceptor', $id]
        ])->orderBy('idreceptor', 'desc')->get();
        
         $cliente = Cliente::where([
             ['num_id','=',$receptor[0]->cedula_emisor],
             ['idconfigfact','=', Auth::user()->idconfigfact ],
         
         ])->get();
      
         $total = Receptor::find($id);
        if($total->moneda!='CRC'){
            $total_comprobante=($total->total_comprobante*$total->tc);
        }else{
             $total_comprobante=($total->total_comprobante);
        }
         $clasificaciones = Proveedor::where([
             ['cedula','=',$receptor[0]->cedula_emisor],
             ['idconfigfact','=', Auth::user()->idconfigfact ],
             ])->get();
             
             
             
         return view('egresos.reg_egresos', ['id' => $id, 'factura'=> $total->consecutivo_doc_receptor, 'totalc' => $total_comprobante ,'clasificaciones' => $clasificaciones, 'sales'=>$receptor, 'bancos'=>$bancos, 'cliente'=>$cliente]);
      
    }
    
    public function autocomplete_clasificacion(Request $request)
    {
        $search = $request->get('term');
        $result = Clasificaciones::where([
            ['descripcion', 'like', "%".$search."%"],
            //['tipo_cliente', '=', 1 or 2],
           // ['idconfigfact', '=', Auth::user()->idconfigfact],
        ])->get();
        return response()->json($result);
    }
    
    //rechazar
public function rechaza($id)
    {
     
       
     $update_sales  = Receptor::where('idreceptor', $id)->update([
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
    	
   
        
}
