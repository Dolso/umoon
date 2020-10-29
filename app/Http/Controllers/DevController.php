<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Jobs\AfterResponseMessageAddHisoryJob;

class DevController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request, $hash)
    {
        $data = $request->input();

        $response = $this->sendMessageOrReturnConfirmationToken($data, $hash);

        return response($response);
    }

    /**
     * Формируем сообщение или отправляем токен.
     * 
     * @param array $data - данные, которые были в теле запроса
     * @param string $hash - хеш бота
     * @return string возвращает ответ
     */
    private function sendMessageOrReturnConfirmationToken ($data, $hash) : string
    {
        if ($data['type'] == 'confirmation') {

            $bot = DB::select('
                SELECT 
                    confirmation_token
                FROM 
                    bots
                WHERE 
                    hash = ?
                LIMIT 1', 
            [$hash]);

            if (!empty($bot[0]->confirmation_token)) {
                return response($bot[0]->confirmation_token);
            }
            
        } else if ($data['type'] == 'message_new') {
            $resonse = DB::select('
                SELECT
                    bots.id,
                    bots.token,
                    triggers.response
                FROM 
                    bots 
                LEFT JOIN 
                    triggers ON bots.id = triggers.bot_id
                WHERE 
                    bots.hash = ? AND 
                    triggers.trigger_name = ? AND 
                    bots.is_active = ?
                LIMIT 1', 
            [$hash, $data['object']['text'], true]);

            if (!empty($resonse)) {
                $request_params = array(
                    'message' => $resonse[0]->response,
                    'peer_id' => $data['object']['peer_id'],
                    'access_token' => $resonse[0]->token,
                    'v' => '5.87'
                );

                $get_params = http_build_query($request_params);
                file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);

                $job = new AfterResponseMessageAddHisoryJob(
                    $resonse[0]->id,
                    $data['object']['text'], 
                    $data['object']['peer_id']
                );
                $this->dispatch($job);
            }
        }

        return "ok";
    }
}

