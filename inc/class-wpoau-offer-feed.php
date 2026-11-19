<?php
/** Public, background-only promotional campaign feed. */
defined( 'ABSPATH' ) || exit;

final class WPOAU_Offer_Feed {
	const CACHE = 'wpoau_offer_feed_cache';
	const LOCK = 'wpoau_offer_feed_lock';
	const HOOK = 'wpoau_offer_feed_refresh';
	const URL = 'https://webizito.com/wp-json/wpoau-campaigns/v1/offers';
	const MAX_BYTES = 65536;
	const MAX_AGE = 86400;

	public static function init(): void {
		add_action( 'admin_init', array( __CLASS__, 'schedule' ), 10, 0 );
		add_action( self::HOOK, array( __CLASS__, 'refresh' ) );
		register_deactivation_hook( WPOAU_PLUGIN_FILE, array( __CLASS__, 'deactivate' ) );
	}

	public static function schedule( int $delay = 30 ): void {
		if ( class_exists( 'WPOAU\Server\Offer_Campaigns' ) || wp_next_scheduled( self::HOOK ) ) {
			return;
		}
		if ( ! wp_schedule_single_event( time() + $delay, self::HOOK ) ) {
			error_log( 'WPOAU: Could not schedule promo campaign refresh.' );
		}
	}

	public static function deactivate(): void {
		wp_clear_scheduled_hook( self::HOOK );
		delete_option( self::LOCK );
	}

	public static function validate( $data ) {
		if ( ! is_array( $data ) || ! in_array( $data['version'] ?? 0, array( 1, 2 ), true ) || ! is_bool( $data['enabled'] ?? null ) || ! is_array( $data['campaigns'] ?? null ) || count( $data['campaigns'] ) > 1 ) {
			return false;
		}
		if ( ! $data['enabled'] ) {
			return empty( $data['campaigns'] ) ? array( 'version' => 2, 'enabled' => false, 'campaigns' => array() ) : false;
		}
		$campaign = $data['campaigns'][0] ?? null;
		if ( ! is_array( $campaign ) ) {
			return false;
		}
		$headline = $campaign['headline'] ?? $campaign['heading'] ?? null;
		$revision = $campaign['revision'] ?? null;
		$url = $campaign['cta_url'] ?? $campaign['url'] ?? null;
		$theme = $campaign['theme'] ?? 'custom';
		$starts = $campaign['starts_at'] ?? $campaign['start'] ?? '';
		$expires = $campaign['expires_at'] ?? $campaign['end'] ?? '';
		$timezone = $campaign['timezone'] ?? 'UTC';
		$description = $campaign['description'] ?? '';
		$percent = $campaign['discount_percent'] ?? 0;
		$coupon = $campaign['coupon_code'] ?? '';
		$cta = $campaign['cta_text'] ?? $campaign['button_text'] ?? 'Explore Pro';
		$id = $campaign['id'] ?? '';
		if (
			! is_string( $id ) || ! preg_match( '/^[a-z0-9_-]{1,60}$/D', $id )
			|| ! is_string( $headline ) || '' === trim( $headline ) || strlen( $headline ) > 100 || sanitize_text_field( $headline ) !== $headline
			|| ! is_string( $description ) || strlen( $description ) > 180 || sanitize_text_field( $description ) !== $description
			|| ! is_string( $theme ) || ! in_array( $theme, array( 'black-friday', 'cyber-monday', 'christmas', 'new-year', 'custom' ), true )
			|| ! is_int( $revision ) || $revision < 1 || $revision > 999999
			|| ! is_int( $percent ) || $percent < 0 || $percent > 100
			|| ! is_string( $coupon ) || ( '' !== $coupon && ! preg_match( '/^[A-Z0-9_-]{1,32}$/D', $coupon ) )
			|| ! is_string( $cta ) || '' === trim( $cta ) || strlen( $cta ) > 40 || sanitize_text_field( $cta ) !== $cta
			|| ! is_string( $starts ) || strlen( $starts ) > 16 || ! is_string( $expires ) || strlen( $expires ) > 16
			|| ! is_string( $timezone ) || ! in_array( $timezone, timezone_identifiers_list(), true )
			|| ! is_string( $url ) || ! WPOAU_Promo::valid_url( $url )
			|| ( $starts && ! self::valid_date( $starts, $timezone ) )
			|| ( $expires && ! self::valid_date( $expires, $timezone ) )
		) {
			return false;
		}
		return array(
			'version'   => 2,
			'enabled'   => true,
			'campaigns' => array(
				array(
					'id'               => $id,
					'revision'         => $revision,
					'enabled'          => true,
					'theme'            => $theme,
					'headline'         => $headline,
					'description'      => $description,
					'discount_percent' => $percent,
					'coupon_code'      => $coupon,
					'cta_text'         => $cta,
					'cta_url'          => $url,
					'starts_at'        => $starts,
					'expires_at'       => $expires,
					'timezone'         => $timezone,
				),
			),
		);
	}

	private static function valid_date( string $value, string $timezone ): bool {
		$date = DateTimeImmutable::createFromFormat( '!Y-m-d H:i', $value, new DateTimeZone( $timezone ) );
		$errors = DateTimeImmutable::getLastErrors();
		return $date && ( ! is_array( $errors ) || ( ! $errors['warning_count'] && ! $errors['error_count'] ) ) && $date->format( 'Y-m-d H:i' ) === $value;
	}

	public static function refresh(): void {
		if ( class_exists( 'WPOAU\Server\Offer_Campaigns' ) ) {
			return;
		}
		$lock = (int) get_option( self::LOCK, 0 );
		if ( $lock && $lock < time() - 300 ) {
			delete_option( self::LOCK );
		}
		if ( ! add_option( self::LOCK, time(), '', false ) ) {
			self::schedule( 300 );
			return;
		}
		$success = false;
		try {
			$response = wp_safe_remote_get( self::URL, array(
				'timeout'             => 4,
				'redirection'         => 0,
				'limit_response_size' => self::MAX_BYTES + 1,
				'reject_unsafe_urls'  => true,
				'sslverify'           => true,
				'user-agent'          => 'WPOAU-Promo-Campaign/3.0',
				'headers'             => array( 'Accept' => 'application/json', 'Cache-Control' => 'no-cache' ),
				'cookies'             => array(),
			) );
			if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) && false !== stripos( (string) wp_remote_retrieve_header( $response, 'content-type' ), 'application/json' ) ) {
				$body = wp_remote_retrieve_body( $response );
				$payload = is_string( $body ) && strlen( $body ) <= self::MAX_BYTES ? self::validate( json_decode( $body, true ) ) : false;
				if ( false !== $payload ) {
					$cache = array( 'fetched' => time(), 'payload' => $payload );
					$success = update_option( self::CACHE, $cache, false ) || get_option( self::CACHE ) === $cache;
				}
			}
		} finally {
			delete_option( self::LOCK );
			self::schedule( $success ? 6 * HOUR_IN_SECONDS : HOUR_IN_SECONDS );
		}
	}

	public static function current(): ?array {
		if ( class_exists( 'WPOAU\Server\Offer_Campaigns' ) ) {
			$payload = \WPOAU\Server\Offer_Campaigns::feed_for_client();
		} else {
			$cache = get_option( self::CACHE, array() );
			if ( ! is_array( $cache ) || ! is_int( $cache['fetched'] ?? null ) || $cache['fetched'] < time() - self::MAX_AGE ) {
				return null;
			}
			$payload = $cache['payload'] ?? null;
		}
		return self::validate( $payload ) ?: null;
	}
}

WPOAU_Offer_Feed::init();
