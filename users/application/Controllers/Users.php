<?php
// application/Controllers/Users.php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Users extends ResourceController
{
    public function post()
    {
        $data = $this->request->getJSON();
        if ($data) {
            // Save user data to a database or log file
            // For simplicity, we'll log the data
            log_message('info', 'User data: ' . print_r($data, true));

            // Publish an event to RabbitMQ
            $this->publishEvent($data);

            return $this->respondCreated(['message' => 'User data saved and event published.']);
        } else {
            return $this->failValidationError('Invalid data');
        }
    }

    private function publishEvent($data)
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('user_events', false, false, false, false);

        $msg = new AMQPMessage(json_encode($data));
        $channel->basic_publish($msg, '', 'user_events');

        $channel->close();
        $connection->close();
    }
}
