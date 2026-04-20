<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Consecutivos;
use App\Cajas_user;
use App\Cajas;
use Illuminate\Support\Facades\DB;
use Input;
use App\Http\Requests\ConfiguracionRequest;
use App\Facelectron;
use Illuminate\Support\Facades\Hash;
use App\User;
use File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Artisan;
use App\App_settings;
class ConfigController extends Controller
{
       public function index(Configuracion $model)
    {



        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver configuraciones");
            return redirect()->route('facturar.index');
        }
        //
        if (Auth::user()->super_admin === 1) {



            if (Auth::user()->id === 6) {
                 $configuracion = Configuracion::all();


            }else{

                 $configuracion = DB::table('configuracion')->select('configuracion.*')
                ->join('users','configuracion.idconfigfact', '=', 'users.idconfigfact')
                ->where([
                    ['configuracion.gnl', '=', Auth::user()->id],
                    ['users.id', '!=', Auth::user()->id],
                   ['configuracion.status', '!=', 1]
                ])->get();

               // dd($configuracion);
            }
        }else{

            $configuracion = DB::table('configuracion')->select('configuracion.*')
                ->join('users','configuracion.idconfigfact', '=', 'users.idconfigfact')
                ->where([
                    ['users.id', '=', Auth::user()->id]
                ])->get();

        }
     // dd($configuracion);
    	return view('config.index', ['configuracion' => $configuracion]);
    }

       public function create()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite crear configuraciones");
            return redirect()->route('facturar.index');
        }
        return view('config.create');
    }

     	public function store(ConfiguracionRequest $request, Configuracion $model)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite guardar configuraciones");
            return redirect()->route('facturar.index');
        }
    	$datos = $request->all();
    	$cantones = DB::table('cantones')->where('idcanton', $datos['canton_emisor'])->get();
    	$distritos = DB::table('distritos')->where('iddistrito', $datos['distrito_emisor'])->get();
    	$datos['canton_emisor'] = $cantones[0]->codigo_canton;
    	$datos['distrito_emisor'] = $distritos[0]->codigo_distrito;
    	$datos['nombre_comercial'] = $datos['nombre_emisor'];
        $clave = Hash::make($request->get('password'));
        // prepare image
        $new_name = rand() . '.' . $request->file('ruta_certificado')->getClientOriginalExtension();
        $request->file('ruta_certificado')->move(public_path().'/certificados/', $new_name);

       $fechaactual = date('Y-m-d'); // 2016-12-29
       $nuevafecha = strtotime ('+1 year' , strtotime($fechaactual)); //Se a�0�9ade un a�0�9o mas
       $nuevafecha = date ('Y-m-d',$nuevafecha);

        // set image with the new name
        $datos['ruta_certificado'] = $new_name;
        $datos['barrio_emisor'] = $datos['barrio_emisor'];
        $datos['logo'] = 'logo.JPG';
        $datos['config_automatica'] = '1';
        $datos['gnl'] = Auth::user()->id;
        $datos['fecha_certificado'] = $datos['fecha_certificado'];
        $datos['fecha_plan'] = $nuevafecha;
        $datos['sin_impuesto_pos'] = 0;
        $datos['tipo_moneda_confi'] = 'CRC';
        //$datos['docs'] = $datos['docs'];
        //$datos['mail_not']= $datos['mail_not'];

    	$config = $model->create($datos);

        $usuario = User::create(
                [
                    'idconfigfact' => $config->idconfigfact,
                    'name' => $config->nombre_emisor,
                    'email' => $config->email_emisor,
                    'password' => $clave,
                    'es_admin' => 1,
                    'super_admin' => 0
                    
                ]
            );
        File::makeDirectory(public_path('/XML/'. $config->idconfigfact));

        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/DocReceptor"));

        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/DocReceptor/Envio"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/DocReceptor/Envio/email"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/DocReceptor/Envio/rechazados"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/DocReceptor/Envio/parcialAceptados"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/DocReceptor/Envio/aceptados"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/DocReceptor/Respuesta"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/DocReceptor/Respuesta/rechazados"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/DocReceptor/Respuesta/parcialAceptados"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/DocReceptor/Respuesta/aceptados"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/email"));

        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/FacturaCompra"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/FacturaCompra/Envio"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/FacturaCompra/Respuesta"));

        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/FacturaExportacion"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/FacturaExportacion/Envio"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/FacturaExportacion/Respuesta"));

        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/Facturas"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/Facturas/Envio"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/Facturas/Respuesta"));

        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/NotaCredito"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/NotaCredito/Envio"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/NotaCredito/Respuesta"));

        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/NotaDebito"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/NotaDebito/Envio"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/NotaDebito/Respuesta"));

        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/Tiquete"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/Tiquete/Envio"));
        File::makeDirectory(public_path('/XML/' . $config->idconfigfact . "/Tiquete/Respuesta"));

        File::makeDirectory(public_path('/receptor/'. $config->idconfigfact));
        File::makeDirectory(public_path('/receptor/' . $config->idconfigfact . "/carga"));

        File::makeDirectory(public_path('/PDF/'. $config->idconfigfact));

        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/DocReceptor"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/DocReceptor/Envio"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/DocReceptor/Envio/rechazados"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/DocReceptor/Envio/parcialAceptados"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/DocReceptor/Envio/aceptado"));

        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/FacturaCompra"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/FacturaCompra/Envio"));

        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/FacturaExportacion"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/FacturaExportacion/Envio"));

        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/Facturas"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/Facturas/Envio"));

        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/NotaCredito"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/NotaCredito/Envio"));

        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/NotaDebito"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/NotaDebito/Envio"));

        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/Tiquete"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/Tiquete/Envio"));

        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/Caja"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/IVA"));
        File::makeDirectory(public_path('/PDF/' . $config->idconfigfact . "/Pedidos"));

        return redirect()->route('config.index')->withStatus(__('Configuración creada correctamente.'));
    }

        public function edit($id)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite editar configuraciones");
            return redirect()->route('facturar.index');
        }
        $config = Configuracion::find($id);
        return view('config.edit', ['config' => $config]);
    }

       public function update(Request $request, $id)
{
    if (Auth::user()->es_vendedor == 1) {
        Session::flash('message', "Tu usuario no permite actualizar configuraciones");
        return redirect()->route('facturar.index');
    }

    // Obtén todos los datos excepto el token y el método de la solicitud
    $datos = $request->except(['_token', '_method']);
    $cantones = DB::table('cantones')->where('idcanton', $datos['canton_emisor'])->first();
    $distritos = DB::table('distritos')->where('iddistrito', $datos['distrito_emisor'])->first();

    if (!is_null($cantones)) {
        $datos['canton_emisor'] = $cantones->codigo_canton;
    }

    if (!is_null($distritos)) {
        $datos['distrito_emisor'] = $distritos->codigo_distrito;
    }

    $datos['nombre_comercial'] = $datos['nombre_emisor'];
    $datos['fecha_certificado'] = $datos['fecha_certificado'];

    // Manejo de la ruta del certificado
    if ($request->file('ruta_certificado')) {
        // Prepare imagen del certificado
        $new_name = rand() . '.' . $request->file('ruta_certificado')->getClientOriginalExtension();
        $request->file('ruta_certificado')->move(public_path('/certificados/'), $new_name);
        // Set image with the new name
        $datos['ruta_certificado'] = $new_name;
    }

    // Manejo de la imagen del logo
    if ($request->file('logo')) {
        // Eliminar la imagen antigua si existe
        $configuracion = Configuracion::find($id);
        if ($configuracion && $configuracion->logo) {
            $oldLogoPath = public_path('black/img/' . $configuracion->logo);
            if (file_exists($oldLogoPath)) {
                unlink($oldLogoPath); // Elimina el archivo antiguo del servidor
            }
        }

        // Cargar la nueva imagen
        $logoFile = $request->file('logo');
        $logoName = time() . '.' . $logoFile->getClientOriginalExtension(); // Generar un nombre único
        $logoFile->move(public_path('black/img/'), $logoName); // Mover a la carpeta

        // Actualizar el campo logo en los datos
      
        $datos['logo'] = $logoName;
    }
//dd($datos);
    // Actualiza la configuración
    Configuracion::where('idconfigfact', $id)->update($datos);

    return redirect()->route('config.index')->withStatus(__('Configuración actualizada correctamente.'));
}

        public function limpiar($id)
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite limpiar la data");
            return redirect()->route('facturar.index');
        }
        $borrado2 = DB::table('sales')->select('sales.*', 'sales_item.*')
        ->join('sales_item','sales.idsale','=','sales_item.idsales')
        ->where([
            ['sales.estatus_sale','=', '1'],
            ['sales.idconfigfact','=', Auth::user()->idconfigfact],
        ])->delete();
        $borrado = DB::select('DELETE sales.*, sales_item.* FROM sales JOIN sales_item ON sales.idsale = sales_item.idsales WHERE sales.estatus_sale = 1 and sales.idconfigfact = '.Auth::user()->idconfigfact);
        $borrado_sales = DB::select('DELETE sales.* FROM sales WHERE sales.estatus_sale = 1 and sales.idconfigfact = '.Auth::user()->idconfigfact);

        return redirect()->route('config.index')->withStatus(__('Base de Datos Limpiada correctamente.'));
    }

    public function recepcion()
    {
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite correr el comando de recepcion");
            return redirect()->route('facturar.index');
        }
        $init = Artisan::call('recepcion:start');
        $finish = Artisan::call('carga:start');
        return redirect()->route('config.index')->withStatus(__('Comando ejecutado correctamente.'));
    }

