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
