<?php
/**
* Uninstall script
*
* Uninstall script for Easy Updates Manager.
*
* @since 5.0.0
*
* @package WordPress
*/
if (!defined( 'WP_UNINSTALL_PLUGIN' )) {
    exit ();
} 
delete_option( '_disable_updates' );
delete_site_option( '_disable_updates' );
delete_option( 'MPSUM' );
delete_site_option( 'MPSUM' );
delete_option( 'mpsum_log_table_version' );
delete_site_option( 'mpsum_log_table_version' );
global $wpdb;
$tablename = $wpdb->base_prefix . 'eum_logs';
$sql = "drop table if exists $tablename";
$wpdb->query( $sql );
