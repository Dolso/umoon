<?php

namespace App\Service\Job;

class MessageInformation {

    public function getFullUserName (int $peer_id, string $token) : string
    {
        $json_full_name = file_get_contents("https://api.vk.com/method/users.get?user_ids={$peer_id}&v=5.87&access_token={$token}");

        if (!empty($json_full_name)) {
            $first_name_and_lastname = json_decode($json_full_name, true);
            
            if (isset($first_name_and_lastname["response"][0]["last_name"]) and !empty($first_name_and_lastname["response"][0]["last_name"])) {
                $name = $first_name_and_lastname["response"][0]["last_name"]. " " .$first_name_and_lastname["response"][0]["first_name"];

                return $name;
            }
        }

        return "";
    }
}
