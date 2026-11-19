<?php
// Exercise the real WordPress hook dispatcher without loading the site database.
$root = dirname( __DIR__, 4 );
define( 'ABSPATH', $root . DIRECTORY_SEPARATOR );
define( 'WPOAU_PLUGIN_FILE', dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'online-active-users.php' );
define( 'WP_PLUGIN_DIR', dirname( __DIR__, 2 ) );
define( 'WPMU_PLUGIN_DIR', $root . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'mu-plugins' );
$GLOBALS['offer_test_event'] = false;
$GLOBALS['wp_plugin_paths'] = array();
function wp_normalize_path( $path ) { return str_replace( '\\', '/', $path ); }
function wp_next_scheduled( $hook ) { return $GLOBALS['offer_test_event']; }
function wp_schedule_single_event( $time, $hook ) { $GLOBALS['offer_test_event'] = $time; return true; }
require $root . DIRECTORY_SEPARATOR . 'wp-includes' . DIRECTORY_SEPARATOR . 'plugin.php';
require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'class-wpoau-offer-feed.php';

do_action( 'admin_init' );
$event = $GLOBALS['offer_test_event'];
if ( $event < time() + 29 || $event > time() + 31 ) {
	throw new RuntimeException( 'The default refresh delay was not scheduled.' );
}
$GLOBALS['offer_test_event'] = false;
WPOAU_Offer_Feed::schedule( 5 );
if ( $GLOBALS['offer_test_event'] < time() + 4 || $GLOBALS['offer_test_event'] > time() + 6 ) {
	throw new RuntimeException( 'The explicit refresh delay was not retained.' );
}
echo "Promo feed hook checks passed.\n";
