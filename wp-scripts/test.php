<?php
print( "Testing some stuff\n" );

print( "PATH:\n" );
print( get_include_path() . "\n" );

error_log( 'Test error log' );

var_dump( getopt("a:b:c") );

