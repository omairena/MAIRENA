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
use Illuminate\Contracts\Container\Container;
use App\Items_exonerados;
use App\Productos;
use App\Unidades_medidas;
use DataTables;
use App\Cxcobrar;
use App\Mov_cxcobrar;
use App\Cajas;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CxcobrarController extends Controller
{
       	public function index(Cxcobrar $model)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver las cuentas");
            return redirect()->route('facturar.index');
        }
        $cxcobrar = Cxcobrar::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
       
       foreach($cxcobrar as $csc){
           
         $monto_total_cxc=0;
               $monto_total_cxc = DB::table('mov_cxcobrar')
               ->where('idcxcobrar', '=', $csc->idcxcobrar)
                ->sum('saldo_pendiente');
              
               
                 Cxcobrar::where('idcxcobrar', $csc->idcxcobrar)->update([
                    'saldo_cuenta' => $monto_total_cxc
                    
                ]);
               //  dd($monto_total_cxc);
            }

         
        
        return view('cxcobrar.index', ['cxcobrar' => $cxcobrar]);
    }

        public function show($id)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver las cuentas");
            return redirect()->route('facturar.index');
        }
    	$mov_cxcobrar = Mov_cxcobrar::where('idcxcobrar', $id)->orderByRaw('estatus_mov - 1 ASC')->get();
    	$cxcobrar = Cxcobrar::find($id);
        $cajas = Cajas::where([
            ['idconfigfact', $cxcobrar->idconfigfact],
            ['estatus', 1]
        ])->get();
        return view('cxcobrar.show', ['mov_cxcobrar' => $mov_cxcobrar, 'cxcobrar' => $cxcobrar, 'cajas' => $cajas]);
    }
}
