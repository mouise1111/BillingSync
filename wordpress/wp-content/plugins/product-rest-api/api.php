<?php
/**
 * Plugin Name: Products REST API 
 * Description: Adds custom REST API endpoints for product, including integration with FOSSBilling.
 * Version: 1.0
 * Author: Mouise Bashir
 */
require_once 'RabbitMQPublisher.php';
require_once __DIR__ . '/Models/Product.php';

function product_management_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'frontend/index.html';
    return ob_get_clean();
}
add_shortcode('product_management', 'product_management_shortcode');

// Api endpoints
add_action('rest_api_init', function () {
  // Endpoints for handling product in general
  register_rest_route('productmanager/v1', '/products', [
    'methods' => 'GET',
    'callback' => 'get_products',
  ]);
   register_rest_route('productmanager/v1', '/products/(?P<id>\d+)', [
    'methods' => 'GET',
    'callback' => 'get_product',
   ]);
  register_rest_route('productmanager/v1', '/products', [
    'methods' => 'POST',
    'callback' => 'create_product',
  ]);
});


/**
 * Originating from WordPress and Fossbilling
 *
 /**
  * Get all products
 */
function get_products(WP_REST_Request $request) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'products';
  $results = $wpdb->get_results("SELECT * FROM $table_name");

  return new WP_REST_Response($results, 200);
}

/**
 * Get a single product
 */
function get_product(WP_REST_Request $request) {
  global $wpdb;
  $id = $request['id'];
  $table_name = $wpdb->prefix . 'product';
  $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

  if (is_null($result)) {
    return new WP_REST_Response(['message' => 'Product not found'], 404);
  }

  return new WP_REST_Response($result, 200);
}

/**
 * Create a new product
 */
function create_product(WP_REST_Request $request) {
  global $wpdb;
  $data = $request->get_json_params();
  if (!$data || !isset($data['title']) || !isset($data['type'])) {
    return new WP_REST_Response(['message' => 'Invalid input'], 400);
  }

  $title = sanitize_text_field($data['title']);
  $type = sanitize_text_field($data['type']);
  //$productCategoryId = sanitize_text_field($data['productCategoryId']);
  //$origin = $data['origin'] ?? null;

  $product = new Product($title, $type);

  $table_name = $wpdb->prefix . 'products';
  $wpdb->insert($table_name, [
    'title' => $product->getTitle(),
    'type' => $product->getType(),
  ]);

  $product->setId($wpdb->insert_id);

  if (!isset($data['origin']) || $data['origin'] !== "fossbilling") {
  // Send product data to RabbitMQ
  $publisher = new RabbitMQPublisher();
  $publisher->publish(json_encode([
    'origin' => "wordpress",
    'title' => $product->getTitle(),
    'type' => $product->getType(),
  ]));
  }
  return new WP_REST_Response($product, 201);
}

