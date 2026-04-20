<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedidos_item extends Model
{
 	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'pedidos_item';
    protected $primaryKey = 'idpedidositem';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idpedido', 'idproducto', 'cantidad_ped','valor_neto','valor_impuesto', 'valor_descuento', 'tipo_impuesto', 'descuento_prc', 'impuesto_prc', 'costo_utilidad', 'nombre_proc',
    ];

    public function prod_pedidos(){
        return $this->hasMany(Productos::class,'idproducto','idproducto');
    }
}
