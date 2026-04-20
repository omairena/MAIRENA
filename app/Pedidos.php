<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pedidos';
    protected $primaryKey = 'idpedido';
    public $timestamps = false; // Asumiendo que no usas timestamps automáticos
    protected $guarded = []; // Puedes mantener esto vacío si usas $fillable

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idcaja',
        'idconfigfact',
        'idcliente',
        'numero_documento',
        'total_serv_grab_ped',
        'total_serv_exento_ped',
        'total_serv_exonerado_ped',
        'total_mercancia_grav_ped',
        'total_mercancia_exenta_ped',
        'total_mercancia_exonerada_ped',
        'total_exento_ped',
        'total_exonerado_ped',
        'total_neto_ped',
        'total_descuento_ped',
        'total_impuesto_ped',
        'total_otros_cargos_ped',
        'total_iva_devuelto_ped',
        'total_comprobante_ped',
        'estatus_doc',
        'fecha_doc',
        'pdf_pedido',
        'tipo',
        // Nuevos campos agregados:
        'descripcion',       // descripción del artículo a reparar
        'marca',             // marca del artículo
        'modelo',            // modelo del artículo
        'serie',             // número de serie
        'factura',           // número de factura
        'num_servicio',      // número de servicio
        'fecha_venta',       // fecha de venta
        'tiene_garantia',    // indicador de garantía (boolean)
        'accesorios',        // accesorios que acompañan al artículo
        'falla',             // fallo del artículo
        'observaciones',
        'user',
        'datos_cierre'
    ];

    /**
     * Define the relationship between Pedidos and Cliente.
     */
    public function ped_cli()
    {
        return $this->belongsTo(Cliente::class, 'idcliente', 'idcliente'); // Cambiado a `belongsTo` porque un pedido pertenece a un cliente.
    }
    
}