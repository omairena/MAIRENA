<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_config extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'user_config';
    protected $primaryKey = 'iduserconfig';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'idusuario','idconfigfact', 'usa_pos','fecha_creado', 'estatus',
    ];

    public function user_c(){
        return $this->hasMany(User::class,'id','idusuario');
    }
    public function viene_user_c(){
        return $this->belongsTo(User::class);
    }
    public function config_u(){
        return $this->hasMany(Configuracion::class,'idconfigfact','idconfigfact');
    }
}
