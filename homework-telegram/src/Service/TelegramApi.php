<?php

namespace App\src\Service;
use App\src\Cache\Redis;
use PHPUnit\Framework\MockObject\Stub\ReturnSelf;
use Predis\Client;

class TelegramApi {
    private $url = "";
    private int $offset;
    private Redis $redis;

    function __construct( $url=null, $token = null, $redis = null ) {
        if ( is_null( $token ) ) {
            $token = $_ENV['TELEGRAM_TOKEN'];
        }
        $this->url = $url ? $url : "https://api.telegram.org/bot" . $token . "/";
        $this->offset = 0;


        $client = new Client([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379
        ]);

        if ($redis == null) {
            $this->redis = new Redis($client);
        } else {
            $this->redis = $redis;
        }

    }

    /**
     * Send get request to telegrams bot and return all messages
     * @return array messages from telegrams bot
     */
    public function getMessages(): array {

        $this->offset = $this->redis->get("telegramBot:offset", 0);

        $response = file_get_contents( $this->url . "getUpdates?offset=" . $this->offset+1);
        $result = json_decode( $response, true );

        if (!$result['ok']) {
            return $result;
        }

        
        if (count($result['result']) > 0) {
            $oldmessages = json_decode($this->redis->get("telegramBot:oldMessages", false), true);

            if ($oldmessages) {
                array_unshift($result['result'], ...$oldmessages);

                $this->redis->set('telegramBot:oldMessages', json_encode($oldmessages));
                $this->redis->set('telegramBot:offset', end($oldmessages)['update_id']);

                return $oldmessages;
            } else {
                $this->redis->set('telegramBot:oldMessages', json_encode($result['result']));
                $this->redis->set('telegramBot:offset', end($result['result'])['update_id']);

                return $result;
            }
        } else {
            $oldMessages = json_decode($this->redis->get('telegramBot:oldMessages'), true);

            if ($oldMessages) {
                $cacheResult = [
                    'ok' => true,
                    'result' => $oldMessages
                ];
                return $cacheResult;
            } else {
                return $result;
            }
        }
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