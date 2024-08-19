<?php
/**
 * Plugin Name: Client REST API Extended
 * Description: Adds custom REST API endpoints for clients, including integration with FOSSBilling.
 * Version: 1.0
 * Author: Mouise Bashir
 */
require_once 'RabbitMQPublisherForClients.php';
require_once __DIR__ . '/Models/Client.php';

// Enqueue frontend styles and scripts
function enqueue_client_management_scripts() {
    wp_enqueue_style('client-management-styles', plugins_url('frontend/styles.css', __FILE__));
    wp_enqueue_script('client-management-scripts', plugins_url('frontend/app.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_client_management_scripts');

// Register shortcode for embedding the frontend
function client_management_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'frontend/index.html';
    return ob_get_clean();
}
add_shortcode('client_management', 'client_management_shortcode');

// Api endpoints
add_action('rest_api_init', function () {
  // Endpoints for handling clients in general
  register_rest_route('clientmanager/v1', '/clients', [
    'methods' => 'GET',
    'callback' => 'get_clients',
  ]);
  register_rest_route('clientmanager/v1', '/clients/(?P<id>\d+)', [
    'methods' => 'PUT',
    'callback' => 'update_client',
  ]);
  register_rest_route('clientmanager/v1', '/clients/(?P<id>\d+)', [
    'methods' => 'DELETE',
    'callback' => 'delete_client',
  ]);
   register_rest_route('clientmanager/v1', '/clients/(?P<id>\d+)', [
    'methods' => 'GET',
    'callback' => 'get_client',
   ]);

  register_rest_route('clientmanager/v1', '/clients', [
    'methods' => 'POST',
    'callback' => 'create_client',
  ]);
   register_rest_route('clientmanager/v1', '/clients/', [
    'methods' => 'PUT',
    'callback' => 'update_client',
  ]);
   register_rest_route('clientmanager/v1', '/clients/', [
    'methods' => 'DELETE',
    'callback' => 'delete_client_by_custom_1',
  ]);
  register_rest_route('clientmanager/v1', '/clients/by_custom_1', [
    'methods' => 'POST',
    'callback' => 'get_client_by_custom_1_json',
  ]);
  // Endpoints specifically for FOSSBilling to WordPress integration
  register_rest_route('clientmanager/v1', '/clients/from_fossbilling', [
    'methods' => 'POST',
    'callback' => 'create_client_from_fossbilling',
  ]);
   register_rest_route('clientmanager/v1', '/clients/from_fossbilling/', [
    'methods' => 'PUT',
    'callback' => 'update_client_from_fossbilling_using_custom_1',
  ]);
  register_rest_route('clientmanager/v1', '/clients/from_fossbilling/', [
    'methods' => 'DELETE',
    'callback' => 'delete_client_from_fossbilling_using_custom_1',
  ]);
});

/**
 * Create a new client from FOSSBilling
 */
function create_client_from_fossbilling(WP_REST_Request $request) {
  global $wpdb;
  $data = $request->get_json_params();
  $name = sanitize_text_field($data['name']);
  $email = sanitize_email($data['email']);
  $created_at = new DateTime();
  $birthday = $data['birthday'];
  $custom_1 = $data['custom_1'] ?? uniqid();
  $client = new Client($name, $email, $created_at, $custom_1, $birthday);

  $table_name = $wpdb->prefix . 'clients';
  $wpdb->insert($table_name, [
    'name' => $client->getName(),
    'email' => $client->getEmail(),
    'created_at' => $client->getCreatedAt()->format('Y-m-d H:i:s'),
    'birthday' => $client->getDateOfBirth(),
    'custom_1' => $client->getCustom1(),
  ]);

  $client->setId($wpdb->insert_id);
  return new WP_REST_Response($client, 201);
}

/**
 * Update an existing client from FOSSBilling
 */
function update_client_from_fossbilling_using_custom_1(WP_REST_Request $request) {
    global $wpdb;
    $custom_1 = $request->get_param('custom_1');
    $data = $request->get_json_params();
    $name = sanitize_text_field($data['name']);
    $email = sanitize_email($data['email']);
    $birthday = $data['birthday'];
    $table_name = $wpdb->prefix . 'clients';

    $client_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE custom_1 = %s", $custom_1));

    if (is_null($client_data)) {
        return new WP_REST_Response(['message' => 'Client not found'], 404);
    }

    $client = new Client($name, $email, new DateTime($client_data->created_at), $custom_1, $birthday);
    $client->setId($client_data->id);

    $wpdb->update($table_name, [
        'name' => $client->getName(),
        'email' => $client->getEmail(),
        'birthday' => $client->getDateOfBirth(),
    ], ['id' => $client_data->id]);

    return new WP_REST_Response($client, 200);
}

/**
 * Delete a client from FOSSBilling
 */
function delete_client_from_fossbilling_using_custom_1(WP_REST_Request $request) {
  global $wpdb;
  $custom_1 = $request->get_param('custom_1');

  $table_name = $wpdb->prefix . 'clients';
  $client_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE custom_1 = %s", $custom_1));

  if (is_null($client_data)) {
    return new WP_REST_Response(['message' => 'Client not found'], 404);
  }

  $wpdb->delete($table_name, ['id' => $client_data->id]);

  return new WP_REST_Response(['message' => 'Client deleted'], 200);
}


/**
 * Originating from WordPress
 /**
  * Get all clients
 */
function get_clients(WP_REST_Request $request) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'clients';
  $results = $wpdb->get_results("SELECT * FROM $table_name");

  return new WP_REST_Response($results, 200);
}

