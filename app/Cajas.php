<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cajas extends Model
{
	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'cajas';
    protected $primaryKey = 'idcaja';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idconfigfact','nombre_caja', 'monto_fondo','estatus','codigo_unico','fecha_apertura', 'fecha_cierre','usa_impresion','nombre_imp','ip_imp',
    ];

    public function caja_emp(){
        return $this->hasMany(Configuracion::class,'idconfigfact','idconfigfact');
    }
}
