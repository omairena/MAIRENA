<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Consecutivos;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ConsecutivoController extends Controller
{
   	 	public function show($id)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite crear cajas");
            return redirect()->route('cajas.index');
        }
    	$consecutivos = Consecutivos::where('idcaja', $id)->paginate(15);
    	return view('consecutivos.index', ['consecutivos' => $consecutivos]);
    }
       	public function edit($id)
    {
    	$consecutivo = Consecutivos::where('idconsecutivo', $id)->get();
        return view('consecutivos.edit', ['consecutivo' => $consecutivo]);
    }

        public function update(Request $request, $id)
    {
    	$selection = Consecutivos::find($id);
		$selection->update($request->all());
        return redirect()->route('cajas.index')->withStatus(__('Consecutivo Actualizado Correctamente.'));
    }
}
