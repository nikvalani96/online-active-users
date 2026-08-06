<?php
/**
 * Renders the online users list table on the "Online Active Users" admin page.
 *
 * @package Online_Active_Users
 */

// This template is always require()'d from inside Webi_Active_User::wpoau_active_users_page(),
// so its variables live in that method's local scope at runtime, not the global scope PHPCS
// assumes for top-level file code.
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

defined( 'ABSPATH' ) || exit;

global $wpoau_users;

$all_roles     = array();
$all_countries = array();

// @phpstan-ignore-next-line variable.undefined -- $users is always set by the caller, Webi_Active_User::wpoau_active_users_page(), before requiring this template.
foreach ( $users as $u ) {
	$u_obj = get_userdata( $u['id'] );
	if ( ! empty( $u_obj->roles ) ) {
		foreach ( $u_obj->roles as $r ) {
			$all_roles[ $r ] = ucfirst( $r );
		}
	}
	if ( ! empty( $u['country'] ) ) {
		$all_countries[ $u['country'] ] = $u['country'];
	}
}

// This form only filters the list displayed below; it does not change any state, so no nonce is required.
$filter_role    = isset( $_GET['filter_role'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_role'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$filter_country = isset( $_GET['filter_country'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_country'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

echo '<div class="webi-table-container">';
echo '<div class="table-header-wrap">';
echo '<form method="get">
    <input type="hidden" name="page" value="wpoau-active-users" />
    <select name="filter_role" style="margin-right: 10px;">
        <option value="">' . esc_html__( 'All Roles', 'online-active-users' ) . '</option>';
foreach ( $all_roles as $user_role => $label ) {
	$selected = ( $filter_role === $user_role ) ? 'selected' : '';
	echo '<option value="' . esc_attr( $user_role ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $label ) . '</option>';
}
echo '</select>';

echo '<select name="filter_country" style="margin-right: 10px;">
        <option value="">' . esc_html__( 'All Countries', 'online-active-users' ) . '</option>';
foreach ( $all_countries as $c ) {
	$selected      = ( $filter_country === $c ) ? 'selected' : '';
	$country_label = ( 'Unknown' === $c ) ? __( 'Unknown', 'online-active-users' ) : $c;
	echo '<option value="' . esc_attr( $c ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $country_label ) . '</option>';
}
echo '</select>';

echo '<button class="button">' . esc_html__( 'Filter', 'online-active-users' ) . '</button>';
echo '</form>';
echo '<div class="top-total-user">' . esc_html__( 'Total Online Users:', 'online-active-users' ) . ' ' . absint( count( $users ) ) . '</div>';
echo '</div>';
echo '<table class="webi-online-table">';

echo '<thead><tr>
    <th>' . esc_html__( 'Sr. No.', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'Avatar', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'Username', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'Full Name', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'User Role', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'IP Address', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'Country', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'Timezone', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'Status', 'online-active-users' ) . '</th>
</tr></thead>';
echo '<tbody>';

if ( $filter_role || $filter_country ) {
	$users = array_filter(
		$users,
		function ( $u ) use ( $filter_role, $filter_country ) {
			$user_obj = get_userdata( $u['id'] );

			$match_role    = ! $filter_role || in_array( $filter_role, $user_obj->roles, true );
			$match_country = ! $filter_country || ( isset( $u['country'] ) && $u['country'] === $filter_country );

			return $match_role && $match_country;
		}
	);
}

$sr = 1;
foreach ( $users as $user ) {
	$user_obj   = get_userdata( $user['id'] );
	$avatar     = get_avatar( $user['id'], 32, '', '', array( 'class' => 'webi-avatar' ) );
	$first_name = get_user_meta( $user_obj->ID, 'first_name', true );
	$last_name  = get_user_meta( $user_obj->ID, 'last_name', true );
	$full_name  = trim( $first_name . ' ' . $last_name );
	if ( ! $full_name ) {
		$full_name = $user_obj->display_name;
	}
	$is_online = $wpoau_users->wpoau_is_user_online( $user['id'] );
	$last_seen = wp_date( 'M j, Y @ g:ia', $user['last'] );
	// 'Unknown' is the untranslated sentinel Wpoau_Active_Users::wpoau_get_user_ip()/_country()/_timezone() store and compare against; translate only for display below.
	$ip           = isset( $user['ip'] ) ? $user['ip'] : 'Unknown';
	$country      = isset( $user['country'] ) ? $user['country'] : 'Unknown';
	$timezone     = isset( $user['timezone'] ) ? $user['timezone'] : 'Unknown';
	$roles        = isset( $user_obj->roles ) ? implode( ', ', $user_obj->roles ) : '—';
	$country_code = strtolower( $wpoau_users->wpoau_get_user_country_code( $ip ) );

	echo '<tr>';
	echo '<td>' . absint( $sr ) . '</td>';
	++$sr;
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() returns core-escaped markup.
	echo '<td>' . $avatar . '</td>';
	echo '<td>' . esc_html( $user_obj->user_login ) . '</td>';
	echo '<td>' . esc_html( $full_name ) . '</td>';
	echo '<td>' . esc_html( ucfirst( $roles ) ) . '</td>';
	echo '<td>' . esc_html( 'Unknown' === $ip ? __( 'Unknown', 'online-active-users' ) : $ip ) . '</td>';
	echo '<td><img src="https://flagcdn.com/16x12/' . esc_attr( $country_code ) . '.webp" class="webi-country-flag" alt="' . esc_attr( $country_code ) . '" />' . esc_html( 'Unknown' === $country ? __( 'Unknown', 'online-active-users' ) : $country ) . '</td>';
	echo '<td>' . esc_html( 'Unknown' === $timezone ? __( 'Unknown', 'online-active-users' ) : $timezone ) . '</td>';
	echo '<td class="status-cols-wrap">';
	if ( $is_online ) {
		echo ' <span class="online-logged-in">●</span> <br />';
	}
	echo $is_online
		? '<strong style="color:green;">' . esc_html__( 'Online Now', 'online-active-users' ) . '</strong>'
		: esc_html( $last_seen );
	echo '</td>';
	echo '</tr>';
}

echo '</tbody>';
echo '</table>';
echo '</div>';
echo '</div>';
