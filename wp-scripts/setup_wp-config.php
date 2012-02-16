<?php
/*****
 * Wordpress automated setup
 * setup_wp-config.php
 *
 * Generates the basic configuration files for the install. By default, just generates a 
 * basic single install wp-config file for the setup process. Use '--finish' to generate
 * the .htaccess and complete network installation config file.
 *****/

if ( file_exists( 'wp-config.php' ) ) {
    rename( 'wp-config.php', 'wp-config.old.php' );
    print( "Moved old wp-config.php out of the way\n" );
}

require_once( 'tools/cli-load.php' );

$options = getopt("", array("finish"));

global $settings;

// lets write some files

if ( array_key_exists('finish', $options) && empty( $settings['install']['no_apache'] ) ) {
    // Write the .htaccess file
    $htaccess_file = 'RewriteEngine On
	RewriteBase ' . $settings['install']['base'] . '
	RewriteRule ^index\.php$ - [L]

	# uploaded files
	RewriteRule ^' . ( $settings['install']['subdomain_install'] ? '' : '([_0-9a-zA-Z-]+/)?' ) . 'files/(.+) wp-includes/ms-files.php?file=$' . ( $settings['install']['subdomain_install'] ? 1 : 2 ) . ' [L]' . "

	RewriteCond %{REQUEST_FILENAME} -f [OR]
	RewriteCond %{REQUEST_FILENAME} -d
	RewriteRule ^ - [L]
	RewriteRule ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
	RewriteRule ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
	RewriteRule . index.php [L]";

	fwrite(fopen(ABSPATH.".htaccess", 'w'), $htaccess_file);
}

// Write the wp-config file
$newConfig = "<?php
/** Auto-generated config file **/\n\n";

# Go through the 'wp-config' items from the settings file
# and write them to the file
foreach ( $settings['wp-config'] as $key => $val ) {
    if ( is_string( $val ) )
        $newConfig .= "define('$key', '$val');\n";
    elseif ( is_bool( $val ) ) {
        $txt = $val ? 'true' : 'false';
        $newConfig .= "define('$key', $txt);\n";
    } elseif ( is_numeric( $val ) ) {
        $newConfig .= "define('$key', $val);\n";
    }
}

$newConfig .= "
\$table_prefix  = 'wp_';\n";

$newConfig .= file_get_contents('https://api.wordpress.org/secret-key/1.1/salt/');

if ( array_key_exists('network', $settings) &&
     array_key_exists('finish', $options) ) {
       $newConfig .= "
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', ";
$newConfig .= $settings['install']['subdomain_install'] ? 'true' : 'false';
$newConfig .= ");
\$base = '".$settings['install']['base']."';
define( 'DOMAIN_CURRENT_SITE', '".$settings['install']['hostname']."' );
define( 'PATH_CURRENT_SITE', '".$settings['install']['base']."' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
\n";
}

# Check if this is a traditionally orgainzed project, or if it's fancy
if ( is_dir( PROJECT_PATH . '/wordpress' ) )
    $newConfig .= "
define( 'WP_CONTENT_DIR', dirname(__FILE__) );
define( 'WP_PLUGIN_DIR', dirname(__FILE__) . '/plugins' );
define( 'WPMU_PLUGIN_DIR', dirname(__FILE__) . '/mu-plugins' );

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/wordpress/');\n";
else
    $newConfig .= "
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) );\n";


$newConfig .= "
define( 'DISABLE_WP_CRON', true);

/** Extra configuration settings that won't change **/
if ( file_exists( dirname(__FILE__) . '/wp-config.global.php' ) )
    require_once( dirname(__FILE__) . '/wp-config.global.php' );

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');";

fwrite(fopen("wp-config.php", 'w'), $newConfig);

if ( array_key_exists('finish', $options) )
	print("Wrote finalized wp-config.php and (maybe) .htaccess\n");
else
	print("Wrote starter wp-config.php\n");
