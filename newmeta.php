<?php
/**
 * Plugin Name: New Plugin Meta
 * Plugin URI: https://21douze.fr/new-meta-des-infos-pour-vos-plugins
 * Description: Add useful information about plugins, contribution powered!
 * Author Name: Julio Potier
 * Author URI: https://secupress.pro
 * Version: 1.1
 * Licence: GPLv3
 * Domain: bawnm
 */

defined( 'ABSPATH' ) || die( 'Something went wrong.' );

define( 'BAWNM_ASSETS_URL', plugins_url( 'assets/', __FILE__ ) );
define( 'BAWNM_API_URL', 'https://21douze.fr/?bawnm_api' );

require __DIR__ . '/functions.php';

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bawnm_add_action_links' );
function bawnm_add_action_links( $links ) {
	 $links[] = '<a href="' . admin_url( 'options-general.php?page=new_meta' ) . '">' . __( 'Settings' ) . '</a>';
	return $links;
}

add_action( 'load-plugins.php', 'bawnm_add_css' );
add_action( 'load-plugin-install.php', 'bawnm_add_css' );
add_action( 'load-settings_page_new_meta', 'bawnm_add_css' );
function bawnm_add_css() {
	wp_enqueue_style( 'newmeta', BAWNM_ASSETS_URL . 'sprite.css' );
	wp_enqueue_style( 'candlestick', BAWNM_ASSETS_URL . 'candlestick.css' );
}

add_action( 'load-settings_page_new_meta', 'bawnm_add_js' );
function bawnm_add_js() {
	wp_enqueue_script( 'candlestick', BAWNM_ASSETS_URL . 'candlestick.js' );
	wp_enqueue_script( 'newmeta', BAWNM_ASSETS_URL . 'script.js' );
}

add_filter( 'manage_plugins_columns', 'bawnm_manage_plugins_columns' );
function bawnm_manage_plugins_columns( $columns ) {
	$columns['newmeta'] = 'New Meta';
	return $columns;
}

add_filter( 'plugin_install_action_links', 'juliotest', 10, 2 );
function juliotest( $action_links, $plugin ) {
	$options = get_option( 'bawnm_settings' );
	$list    = bawnm_get_icons();
	$data    = get_option( 'bawnm_data' );
	$values  = array_filter( (array) $options );
	$slug    = $plugin['slug'];
	$content = '';
	foreach ( $values as $key => $value ) {
		// var_dump($data[ $slug ]);
		if ( isset( $data[ $slug ] ) && is_array( $data[ $slug ] ) && ! empty( $data[ $slug ] ) ) {
			$content .= '<span class="bawnm_icon bawnm_color-' . $data[ $slug ][ $key ] . ' bawnm_icon-' . $key . '" title="' . sprintf( _x( '%1$s %2$s %3$s', '1: Does the plugin ; 2: does this or that ; 3 yes/no', 'bawnm' ), __( 'Does the plugin', 'bawnm' ), esc_attr( $list[ $key ] ), bawnm_get_label( $data[ $slug ][ $key ] ) ) . '"></span>';
		}
	}

	$action_links['newmeta'] = $content;
	return $action_links;
}

add_action( 'manage_plugins_custom_column' , 'bawnm_manage_plugins_custom_column', 10, 3 );
function bawnm_manage_plugins_custom_column( $column_name, $plugin_file, $plugin_data ) {
	if ( 'newmeta' !== $column_name ) {
		return;
	}
	$options = get_option( 'bawnm_settings' );
	$list    = bawnm_get_icons();
	$data    = get_option( 'bawnm_data' );
	$values  = array_filter( (array) $options );
	$url     = admin_url( 'options-general.php?page=new_meta&plugin=' . $plugin_file );
	$slug    = bawnm_get_slug( $plugin_file );
	foreach ( $values as $key => $value ) {
		if ( isset( $data[ $slug ] ) && is_array( $data[ $slug ] ) && ! empty( $data[ $slug ] ) ) {
			echo '<span class="bawnm_icon bawnm_color-' . $data[ $slug ][ $key ] . ' bawnm_icon-' . $key . '" title="' . sprintf( _x( '%1$s %2$s %3$s', '1: Does the plugin ; 2: does this or that ; 3 yes/no', 'bawnm' ), __( 'Does the plugin', 'bawnm' ), esc_attr( $list[ $key ] ), bawnm_get_label( $data[ $slug ][ $key ] ) ) . '"></span>';
		}
	}
	$class = isset( $data[ $slug ] ) && is_array( $data[ $slug ] ) && ! empty( $data[ $slug ] ) ? 'bawnm_hidden' : '';
	echo '<a href="' . esc_url( $url ) . '"><span class="bawnm_icon ' . $class . ' bawnm_icon-cog" title="' . esc_attr__( 'Contribute', 'bawnm' ) . '"></span></a>';
}

