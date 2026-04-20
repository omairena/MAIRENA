<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'codigo_actividad';
    protected $primaryKey = 'idcodigoactv';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idconfigfact','descripcion', 'codigo_actividad','estado','principal'
    ];
}
