<?php
require_once './vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

function consumeMessages()
{
  $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
  $channel = $connection->channel();

  $channel->queue_declare('fossbilling_to_wordpress_queue', false, false, false, false); // change this to fossbilling_to_wordpress_queue
  $channel->queue_declare('wordpress_to_fossbilling_queue', false, false, false, false);

  echo " [*] Waiting for messages. To exit press CTRL+C\n";

  $callback_F_to_W = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";

    // Decode the message body
    $data = json_decode($msg->body, true);
    $method = $data['method'];
    switch ($method) {
    case 'create':
      create_wordpress_client($data);
      //create_fossbilling_client($data);
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

  $channel->basic_consume('fossbilling_to_wordpress_queue', '', false, true, false, false, $callback_F_to_W);
  $channel->basic_consume('wordpress_to_fossbilling_queue', '', false, true, false, false, $callback);

  while ($channel->is_consuming()) {
    $channel->wait();
    // Sleep for 5 seconds before checking again
    sleep(5);
  }

  $channel->close();
  $connection->close();
}

/**
 * Creates a new client in Wordpress using the client-rest-api plugin
 * A client needs a name and email to be created
 * @param array $data 
 */ 
function create_wordpress_client($data){
  echo "Email of the to be deleteed client: " . $data['email'] . "\n";
  echo "We have entered the create wordpress client function \n";
  if (isset($data['first_name']) && isset($data['email']) && isset($data['custom_1'])) { //this is from wordpress to fossbilling
    // Prepare the payload for WordPress
    $payload = json_encode([
      'name' => $data['first_name'],
      'email' => $data['email'],
      'custom_1' => $data['custom_1'],
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
}

/**
 * Updates a client in Wordpress using the client-rest-api plugin
 * @param array $data 
 */ 
function update_wordpress_client($data){
  echo "we have entered the update wordpress client function";
  
  if (isset($data['email']) && isset($data['first_name'])) { //this is from wordpress to fossbilling
    // Prepare the payload for WordPress
    $payload = json_encode([
      'email' => $data['email'],
      'name' => $data['first_name'],
    ]);

    echo $payload;

    // Initialize cURL session
    $ch = curl_init('http://192.168.122.79:9500/wp-json/myplugin/v1/clients');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
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
}

/**
 * Deletes a client in WordPress using the client-rest-api plugin
 * Client gets deleted by their custom_1 field
 * @param array $data
 */
function delete_wordpress_client($data){
    echo "Entered the delete WordPress client function \n";

    if (isset($data['custom_1'])) {
        // Prepare the payload for WordPress
        $payload = json_encode([
            'custom_1' => $data['custom_1'],
        ]);
        echo $payload . "\n";
        
        // Initialize cURL session
        $ch = curl_init('http://192.168.122.79:9500/wp-json/myplugin/v1/clients');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        ]);

        // Execute the DELETE request
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
        echo "Invalid message format: custom_1 field is missing\n";
    }
}

/**
 * Creates a client in Fossbilling 
 * A client will be deleted by their mail
 * @param array $data
 **/
function create_fossbilling_client($data){
  // Prepare the payload for WordPress
  $payload = json_encode([
    'first_name' => $data['name'],
    'last_name' => "change",
    'email' => $data['email'],
    'password' => "Securepassword123"
  ]);
  echo $payload;
  // Initialize cURL session
  $ch = curl_init('http://192.168.122.79/api/admin/client/create');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload)
  ]);

  //basic auth
  curl_setopt($ch, CURLOPT_USERPWD, "admin:CUCabpVhp92744tMq9Ekr7Qpq3XQUHM0"); 

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
}
