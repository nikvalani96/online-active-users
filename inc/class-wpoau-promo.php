<?php
/**
 * Seasonal admin promo banner shared by Free and Pro.
 *
 * @package Online_Active_Users
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Displays and manages the shared seasonal promotion.
 */
final class WPOAU_Promo {
	const META   = 'wpoau_promo_dismissed';
	const ACTION = 'wpoau_promo_dismiss';

	/** Register campaign rendering and dismissal hooks. */
	public static function init(): void {
		add_action( 'admin_notices', array( __CLASS__, 'render' ) );
		add_action( 'network_admin_notices', array( __CLASS__, 'render' ) );
		add_action( 'admin_post_' . self::ACTION, array( __CLASS__, 'dismiss' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	/** Enqueue campaign presentation assets. */
	public static function assets(): void {
		wp_enqueue_style( 'wpoau-admin-hub', plugin_dir_url( WPOAU_PLUGIN_FILE ) . 'assets/css/admin-hub.css', array(), WPOAU_VERSION );
		wp_enqueue_script( 'wpoau-promo', plugin_dir_url( WPOAU_PLUGIN_FILE ) . 'assets/js/promo.js', array(), WPOAU_VERSION, true );
	}

	/**
	 * Validate a campaign destination URL.
	 *
	 * @param string $url Campaign URL.
	 * @return bool
	 */
	public static function valid_url( string $url ): bool {
		$parts = wp_parse_url( $url );
		return is_array( $parts )
			&& 'https' === strtolower( (string) ( $parts['scheme'] ?? '' ) )
			&& 'webizito.com' === strtolower( (string) ( $parts['host'] ?? '' ) )
			&& 0 === strpos( (string) ( $parts['path'] ?? '' ), '/' )
			&& ! isset( $parts['user'] )
			&& ! isset( $parts['pass'] )
			&& ! isset( $parts['port'] )
			&& ( ! isset( $parts['fragment'] ) || '' === $parts['fragment'] );
	}

	/**
	 * Build the per-revision dismissal key.
	 *
	 * @param array $campaign Campaign data.
	 * @return string
	 */
	public static function key( array $campaign ): string {
		return sanitize_key( $campaign['id'] ?? 'global-seasonal' ) . '@' . max( 1, (int) ( $campaign['revision'] ?? 1 ) );
	}

	/**
	 * Get the Free-edition Plugins screen offer without applying user dismissal.
	 *
	 * @return array{url:string,label:string}
	 */
	public static function plugin_offer(): array {
		$offer    = array(
			'label' => __( 'Get Pro deal', 'online-active-users' ),
			'url'   => WPOAU_Hub::buy_url(),
		);
		$feed     = class_exists( 'WPOAU_Offer_Feed' ) ? WPOAU_Offer_Feed::current() : null;
		$campaign = is_array( $feed ) && ! empty( $feed['enabled'] ) ? ( $feed['campaigns'][0] ?? null ) : null;
		if ( ! is_array( $campaign ) || ! self::active( $campaign ) ) {
			return $offer;
		}
		$labels         = array(
			'black-friday' => __( 'Get Black Friday Deal', 'online-active-users' ),
			'cyber-monday' => __( 'Get Cyber Monday Deal', 'online-active-users' ),
			'christmas'    => __( 'Get Christmas Deal', 'online-active-users' ),
			'new-year'     => __( 'Get New Year Deal', 'online-active-users' ),
		);
		$coupon         = $campaign['coupon_code'] ?? '';
		$offer['label'] = $labels[ $campaign['theme'] ?? 'custom' ] ?? __( 'Get Pro offer', 'online-active-users' );
		$offer['url']   = $coupon ? add_query_arg( 'wpoau_coupon', $coupon, $campaign['cta_url'] ) : $campaign['cta_url'];
		return $offer;
	}

	/**
	 * Return the current administrator's dismissed campaign keys.
	 *
	 * @return string[]
	 */
	public static function dismissed(): array {
		$value = get_user_meta( get_current_user_id(), self::META, true );
		return is_array( $value ) ? array_values( array_filter( $value, 'is_string' ) ) : array();
	}

	/**
	 * Determine whether a campaign is currently scheduled to display.
	 *
	 * @param array                  $campaign Campaign data.
	 * @param DateTimeImmutable|null $now      Time used for date checks.
	 * @return bool
	 */
	public static function active( array $campaign, ?DateTimeImmutable $now = null ): bool {
		$now      = null === $now ? new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ) : $now;
		$timezone = $campaign['timezone'] ?? 'UTC';
		if ( ! in_array( $timezone, timezone_identifiers_list(), true ) ) {
			return false;
		}
		foreach ( array( 'starts_at', 'expires_at' ) as $field ) {
			$value = $campaign[ $field ] ?? '';
			if ( '' !== $value ) {
				$date   = DateTimeImmutable::createFromFormat( '!Y-m-d H:i', $value, new DateTimeZone( $timezone ) );
				$errors = DateTimeImmutable::getLastErrors();
				if ( ! $date || ( is_array( $errors ) && ( $errors['warning_count'] || $errors['error_count'] ) ) ) {
					return false;
				}
				if ( 'starts_at' === $field && $now < $date ) {
					return false;
				}
				if ( 'expires_at' === $field && $now > $date ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Return the campaign visible to the current administrator.
	 *
	 * @return array|null
	 */
	public static function card(): ?array {
		if ( ! current_user_can( 'manage_options' ) ) {
			return null;
		}
		$feed     = class_exists( 'WPOAU_Offer_Feed' ) ? WPOAU_Offer_Feed::current() : null;
		$campaign = is_array( $feed ) && ! empty( $feed['enabled'] ) ? ( $feed['campaigns'][0] ?? null ) : null;
		if ( ! is_array( $campaign ) || ! self::active( $campaign ) || in_array( self::key( $campaign ), self::dismissed(), true ) ) {
			return null;
		}
		return $campaign;
	}

	/** Persist dismissal for the current administrator. */
	public static function dismiss(): void {
		check_admin_referer( self::ACTION );
		if ( current_user_can( 'manage_options' ) ) {
			$key = isset( $_POST['key'] ) && is_string( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
			if ( 1 === preg_match( '/^[a-z0-9_-]{1,60}@\d{1,6}$/D', $key ) ) {
				$keys = array_unique( array_merge( self::dismissed(), array( $key ) ) );
				update_user_meta( get_current_user_id(), self::META, array_slice( array_values( $keys ), -50 ) );
			}
		}
		$referer = wp_get_referer();
		wp_safe_redirect( $referer ? $referer : admin_url() );
		exit;
	}

	/**
	 * Get the visible label for a seasonal theme.
	 *
	 * @param string $theme Theme identifier.
	 * @return string
	 */
	private static function theme_label( string $theme ): string {
		$labels = array(
			'black-friday' => __( 'Black Friday special', 'online-active-users' ),
			'cyber-monday' => __( 'Cyber Monday special', 'online-active-users' ),
			'christmas'    => __( 'Season of savings', 'online-active-users' ),
			'new-year'     => __( 'New year offer', 'online-active-users' ),
			'custom'       => __( 'Limited-time offer', 'online-active-users' ),
		);
		return $labels[ $theme ] ?? $labels['custom'];
	}

	/** Render the active campaign. */
	public static function render(): void {
		$campaign = self::card();
		if ( ! $campaign ) {
			return;
		}
		$coupon  = $campaign['coupon_code'];
		$cta_url = $coupon ? add_query_arg( 'wpoau_coupon', $coupon, $campaign['cta_url'] ) : $campaign['cta_url'];
		$theme   = $campaign['theme'];
		?>
		<section class="wpoau-campaign wpoau-campaign--<?php echo esc_attr( $theme ); ?>" aria-label="<?php echo esc_attr( self::theme_label( $theme ) ); ?>">
			<div class="wpoau-campaign__glow" aria-hidden="true"></div>
			<div class="wpoau-campaign__content">
				<div class="wpoau-campaign__theme-icon" aria-hidden="true"><?php echo self::theme_icon( $theme ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static bundled SVG. ?></div>
				<div class="wpoau-campaign__message">
					<span class="wpoau-campaign__eyebrow"><?php echo esc_html( self::theme_label( $theme ) ); ?></span>
					<h2><?php echo esc_html( $campaign['headline'] ); ?></h2>
					<?php
					if ( '' !== $campaign['description'] ) :
						?>
						<p><?php echo esc_html( $campaign['description'] ); ?></p><?php endif; ?>
				</div>
			</div>
			<div class="wpoau-campaign__actions">
				<?php if ( $coupon ) : ?>
					<div class="wpoau-campaign__coupon">
						<span><?php esc_html_e( 'Use code', 'online-active-users' ); ?></span>
						<code><?php echo esc_html( $coupon ); ?></code>
						<button class="wpoau-campaign__copy-button" type="button" data-coupon="<?php echo esc_attr( $coupon ); ?>" data-copy-label="<?php esc_attr_e( 'Copy', 'online-active-users' ); ?>" data-copied-label="<?php esc_attr_e( 'Copied!', 'online-active-users' ); ?>"><?php esc_html_e( 'Copy', 'online-active-users' ); ?></button>
					</div>
				<?php endif; ?>
				<a class="wpoau-campaign__cta" href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $campaign['cta_text'] ); ?> <span aria-hidden="true">→</span></a>
			</div>
			<form class="wpoau-campaign__dismiss" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( self::ACTION ); ?>
				<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>">
				<input type="hidden" name="key" value="<?php echo esc_attr( self::key( $campaign ) ); ?>">
				<button type="submit" aria-label="<?php esc_attr_e( 'Dismiss promotion', 'online-active-users' ); ?>">×</button>
			</form>
		</section>
		<?php
	}

	/**
	 * Render the static inline icon for a campaign theme.
	 *
	 * @param string $theme Theme identifier.
	 * @return string
	 */
	private static function theme_icon( string $theme ): string {
		$icons = array(
			'black-friday' => '<path d="M4 9V4h9l7 7-9 9L4 13V9Z"/><circle cx="8.2" cy="8.2" r="1.2"/>',
			'cyber-monday' => '<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4M7 9h10"/>',
			'christmas'    => '<path d="m12 3 5 8h-3l4 6H6l4-6H7l5-8ZM12 17v4"/>',
			'new-year'     => '<path d="M12 2v4M12 18v4M2 12h4M18 12h4M5 5l3 3M16 16l3 3M19 5l-3 3M8 16l-3 3"/><circle cx="12" cy="12" r="3"/>',
			'custom'       => '<path d="m12 3 2.2 4.5 5 .7-3.6 3.5.8 5-4.4-2.4-4.4 2.4.8-5-3.6-3.5 5-.7L12 3Z"/>',
		);
		$path  = $icons[ $theme ] ?? $icons['custom'];
		return '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
	}
}

WPOAU_Promo::init();
