<?php
/**
 * Plugin Name: Order REST API 
 * Description: Adds custom REST API endpoints for orders, including integration with FOSSBilling.
 * Version: 1.0
 * Author: Mouise Bashir
 */
require_once 'RabbitMQPublisherForOrders.php';
require_once __DIR__ . '/Models/Order.php';

// Api endpoints
add_action('rest_api_init', function () {
  // Endpoints for handling product in general
  register_rest_route('ordermanager/v1', '/orders', [
    'methods' => 'GET',
    'callback' => 'get_orders',
  ]);
   register_rest_route('ordermanager/v1', '/orders/(?P<id>\d+)', [
    'methods' => 'GET',
    'callback' => 'get_order',
   ]);
  register_rest_route('ordermanager/v1', '/orders', [
    'methods' => 'POST',
    'callback' => 'create_order',
  ]);
});

function order_management_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'frontend/index.html';
    return ob_get_clean();
}
add_shortcode('order_management', 'order_management_shortcode');


/**
 * Originating from WordPress and Fossbilling
 *
 /**
  * Get all products
 */
function get_orders(WP_REST_Request $request) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'orders';
  $results = $wpdb->get_results("SELECT * FROM $table_name");

  return new WP_REST_Response($results, 200);
}

/**
 * Get a single order
 */
function get_order(WP_REST_Request $request) {
  global $wpdb;
  $id = $request['id'];
  $table_name = $wpdb->prefix . 'orders';
  $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

  if (is_null($result)) {
    return new WP_REST_Response(['message' => 'Order not found'], 404);
  }

  return new WP_REST_Response($result, 200);
}

/**
 * Create a new order
 */
function create_order(WP_REST_Request $request) {
  global $wpdb;
  $data = $request->get_json_params();
  if (!$data || !isset($data['client_custom_1']) || !isset($data['product_custom_1'])) {
    return new WP_REST_Response(['message' => 'Invalid input'], 400);
  }

  $client_custom_1 = $data['client_custom_1'];
  $product_custom_1 = $data['product_custom_1'];
  $price = $data['price'] ?? null;
  

  $order = new Order($client_custom_1, $product_custom_1, $price);

  $table_name = $wpdb->prefix . 'orders';
  $wpdb->insert($table_name, [
    'client_custom_1' => $order->getClientCustom1(),
    'product_custom_1' => $order->getProductCustom1(),
    'price' => $order->getPrice()
  ]);

  $order->setId($wpdb->insert_id);

  if (!isset($data['origin']) || $data['origin'] !== "fossbilling") {
  // Send product data to RabbitMQ
  $publisher = new RabbitMQPublisherForOrders();
  $publisher->publish(json_encode([
    'origin' => "wordpress",
    'client_custom_1' => $order->getClientCustom1(),
    'product_custom_1' => $order->getProductCustom1(),
    'price' => $order->getPrice(),
  ]));
  }
  return new WP_REST_Response($order, 201);
}

