<?php
/**
 * Shared documentation and support links for the Pro admin tabs.
 *
 * @package Online_Active_Users
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<aside class="wpoau-admin-resources" aria-labelledby="wpoau-admin-resources-title">
	<p class="wpoau-admin-resources-eyebrow"><?php esc_html_e( 'GUIDES AND SUPPORT', 'online-active-users' ); ?></p>
	<h2 id="wpoau-admin-resources-title"><?php esc_html_e( 'Need a hand?', 'online-active-users' ); ?></h2>
	<p><?php esc_html_e( 'Find plugin guidance or contact our team for help with Online Active Users.', 'online-active-users' ); ?></p>
	<a href="<?php echo esc_url( WPOAU_Hub::docs_url() ); ?>" target="_blank" rel="noopener noreferrer">
		<span class="dashicons dashicons-book-alt" aria-hidden="true"></span>
		<span><strong><?php esc_html_e( 'Documentation', 'online-active-users' ); ?></strong><small><?php esc_html_e( 'Read the plugin guide', 'online-active-users' ); ?></small></span>
		<span class="dashicons dashicons-arrow-up-right-alt2" aria-hidden="true"></span>
	</a>
	<a href="<?php echo esc_url( WPOAU_Hub::contact_url() ); ?>" target="_blank" rel="noopener noreferrer">
		<span class="dashicons dashicons-email-alt" aria-hidden="true"></span>
		<span><strong><?php esc_html_e( 'Contact support', 'online-active-users' ); ?></strong><small><?php esc_html_e( 'Get help with WP Online Active Users', 'online-active-users' ); ?></small></span>
		<span class="dashicons dashicons-arrow-up-right-alt2" aria-hidden="true"></span>
	</a>
</aside>
