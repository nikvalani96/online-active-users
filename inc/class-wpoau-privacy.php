<?php
/**
 * Privacy settings and personal-data tools.
 *
 * @package Online_Active_Users
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WordPress privacy policy and personal-data callbacks.
 */
final class WPOAU_Privacy {

	/**
	 * Register privacy-related WordPress hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register' ) );
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'exporters' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'erasers' ) );
	}
	/**
	 * Check whether geolocation is authorized for this site.
	 *
	 * Saved consent is retained; developer testing authorizes lookups only when no
	 * site choice has been saved.
	 *
	 * @return bool
	 */
	public static function geolocation_enabled(): bool {
		if ( defined( 'WPOAU_GEOLOCATION_ENABLED' ) ) {
			return (bool) WPOAU_GEOLOCATION_ENABLED;
		}
		$consent = get_option( 'wpoau_geolocation_consent', null );
		// A saved OFF choice must override the developer-testing default.
		return null === $consent ? self::developer_testing() : (bool) $consent;
	}

	/**
	 * Check whether seller developer testing is enabled.
	 *
	 * @return bool
	 */
	public static function developer_testing(): bool {
		return class_exists( 'WPOAU\Server\Developer' ) && \WPOAU\Server\Developer::enabled();
	}

	/**
	 * Add the plugin privacy policy text.
	 */
	public function register(): void {
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			wp_add_privacy_policy_content( 'Online Active Users', '<p>This site records the last activity time of signed-in users. If the site administrator enables geolocation, IP addresses are sent to ipwho.is to estimate country and timezone and cached for 24 hours. Pro support conversations and feedback are stored on this site when those features are enabled. Optional notification emails are sent using the site mail provider. Contact the site administrator about access and erasure.</p>' );
		}
	}

	/**
	 * Register the personal-data exporter.
	 *
	 * @param array $items Existing exporters.
	 * @return array
	 */
	public function exporters( array $items ): array {
		$items['wpoau'] = array(
			'exporter_friendly_name' => 'Online Active Users',
			'callback'               => array( $this, 'export' ),
		);
		return $items;
	}

	/**
	 * Register the personal-data eraser.
	 *
	 * @param array $items Existing erasers.
	 * @return array
	 */
	public function erasers( array $items ): array {
		$items['wpoau'] = array(
			'eraser_friendly_name' => 'Online Active Users',
			'callback'             => array( $this, 'erase' ),
		);
		return $items;
	}

	/**
	 * Export personal data for a user.
	 *
	 * @param string $email User email address.
	 * @param int    $page  Export page number.
	 * @return array
	 */
	public function export( $email, $page = 1 ): array {
		$user = get_user_by( 'email', $email );
		$data = array();
		if ( $user && 1 === (int) $page ) {
			$seen = get_user_meta( $user->ID, 'last_seen', true );
			if ( $seen ) {
				$data[] = array(
					'group_id'    => 'wpoau-activity',
					'group_label' => 'Online user activity',
					'item_id'     => 'wpoau-user-' . $user->ID,
					'data'        => array(
						array(
							'name'  => 'Last seen',
							'value' => wp_date( 'c', (int) $seen ),
						),
					),
				);
			}
		}
		return array(
			'data' => $data,
			'done' => true,
		);
	}

	/**
	 * Erase personal data for a user.
	 *
	 * @param string $email User email address.
	 * @param int    $page  Erasure page number.
	 * @return array
	 */
	public function erase( $email, $page = 1 ): array {
		unset( $page );
		$user    = get_user_by( 'email', $email );
		$removed = false;
		if ( $user ) {
			$removed = (bool) delete_user_meta( $user->ID, 'last_seen' );
			$status  = get_transient( 'users_status' );
			if ( is_array( $status ) && isset( $status[ $user->ID ] ) ) {
				unset( $status[ $user->ID ] );
				set_transient( 'users_status', $status, 5 * MINUTE_IN_SECONDS );
				$removed = true;
			}
		}
		return array(
			'items_removed'  => $removed,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}
}
new WPOAU_Privacy();
