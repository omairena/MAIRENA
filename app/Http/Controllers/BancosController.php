<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Cliente;
use App\Productos;
use App\Cajas;
use App\Log_masivo;
use App\Bancos;
use App\Config_masivo;
use App\Items_masivo;
use DB;
use App\Cxcobrar;
use App\Tr_bancos;
use App\Mov_cxcobrar;
use App\Log_cxcobrar;
use App\Sales;
use App\Sales_item;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class BancosController extends Controller
{
       	public function index()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        
        $bancos = Bancos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        return view('bancos.index', ['bancos' => $bancos]);
    }
    public function jsonbancos(Request $request)
    {
        $datos = $request->all();
       
        $bancos = Bancos::create([
            'idconfigfact' => Auth::user()->idconfigfact,
            'cuenta' => $datos['cuenta_bancos'],
            'saldo' => 0,
            
        ]);
          
        $bancos = Bancos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        return view('bancos.index', ['bancos' => $bancos]);
    }
    public function deleted ($id)
    {
        $bancos = Bancos::find($id);
        $tr_banco = Tr_bancos::where('id_bancos', $id)->get();
     
        
        if (count($tr_banco) > 0) {
        return redirect()->route('bancos.index')->withStatus(__('Cuenta no puede eliminarse pues tiene transacciones realizadas y no se puede eliminar su record.'));
        }else{
        $bancos->delete();
        return redirect()->route('bancos.index')->withStatus(__('Eliminado correctamente.'));
        }
    }
   
    
  
    

}