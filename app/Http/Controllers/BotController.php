<?php

namespace App\Http\Controllers;

use App\Bot;
use App\User;
use App\Trigger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class BotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::id();

        $user = User::with('bots')->firstWhere('id', $user_id);

        return view('bot.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $bot = new Bot();

        return view('bot.create', compact('bot'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Bot $bot)
    {
        $data = $this->validate($request, [
            'token' => 'required|min:1',
            'confirmation_token' => 'required|min:1',
            'name' => 'required|min:1',
            'description' => 'required|min:1'
        ]);
        
        $user_id = Auth::id();

        $user = User::with('bots')->firstWhere('id', $user_id);

        $bot = $user->bots()->make();

        $bot->fill($data);
        $bot->hash = Str::random(15).md5(date('Y-m-d H:i:s', time())).md5($user->email);
        $bot->save();

        return redirect()->route('bots.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bot  $bot
     * @return \Illuminate\Http\Response
     */
    public function show(Bot $bot)
    {
        $triggers = $bot->triggers;
        
        $triggers_arr = [];

        foreach ($triggers as $trigger) {
            $triggers_arr[$trigger['trigger_name']] = $trigger['response'];
        }

        if (!empty($triggers_arr)) {
            $triggers_json = json_encode($triggers_arr, JSON_PRETTY_PRINT);
        } else {
            $triggers_json = json_encode(['trigger' => 'response'], JSON_PRETTY_PRINT);
        }

        return view('bot.show', compact('bot', 'triggers_json')); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bot  $bot
     * @return \Illuminate\Http\Response
     */
    public function edit(Bot $bot)
    {
        return view('bot.edit', compact('bot'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bot  $bot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bot $bot)
    {
        if ($request->has('switch')) {
            $this->updateSwitchStatus($request, $bot);
        }

        if ($request->has('triggers')) {
            $this->updateTriggers($request, $bot);
        }
        
    }

    private function updateSwitchStatus(Request $request, Bot $bot) 
    {

        if ($request->input('switch') == 'Выключить бот') {
            $bot->is_active = false;
        } else if ($request->input('switch') == 'Включить бот') {
            $bot->is_active = true;
        }
        $bot->save();

        return response(null);
    }

    private function updateTriggers(Request $request, Bot $bot) 
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bot  $bot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bot $bot)
    {
        //
    }

    private function is_JSON($args) 
    {
        json_decode($args);
        return (json_last_error()===JSON_ERROR_NONE);
    }
}
