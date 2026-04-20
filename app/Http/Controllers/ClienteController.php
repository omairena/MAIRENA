<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Distritos;
use App\Cantones;
use App\Http\Requests\ClienteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Clasificaciones;
use App\ClasificacionProveedor;
use App\ListaClientes;
use App\Listprice;

class ClienteController extends Controller
{
    public function index(Cliente $model)
    {
        $clientes = Cliente::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    	return view('cliente.index', ['clientes' => $clientes]);
    }

    public function create()
    {
        $clasificaciones = Clasificaciones::where('estatus', '=', 1)->get();
        return view('cliente.create', ['clasificaciones' => $clasificaciones]);
    }

    public function store(ClienteRequest $request, Cliente $model)
    {
        $tipo_clasificacion = $request->tipo_clasificacion;
        $datos = $request->except(['tipo_clasificacion']);
        if ($request->tipo_id !== '05' && $request->tipo_id !== '06') {
            if($datos['canton'] == 0 || $datos['distrito'] == 0 || $datos['provincia'] == 0 ){
                if(isset($datos['tipo_cliente']) && $datos['tipo_cliente'] == 2){
                    return redirect()->back()->with('message', 'Debe seleccionar Provincia, Cantón y Distrito para Proveedor.');
                } else {
                    return redirect()->route('cliente.index')->withStatus(__('Falta seleccionar Provincia, Canton o Distrito.'));
                }
            }
            $cantonObj = Cantones::find($datos['canton']);
            $distritoObj = Distritos::find($datos['distrito']);
            $datos['canton']    = $cantonObj ? $cantonObj->codigo_canton : null;
            $datos['distrito']  = $distritoObj ? $distritoObj->codigo_distrito : null;
        }

    	$datos['nombre_contribuyente'] = $datos['nombre'];
    	$cliente = $model->create($datos);

        if ($datos['tipo_cliente'] == 2) {
            // Se agrega el insert a la nueva tabla de proveedores
            $codigo_actividad = $datos['codigo_actividad'];
            $razon_social     = $datos['razon_social'];
            $clasificacion    = Clasificaciones::where('clasificaciones.idclasifica', '=', $tipo_clasificacion)->first();
            ClasificacionProveedor::create([
                'idcliente'                 => $cliente->id,
                'codigo_actividad'          =>  $codigo_actividad,
                'razon_social'              => $razon_social,
                'tipo_clasificacion'        => $tipo_clasificacion,
                'descripcion_clasificacion' => $clasificacion->descripcion
            ]);
        }

        return redirect()->route('cliente.index')->withStatus(__('Cliente creado correctamente.'));
    }

        public function edit($id)
    {
        $cliente = Cliente::find($id);
        return view('cliente.edit', ['cliente' => $cliente]);
    }

        public function update(Request $request, Cliente  $cliente)
    {
        $datos                         = $request->except(['additional_email']);
        if ($request->tipo_id !== '05' && $request->tipo_id !== '06') {
            if($datos['canton'] == 0 || $datos['distrito'] == 0 || $datos['provincia'] == 0 ){
                if(isset($datos['tipo_cliente']) && $datos['tipo_cliente'] == 2){
                    return redirect()->back()->with('message', 'Debe seleccionar Provincia, Cantón y Distrito para Proveedor.');
                } else {
                    return redirect()->route('cliente.index')->withStatus(__('Falta seleccionar Provincia, Canton o Distrito.'));
                }
            }
            $cantonObj = Cantones::find($datos['canton']);
            $distritoObj = Distritos::find($datos['distrito']);
            $datos['canton']    = $cantonObj ? $cantonObj->codigo_canton : null;
            $datos['distrito']  = $distritoObj ? $distritoObj->codigo_distrito : null;
        }
        $datos['nombre_contribuyente'] = $datos['nombre'];
        $cliente->update($datos);
        return redirect()->route('cliente.index')->withStatus(__('Cliente Actualizado Correctamente.'));

    }

    public function mostrarClasificacion($id)
    {
        $clasificaciones_proveedor = ClasificacionProveedor::where('clasificacion_proveedor.idcliente', '=', $id)->get();
        $cliente                   = Cliente::find($id);
        $clasificaciones           = Clasificaciones::where('estatus', '=', 1)->get();
        return view('cliente.clasifica', ['clasificacion' => $clasificaciones_proveedor, 'cliente' => $cliente, 'clasificaciones' => $clasificaciones]);
    }