public function ingresar($id)
    {
   $terminos = DB::table('configuracion')->select('configuracion.*')
        ->join('users','configuracion.idconfigfact', '=', 'users.idconfigfact')
        ->where([
            ['users.id', '=', Auth::user()->id]
        ])->first();
   $contrato = App_settings::where('name', '=', 'terms_conditions')->first();
        $actualizar = user::where('id','=', Auth::user()->id)->update(['idconfigfact' => $id]);

       // $caja = Cajas_user::where('idusuario','=', Auth::user()->id)->update(['estado' => 0]);

 $consulta = DB::table('cajas')
        ->select('cajas.*')
        ->where('idconfigfact', '=',$id)
        ->where('ip_imp', '=', 1)
        ->first();

//$cajad = Cajas_user::where('idcaja','=', $consulta->idcaja)->update(['estado' => 1]);
 app('App\Http\Controllers\PeticionesController')->ajaxConsultar();
          return view('dashboard1', ['terminos' => $terminos, 'contrato' => $contrato]);
    }
public function onoff($id)
{
    // Buscando el usuario en la base de datos por 'idconfigfact' y asegur��ndose de que el id no sea 2
    $consulta = DB::table('users')
        ->select('users.*')
        ->where([
            ['idconfigfact', '=', $id],
            ['id', '!=', 2]
        ])
        ->first();

    // Comprobaci��n para ver si la consulta devolvi�� alg��n resultado
    if (!$consulta) {
        return redirect()->route('config.index')->withErrors(__('Usuario no encontrado o configuraci��n incorrecta.'));
    }

    // Para depuraci��n: muestra el estado actual del usuario
   // dd($consulta);

    if ($consulta->status === 0) {
        // Si el estado es 0, se actualiza a 1
        $actualizar = User::where('id', '=', $consulta->id)->update(['status' => 1]);
       $actualizarconf = Configuracion::where('idconfigfact', '=', $consulta->idconfigfact)
    ->update(['status' => 1, 'recepciona' => 1]);
    } else {
        // Si no, se actualiza a 0
        $actualizar = User::where('id', '=', $consulta->id)->update(['status' => 0]);
         $actualizarconf = Configuracion::where('idconfigfact', '=', $consulta->idconfigfact)->update(['status' => 0, 'recepciona' => 0]);
    }

    // Redirigir a la ruta con un mensaje de ��xito
    return redirect()->route('config.index')->withStatus(__('Actualizado Correctamente'));
}
}
