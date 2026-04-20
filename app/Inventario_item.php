<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventario_item extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'inventario_item';
    protected $primaryKey = 'idinventario_item';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idinventario','idproducto', 'fecha', 'cantidad_inventario',
    ];
}
