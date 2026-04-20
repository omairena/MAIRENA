<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cxpagar extends Model
{
 	protected $table = 'cxpagar';
    protected $primaryKey = 'idcxpagar';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idcliente', 'idconfigfact','saldo_pendiente', 'cantidad_dias','fecha_cuenta',
    ];

    public function cxpagar_cli(){
        return $this->hasMany(Cliente::class,'idcliente','idcliente');
    }

    public function mov_cxp(){
        return $this->belongsTo(Mov_cxpagar::class);
    }
}
