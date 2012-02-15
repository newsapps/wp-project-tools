<?php
/**
 * Setup all the blogs to use the media directory to store uploaded
 * files. This keeps stuff out of the WordPress git submodule. It
 * also uses uses the blog's name instead of the number so you can
 * setup web server rewrite rules to serve media files directly.
 *
 * Currently only works with subdirectory blogs and non-network 
 * installations.
 *
 * TODO: Get this to work with subdomain blogs
 **/

include( 'tools/cli-load.php' );

update_option("upload_path", "../uploads");
update_option("upload_url_path", "/wp-content/uploads");
print( "Set the root blog upload dir to 'uploads'\n" );

if ( is_multisite() ) {
    $query = "select blog_id, path from wp_blogs where blog_id > 1";
    $results = $wpdb->get_results($query, 'ARRAY_A');

    foreach ( $results as $blog ) {
        switch_to_blog($blog['blog_id']);

        $blog_slug = str_replace('/', '', $blog['path']);

        update_option("upload_path", "../blogs.dir/${blog['blog_id']}");
        update_option("upload_url_path", "/wp-content/blogs.dir/${blog['blog_id']}");
        print( "Set the upload dir for '".get_bloginfo('name')."' to 'blogs.dir/${blog['blog_id']}'\n" );

        restore_current_blog();
    }
}
