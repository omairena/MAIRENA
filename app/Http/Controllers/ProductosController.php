<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sales;
use App\Configuracion;
use App\Sales_item;
use App\Facelectron;
use App\Cliente;
use App\Productos;
use App\Forma_farmaceutica;
use App\Unidades_medidas;
use App\Http\Requests\ProductosRequest;
use App\Inventario;
use App\Inventario_item;
use Auth;
use App\Actividad;
use Validator;

class ProductosController extends Controller
{
        public function index(Productos $model)
    {
        $productos = Productos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    	return view('producto.index', ['productos' => $productos]);
    }

   		public function create()
    {
        $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    	//$unidades = Unidades_medidas::all();
    	$unidades = Unidades_medidas::where('tipo', '=', 0)->get();
    	$unidades_servicios = Unidades_medidas::where('tipo', '=', 1)->get();
        $actividades= Actividad::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        return view('producto.create', ['unidad_medida'  => $unidades,'unidad_medida_serv'  => $unidades_servicios, 'configuracion'  => $configuracion,'actividades'  => $actividades]);
    }
    
     public function autocomplete_forma_farmaceutica(Request $request)
    {
        $search = $request->get('term');
        $result = Forma_farmaceutica::where([
            ['forma', 'like', "%".$search."%"],
            
        ])->get();
        return response()->json($result);
    }
    public function buscar_forma_farmaceutica(Request $request)
    {
        $datos = $request->all();
        $cliente = Forma_farmaceutica::where([
            ['forma', 'like', "%".$datos['forma_farmaceutica']."%"],
           
        ])->get();
        return response()->json(['success'=> $cliente]);

    }

     	public function store(ProductosRequest $request, Productos $model)
    {
$idunidadmedida=0;
    	$datos = $request->all();
    	//dd($datos);
    	//$producto = $model->create($datos);
    	if($datos['tipo_producto'] == 1){
    	    $idunidadmedida=$datos['idunidadmedida'];
    	}else{
    	     //$idunidadmedida=$datos['idunidadmedidas'];
    	      $idunidadmedida=$datos['idunidadmedida'];
    	}
    	
// Asignación de tipo_producto de manera concisa
$tipo_producto = (isset($datos['codigo_cabys']) && in_array(substr($datos['codigo_cabys'], 0, 1), ['0', '1', '2', '3', '4'])) ? 1 : 2;
//dd($tipo_producto);
    	 $producto = Productos::create(
                    [
                        'idconfigfact' => $datos['idconfigfact'],
                        'idcodigoactv' => $datos['idcodigoactv'],
                        'nombre_producto' => $datos['nombre_producto'],
                        'codigo_producto' => $datos['codigo_producto'],
                        'codigo_cabys' => $datos['codigo_cabys'],
                        'idunidadmedida' => $idunidadmedida,
                        'tipo_producto' => $tipo_producto,
                        'impuesto_iva' => $datos['impuesto_iva'],
                        'porcentaje_imp' => $datos['porcentaje_imp'],
                        'costo' => $datos['costo'],
                        'utilidad_producto' => $datos['utilidad_producto'],
                        'precio_sin_imp' => $datos['precio_sin_imp'],
                        'precio_final' => $datos['precio_final'],
                        'cantidad_stock'  => $datos['cantidad_stock'],
                        'partida_arancelaria'  => $datos['partida_arancelaria'],
                        'fecha_creado'  => $datos['precio_final'],
                        'flotante'  => $datos['flotante'],
                        'exportable'  => $datos['exportable'],
                        'granel'  => 0,
                        'reg_med'  =>$datos['registro_sanitario'],
                        'forma'  => $datos['registro_medicamento'],
                        'cod_reg_med'  => $datos['forma'],
                        
                    ] );
        return redirect()->route('productos.index')->withStatus(__('Producto Creado Correctamente.'));
    }

