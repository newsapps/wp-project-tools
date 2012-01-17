<?php

/**
 * Setup the cli and run WordPress
 * Made this a function to keep variables local
 **/
function cli_load_main() {
    if ( !defined('STDIN') ) 
        throw new Exception("Please run this script from the command line."); 

    # Setup the project path
    $project_path = dirname( __DIR__ );

    # Figure out if we're running in development, staging or production
    if ( isset($_SERVER['DEPLOYMENT_TARGET']) )
        $settings_file = $_SERVER['DEPLOYMENT_TARGET'] . '_settings.json';
    else $settings_file = 'development_settings.json';

    # Load our settings JSON file for this deployment environment
    global $settings;
    $settings_path = $project_path . '/data/' . $settings_file;
    $settings = json_decode( file_get_contents( $settings_path ), true );

    # Catch JSON errors
    if ( $settings == NULL )
        throw new Exception( "Settings file '$settings_path' could not be parsed\n" );

    //define('DOING_AJAX', true);

    # Don't try to process this like a request
    define('WP_USE_THEMES', false);

    # Spoof some http request stuff
    global $_SERVER;
    $_SERVER["HTTP_HOST"] = $settings['install']['hostname'];
    $_SERVER["SERVER_NAME"] = $settings['install']['hostname'];
    $_SERVER["REQUEST_URI"] = "/cli";
    $_SERVER["REQUEST_METHOD"] = "GET";

    # Set the ABSPATH so WordPress can find its files
    define( 'ABSPATH', $project_path . '/wordpress/' );

    if ( ! defined('WP_SITEURL') )
        define( 'WP_SITEURL', "http://".$settings['install']['hostname'] );

    # Load WordPress!
    if ( file_exists( $project_path . '/wp-config.php' ) )
        require_once( $project_path . '/wp-config.php' );

    // define('WP_ADMIN', TRUE);
}

cli_load_main();