    public function addClasificacion(Request $request)
    {
        $datos = $request->all();

        $cliente = Cliente::where('idconfigfact', '=', Auth::user()->idconfigfact)
        ->where('num_id', '=', $datos['num_id'])
        ->first();

        $qry = DB::table('clasificacion_proveedor')
        ->select('clasificacion_proveedor.*')
        ->where('clasificacion_proveedor.idcliente', '=', $cliente->idcliente)
        ->where('clasificacion_proveedor.codigo_actividad', '=', $datos['codigo_actividad'])
        ->count();

        if ($qry > 0) {
            return response()->json(['success'=> 0]);
        }
        $qry2 = DB::table('clasificaciones')
        ->select('clasificaciones.*')
        ->where('clasificaciones.idclasifica', '=', $datos['tipo_clasificacion'])
        ->first();
        $data = array(
            'idcliente' => $cliente->idcliente,
            'codigo_actividad' =>  $datos['codigo_actividad'],
            'razon_social' => $datos['razon_social'],
            'tipo_clasificacion' => $datos['tipo_clasificacion'],
            'descripcion_clasificacion' => $qry2->descripcion
        );
        $id = DB::table('clasificacion_proveedor')->insertGetId($data);
        return response()->json(['success'=> $id]);
    }

        public function updateClasificacion(Request $request)
    {
        $datos = $request->all();

        $qry2 = DB::table('clasificaciones')
        ->select('clasificaciones.*')
        ->where('clasificaciones.idclasifica', '=', $datos['tipo_clasificacion'])
        ->first();
        // Actualizar
        if ($datos['por_defecto'] > 0) {
            $consulta = DB::table('clasificacion_proveedor')
            ->select('clasificacion_proveedor.idcliente')
            ->where('clasificacion_proveedor.idclasificacion', $datos['idclasificacion'])
            ->first();
            $array2 = [
                'clasificacion_proveedor.por_defecto' => 0
            ];
            $update2 =  DB::table('clasificacion_proveedor')
            ->where('clasificacion_proveedor.idcliente', $consulta->idcliente)
            ->update($array2);
        }
        $array = [
            'clasificacion_proveedor.tipo_clasificacion' => $datos['tipo_clasificacion'],
            'clasificacion_proveedor.por_defecto' => $datos['por_defecto'],
            'clasificacion_proveedor.descripcion_clasificacion' => $qry2->descripcion,
        ];
        $update =  DB::table('clasificacion_proveedor')
        ->where('clasificacion_proveedor.idclasificacion', $datos['idclasificacion'])
        ->update($array);
        return response()->json(['success'=> 1]);
    }
        public function deleteClasificacion(Request $request)
    {
        $datos = $request->all();
        DB::table('clasificacion_proveedor')->where('idclasificacion', $datos['idclasificacion'])->delete();
        return response()->json(['success'=> $datos]);
    }

    public function actualiarEmailAdicional(Request $request)
    {
        $datos = $request->all();
        $cliente = Cliente::find($datos['idcliente']);
        if (is_null($cliente->additional_email)) {

            $data = $datos['email'];
        } else {

            $data = $cliente->additional_email.",".$datos['email'];
        }
        $array = [
            'clientes.additional_email' => $data,
        ];
        $update =  DB::table('clientes')
        ->where('clientes.idcliente', $datos['idcliente'])
        ->update($array);
        return response()->json(['success'=> $datos]);
    }

    public function deleteEmailAdicional(Request $request)
    {
        $datos = $request->all();
        $cliente = Cliente::find($datos['idcliente']);
        $emails = explode(',', $cliente->additional_email);
        $data = '';
        foreach ($emails as $email) {
            if ($email != $datos['email']) {

                if (empty($data)) {

                    $data = $email.",";
                } else {

                    $data = $data.''.$email.",";
                }
            } else {

                continue;
            }
        }
        $data = substr($data, 0, -1);
        $array = [
            'clientes.additional_email' => $data,
        ];
        $update =  DB::table('clientes')
        ->where('clientes.idcliente', $datos['idcliente'])
        ->update($array);
        return response()->json(['success'=> $data]);

    }

    public function mostrarListascli(ListaClientes $model, $id)
    {
        $lista_clientes = DB::table('clientes_list_price')
        ->LeftJoin('list_price', 'clientes_list_price.idlist', '=', 'list_price.idlist')
        ->select('list_price.*')
        ->where('clientes_list_price.idcliente', '=', $id)
        ->get();
    	return view('listaclientes.index', ['lista' => $lista_clientes, 'idcliente' => $id]);
    }

    public function crearListascli($id)
    {
        $lista = Listprice::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $cliente = Cliente::find($id);

    	return view('listaclientes.create', ['cliente' => $cliente, 'lista' => $lista]);
    }

    public function storeListascli(Request $request, ListaClientes $model)
    {
        $datos = $request->except(['nombre_cliente']);
        $lista_clientes = ListaClientes::where('idcliente', '=', $datos['idcliente'])->where('idlist', '=', $datos['idlist'])->count();

        if($lista_clientes > 0){

            return redirect()->route('cliente.listacli', $datos['idcliente'])->withStatus(__('Lista de precio ya existe registrada para este cliente.'));

        } else {

            if($datos['por_defecto'] > 0){

                ListaClientes::where('idcliente', $datos['idcliente'])->update(['por_defecto' => 0]);
                $model->create($datos);
            }
        }
        return redirect()->route('cliente.listacli', $datos['idcliente'])->withStatus(__('Lista de precio guardada correctamente.'));
    }
}
