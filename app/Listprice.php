<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listprice extends Model
{
    protected $table = 'list_price';
    protected $primaryKey = 'idlist';
    public $timestamps = false;
    protected $guarded = [];

    protected $fillable = [
        'idconfigfact','descripcion', 'porcentaje', 'estatus','created_at',
    ];
}