        public function edit($id)
    {
        $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $unidades = Unidades_medidas::all();
        $producto = Productos::find($id);
        $actividades= Actividad::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        return view('producto.edit', ['producto'  => $producto, 'unidad_medida'  => $unidades, 'configuracion'  => $configuracion,'actividades'  => $actividades]);
    }

        public function update(Request $request, Productos $producto)
    {
       
        $producto->update($request->all());
        return redirect()->route('productos.index')->withStatus(__('Producto Actualizado Correctamente.'));
    }
    public function duplicar($id)
    {
        $configuracion = Configuracion::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        $unidades = Unidades_medidas::all();
        $producto = Productos::find($id);
        $actividades= Actividad::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
        return view('producto.duplicar', ['producto'  => $producto, 'unidad_medida'  => $unidades, 'configuracion'  => $configuracion,'actividades'  => $actividades]);
    }

        public function actualiarProducto(Request $request)
    {
        $input = $request->all();
        $producto = Productos::find($input['idproducto']);
        $precio_sin_imp = (($input['costo'] * $producto->utilidad_producto)/100)+$input['costo'];
        $precio_final = (($precio_sin_imp * $producto->porcentaje_imp)/100)+$precio_sin_imp;
        $actualizar = Productos::where('idproducto', $input['idproducto'])->update(['costo' => $input['costo'], 'precio_sin_imp' => $precio_sin_imp, 'precio_final' => $precio_final]);
        return response()->json(['success'=> $input]);
    }

        public function show($id)
    {
        $producto = Productos::find($id);
        $inventario_item = Inventario_item::where('idproducto',$id)->paginate(10);
        $configuracion = Configuracion::all();
        $unidades = Unidades_medidas::all();
        return view('producto.show', ['producto'  => $producto, 'unidad_medida'  => $unidades, 'configuracion'  => $configuracion, 'inventario_item' => $inventario_item]);
    }
//omairena 20-04-2022

 public function deleted ($id)
    {
        $producto = Productos::find($id);
        $sales_item = Sales_item::where('idproducto', $id)->get();
        //dd($sales_item);
        
        //$consulta_fac = Sales_item::where([
         //   ['idproducto', $producto],
        //    ['idconfigfact', '=', Auth::user()->idconfigfact]
       // ])->get();
        
        if (count($sales_item) > 0) {
        return redirect()->route('productos.index')->withStatus(__('Producto no puede eliminarse pues tiene ventas realizadas y no se puede eliminar su record.'));
        }else{
        $producto->delete();
        return redirect()->route('productos.index')->withStatus(__('Eliminado correctamente.'));
        }
    }
    
    ///
        public function cabys($id)
    {
        $producto = Productos::find($id);
        return view('producto.cabys', ['producto'  => $producto]);
    }
       public function savecabys(Request $request)
    {
        $datos = $request->all(); 
       
       if (array_key_exists('seleccion', $datos)) {
       
        $consulta = $this->Consulta_id_cabys($datos['seleccion'][0]);
         //dd($consulta);
        $producto_cabys = json_decode($consulta,true);
        $producto_intero = Productos::find($datos['producto_cabys']);
       
        if ($producto_intero->impuesto_iva != '99')   {
         $tipo_producto=$producto_intero->tipo_producto;   
            
            
if (strpos('01234', $producto_cabys[0]['codigo_cabys'][0]) !== false) {  
    $tipo_producto = 1;  
} else {  
    $tipo_producto = 2;  
}  


            $impuesto = ($producto_intero->precio_sin_imp * $producto_cabys[0]['impuesto_cabys'])/100;
            $precio_final = $producto_intero->precio_sin_imp + $impuesto;
            $actualizar = Productos::where('idproducto', $datos['producto_cabys'])->update(['codigo_cabys' => $producto_cabys[0]['codigo_cabys'], 'impuesto_iva' => $producto_cabys[0]['tarifa_cabys'], 'porcentaje_imp' => $producto_cabys[0]['impuesto_cabys'], 'precio_final' => $precio_final, 'tipo_producto' =>  $tipo_producto]);
            
        } else {
           $tipo_producto=0;   
            if (strpos('01234', $producto_cabys[0]['codigo_cabys'][0]) !== false) {  
    $tipo_producto = 1;  
} else {  
    $tipo_producto = 2;  
}  
dd($tipo_producto);
            $impuesto = ($producto_intero->precio_sin_imp * $producto_intero->porcentaje_imp)/100;
            $precio_final = $producto_intero->precio_sin_imp + $impuesto;
            
            
            $actualizar = Productos::where('idproducto', $datos['producto_cabys'])->update(['codigo_cabys' => $producto_cabys[0]['codigo_cabys'], 'precio_final' => $precio_final, 'tipo_producto' =>  $tipo_producto]);
            
            
        }
        
        return redirect()->route('productos.index')->withStatus(__('Producto cabys Actualizado correctamente.'));
    }else
     return redirect()->route('productos.index')->withStatus(__('Debes seleccionar un codigo CABYS para continuar.'));    
        }
       

