<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tr_bancos extends Model
{
    protected $table = 'tr_bancos';
    protected $primaryKey = 'id_tr_bancos';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_tr_bancos','id_bancos','monto', 'obs','idcliente','clasificacion','referencia','idsales','idconfigfact','fecha','idreceptor','clasificacion_recep','factura','signo','user'
    ];
}
