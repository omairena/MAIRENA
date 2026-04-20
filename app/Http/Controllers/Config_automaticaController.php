<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion_automatica;
use App\Cajas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class Config_automaticaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Configuracion_automatica $model)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver las configuraciones automaticas");
            return redirect()->route('facturar.index');
        }
        if (Auth::user()->super_admin === 1) {
            //$configuracion_autm = Configuracion_automatica::all();
            $configuracion_autm = DB::table('config_automatica')->select('config_automatica.*','configuracion.*')
                ->join('configuracion','config_automatica.idconfigfact', '=', 'configuracion.idconfigfact')
                ->join('users','configuracion.idconfigfact', '=', 'users.idconfigfact')
                ->get();
              //  dd($configuracion_autm);
        }else{
            $configuracion_autm = DB::table('config_automatica')->select('config_automatica.*','configuracion.*')
                ->join('configuracion','config_automatica.idconfigfact', '=', 'configuracion.idconfigfact')
                ->join('users','configuracion.idconfigfact', '=', 'users.idconfigfact')
                ->where([
                    ['users.id', '=', Auth::user()->id]
                ])->get();
        }
    	return view('config_automatica.index', ['configuracion_autm' => $configuracion_autm]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite crear las configuraciones automaticas");
            return redirect()->route('facturar.index');
        }
        $cajas = Cajas::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();

        return view('config_automatica.create', ['cajas' => $cajas]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Configuracion_automatica $model)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite guardar las configuraciones automaticas");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
    	$datos['idconfigfact'] = Auth::user()->idconfigfact;
    	$config_automatica = $model->create($datos);
        return redirect()->route('config_automatica.index')->withStatus(__('Configuración Automatica para Recepcion creada correctamente.'));

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
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite editar las configuraciones automaticas");
            return redirect()->route('facturar.index');
        }
        $configuracion_autm = Configuracion_automatica::find($id);
       
        $cajas = Cajas::where('idconfigfact', '=',$configuracion_autm->idconfigfact)->get();

        return view('config_automatica.edit', ['configuracion_autm' => $configuracion_autm, 'cajas' => $cajas]);
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
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite actualizar las configuraciones automaticas");
            return redirect()->route('facturar.index');
        }
        $datos = $request->except(['_token', '_method']);
        Configuracion_automatica::where('idconfigautomatica',$id)->update($datos);
        return redirect()->route('config_automatica.index')->withStatus(__('Configuración  Automatica para Recepcion actualizada correctamente.'));
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
}
