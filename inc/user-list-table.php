<?php
/**
 * Renders the online users list table on the "Online Active Users" admin page.
 *
 * @package Online_Active_Users
 * @var WPOAU_User_Directory $wpoau_directory        User list filters and pagination state.
 * @var array                $wpoau_user_records     User activity records for the current page.
 * @var int                  $wpoau_online_user_count Current online user count.
 */

defined( 'ABSPATH' ) || exit;

global $wpoau_users;

$wpoau_location_enabled = WPOAU_Privacy::geolocation_enabled();
$wpoau_extra_columns    = apply_filters( 'wpoau_extra_columns', array() );
$wpoau_all_roles        = array_map(
	static function ( $role ) {
		return translate_user_role( $role['name'] );
	},
	wp_roles()->roles
);
$wpoau_all_countries    = array();

$wpoau_country_records = get_transient( 'users_status' );
$wpoau_country_records = is_array( $wpoau_country_records ) ? $wpoau_country_records : array();
foreach ( $wpoau_country_records as $wpoau_user_record ) {
	$wpoau_user_object = get_userdata( $wpoau_user_record['id'] );
	if ( ! empty( $wpoau_user_object->roles ) ) {
		foreach ( $wpoau_user_object->roles as $wpoau_role ) {
			$wpoau_all_roles[ $wpoau_role ] = ucfirst( $wpoau_role );
		}
	}
	if ( ! empty( $wpoau_user_record['country'] ) ) {
		$wpoau_all_countries[ $wpoau_user_record['country'] ] = $wpoau_user_record['country'];
	}
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only table filter; no state changes occur.
$wpoau_filter_role = isset( $_GET['filter_role'] ) && is_string( $_GET['filter_role'] ) ? sanitize_key( wp_unslash( $_GET['filter_role'] ) ) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only table filter; no state changes occur.
$wpoau_filter_country = $wpoau_location_enabled && isset( $_GET['filter_country'] ) && is_string( $_GET['filter_country'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_country'] ) ) : '';
echo '<div class="webi-table-container">';
echo '<div class="table-header-wrap">';
echo '<form method="get">
    <input type="hidden" name="page" value="wpoau-active-users" />
    <select name="filter_role">
        <option value="">' . esc_html__( 'All Roles', 'online-active-users' ) . '</option>';
foreach ( $wpoau_all_roles as $wpoau_user_role => $wpoau_label ) {
	$wpoau_selected = ( $wpoau_filter_role === $wpoau_user_role ) ? 'selected' : '';
	echo '<option value="' . esc_attr( $wpoau_user_role ) . '" ' . esc_attr( $wpoau_selected ) . '>' . esc_html( $wpoau_label ) . '</option>';
}
echo '</select>';
echo '<select name="filter_status"><option value="">All statuses</option><option value="online" ' . selected( $wpoau_directory->status, 'online', false ) . '>Online</option><option value="offline" ' . selected( $wpoau_directory->status, 'offline', false ) . '>Offline</option></select>';
echo '<select data-wpoau-geo ' . ( $wpoau_location_enabled ? '' : 'hidden disabled ' ) . 'name="filter_country">
        <option value="">' . esc_html__( 'All Countries', 'online-active-users' ) . '</option>';
foreach ( $wpoau_all_countries as $wpoau_country_name ) {
	$wpoau_selected      = ( $wpoau_filter_country === $wpoau_country_name ) ? 'selected' : '';
	$wpoau_country_label = ( 'Unknown' === $wpoau_country_name ) ? __( 'Unknown', 'online-active-users' ) : $wpoau_country_name;
	echo '<option value="' . esc_attr( $wpoau_country_name ) . '" ' . esc_attr( $wpoau_selected ) . '>' . esc_html( $wpoau_country_label ) . '</option>';
}
echo '</select>';
echo '<button class="button">' . esc_html__( 'Filter', 'online-active-users' ) . '</button>';
echo '</form>';
echo '<div class="top-total-user">' . esc_html__( 'Total Online Users:', 'online-active-users' ) . ' ' . absint( $wpoau_online_user_count ) . '</div>';
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
$wpoau_geo_visibility = $wpoau_location_enabled ? '' : ' hidden';
echo '<th data-wpoau-geo' . esc_attr( $wpoau_geo_visibility ) . '>' . esc_html__( 'Country', 'online-active-users' ) . '</th><th data-wpoau-geo' . esc_attr( $wpoau_geo_visibility ) . '>' . esc_html__( 'Timezone', 'online-active-users' ) . '</th>';

foreach ( $wpoau_extra_columns as $wpoau_label ) {
	echo '<th>' . esc_html( $wpoau_label ) . '</th>';
}
echo '<th>' . esc_html__( 'Status', 'online-active-users' ) . '</th></tr></thead>';
echo '<tbody>';
$wpoau_row_number = ( $wpoau_directory->page - 1 ) * 25 + 1;
foreach ( $wpoau_user_records as $wpoau_user_record ) {
	$wpoau_user_object   = get_userdata( $wpoau_user_record['id'] );
	$wpoau_avatar_markup = get_avatar( $wpoau_user_record['id'], 32, '', '', array( 'class' => 'webi-avatar' ) );
	$wpoau_first_name    = get_user_meta( $wpoau_user_object->ID, 'first_name', true );
	$wpoau_last_name     = get_user_meta( $wpoau_user_object->ID, 'last_name', true );
	$wpoau_full_name     = trim( $wpoau_first_name . ' ' . $wpoau_last_name );
	if ( ! $wpoau_full_name ) {
		$wpoau_full_name = $wpoau_user_object->display_name;
	}
	$wpoau_is_online = $wpoau_users->wpoau_is_user_online( $wpoau_user_record['id'] );
	$wpoau_last_seen = $wpoau_user_record['last'] ? wp_date( 'M j, Y @ g:ia', $wpoau_user_record['last'] ) : __( 'No activity recorded', 'online-active-users' );
	// 'Unknown' is the untranslated sentinel Wpoau_Active_Users::wpoau_get_user_ip()/_country()/_timezone() store and compare against; translate only for display below.
	$wpoau_ip           = isset( $wpoau_user_record['ip'] ) ? $wpoau_user_record['ip'] : 'Unknown';
	$wpoau_country      = isset( $wpoau_user_record['country'] ) ? $wpoau_user_record['country'] : 'Unknown';
	$wpoau_timezone     = isset( $wpoau_user_record['timezone'] ) ? $wpoau_user_record['timezone'] : 'Unknown';
	$wpoau_roles        = isset( $wpoau_user_object->roles ) ? implode( ', ', $wpoau_user_object->roles ) : '—';
	$wpoau_country_code = $wpoau_location_enabled && isset( $wpoau_user_record['country_code'] ) && is_string( $wpoau_user_record['country_code'] ) ? strtolower( $wpoau_user_record['country_code'] ) : 'xx';
	echo '<tr>';
	echo '<td>' . absint( $wpoau_row_number ) . '</td>';
	++$wpoau_row_number;
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() returns core-escaped markup.
	echo '<td>' . $wpoau_avatar_markup . '</td>';
	echo '<td>' . esc_html( $wpoau_user_object->user_login ) . '</td>';
	echo '<td>' . esc_html( $wpoau_full_name ) . '</td>';
	echo '<td>' . esc_html( ucfirst( $wpoau_roles ) ) . '</td>';
	echo '<td>' . esc_html( 'Unknown' === $wpoau_ip ? __( 'Unknown', 'online-active-users' ) : $wpoau_ip ) . '</td>';
	$wpoau_flag = WPOAU_Geolocation::flag( $wpoau_country_code );
	echo '<td data-wpoau-geo' . esc_attr( $wpoau_geo_visibility ) . '>' . wp_kses_post( $wpoau_flag ) . esc_html( 'Unknown' === $wpoau_country ? __( 'Unavailable', 'online-active-users' ) : $wpoau_country ) . '</td>';
	echo '<td data-wpoau-geo' . esc_attr( $wpoau_geo_visibility ) . '>' . esc_html( 'Unknown' === $wpoau_timezone ? __( 'Unavailable', 'online-active-users' ) : $wpoau_timezone ) . '</td>';
	do_action( 'wpoau_extra_column_cells', $wpoau_user_record );
	echo '<td class="status-cols-wrap">';
	$wpoau_activity_state = WPOAU_User_Directory::activity_state( $wpoau_is_online, $wpoau_user_record['last'] );
	$wpoau_status_labels  = array(
		'online'     => __( 'Online Now', 'online-active-users' ),
		'offline'    => __( 'Offline', 'online-active-users' ),
		'inactive'   => __( 'Inactive (30+ days)', 'online-active-users' ),
		'unrecorded' => __( 'No activity recorded', 'online-active-users' ),
	);
	echo '<span class="wpoau-presence wpoau-presence--' . esc_attr( $wpoau_activity_state ) . '"><span class="wpoau-presence-dot" aria-hidden="true"></span><br><strong>' . esc_html( $wpoau_status_labels[ $wpoau_activity_state ] ) . '</strong></span>';
	if ( ! $wpoau_is_online && $wpoau_user_record['last'] ) {
		echo '<br><small>' . esc_html__( 'Last seen:', 'online-active-users' ) . ' ' . esc_html( $wpoau_last_seen ) . '</small>';
	}

	echo '</td>';
	echo '</tr>';
}
if ( ! $wpoau_user_records ) {
	echo '<tr><td colspan="' . absint( 9 + count( $wpoau_extra_columns ) ) . '">No users match these filters.</td></tr>';
}
echo '</tbody>';
echo '</table>';
$wpoau_directory->pagination();
echo '</div>';
echo '</div>';
