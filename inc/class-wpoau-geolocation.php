<?php
/**
 * Geolocation controls for the Free edition.
 *
 * @package Online_Active_Users
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Makes optional location lookup configurable without editing PHP files. */
final class WPOAU_Geolocation {
	/** Register the read-only screen and authorized settings handler. */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'wpoau_hub_overview_settings', array( $this, 'render' ) );
		add_action( 'admin_post_wpoau_geolocation', array( $this, 'save' ) );
	}

	/** Load the local visibility preview only on Active Users. */
	public function assets(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin page routing.
		$page = isset( $_GET['page'] ) && is_string( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin tab routing.
		$tab = isset( $_GET['tab'] ) && is_string( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'overview';
		if ( current_user_can( 'manage_options' ) && 'wpoau-active-users' === $page && 'overview' === $tab ) {
			wp_enqueue_script( 'wpoau-location-settings', plugin_dir_url( WPOAU_PLUGIN_FILE ) . 'assets/js/location-settings.js', array(), WPOAU_VERSION, true );
		}
	}

	/** Render settings only for administrators. */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'list_users' ) ) {
			return;
		}
		$enabled = WPOAU_Privacy::geolocation_enabled();
		$managed = defined( 'WPOAU_GEOLOCATION_ENABLED' );
		$error   = get_transient( 'wpoau_geo_lookup_error' );
		require __DIR__ . '/views/geolocation-settings.php';
	}

	/** Save explicit site authorization; never enable lookup merely on upgrade. */
	public function save(): void {
		$request_method = '';
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && is_string( $_SERVER['REQUEST_METHOD'] ) ) {
			$request_method = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) );
		}
		if ( 'POST' !== $request_method || ! current_user_can( 'manage_options' ) || ! current_user_can( 'list_users' ) ) {
			wp_die( esc_html__( 'Access denied.', 'online-active-users' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'wpoau_geolocation' );
		if ( ! defined( 'WPOAU_GEOLOCATION_ENABLED' ) ) {
			$enabled = isset( $_POST['geolocation_enabled'] ) && is_string( $_POST['geolocation_enabled'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['geolocation_enabled'] ) );
			update_option( 'wpoau_geolocation_consent', $enabled ? 1 : 0, false );
		}
		delete_transient( 'wpoau_geo_lookup_error' );
		if ( isset( $GLOBALS['wpoau_users'] ) && WPOAU_Privacy::geolocation_enabled() ) {
			$ip = $GLOBALS['wpoau_users']->wpoau_get_user_ip();
			delete_transient( 'wpoau_geodata_' . md5( $ip ) );
			$GLOBALS['wpoau_users']->wpoau_users_status_init();
		}
		set_transient( 'wpoau_geo_saved_' . get_current_user_id(), 1, 60 );
		wp_safe_redirect( admin_url( 'users.php?page=wpoau-active-users&tab=overview' ) );
		exit;
	}

	/**
	 * Render only a bundled image selected by a strict country-code allowlist.
	 *
	 * @param string $code Two-letter country code.
	 * @return string Escaped local image markup or an empty string.
	 */
	public static function flag( string $code ): string {
		$code = strtolower( $code );
		if ( ! preg_match( '/^[a-z]{2}$/D', $code ) || 'xx' === $code || ! is_file( WPOAU_PLUGIN_DIR . '/assets/flags/' . $code . '.svg' ) ) {
			return '';
		}
		return '<img class="webi-country-flag" src="' . esc_url( plugin_dir_url( WPOAU_PLUGIN_FILE ) . 'assets/flags/' . $code . '.svg' ) . '" alt="" aria-hidden="true" width="24" height="18"> ';
	}
}

new WPOAU_Geolocation();
