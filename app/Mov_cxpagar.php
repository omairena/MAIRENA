<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mov_cxpagar extends Model
{
    protected $table = 'mov_cxpagar';
    protected $primaryKey = 'idmovcxpagar';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idcxpagar', 'num_documento_mov', 'fecha_mov','monto_mov','abono_mov','saldo_pendiente','cant_dias_pendientes','estatus_mov',
    ];

    public function cxpagar_mov(){
        return $this->hasMany(Cxpagar::class,'idcxpagar','idcxpagar');
    }
}
