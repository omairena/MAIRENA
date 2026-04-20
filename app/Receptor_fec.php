<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receptor_fec extends Model
{
   	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'receptor_fec';
    protected $primaryKey = 'idreceptor';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idconfigfact','idcaja','idcodigoactv','tipo_documento', 'clave', 'consecutivo','detalle_mensaje','ruta_carga','xml_envio','xml_respuesta', 'fecha','nombre_emisor', 'total_impuesto', 'total_comprobante','cedula_emisor','pendiente','estatus_hacienda','clasifica_d151','condicion_impuesto', 'imp_creditar', 'gasto_aplica','hacienda_imp_creditar','hacienda_gasto_aplica','tipo_documento_recibido','numero_documento_receptor','fecha_xml_envio','consecutivo_doc_receptor','moneda','tc',,'version','codigo_act_xml'
    ];
}
