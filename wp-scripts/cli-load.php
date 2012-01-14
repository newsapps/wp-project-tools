<?php

if ( !defined('STDIN') ) 
  die("Please run this script from the command line."); 

if ( isset($_SERVER['DEPLOYMENT_TARGET']) )
    $settings_file = $_SERVER['DEPLOYMENT_TARGET'] . '_settings.json';
else $settings_file = 'development_settings.json';

global $settings;
$settings_path = dirname( __DIR__ ) .'/data/'. $settings_file;
$settings = json_decode( file_get_contents( $settings_path ), true );

define('DOING_AJAX', true);
define('WP_USE_THEMES', false);

global $_SERVER;
$_SERVER["HTTP_HOST"] = $settings['hostname'];
$_SERVER["SERVER_NAME"] = $settings['hostname'];
$_SERVER["REQUEST_URI"] = "/cli";
$_SERVER["REQUEST_METHOD"] = "GET";

define( 'ABSPATH', dirname( __DIR__ ) . '/wordpress/' );

if ( ! defined('WP_SITEURL') )
    define( 'WP_SITEURL', "http://".$settings['hostname']);

# Errors should go to stderr and not the log file
ini_set('display_errors', 'On');

if ( file_exists( dirname( __DIR__ ) . '/wp-config.php' ) )
    require_once( dirname( __DIR__ ) . '/wp-config.php');

// define('WP_ADMIN', TRUE);
