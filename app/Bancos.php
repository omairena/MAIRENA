<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bancos extends Model
{
    protected $table = 'bancos';
    protected $primaryKey = 'id_bancos';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'id_bancos','idconfigfact','cuenta','saldo'
    ];
}
