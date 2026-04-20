<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Cliente;
use App\Sales;
use App\Sales_item;
use App\Consecutivos;
use App\Facelectron;
use DB;
use Auth;

class TiquetesController extends Controller
{
        public function index(Sales $model)
    {
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-t');
        $sales = Sales::where([
            ['tipo_documento', '04'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $fecha_desde],
            ['fecha_creada', '<=', $fecha_hasta]
        ])->orderBy('numero_documento', 'desc')->get();
    	return view('tiquetes.index', ['sales' => $sales]);
    }

        public function filtrarTiquetes(Request $request)
    {
        $datos = $request->all();
        $sales = Sales::where([
            ['tipo_documento', '=', '04'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $datos['fecha_desde']],
            ['fecha_creada', '<=', $datos['fecha_hasta']],
        ])->get();
        return view('tiquetes.index', ['sales' => $sales]);
    }
}
