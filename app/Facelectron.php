<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facelectron extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'facelectron';
    protected $primaryKey = 'idfacelectron';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idsales','idconfigfact', 'tipodoc', 'numdoc','clave','codigoHTTP','estatushacienda', 'mensajehacienda', 'rutaxml', 'respuesta_xml', 'fechahora','consecutivo','pendiente','pdf_factura','enviado_correo','fecha_hora_pdf',
    ];
    
    public function sales()  
    {  
        return $this->belongsTo(Sales::class, 'idsales', 'idsale');  
    }  
}
