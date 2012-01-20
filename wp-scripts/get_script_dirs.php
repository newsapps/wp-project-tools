<?php
/*****
 * Get a list of directories where we should
 * search for scripts
 **/

require_once( 'tools/cli-load.php' );

# print theme script paths
echo get_stylesheet_directory() ."/wp-scripts\n";

if ( get_stylesheet_directory() != get_template_directory() )
    echo get_template_directory() ."/wp-scripts\n";

if ( is_multisite() )
    $plugins = array_keys( get_site_option('active_sitewide_plugins') );

else
    $plugins = array_keys( get_option('active_plugins') );

# print plugin script paths
foreach( $plugins as $plugin )
    echo WP_PLUGIN_DIR .'/'. dirname( $plugin ) ."/wp-scripts\n";
