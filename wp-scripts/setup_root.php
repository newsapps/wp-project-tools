<?php
/*****
 * Wordpress automated setup
 * setup_root.php
 *
 * Configures the root blog
 *****/

require_once( 'cli-load.php' );

global $settings;

// Set default blog options
foreach ($settings['options'] as $key=>$val)
    update_option($key, $val);

// Set default root blog options
foreach ($settings['root_options'] as $key=>$val)
    update_option($key, $val);

print("Default blog options set\n");

// Set network options
foreach ($settings['network'] as $key => $val)
    update_site_option($key, $val);

$allowed_themes = array();
foreach ( $settings['network']['allowed_themes'] as $theme )
    $allowed_themes[$theme] = true;

update_site_option('allowedthemes', $allowed_themes);

print("Default network options set\n");
