<?php

namespace App\Http\Controllers;

use App\Bot;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class DevController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $hash)
    {

        $bot = Bot::firstWhere('hash', $hash);

        if (empty($bot)) {
            return response('ok');
        }

        if ($bot->is_active == 0) {
            return response('ok');
        }

        $data = $request->input();

        if ($data['type'] == 'confirmation') {
            return response($bot->confirmation_token);
        } else if ($data['type'] == 'message_new') {
            $peer_id = $data['object']['peer_id'];
            $message = $data['object']['text'];

            $trigger_response = $bot->triggers()->where('trigger_name', $message)->value('response');

            empty($trigger_response) ? $message_response = 'Напишите что-нибудь другое' : $message_response = $trigger_response;

            $request_params = array(
                'message' => $message_response,
                'peer_id' => $peer_id,
                'access_token' => $bot->token,
                'v' => '5.87'
            );

            $get_params = http_build_query($request_params);
            file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);

            //$bot->increment('number_response');
        }

        return response('ok');
    }
}

