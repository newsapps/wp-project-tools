<?php
/**
 * Load WordPress for use in commandline scripts and shells
 **/

if ( !defined('STDIN') ) 
    throw new Exception("Please run this script from the command line."); 

# Make sure errors get to the user
ini_set( 'display_errors', 'stderr' );

# Root path for this project
define( 'PROJECT_PATH', dirname( __DIR__ ) );

//define('DOING_AJAX', true);

# Don't try to process this like a request
define('WP_USE_THEMES', false);

$settings = get_project_settings();

# Spoof some http request stuff
$_SERVER["HTTP_HOST"] = $settings['install']['hostname'];
$_SERVER["SERVER_NAME"] = $settings['install']['hostname'];
$_SERVER["REQUEST_METHOD"] = "GET";
if ( empty( $_SERVER["REQUEST_URI"] ) )
  $_SERVER["REQUEST_URI"] = "/cli";

# Set the ABSPATH so WordPress can find its files
define( 'ABSPATH', PROJECT_PATH . '/wordpress/' );

if ( ! defined('WP_SITEURL') )
    define( 'WP_SITEURL', "http://".$settings['install']['hostname'] );

// define('WP_ADMIN', TRUE);

# Load WordPress!
if ( file_exists( PROJECT_PATH . '/wp-config.php' ) )
    require_once( PROJECT_PATH . '/wp-config.php' );

/**
 * Load a settings json file and return the associative array of stuff
 **/
function get_project_settings() {
    # Figure out if we're running in development, staging or production
    if ( isset($_SERVER['DEPLOYMENT_TARGET']) )
        $settings_file = $_SERVER['DEPLOYMENT_TARGET'] . '_settings.json';
    else $settings_file = 'development_settings.json';

    # Load our settings JSON file for this deployment environment
    $settings_path = PROJECT_PATH . '/data/' . $settings_file;
    $settings = json_decode( file_get_contents( $settings_path ), true );

    # Catch JSON errors
    if ( $settings == NULL )
        throw new Exception( "Settings file '$settings_path' could not be parsed\n" );

    return $settings;
}
