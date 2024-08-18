<?php

/**
 * FOSSBilling API FUNCTIONS for products
 **/

 /**
 * Creates a product in Fossbilling
 * @param array $data
 **/
function create_fossbilling_product($data){
  // Prepare the payload for Fossbilling
  $payload = json_encode([
    'title' => $data['title'],
    'type' => $data['type'],
    'origin' => $data['origin']
  ]);
  echo $payload;
  // Initialize cURL session
  $ch = curl_init('http://192.168.122.79/api/admin/product/prepare');
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
