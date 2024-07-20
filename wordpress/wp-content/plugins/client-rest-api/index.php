<?php
/**
 * Plugin Name: Client REST API
 * Description: Adds a custom REST API endpoint for clients.
 * Version: 1.1
 * Author: Mouise Bashir
 */

require_once 'Client.php';
//require_once plugin_dir_path(__FILE__) . 'Client.php';
// Hook into the REST API initialization action
add_action('rest_api_init', function () {
  register_rest_route('myplugin/v1', '/clients', [
    'methods' => 'GET',
    'callback' => 'get_clients',
  ]);
  register_rest_route('myplugin/v1', '/clients/(?P<id>\d+)', [
    'methods' => 'GET',
    'callback' => 'get_client',
  ]);
  register_rest_route('myplugin/v1', '/clients', [
    'methods' => 'POST',
    'callback' => 'create_client',
  ]);
  register_rest_route('myplugin/v1', '/clients/(?P<id>\d+)', [
    'methods' => 'PUT',
    'callback' => 'update_client',
  ]);
  register_rest_route('myplugin/v1', '/clients/(?P<id>\d+)', [
    'methods' => 'DELETE',
    'callback' => 'delete_client',
  ]);
});

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

  $client = new Client($name, $email, $created_at);

  $table_name = $wpdb->prefix . 'clients';
  $wpdb->insert($table_name, [
    'name' => $client->getName(),
    'email' => $client->getEmail(),
    'created_at' => $client->getCreatedAt()->format('Y-m-d H:i:s'),
  ]);

  $client->setId($wpdb->insert_id);

  return new WP_REST_Response($client, 201);
}

/**
 * Update an existing client
 */
function update_client(WP_REST_Request $request) {
  global $wpdb;
  $id = $request['id'];
  $data = $request->get_json_params();
  $name = sanitize_text_field($data['name']);
  $email = sanitize_email($data['email']);

  $table_name = $wpdb->prefix . 'clients';
  $client_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

  if (is_null($client_data)) {
    return new WP_REST_Response(['message' => 'Client not found'], 404);
  }

  $client = new Client($name, $email, new DateTime($client_data->created_at));
  $client->setId($id);

  $wpdb->update($table_name, [
    'name' => $client->getName(),
    'email' => $client->getEmail(),
  ], ['id' => $id]);

  return new WP_REST_Response($client, 200);
}

/**
 * Delete a client
 */
function delete_client(WP_REST_Request $request) {
  global $wpdb;
  $id = $request['id'];

  $table_name = $wpdb->prefix . 'clients';
  $client_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

  if (is_null($client_data)) {
    return new WP_REST_Response(['message' => 'Client not found'], 404);
  }

  $wpdb->delete($table_name, ['id' => $id]);

  return new WP_REST_Response(['message' => 'Client deleted'], 200);
}


