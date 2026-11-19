<?php
// Isolated server/client seasonal campaign checks.
define( 'ABSPATH', __DIR__ );
define( 'WPOAU_PLUGIN_FILE', __FILE__ );
define( 'HOUR_IN_SECONDS', 3600 );
$GLOBALS['options'] = array();
$GLOBALS['user'] = 1;
function add_action() {}
function register_deactivation_hook() {}
function get_option( $key, $default = false ) { return $GLOBALS['options'][$key] ?? $default; }
function update_option( $key, $value, ...$args ) { $GLOBALS['options'][$key] = $value; return true; }
function add_option( $key, $value, ...$args ) { if ( isset( $GLOBALS['options'][$key] ) ) { return false; } $GLOBALS['options'][$key] = $value; return true; }
function delete_option( $key ) { unset( $GLOBALS['options'][$key] ); return true; }
function wp_parse_url( $url ) { return parse_url( $url ); }
function esc_url_raw( $value, $protocols = array() ) { return filter_var( $value, FILTER_VALIDATE_URL ) ? $value : ''; }
function sanitize_text_field( $value ) { return trim( strip_tags( $value ) ); }
function wp_unslash( $value ) { return $value; }
function absint( $value ) { return abs( (int) $value ); }
function wp_next_scheduled() { return false; }
function wp_schedule_single_event() { return true; }
function wp_clear_scheduled_hook() {}
function is_ssl() { return true; }
function wp_get_environment_type() { return 'production'; }
function wp_safe_remote_get() { return null; }
function is_wp_error( $value ) { return false; }
function wp_remote_retrieve_response_code() { return 500; }
function wp_remote_retrieve_header() { return ''; }
function wp_remote_retrieve_body() { return ''; }
function WC() { return $GLOBALS['wc']; }
function wc_get_coupon_id_by_code( $code ) { return 'BF40' === $code ? 9 : 0; }
class WP_REST_Response {
	public $data;
	public function __construct( $data, $status ) { $this->data = $data; }
	public function header( $name, $value ) {}
}
class Test_Promo_Session {
	public $values = array();
	public function set( $key, $value ) { $this->values[ $key ] = $value; }
	public function get( $key ) { return $this->values[ $key ] ?? null; }
}
class Test_Promo_Cart {
	public $applied = array();
	public function get_cart_contents_count() { return 1; }
	public function has_discount( $coupon ) { return in_array( $coupon, $this->applied, true ); }
	public function apply_coupon( $coupon ) { $this->applied[] = $coupon; return true; }
}
class WPOAU_Promo {
	public static function valid_url( $url ) {
		$p = parse_url( $url );
		return is_array( $p ) && 'https' === ( $p['scheme'] ?? '' ) && 'webizito.com' === ( $p['host'] ?? '' ) && ! isset( $p['user'], $p['pass'], $p['port'] );
	}
}
require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'class-wpoau-offer-feed.php';
require dirname( __DIR__, 2 ) . DIRECTORY_SEPARATOR . 'wpoau-license-server' . DIRECTORY_SEPARATOR . 'class-offer-campaigns.php';

$checks = 0;
function check( $name, $ok ): void {
	global $checks;
	++$checks;
	if ( ! $ok ) {
		throw new RuntimeException( $name );
	}
}
$settings = array(
	'enabled' => true, 'theme' => 'black-friday', 'headline' => 'Black Friday: 40% off',
	'description' => 'Unlock Pro for less.', 'discount_percent' => 40, 'coupon_code' => 'BF40',
	'cta_text' => 'Upgrade now', 'cta_url' => 'https://webizito.com/pro-account/',
	'starts_at' => '2026-11-20 00:00', 'expires_at' => '2026-11-30 23:59',
	'timezone' => 'Asia/Kolkata', 'revision' => 4,
);
update_option( WPOAU\Server\Offer_Campaigns::OPTION, $settings );
$server = new WPOAU\Server\Offer_Campaigns();
$payload = $server->feed()->data;
check( 'server exposes one rich campaign', 2 === $payload['version'] && 1 === count( $payload['campaigns'] ) );
check( 'campaign supports seasonal and commerce fields', 'black-friday' === $payload['campaigns'][0]['theme'] && 'BF40' === $payload['campaigns'][0]['coupon_code'] );
check( 'client accepts the server feed', false !== WPOAU_Offer_Feed::validate( $payload ) );
$bad = $payload;
$bad['campaigns'][0]['headline'] = '<script>bad</script>';
check( 'client rejects unescaped campaign HTML', false === WPOAU_Offer_Feed::validate( $bad ) );
$bad = $payload;
$bad['campaigns'][0]['cta_url'] = 'https://evil.example/';
check( 'client rejects foreign CTA domains', false === WPOAU_Offer_Feed::validate( $bad ) );
$bad = $payload;
$bad['campaigns'][0]['theme'] = 'unknown';
check( 'client rejects unknown themes', false === WPOAU_Offer_Feed::validate( $bad ) );
update_option( WPOAU\Server\Offer_Campaigns::OPTION, array_merge( $settings, array( 'enabled' => false ) ) );
$disabled = $server->feed()->data;
check( 'disabled campaign emits no banner', false === $disabled['enabled'] && array() === $disabled['campaigns'] );
update_option( WPOAU\Server\Offer_Campaigns::OPTION, array_merge( $settings, array( 'starts_at' => '', 'expires_at' => '' ) ) );
$GLOBALS['wc'] = (object) array( 'cart' => new Test_Promo_Cart(), 'session' => new Test_Promo_Session() );
$_GET['wpoau_coupon'] = 'BF40';
$server->capture_coupon();
check( 'coupon query is captured in WooCommerce session', 'BF40' === WC()->session->get( 'wpoau_promo_coupon' ) );
$server->apply_captured_coupon();
check( 'captured coupon is applied by WooCommerce cart', in_array( 'BF40', WC()->cart->applied, true ) && null === WC()->session->get( 'wpoau_promo_coupon' ) );
echo "All $checks feed checks passed.\n";
