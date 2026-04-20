<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log_cxcobrar extends Model
{
    protected $table = 'log_cxcobrar';
    protected $primaryKey = 'idlogcxcobrar';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idmovcxcobrar','idcaja', 'medio_pago', 'num_recibo_abono', 'fecha_rec_mov','monto_abono','tipo_mov','referencia',
    ];
}
