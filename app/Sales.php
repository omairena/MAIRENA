<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sales extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'sales';
    protected $primaryKey = 'idsale';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idconfigfact', 'tipo_documento', 'numero_documento','punto_venta','idcaja','idcliente','tipo_moneda','tipo_cambio', 'condicion_venta','p_credito', 'medio_pago', 'referencia_pago','total_neto', 'total_descuento', 'total_impuesto', 'total_otros_cargos', 'total_iva_devuelto', 'total_comprobante','idcodigoactv','referencia_pago', 'tipo_devolucion', 'tiene_exoneracion','total_serv_grab','total_serv_exento', 'total_serv_exonerado','total_mercancia_grav','total_mercancia_exenta','total_mercancia_exonerada','total_exento','total_exonerado','estatus_sale','tiene_exoneracion','fecha_creada','idmovcxcobrar','referencia_sale','observaciones','fecha_reenvio','referencia_compra','uso_listaprecio','idlistaprecio','es_op','total_abonos_op','num_documento_convertido','estatus_op','viene_de_op','desea_enviarcorreo',
'TipoDocIR','api_aby','creado_por'];

      public function facelectron()
    {
        return $this->hasOne(Facelectron::class, 'idsales', 'idsale');
    }
     public function sales_item_otrocargo()
    {
        return $this->hasOne(sales_item_otrocargo::class, 'idsales', 'idsale');
    }

    public function items() : HasMany
    {
        return $this->hasMany(Sales_item::class, 'idsales');
    }

    public function cliente() : BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'idcliente');
    }

    public function actividad() : BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'idcodigoactv');
    }

    public function caja() : BelongsTo
    {
        return $this->belongsTo(Cajas::class, 'idcaja');
    }

    public function configuracion() : BelongsTo
    {
        return $this->belongsTo(Configuracion::class, 'idconfigfact');
    }

    public function cargos() : HasMany
    {
        return $this->hasMany(Otrocargo::class, 'idsales');
    }

    public function facturaElectronica()
    {
        return $this->hasOne(Facelectron::class, 'idsales', 'idsale');
    }

    public function documentoReferencia()
    {
        return $this->belongsTo(Sales::class, 'referencia_sale', 'idsale');
    }

    public function documentosGenerados()
    {
        return $this->hasMany(Sales::class, 'referencia_sale');
    }

    public function medioPagos()
    {
        return $this->belongsToMany(MedioPago::class, 'medio_pago_sale', 'sale_id', 'medio_pago_id')->withPivot('referencia', 'monto'); // Incluye la columna referencia
    }

    /**
     * Trae el total Venta de un documento
    */
    public function getTotalVentaAttribute()
    {
       return (($this->total_mercancia_grav + $this->total_serv_grab)
            + $this->total_mercancia_exenta
            + $this->total_mercancia_exonerada
            + $this->total_serv_exonerado
            + $this->total_serv_exento);
    }

    /**
     * Trae el total Venta de un documento
    */
    public function getTotalVentaNetaAttribute()
    {
       return (
            ($this->total_mercancia_grav
                + $this->total_mercancia_exenta
                + $this->total_mercancia_exonerada
                + $this->total_serv_grab
                + $this->total_serv_exonerado
                + $this->total_serv_exento
            )
            - $this->total_descuento
        );
    }

    /**
     * Trae el total Venta de un documento
    */
    public function getTotalComprobanteAttribute()
    {
       return (
                (
                    (
                        ($this->total_mercancia_grav
                        + $this->total_mercancia_exenta
                        + $this->total_mercancia_exonerada
                        + $this->total_serv_grab
                        + $this->total_serv_exonerado
                        + $this->TotalServNoSujeto
                        + $this->TotalMercNoSujeta
                        + $this->total_serv_exento)
                    - $this->total_descuento)
                + $this->total_impuesto)
            - $this->total_iva_devuelto)
        + $this->total_otros_cargos;
    }
}
