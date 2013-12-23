<?php
/**
 * Plugin Name: Aviation Weather from NOAA
 * Plugin URI:  http://plugins.machouinard.com/adds
 * Description: Aviation weather data from NOAA's Aviation Digital Data Service (ADDS)
 * Version:     0.2.6
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
define( 'MACHOUINARD_ADDS_VERSION', '0.2.5' );
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
//
// add_action( 'init', 'machouinard_adds_init' );
add_action( 'widgets_init', 'machouinard_adds_register_widget' );
add_shortcode( 'adds_weather', 'machouinard_adds_weather_shortcode' );
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

/**
 * Setup the settings page - For future use
 */

function machouinard_adds_settings_page() {

}

function machouinard_adds_register_widget() {
	register_widget( 'machouinard_adds_weather_widget' );
}


/**
 * [machouinard_adds_weather_shortcode description]
 * @param  array $atts defaults
 * @return string       Weather info to display
 */
function machouinard_adds_weather_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'apts'        => 'KORD',
		'hours'       => '2',
		'show_taf'    => '1',
		'show_pireps' => '1',
		'radial_dist' => '30',
		'title'       => null
		), $atts, 'adds_weather' ) );

	$icao        = machouinard_adds_weather_widget::clean_icao( $apts );
	$hours       = intval( $hours );
	$show_taf    = intval( $show_taf );
	$show_pireps = intval( $show_pireps );
	$radial_dist = intval( $radial_dist );
	$title       = sanitize_text_field( $title );

	$data        = '';

	if( $title == null ) {
		$title = sprintf( _n('Available data for %s from the past hour', 'Available data for %s from the past %d hours', $hours, 'machouinard_adds' ), $icao, $hours );
	}

	$wx    = machouinard_adds_weather_widget::get_metar( $icao, $hours );
	$icaos = preg_split( '~,\s~', $icao );

	foreach( $icaos as $apt ) {
		$pireps[] = machouinard_adds_weather_widget::get_pireps( $apt, $radial_dist );
	}

	if( ! empty( $wx['metar'] ) ) {
		$data .= '<p><strong>';
		$data .= $title;
		$data .= "</strong></p>";
		foreach( $wx as $type=>$info ) {

			if( $type == 'taf' && $show_taf || $type == 'metar' ) {
				$data .= '<strong>' . strtoupper( $type) . "</strong><br />";
			}

			if( $type == "taf" && !$show_taf ) continue;

			if( is_array( $info ) ) {
				foreach ( $info as $value ) {
					if( ! empty( $value ) ) {
						$data .=  $value . "<br />\n";
					}
				}
			} else {
				$data .= $info . "<br />\n";
			}
		}
	}

	if( ! empty( $pireps[0] ) && $show_pireps ) {
		$data .= '<strong>PIREPS ' . $radial_dist . 'sm</strong><br />';
		foreach( $pireps[0] as $pirep ) {
			$data .= $pirep . '<br />';
		}
	}

	return $data;
}

/**
 *
 */
class machouinard_adds_weather_widget extends WP_Widget {

	function machouinard_adds_weather_widget() {
		$machouinard_options = array(
			'classname'   => 'machouinard_adds_widget_class',
			'description' => __( 'Displays METAR & other info from NOAA\'s Aviation Digital Data Service', 'machouinard_adds' )
			);
		$this->WP_Widget( 'machouinard_adds_weather_widget', 'ADDS Weather Info', $machouinard_options );
	}

