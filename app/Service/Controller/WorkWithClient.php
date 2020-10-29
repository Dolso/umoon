<?php

namespace App\Service\Controller;

use Illuminate\Support\Facades\DB;
use App\Jobs\AfterResponseMessageAddHisoryJob;

class WorkWithClient {


    /**
     * Формируем сообщение или отправляем токен.
     * 
     * @param array $data - данные, которые были в теле запроса
     * @param string $key_bot - ключ бота
     * @return string возвращает ответ
     */
    public function sendMessageOrReturnConfirmationToken(array $data, string $key_bot) : string
    {
        if ($data['type'] == 'confirmation') {

            $confirmation_token = $this->returnConfirmationToken($key_bot);

            if ($confirmation_token === null) {
                return false;
            }

            return 'ok';
            
        } else if ($data['type'] == 'message_new') {
            $resonse = $this->getMessageReply($key_bot, $data['object']['text'], true);

            if (!empty($resonse !== null)) {
                $this->sendMessage($resonse, $data['object']['peer_id']);

                AfterResponseMessageAddHisoryJob::dispatch(
                    $resonse[0]->id,
                    $data['object']['text'], 
                    $data['object']['peer_id']
                );
            }
        }

        return "ok";
    }

    /**
     * Ищет бота и возращает токен
     * 
     * @param string $key_bot - ключ бота
     * @return string возвращает токен
     */
    private function getConfirmationToken (string $key_bot) : ?string
    {
        $bot = DB::select('
            SELECT 
                confirmation_token
            FROM 
                bots
            WHERE 
                hash = ?
            LIMIT 1', 
        [$key_bot]);

        if (!empty($bot[0]->confirmation_token)) {
            return response($bot[0]->confirmation_token);
        } else {
            return null;
        }
    }

    /**
     * Ищет бота и возращает токен
     * 
     * @param string $key_bot - ключ бота
     * @param string $text - сообщение
     * @param string $is_active - активность бота
     * @return string возвращает токен бота
     */
    private function getMessageReply (string $key_bot, string $text, bool $is_active) : ?array
    {
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
        [$key_bot, $text, $is_active]);

        if (!empty($resonse)) {
            return $resonse;
        } else {
            return null;
        }
    }

    /**
     * Отправляет сообщение
     * 
     * @param array $key_bot - ключ бота
     */
    private function sendMessage (array $resonse, int $peer_id) : void
    {
        $request_params = array(
            'message' => $resonse[0]->response,
            'peer_id' => $peer_id,
            'access_token' => $resonse[0]->token,
            'v' => '5.87'
        );

        $get_params = http_build_query($request_params);
        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
    }
}