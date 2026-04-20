<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cxcobrar;
use App\Mov_cxcobrar;
use App\Cliente;
use App\Log_cxcobrar;
use DB;
use App\Cajas;
use App\Configuracion;

class LogcxcobrarController extends Controller
{
        public function show($id)
    {   
    	$log_cxcobrar = Log_cxcobrar::where('idmovcxcobrar', $id)->get();
        return view('cxcobrar.log', ['log_cxcobrar' => $log_cxcobrar]);
    }

    	public function crear($id)
    {
        $mov_cxcobrar = Mov_cxcobrar::find($id);
        $cxcobrar = Cxcobrar::find($mov_cxcobrar->idcxcobrar);
        $cajas = Cajas::where([
            ['idconfigfact', $cxcobrar->idconfigfact],
            ['estatus', 1]
        ])->get();

    	return view('cxcobrar.crearab', ['id' => $id, 'idcxcobrar' => $mov_cxcobrar->idcxcobrar, 'saldo_pend' => $mov_cxcobrar->saldo_pendiente, 'cajas' => $cajas]);
    }

    	public function store(Request $request)
    {
        $request->validate([
            'num_recibo_abono' => 'required|max:10',
            'medio_pago' => 'required',
            'tipo_mov' => 'required',
            'monto_abono' => 'required',
            'referencia' => 'required|max:15',
        ]);
        	$datos = $request->all();
        $monto_ab = $datos['monto_abono'];
    $monto_ab = floatval(str_replace(',', '', $monto_ab));///lo paso de tener separador de coma , a sin separador
    
    //dd($ventaspre);
    
        $mov_cxcobrar = Mov_cxcobrar::find($datos['idmovcxcobrar']);
        $saldo_pendiente = $mov_cxcobrar->saldo_pendiente - $monto_ab;
        if ($saldo_pendiente >= 0) {
            $log_cxcobrar = Log_cxcobrar::create(
                [
                    'idmovcxcobrar' => $datos['idmovcxcobrar'],
                    'idcaja' => $datos['idcaja'],
                    'medio_pago' => $datos['medio_pago'],
                    'num_recibo_abono' => $datos['num_recibo_abono'],
                    'fecha_rec_mov' => date('Y-m-d'),
                    'monto_abono' => $monto_ab,
                    'tipo_mov' => $datos['tipo_mov'],
                    'referencia' => $datos['referencia']
                ]
            );
            $monto_total_abono = DB::table('log_cxcobrar')
                ->join('mov_cxcobrar', 'log_cxcobrar.idmovcxcobrar', '=', 'mov_cxcobrar.idmovcxcobrar')
                ->where('log_cxcobrar.idmovcxcobrar', '=', $datos['idmovcxcobrar'])
                ->sum('log_cxcobrar.monto_abono');
            if ($saldo_pendiente < 1 ) {
                $update_cxcobrar  = Mov_cxcobrar::where('idmovcxcobrar', $datos['idmovcxcobrar'])->update([
                    'abono_mov' => $monto_total_abono,
                    'estatus_mov' => 2,
                    'saldo_pendiente' => $saldo_pendiente
                ]);
            }else{
                $update_cxcobrar  = Mov_cxcobrar::where('idmovcxcobrar', $datos['idmovcxcobrar'])->update([
                    'abono_mov' => $monto_total_abono,
                    'saldo_pendiente' => $saldo_pendiente
                ]);
            }
            
            $qry_cxcobrar = Cxcobrar::find($mov_cxcobrar->idcxcobrar);
            $saldo_cxcobrar = $qry_cxcobrar->saldo_cuenta - $monto_ab;
            $cxcobrar  = Cxcobrar::where('idcxcobrar', $qry_cxcobrar->idcxcobrar)->update([
                    'saldo_cuenta' => $saldo_cxcobrar
                ]);
            $cxcobrar = Cxcobrar::find($mov_cxcobrar->idcxcobrar);
            $new = $datos['num_recibo_abono'] + 1;
            $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = 98 and idcaja = '.$datos['idcaja']);
            $caja = Cajas::find($log_cxcobrar->idcaja);

                //if ($caja->usa_impresion === 1) {
                    //app('App\Http\Controllers\ReportesController')->imprimirCxc($log_cxcobrar->idlogcxcobrar, $log_cxcobrar->idcaja);
                //}
            return redirect()->action('DonwloadController@correoCxc', $log_cxcobrar->idlogcxcobrar)->withStatus(__('Abono Agregado Correctamente.'));

        }else{
                return redirect()->back()->withStatus(__('Verificar el monto que esta colocando. Es mayor a la cuenta'));   
        }
    	
    }

