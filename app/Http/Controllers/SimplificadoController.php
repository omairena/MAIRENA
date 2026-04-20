<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sales;
use App\Sales_item;
use DB;
use Illuminate\Contracts\Container\Container;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SimplificadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-t');
        if(Auth::user()->config_u[0]->usa_op > 0){
            $es_op = 1;
        } else {
            $es_op = 0;
        }
        $sales = Sales::where([
            ['tipo_documento', '96'],
            ['idconfigfact', '=', Auth::user()->idconfigfact],
            ['fecha_creada', '>=', $fecha_desde],
            ['fecha_creada', '<=', $fecha_hasta],
            ['es_op', '=', $es_op]
        ])->orderBy('numero_documento', 'desc')->get();

        return view('regimen.index', ['sales' => $sales, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

        public function filtrarRegimen(Request $request)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
        if(Auth::user()->config_u[0]->usa_op > 0){

            if ($datos['seleccion_regimen'] > 1) {

                $sales = Sales::where([
                    ['tipo_documento', '=', '96'],
                    ['fecha_creada', '>=', Carbon::parse($datos['fecha_desde'])],
                    ['fecha_creada', '<=', Carbon::parse($datos['fecha_hasta'])],
                    ['idconfigfact', '=', Auth::user()->idconfigfact],
                    ['estatus_op', '=', $datos['estatus_op']],
                ])->get();
            } else {

                $sales = Sales::where([
                    ['tipo_documento', '=', '96'],
                    ['fecha_creada', '>=', $datos['fecha_desde']],
                    ['fecha_creada', '<=', $datos['fecha_hasta']],
                    ['idconfigfact', '=', Auth::user()->idconfigfact],
                    ['es_op', '=', $datos['seleccion_regimen']],
                    ['estatus_op', '=', $datos['estatus_op']],
                ])->get();
            }

        } else {

            $sales = Sales::where([
                ['tipo_documento', '=', '96'],
                ['fecha_creada', '>=', $datos['fecha_desde']],
                ['fecha_creada', '<=', $datos['fecha_hasta']],
                ['idconfigfact', '=', Auth::user()->idconfigfact],
            ])->get();
        }

        return view('regimen.index', ['sales' => $sales, 'fecha_desde' => $datos['fecha_desde'], 'fecha_hasta' => $datos['fecha_hasta']]);
    }
}