/**
 * Get a single client
 */
function get_client(WP_REST_Request $request) {
  global $wpdb;
  $id = $request['id'];
  $table_name = $wpdb->prefix . 'clients';
  $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

  if (is_null($result)) {
    return new WP_REST_Response(['message' => 'Client not found'], 404);
  }

  return new WP_REST_Response($result, 200);
}

/**
 * Create a new client
 */
function create_client(WP_REST_Request $request) {
  global $wpdb;
  $data = $request->get_json_params();
  $name = sanitize_text_field($data['name']);
  $email = sanitize_email($data['email']);
  $created_at = new DateTime();
  $custom_1 = $data['custom_1'] ?? null;
  $birthday = $data['birthday'];
  $client = new Client($name, $email, $created_at, $custom_1, $birthday);

  $table_name = $wpdb->prefix . 'clients';
  $wpdb->insert($table_name, [
    'name' => $client->getName(),
    'email' => $client->getEmail(),
    'created_at' => $client->getCreatedAt()->format('Y-m-d H:i:s'),
    'custom_1' => $client->getCustom1(),
    'birthday' => $client->getDateOfBirth(),
  ]);

  $client->setId($wpdb->insert_id);
  // Send client data to RabbitMQ
  $publisher = new RabbitMQPublisherForClients();
  $publisher->publish(json_encode([
    'method' => "create",
    'id' => $client->getId(),
    'name' => $client->getName(),
    'email' => $client->getEmail(),
    'created_at' => $client->getCreatedAt()->format('Y-m-d H:i:s'),
    'birthday' => $client->getDateOfBirth(),
    'custom_1' => $client->getCustom1(),
  ]));
  return new WP_REST_Response($client, 201);
}

/**
 * Update an existing client
 */