        public function ajaxCuentacierre(Request $request)
    {
        $datos = $request->all();
        $saldo_pendiente = 0;
        if(strstr($datos['datos'],',') ){
            $valores = explode(',', $datos['datos']);
            for ($i=0; $i < count($valores); $i++) { 
                $mov_cxcobrar = Mov_cxcobrar::find($valores[$i]);
                $saldo_pendiente += $mov_cxcobrar->saldo_pendiente;
            }
        }else{
            $mov_cxcobrar = Mov_cxcobrar::find($datos['datos']);
            $saldo_pendiente += $mov_cxcobrar->saldo_pendiente;
        }
        return response()->json(['success'=> $saldo_pendiente]);
    }

        public function Storecierre(Request $request)
    {
        $request->validate([
            'monto_abonado' => 'required',
            'medio_pago' => 'required',
            'referencia' => 'required|max:15',
        ]);
        $datos = $request->all();
        $correos = [];
        $monto_total_abono = $datos['monto_abonado'];
        if(strstr($datos['cxcobrar_modal'],',') ){
            $valores = explode(',', $datos['cxcobrar_modal']);
            for ($i=0; $i < count($valores); $i++) {
                $mov_cxcobrar = Mov_cxcobrar::find($valores[$i]);
                $saldo_pendiente = $mov_cxcobrar->saldo_pendiente - $monto_total_abono;
                if ($saldo_pendiente <= 0) {
                    $mto_trans_abono = $mov_cxcobrar->saldo_pendiente;
                    $consecutivo = DB::table('consecutivos')->where([
                        ['idcaja', '=', $datos['idcaja']],
                        ['tipo_documento', '=', 98],
                    ])->get();
                    $numero_abono = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
                    $log_cxcobrar = Log_cxcobrar::create(
                        [
                            'idmovcxcobrar' => $mov_cxcobrar->idmovcxcobrar,
                            'idcaja' => $datos['idcaja'],
                            'medio_pago' => $datos['medio_pago'],
                            'num_recibo_abono' => $numero_abono,
                            'fecha_rec_mov' => date('Y-m-d'),
                            'monto_abono' => $mto_trans_abono,
                            'tipo_mov' => 1,
                            'referencia' => $datos['referencia']
                        ]
                    );

                    $update_cxcobrar  = Mov_cxcobrar::where('idmovcxcobrar', $mov_cxcobrar->idmovcxcobrar)->update([
                        'abono_mov' => $mto_trans_abono,
                        'estatus_mov' => 2,
                        'saldo_pendiente' => 0
                    ]);

                    $qry_cxcobrar = Cxcobrar::find($mov_cxcobrar->idcxcobrar);
                    $saldo_cxcobrar = $qry_cxcobrar->saldo_cuenta - $mto_trans_abono;
                    $cxcobrar  = Cxcobrar::where('idcxcobrar', $qry_cxcobrar->idcxcobrar)->update([
                        'saldo_cuenta' => $saldo_cxcobrar
                    ]);
                    $new = $numero_abono + 1;
                    $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = 98 and idcaja = '.$datos['idcaja']);
                    $caja = Cajas::find($log_cxcobrar->idcaja);
                    $monto_total_abono = (-1) * ($saldo_pendiente);
                    array_push($correos, $log_cxcobrar->idlogcxcobrar);
                    //if ($caja->usa_impresion === 1) {
                    //app('App\Http\Controllers\ReportesController')->imprimirCxc($log_cxcobrar->idlogcxcobrar, $log_cxcobrar->idcaja);
                    //}
                }else{
                    $consecutivo = DB::table('consecutivos')->where([
                        ['idcaja', '=', $datos['idcaja']],
                        ['tipo_documento', '=', 98],
                    ])->get();
                    $numero_abono = str_pad($consecutivo[0]->numero_documento, 10, "0", STR_PAD_LEFT);
                    $log_cxcobrar = Log_cxcobrar::create(
                        [
                            'idmovcxcobrar' => $mov_cxcobrar->idmovcxcobrar,
                            'idcaja' => $datos['idcaja'],
                            'medio_pago' => $datos['medio_pago'],
                            'num_recibo_abono' => $numero_abono,
                            'fecha_rec_mov' => date('Y-m-d'),
                            'monto_abono' => $monto_total_abono,
                            'tipo_mov' => 1,
                            'referencia' => $datos['referencia']
                        ]
                    );
                    if ($saldo_pendiente === 0) {
                        $update_cxcobrar  = Mov_cxcobrar::where('idmovcxcobrar', $mov_cxcobrar->idmovcxcobrar)->update([
                            'abono_mov' => $monto_total_abono,
                            'estatus_mov' => 2,
                            'saldo_pendiente' => $saldo_pendiente
                        ]);
                    }else{
                        $update_cxcobrar  = Mov_cxcobrar::where('idmovcxcobrar', $mov_cxcobrar->idmovcxcobrar)->update([
                            'abono_mov' => $monto_total_abono,
                            'saldo_pendiente' => $saldo_pendiente
                        ]);
                    }
                    

                    $qry_cxcobrar = Cxcobrar::find($mov_cxcobrar->idcxcobrar);
                    $saldo_cxcobrar = $qry_cxcobrar->saldo_cuenta - $monto_total_abono;
                    $cxcobrar  = Cxcobrar::where('idcxcobrar', $qry_cxcobrar->idcxcobrar)->update([
                        'saldo_cuenta' => $saldo_cxcobrar
                    ]);
                    $new = $numero_abono + 1;
                    $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = 98 and idcaja = '.$datos['idcaja']);
                    $caja = Cajas::find($log_cxcobrar->idcaja);
                    array_push($correos, $log_cxcobrar->idlogcxcobrar);
                    //if ($caja->usa_impresion === 1) {
                    //app('App\Http\Controllers\ReportesController')->imprimirCxc($log_cxcobrar->idlogcxcobrar, $log_cxcobrar->idcaja);
                    //}
                    return redirect()->action('DonwloadController@correoCxcmasivo', ['correos' => $correos])->withStatus(__('Abono Agregado Correctamente.'));
                }

            }
            return redirect()->action('DonwloadController@correoCxcmasivo', ['correos' => $correos])->withStatus(__('Abono Agregado Correctamente.'));
        }else{
                return redirect()->back()->withStatus(__('Debe seleccionar mas de una cuenta para procesar'));   
        }
    }

        public function ImprimirAbono($id)
    {
        $log_cxcobrar = Log_cxcobrar::find($id);
        $mov_cxcobrar = Mov_cxcobrar::find($log_cxcobrar->idmovcxcobrar);
        $cxcobrar = Cxcobrar::find($mov_cxcobrar->idcxcobrar);
        $configuracion = Configuracion::find($cxcobrar->idconfigfact);
        $cliente = Cliente::find($cxcobrar->idcliente);
        $caja = Cajas::find($log_cxcobrar->idcaja);
        return view('cxcobrar.imprimir', ['log_cxcobrar' => $log_cxcobrar, 'mov_cxcobrar' => $mov_cxcobrar, 'cxcobrar' => $cxcobrar, 'configuracion' => $configuracion, 'cliente' => $cliente, 'caja' => $caja]);
    }
}
