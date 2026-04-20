<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Distritos extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'distritos';
    protected $primaryKey = 'iddistrito';
    public $timestamps = false;
}
