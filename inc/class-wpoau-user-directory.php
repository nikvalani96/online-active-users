<?php
/**
 * Paginated registered user activity directory.
 *
 * @package Online_Active_Users
 */
defined( 'ABSPATH' ) || exit;

final class WPOAU_User_Directory {
	public $page   = 1;
	public $status = '';
	private $total = 0;

	/** Fetch one page; last_seen survives logout and transient expiry. */
	public function records() {
		if ( ! current_user_can( 'list_users' ) ) {
			return array();
		}
		// Read-only filters; no state is changed by this request.
		$this->page   = max( 1, absint( self::filter( 'user_page' ) ) );
		$this->status = sanitize_key( self::filter( 'filter_status' ) );
		$role         = sanitize_key( self::filter( 'filter_role' ) );
		$country      = WPOAU_Privacy::geolocation_enabled() ? sanitize_text_field( self::filter( 'filter_country' ) ) : '';
		$activity     = get_transient( 'users_status' );
		$activity     = is_array( $activity ) ? $activity : array();
		$online       = array_keys(
			array_filter(
				$activity,
				static function ( $row ) {
					return (int) ( $row['last'] ?? 0 ) > time() - 50;
				}
			)
		);
		$args         = array(
			'number'      => 25,
			'paged'       => $this->page,
			'orderby'     => 'ID',
			'order'       => 'DESC',
			'count_total' => true,
		);
		if ( $role ) {
			$args['role'] = $role; }
		if ( 'online' === $this->status ) {
			$args['include'] = $online ?: array( 0 ); }
		if ( 'offline' === $this->status && $online ) {
			$args['exclude'] = $online; }
		if ( $country ) {
			$matches         = array_keys(
				array_filter(
					$activity,
					static function ( $row ) use ( $country ) {
						return ( $row['country'] ?? '' ) === $country;
					}
				)
			);
			$args['include'] = isset( $args['include'] ) ? array_values( array_intersect( $args['include'], $matches ) ) : $matches;
			$args['include'] = $args['include'] ?: array( 0 );
		}
		$query       = new WP_User_Query( $args );
		$this->total = $query->get_total();
		$rows        = array();
		foreach ( $query->get_results() as $user ) {
			$rows[] = array_merge(
				$activity[ $user->ID ] ?? array(),
				array(
					'id'       => $user->ID,
					'username' => $user->user_login,
					'last'     => (int) get_user_meta( $user->ID, 'last_seen', true ),
				)
			);
		}
		return $rows;
	}

	/** Read scalar query filters without accepting array input. */
	private static function filter( $name ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only directory filter.
		return isset( $_GET[ $name ] ) && is_string( $_GET[ $name ] ) ? wp_unslash( $_GET[ $name ] ) : '';
	}

	/** Classify activity; online presence always takes priority over age. */
	public static function activity_state( $online, $last_seen ) {
		if ( $online ) {
			return 'online';
		}
		if ( ! $last_seen ) {
			return 'unrecorded';
		}
		return (int) $last_seen <= time() - 30 * DAY_IN_SECONDS ? 'inactive' : 'offline';
	}

	/** Render escaped WordPress pagination preserving current filters. */
	public function pagination() {
		$links = paginate_links(
			array(
				'base'    => add_query_arg( 'user_page', '%#%' ),
				'format'  => '',
				'current' => $this->page,
				'total'   => (int) ceil( $this->total / 25 ),
			)
		);
		echo '<div class="tablenav"><span>' . esc_html( sprintf( __( '%d users match these filters', 'online-active-users' ), $this->total ) ) . '</span> ' . wp_kses_post( (string) $links ) . '</div>';
	}
}