        public function buscarcabys(Request $request)
    {
        $datos = $request->all();
        if ($datos['categoria'] != '99') {
            $categoria = $datos['categoria'];
        }else{
            $categoria = '';
        }
        if ($datos['tarifa'] != 0) {
            $tarifa = $datos['tarifa'];
            switch ($tarifa) {
                case '01':
                    $impuesto = '0';
                break;      
                case '02':
                    $impuesto = '1';
                break;  
                case '03':
                    $impuesto = '2';
                break;  
                case '04':
                    $impuesto = '4';
                break;  
                case '08':
                    $impuesto = '13';
                break;            
            }
        }else{
            $tarifa = '';
            $impuesto = '';
        }
        $arreglo_datos_cabys = [
            'categoria' => $categoria,
            'codigo' => $datos['codigo'],
            'descripcion' => $datos['descripcion'],
            'tarifa' => $tarifa,
            'impuesto' => $impuesto,
            'token' => "d0fba01f19553ecd7795d95f278d66f50b7b269c",
        ];
        $consulta = $this->Consulta_cabys($arreglo_datos_cabys);
        $producto = Productos::find($datos['producto_cabys']);
        $cabys = json_decode($consulta, true);
        return view('producto.filtrocabys', ['producto'  => $producto, 'cabys' => $cabys, 'datos_array' => $datos]);
    }

    //SECCION DE CONSULTA DEL CABYS API CREADA Y COLOCADA DEBE VERIFICAR LA RUTA
    function Consulta_cabys($array){
        $url = "https://cabys.snesteban.com/public/api/productos/masivo?categoria=".$array['categoria']."&codigo=".$array['codigo']."&descripcion=".urlencode($array['descripcion'])."&impuesto=".$array['impuesto']."&tarifa=".$array['tarifa']."&token=".$array['token']."";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "".$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
                "postman-token: 65becdcc-f3f2-8598-f38a-9f6d9adb803a"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $info = curl_getinfo($curl, CURLOPT_HTTPHEADER);
        curl_close($curl);
        if ($err) {
            $respuesta = "cURL Error #:" . $err . " Info: " . $info;
            return  $respuesta;
        } else {
                return $response;
        }
    }

    function Consulta_id_cabys($id){
        $url = "https://cabys.snesteban.com/public/api/productos/ver?idproducto=".$id."&token=d0fba01f19553ecd7795d95f278d66f50b7b269c";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "".$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
                "postman-token: 65becdcc-f3f2-8598-f38a-9f6d9adb803a"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $info = curl_getinfo($curl, CURLOPT_HTTPHEADER);
        curl_close($curl);
        if ($err) {
            $respuesta = "cURL Error #:" . $err . " Info: " . $info;
            return  $respuesta;
        } else {
                return $response;
        }
    }
}
