<?php
/**
 * wordpress api function calls for PRODUCTS
 * Incoming data FROM FOSSBilling
 **/

 /**
 * Product a new client in Wordpress using the product-rest-api plugin
 * A client needs a title and type to be created
 * Optional is the category (id)
 * @param array $data
 */
function create_wordpress_product($data){
  echo "We have entered the create wordpress product function \n";
  // we are checking if the data is Incoming from Fossbilling, along with other fields
  if (isset($data['title']) && isset($data['type']) && isset($data['origin']) && $data['origin'] === "fossbilling") { 
    // Prepare the payload for WordPress
    $payload = json_encode([
      'title' => $data['title'],
      'type' => $data['type'],
      'origin' => $data['origin']
    ]);
    echo $payload;
    // Initialize cURL session
    $ch = curl_init('http://192.168.122.79:9500/wp-json/productmanager/v1/products');
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

