<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log_cxpagar extends Model
{
    protected $table = 'log_cxpagar';
    protected $primaryKey = 'idlogcxpagar';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idmovcxpagar','idcaja', 'num_recibo_abono', 'fecha_rec_mov','monto_abono','tipo_mov','referencia',
    ];
}
