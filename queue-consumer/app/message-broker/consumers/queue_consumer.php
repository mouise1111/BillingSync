<?php
require_once './vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

function consumeMessages()
{
  $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
  $channel = $connection->channel();

  $channel->queue_declare('client_queue', false, false, false, false);
  $channel->queue_declare('wordpress_to_fossbilling_queue', false, false, false, false);

  echo " [*] Waiting for messages. To exit press CTRL+C\n";

  $callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";

    // Decode the message body
    $data = json_decode($msg->body, true);

    // Check if data is valid
    if (isset($data['first_name']) && isset($data['email']) && isset($data['last_name'])) {
      // Prepare the payload for WordPress
      $payload = json_encode([
        'name' => $data['first_name'],
        'email' => $data['email'],
        'created_at' => $data['created_at']
      ]);
      echo $payload;
      // Initialize cURL session
      $ch = curl_init('http://192.168.122.79:9500/wp-json/myplugin/v1/clients');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
      ]);

      // Execute the POST request
      $response = curl_exec($ch);

      // Check for cURL errors
      if ($response === false) {
        echo 'cURL Error: ' . curl_error($ch) . "\n";
      } else {
        echo 'Response: ' . $response . "\n";
      }

      // Close cURL session
      curl_close($ch);
    } else {
      echo "Invalid message format\n";
    }

    // Log the message
    file_put_contents('/var/log/consumer.log', $msg->body . PHP_EOL, FILE_APPEND);
  };

  $channel->basic_consume('client_queue', '', false, true, false, false, $callback);
  $channel->basic_consume('wordpress_to_fossbilling_queue', '', false, true, false, false, $callback);

  while ($channel->is_consuming()) {
    $channel->wait();
    // Sleep for 5 seconds before checking again
    sleep(5);
  }

  $channel->close();
  $connection->close();
}


