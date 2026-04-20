<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log_masivo extends Model
{
    protected $table = 'log_masivo';
    protected $primaryKey = 'idlogmasivo';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idconfigfact','nombre_masivo','estatus_masivo', 'fecha_masivo',
    ];
}
