<?php

// code to remove files. no data will be left behind!
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit ();
} 

// it was defined, now delete 
delete_option('_disable_updates');
