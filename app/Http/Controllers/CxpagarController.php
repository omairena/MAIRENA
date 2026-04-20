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
use App\Inventario;
use App\Inventario_item;
use App\Cxpagar;
use App\Mov_cxpagar;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CxpagarController extends Controller
{
        public function index(Cxpagar $model)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver las cuentas");
            return redirect()->route('facturar.index');
        }
        $cxpagar = Cxpagar::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    	return view('cxpagar.index', ['cxpagar' => $cxpagar]);

    }

        public function show($id)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver las cuentas");
            return redirect()->route('facturar.index');
        }
    	$mov_cxpagar = Mov_cxpagar::where('idcxpagar', $id)->get();
    	$cxpagar = Cxpagar::find($id);
        return view('cxpagar.show', ['mov_cxpagar' => $mov_cxpagar, 'cxpagar' => $cxpagar]);
    }
}
