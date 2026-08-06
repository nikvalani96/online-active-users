<?php
/**
 * Constants normally defined at runtime in the plugin's main file,
 * declared here so PHPStan can resolve them during static analysis.
 *
 * @package Online_Active_Users
 */

if ( ! defined( 'WPOAU_PLUGIN_DIR' ) ) {
	define( 'WPOAU_PLUGIN_DIR', __DIR__ );
}
if ( ! defined( 'WPOAU_PLUGIN_FILE' ) ) {
	define( 'WPOAU_PLUGIN_FILE', __DIR__ . '/online-active-users.php' );
}
if ( ! defined( 'WPOAU_VERSION' ) ) {
	define( 'WPOAU_VERSION', '3.4.1' );
}
