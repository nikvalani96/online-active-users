<?php
/**
 * Optional country and timezone settings. The controller supplies the view state.
 *
 * @package Online_Active_Users
 * @var bool  $enabled Whether geolocation is enabled.
 * @var bool  $managed Whether the setting is controlled by a constant.
 * @var mixed $error   Whether a geolocation lookup error is present.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="wpoau-geo-settings" aria-labelledby="wpoau-geo-title">
	<div>
		<span class="wpoau-geo-state"><?php echo esc_html( $enabled ? __( 'Location enabled', 'online-active-users' ) : __( 'Location disabled', 'online-active-users' ) ); ?></span>
		<h2 id="wpoau-geo-title"><?php esc_html_e( 'Country, timezone & flags', 'online-active-users' ); ?></h2>
		<p><?php esc_html_e( 'Free feature. Enable IP-based country and timezone lookup for signed-in users. Flags are loaded locally from this plugin.', 'online-active-users' ); ?></p>
	</div>
	<?php if ( get_transient( 'wpoau_geo_saved_' . get_current_user_id() ) ) : ?>
		<p class="wpoau-geo-saved" role="status"><?php esc_html_e( 'Location settings saved.', 'online-active-users' ); ?></p>
		<?php delete_transient( 'wpoau_geo_saved_' . get_current_user_id() ); ?>
	<?php endif; ?>
	<details class="wpoau-geo-details" <?php echo $enabled && ! $error ? '' : 'open'; ?>>
		<summary><?php echo esc_html( $enabled ? __( 'Manage location settings', 'online-active-users' ) : __( 'Enable location lookup', 'online-active-users' ) ); ?></summary>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="wpoau_geolocation">
		<?php wp_nonce_field( 'wpoau_geolocation' ); ?>
		<label class="wpoau-geo-toggle">
			<input type="checkbox" name="geolocation_enabled" value="1" data-wpoau-location-toggle <?php checked( $enabled ); ?> <?php disabled( $managed ); ?>>
			<strong><?php esc_html_e( 'Enable country and timezone lookup', 'online-active-users' ); ?></strong>
		</label>
		<p data-wpoau-location-pending role="status" hidden><?php esc_html_e( 'Preview only — save location settings to keep this change. Enabling lookup may load location data after saving.', 'online-active-users' ); ?></p>
		<p class="description"><?php esc_html_e( 'When enabled, user IP addresses are sent to ipwho.is and results are cached for 24 hours. Inform your users about this external service in your privacy information.', 'online-active-users' ); ?> <a href="https://ipwhois.io/privacy" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Service privacy policy', 'online-active-users' ); ?></a></p>
		<?php if ( $managed ) : ?>
			<p><?php esc_html_e( 'This site already controls this setting through WPOAU_GEOLOCATION_ENABLED. No additional configuration is required.', 'online-active-users' ); ?></p>
		<?php endif; ?>
		<?php if ( $enabled && $error ) : ?>
			<p class="wpoau-geo-error"><?php esc_html_e( 'Location lookup is temporarily unavailable. Check your hosting outbound HTTPS connection or the provider limit, then retry.', 'online-active-users' ); ?></p>
		<?php elseif ( ! $enabled ) : ?>
			<p><?php esc_html_e( 'Location lookup is off. Country and timezone columns are hidden. Enable lookup below to display available locations.', 'online-active-users' ); ?></p>
		<?php endif; ?>
		<?php
		$submit_label = $managed
			? __( 'Refresh my location', 'online-active-users' )
			: ( $enabled ? __( 'Save location settings', 'online-active-users' ) : __( 'Enable location', 'online-active-users' ) );
		?>
		<button type="submit" class="button button-primary"><?php echo esc_html( $submit_label ); ?></button>
	</form>
	</details>
</section>
