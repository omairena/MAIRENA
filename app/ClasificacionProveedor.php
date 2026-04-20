<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClasificacionProveedor extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'clasificacion_proveedor';
    protected $primaryKey = 'idclasificacion';
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idcliente', 'codigo_actividad', 'razon_social','tipo_clasificacion','descripcion_clasificacion','por_defecto',
    ];
}
