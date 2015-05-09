<?php
/*
Plugin Name: Aviation Weather from NOAA
Plugin URI:  https://github.com/machouinard/aviation-weather-from-noaa
Description: Aviation weather data from NOAA's Aviation Digital Data Service (ADDS)
Version:     0.3.7
Author:      Mark Chouinard
Author URI:  http://machouinard.com
License:     GPLv2+
Text Domain: machouinard_adds
Domain Path: /languages
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
define( 'MACHOUINARD_ADDS_VERSION', '0.3.7' );
define( 'MACHOUINARD_ADDS_URL', plugin_dir_url( __FILE__ ) );
define( 'MACHOUINARD_ADDS_PATH', dirname( __FILE__ ) . '/' );

require_once MACHOUINARD_ADDS_PATH . 'class-weather-widget.php';

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function machouinard_adds_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'machouinard_adds' );
	load_textdomain( 'machouinard_adds', WP_LANG_DIR . '/machouinard_adds/machouinard_adds-' . $locale . '.mo' );
	load_plugin_textdomain( 'machouinard_adds', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
add_action( 'init', 'machouinard_adds_init' );
add_action( 'widgets_init', 'machouinard_adds_register_widget' );
add_action( 'wp_enqueue_scripts', 'machouinard_adds_scripts' );
add_shortcode( 'adds_weather', 'machouinard_adds_weather_shortcode' );
// Wireup filters

/**
 * Enqueue stylesheet, allowing for override by theme
 */
function machouinard_adds_scripts() {
	// Check child theme
	$file = 'css/aviation_weather_from_noaa.css';
	if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
		$location = trailingslashit( get_stylesheet_directory_uri() ) . $file;
		$handle   = 'adds-override-child-css';

		// Check parent theme
	} elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
		$location = trailingslashit( get_template_directory_uri() ) . $file;
		$handle   = 'adds-override-parent-css';

		// use our style
	} else {
		$location = plugins_url( "/assets/css/aviation_weather_from_noaa.css", __FILE__ );
		$handle   = 'machouinard_adds_style';
	}
	wp_enqueue_style( $handle, apply_filters( 'adds_custom_css', $location ) );
}

function machouinard_adds_register_widget() {
	register_widget( 'machouinard_adds_weather_widget' );
}


/**
 * Shortcode Usage: ( shown with defaults )
 * [adds_weather apts='KSMF' hours=2 show_taf=1 show_pireps=1 radial_dist=30 title='']
 *
 * @param  array $atts defaults
 *
 * @return string $data     Weather info to display
 */
function machouinard_adds_weather_shortcode( $atts ) {

	$defaults = array(
		'apts'        => 'KSMF',
		'hours'       => '2',
		'show_taf'    => '1',
		'show_pireps' => '1',
		'radial_dist' => '30',
		'title'       => ''
	);

	$atts = wp_parse_args( $atts, $defaults );

	$icao        = machouinard_adds_weather_widget::clean_icao( $atts['apts'] );
	$hours       = absint( $atts['hours'] ) <= 6 ? absint( $atts['hours'] ) : 6;
	$show_taf    = (bool) $atts['show_taf'];
	$show_pireps = (bool) $atts['show_pireps'];
	$radial_dist = absint( $atts['radial_dist'] );
	$title       = $atts['title'];

	$data = '';

	if ( empty ( $title ) ) {
		$title = sprintf( _n( 'Available data for %s from the past hour',
			'Available data for %s from the past %d hours', $hours, 'machouinard_adds' ), $icao, $hours );
	}

	$wx = machouinard_adds_weather_widget::get_metar( $icao, $hours );

	$pireps[] = machouinard_adds_weather_widget::get_pireps( $icao, $radial_dist, $hours );

	$title_wrap = apply_filters( 'adds_shortcode_title_wrap', 'h2' );

	if ( ! empty( $wx['metar'] ) ) {
		$data .= '<div class="' . apply_filters( 'adds_shortcode_wrapper',
				'adds-weather-wrapper' ) . '"><' . $title_wrap . '>';
		$data .= sanitize_text_field( $title );
		$data .= "</{$title_wrap}>";
		foreach ( $wx as $type => $info ) {

			if ( ( $type == 'taf' && $show_taf ) || $type == 'metar' ) {
				$data .= '<p class="adds-heading">' . $type . '</p>';
			}

			if ( $type == 'taf' && ! $show_taf ) {
				continue;
			}

			if ( is_array( $info ) ) {
				$data .= '<ul>';
				foreach ( $info as $value ) {
					if ( ! empty( $value ) ) {
						$data .= '<li>' . esc_html( $value ) . "</li>";
					}
				}
				$data .= '</ul>';
			} else {
				$data .= $info . "<br />\n";
			}
		}
	}

	if ( ! empty( $pireps[0] ) && $show_pireps ) {
		$data .= '<p class="adds-heading">pireps ' . $radial_dist . '<span class="adds-sm">sm</span></p><ul>';
		foreach ( $pireps[0] as $pirep ) {
			$data .= '<li>' . esc_html( $pirep ) . '</li>';
		}
		$data .= '</ul></div>';
	}

	$args = array(
		'p'    => array(
			'class' => array()
		),
		'span' => array(
			'class' => array()
		),
		'ul'   => array(),
		'li'   => array(),
		'h2'   => array(),
		'div'  => array(
			'id'    => array(),
			'class' => array()
		),
	);

	return wp_kses( $data, apply_filters( 'adds_kses', $args ) );
}




