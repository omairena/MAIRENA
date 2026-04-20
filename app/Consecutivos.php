<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Consecutivos extends Model
{
	use Notifiable;
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'consecutivos';
    protected $primaryKey = 'idconsecutivo';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idcaja', 'tipo_documento', 'numero_documento','doc_desde', 'doc_hasta','tipo_compra',
    ];
}
