<?php
/*****
 * Wordpress automated setup
 * install.php
 *
 * Setup a WordPress instance and enable the network.
 *   -d     Delete default WordPress posts
 *
 *****/

define('WP_INSTALLING', true);

# Should we delete the automatically created wordpress posts?
$options = getopt("d");

require_once( 'tools/cli-load.php' );

/** Load WordPress Administration Upgrade API */
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

global $settings;

$wp_install_result = wp_install(
    $settings['install']['weblog_title'],
    $settings['install']['user_name'],
    $settings['install']['admin_email'],
    $settings['install']['public'],
    '',
    $settings['install']['admin_password']
);

if (is_wp_error( $wp_install_result )) {
    var_dump($wp_install_result);
    die("Wordpress install failed");
}

if ( !empty( $options['d'] ) ) {
    // Delete the first post
    wp_delete_post( 1, true );

    // Delete the default about page
    wp_delete_post( 2, true );
}

print("Wordpress install finished\n");

