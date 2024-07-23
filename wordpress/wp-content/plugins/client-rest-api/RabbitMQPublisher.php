<?php
require_once __DIR__.'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher {
  private $connection;
  private $channel;

  public function __construct() {
    $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest'); // Adjust the connection details as needed
    $this->channel = $this->connection->channel();
    $this->channel->queue_declare('client_queue', false, false, false, false);
  }

  public function publish($message) {
    $msg = new AMQPMessage($message);
    $this->channel->basic_publish($msg, '', 'client_queue');
  }

  public function __destruct() {
    $this->channel->close();
    $this->connection->close();
  }
}

