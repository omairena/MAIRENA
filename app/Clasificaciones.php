<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Clasificaciones extends Model
{
        use Notifiable;
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'clasificaciones';
    protected $primaryKey = 'idclasifica';
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idclasifica', 'descripcion', 'estatus',
    ];
}
