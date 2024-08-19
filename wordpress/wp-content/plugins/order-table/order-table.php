<?php
/*
Plugin Name: Order DB Table
Description: Creates a custom database table on plugin activation.
Version: 1.0
Author: Mouise Bashir
*/

// Hook activation function to plugin activation
register_activation_hook(__FILE__, 'create_orders_db_table');

/**
 * Function to create custom database table
 */
function create_orders_db_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'orders';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        client_custom_1 tinytext NOT NULL,
        product_custom_1 tinytext NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

