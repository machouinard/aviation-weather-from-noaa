<?php

/**
 * Class AWFN_Shortcode
 *
 * This class handles caching, escaping and display of shortcodes
 *
 * @package     Aviation Weather from NOAA
 * @subpackage  Shortcode
 * @since       0.4.0
 */
class AWFN_Shortcode {


	/**
	 * Shortcode Usage: ( shown with defaults )
	 * [adds_weather apts='KSMF' hours=2 show_taf=1 show_pireps=1 show_station_info=1 radial_dist=100 title='']
	 *
	 * @param  array $atts defaults
	 *
	 * @return string $data     Weather info to display
	 *
	 * @since 0.4.0
	 */
	public static function adds_weather_shortcode( $atts ) {

		$defaults = array(
			'apts'              => 'KSMF',
			'hours'             => '2',
			'show_metar'        => '1',
			'show_taf'          => '1',
			'show_pireps'       => '1',
			'show_station_info' => '1',
			'radial_dist'       => '100',
			'title'             => ''
		);

		$atts = wp_parse_args( $atts, $defaults );

		$hours             = absint( $atts['hours'] ) <= 6 ? absint( $atts['hours'] ) : 1;
		$show_metar        = (bool) $atts['show_metar'];
		$show_taf          = (bool) $atts['show_taf'];
		$show_pireps       = (bool) $atts['show_pireps'];
		$show_station_info = (bool) $atts['show_station_info'];
		$distance          = absint( $atts['radial_dist'] );
		$title             = $atts['title'];

		// Calling this up here so we can set $icao and not have to call it again
		$station = new AwfnStation( $atts['apts'], $show_station_info );
		$station->clean_icao();

		$icao = isset( $station->station ) ? (string) $station->station : false;

		// The same shortcode can be used multiple times while being cached once based on its attributes
		$shortcode_id = SHORTCODE_SLUG . md5( serialize( $atts ) );

		// Check for cached output
		$output       = get_transient( $shortcode_id );

		if ( ! $output ) {

			if ( empty ( $title ) ) {
				$title = sprintf( _n( 'Available data for %s from the past hour',
					'Available data for %s from the past %d hours', $hours, Adds_Weather_Widget::get_widget_slug() ), $icao, $hours );
			}

			// Start output buffering
			ob_start();

			echo '<section class="adds-weather-shortcode-wrapper">';

			// Do we have station data?
			if ( $station->station_exist() ) {

				echo '<header>' . esc_html( $title ) . '</header>';

				$station->decode_data();
				$station->build_display();
				$station->display_data();

				// Handle METAR
				$metar = new AwfnMetar( $icao, $hours, $show_metar );
				$metar->go();

				// Handle TAF
				$taf = new AwfnTaf( $icao, $hours, $show_taf );
				$taf->go();

				// Handle PIREPS
				$pirep = new AwfnPirep( $station->lat(), $station->lng(), $distance, $hours, $show_pireps );
				$pirep->go();

			} else {
				// We have no station data; say so.
				echo '<header>ICAO ' . esc_html( $atts['apts'] ) . ' not found<header>';
			}

			echo '</section>';

			$output = ob_get_clean();

			// Cache shortcode output
			set_transient( $shortcode_id, $output, EXPIRE_TIME );

		}

		// Allowed markup in shortcode. This is filterable.
		$kses_args = array(
			'p'       => array(
				'class' => array()
			),
			'span'    => array(
				'class' => array(),
				'style' => array()
			),
			'ul'      => array(),
			'li'      => array(),
			'h2'      => array(),
			'div'     => array(
				'id'    => array(),
				'class' => array()
			),
			'section' => array(
				'id'    => array(),
				'class' => array()
			),
			'article' => array(
				'id'    => array(),
				'class' => array()
			),
			'header'  => array(
				'id'    => array(),
				'class' => array(),
			),
			'table'   => array(
				'border'      => array(),
				'cellpadding' => array(),
				'cellspacing' => array()
			),
			'tr'      => array(),
			'td'      => array(
				'align' => array(),
				'style' => array()
			),
			'script'  => array()
		);

		// Run output through KSES
		return wp_kses( $output, apply_filters( 'adds_kses', $kses_args ) );
	}
}