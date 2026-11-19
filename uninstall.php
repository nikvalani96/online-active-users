<?php
/**
 * Uninstall handler: removes plugin transients and options.
 *
 * @package Online_Active_Users
 */

// If uninstall is not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}
// Delete plugin transient.
delete_transient( 'users_status' );
// Delete all custom transients.
$wpoau_stored_keys = get_option( 'wpoau_transient_keys', array() );
if ( ! empty( $wpoau_stored_keys ) ) {
	foreach ( $wpoau_stored_keys as $wpoau_key ) {
		delete_transient( $wpoau_key );
	}
	delete_option( 'wpoau_transient_keys' );
}
delete_option( 'wpoau_geolocation_consent' );
delete_transient( 'wpoau_developer_public_ip' );
delete_option( 'wpoau_updates_consent' );
delete_option( 'wpoau_updates_cache' );
delete_option( 'wpoau_updates_lock' );
delete_transient( 'wpoau_updates_backoff' );
delete_transient( 'wpoau_updates_cache_error_logged' );
delete_transient( 'wpoau_updates_schedule_error_logged' );
wp_clear_scheduled_hook( 'wpoau_updates_refresh' );
wp_clear_scheduled_hook( 'wpoau_offer_feed_refresh' );
delete_option( 'wpoau_offer_feed_consent' );
delete_option( 'wpoau_offer_feed_cache' );
delete_option( 'wpoau_offer_feed_lock' );
