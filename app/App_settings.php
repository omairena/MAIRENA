<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App_settings extends Model
{

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'app_settings';
    protected $primaryKey = 'idsettings';
    protected $guarded = [];

    /**
    * The attributes that are mass assignable.
    *
     * @var array
     */
    protected $fillable = [
        'name', 'value',
    ];

}
