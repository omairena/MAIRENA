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
use App\Sales;
use File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Artisan;
use App\App_settings;
use App\Actividad;
use App\User_config;
use App\Configuracion_automatica;
class ConfigController extends Controller
{
    private function buildUniqueNumericCertificateName($extension)
    {
        $extension = ltrim((string) $extension, '.');

        do {
            // Indicador numerico unico basado en timestamp + aleatorio.
            $indicator = (string) ((int) round(microtime(true) * 1000)) . random_int(100, 999);
            $fileName = $indicator . '.' . $extension;
        } while (file_exists(public_path('/certificados/' . $fileName)));

        return $fileName;
    }

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



public function storeFull(Request $request)
{

    $request->validate([
        'ruta_certificado' => 'required|file|max:5120'
    ]);

    DB::beginTransaction();

    try {

        $datos = $request->all();

        // Convertir canton y distrito a código
        $canton = DB::table('cantones')->where('idcanton', $datos['canton_emisor'])->first();
        $distrito = DB::table('distritos')->where('iddistrito', $datos['distrito_emisor'])->first();
        if ($canton) $datos['canton_emisor'] = $canton->codigo_canton;
        if ($distrito) $datos['distrito_emisor'] = $distrito->codigo_distrito;

        // Manejar subida del certificado
        if ($request->hasFile('ruta_certificado')) {
            if (!$request->file('ruta_certificado')->isValid()) {
                throw new \Exception('El certificado no se pudo cargar correctamente. Intente nuevamente.');
            }
            $new_name = $this->buildUniqueNumericCertificateName($request->file('ruta_certificado')->getClientOriginalExtension());
            $request->file('ruta_certificado')->move(public_path('/certificados/'), $new_name);
            $datos['ruta_certificado'] = $new_name;
        } else {
            throw new \Exception('Debe seleccionar un certificado digital para continuar.');
        }

        // Campos derivados
        $datos['nombre_comercial']  = $datos['nombre_emisor'];
        $datos['logo']              = 'logo.JPG';
        $datos['config_automatica'] = '1';
        $datos['gnl']               = Auth::user()->id;
        $datos['sin_impuesto_pos']  = 0;
        $datos['tipo_moneda_confi'] = 'CRC';
        $fechaactual = date('Y-m-d');
        $datos['fecha_plan'] = date('Y-m-d', strtotime('+1 year', strtotime($fechaactual)));

        $config = Configuracion::create($datos);

        // Forzar campos derivados que pueden no estar en $fillable del modelo
        Configuracion::where('idconfigfact', $config->idconfigfact)->update([
            'nombre_comercial'  => $datos['nombre_emisor'],
            'logo'              => 'logo.JPG',
            'config_automatica' => '1',
            'gnl'               => Auth::user()->id,
            'sin_impuesto_pos'  => 0,
            'tipo_moneda_confi' => 'CRC',
            'fecha_plan'        => $datos['fecha_plan'],
        ]);
        $config->refresh();

        $caja = Cajas::create([
            'idconfigfact' => $config->idconfigfact,
            'nombre_caja' => $request->input('nombre_caja'),
            'codigo_unico' => $request->input('codigo_unico'),
        ]);

        // Crear consecutivos de caja como en CajasController@store
        for ($i = 1; $i < 10; $i++) {
            Consecutivos::create([
                'idcaja' => $caja->idcaja,
                'tipo_documento' => '0' . $i,
                'numero_documento' => 1,
                'doc_desde' => 1,
                'doc_hasta' => 1000,
                'tipo_compra' => 1,
            ]);
        }

        Consecutivos::create([
            'idcaja' => $caja->idcaja,
            'tipo_documento' => '99',
            'numero_documento' => 1,
            'doc_desde' => 1,
            'doc_hasta' => 1000,
            'tipo_compra' => 1,
        ]);

        Consecutivos::create([
            'idcaja' => $caja->idcaja,
            'tipo_documento' => '98',
            'numero_documento' => 1,
            'doc_desde' => 1,
            'doc_hasta' => 1000,
            'tipo_compra' => 1,
        ]);

        Consecutivos::create([
            'idcaja' => $caja->idcaja,
            'tipo_documento' => '97',
            'numero_documento' => 1,
            'doc_desde' => 1,
            'doc_hasta' => 1000,
            'tipo_compra' => 1,
        ]);

        if ((int) $config->es_simplificado === 1) {
            Consecutivos::create([
                'idcaja' => $caja->idcaja,
                'tipo_documento' => '96',
                'numero_documento' => 1,
                'doc_desde' => 1,
                'doc_hasta' => 1000,
                'tipo_compra' => 1,
            ]);

            Consecutivos::create([
                'idcaja' => $caja->idcaja,
                'tipo_documento' => '95',
                'numero_documento' => 1,
                'doc_desde' => 1,
                'doc_hasta' => 1000,
                'tipo_compra' => 1,
            ]);
        }

        // Abrir caja con el usuario gnl (quien crea la cuenta)
        DB::table('caja_usuario')->insert([
            'idusuario' => Auth::user()->id,
            'idcaja'    => $caja->idcaja,
            'estado'    => 1,
        ]);
        Cajas::where('idcaja', $caja->idcaja)->update([
            'estatus'        => 1,
            'fecha_apertura' => date('Y-m-d'),
        ]);

        // Crear actividad como en ActividadController@store
        $principal = (int) $request->input('principal', 1);
        $codigoActividad = (string) $request->input('hidden_codigo', $request->input('codigo_actividad', '000000'));
        $descripcionActividad = (string) $request->input('hidden_descripcion', $request->input('descripcion_actividad', $config->nombre_emisor));

        // En flujo de creacion completa siempre se crea una actividad nueva.
        $actividad = Actividad::create([
            'idconfigfact' => $config->idconfigfact,
            'descripcion' => $descripcionActividad,
            'codigo_actividad' => $codigoActividad,
            'principal' => $principal,
        ]);
        // Guardar configuracion automatica de recepcion en el flujo completo
        $datos_config_automatica = $request->all();
        $datos_config_automatica['idconfigfact'] = $config->idconfigfact;
        $datos_config_automatica['idcaja'] = $caja->idcaja;
        $datos_config_automatica['idcodigoactv'] = $actividad->idcodigoactv;
        $estatus_automatica = (string) $request->input('estatus', 'aprobado');
        if (!in_array($estatus_automatica, ['aprobado', 'rechazado', 'pendiente'], true)) {
            $estatus_automatica = 'aprobado';
        }
        $datos_config_automatica['estatus'] = $estatus_automatica;
        Configuracion_automatica::create($datos_config_automatica);

        // Insertar registro de pago
        try {
            DB::table('pago')->insert([
                'nombre' => 'NUEVO USUARIO ' . $config->nombre_emisor,
                'fecha'  => date('Y-m-d'),
                'docs'   => isset($datos['docs']) ? intval($datos['docs']) : 0,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error insertando en pago: ' . $e->getMessage());
        }

        // Crear usuario administrador
        $clave = Hash::make($request->input('password'));
        $usuario = User::create([
            'idconfigfact'              => $config->idconfigfact,
            'name'                      => $config->nombre_emisor,
            'email'                     => $config->email_emisor,
            'password'                  => $clave,
            'es_admin'                  => 1,
            'super_admin'               => 0,
        ]);

        // Llenar session_active_config_id que puede no estar en $fillable
        User::where('id', $usuario->id)->update([
            'session_active_config_id' => $config->idconfigfact,
        ]);
        $usuario->refresh();

        // Crear relacion usuario-config
        User_config::create([
            'idconfigfact'  => $config->idconfigfact,
            'idusuario'     => $usuario->id,
            'usa_pos'       => 1,
            'fecha_creado'  => date('Y-m-d'),
            'estatus'       => 1,
        ]);

        // Crear directorios XML y PDF
        $id = $config->idconfigfact;
        File::makeDirectory(public_path('/XML/' . $id));
        File::makeDirectory(public_path('/XML/' . $id . '/DocReceptor'));
        File::makeDirectory(public_path('/XML/' . $id . '/DocReceptor/Envio'));
        File::makeDirectory(public_path('/XML/' . $id . '/DocReceptor/Envio/email'));
        File::makeDirectory(public_path('/XML/' . $id . '/DocReceptor/Envio/rechazados'));
        File::makeDirectory(public_path('/XML/' . $id . '/DocReceptor/Envio/parcialAceptados'));
        File::makeDirectory(public_path('/XML/' . $id . '/DocReceptor/Envio/aceptados'));
        File::makeDirectory(public_path('/XML/' . $id . '/DocReceptor/Respuesta'));
        File::makeDirectory(public_path('/XML/' . $id . '/DocReceptor/Respuesta/rechazados'));
        File::makeDirectory(public_path('/XML/' . $id . '/DocReceptor/Respuesta/parcialAceptados'));
        File::makeDirectory(public_path('/XML/' . $id . '/DocReceptor/Respuesta/aceptados'));
        File::makeDirectory(public_path('/XML/' . $id . '/email'));
        File::makeDirectory(public_path('/XML/' . $id . '/FacturaCompra'));
        File::makeDirectory(public_path('/XML/' . $id . '/FacturaCompra/Envio'));
        File::makeDirectory(public_path('/XML/' . $id . '/FacturaCompra/Respuesta'));
        File::makeDirectory(public_path('/XML/' . $id . '/FacturaExportacion'));
        File::makeDirectory(public_path('/XML/' . $id . '/FacturaExportacion/Envio'));
        File::makeDirectory(public_path('/XML/' . $id . '/FacturaExportacion/Respuesta'));
        File::makeDirectory(public_path('/XML/' . $id . '/Facturas'));
        File::makeDirectory(public_path('/XML/' . $id . '/Facturas/Envio'));
        File::makeDirectory(public_path('/XML/' . $id . '/Facturas/Respuesta'));
        File::makeDirectory(public_path('/XML/' . $id . '/NotaCredito'));
        File::makeDirectory(public_path('/XML/' . $id . '/NotaCredito/Envio'));
        File::makeDirectory(public_path('/XML/' . $id . '/NotaCredito/Respuesta'));
        File::makeDirectory(public_path('/XML/' . $id . '/NotaDebito'));
        File::makeDirectory(public_path('/XML/' . $id . '/NotaDebito/Envio'));
        File::makeDirectory(public_path('/XML/' . $id . '/NotaDebito/Respuesta'));
        File::makeDirectory(public_path('/XML/' . $id . '/Tiquete'));
        File::makeDirectory(public_path('/XML/' . $id . '/Tiquete/Envio'));
        File::makeDirectory(public_path('/XML/' . $id . '/Tiquete/Respuesta'));
        File::makeDirectory(public_path('/receptor/' . $id));
        File::makeDirectory(public_path('/receptor/' . $id . '/carga'));
        File::makeDirectory(public_path('/PDF/' . $id));
        File::makeDirectory(public_path('/PDF/' . $id . '/DocReceptor'));
        File::makeDirectory(public_path('/PDF/' . $id . '/DocReceptor/Envio'));
        File::makeDirectory(public_path('/PDF/' . $id . '/DocReceptor/Envio/rechazados'));
        File::makeDirectory(public_path('/PDF/' . $id . '/DocReceptor/Envio/parcialAceptados'));
        File::makeDirectory(public_path('/PDF/' . $id . '/DocReceptor/Envio/aceptado'));
        File::makeDirectory(public_path('/PDF/' . $id . '/FacturaCompra'));
        File::makeDirectory(public_path('/PDF/' . $id . '/FacturaCompra/Envio'));
        File::makeDirectory(public_path('/PDF/' . $id . '/FacturaExportacion'));
        File::makeDirectory(public_path('/PDF/' . $id . '/FacturaExportacion/Envio'));
        File::makeDirectory(public_path('/PDF/' . $id . '/Facturas'));
        File::makeDirectory(public_path('/PDF/' . $id . '/Facturas/Envio'));
        File::makeDirectory(public_path('/PDF/' . $id . '/NotaCredito'));
        File::makeDirectory(public_path('/PDF/' . $id . '/NotaCredito/Envio'));
        File::makeDirectory(public_path('/PDF/' . $id . '/NotaDebito'));
        File::makeDirectory(public_path('/PDF/' . $id . '/NotaDebito/Envio'));
        File::makeDirectory(public_path('/PDF/' . $id . '/Tiquete'));
        File::makeDirectory(public_path('/PDF/' . $id . '/Tiquete/Envio'));
        File::makeDirectory(public_path('/PDF/' . $id . '/Caja'));
        File::makeDirectory(public_path('/PDF/' . $id . '/IVA'));
        File::makeDirectory(public_path('/PDF/' . $id . '/Pedidos'));

        // Marcar aceptación de términos para la nueva configuración creada.
        Configuracion::where('idconfigfact', $config->idconfigfact)->update([
            'acepto_terminos' => 1,
            'aceptado_ter' => $usuario->name,
            'fecha_ter' => now(),
        ]);

        $terminosTexto = DB::table('app_settings')->where('idsettings', '=', 1)->value('value');
        $correoTerminos = Configuracion::where('idconfigfact', '=', $config->idconfigfact)->first();

        DB::commit();

        // Enviar correo de confirmación después del commit para no afectar el alta.
        try {
            if ($correoTerminos && !empty($correoTerminos->email_emisor) && class_exists(\App\Mail\TerminosUsoMail::class)) {
                Mail::to($correoTerminos->email_emisor)
                    ->cc(['omairena@fesanesteban.com', 'servicioscontables@pchconta.com'])
                    ->send(new \App\Mail\TerminosUsoMail($correoTerminos->nombre_emisor, $terminosTexto));
            }
        } catch (\Exception $mailException) {
            \Log::error('Error enviando correo de terminos en storeFull: ' . $mailException->getMessage());
        }

        return redirect()->route('config.index')
            ->with('success', 'Configuración completa creada');

    } catch (\Exception $e) {

        DB::rollBack();
        return back()->withErrors($e->getMessage());
    }
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
        $new_name = $this->buildUniqueNumericCertificateName($request->file('ruta_certificado')->getClientOriginalExtension());
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
        $datos['docs'] = $datos['docs'];
        $datos['mail_not']= $datos['mail_not'];

    	$config = $model->create($datos);
try {
    $fecha_actual = date('Y-m-d');
    $docs_insert = isset($datos['docs']) ? intval($datos['docs']) : 0;
    $nombre_pago = 'NUEVO USUARIO ' . $config->nombre_emisor;

    DB::table('pago')->insert([
        'nombre' => $nombre_pago,
        'fecha'  => $fecha_actual,
        'docs'   => $docs_insert
    ]);
} catch (\Exception $e) {
    // opcional: loguear error sin interrumpir el flujo
    \Log::error('Error insertando en pago: '.$e->getMessage());
}
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
        $new_name = $this->buildUniqueNumericCertificateName($request->file('ruta_certificado')->getClientOriginalExtension());
        $request->file('ruta_certificado')->move(public_path('/certificados/'), $new_name);
        // Set image with the new name
        $datos['ruta_certificado'] = $new_name;
    } else {
        unset($datos['ruta_certificado']);
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

public function ingresddr($id)
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
  public function ingresar($id, Request $request)
{
    $terminos = DB::table('configuracion')->select('configuracion.*')
        ->join('users','configuracion.idconfigfact', '=', 'users.idconfigfact')
        ->where([
            ['users.id', '=', Auth::user()->id]
        ])->first();

    $contrato = App_settings::where('name', '=', 'terms_conditions')->first();
     $user = Auth::user();

    // 0) Verificar si la configuración actual del usuario tiene ventas activas (estatus_sale = 1)
    $configActual = $user->idconfigfact; // asume que es la configuración actual del usuario
    $hayVentasActivas = Sales::where('idconfigfact', $configActual)
                        ->where('estatus_sale', 1)
                        ->exists();

    if ($hayVentasActivas) {
        return back()->with('error', 'No se puede ingresar a la configuración porque ya existe una venta activa en la configuración actual.');
    }

    // Si ya hay una sesión activa en otra configuración, mostramos confirmación
    if ($user->session_active_config_id && $user->session_active_config_id != $id) {
        if ($request->has('confirm_change') && $request->get('confirm_change') == '1') {
            DB::beginTransaction();
            try {
                $user->update([
                    'idconfigfact' => $id,
                    'session_active_config_id' => $id,
                    'session_last_seen_at' => now(),
                ]);
                app('App\Http\Controllers\PeticionesController')->ajaxConsultar();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Ocurrió un error al cambiar la configuración.');
            }

            return view('dashboard1', [
                'terminos' => $terminos,
                'contrato' => $contrato
            ]);
        }

        return back()->with('confirm_change',
            [
                'message' => 'Ya existe una sesión activa en otra configuración. ¿Deseas cambiar a la configuración solicitada?',
                'target_id' => $id
            ]
        );
    }

    // Sin bloqueo por ventas y sin conflicto de sesión, proceder normalmente
    DB::beginTransaction();
    try {
        $user->update([
            'idconfigfact' => $id,
            'session_active_config_id' => $id,
            'session_last_seen_at' => now(),
        ]);

        app('App\Http\Controllers\PeticionesController')->ajaxConsultar();

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Ocurrió un error al cambiar la configuración.');
    }

    return view('dashboard1', [
        'terminos' => $terminos,
        'contrato' => $contrato
    ]);
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
