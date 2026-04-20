<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\App_settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Mail\TerminosUsoMail; // Importa el Mailable necesario  
use Illuminate\Support\Facades\Mail; // Importa la clase Mail  
use App\Configuracion; 


class SettingappController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    public function edit_terms()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite editar los terminos y condiciones");
            return redirect()->route('facturar.index');
        }
        $terminos = App_settings::where('app_settings.name', '=', 'terms_conditions')->first();
        return view('terms.edit', ['terminos' => $terminos]);
    }

    public function update_terms(Request $request)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite actualizar los terminos y condiciones");
            return redirect()->route('facturar.index');
        }
        // Actualizo el terminos y condiciones
        DB::table('app_settings')->where('app_settings.idsettings', $request->idsettings)->update([
            'app_settings.value' => $request->value,
        ]);
        // Actualizo todas las configuracion para que tengan que confirmar los terminos y condiciones del sistema
        DB::table('configuracion')->where('configuracion.acepto_terminos', '=', 1)->update([
            'configuracion.acepto_terminos' => 0,
        ]);

        return redirect()->route('config.index')->withStatus(__('Terminos y condiciones actualizados correctamente.'));
    }

  public function aceptarTerminos(Request $request)  
{  
    $correo = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->first();
    // Actualizo todas las configuraciones para que tengan que confirmar los términos y condiciones del sistema  
    DB::table('configuracion')->where('idconfigfact', '=', Auth::user()->idconfigfact)->update([  
        'configuracion.acepto_terminos' => 1,  
        'configuracion.aceptado_ter' => Auth::user()->name,  
        'configuracion.fecha_ter' => now(), // Cambié date('y/m/d h:i:s') por now() de Laravel  
    ]);  

    // Obtener el texto de los términos de uso de la base de datos  
    $terminosTexto = DB::table('app_settings')->where('idsettings', '=', 1)->value('value');  

     
    // Enviar correo de confirmación de aceptación de términos  
    Mail::to($correo->email_emisor)  
    ->cc(['omairena@fesanesteban.com','contratos@snesteban.com']) // múltiples CC  
    ->send(new TerminosUsoMail($correo->nombre_emisor, $terminosTexto)); 

    return response()->json(['success'=> 'Actualizado correctamente']);  
}  
}
