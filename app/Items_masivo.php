<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Items_masivo extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'items_masivo';
    protected $primaryKey = 'iditemsmasivo';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idconfigmasivo', 'idproducto', 'codigo_producto', 'nombre_producto','cantidad_masivo','valor_neto','valor_impuesto', 'valor_descuento', 'tipo_impuesto','descuento_prc','impuesto_prc','existe_exoneracion','costo_utilidad', 
    ];

    public function prod_masivo(){
        return $this->hasMany(Productos::class,'idproducto','idproducto');
    }
}