	function form( $instance ) {
		$defaults    = array( 'icao' => 'KZZV', 'hours' => 2, 'show_taf' => true, 'show_pireps' => true, 'radial_dist' => '30', 'title' => null );
		$instance    = wp_parse_args(  (array) $instance, $defaults );
		$icao        = $instance['icao'];
		$hours       = $instance['hours'];
		$show_taf    = $instance['show_taf'];
		$show_pireps = $instance['show_pireps'];
		$radial_dist = $instance['radial_dist'];
		$title       = $instance['title'];
		?>
		<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e('Title', 'machouinard_adds' ); ?></label>
		<input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

		<label for="<?php echo $this->get_field_name( 'icao' ); ?>"><?php _e('ICAO', 'machouinard_adds' ); ?></label>
		<input class="widefat" name="<?php echo $this->get_field_name( 'icao' ); ?>" type="text" value="<?php echo esc_attr( $icao ); ?>" />
		<label for="<?php echo $this->get_field_name( 'hours' ); ?>">Hours before now</label>
		<select name="<?php echo $this->get_field_name( 'hours' ); ?>" id="<?php echo $this->get_field_id('hours' ); ?>" class="widefat">

			<?php
			for( $x = 1; $x < 7; $x++) {
				echo '<option value="' . $x . '" id="' . $x . '"', $hours == $x ? ' selected="selected"' : '', '>', $x, '</option>';
			}
			?>
		</select>

		<label for="<?php echo $this->get_field_id( 'show_pireps' ); ?>"><?php _e('Display PIREPS?', 'machouinard_adds' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'show_pireps' ); ?>" name="<?php echo $this->get_field_name( 'show_pireps' ); ?>" type="checkbox" value="1" <?php checked( true, $show_pireps ); ?> class="checkbox"  />
		<label for="<?php echo $this->get_field_id( 'show_taf' ); ?>"><?php _e('Display TAF?', 'machouinard_adds' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'show_taf' ); ?>" name="<?php echo $this->get_field_name( 'show_taf' ); ?>" type="checkbox" value="1" <?php checked( true, $show_taf ); ?> class="checkbox"  /><br />
		<label for="<?php echo $this->get_field_name( 'radial_dist' ); ?>">Radial Distance</label>
		<select name="<?php echo $this->get_field_name( 'radial_dist' ); ?>" id="<?php echo $this->get_field_id('radial_dist' ); ?>" class="widefat">
			<?php
			for( $x = 10; $x < 210; $x += 10) {
				echo '<option value="' . $x . '" id="' . $x . '"', $radial_dist == $x ? ' selected="selected"' : '', '>', $x, '</option>';
			}
			?>
		</select>
		<?php
	}

	function update ( $new_instance, $old_instance ) {
		$instance                = $old_instance;
		$instance['icao']        = $this->clean_icao( $new_instance['icao'] );
		$instance['hours']       = intval( $new_instance['hours'] );
		$instance['show_taf']    = intval( $new_instance['show_taf'] );
		$instance['show_pireps'] = intval( $new_instance['show_pireps' ] );
		$instance['radial_dist'] = intval( $new_instance['radial_dist'] );
		$instance['title']       = sanitize_text_field( $new_instance['title'] );
		if( !$this->get_apt_info( $instance['icao'] ) ) {
			$instance['icao'] = 'Please enter valid ICAO';
		}
		// Delete transient data
		delete_transient( 'noaa_wx' );
		delete_transient( 'noaa_pireps' );
		return $instance;
	}
	/**
	 * [clean_icao description]
	 * @param  string $icao string of airport identifiers
	 * @return string       clean string of airport identifiers
	 */
	static function clean_icao( $icao ) {
		$ptrn        = '~[-\s,.;:\/+]+~';
		$icao        = strtoupper(sanitize_text_field( $icao ) );
		$icao_arr    = preg_split( $ptrn, $icao);
		$icao_arr    = array_splice( $icao_arr, 0, 1); // Initially I was including up to 4, but I guess if more are needed, just use more widgets.
		$icao_string = implode(', ', $icao_arr);
		return $icao_string;
	}

