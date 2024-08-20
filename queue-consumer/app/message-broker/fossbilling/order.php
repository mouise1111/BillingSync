<?php

/**
 * FOSSBilling API FUNCTIONS for orders
 **/

 /**
 * Creates an order in Fossbilling
 * needs a client_id or custom_1 and a product_id
 * @param array $data
 **/
function create_fossbilling_order($data){
  // Prepare the payload for Fossbilling
  $payload = json_encode([
    'client_custom_1' => $data['client_custom_1'], // substituting the client_id 
    'product_custom_1' => $data['product_custom_1'],
    'origin' => $data['origin'],
    'price' => $data['price']
  ]);
  echo $payload;
  // Initialize cURL session
  $ch = curl_init('http://192.168.122.79/api/admin/order/create');
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
