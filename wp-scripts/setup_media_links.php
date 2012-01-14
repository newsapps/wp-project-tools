<?php

include( 'cli-load.php' );

$query = "select blog_id,path from wp_blogs";
$results = $wpdb->get_results($query, 'ARRAY_A');

foreach ( $results as $blog ) {

    $slug = str_replace('/', '', $blog['path']);

    $media_directory = ABSPATH . "wp-content/blogs.dir/" . $blog['blog_id'];
    $symlink = ABSPATH . "wp-content/blogs.dir/" . $slug;

    if ( file_exists( $symlink ) ) continue;

    if ( ! symlink ( $blog['blog_id'], $symlink ) ) throw new Exception("Can't create symlink");

    print "Creating media link for ". $slug . "\n";

}
