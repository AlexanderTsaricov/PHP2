<?php

namespace App\src\Service;

class TelegramApi {
    // Не вижу смысла реализовывать через интерфейс
    private $url = "";

    function __construct( $url=null, $token = null ) {
        if ( is_null( $token ) ) {
            $token = $_ENV['TELEGRAM_TOKEN'];
        }
        $this->url = $url ? $url : "https://api.telegram.org/bot" . $token . "/";
    }

    /**
     * Send get request to telegrams bot and return all messages
     * @return array messages from telegrams bot
     */
    public function getMessages(): array {
        $response = file_get_contents( $this->url . "getUpdates" );
        $messages = json_decode( $response, true );
        return $messages;
    }

    /**
     * Send poset request to telegrams bot and return true if response is ok
     * @param mixed $message message to telegrams user
     * @param mixed $user_id id telegrams user
     * @return array fisrt element - result response. If response is not ok - second element: error code
     */
    public function sendMessage( $message, $user_id): array {
        $sendUrl = $this->url ."sendMessage";
        $data = [
            "chat_id"=> $user_id,
            "text"=> $message,
        ];
        $options = [
            'http' => [
                'method' => 'POST',
                'content' => http_build_query( $data ),
                'header'  => "Content-Type: application/x-www-form-urlencoded",
                'ignore_errors' => true
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($sendUrl, false, $context);
        $result = json_decode( $response, true );

        return $result;
    }
}