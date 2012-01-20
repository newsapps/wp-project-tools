<?php

include( 'tools/cli-load.php' );

$query = "select blog_id from wp_blogs";
$results = $wpdb->get_results($query, 'ARRAY_A');

foreach ( $results as $blog ) {
    switch_to_blog($blog['blog_id']);

    $wp_rewrite = new \WP_Rewrite();
    $wp_rewrite->set_permalink_structure('/%year%/%monthnum%/%postname%/');
    $wp_rewrite->flush_rules();
    print "Set new permalink structure for " . get_bloginfo('name') . "\n";

    update_option('category_base', '/category');
    update_option('tag_base', '/tag');

    restore_current_blog();
}
