<?php
//FossBilling API Key
define('FOSS_PASS', "admin:6qTnJGqni9GdlK5VXoHyn3lnswqevDK5");
/**
 * FOSSBilling API FUNCTIONS
 **/

 /**
 * Creates a client in Fossbilling 
 * A client will be deleted by the "custom_1" field 
 * @param array $data
 **/
function create_fossbilling_client($data){
  // Prepare the payload for WordPress
  $payload = json_encode([
    'first_name' => $data['name'],
    'email' => $data['email'],
    'password' => "Securepassword123",
    'custom_1' => $data['custom_1'],
    'birthday' => $data['birthday'],
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
  curl_setopt($ch, CURLOPT_USERPWD, FOSS_PASS); 

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

/**
 * Deletes a client in Fossbilling 
 * A client will be deleted by the "custom_1" field
 * @param array $data
 **/
function delete_fossbilling_client($data){
  // Prepare the payload for WordPress
  $payload = json_encode([
    'custom_1' => $data['custom_1'],
  ]);
  echo $payload;
  // Initialize cURL session
  $ch = curl_init('http://192.168.122.79/api/admin/client/delete');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload)
  ]);

  //basic auth
  curl_setopt($ch, CURLOPT_USERPWD, FOSS_PASS); 
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

/**
 * Updates a client in Fossbilling
 * A client will be updated by the "custom_1" field
 * @param array $data
 **/
function update_fossbilling_client($data) {
    // Prepare the payload for FOSSBilling
    $payload = json_encode([
        'email' => $data['email'],
        'first_name' => $data['name'],
        'custom_1' => $data['custom_1'],
        'origin' => $data['origin'],
        'birthday' => $data['birthday'],
    ]);

    echo $payload;
    
    // Initialize cURL session
    $ch = curl_init('http://192.168.122.79/api/admin/client/update');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);

    // Set up basic authentication
    curl_setopt($ch, CURLOPT_USERPWD, FOSS_PASS);

    // Execute the PUT request
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

