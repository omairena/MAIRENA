<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Listprice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver las listas de precio");
            return redirect()->route('facturar.index');
        }

        $lista = Listprice::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        return view('lista.index', ['lista' => $lista]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite crear las listas de precio");
            return redirect()->route('facturar.index');
        }
        return view('lista.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Listprice $model)
    {

        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite guardar las listas de precio");
            return redirect()->route('facturar.index');
        }
        $datos = $request->all();
        $datos['idconfigfact'] = Auth::user()->idconfigfact;
    	$model->create($datos);
        return redirect()->route('list.index')->withStatus(__('Lista de precio creada correctamente.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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

            Session::flash('message', "Tu usuario no permite editar las listas de precio");
            return redirect()->route('facturar.index');
        }
        $lista = Listprice::find($id);
        return view('lista.edit', ['lista' => $lista]);

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

            Session::flash('message', "Tu usuario no permite actualizar las listas de precio");
            return redirect()->route('facturar.index');
        }
        $datos = $request->except(['_token', '_method']);
        Listprice::where('idlist',$id)->update($datos);
        return redirect()->route('list.index')->withStatus(__('Lista de precio actualizada correctamente.'));
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
