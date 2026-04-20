<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Forma_farmaceutica extends Model
{
	/**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'forma_farmaceutica';
    protected $primaryKey = 'id_formula';
    public $timestamps = false;
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'id_formula','codigo', 'forma',
    ];

   
}
