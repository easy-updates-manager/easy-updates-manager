<?php

// uninstall code
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit ();
} 

// it was defined, now delete 
delete_option('_disable_updates');

?>