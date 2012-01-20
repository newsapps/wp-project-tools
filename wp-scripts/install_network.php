<?php
/*****
 * Wordpress automated setup
 * install_network.php
 *
 * Setup a WordPress network.
 *
 *****/

define('WP_INSTALLING', true);

require_once( 'tools/cli-load.php' );

# Are we making a network today?
if ( empty( $settings['network'] ) )
    die( "Skipping network setup\n" );

# Load WordPress Administration Upgrade API
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

# We need to create references to ms global tables to enable Network.
foreach ( $wpdb->tables( 'ms_global' ) as $table => $prefixed_table )
    $wpdb->$table = $prefixed_table;

install_network();

$ms_install_result = populate_network(
    $settings['install']['network_id'],
    $settings['install']['hostname'],
    $settings['install']['admin_email'],
    $settings['install']['network_title'],
    $settings['install']['base'],
    $settings['install']['subdomain_install']
);

if (is_wp_error( $ms_install_result ) && $ms_install_result->get_error_code() != 'no_wildcard_dns') {
    print($ms_install_result->get_error_message() . "\n");
    die("Network setup failed\n");
}

print("Network setup finished\n");
