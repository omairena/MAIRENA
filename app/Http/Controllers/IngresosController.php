<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configuracion;
use App\Cliente;
use App\Sales;
use App\Sales_item;
use App\Consecutivos;
use App\Facelectron;
use App\Clasificaciones;
use App\Bancos;
use App\Tr_bancos;
use DB;
use Auth;

class IngresosController extends Controller
{
        public function index(Sales $model)
    {
        $fecha_desde = date('Y-m-01');
        $fecha_hasta = date('Y-m-t');
       // $sales = Sales::where([
           
        //    ['tipo_documento', '01' or '04'],
         //   ['idconfigfact', '=', Auth::user()->idconfigfact],
         //   ['idconfigfact', '=', Auth::user()->idconfigfact],
      //  ])->orderBy('numero_documento', 'desc')->get();
    //	return view('ingresos.index', ['sales' => $sales]);
    	
    	
    	
    	 $sales = DB::table('sales')
          ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.observaciones','sales.idcliente','sales.numero_documento','sales.fecha_creada','sales.total_neto', 'sales.total_descuento')
          ->where('sales.estatus_sale', 2)
          //->where('sales.tipo_documento', '!=' , '03')
          ->where('sales.bancos', 0)
          ->where('facelectron.estatushacienda', '=', 'aceptado')
          ->where('sales.idconfigfact', '=', Auth::user()->idconfigfact)
          ->get();
          	return view('ingresos.index', ['sales' => $sales]);
    }


//rechaza
public function rechaza($id)
    {
     
       
    $update_sales  = Sales::where('idsale', $id)->update([
                    'bancos' => 1, ]);
     $clasificaciones = Clasificaciones::where('estatus', '=', 1)->get();
        $bancos = Bancos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    
    
       $sales = DB::table('sales')
          ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado', 'sales.total_comprobante', 'sales.idsale', 'sales.observaciones','sales.idcliente','sales.numero_documento','sales.fecha_creada','sales.total_neto', 'sales.total_descuento')
          ->where('sales.estatus_sale', 2)
          //->where('sales.tipo_documento', '!=' , '03')
          ->where('sales.bancos', 0)
          ->where('facelectron.estatushacienda', '=', 'aceptado')
          ->where('sales.idconfigfact', '=', Auth::user()->idconfigfact)
          ->get();
          	return view('ingresos.index', ['sales' => $sales]);
    	           
           
        }

///
public function show($id)
    {
     
        $clasificaciones = Clasificaciones::where('estatus', '=', 1)->get();
        $bancos = Bancos::where('idconfigfact', '=', Auth::user()->idconfigfact)->get();
    
    
        if (Auth::user()->es_vendedor == 1){

            Session::flash('message', "Tu usuario no permite ver las cuentas");
            return redirect()->route('facturar.index');
        }
    	$sales = DB::table('sales')
          ->leftjoin('facelectron', 'sales.idsale', '=', 'facelectron.idsales')
          ->leftjoin('clientes', 'sales.idcliente', '=', 'clientes.idcliente')
          ->leftjoin('codigo_actividad', 'sales.idcodigoactv', '=', 'codigo_actividad.idcodigoactv')
          ->select('sales.tipo_documento','sales.total_comprobante','sales.total_impuesto','sales.condicion_venta', 'sales.condicion_venta','sales.tipo_cambio','sales.tipo_moneda', 'sales.tiene_exoneracion','facelectron.tipodoc', 'facelectron.numdoc', 'facelectron.fechahora', 'facelectron.estatushacienda', 'clientes.nombre', 'clientes.num_id as identificacion_cliente', 'codigo_actividad.codigo_actividad', 'sales.total_impuesto', 'sales.total_otros_cargos', 'sales.total_iva_devuelto', 'sales.total_exonerado',  'sales.idsale', 'sales.observaciones','sales.idcliente','sales.numero_documento','sales.fecha_creada','sales.total_neto', 'sales.total_descuento')
          ->where('sales.idsale', $id)
          ->get();
         $cliente = Cliente::where('idcliente','=',$sales[0]->idcliente)->get();
      
         $total = Sales::find($id);
         return view('ingresos.regingre', ['id' => $id, 'totalc' => $total->total_comprobante ,'clasificaciones' => $clasificaciones, 'sales'=>$sales, 'bancos'=>$bancos, 'cliente'=>$cliente]);
      
    }
    
    	public function store(Request $request)
    {
         $request->validate([
           
            'obs' => 'required|max:100',
        ]);
       
    	$datos = $request->all();
       
       
       // 
       if($datos['t_factura'] !='03'){
            $tr_ingresos = tr_bancos::create(
                [
                    'id_bancos' => $datos['banco'],
                    'monto' => $datos['monto_abono'],
                    'obs' => $datos['obs'],
                    'idcliente' => $datos['idcliente'],
                    'clasificacion'=> $datos['cliente_serch'],
                    'referencia' => $datos['referencia'],
                    'idsales' => $datos['id_referencia'],
                    'fecha' => date('Y-m-d'),
                    'idconfigfact'=> Auth::user()->idconfigfact,
                    'factura' => $datos['factura'],
                    'signo' => '+',
                    'user'=> Auth::user()->email,
                    ]
            );
            //dd($tr_ingresos);
                $update_sales  = Sales::where('idsale', $datos['id_referencia'])->update([
                    'bancos' => 1,
                    
                ]);
             $bancos = Bancos::where('id_bancos', '=', $datos['banco'])->get();
             
             
            // dd($bancos);
             $update_banco  = Bancos::where('id_bancos', $datos['banco'])->update([
                    'saldo' => ($bancos[0]->saldo + $datos['monto_abono']),
                    
                ]);
            
           	
           	return redirect()->route('ingresos.index')->withStatus(__('Registrado correctamente.'));
       }else{  //si es NC
          $tr_ingresos = tr_bancos::create(
                [
                    'id_bancos' => $datos['banco'],
                    'monto' => $datos['monto_abono'],
                    'obs' => $datos['obs'],
                    'idcliente' => $datos['idcliente'],
                    'clasificacion'=> $datos['cliente_serch'],
                    'referencia' => $datos['referencia'],
                    'idsales' => $datos['id_referencia'],
                    'fecha' => date('Y-m-d'),
                    'idconfigfact'=> Auth::user()->idconfigfact,
                    'factura' => $datos['factura'],
                    'signo' => '-',
                    'user'=> Auth::user()->email,
                    ]
            );
            //dd($tr_ingresos);
                $update_sales  = Sales::where('idsale', $datos['id_referencia'])->update([
                    'bancos' => 1,
                    
                ]);
             $bancos = Bancos::where('id_bancos', '=', $datos['banco'])->get();
             
             
            // dd($bancos);
             $update_banco  = Bancos::where('id_bancos', $datos['banco'])->update([
                    'saldo' => ($bancos[0]->saldo - $datos['monto_abono']),
                    
                ]);
            
           	
           	return redirect()->route('ingresos.index')->withStatus(__('Registrado correctamente.')); 
       }
        }
    //
   
        
}
