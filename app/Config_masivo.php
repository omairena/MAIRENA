<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config_masivo extends Model
{
	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'config_masivo';
    protected $primaryKey = 'idconfigmasivo';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idlogmasivo', 'idclientes','idcaja','idconfigfact', 'idcodigoactv','tipo_documento_mas', 'condicion_venta','p_credito', 'medio_pago','total_serv_grab','total_serv_exento', 'total_serv_exonerado','total_mercancia_grav','total_mercancia_exenta','total_mercancia_exonerada','total_exento','total_exonerado','total_neto', 'total_descuento', 'total_impuesto', 'total_otros_cargos', 'total_iva_devuelto', 'total_comprobante','sales_masivo','observacion_masivo','tipo_moneda','tipo_cambio'
    ];

    public function configmas_cli(){
        return $this->hasMany(Cliente::class,'idcliente','idclientes');
    }
}
