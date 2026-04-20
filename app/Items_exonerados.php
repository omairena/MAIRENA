<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Items_exonerados extends Model
{
   	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'items_exonerados';
    protected $primaryKey = 'iditemexonera';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idsalesitem', 'tipo_exoneracion', 'numero_exoneracion','institucion','fecha_exoneracion','porcentaje_exoneracion', 'monto_exoneracion','articulo','inciso','tipo_exoneracion_otro','institucion_otro',
    ];
}
