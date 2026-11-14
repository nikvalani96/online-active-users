<?php
/**
 * Free-edition introduction to the optional Pro add-on.
 *
 * @package Online_Active_Users
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Keeps Pro discovery separate from tracking and license enforcement. */
final class WPOAU_Addons {
	/** Register the free-only screen and its stylesheet. */
	public function __construct() {
		add_action( 'wpoau_hub_addons', array( $this, 'render' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
	}

	/** Whether the Pro add-on is loaded, including an unlicensed installation. */
	public static function pro_loaded(): bool {
		return class_exists( 'WPOAU\\Pro\\License' );
	}

	/** Enqueue only on the free add-ons screen. */
	public function assets(): void {
		// Read-only routing; no settings or user data are changed.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin page routing.
		$page = isset( $_GET['page'] ) && is_string( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin tab routing.
		$tab = isset( $_GET['tab'] ) && is_string( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
		if ( self::pro_loaded() || ! current_user_can( 'manage_options' ) || ! current_user_can( 'list_users' ) || 'wpoau-active-users' !== $page || 'addons' !== $tab ) {
			return;
		}
		wp_enqueue_style( 'wpoau-addons', plugin_dir_url( WPOAU_PLUGIN_FILE ) . 'assets/css/addons.css', array(), WPOAU_VERSION );
	}

	/** Actual features available in the single Pro add-on, without speculative claims. */
	public static function features(): array {
		return array(
			array(
				'icon'   => 'desktop',
				'title'  => __( 'Understand browser sessions', 'online-active-users' ),
				'text'   => __( 'See estimated devices, current page paths and activity for signed-in users.', 'online-active-users' ),
				'points' => array( __( 'Optional login limits and session logout', 'online-active-users' ), __( 'Administrator-targeted session details', 'online-active-users' ) ),
			),
			array(
				'icon'   => 'megaphone',
				'title'  => __( 'Keep online users informed', 'online-active-users' ),
				'text'   => __( 'Display a maintenance notice or website update without sending email.', 'online-active-users' ),
				'points' => array( __( 'Scheduled announcement banner', 'online-active-users' ), __( 'Role targeting and optional dismissal', 'online-active-users' ) ),
			),
			array(
				'icon'   => 'backup',
				'title'  => __( 'Welcome returning users', 'online-active-users' ),
				'text'   => __( 'Turn a return visit into a useful next step with a personal welcome message.', 'online-active-users' ),
				'points' => array( __( 'Inactivity days and cooldown rules', 'online-active-users' ), __( 'Role targeting and a custom action button', 'online-active-users' ) ),
			),
			array(
				'icon'   => 'email-alt',
				'title'  => __( 'Keep your team informed', 'online-active-users' ),
				'text'   => __( 'Receive admin email alerts when users return, ask for support or send feedback.', 'online-active-users' ),
				'points' => array( __( 'HTML welcome-back email customization', 'online-active-users' ), __( 'Optional support and feedback alerts', 'online-active-users' ) ),
			),
			array(
				'icon'   => 'art',
				'title'  => __( 'Make notifications your own', 'online-active-users' ),
				'text'   => __( 'Match welcome notifications to your website without editing CSS.', 'online-active-users' ),
				'points' => array( __( 'Colors, buttons and screen position', 'online-active-users' ), __( 'Live appearance preview while editing', 'online-active-users' ) ),
			),
			array(
				'icon'   => 'format-chat',
				'title'  => __( 'Offer human and AI support chat', 'online-active-users' ),
				'text'   => __( 'Give logged-in users a single place to get an AI response or talk with your team. A person can take over when needed.', 'online-active-users' ),
				'points' => array( __( 'Manual replies and conversation history', 'online-active-users' ), __( 'Optional AI replies using your provider API key', 'online-active-users' ), __( 'Provider usage may incur separate charges', 'online-active-users' ) ),
			),
			array(
				'icon'   => 'smiley',
				'title'  => __( 'Learn what users think', 'online-active-users' ),
				'text'   => __( 'Ask a short question at the right moment and review responses in your dashboard.', 'online-active-users' ),
				'points' => array( __( 'Smiley ratings and short text responses', 'online-active-users' ), __( 'Audience, page and repeat-frequency rules', 'online-active-users' ) ),
			),
			array(
				'icon'   => 'download',
				'title'  => __( 'Turn activity into a useful CSV', 'online-active-users' ),
				'text'   => __( 'Build a focused user list for reporting with only the columns you need.', 'online-active-users' ),
				'points' => array( __( 'Online, offline, role and last-seen filters', 'online-active-users' ), __( 'Preview results before downloading', 'online-active-users' ) ),
			),
		);
	}

	/** Render only for administrators using the free edition. */
	public function render(): void {
		if ( self::pro_loaded() || ! current_user_can( 'manage_options' ) || ! current_user_can( 'list_users' ) ) {
			return;
		}
		$features    = self::features();
		$buy_url     = WPOAU_Hub::buy_url();
		$docs_url    = WPOAU_Hub::docs_url();
		$contact_url = WPOAU_Hub::contact_url();
		require __DIR__ . '/views/addons.php';
	}
}

new WPOAU_Addons();
