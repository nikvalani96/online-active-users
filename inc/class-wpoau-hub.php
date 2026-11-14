<?php
/** Shared admin navigation and edition status. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
final class WPOAU_Hub {

	public static function buy_url(): string {
		return esc_url_raw( apply_filters( 'wpoau_pro_buy_url', 'https://webizito.com/pro-account/' ) );
	}

	public static function docs_url(): string {
		return esc_url_raw( apply_filters( 'wpoau_docs_url', 'https://webizito.com/docs/' ) );
	}

	public static function contact_url(): string {
		return esc_url_raw( apply_filters( 'wpoau_contact_url', 'https://webizito.com/contact-us/' ) );
	}

	public static function premium(): bool {
		return class_exists( 'WPOAU\Pro\License' ) && \WPOAU\Pro\License::features();
	}

	public static function overview(): bool {
		return require __DIR__ . '/views/hub-overview.php';
	}
}
add_action(
	'admin_enqueue_scripts',
	static function () {
		$page = isset( $_GET['page'] ) && is_string( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		if ( 'wpoau-active-users' === $page ) {
			wp_enqueue_style( 'wpoau-admin-hub', plugin_dir_url( WPOAU_PLUGIN_FILE ) . 'assets/css/admin-hub.css', array(), WPOAU_VERSION );
		}
	}
);
