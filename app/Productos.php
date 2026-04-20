<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Productos extends Model
{
	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'productos';
    protected $primaryKey = 'idproducto';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idconfigfact','idcodigoactv', 'nombre_producto', 'codigo_producto', 'codigo_cabys','img_producto','idunidadmedida','tipo_producto', 'impuesto_iva','costo', 'utilidad_producto', 'precio_sin_imp','precio_final','cantidad_stock','partida_arancelaria', 'fecha_creado','porcentaje_imp','flotante','exportable','granel','reg_med','forma','cod_reg_med',
    ];

    public function productos_unidad(){
        return $this->hasMany(Unidades_medidas::class,'idunidadmedida','idunidadmedida');
    }

    public function sale_prod(){
        return $this->belongsTo(Sales_item::class);
    }
    public function pedido_prod(){
        return $this->belongsTo(Pedidos_item::class);
    }

    public function actividad() : HasOne
    {
        return $this->hasOne(Actividad::class, 'idcodigoactv');
    }

    public function unidad() : BelongsTo
    {
        return $this->belongsTo(Unidades_medidas::class, 'idunidadmedida');
    }

    public function configuracion() : HasOne
    {
        return $this->hasOne(Configuracion::class, 'idconfigfact');
    }
}
