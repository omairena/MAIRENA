<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configuracion_automatica extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'config_automatica';
    protected $primaryKey = 'idconfigautomatica';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idcaja','idconfigfact', 'detalle_mensaje', 'estatus',
    ];
}
