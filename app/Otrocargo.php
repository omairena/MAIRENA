<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Otrocargo extends Model
{
    protected $table = 'sales_item_otrocargo';
    protected $primaryKey = 'idotrocargo';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'numero_identificacion','nombre', 'detalle', 'porcentaje_cargo', 'monto_cargo','fecha_creado_cargo','idsales','tipo_otrocargo',
    ];
    
    public function sales()
    {
        return $this->hasOne(sales::class, 'idsale', 'idsales');
    }
    
}
