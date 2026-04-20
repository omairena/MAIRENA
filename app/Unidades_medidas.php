<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unidades_medidas extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'unidad_medida';
    protected $primaryKey = 'idunidadmedida';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'simbolo','descripcion', 'version_hacienda',
    ];

    public function unid(){
        return $this->hasMany(Productos::class,'idunidadmedida','idunidadmedida');
    }
}
