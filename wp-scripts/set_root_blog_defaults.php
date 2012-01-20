<?php
/*****
 * Set all the root blog and network options to the defaults
 * in data/*_settings.json file.
 *
 *****/

require_once( 'tools/cli-load.php' );

global $settings;

# Set default blog options
foreach ($settings['options'] as $key=>$val)
    update_option($key, $val);

# Set default root blog options
foreach ($settings['root_options'] as $key=>$val)
    update_option($key, $val);

print("Default blog options set\n");

# Are we setting up a network today?
if ( empty( $settings['network'] ) )
    die( "Skipping network options\n" );

# Set network options
foreach ($settings['network'] as $key => $val)
    update_site_option($key, $val);

# The allowed theme setting needs some special attention
if ( !empty( $settings['network']['allowed_themes'] ) ) {
    $allowed_themes = array();
    foreach ( $settings['network']['allowed_themes'] as $theme )
        $allowed_themes[$theme] = true;

    update_site_option('allowedthemes', $allowed_themes);
}

print("Default network options set\n");
