<?php
/*
Plugin Name: clients DB Table
Description: Creates a custom database table on plugin activation.
Version: 1.0
Author: Mouise Bashir
*/

// Hook activation function to plugin activation
register_activation_hook(__FILE__, 'create_custom_db_table');

/**
 * Function to create custom database table
 */
function create_custom_db_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'clients';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        email varchar(100) NOT NULL UNIQUE,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        custom_1 VARCHAR(255) UNIQUE,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

