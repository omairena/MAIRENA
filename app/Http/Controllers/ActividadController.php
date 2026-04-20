<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actividad;
use App\Configuracion;
use App\Http\Requests\ActividadRequest;
use Auth;

use Illuminate\Support\Facades\DB;

class ActividadController extends Controller
{
    
   public function actualizarActividades()
{
    // Obtener el idconfigfact del usuario autenticado
    $idconfigfact = Auth::user()->idconfigfact; // O como esté definido en tu sistema

    // Realiza la actualización solo para los registros del usuario logueado
    $affectedRows = DB::table('codigo_actividad as ca')
        ->join('ciiu4 as c', 'ca.codigo_actividad', '=', 'c.ciiu3')
        ->where('ca.idconfigfact', $idconfigfact) // Filtrar por idconfigfact
        ->update([
            'ca.codigo_actividad' => DB::raw('c.ciiu4'),
            'ca.descripcion' => DB::raw('c.descripcion')
        ]);

    if ($affectedRows > 0) {
        return redirect()->back()->with('success', 'Actividades actualizadas correctamente.');
    } else {
        return redirect()->back()->with('error', 'No se encontraron actividades para actualizar.');
    }
}


        public function show($id)
    {
    	$actividades = Actividad::where([
    	   [ 'idconfigfact', $id],
    	  // ['estado', '0' ]
    	   ]
    	 
    	)->paginate(15);
    	return view('actividad.index', ['actividades' => $actividades, 'idconfig' => $id]);
    }

        public function create(Request $request)
    {
    	$datos = $request->all();
    	$config = Configuracion::find($datos['configuracion']);
    	return view('actividad.create', ['config' => $config]);
    }

    public function store(ActividadRequest $request, Actividad $model)
    {
		$datos = $request->all();
		$actividades = Actividad::where([
    	   [ 'idconfigfact', '=', Auth::user()->idconfigfact],
    	   ['estado', '0' ],
    	    ['principal', '1' ]
    	   ]
    	)->get();
    		 $activid = $actividades->count();
    	if($datos['principal']==1){
    	  	if($activid==0  ){
    	
		$actividad = Actividad::create(
                [
                    'idconfigfact' => $datos['idconfigfact'],
                    'descripcion' => $datos['hidden_descripcion'],
                    'codigo_actividad' => $datos['hidden_codigo'],
                    'principal' => $datos['principal'],
                ]
            );
        return redirect()->route('actividad.show',  $datos['idconfigfact'])->withStatus(__('Actividad creada Correctamente.'));
    	}else{
    	     return redirect()->route('actividad.show',  $datos['idconfigfact'])->withStatus(__('Ya existe una actividad configurada como actividad Principal.'));
    	}  
    	}else{
    	    	$actividad = Actividad::create(
                [
                    'idconfigfact' => $datos['idconfigfact'],
                    'descripcion' => $datos['hidden_descripcion'],
                    'codigo_actividad' => $datos['hidden_codigo'],
                    'principal' => $datos['principal'],
                ]
            );
        return redirect()->route('actividad.show',  $datos['idconfigfact'])->withStatus(__('Actividad creada Correctamente.'));
    	}
    	
    
    }

    	public function edit($id)
    {
        $actividad = Actividad::find($id);
        $config = Configuracion::find($actividad->idconfigfact);
        return view('actividad.edit', ['actividad' => $actividad, 'config' => $config]);
    }

    public function update(Request $request)
    {
        
       
    	$datos = $request->all();
  	$actividades = Actividad::where([
    	   [ 'idconfigfact', '=', Auth::user()->idconfigfact],
    	   ['estado', '0' ],
    	    ['principal', '1' ]
    	   ]
    	)->get();
    	 $activid = $actividades->count();
    //	dd($datos['principal']);
    	if($datos['principal']=='1'){
    	  //  dd($activid);
    	  	if($activid==0  ){
     $actualizar = Actividad::where('idcodigoactv', $datos['codigo_actividad_id'])->update(['principal' => $datos['principal'], 'descripcion' => $datos['descripcion'], 'codigo_actividad' => $datos['codigo_actividad'], 'estado' => $datos['estado']]);
      return redirect()->route('actividad.show',  $datos['idconfigfact'])->withStatus(__('Actividad Actualizada Correctamente.'));
    	  	}else{
    	  	   return redirect()->route('actividad.show',  $datos['idconfigfact'])->withStatus(__('Ya existe una actividad configurada como actividad Principal.'));  
    	  	}
    	}else{
    	      $actualizar = Actividad::where('idcodigoactv', $datos['codigo_actividad_id'])->update(['principal' => $datos['principal'], 'descripcion' => $datos['descripcion'], 'codigo_actividad' => $datos['codigo_actividad'], 'estado' => $datos['estado']]);
    	       return redirect()->route('actividad.show',  $datos['idconfigfact'])->withStatus(__('Actividad Actualizada Correctamente.'));
    	}
       
    }

}
