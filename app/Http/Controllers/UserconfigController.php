<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Configuracion;
use App\User_config;
use DB;
use Auth;

class UserconfigController extends Controller
{
       	public function show($id)
    {
        
    	$user_config = User_config::where('idconfigfact', $id)->paginate(15);
    	 $user = DB::table('users')
                ->select('users.*')
               //  ->where('idconfigfact', '=', $user_config[0]->idconfigfact)->get();
                 ->where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
                 
    //	dd($user_config);
    	
    	return view('user_config.index', ['user_config' => $user_config, 'idconfig' => $id, 'user'=>$user]);
    }

        public function create(Request $request)
    {
    	$datos = $request->all();
    	$config = Configuracion::find($datos['configuracion']);
    	$usuariosd = User::where('idconfigfact', Auth::user()->idconfigfact)->get();
        
        
    	$usuarios = \DB::table('users')->select('users.*')
        ->whereNotExists( function ($query) use ($datos) {
        $query->select(DB::raw(1))
        ->from('user_config')
        ->whereRaw('user_config.idusuario = users.id')
        ->where('user_config.idconfigfact', '=', Auth::user()->idconfigfact);
        })->get();
    	return view('user_config.create', ['config' => $config, 'usuarios' => $usuarios, 'usuariosd' => $usuariosd]);
    }

     	public function store(Request $request, User_config $model)
    {
		$datos = $request->all();
		$config_user = User_config::create(
                [
                    'idconfigfact' => $datos['idconfigfact'],
                    'idusuario' => $datos['idusuario'],
                    'usa_pos' => $datos['usa_pos'],
                    'fecha_creado' => date('Y-m-d'),
                    'estatus' => 1
                ]
            );
        return redirect()->route('userconfig.show',  $datos['idconfigfact'])->withStatus(__('Configuracion creada Correctamente.'));
    }
    
    public function toggleStatus($idconfigfact)  
{  
    // Encuentra el registro correspondiente  
    $configUser = User_config::where('idconfigfact', $idconfigfact)->first();  

    if ($configUser) {  
        // Cambia el valor de usa_pos  
        $configUser->usa_pos = $configUser->usa_pos == 1 ? 0 : 1;  
        $configUser->save(); // Guarda los cambios  

        return redirect()->route('userconfig.show', $idconfigfact)->withStatus(__('Configuración actualizada correctamente.'));  
    }  

    return redirect()->route('userconfig.show', $idconfigfact)->withErrors(__('Configuración no encontrada.'));  
}

}
