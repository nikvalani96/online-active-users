<?php
// Run: php tests/promo-run.php
define( 'ABSPATH', __DIR__ );
$GLOBALS['admin'] = true;
$GLOBALS['meta'] = array();
$GLOBALS['user'] = 7;
$GLOBALS['premium'] = false;
function __( $text ) { return $text; }
function add_action() {}
function sanitize_key( $key ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( $key ) ); }
function add_query_arg( $key, $value, $url ) { return $url . ( str_contains( $url, '?' ) ? '&' : '?' ) . rawurlencode( $key ) . '=' . rawurlencode( $value ); }
function wp_parse_url( $url ) { return parse_url( $url ); }
function current_user_can( $cap ) { return $GLOBALS['admin']; }
function get_current_user_id() { return $GLOBALS['user']; }
function get_user_meta( $user, $key, $single ) { return $GLOBALS['meta'][$user][$key] ?? array(); }
class WPOAU_Offer_Feed {
	public static function current() { return $GLOBALS['feed'] ?? null; }
}
class WPOAU_Hub {
	public static function buy_url() { return 'https://webizito.com/pro-account/'; }
}
require dirname( __DIR__ ) . '/inc/class-wpoau-promo.php';

$checks = 0;
function check( $name, $ok ): void {
	global $checks;
	++$checks;
	if ( ! $ok ) {
		throw new RuntimeException( $name );
	}
}
$campaign = array(
	'id' => 'seasonal-sale', 'revision' => 2, 'enabled' => true, 'theme' => 'christmas',
	'headline' => 'Save on Pro', 'description' => 'Unlock every feature.', 'discount_percent' => 30,
	'coupon_code' => 'XMAS30', 'cta_text' => 'Get Pro', 'cta_url' => 'https://webizito.com/pro-account/',
	'starts_at' => '2026-11-20 00:00', 'expires_at' => '2026-12-31 23:59', 'timezone' => 'UTC',
);
$GLOBALS['feed'] = array( 'version' => 2, 'enabled' => true, 'campaigns' => array( $campaign ) );
$now = new DateTimeImmutable( '2026-11-25 12:00 UTC' );
check( 'active campaign is available', WPOAU_Promo::active( $campaign, $now ) );
check( 'future campaign is hidden', ! WPOAU_Promo::active( $campaign, new DateTimeImmutable( '2026-11-19 23:59 UTC' ) ) );
check( 'expired campaign is hidden', ! WPOAU_Promo::active( $campaign, new DateTimeImmutable( '2027-01-01 00:00 UTC' ) ) );
$campaign['starts_at'] = '';
$campaign['expires_at'] = '';
$GLOBALS['feed']['campaigns'][0] = $campaign;
check( 'campaign is shown to administrators', null !== WPOAU_Promo::card() );
$GLOBALS['premium'] = true;
check( 'campaign is unchanged for Pro installations', null !== WPOAU_Promo::card() );
$GLOBALS['premium'] = false;
$GLOBALS['meta'][7][WPOAU_Promo::META] = array( WPOAU_Promo::key( $campaign ) );
check( 'dismissal hides only current revision', null === WPOAU_Promo::card() );
$campaign['revision'] = 3;
$GLOBALS['feed']['campaigns'][0] = $campaign;
check( 'new campaign revision is shown', null !== WPOAU_Promo::card() );
check( 'plugin offer uses the active seasonal label and coupon URL', 'Get Christmas Deal' === WPOAU_Promo::plugin_offer()['label'] && str_contains( WPOAU_Promo::plugin_offer()['url'], 'wpoau_coupon=XMAS30' ) );
$GLOBALS['admin'] = false;
check( 'banner does not render for non-administrators', null === WPOAU_Promo::card() );
$GLOBALS['admin'] = true;
check( 'HTTPS Webizito CTA URLs only', WPOAU_Promo::valid_url( $campaign['cta_url'] ) && ! WPOAU_Promo::valid_url( 'https://evil.example/' ) );
echo "All $checks promo checks passed.\n";
