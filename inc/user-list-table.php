<?php
/**
 * Renders the online users list table on the "Online Active Users" admin page.
 *
 * @package Online_Active_Users
 * @var WPOAU_User_Directory $directory User list filters and pagination state.
 * @var array               $users     User activity records for the current page.
 * @var int                 $online_user_count Current online user count.
 */

defined( 'ABSPATH' ) || exit;

global $wpoau_users;

$location_enabled = WPOAU_Privacy::geolocation_enabled();
$extra_columns    = apply_filters( 'wpoau_extra_columns', array() );
$all_roles        = array_map(
	static function ( $role ) {
		return translate_user_role( $role['name'] );
	},
	wp_roles()->roles
);
$all_countries    = array();

$country_records = get_transient( 'users_status' );
$country_records = is_array( $country_records ) ? $country_records : array();
foreach ( $country_records as $u ) {
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

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only table filter; no state changes occur.
$filter_role = isset( $_GET['filter_role'] ) && is_string( $_GET['filter_role'] ) ? sanitize_key( wp_unslash( $_GET['filter_role'] ) ) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only table filter; no state changes occur.
$filter_country = $location_enabled && isset( $_GET['filter_country'] ) && is_string( $_GET['filter_country'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_country'] ) ) : '';
echo '<div class="webi-table-container">';
echo '<div class="table-header-wrap">';
echo '<form method="get">
    <input type="hidden" name="page" value="wpoau-active-users" />
    <select name="filter_role">
        <option value="">' . esc_html__( 'All Roles', 'online-active-users' ) . '</option>';
foreach ( $all_roles as $user_role => $label ) {
	$selected = ( $filter_role === $user_role ) ? 'selected' : '';
	echo '<option value="' . esc_attr( $user_role ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $label ) . '</option>';
}
echo '</select>';
echo '<select name="filter_status"><option value="">All statuses</option><option value="online" ' . selected( $directory->status, 'online', false ) . '>Online</option><option value="offline" ' . selected( $directory->status, 'offline', false ) . '>Offline</option></select>';
echo '<select data-wpoau-geo ' . ( $location_enabled ? '' : 'hidden disabled ' ) . 'name="filter_country">
        <option value="">' . esc_html__( 'All Countries', 'online-active-users' ) . '</option>';
foreach ( $all_countries as $c ) {
	$selected      = ( $filter_country === $c ) ? 'selected' : '';
	$country_label = ( 'Unknown' === $c ) ? __( 'Unknown', 'online-active-users' ) : $c;
	echo '<option value="' . esc_attr( $c ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $country_label ) . '</option>';
}
echo '</select>';
echo '<button class="button">' . esc_html__( 'Filter', 'online-active-users' ) . '</button>';
echo '</form>';
echo '<div class="top-total-user">' . esc_html__( 'Total Online Users:', 'online-active-users' ) . ' ' . absint( $online_user_count ) . '</div>';
echo '</div>';
echo '<table class="webi-online-table">';
echo '<thead><tr>
    <th>' . esc_html__( 'Sr. No.', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'Avatar', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'Username', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'Full Name', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'User Role', 'online-active-users' ) . '</th>
    <th>' . esc_html__( 'IP Address', 'online-active-users' ) . '</th>
';
$geo_visibility = $location_enabled ? '' : ' hidden';
echo '<th data-wpoau-geo' . esc_attr( $geo_visibility ) . '>' . esc_html__( 'Country', 'online-active-users' ) . '</th><th data-wpoau-geo' . esc_attr( $geo_visibility ) . '>' . esc_html__( 'Timezone', 'online-active-users' ) . '</th>';

foreach ( $extra_columns as $label ) {
	echo '<th>' . esc_html( $label ) . '</th>';
}
echo '<th>' . esc_html__( 'Status', 'online-active-users' ) . '</th></tr></thead>';
echo '<tbody>';
$sr = ( $directory->page - 1 ) * 25 + 1;
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
	$last_seen = $user['last'] ? wp_date( 'M j, Y @ g:ia', $user['last'] ) : __( 'No activity recorded', 'online-active-users' );
	// 'Unknown' is the untranslated sentinel Wpoau_Active_Users::wpoau_get_user_ip()/_country()/_timezone() store and compare against; translate only for display below.
	$ip           = isset( $user['ip'] ) ? $user['ip'] : 'Unknown';
	$country      = isset( $user['country'] ) ? $user['country'] : 'Unknown';
	$timezone     = isset( $user['timezone'] ) ? $user['timezone'] : 'Unknown';
	$roles        = isset( $user_obj->roles ) ? implode( ', ', $user_obj->roles ) : '—';
	$country_code = $location_enabled && isset( $user['country_code'] ) && is_string( $user['country_code'] ) ? strtolower( $user['country_code'] ) : 'xx';
	echo '<tr>';
	echo '<td>' . absint( $sr ) . '</td>';
	++$sr;
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() returns core-escaped markup.
	echo '<td>' . $avatar . '</td>';
	echo '<td>' . esc_html( $user_obj->user_login ) . '</td>';
	echo '<td>' . esc_html( $full_name ) . '</td>';
	echo '<td>' . esc_html( ucfirst( $roles ) ) . '</td>';
	echo '<td>' . esc_html( 'Unknown' === $ip ? __( 'Unknown', 'online-active-users' ) : $ip ) . '</td>';
	$flag = WPOAU_Geolocation::flag( $country_code );
	echo '<td data-wpoau-geo' . esc_attr( $geo_visibility ) . '>' . wp_kses_post( $flag ) . esc_html( 'Unknown' === $country ? __( 'Unavailable', 'online-active-users' ) : $country ) . '</td>';
	echo '<td data-wpoau-geo' . esc_attr( $geo_visibility ) . '>' . esc_html( 'Unknown' === $timezone ? __( 'Unavailable', 'online-active-users' ) : $timezone ) . '</td>';
	do_action( 'wpoau_extra_column_cells', $user );
	echo '<td class="status-cols-wrap">';
	$activity_state = WPOAU_User_Directory::activity_state( $is_online, $user['last'] );
	$status_labels  = array(
		'online'     => __( 'Online Now', 'online-active-users' ),
		'offline'    => __( 'Offline', 'online-active-users' ),
		'inactive'   => __( 'Inactive (30+ days)', 'online-active-users' ),
		'unrecorded' => __( 'No activity recorded', 'online-active-users' ),
	);
	echo '<span class="wpoau-presence wpoau-presence--' . esc_attr( $activity_state ) . '"><span class="wpoau-presence-dot" aria-hidden="true"></span><br><strong>' . esc_html( $status_labels[ $activity_state ] ) . '</strong></span>';
	if ( ! $is_online && $user['last'] ) {
		echo '<br><small>' . esc_html__( 'Last seen:', 'online-active-users' ) . ' ' . esc_html( $last_seen ) . '</small>';
	}

	echo '</td>';
	echo '</tr>';
}
if ( ! $users ) {
	echo '<tr><td colspan="' . absint( 9 + count( $extra_columns ) ) . '">No users match these filters.</td></tr>';
}
echo '</tbody>';
echo '</table>';
$directory->pagination();
echo '</div>';
echo '</div>';
