<?php

include( 'tools/cli-load.php' );

$query = "select blog_id,path from wp_blogs";
$results = $wpdb->get_results($query, 'ARRAY_A');

foreach ( $results as $blog ) {

    $slug = str_replace('/', '', $blog['path']);

    $media_directory =  "blogs.dir/" . $blog['blog_id'];
    $symlink = "blogs.dir/" . $slug;

    if ( file_exists( $symlink ) ) continue;
    if ( is_link ( $symlink ) ) unlink( $symlink );

    symlink ( $blog['blog_id'], $symlink );

    print "Creating media link for ". $slug . "\n";

}
