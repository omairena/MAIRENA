<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mov_cxcobrar extends Model
{
    protected $table = 'mov_cxcobrar';
    protected $primaryKey = 'idmovcxcobrar';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idcxcobrar', 'num_documento_mov', 'fecha_mov','monto_mov','abono_mov','saldo_pendiente','cant_dias_pendientes','estatus_mov',
    ];

    public function cxcobrar_mov(){
        return $this->hasMany(Cxcobrar::class,'idcxcobrar','idcxcobrar');
    }
}
