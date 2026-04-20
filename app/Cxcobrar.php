<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cxcobrar extends Model
{
    protected $table = 'cxcobrar';
    protected $primaryKey = 'idcxcobrar';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idcliente', 'idconfigfact','saldo_cuenta', 'cantidad_dias',
    ];

    public function cxcobrar_cli(){
        return $this->hasMany(Cliente::class,'idcliente','idcliente');
    }

    public function mov_cxc(){
        return $this->belongsTo(Mov_cxcobrar::class);
    }
}
