<?php

namespace FossBilling\Modules\CustomClientSync;

class CustomClientSync {
    public function __construct() {
        // Initialization code here
    }

    public function receiveClientData() {
        // Implementation to receive data from RabbitMQ
        require_once __DIR__ . '/vendor/autoload.php';

        use PhpAmqpLib\Connection\AMQPStreamConnection;
        use PhpAmqpLib\Message\AMQPMessage;

        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('client_queue', false, false, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($msg) {
            echo " [x] Received ", $msg->body, "\n";
            $data = json_decode($msg->body, true);
            if ($data) {
                $this->saveClientData($data);
            }
        };

        $channel->basic_consume('client_queue', '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    private function saveClientData($data) {
        // Example function to save client data to the database
        $mysqli = new \mysqli('mysql', 'fossbilling', 'fossbilling', 'fossbilling');
        if ($mysqli->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }

        $stmt = $mysqli->prepare("INSERT INTO clients (id, name, email, created_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $data['id'], $data['name'], $data['email'], $data['created_at']);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    }
}

