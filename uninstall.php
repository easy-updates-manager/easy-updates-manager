<?php

/*
	Description: Removes all data when deleted.
*/
if (!defined( 'WP_UNINSTALL_PLUGIN' )) {
    exit ();
} 
delete_option('_disable_updates');
