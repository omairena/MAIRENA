<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log_cajas extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'log_cajas';
    protected $primaryKey = 'idlogcaja';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idcaja','idusuario', 'fecha_apertura_caja', 'fecha_cierre_caja', 'fondo_caja', 'ventas_contado','ventas_credito','recibo_dinero','t_efectivo_entrante','cobro_tarjeta','pago_del_dia','t_efectivo_caja','t_efectivo_depositar','ruta_reporte','t_tarjeta_abono',
    ];
}
