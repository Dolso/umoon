<?php

namespace App\Jobs;

use Illuminate\Support\Facades\DB;
use App\Service\Job\MessageInformation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AfterResponseMessageAddHisoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $bot_id;
    private $message_text;
    private $peer_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bot_id, $message_text, $peer_id)
    {
        $this->bot_id = $bot_id;
        $this->message_text = $message_text;
        $this->peer_id = $peer_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MessageInformation $message_information)
    {
        $bot = DB::select(
            'SELECT token FROM bots WHERE id = ? LIMIT 1', 
            [$this->bot_id]
        );

        if (!empty($bot)) {
            $name = $message_information->getFullUserName($this->peer_id, $bot[0]->token);

            DB::insert(
                'INSERT INTO messages (text, peer_id, name, bot_id) values (?, ?, ?, ?)', 
                [$this->message_text, $this->peer_id, $name, $this->bot_id]
            );
        }
    }
}