add_action( 'admin_menu', 'bawnm_add_admin_menu' );
function bawnm_add_admin_menu() {
	add_options_page( 'New Meta', 'New Meta', 'manage_options', 'new_meta', 'bawnm_options_page' );
}

add_action( 'admin_init', 'bawnm_settings_init' );
function bawnm_settings_init() {
	register_setting( 'bawnm', 'bawnm_settings', [ 'sanitize_callback' => 'bawnm_sanitize_checkboxes' ] );

	add_settings_section(
		'bawnm_bawnm_section',
		'',
		'bawnm_settings_section_callback',
		'bawnm'
	);

	$plugins = get_plugins();
	if ( ! isset( $_GET['plugin'] ) || ! array_key_exists( $_GET['plugin'], $plugins ) ) {
		add_settings_field(
			'bawnm_checkboxes',
			__( 'Does the plugin', 'bawnm' ) . '…',
			'bawnm_checkboxes_render',
			'bawnm',
			'bawnm_bawnm_section'
		);
	} else {
		$plugin = $plugins[ $_GET['plugin'] ];
		$plugin['slug'] = $_GET['plugin'];
		add_settings_field(
			'bawnm_checkboxes',
			__( 'Does the plugin', 'bawnm' ) . '…',
			'bawnm_radios_render',
			'bawnm',
			'bawnm_bawnm_section',
			$plugin
		);
	}

}

function bawnm_sanitize_checkboxes( $settings ) {
	if ( isset( $_POST['update'] ) ) {
		_bawnm_refresh_data();
	}
	$plugins = get_plugins();
	$list    = bawnm_get_icons();
	if ( isset( $settings['plugin_slug'] ) && array_key_exists( $settings['plugin_slug'], $plugins ) ) {
		$plugin_slug = $settings['plugin_slug'];
		$plugin_ver  = $settings['plugin_ver'];
		$settings    = array_intersect_key( $settings, $list );
		$data        = get_option( 'bawnm_data' );
		if ( ! is_array( $data ) ) {
			$data = [];
		}
		$data[ $plugin_slug ] = $settings;
		update_option( 'bawnm_data', $data, false );
		wp_remote_post( BAWNM_API_URL . '&newmeta',
			[
				'blocking' => false,
				'timeout'  => 0.1,
				'body'     =>
					[
						'uniqid' => bawnm_get_uniqid(),
						'ver' => $plugin_ver,
						$plugin_slug => $data[ $plugin_slug ],
					],
			] );
		wp_redirect( admin_url( 'plugins.php' ) );
		die();
	} else {
		return array_intersect_key( $settings, $list );
	}
}

function bawnm_checkboxes_render() {
	$options = get_option( 'bawnm_settings' );
	$list    = bawnm_get_icons();
	foreach ( $list as $key => $label ) {
		$value = isset( $options[ $key ] ) ? esc_attr( $options[ $key ] ) : '';
		$value = ! $value && ! isset( $_GET['plugin'] ) ? '0' : $value;
		?>
		<p><label><span class="bawnm_icon bawnm_color-1 bawnm_icon-<?php echo $key; ?>"></span> <input id="candlestick-<?php echo $key; ?>" class="candlestick" type="checkbox" name="bawnm_settings[<?php echo $key; ?>]" value="<?php echo $value; ?>">…<?php echo $label; ?></label></p>
		<?php
	}
}

