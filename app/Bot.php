<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{

    protected $fillable = [
        'token', 'confirmation_token', 'name', 'description'
    ];
    
    //bot can have one user
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    //bot can have several triggers
    public function triggers()
    {
        return $this->hasMany('App\Trigger');
    }
}