	function widget ( $args, $instance ) {
		$icao        = empty( $instance[ 'icao' ] ) ? '' : strtoupper( $instance[ 'icao' ] );
		$hours       = empty( $instance[ 'hours' ] ) ? '' : $instance[ 'hours' ];
		$radial_dist = empty( $instance[ 'radial_dist' ] ) ? '' : $instance[ 'radial_dist' ];
		$show_taf    = isset( $instance[ 'show_taf' ] ) ? $instance[ 'show_taf' ] : false;
		$show_pireps = isset( $instance[ 'show_pireps' ] ) ? $instance[ 'show_pireps' ] : false;
		$title       = empty( $instance[ 'title' ] ) ? sprintf( _n('Available data for %s from the past hour', 'Available data for %s from the past %d hours', $hours, 'machouinard_adds' ), $icao, $hours ) : $instance[ 'title' ];
		$hours       = apply_filters( 'hours_before_now', $hours );
		$radial_dist = apply_filters( 'radial_dist', $radial_dist );
		$title       = apply_filters( 'machouinard_title' , $title );

		$wx          = $this->get_metar( $icao, $hours );
		$icaos       = preg_split( '~,\s~', $icao );

		foreach( $icaos as $apt ) {
			$pireps[] = $this->get_pireps( $apt, $radial_dist );
		}
		extract( $args );
		echo $before_widget;

		if( ! empty( $wx[ 'metar' ] ) ) {
			echo '<p><strong>';
			echo $title;
			echo "</strong></p>";
			foreach( $wx as $type=>$info ) {

				if( $type == 'taf' && $show_taf || $type == 'metar' ) {
					echo '<strong>' . strtoupper( $type ) . "</strong><br />";
				}

				if( $type == "taf" && !$show_taf ) continue;
				if( is_array( $info ) ) {
					foreach ( $info as $value ) {
						if( !empty( $value ) ) {
							echo  $value . "<br />\n";
						}
					}
				} else {
					echo $info . "<br />\n";
				}
			}
		}
		if( ! empty( $pireps[0] ) && $show_pireps ) {
			echo '<strong>PIREPS ' . $radial_dist . 'sm</strong><br />';
			foreach( $pireps[0] as $pirep ) {
				echo $pirep . '<br />';
			}
		}
		echo $after_widget;
	}
	/**
	 * [get_metar description]
	 * @param  string $icao  string of airport identifieers
	 * @param  int $hours number of hours history to include
	 * @return array        metar and taf arrays containing weather data
	 */
	static function get_metar( $icao, $hours ) {

		if( !get_transient( 'noaa_wx_' . $icao ) ) {
			// echo 'no transient';die( 'line 315' );
			$metar_url    = "http://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars&requestType=retrieve&format=xml&stationString={$icao}&hoursBeforeNow={$hours}";
			$tafs_url     = "http://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=tafs&requestType=retrieve&format=xml&stationString={$icao}&hoursBeforeNow={$hours}";
			$xml['metar'] = simplexml_load_file( $metar_url );
			$xml['taf']   = simplexml_load_file( $tafs_url );

			// Store the METAR for display
			$count = count( $xml['metar']->data->METAR );
			for( $i = 0; $i < $count; $i++) {
				$wx['metar'][ $i ] = (string)$xml['metar']->data->METAR[ $i ]->raw_text;
			}
			// Only store the most recent forecast
			$wx['taf'][ 0 ] = (string)$xml['taf']->data->TAF[ 0 ]->raw_text;
			// save wx data for 15 minutes
			set_transient( 'noaa_wx_' . $icao, $wx, 60 * 15 );
		}

		$wx = get_transient( 'noaa_wx_' . $icao );
		return $wx;
	}
	/**
	 * [get_pireps description]
	 * @param  string $icao        Airport Identifier
	 * @param  int $radial_dist    include pireps this distance from airport
	 * @return array               pirep data
	 */
	static function get_pireps( $icao, $radial_dist ) {
		if( !get_transient( 'noaa_pireps_' . $icao ) ) {
			$info      = self::get_apt_info( $icao );
			$pirep_url = "http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=aircraftreports&requestType=retrieve&format=xml&radialDistance={$radial_dist};{$info['lon']},{$info['lat']}&hoursBeforeNow=3";
			$xml       =  simplexml_load_file( $pirep_url );
			$pireps = array();
			for( $i = 0; $i < count( $xml->data->AircraftReport ); $i++ ) {
				$pireps[] = (string)$xml->data->AircraftReport[$i]->raw_text;
			}
			// save pirep data for 15 minutes
			set_transient( 'noaa_pireps_' . $icao, $pireps, 60 * 15 );
		}

		$pireps = get_transient( 'noaa_pireps_' . $icao );
		return $pireps;
	}
	/**
	 * [get_apt_info description]
	 * @param  string $icao Airport Identifier
	 * @return array       array containing lat & lon for provided airport
	 */
	public static function get_apt_info( $icao ) {
		$url                = "http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=stations&requestType=retrieve&format=xml&stationString={$icao}";
		$xml                = simplexml_load_file( $url );
		if( isset( $xml->data->Station ) ) {
			$info['station_id'] = $xml->data->Station->station_id;
			$info['lat']        = $xml->data->Station->latitude;
			$info['lon']        = $xml->data->Station->longitude;
		} else {
			$info = false;
		}

		return $info;
	}

}


