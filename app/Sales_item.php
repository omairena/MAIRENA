<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sales_item extends Model
{
     /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'sales_item';
    protected $primaryKey = 'idsalesitem';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idsales', 'codigo_producto', 'nombre_producto','cantidad','valor_neto','valor_impuesto', 'valor_descuento', 'tipo_impuesto','descuento_prc','impuesto_prc','existe_exoneracion','costo_utilidad', 'idproducto',
    ];

    public function prod_sale(): HasMany
    {
        return $this->hasMany(Productos::class, 'idproducto', 'idproducto');
    }

    public function sale() : BelongsTo
    {
        return $this->belongsTo(Sales::class, 'idsales');
    }

    public function producto() : BelongsTo
    {
        return $this->belongsTo(Productos::class,'idproducto');
    }

    public function exoneracion() : HasOne
    {
        return $this->hasOne(Items_exonerados::class, 'idsalesitem');
    }

public function getPorcentajeImpuestoAttribute()  
{  
    $valoresImpuesto = [  
        '01' => 0,  
        '02' => 1,  
        '03' => 2,  
        '04' => 4,  
        '05' => 0,  
        '06' => 4,  
        '07' => 8,  
        '08' => 13,  
        '09' => 0.50,  
        '10' => 0,  
        '11' => 0  
    ];  

    return $valoresImpuesto[$this->tipo_impuesto] ?? null; // Devuelve null si no se encuentra el tipo  
}

    public function getMontoTotalLineaAttribute()
    {
        // Asegúrate de que todos los campos utilizados estén en el modelo
        return (
            ($this->costo_utilidad * $this->cantidad)
            - $this->valor_descuento
            + $this->valor_impuesto
            - ($this->exoneracion ? $this->exoneracion->monto_exoneracion : 0) // Manejo de null
        );
    }
}
