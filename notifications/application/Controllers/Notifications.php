<?php
// application/Controllers/Notifications.php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Notifications extends ResourceController
{
    public function consumeEvents()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('user_events', false, false, false, false);

        echo ' [*] Waiting for user events. To exit press CTRL+C', "\n";

        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
            // Save the event data to a log file
            log_message('info', 'Received event: ' . $msg->body);
        };

        $channel->basic_consume('user_events', '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