function bawnm_radios_render( $plugin ) {
	$data     = get_option( 'bawnm_data' );
	if ( is_array( $data ) && isset( $data[ $plugin['slug'] ] ) ) {
		$data = $data[ $plugin['slug'] ];
	} else {
		$data = [];
	}
	$list = bawnm_get_icons();
	foreach ( $list as $key => $label ) {
		$value = isset( $data[ $key ] ) ? $data[ $key ] : '';
		?>
		<p><label><span class="bawnm_icon bawnm_color-1 bawnm_icon-<?php echo $key; ?>"></span> <input id="r-candlestick-<?php echo $key; ?>" class="candlestick" type="checkbox" name="bawnm_settings[<?php echo $key; ?>]" value="<?php echo esc_attr( $value ); ?>">…<?php echo $label; ?></label></p>
		<?php
	}
	$slug = bawnm_get_slug( $plugin['slug'] );
	?>
	<input type="hidden" name="bawnm_settings[plugin_slug]" value="<?php echo esc_attr( $slug ); ?>">
	<input type="hidden" name="bawnm_settings[plugin_ver]" value="<?php echo esc_attr( $plugin['Version'] ); ?>">
	<?php
}


function bawnm_settings_section_callback() {
	$plugins = get_plugins();
	$list    = bawnm_get_icons();
	if ( isset( $_GET['plugin'] ) && array_key_exists( $_GET['plugin'], $plugins ) ) {
		printf( __( 'Contribution for <strong>%s</strong>, thank you!', 'bawnm' ), wp_kses_post( $plugins[ $_GET['plugin'] ]['Name'] ) );
	} else {
		_e( 'What informations do you need to display?', 'bawnm' );
	}
}


function bawnm_options_page() {
	?>
	<form action='options.php' method='post'>
		<h2>New Meta</h2>
		<?php
		settings_fields( 'bawnm' );
		do_settings_sections( 'bawnm' );
		echo '<p class="legend">' . __( 'Legend: ', 'bawnm' ) .
			'<br><input type="checkbox" id="legend-yes" value="1" disabled="disabled" class="candlestick"> ' . __( 'Yes', 'bawnm' ) .
			'<br><input type="checkbox" id="legend-no"  value="0" disabled="disabled" class="candlestick"> ' . __( 'No', 'bawnm' );
		if ( ! isset( $_GET['plugin'] ) ) {
			echo '<p>';
			submit_button( __( 'Save Changes', 'bawnm' ), 'primary', 'submit', false );
			echo ' ';
			submit_button( __( 'Save Changes and update the data now', 'bawnm' ), 'secondary small', 'update', false );
			echo '</p>';
		} else {
			echo '<br><input type="checkbox" id="legend-idk" value=""  disabled="disabled" class="candlestick"> ' . __( 'I don’t know', 'bawnm' ) . '</p>';
			echo '<p class="description">⚠️ ' . sprintf( __( 'By sending your contribution, you will send it to %s but nothing private will be saved.', 'bawnm' ), '21douze.fr' ) . '</p>';
			submit_button( __( 'Send my contribution', 'bawnm' ) );
		}
		?>
	</form>
	<?php
}

register_activation_hook( __FILE__, 'bawnm_activation' );
function bawnm_activation() {
    if ( ! wp_next_scheduled ( 'bawnm_refresh_data' ) ) {
        wp_schedule_event( time(), 'twicedaily', 'bawnm_refresh_data' );
    }
}

add_action( 'bawnm_refresh_data', '_bawnm_refresh_data' );
function _bawnm_refresh_data() {
	if ( ! function_exists( 'get_plugins' ) ) {
		require( ABSPATH . '/wp-admin/includes/plugin.php' );
	}
	if ( ! function_exists( 'bawnm_get_uniqid' ) ) {
		bawnm_require_functions();
	}
	$wp_plugins = get_plugins();
	$plugins    = wp_list_pluck( $wp_plugins, 'Version' );
	$r = wp_remote_post( BAWNM_API_URL . '&getmeta',
		[
			'body' =>
				[
					'uniqid'  => bawnm_get_uniqid(),
					'plugins' => $plugins,
				]
		] );
	$data = @json_decode( wp_remote_retrieve_body( $r ), true );
	if ( isset( $data['success'], $data['data'] ) ) {
		update_option( 'bawnm_data', $data['data'], false );
	}
}

register_deactivation_hook( __FILE__, 'bawnm_deactivation' );
function bawnm_deactivation() {
    wp_clear_scheduled_hook( 'bawnm_refresh_data' );
}

register_uninstall_hook( __FILE__, 'bawnm_uninstall' );
function bawnm_uninstall() {
	delete_option( 'bawnm_settings' );
	delete_option( 'bawnm_data' );
}
