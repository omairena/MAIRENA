<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedioPago extends Model
{
    protected $table = 'medio_pagos';
    protected $guarded = [];

    protected $fillable = [
        'nombre', 'codigo', 'activo', 'nombre_interno',
    ];

    public function sales()
    {
        return $this->belongsToMany(Sales::class, 'medio_pago_sale', 'medio_pago_id', 'sale_id')->withPivot('referencia', 'monto'); // Incluye la columna referencia 
    }
}
