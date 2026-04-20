<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Configuracion extends Model
{
	use Notifiable;
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'configuracion';
    protected $primaryKey = 'idconfigfact';
    public $timestamps = false;
    protected $guarded = [];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre_empresa', 'client_id', 'nombre_emisor','ruta_certificado','clave_certificado','credenciales_conexion','clave_conexion','tipo_id_emisor','numero_id_emisor','telefono_emisor','email_emisor','provincia_emisor','canton_emisor','distrito_emisor','barrio_emisor', 'direccion_emisor', 'nombre_comercial','sucursal', 'factor_receptor','imprimir_comanda','nombre_impresora','usa_lector','es_transporte','usa_balanza','es_simplificado','servidor_email', 'email_servidor', 'clave_email_servidor','logo', 'usa_listaprecio', 'acepto_terminos','config_automatica','gnl','usa_op','sum_op','fecha_certificado','sin_impuesto_pos','fecha_plan',
        'proveedor_sistema','tipo_moneda_confi','otros_datos_factura','auto_copia_email','docs','mail_not',
    ];

    public function c_user(){
        return $this->belongsTo(User_config::class);
    }

    public function c_caja(){
        return $this->belongsTo(Cajas::class);
    }

    public function config_usuario(){
        return $this->belongsTo(User::class);
    }

}
