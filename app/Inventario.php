<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
   /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'inventario';
    protected $primaryKey = 'idinventario';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'tipo_movimiento','idcliente', 'fecha', 'observaciones','num_documento','estatus_movimiento','condicion_movimiento','plazo_credito','total_inventario','idconfigfact',
    ];

    public function proveedores(){
        return $this->hasMany(Cliente::class,'idcliente','idcliente');
    }
   
}
