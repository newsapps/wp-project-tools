<?php
/*****
 * Wordpress automated setup
 * setup_blog.php
 *
 * Creates and configures one network blog. Pass it the index of the blog
 * in the blogs data file.
 *
 * As of 3.1.2 we can only make one blog per init.
 * http://core.trac.wordpress.org/ticket/12028
 *****/

require_once( 'cli-load.php' );

global $settings;

$options = getopt("n:");

// Check for data/blogs.json
if ( file_exists( dirname( dirname( __DIR__ ) ) . '/data/blogs.json' ) ) {
    $tmp_fn = dirname( dirname( __DIR__ ) ) . '/data/blogs.json';
    $tmp_fc = file_get_contents($tmp_fn);

    $blog_data = json_decode($tmp_fc, $assoc = true);
    $sites = $blog_data['blogs'];
}

global $wpdb;

if ( $options['n'] >= count($sites) ) die("No more blogs.\n");

$site = $sites[$options['n']];

$wpdb->hide_errors();

if ($settings['install']['subdomain_install']) {
    $id = wpmu_create_blog($site['slug'].".".$settings['install']['hostname'], "", $site['name'], 1, $settings['install']['site'], 1);
} else {
    $id = wpmu_create_blog($settings['install']['hostname'], "/".$site['slug'], $site['name'], 1, $settings['install']['site'], 1);
}

$wpdb->show_errors();

if (!is_wp_error( $id )) {
    //doing a normal flush rules will not work, just delete the rewrites
    switch_to_blog( $id );

    // Delete the first post
    wp_delete_post( 1, true );

    // Delete the about page
    wp_delete_post( 2, true );

    // flush rewrite rules
    delete_option( 'rewrite_rules' );

    // set all the defaults for the blog
    foreach ($settings['install']['options'] as $key=>$val)
        update_option($key, $val);

    // set all the custom options for the blog
    foreach ($site['options'] as $key=>$val)
        update_option($key, $val);

    restore_current_blog();
    unset( $id );

    print("Success - ".$site['name']." setup\n");
} else {
    die($id->get_error_message());
}
