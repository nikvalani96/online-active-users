<?php
/**
 * Pro discovery screen. Variables supplied by WPOAU_Addons::render().
 *
 * @package Online_Active_Users
 * @var string $buy_url    Pro purchase URL.
 * @var string $docs_url   Documentation URL.
 * @var string $contact_url Support URL.
 * @var array  $features   Available Pro feature descriptions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpoau-addons">
	<section class="wpoau-addons-hero" aria-labelledby="wpoau-addons-title">
		<div class="wpoau-addons-intro">
			<p class="wpoau-addons-kicker"><?php esc_html_e( 'ONLINE ACTIVE USERS PRO', 'online-active-users' ); ?></p>
			<h2 id="wpoau-addons-title"><?php esc_html_e( 'Know who is here. Make their next visit count.', 'online-active-users' ); ?></h2>
			<p class="wpoau-addons-lead"><?php esc_html_e( 'Go beyond online status with welcome messages, personal support, user feedback and filtered activity exports.', 'online-active-users' ); ?></p>
			<div class="wpoau-addons-actions">
				<a class="wpoau-addons-primary" href="<?php echo esc_url( $buy_url ); ?>"><?php esc_html_e( 'Get the Pro Add-on', 'online-active-users' ); ?><span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span></a>
				<a class="wpoau-addons-secondary" href="#wpoau-pro-features"><?php esc_html_e( 'Explore the features', 'online-active-users' ); ?></a>
			</div>
			<p class="wpoau-addons-caption"><?php esc_html_e( 'One Pro add-on. All the features below. Your free plugin stays installed.', 'online-active-users' ); ?></p>
		</div>
		<aside class="wpoau-addons-summary" aria-label="<?php esc_attr_e( 'How Pro helps', 'online-active-users' ); ?>">
			<span class="wpoau-addons-summary-icon dashicons dashicons-groups" aria-hidden="true"></span>
			<h3><?php esc_html_e( 'From activity to connection', 'online-active-users' ); ?></h3>
			<ul>
				<li><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span><?php esc_html_e( 'Welcome users back', 'online-active-users' ); ?></li>
				<li><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span><?php esc_html_e( 'Answer their questions', 'online-active-users' ); ?></li>
				<li><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span><?php esc_html_e( 'Listen to their feedback', 'online-active-users' ); ?></li>
			</ul>
		</aside>
	</section>

	<section id="wpoau-pro-features" aria-labelledby="wpoau-features-title">
		<div class="wpoau-addons-section-heading">
			<div>
				<h2 id="wpoau-features-title"><?php esc_html_e( 'More ways to engage your users', 'online-active-users' ); ?></h2>
				<p><?php esc_html_e( 'Enable the tools you need and manage them in familiar WordPress tabs.', 'online-active-users' ); ?></p>
			</div>
			<span class="wpoau-addons-included"><?php esc_html_e( 'Included with Pro', 'online-active-users' ); ?></span>
		</div>
		<div class="wpoau-addons-grid">
			<?php foreach ( $features as $feature ) : ?>
				<article class="wpoau-addons-card">
					<span class="wpoau-addons-feature-icon dashicons dashicons-<?php echo esc_attr( $feature['icon'] ); ?>" aria-hidden="true"></span>
					<h3><?php echo esc_html( $feature['title'] ); ?></h3>
					<p><?php echo esc_html( $feature['text'] ); ?></p>
					<ul>
						<?php foreach ( $feature['points'] as $point ) : ?>
							<li><span class="dashicons dashicons-yes" aria-hidden="true"></span><?php echo esc_html( $point ); ?></li>
						<?php endforeach; ?>
					</ul>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="wpoau-addons-resources" aria-labelledby="wpoau-addons-resources-title">
		<div class="wpoau-addons-resources-copy">
			<p class="wpoau-addons-kicker"><?php esc_html_e( 'DOCUMENTATION AND SUPPORT', 'online-active-users' ); ?></p>
			<h2 id="wpoau-addons-resources-title"><?php esc_html_e( 'Need a hand?', 'online-active-users' ); ?></h2>
			<p><?php esc_html_e( 'Find plugin guidance or contact our team for help with Online Active Users.', 'online-active-users' ); ?></p>
		</div>
		<div class="wpoau-addons-resources-links">
			<a href="<?php echo esc_url( $docs_url ); ?>" target="_blank" rel="noopener noreferrer">
				<span class="dashicons dashicons-book-alt" aria-hidden="true"></span>
				<span><strong><?php esc_html_e( 'Documentation', 'online-active-users' ); ?></strong><small><?php esc_html_e( 'Read the plugin guide', 'online-active-users' ); ?></small></span>
				<span class="dashicons dashicons-arrow-up-right-alt2" aria-hidden="true"></span>
			</a>
			<a href="<?php echo esc_url( $contact_url ); ?>" target="_blank" rel="noopener noreferrer">
				<span class="dashicons dashicons-email-alt" aria-hidden="true"></span>
				<span><strong><?php esc_html_e( 'Contact support', 'online-active-users' ); ?></strong><small><?php esc_html_e( 'Get help with WP Online Active Users', 'online-active-users' ); ?></small></span>
				<span class="dashicons dashicons-arrow-up-right-alt2" aria-hidden="true"></span>
			</a>
		</div>
	</section>

	<section class="wpoau-addons-start" aria-labelledby="wpoau-start-title">
		<h2 id="wpoau-start-title"><?php esc_html_e( 'Get started in three steps', 'online-active-users' ); ?></h2>
		<ol>
			<li><strong><?php esc_html_e( 'Choose your plan', 'online-active-users' ); ?></strong><span><?php esc_html_e( 'Pick the license duration that suits your website.', 'online-active-users' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Install the Pro add-on', 'online-active-users' ); ?></strong><span><?php esc_html_e( 'Download it from your account and keep the free plugin active.', 'online-active-users' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Activate your license', 'online-active-users' ); ?></strong><span><?php esc_html_e( 'Add your key in License & Plan, then enable the features you need.', 'online-active-users' ); ?></span></li>
		</ol>
	</section>

	<section class="wpoau-addons-bottom" aria-labelledby="wpoau-upgrade-title">
		<div>
			<h2 id="wpoau-upgrade-title"><?php esc_html_e( 'Ready to make your website more welcoming?', 'online-active-users' ); ?></h2>
			<p><?php esc_html_e( 'Bring welcome notifications, support, feedback and exports together in one workspace.', 'online-active-users' ); ?></p>
		</div>
		<a class="wpoau-addons-primary" href="<?php echo esc_url( $buy_url ); ?>"><?php esc_html_e( 'Buy Pro Version', 'online-active-users' ); ?><span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span></a>
	</section>
	<p class="wpoau-addons-existing">
		<?php esc_html_e( 'Already purchased? Install your Pro download, then activate your key in', 'online-active-users' ); ?>
		<a href="<?php echo esc_url( admin_url( 'users.php?page=wpoau-active-users&tab=license' ) ); ?>"><?php esc_html_e( 'License & Plan', 'online-active-users' ); ?></a>.
	</p>
</div>
