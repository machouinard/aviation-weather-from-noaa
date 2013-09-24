<?php
/**
 * Plugin Name: Aviation Weather from NOAA
 * Plugin URI:  http://plugins.machouinard.com/adds
 * Description: Aviation weather data from NOAA's Aviation Digital Data Service (ADDS)
 * Version:     0.2.3
 * Author:      Mark Chouinard
 * Author URI:  http://machouinard.com
 * License:     GPLv2+
 * Text Domain: machouinard_adds
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2013 Mark Chouinard (email : mark@chouinard.me)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'MACHOUINARD_ADDS_VERSION', '0.2.3' );
define( 'MACHOUINARD_ADDS_URL',     plugin_dir_url( __FILE__ ) );
define( 'MACHOUINARD_ADDS_PATH',    dirname( __FILE__ ) . '/' );

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function machouinard_adds_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'machouinard_adds' );
	load_textdomain( 'machouinard_adds', WP_LANG_DIR . '/machouinard_adds/machouinard_adds-' . $locale . '.mo' );
	load_plugin_textdomain( 'machouinard_adds', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	// add_action( 'admin_menu', 'machouinard_adds_admin_settings' );
}

/**
 * Activate the plugin
 */
function machouinard_adds_activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	machouinard_adds_init();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'machouinard_adds_activate' );

/**
 * Deactivate the plugin
 * Uninstall routines should be in uninstall.php
 */
function machouinard_adds_deactivate() {

}
register_deactivation_hook( __FILE__, 'machouinard_adds_deactivate' );

// Wireup actions
// For now, only using widget and shortcode
// add_action( 'init', 'machouinard_adds_init' );
add_action( 'widgets_init', 'machouinard_adds_register_widget' );
add_shortcode( 'adds_weather', 'machouinard_adds_weather_short' );
// Wireup filters


// Setup the settings menu option - For future use
function machouinard_adds_admin_settings() {
	add_options_page(
		__( 'ADDS Weather Settings', 'machouinard_adds' ),
		__( 'ADDS Weather Settings', 'machouinard_adds' ),
		'manage_options',
		'machouinard_adds_settings',
		'machouinard_adds_settings_page'
		);
}

// Setup the settings page - For future use
function machouinard_adds_settings_page() {

}

function machouinard_adds_register_widget() {
	register_widget( 'machouinard_adds_weather_widget' );
}

// Wireup shortcodes
// 
function machouinard_adds_weather_short( $atts ) {
	extract( shortcode_atts( array(
		'apts' => 'KORD',
		'hours' => '3',
		'show_taf' => '1'               
		), $atts ) );

	$icao = machouinard_adds_weather_widget::clean_icao( $apts );
	$hours = intval( $hours );
	$show_taf = intval( $show_taf );
	$data = '';

	$wx = machouinard_adds_weather_widget::get_metar( $icao, $hours );
	arsort( $wx);

	if( !empty( $wx[ 'metar' ] ) ) {
		$data .= '<p><strong>';
		$data .= sprintf( _n('All available data for %s in the past hour', 'All available data for %s in the past %d hours', $hours, 'machouinard_adds' ), $icao, $hours );
		$data .= "</strong></p>";
		foreach( $wx as $type=>$info ) {

			if( $type == 'taf' && $show_taf || $type == 'metar' ) {
				$data .= '<strong>' . strtoupper( $type) . "</strong><br />";
			}

			if( $type == "taf" && !$show_taf ) continue;
			if( is_array( $info ) ) {
				foreach ( $info as $key => $value ) {
					if( !empty( $value ) ) {
						$data .=  $value . "<br />\n";
					}
				}
			} else {
				$data .= $info . "<br />\n";
			}
		}
	}

	return $data;
}

class machouinard_adds_weather_widget extends WP_Widget {

	function machouinard_adds_weather_widget() {
		$machouinard_options = array(
			'classname' => 'machouinard_adds_widget_class',
			'description' => __( 'Displays METAR & other info from NOAA\'s Aviation Digital Data Service', 'machouinard_adds' )
			);
		$this->WP_Widget( 'machouinard_adds_weather_widget', 'ADDS Weather Info', $machouinard_options );
	}

