<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cxpagar;
use App\Mov_cxpagar;
use App\Cliente;
use App\Log_cxpagar;
use DB;
use App\Cajas;

class LogcxpagarController extends Controller
{
        public function show($id)
    {   
    	$log_cxpagar = Log_cxpagar::where('idmovcxpagar', $id)->get();
        return view('cxpagar.log', ['log_cxpagar' => $log_cxpagar]);
    }

    	public function crear($id)
    {
        $mov_cxpagar = Mov_cxpagar::find($id);
        $cxpagar = Cxpagar::find($mov_cxpagar->idcxpagar);
        $cajas = Cajas::where([
            ['idconfigfact', $cxpagar->idconfigfact],
            ['estatus', 1]
        ])->get();
        
    	return view('cxpagar.crearab', ['id' => $id, 'idcxpagar' => $mov_cxpagar->idcxpagar, 'cajas' => $cajas]);
    }

    	public function store(Request $request)
    {
    	$datos = $request->all();    
        $request->validate([
            'num_recibo_abono' => 'required|max:10',
            'tipo_mov' => 'required',
            'monto_abono' => 'required',
            'referencia' => 'required|max:15',
        ]);
        $mov_cxpagar = Mov_cxpagar::find($datos['idmovcxpagar']);
        $saldo_pendiente = $mov_cxpagar->saldo_pendiente - $datos['monto_abono'];
        if ($saldo_pendiente >= 0) {
    	    $log_cxpagar = Log_cxpagar::create(
                [
               	    'idmovcxpagar' => $datos['idmovcxpagar'],
                    'idcaja' => $datos['idcaja'],
                    'num_recibo_abono' => $datos['num_recibo_abono'],
                    'fecha_rec_mov' => date('Y-m-d'),
                    'monto_abono' => $datos['monto_abono'],
                    'tipo_mov' => $datos['tipo_mov'],
                    'referencia' => $datos['referencia']
                ]);
            $monto_total_abono = DB::table('log_cxpagar')
    		->join('mov_cxpagar', 'log_cxpagar.idmovcxpagar', '=', 'mov_cxpagar.idmovcxpagar')
    		->where('log_cxpagar.idmovcxpagar', '=', $datos['idmovcxpagar'])
    		->sum('log_cxpagar.monto_abono');
             if ($saldo_pendiente == 0) {
                $mov_cxpagar_upd  = Mov_cxpagar::where('idmovcxpagar', $datos['idmovcxpagar'])->update([
                    'abono_mov' => $monto_total_abono,
                    'estatus_mov' => 2,
                    'saldo_pendiente' => $saldo_pendiente
                ]);
            }else{
                $mov_cxpagar_upd  = Mov_cxpagar::where('idmovcxpagar', $datos['idmovcxpagar'])->update([
                    'abono_mov' => $monto_total_abono,
                    'saldo_pendiente' => $saldo_pendiente
                ]);
            }
            $qry_cxpagar = Cxpagar::find($mov_cxpagar->idcxpagar);
            $saldo_cxpagar = $qry_cxpagar->saldo_pendiente - $datos['monto_abono'];
            $cxpagar  = Cxpagar::where('idcxpagar', $qry_cxpagar->idcxpagar)->update([
                    'saldo_pendiente' => $saldo_cxpagar
                ]);
    	    $cxpagar = Cxpagar::find($mov_cxpagar->idcxpagar);
            $new = $datos['num_recibo_abono'] + 1;
            $consecutivo = DB::update('update consecutivos set numero_documento = '.$new.' where tipo_documento = 99 and idcaja = '.$datos['idcaja']);

            return redirect()->action('DonwloadController@correoCxp', ['idlogcxpagar' => $log_cxpagar->idlogcxpagar])->withStatus(__('Abono Agregado Correctamente.'));

        }else{
            return redirect()->back()->withStatus(__('Verificar el monto que esta colocando. Es mayor a la cuenta'));   
        }
    }
}
