<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','es_admin','super_admin','idconfigfact','es_vendedor','status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function u_c(){
        return $this->belongsTo(User_config::class);
    }

    public function c_u(){
        return $this->hasMany(User_config::class,'id','idusuario');
    }

    public function config_u(){
        return $this->hasMany(Configuracion::class,'idconfigfact','idconfigfact');
    }
}
