<?php
/**
 * Shared admin navigation and edition status.
 *
 * @package Online_Active_Users
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Shared administration links and edition state.
 */
final class WPOAU_Hub {

	/**
	 * Get the verified WP Online Active Users product page.
	 *
	 * @return string
	 */
	public static function product_url(): string {
		return 'https://webizito.com/online-active-users/';
	}

	/**
	 * Get the Pro purchase URL.
	 *
	 * @return string
	 */
	public static function buy_url(): string {
		return esc_url_raw( apply_filters( 'wpoau_pro_buy_url', self::product_url() ) );
	}

	/**
	 * Get the documentation URL.
	 *
	 * @return string
	 */
	public static function docs_url(): string {
		return esc_url_raw( apply_filters( 'wpoau_docs_url', 'https://webizito.com/docs/' ) );
	}

	/**
	 * Get the support contact URL.
	 *
	 * @return string
	 */
	public static function contact_url(): string {
		return esc_url_raw( apply_filters( 'wpoau_contact_url', 'https://webizito.com/contact-us/' ) );
	}

	/**
	 * Check whether the installed Pro add-on has an active license.
	 *
	 * @return bool
	 */
	public static function premium(): bool {
		return class_exists( 'WPOAU\Pro\License' ) && \WPOAU\Pro\License::features();
	}

	/**
	 * Return the offer card for the current admin, or null when none should show.
	 *
	 * @return array|null
	 */
	public static function promo_link(): ?array {
		return WPOAU_Promo::card();
	}

	/**
	 * Render the shared admin overview.
	 *
	 * @return bool
	 */
	public static function overview(): bool {
		return require __DIR__ . '/views/hub-overview.php';
	}
}
add_action(
	'admin_enqueue_scripts',
	static function () {
		wp_enqueue_style( 'wpoau-admin-hub', plugin_dir_url( WPOAU_PLUGIN_FILE ) . 'assets/css/admin-hub.css', array(), WPOAU_VERSION );
		wp_enqueue_style( 'wpoau-plugin-list', plugin_dir_url( WPOAU_PLUGIN_FILE ) . 'assets/css/plugin-list.css', array(), WPOAU_VERSION );
	}
);
