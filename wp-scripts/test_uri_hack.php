<?php
$_SERVER["REQUEST_URI"] = '/newblog/';

include( 'tools/cli-load.php' );

var_dump( wp_upload_dir() );
