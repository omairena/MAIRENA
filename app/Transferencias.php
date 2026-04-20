<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transferencias extends Model
{
    protected $table = 'transfer';
    protected $primaryKey = 'id_transfer';
    public $timestamps = false;
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'id_transfer','idconficfact','origen','destino','monto','referencia','obs','fecha','user'
    ];
}
