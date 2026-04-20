<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Cajas_user extends Model
{
	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'caja_usuario';
    protected $primaryKey = 'idcajausuario';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idcajausuario','idusuario','idcaja', 'estado',
    ];

   public function caja() {  
    return $this->belongsTo(Cajas::class, 'idcaja');  
}  
}
