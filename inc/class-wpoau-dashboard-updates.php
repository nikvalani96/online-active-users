<?php
/**
 * Cached product updates for the dashboard overview.
 *
 * @package Online_Active_Users
 */

defined( 'ABSPATH' ) || exit;

/** Refreshes the Webizito content feed in the background without blocking dashboard views. */
final class WPOAU_Dashboard_Updates {
	const CACHE_OPTION = 'wpoau_updates_cache';
	const LOCK_OPTION  = 'wpoau_updates_lock';
	const REFRESH_HOOK = 'wpoau_updates_refresh';
	const FEED_URL     = 'https://webizito.com/wp-json/wpoau/v1/content';

	/** Register dashboard rendering and background refresh hooks. */
	public function __construct() {
		add_action( 'wpoau_dashboard_session_summary', array( $this, 'render_widget' ), PHP_INT_MAX );
		add_action( 'admin_init', array( $this, 'ensure_schedule' ) );
		add_action( self::REFRESH_HOOK, array( $this, 'refresh' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		register_deactivation_hook( WPOAU_PLUGIN_FILE, array( $this, 'deactivate' ) );
	}

	/** Load the dashboard styles only for users who can see this widget. */
	public function assets(): void {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && 'dashboard' === $screen->id && current_user_can( 'list_users' ) ) {
			wp_enqueue_style( 'wpoau-dashboard-overview', plugin_dir_url( WPOAU_PLUGIN_FILE ) . 'assets/css/dashboard-overview.css', array(), WPOAU_VERSION );
		}
	}

	/** Always render cached updates and verified product links inside the dashboard widget. */
	public function render_widget(): void {
		$items      = self::items();
		$categories = array(
			'product-update' => __( 'Product Update', 'online-active-users' ),
			'guide'           => __( 'Guide', 'online-active-users' ),
			'tutorial'        => __( 'Tutorial', 'online-active-users' ),
		);
		echo '<section class="wpoau-dashboard-feed" aria-labelledby="wpoau-dashboard-feed-title">
		<h3 id="wpoau-dashboard-feed-title"><b>' . esc_html__( "What's New in WPOAU", 'online-active-users' ) . '</b></h3>';
		if ( $items ) {
			echo '<ul class="wpoau-dashboard-feed-list">';
			foreach ( $items as $item ) {
				echo '<li class="wpoau-dashboard-feed-item"><div class="wpoau-dashboard-feed-meta"><span class="eyes-tag">WPOAU</span><time datetime="' . esc_attr( gmdate( 'c', $item['date'] ) ) . '">' . esc_html( wp_date( get_option( 'date_format' ), $item['date'] ) ) . '</time></div>';
				echo '<a href="' . esc_url( $item['url'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $item['title'] ) . '</a>';
				echo '<p class="wpoau-dashboard-feed-description">' . esc_html( $item['description'] ) . '</p></li>';
			}
			echo '</ul>';
		} else {
			echo '<p class="wpoau-dashboard-empty">' . esc_html__( 'No related news are available right now.', 'online-active-users' ) . '</p>';
		}
	}

	/** Queue feed refreshes without making a network request during page rendering. */
	public function ensure_schedule(): void {
		$this->schedule_refresh( 30 );
	}

	/** Fetch and validate the content feed in the background. */
	public function refresh(): void {
		if ( ! add_option( self::LOCK_OPTION, time(), '', false ) ) {
			$locked_at = (int) get_option( self::LOCK_OPTION, 0 );
			if ( $locked_at > time() - 300 ) {
				$this->schedule_refresh( 5 * MINUTE_IN_SECONDS );
				return;
			}
			delete_option( self::LOCK_OPTION );
			if ( ! add_option( self::LOCK_OPTION, time(), '', false ) ) {
				$this->schedule_refresh( 5 * MINUTE_IN_SECONDS );
				return;
			}
		}

		$success = false;
		if ( ! get_transient( 'wpoau_updates_backoff' ) ) {
			$feed_url = apply_filters( 'wpoau_updates_feed_url', self::FEED_URL );
			$url      = is_string( $feed_url ) ? esc_url_raw( $feed_url ) : '';
			if ( self::valid_feed_url( $url ) ) {
				$response = wp_safe_remote_get(
					$url,
					array(
						'timeout'             => 4,
						'redirection'         => 0,
						'limit_response_size' => 65536,
						'reject_unsafe_urls'  => true,
						'user-agent'          => 'WP Online Active Users feed',
						'headers'             => array( 'Accept' => 'application/json' ),
					)
				);
				if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
					$content_type = (string) wp_remote_retrieve_header( $response, 'content-type' );
					$body         = wp_remote_retrieve_body( $response );
					$payload      = strlen( $body ) <= 65536 && false !== stripos( $content_type, 'application/json' )
						? json_decode( $body, true )
						: null;
					$items = self::validate_items( is_array( $payload ) ? ( $payload['items'] ?? null ) : null );
					if ( is_array( $items ) ) {
						$cache = array(
							'fetched' => time(),
							'items'   => $items,
						);
						if ( update_option( self::CACHE_OPTION, $cache, false ) || get_option( self::CACHE_OPTION, array() ) === $cache ) {
							delete_transient( 'wpoau_updates_backoff' );
							$success = true;
						} elseif ( ! get_transient( 'wpoau_updates_cache_error_logged' ) ) {
							error_log( 'WPOAU: unable to save the product updates cache.' );
							set_transient( 'wpoau_updates_cache_error_logged', 1, HOUR_IN_SECONDS );
						}
					}
				}
			}
			if ( ! $success ) {
				set_transient( 'wpoau_updates_backoff', 1, HOUR_IN_SECONDS );
			}
		}

		delete_option( self::LOCK_OPTION );
		$this->schedule_refresh( $success ? 12 * HOUR_IN_SECONDS : HOUR_IN_SECONDS );
	}

	/**
	 * Return validated cached content only; dashboard rendering never performs HTTP.
	 *
	 * @return array
	 */
	public static function items(): array {
		$cache = get_option( self::CACHE_OPTION, array() );
		if ( ! is_array( $cache ) || empty( $cache['fetched'] ) || ! is_array( $cache['items'] ?? null ) ) {
			return self::fallback_items();
		}
		return $cache['items'] ? $cache['items'] : self::fallback_items();
	}

	/**
	 * Show verified, product-related blog posts while the dedicated feed is unavailable.
	 *
	 * @return array
	 */
	private static function fallback_items(): array {
		$published = ( new \DateTimeImmutable( '2026-11-15T00:00:00', wp_timezone() ) )->getTimestamp();
		return array(
			array(
				'title'       => __( 'AI Chatbot for WooCommerce: What to Automate', 'online-active-users' ),
				'url'         => 'https://webizito.com/ai-chatbot-for-woocommerce/',
				'date'        => $published,
				'description' => __( 'Decide which customer questions AI can answer and when a human should take over.', 'online-active-users' ),
			),
			array(
				'title'       => __( 'Welcome Returning Users in WordPress: Practical Guide', 'online-active-users' ),
				'url'         => 'https://webizito.com/welcome-returning-users-in-wordpress-practical-guide/',
				'date'        => $published,
				'description' => __( 'Learn when and how to show a useful welcome-back message with WP Online Active Users Pro.', 'online-active-users' ),
			),
		);
	}

	/**
	 * Restrict feed transport to HTTPS on the intended Webizito host.
	 *
	 * @param string $url Configured feed URL.
	 * @return bool
	 */
	public static function valid_feed_url( string $url ): bool {
		$parts = wp_parse_url( $url );
		return is_array( $parts )
			&& 'https' === strtolower( (string) ( $parts['scheme'] ?? '' ) )
			&& 'webizito.com' === strtolower( (string) ( $parts['host'] ?? '' ) )
			&& ! isset( $parts['user'] )
			&& ! isset( $parts['pass'] )
			&& ( ! isset( $parts['port'] ) || 443 === (int) $parts['port'] )
			&& '/wp-json/wpoau/v1/content' === ( $parts['path'] ?? '' )
			&& ! isset( $parts['query'] )
			&& ! isset( $parts['fragment'] );
	}

	/**
	 * Validate article links served by the trusted Webizito endpoint.
	 *
	 * @param string $url Article URL.
	 * @return bool
	 */
	private static function valid_content_url( string $url ): bool {
		$parts = wp_parse_url( $url );
		return is_array( $parts )
			&& 'https' === strtolower( (string) ( $parts['scheme'] ?? '' ) )
			&& 'webizito.com' === strtolower( (string) ( $parts['host'] ?? '' ) )
			&& ! isset( $parts['user'] )
			&& ! isset( $parts['pass'] )
			&& ( ! isset( $parts['port'] ) || 443 === (int) $parts['port'] )
			&& ! empty( $parts['path'] )
			&& ( ! isset( $parts['query'] ) || preg_match( '/^p=[1-9][0-9]*$/D', $parts['query'] ) )
			&& ! isset( $parts['fragment'] );
	}

	/**
	 * Validate and normalize the small public feed response.
	 *
	 * @param mixed $raw_items Untrusted decoded feed records.
	 * @return array|false
	 */
	public static function validate_items( $raw_items ) {
		if ( ! is_array( $raw_items ) || count( $raw_items ) > 3 ) {
			return false;
		}
		$items = array();
		foreach ( $raw_items as $item ) {
			if ( ! is_array( $item ) || ! is_string( $item['title'] ?? null ) || ! is_string( $item['category'] ?? null ) || ! is_string( $item['url'] ?? null ) || ! is_string( $item['date'] ?? null ) || ! is_string( $item['description'] ?? null ) ) {
				return false;
			}
			$category = sanitize_key( $item['category'] );
			$url      = esc_url_raw( $item['url'], array( 'https' ) );
			$parts    = wp_parse_url( $url );
			$date = false;
			if ( preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:Z|[+-]\d{2}:\d{2})$/D', $item['date'] ) ) {
				$date_object = \DateTimeImmutable::createFromFormat( 'Y-m-d\TH:i:sP', $item['date'] );
				$date_errors = \DateTimeImmutable::getLastErrors();
				if ( $date_object && ( false === $date_errors || ( 0 === $date_errors['warning_count'] && 0 === $date_errors['error_count'] ) ) && $date_object->format( 'Y-m-d\TH:i:sP' ) === $item['date'] ) {
					$date = $date_object->getTimestamp();
				}
			}
			$title    = sanitize_text_field( $item['title'] );
			if ( ! in_array( $category, array( 'product-update', 'guide', 'tutorial', 'article' ), true ) || '' === $title || ! self::valid_content_url( $url ) || ! is_array( $parts ) || empty( $parts['path'] ) || false === $date ) {
				return false;
			}
			$items[] = array(
				'category'    => $category,
				'title'       => substr( $title, 0, 180 ),
				'url'         => $url,
				'date'        => (int) $date,
				'description' => substr( sanitize_text_field( wp_strip_all_tags( $item['description'] ) ), 0, 320 ),
			);
		}
		return $items;
	}

	/** Schedule a refresh or log a throttled, non-sensitive diagnostic. */
	private function schedule_refresh( int $delay ): bool {
		if ( wp_next_scheduled( self::REFRESH_HOOK ) ) {
			return true;
		}
		$result = wp_schedule_single_event( time() + $delay, self::REFRESH_HOOK, array(), true );
		if ( is_wp_error( $result ) || false === $result ) {
			if ( ! get_transient( 'wpoau_updates_schedule_error_logged' ) ) {
				error_log( 'WPOAU: unable to schedule the product updates refresh.' );
				set_transient( 'wpoau_updates_schedule_error_logged', 1, HOUR_IN_SECONDS );
			}
			return false;
		}
		return true;
	}

	/** Deactivation pauses background refresh while retaining cached articles. */
	public function deactivate(): void {
		wp_clear_scheduled_hook( self::REFRESH_HOOK );
		delete_option( self::LOCK_OPTION );
	}
}
