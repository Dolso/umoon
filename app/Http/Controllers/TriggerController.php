<?php

namespace App\Http\Controllers;

use App\Bot;
use App\Trigger;
use Illuminate\Http\Request;

class TriggerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Bot  $bot
     * @return \Illuminate\Http\Response
     */
    public function index(Bot $bot)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Bot  $bot
     * @return \Illuminate\Http\Response
     */
    public function create(Bot $bot)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bot  $bot
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Bot $bot)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bot  $bot
     * @param  \App\Trigger  $trigger
     * @return \Illuminate\Http\Response
     */
    public function show(Bot $bot, Trigger $trigger)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bot  $bot
     * @param  \App\Trigger  $trigger
     * @return \Illuminate\Http\Response
     */
    public function edit(Bot $bot, Trigger $trigger)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bot  $bot
     * @param  \App\Trigger  $trigger
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bot $bot)
    {
        $bot->triggers->isEmpty() ? true : $bot->triggers()->delete();

        if($this->is_JSON($new_triggers = (string)$request->input('triggers'))) {
            $new_triggers_arr = json_decode($new_triggers, true);
            $triggers_save = [];
            foreach ($new_triggers_arr as $trigger => $response) {
                $triggers_save[] = new Trigger(['trigger_name' => (string)$trigger, 'response' => (string)$response]);
            }
            $bot->triggers()->saveMany($triggers_save);
        }
        
        return response(null);
    }

    private function is_JSON($args) 
    {
        json_decode($args);
        return (json_last_error()===JSON_ERROR_NONE);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bot  $bot
     * @param  \App\Trigger  $trigger
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bot $bot, Trigger $trigger)
    {
        //
    }
}
