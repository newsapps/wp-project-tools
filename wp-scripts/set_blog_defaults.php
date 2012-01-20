<?php
/**
 * Set all options for each network blog from the defaults in the
 * data/*_settings.json file. This does not touch the root blog and
 * does not load options from the data/blogs.json file.
 *
 * TODO: Load settings from the blogs.json file also
 **/

include( 'tools/cli-load.php' );

$query = "select blog_id from wp_blogs where blog_id > 1";
$results = $wpdb->get_results($query, 'ARRAY_A');

foreach ( $results as $blog ) {
    switch_to_blog($blog['blog_id']);

    // set all the defaults for the blog
    foreach ( $settings['options'] as $key=>$val )
        update_option($key, $val);

    print( "Updated '".get_bloginfo('name')."'\n" );

    restore_current_blog();
}
