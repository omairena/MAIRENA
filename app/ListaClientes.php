<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListaClientes extends Model
{
    protected $table = 'clientes_list_price';
    protected $primaryKey = 'idlistacliente ';
    public $timestamps = false;
    protected $guarded = [];

    protected $fillable = [
        'idcliente', 'idlist', 'por_defecto',
    ];
}
