<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sales;
use App\Configuracion;
use App\Sales_item;
use App\Facelectron;
use App\Cliente;
use App\Productos;
use App\Unidades_medidas;
use App\Http\Requests\ProductosRequest;
use App\Inventario;
use App\Inventario_item;
use App\Cxpagar;
use App\Mov_cxpagar;
use App\Log_cxpagar;
use Auth;
use App\Http\Requests\InventarioRequest;
use Validator;
use Illuminate\Support\Arr;

class InventarioController extends Controller
{
       	public function index(Inventario $model)
    {
        $inventario = Inventario::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    	return view('inventario.index', ['inventario' => $inventario]);
    }
 public function eliminarLineainv(Request $request)
    {
        $input = $request->all();
        $sales_item = inventario_item::find($input['idsalesitem']);
       
           $sales_item->delete();
        

        return response()->json(['success'=> $input]);

    }
    	public function create()
    {
        $proveedores = Cliente::where([
           // ['tipo_cliente', 2],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        $productos = Productos::where([
            ['tipo_producto', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        return view('inventario.create', ['proveedores'  => $proveedores, 'productos'  => $productos]);
    }

    	public function show($id)
    {
         $inventario_item = Inventario_item::where('idinventario', $id)->paginate(10);
        $inventario = Inventario::find($id);
        $proveedores = Cliente::get();
         $productos = Productos::where([
            ['tipo_producto', 1],
            ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        return view('inventario.show', ['inventario_item' => $inventario_item, 'inventario' => $inventario, 'proveedores'  => $proveedores, 'productos'  => $productos]);

    }

    public function store(Request $request)
    {
    	$datos = $request->all();
    	if($datos['tipo_movimiento'] === '2'){
    	     $validator = Validator::make($datos, [
                
                'observaciones' => 'required', 'min:2', 'max:200',
                
            ]);
             //Validacion si falla retorna el error
            if ($validator->fails()) {
                return back()->withErrors($validator);
            }
    	}else{
    	    
        if ($datos['condicion_movimiento'] === '2') {
            $validator = Validator::make($datos, [
                'plazo_credito' => 'required|min:1|max:60',
                'tipo_movimiento' => 'required',
                'condicion_movimiento' => 'required',
                'idcliente' => 'required',
                'observaciones' => 'required', 'min:2', 'max:200',
                'num_documento' => 'required',
            ]);
             //Validacion si falla retorna el error
            if ($validator->fails()) {
                return back()->withErrors($validator);
            }
        }else{
            $validator = Validator::make($datos, [
                'tipo_movimiento' => 'required',
                'condicion_movimiento' => 'required',
                'idcliente' => 'required',
                'observaciones' => 'required|min:2|max:200',
                'num_documento' => 'required',
            ]);
             //Validacion si falla retorna el error
            if ($validator->fails()) {
                return back()->withErrors($validator);
            }
        }
    	}
    	
    //	dd($datos);
    	$inventario = Inventario::create(
            [
                'idconfigfact' => Auth::user()->idconfigfact,
               	'tipo_movimiento' => $datos['tipo_movimiento'],
                'idcliente' => $datos['idcliente'],
                'fecha' => date('Y-m-d'),
                'observaciones' => $datos['observaciones'],
                'num_documento' => $datos['num_documento'],
                'condicion_movimiento' => $datos['condicion_movimiento'],
                'plazo_credito' => $datos['plazo_credito'],
                'total_inventario' => '0.00000',
                'estatus_movimiento' => 1
            ]
        );
        if(strstr($datos['sales_item'],',') ){
        	$valores = explode(',', $datos['sales_item']);
        	for ($i=0; $i < count($valores); $i++) { 
        		$producto = Productos::find($valores[$i]);
        		$inventario_item = Inventario_item::create(
            		[
               			'idinventario' => $inventario->idinventario,
                		'idproducto' => $producto->idproducto,
                		'fecha' => date('Y-m-d'),
                		'cantidad_inventario' => 0
            		]
        		);
        	}
        	return redirect()->route('inventario.edit', $inventario->idinventario);
		}else{
				$producto = Productos::find($datos['sales_item']);
        		$inventario_item = Inventario_item::create(
            		[
               			'idinventario' => $inventario->idinventario,
                		'idproducto' => $producto->idproducto,
                		'fecha' => date('Y-m-d'),
                		'cantidad_inventario' => 0
            		]
        		);
        		return redirect()->route('inventario.edit', $inventario->idinventario);
		}
    }

        public function edit($id)
    {
        $inventario_item = Inventario_item::where('idinventario', $id)->get();
        $inventario = Inventario::find($id);
        $proveedores = Cliente::get();
      $productos = Productos::where([['tipo_producto', 1],
         ['idconfigfact', '=', Auth::user()->idconfigfact]
        ])->get();
        return view('inventario.edit', ['inventario_item' => $inventario_item, 'inventario' => $inventario, 'proveedores'  => $proveedores, 'productos'  => $productos]);
    }

        public function agregarLinea(Request $request)
    {
        $datos = $request->all();
      //  dd($datos['sales_item']);
       // $inventario_item = Inventario_item::create(
         //   [
         //      	'idinventario' => $datos['idinventario'],
         //       'idproducto' => $datos['datos'],
         //       'fecha' => date('Y-m-d'),
         //       'cantidad_inventario' => 0
         //   ]
       // );
       
       $valores = explode(',', $datos['datos']);
        	for ($i=0; $i < count($valores); $i++) { 
        		$producto = Productos::find($valores[$i]);
        		$inventario_item = Inventario_item::create(
            		[
               			'idinventario' => $datos['idinventario'],
                		'idproducto' => $producto->idproducto,
                		'fecha' => date('Y-m-d'),
                		'cantidad_inventario' => 0
            		]
        		);
        	}
        	
        return response()->json(['success'=> $datos]);
    }

    	public function update(InventarioRequest $request, $id)
    {
        
     
        $tabla_erro=$request->validated();
         $datos= Arr::except($tabla_erro, ['_token', '_method', 'cantidad_inventario','sales_item','costo_producto']);
       // $datos = $tabla_erro->except(['_token', '_method', 'cantidad_inventario','sales_item','costo_producto']);
        if ($datos['condicion_movimiento'] === '2') {
            $validator = Validator::make($datos, [
                'observaciones' => 'required|min:1|max:200',
            ]);
             //Validacion si falla retorna el error
            if ($validator->fails()) {
                return back()->withErrors($validator);
            }
        }
        //dd($datos);
    	$inventario  = Inventario::where('idinventario',$id)->update($datos);
    	$inventario_item = Inventario_item::where('idinventario', $id)->get();
        $total_final = 0;
    	foreach ($inventario_item as $inv_itm) {
    		$producto = Productos::find($inv_itm->idproducto);
            $total = $producto->precio_final * $inv_itm->cantidad_inventario;
    		if ($datos['tipo_movimiento'] === '1') {
    			$cantidad_stock = $producto->cantidad_stock + $inv_itm->cantidad_inventario;
    		}else{
    			$cantidad_stock = $producto->cantidad_stock - $inv_itm->cantidad_inventario;
    		}
    		$actualizar = Productos::where('idproducto', $inv_itm->idproducto)->update(['cantidad_stock' => $cantidad_stock]);
    		$actualizar2 = Inventario::where('idinventario', $id)->update(['estatus_movimiento' => 2]);
            $total_final = $total_final + $total;
    	}
        $consulta_inventario = Inventario::find($id);
        if ($consulta_inventario->condicion_movimiento == 2) {
            $cli_cxpagar = Cxpagar::where('idcliente', $datos['idcliente'])->get();
            if (count($cli_cxpagar) > 0) {
                $mov_cxcobrar = Mov_cxpagar::create(
                [
                    'idcxpagar' => $cli_cxpagar[0]->idcxpagar,
                    'num_documento_mov' => $datos['num_documento'],
                    'fecha_mov' => date('Y-m-d'),
                    'monto_mov' => ''.$total_final,
                    'abono_mov' => '0.00000',
                    'saldo_pendiente' => ''.$total_final,
                    'cant_dias_pendientes' => $datos['plazo_credito'],
                    'estatus_mov' => 1
                ]);
                $sumando = $cli_cxpagar[0]->saldo_pendiente + $total_final;
                $mcxpagar = Cxpagar::where('idcxpagar', $cli_cxpagar[0]->idcxpagar)->update(['saldo_pendiente' => $sumando]);

            }else{
                $producto = Productos::find($inventario_item[0]->idproducto);
                $cxpagar = Cxpagar::create(
                [
                    'idcliente' => $consulta_inventario->idcliente,
                    'idconfigfact' => $producto->idconfigfact,
                    'saldo_pendiente' => ''.$total_final,
                    'cantidad_dias' => $datos['plazo_credito'],
                    'fecha_cuenta' => date('Y-m-d')
                ]);

                $mov_cxpagar = Mov_cxpagar::create([
                    'idcxpagar' => $cxpagar->idcxpagar,
                    'num_documento_mov' => $datos['num_documento'],
                    'fecha_mov' => date('Y-m-d'),
                    'monto_mov' => ''.$total_final,
                    'abono_mov' => '0.00000',
                    'saldo_pendiente' => ''.$total_final,
                    'cant_dias_pendientes' => $datos['plazo_credito'],
                    'estatus_mov' => 1
                ]);
            }
        }
        
        return redirect()->route('inventario.index')->withStatus(__('Inventario guardado correctamente.'));
    }

    	public function delete(Inventario $inventario, $id)
    {
    	Inventario_item::where('idinventario', $id)->delete();
    	Inventario::where('idinventario', $id)->delete();
        return redirect()->route('inventario.index')->withStatus(__('Movimiento eliminado Correctamente.'));
    }
}
