<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trigger extends Model
{
    protected $fillable = [
        'trigger_name', 'response'
    ];

    //trigger can have one bot
    public function bots()
    {
        return $this->belongsTo('App\Bot');
    }
}
