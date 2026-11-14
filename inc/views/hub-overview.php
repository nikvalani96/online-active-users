<?php
/**
 * Overview view. Variables are supplied by the controller method.
 *
 * @package Online_Active_Users
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! current_user_can( 'list_users' ) ) {
	wp_die( esc_html__( 'Access denied.', 'online-active-users' ) );
}
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin tab routing.
$wpoau_tab = isset( $_GET['tab'] ) && is_string( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'overview';
if ( ! in_array( $wpoau_tab, array( 'overview', 'automation', 'appearance', 'assistance', 'feedback', 'export', 'sessions', 'announcements', 'live_viewers', 'addons', 'license' ), true ) ) {
	$wpoau_tab = 'overview';
}
if ( 'addons' === $wpoau_tab && WPOAU_Addons::pro_loaded() ) {
	$wpoau_tab = 'overview';
}
$pro   = WPOAU_Hub::premium();
$badge = $pro && defined( 'WPOAU\Pro\VERSION' ) ? 'Pro v' . constant( 'WPOAU\Pro\VERSION' ) : 'Free v' . WPOAU_VERSION;
?>

<div class="wpoau-header">
	<div>
		<span class="wpoau-eyebrow">WEBIZITO</span>
		<h1>Online Active Users</h1>
		<p>Understand who is here. Bring your users back.</p>
	</div>
	<span class="wpoau-badge"><?php echo esc_html( $badge ); ?></span>
</div>
<nav class="wpoau-nav" aria-label="Plugin navigation">

<?php
foreach ( array(
	'overview'      => 'Active Users',
	'sessions'      => 'Devices & Sessions',
	'automation'    => 'Returning User Notifications',
	'appearance'    => 'Notification Appearance',
	'assistance'    => 'Support Chat',
	'feedback'      => 'User Feedback',
	'export'        => 'Export Users',
	'announcements' => 'Announcements',
	'live_viewers'  => 'Live Page Viewers',
	'addons'        => __( 'Add-ons', 'online-active-users' ),
	'license'       => 'License & Plan',
) as $key => $label ) {
	if ( 'addons' === $key && WPOAU_Addons::pro_loaded() ) {
		continue;
	}
	if ( ! class_exists( 'WPOAU\Pro\License' ) && in_array( $key, array( 'automation', 'appearance', 'assistance', 'feedback', 'export', 'sessions', 'announcements', 'live_viewers' ), true ) ) {
		continue;
	}
	if ( 'overview' !== $key && ! current_user_can( 'manage_options' ) ) {
		continue;
	}
	$tab_url = add_query_arg(
		array(
			'page' => 'wpoau-active-users',
			'tab'  => $key,
		),
		admin_url( 'users.php' )
	);
	?>
	<a class="<?php echo 'addons' === $key ? 'wpoau-addons-tab' : ''; ?>" <?php echo ( $wpoau_tab === $key ? 'aria-current="page"' : '' ); ?> href="<?php echo esc_url( $tab_url ); ?>"><?php echo esc_html( $label ); ?><?php echo ( in_array( $key, array( 'automation', 'appearance', 'assistance', 'feedback', 'export', 'sessions', 'announcements' ), true ) && ! $pro ? ' <span class="wpoau-tag">Locked</span>' : '' ); ?>
		<?php if ( 'addons' === $key ) : ?>
			<span class="wpoau-addons-tab-badge">PRO</span>
		<?php endif; ?>
	</a>
<?php } ?>
</nav>

<?php
$resource_tabs  = array( 'sessions', 'automation', 'appearance', 'assistance', 'feedback', 'export', 'announcements', 'live_viewers', 'license' );
$show_resources = WPOAU_Addons::pro_loaded() && in_array( $wpoau_tab, $resource_tabs, true );
if ( 'overview' === $wpoau_tab ) {
	?>
<div class="wpoau-tab-layout<?php echo $show_resources ? ' has-resources' : ''; ?>">
	<main class="wpoau-tab-content">
		<?php do_action( 'wpoau_hub_overview_settings' ); ?>
	</main>
	<?php if ( $show_resources ) : ?>
		<?php include __DIR__ . '/admin-resources.php'; ?>
	<?php endif; ?>
</div>
	<?php
	return true;
}
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Access denied.' );
}
?>
<div class="wpoau-tab-layout<?php echo $show_resources ? ' has-resources' : ''; ?>">
	<main class="wpoau-tab-content">
<?php
if ( has_action( 'wpoau_hub_' . $wpoau_tab ) ) {
	do_action( 'wpoau_hub_' . $wpoau_tab );
} else {
	?>
<div class="wrap wpoau-panel">
	<span class="wpoau-eyebrow">CURRENT VERSION: FREE</span>
	<h1><?php echo ( 'license' === $wpoau_tab ? 'Your Free plan is active' : 'Unlock user return automation' ); ?></h1>
	<p>Online user tracking is ready to use. Pro automation requires the Pro add-on and an activated license.</p>
	<div class="wpoau-lock wpoau-free-upsell">
		<div class="wpoau-free-upsell-copy">
			<strong>Included with Pro</strong>
			<p>Return messages · Inactivity rules · Role targeting · Admin email alerts</p>
		</div>
		<a class="button button-primary" href="<?php echo esc_url( WPOAU_Hub::buy_url() ); ?>">View Pro add-on<span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></a>
	</div>
	<p>Already purchased? Download the Pro add-on from your account, install it, then activate your key here.</p>
</div>
	<?php
}
?>
	</main>
	<?php if ( $show_resources ) : ?>
		<?php include __DIR__ . '/admin-resources.php'; ?>
	<?php endif; ?>
</div>
<?php
return false;
