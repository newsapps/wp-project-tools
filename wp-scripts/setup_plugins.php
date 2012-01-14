<?php

define('WP_INSTALLING', true);

include( 'cli-load.php' );

# Check for data/plugins.json
if ( file_exists( dirname( dirname( __DIR__ ) ) . '/data/plugins.json' ) ) {
    $tmp_fn = dirname( dirname( __DIR__ ) ) . '/data/plugins.json';
    $tmp_fc = file_get_contents($tmp_fn);

    $plugins_data = json_decode($tmp_fc, $assoc = true);
    $plugins = $plugins_data['plugins'];
}

# include plugin functions
require_once(ABSPATH . "wp-admin/includes/plugin.php");

foreach( $plugins as $plugin ) {

    if ( array_key_exists( 'network', $settings ) ) {
        echo "Activating network " . $plugin . " ...   ";
        $result = activate_plugin( $plugin, '', true );
    } else {
        echo "Activating " . $plugin . " ...   ";
        $result = activate_plugin( $plugin );
    }

    if ( is_wp_error( $result ) ) {
        foreach ( $result->get_error_messages() as $err )
            print("FAILED: {$err}\n");
    } else {
        print("Activated\n");
    }
}

do_action( 'plugins_loaded' );
