<?php
// phpcs:ignoreFile WordPress.Files.FileName.InvalidClassFileName -- Main plugin bootstrap file; must keep the plugin-slug filename for WordPress to read its header.
/**
 * Plugin Name: Online Active Users
 * Plugin Title: Online Active Users Plugin
 * Plugin URI: https://wordpress.org/plugins/online-active-users/
 * Description: Monitor and display real-time online users and last seen status on your WordPress site with Online Active Users plugin.
 * Tags: online users, active users, user tracking, user status, user activity
 * Version: 3.4.5
 * Requires at least: 6.3
 * Requires PHP: 8.0
 * Author: Webizito
 * Author URI: http://webizito.com/
 * Contributors: valani9099
 * License:  GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: online-active-users
 * Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=APRNBJUZHRP7G
 *
 * @package Online_Active_Users
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WPOAU_PLUGIN_DIR' ) ) {
	define( 'WPOAU_PLUGIN_DIR', __DIR__ );
}

if ( ! defined( 'WPOAU_PLUGIN_FILE' ) ) {
	define( 'WPOAU_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'WPOAU_VERSION' ) ) {
	define( 'WPOAU_VERSION', '3.4.5' );
}

// class-wpoau-active-users.php replaces the old inc/webi-functions.php (removed): both defined the
// same Wpoau_Active_Users class, and this one has the escaping/i18n/WPCS fixes applied.
require_once WPOAU_PLUGIN_DIR . '/inc/class-wpoau-active-users.php';
require_once WPOAU_PLUGIN_DIR . '/class-webi-custom-widget.php';

if ( ! class_exists( 'Webi_Active_User' ) ) {

	/**
	 * Main plugin bootstrap: wires up hooks for tracking and displaying online users.
	 */
	class Webi_Active_User {

		/**
		 * Active users tracking helper instance.
		 *
		 * @var Wpoau_Active_Users
		 */
		public $wpoau;

		/**
		 * Register all plugin hooks.
		 */
		public function __construct() {

			$this->wpoau            = new Wpoau_Active_Users( $this );
			$GLOBALS['wpoau_users'] = $this->wpoau;

			register_activation_hook( WPOAU_PLUGIN_FILE, array( $this->wpoau, 'wpoau_users_status_init' ) );
			add_action( 'init', array( $this->wpoau, 'wpoau_users_status_init' ) );
			add_action( 'init', array( $this->wpoau, 'webi_track_user_activity' ) );
			add_action( 'init', array( $this, 'wpoau_register_active_users_block' ) );
			add_action( 'clear_auth_cookie', array( $this, 'wpoau_user_logout' ) );
			add_action( 'wp_loaded', array( $this, 'wpoau_enqueue_script' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'webi_enqueue_custom_scripts' ) );
			add_action( 'admin_init', array( $this->wpoau, 'wpoau_users_status_init' ) );
			add_action( 'wp_dashboard_setup', array( $this->wpoau, 'wpoau_active_users_metabox' ) );
			add_filter( 'manage_users_columns', array( $this->wpoau, 'wpoau_user_columns_head' ) );
			add_filter( 'manage_users_custom_column', array( $this->wpoau, 'wpoau_user_columns_content' ), 10, 3 );
			add_filter( 'views_users', array( $this, 'wpoau_modify_user_view' ) );
			add_action( 'admin_bar_menu', array( $this->wpoau, 'wpoau_admin_bar_link' ), 999 );
			add_filter( 'plugin_row_meta', array( $this, 'wpoau_support_and_faq_links' ), 10, 4 );
			add_action( 'admin_menu', array( $this, 'wpoau_add_admin_submenu' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( WPOAU_PLUGIN_FILE ), array( $this, 'wpoau_plugin_by_link' ), 10, 1 );
			add_action( 'admin_notices', array( $this, 'wpoau_display_notice' ) );
			register_deactivation_hook( WPOAU_PLUGIN_FILE, array( $this, 'wpoau_display_notice' ) );
			register_deactivation_hook( WPOAU_PLUGIN_FILE, array( $this, 'wpoau_delete_transient' ) );

			$this->wpoau->wpoau_active_user_shortcode();
		}

		/**
		 * Enqueue the front-end plugin stylesheet.
		 */
		public function wpoau_enqueue_script() {
			wp_enqueue_style( 'style-css', plugin_dir_url( WPOAU_PLUGIN_FILE ) . 'assets/css/style.css', array(), WPOAU_VERSION );
		}

		/**
		 * Enqueue admin scripts and localize the last-seen timestamp formatting.
		 */
		public function webi_enqueue_custom_scripts() {
			wp_enqueue_script( 'webi-plugin-script', plugin_dir_url( WPOAU_PLUGIN_FILE ) . 'assets/js/custom.js', array( 'jquery' ), WPOAU_VERSION, true );

			wp_add_inline_script(
				'webi-plugin-script',
				"
                jQuery(document).ready(function($) {
                    $('.webizito-last-seen').each(function() {
                        var timestamp = $(this).data('timestamp');
                        if (timestamp) {
                            var date = new Date(timestamp * 1000);
                            $(this).text(date.toLocaleString());
                        }
                    });
                });
            "
			);
		}

		/**
		 * Remove a user from the online list when they log out.
		 */
		public function wpoau_user_logout() {
			if ( ! is_user_logged_in() ) {
				return; // Prevent errors if already logged out.
			}

			$user_id = get_current_user_id();
			if ( ! $user_id ) {
				return;
			}

			$logged_in_users = get_transient( 'users_status' );

			if ( isset( $logged_in_users[ $user_id ] ) ) {
				unset( $logged_in_users[ $user_id ] );
				set_transient( 'users_status', $logged_in_users, 60 );
			}

			update_user_meta( $user_id, 'last_seen', time() );
		}

		/**
		 * Get a count of online users, or an array of online user records.
		 *
		 * @param string $type Either 'count' for the online user count, or anything else for the full array.
		 * @return int|array
		 */
		public function wpoau_online_users( $type = 'count' ) {
			$logged_in_users = get_transient( 'users_status' );

			// If no users are online.
			if ( empty( $logged_in_users ) ) {
				return ( 'count' === $type ) ? 0 : false;
			}

			$user_online_count = 0;
			$online_users      = array();
			foreach ( $logged_in_users as $user ) {
				if ( ! empty( $user['username'] ) && isset( $user['last'] ) && $user['last'] > time() - 50 ) {
					$online_users[] = $user;
					++$user_online_count;
				}
			}

			return ( 'count' === $type ) ? $user_online_count : $online_users;
		}

		/**
		 * Register the "Online Active Users" submenu under Users.
		 */
		public function wpoau_add_admin_submenu() {
			add_users_page(
				__( 'Online Active Users', 'online-active-users' ),
				__( 'Online Active Users', 'online-active-users' ),
				'list_users',
				'wpoau-active-users',
				array( $this, 'wpoau_active_users_page' )
			);
		}

		/**
		 * Render the "Online Active Users" admin submenu page.
		 */
		public function wpoau_active_users_page() {
			echo '<div class="wrap"><h1>' . esc_html__( 'Online Users Lists', 'online-active-users' ) . '</h1>';

			$users = $this->wpoau_online_users( 'array' );

			if ( empty( $users ) ) {
				echo '<p>' . esc_html__( 'No users are online right now.', 'online-active-users' ) . '</p></div>';
				return;
			}

			require_once WPOAU_PLUGIN_DIR . '/inc/user-list-table.php';
		}

		/**
		 * Add an "Online" view/count to the Users list table view links.
		 *
		 * @param array $views Existing view links.
		 * @return array
		 */
		public function wpoau_modify_user_view( $views ) {

			$logged_in_users = get_transient( 'users_status' );
			$user            = wp_get_current_user();

			$logged_in_users[ $user->ID ] = array(
				'id'       => $user->ID,
				'username' => $user->user_login,
				'last'     => time(),
			);

			$view = '<a href="' . esc_url( admin_url( 'users.php?page=wpoau-active-users' ) ) . '">' . sprintf(
				/* translators: %s: HTML span showing the count of currently online users, e.g. "(3)". */
				esc_html__( 'User Online %s', 'online-active-users' ),
				'<span class="count">(' . absint( $this->wpoau_online_users( 'count' ) ) . ')</span>'
			) . '</a>';

			$views['status'] = $view;
			return $views;
		}

		/**
		 * Add Support and Docs links to the plugin's row meta on the Plugins page.
		 *
		 * @param array  $links_array     Existing plugin row meta links.
		 * @param string $plugin_file_name Path to the plugin file relative to the plugins directory.
		 * @param array  $plugin_data      Unused. Required by the plugin_row_meta filter signature.
		 * @param string $status           Unused. Required by the plugin_row_meta filter signature.
		 * @return array
		 */
		public function wpoau_support_and_faq_links( $links_array, $plugin_file_name, $plugin_data, $status ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
			if ( strpos( $plugin_file_name, basename( WPOAU_PLUGIN_FILE ) ) ) {

				// You can still use `array_unshift()` to add links at the beginning.
				$links_array[] = '<a href="' . esc_url( 'https://wordpress.org/support/plugin/online-active-users/' ) . '" target="_blank">' . esc_html__( 'Support', 'online-active-users' ) . '</a>';
				$links_array[] = '<a href="' . esc_url( 'https://webizito.com/wp-online-active-users/' ) . '" target="_blank">' . esc_html__( 'Docs', 'online-active-users' ) . '</a>';
				$links_array[] = '<strong><a href="' . esc_url( 'https://wordpress.org/support/plugin/online-active-users/reviews/?rate=5#new-post' ) . '" target="_blank">' . esc_html__( 'Rate our plugin', 'online-active-users' ) . '  <span style="color:#ffb900;font-size: 18px;position:relative;top:0.1em;">★★★★★</span></a></strong>';
			}
			return $links_array;
		}

		/**
		 * Add "Donate" and "By Webizito" links to the plugin's action links.
		 *
		 * @param array $links Existing plugin action links.
		 * @return array
		 */
		public function wpoau_plugin_by_link( $links ) {
			$links[] = '<a href="' . esc_url( 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=APRNBJUZHRP7G' ) . '" target="_blank"><span>' . esc_html__( 'Donate', 'online-active-users' ) . '</span></a>';
			return $links;
		}

		/**
		 * Display a review/donation admin notice.
		 */
		public function wpoau_display_notice() {
			$message = sprintf(
				/* translators: 1: opening review link tag, 2: closing link tag, 3: opening donation link tag. */
				esc_html__( 'Enjoying our WP Online Active Users plugin? Please consider leaving us a review %1$shere%2$s. Or support with a small donation %3$shere%2$s. We would greatly appreciate it!', 'online-active-users' ),
				'<a href="' . esc_url( 'https://wordpress.org/support/plugin/online-active-users/reviews/?rate=5#new-post' ) . '" target="_blank">',
				'</a>',
				'<a href="' . esc_url( 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=APRNBJUZHRP7G' ) . '" target="_blank">'
			);

			echo '<div class="notice notice-success is-dismissible wp-online-active-users-notice" id="wp-online-active-users-notice">';
			echo '<p>' . wp_kses_post( $message ) . '</p>';
			echo '</div>';
		}

		/**
		 * Delete the plugin's online-users transient on deactivation.
		 */
		public function wpoau_delete_transient() {
			delete_transient( 'users_status' );
		}

		/**
		 * Register the Online Active Users Count block.
		*/
		public function wpoau_register_active_users_block() {
			register_block_type(
				WPOAU_PLUGIN_DIR . '/blocks/active-users',
				array(
					'render_callback' => array( $this->wpoau, 'wpoau_active_user' ),
				)
			);
		}
	}
}

$wpoau_plugin = new Webi_Active_User();

// Register the custom widget.
add_action(
	'widgets_init',
	function () {
		register_widget( 'Webi_Custom_Widget' );
	}
);
