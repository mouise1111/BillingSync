<?php
/**
 * wordpress api function calls 
 * Incoming data FROM FOSSBilling 
 **/

 /**
 * Creates a new client in Wordpress using the client-rest-api plugin
 * A client needs a name and email to be created
 * @param array $data 
 */ 
function create_wordpress_client($data){
  echo "We have entered the create wordpress client function \n";
  if (isset($data['first_name']) && isset($data['email']) && isset($data['custom_1'])) { //this is from wordpress to fossbilling
    // Prepare the payload for WordPress
    $payload = json_encode([
      'name' => $data['first_name'],
      'email' => $data['email'],
      'custom_1' => $data['custom_1'],
      'birthday' => $data['birthday'],
    ]);
    echo $payload;
    // Initialize cURL session
    $ch = curl_init('http://192.168.122.79:9500/wp-json/clientmanager/v1/clients/from_fossbilling');
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
  
  if (isset($data['email']) && isset($data['first_name']) && isset($data['custom_1'])) { 
    // Prepare the payload for WordPress
    $payload = json_encode([
      'email' => $data['email'],
      'name' => $data['first_name'],
      'custom_1' => $data['custom_1'],
      'birthday' => $data['birthday'],
    ]);

    echo $payload;

    // Initialize cURL session
    $ch = curl_init('http://192.168.122.79:9500/wp-json/clientmanager/v1/clients/from_fossbilling');
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
        $ch = curl_init('http://192.168.122.79:9500/wp-json/clientmanager/v1/clients/from_fossbilling');
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
