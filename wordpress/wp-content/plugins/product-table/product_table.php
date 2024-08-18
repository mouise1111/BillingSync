<?php
/*
Plugin Name: products DB Table
Description: Creates a custom database table on plugin activation.
Version: 1.0
Author: Mouise Bashir
*/

// Hook activation function to plugin activation
register_activation_hook(__FILE__, 'create_products_db_table');

/**
 * Function to create custom database table
 */
function create_products_db_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'products';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title tinytext NOT NULL,
        type tinytext NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

