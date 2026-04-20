<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cantones extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'cantones';
    protected $primaryKey = 'idcanton';
    public $timestamps = false;

}
