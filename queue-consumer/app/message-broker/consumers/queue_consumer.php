<?php
require_once './vendor/autoload.php';
require_once __DIR__ . '/../fossbilling/client.php';
//require_once '/../fossbilling/product.php';
require_once __DIR__ . '/../wordpress/client.php';
//require_once '/../wordpress/product.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

function consumeMessages()
{
  $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
  $channel = $connection->channel();

  $channel->queue_declare('fossbilling_to_wordpress_queue', false, false, false, false); // change this to fossbilling_to_wordpress_queue
  $channel->queue_declare('wordpress_to_fossbilling_queue', false, false, false, false);
  $channel->queue_declare('products_fossbilling_to_wordpress_queue', false, false, false, false);

  echo " [*] Waiting for messages. To exit press CTRL+C\n";

  $callback_F_to_W = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";

    // Decode the message body
    $data = json_decode($msg->body, true);
    $method = $data['method'];
    switch ($method) {
    case 'create':
      create_wordpress_client($data);
      break;
    case 'delete':
      delete_wordpress_client($data);
      break;
    case 'update':
      update_wordpress_client($data);
    default:
      echo "Method should be one of these three: 'create', 'delete' and 'update'\n";
      break;
    }
  };

  $callback_W_to_F = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";

    // Decode the message body
    $data = json_decode($msg->body, true);
    $method = $data['method'];
    switch ($method) {
    case 'create':
      create_fossbilling_client($data);
      break;
    case 'delete':
      delete_fossbilling_client($data);
      break;
    case 'update':
      update_fossbilling_client($data);
    default:
      echo "Method should be one of these three: 'create', 'delete' and 'update'\n";
      break;
    }
  };

  $callback_products_Foss_to_Word = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";

    // Decode the message body
    $data = json_decode($msg->body, true);
    $method = $data['method'];
    switch ($method) {
    case 'create':
      create_fossbilling_product($data);
      break;
    case 'delete':
      delete_fossbilling_product($data);
      break;
    case 'update':
      update_fossbilling_product($data);
    default:
      echo "Method should be one of these three: 'create', 'delete' and 'update'\n";
      break;
    }
  };


  $channel->basic_consume('fossbilling_to_wordpress_queue', '', false, true, false, false, $callback_F_to_W);
  $channel->basic_consume('wordpress_to_fossbilling_queue', '', false, true, false, false, $callback_W_to_F);
  $channel->basic_consume('products_fossbilling_to_wordpress_queue', '', false, true, false, false, $callback_products_Foss_to_Word);

  while ($channel->is_consuming()) {
    $channel->wait();
    // Sleep for 5 seconds before checking again
    sleep(5);
  }

  $channel->close();
  $connection->close();
}