	function form( $instance ) {
		// displays the widget form in the admin dashboard
		$defaults = array( 'icao' => 'KZZV', 'hours' => 2, 'show_taf' => true );
		$instance = wp_parse_args(  (array) $instance, $defaults );
		$icao = $instance[ 'icao' ];
		$hours = $instance[ 'hours' ];
		$show_taf = $instance[ 'show_taf' ];
		?>
		<label for="<?php echo $this->get_field_name( 'icao' ); ?>"><?php _e('ICAO (max 4)', 'machouinard_adds' ); ?></label>
		<input class="widefat" name="<?php echo $this->get_field_name( 'icao' ); ?>" type="text" value="<?php echo esc_attr( $icao ); ?>" />
		<label for="<?php echo $this->get_field_name( 'hours' ); ?>">Hours before now</label>
		<select name="<?php echo $this->get_field_name( 'hours' ); ?>" id="<?php echo $this->get_field_id('hours' ); ?>" class="widefat">

			<?php
			for( $x = 1; $x < 7; $x++) {
				echo '<option value="' . $x . '" id="' . $x . '"', $hours == $x ? ' selected="selected"' : '', '>', $x, '</option>';
			}
			?>
		</select>
		<label for="<?php echo $this->get_field_id( 'show_taf' ); ?>"><?php _e('Display TAF?', 'machouinard_adds' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'show_taf' ); ?>" name="<?php echo $this->get_field_name( 'show_taf' ); ?>" type="checkbox" value="1" <?php checked( true, $show_taf ); ?> class="checkbox"  />

		<?php
	}

	function update ( $new_instance, $old_instance ) {
		// process widget options to save
		$instance = $old_instance;
		$instance[ 'icao' ] = $this->clean_icao( $new_instance[ 'icao' ] );
		$instance[ 'hours' ] = strip_tags( $new_instance[ 'hours' ] );
		$instance[ 'show_taf' ] = strip_tags( $new_instance[ 'show_taf' ] );
		return $instance;
	}

	static function clean_icao( $icao ) {
		$ptrn = '~[-\s,.;:\/+]+~';
		$icao = strtoupper(sanitize_text_field( $icao ) );
		$icao_arr = preg_split( $ptrn, $icao);
		$icao_arr = array_splice( $icao_arr, 0, 4);
		$icao_string = implode(', ', $icao_arr);
		return $icao_string;
	}

	function widget ( $args, $instance ) {
		$icao = empty( $instance[ 'icao' ] ) ? '' : strtoupper( $instance[ 'icao' ] );
		$hours = empty( $instance[ 'hours' ] ) ? '' : $instance[ 'hours' ];
		$show_taf = isset( $instance[ 'show_taf' ] ) ? $instance[ 'show_taf' ] : false;

		$wx = $this->get_metar( $icao, $hours );
		arsort( $wx );
		extract( $args );
		echo $before_widget;

		if( !empty( $wx[ 'metar' ] ) ) {
			echo '<p><strong>';
			printf( _n('All available data for %s in the past hour', 'All available data for %s in the past %d hours', $hours, 'machouinard_adds' ), $icao, $hours );
			echo "</strong></p>";
			foreach( $wx as $type=>$info ) {

				if( $type == 'taf' && $show_taf || $type == 'metar' ) {
					echo '<strong>' . strtoupper( $type ) . "</strong><br />";
				}
				
				if( $type == "taf" && !$show_taf ) continue;
				if( is_array( $info ) ) {
					foreach ( $info as $key => $value ) {
						if( !empty( $value ) ) {
							echo  $value . "<br />\n";
						}
					}
				} else {
					echo $info . "<br />\n";
				}
			}
		}
		echo $after_widget;
	}

	static function get_metar( $icao, $hours ) {
		$metar_url = "http://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars&requestType=retrieve&format=xml&stationString={$icao}&hoursBeforeNow={$hours}";
		$tafs_url = "http://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=tafs&requestType=retrieve&format=xml&stationString={$icao}&hoursBeforeNow={$hours}";
		$xml[ 'metar' ] = simplexml_load_file( $metar_url );
		$xml[ 'taf' ] = simplexml_load_file( $tafs_url );
		for( $i = 1; $i <= count( $xml[ 'taf' ] ); $i++) {
			$wx[ 'taf' ][$i] = $xml[ 'taf' ]->data->TAF[$i]->raw_text;
		}

		for( $i = 1; $i <= count( $xml[ 'metar' ] ); $i++) {
			$wx[ 'metar' ][$i] = $xml[ 'metar' ]->data->METAR[$i]->raw_text;
		}
		
		return $wx;
	}

	public static function get_apt_info( $icao ) {
		$url = "http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=stations&requestType=retrieve&format=xml&stationString={$icao}";
		$xml = simplexml_load_file( $url );
		$info[ 'station_id' ] = $xml->data->Station->station_id;
		$info[ 'lat' ] = $xml->data->Station->latitude;
		$info[ 'lon' ] = $xml->data->Station->longitude;

		return $info;
	}

}

