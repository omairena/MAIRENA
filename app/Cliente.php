<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'clientes';
    protected $primaryKey = 'idcliente';
    public $timestamps = false;
    protected $guarded = [];
    public function proveedor(){
        return $this->belongsTo(Inventario::class);
    }

    public function cliente_cxc(){
        return $this->belongsTo(Cxcobrar::class);
    }

    public function cliente_cmas(){
        return $this->belongsTo(Config_masivo::class);
    }

    public function cliente_ped(){
        return $this->belongsTo(Pedidos::class);
    }
}
