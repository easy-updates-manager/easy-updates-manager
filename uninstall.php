<?php

/* Description: Removes all data when deleted. */
if (!defined( 'WP_UNINSTALL_PLUGIN' )) {
    exit ();
} 
delete_option( '_disable_updates' );
delete_site_option( '_disable_updates' );
delete_option( 'MPSUM' );
delete_site_option( 'MPSUM' );