function update_client(WP_REST_Request $request) {
  global $wpdb;
  $custom_1 = $request->get_param('custom_1');
  $data = $request->get_json_params();
  $name = sanitize_text_field($data['name']);
  $email = sanitize_email($data['email']);
  $birthday = $data['birthday'];

  $table_name = $wpdb->prefix . 'clients';
  $client_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE custom_1 = %s", $custom_1));

  if (is_null($client_data)) {
    return new WP_REST_Response(['message' => 'Client not found'], 404);
  }

  $client = new Client($name, $email, new DateTime($client_data->created_at), $custom_1, $birthday);
  $client->setId($client_data->id);

  $wpdb->update($table_name, [
    'name' => $client->getName(),
    'email' => $client->getEmail(),
    'birthday' => $client->getDateOfBirth(),
  ], ['id' => $client_data->id]);

  $publisher = new RabbitMQPublisherForClients();
  $publisher->publish(json_encode([
    'method' => "update",
    'origin' => "wordpress",
    'name' => $client->getName(),
    'email' => $client->getEmail(),
    'birthday' => $client->getDateOfBirth(),
    'custom_1' => $client->getCustom1(),
  ]));

  //return new WP_REST_Response($client, 200);
  return new WP_REST_Response(['message' => 'Client updated successfully'], 200);
}

/**
 * Updates a client by custom_1
 */
function update_client_by_custom_1(WP_REST_Request $request) {
  return update_client($request);
}
/**
 * Delete a client by ID or custom_1
 */
function delete_client(WP_REST_Request $request) {
  global $wpdb;
  $id = $request['id'];
  $custom_1 = $request->get_param('custom_1');

  $table_name = $wpdb->prefix . 'clients';

  if (!empty($id)) {
    $client_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
  } else if (!empty($custom_1)) {
    $client_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE custom_1 = %s", $custom_1));
  } else {
    return new WP_REST_Response(['message' => 'Client ID or custom_1 is required'], 400);
  }

  if (is_null($client_data)) {
    return new WP_REST_Response(['message' => 'Client not found'], 404);
  }

  $wpdb->delete($table_name, ['id' => $client_data->id]);

  $publisher = new RabbitMQPublisherForClients();
  $publisher->publish(json_encode([
    'method' => "delete",
    'custom_1' => $client->getCustom1(),
  ]));


  return new WP_REST_Response(['message' => 'Client deleted'], 200);
}

/**
 * Delete a client by custom_1
 */
/**
 * Delete a client by custom_1
 */
function delete_client_by_custom_1(WP_REST_Request $request) {
  global $wpdb;
  $custom_1 = $request->get_param('custom_1');

  if (empty($custom_1)) {
    return new WP_REST_Response(['message' => 'custom_1 is required'], 400);
  }

  $table_name = $wpdb->prefix . 'clients';
  $client_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE custom_1 = %s", $custom_1));

  if (is_null($client_data)) {
    return new WP_REST_Response(['message' => 'Client not found'], 404);
  }

  $wpdb->delete($table_name, ['id' => $client_data->id]);

  // Publish the deletion event to RabbitMQ
  $publisher = new RabbitMQPublisherForClients();
  $publisher->publish(json_encode([
    'method' => "delete",
    'custom_1' => $custom_1,
  ]));

  return new WP_REST_Response(['message' => 'Client deleted'], 200);
}

//retrieve a client by custom_1
function get_client_by_custom_1_json(WP_REST_Request $request) {
  global $wpdb;

  // Get JSON data from the request body
  $data = $request->get_json_params();

  if (!isset($data['custom_1'])) {
    return new WP_REST_Response(['message' => 'custom_1 field is required'], 400);
  }

  $custom_1 = sanitize_text_field($data['custom_1']);
  $table_name = $wpdb->prefix . 'clients';

  $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE custom_1 = %s", $custom_1));

  if (is_null($result)) {
    return new WP_REST_Response(['message' => 'Client not found'], 404);
  }

  // Return the client data as a JSON response
  $client_data = [
    'id' => $result->id,
    'name' => $result->name,
    'email' => $result->email,
    'created_at' => $result->created_at,
    'birthday' => $result->birthday,
    'custom_1' => $result->custom_1
  ];

  return new WP_REST_Response($client_data, 200);
}

