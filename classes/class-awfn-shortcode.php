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

		$atts['apts']              = esc_html( $atts['apts'] );
		$atts['hours']             = absint( $atts['hours'] ) <= 6 ? absint( $atts['hours'] ) : 1;
		$atts['show_metar']        = (bool) $atts['show_metar'];
		$atts['show_taf']          = (bool) $atts['show_taf'];
		$atts['show_pireps']       = (bool) $atts['show_pireps'];
		$atts['show_station_info'] = (bool) $atts['show_station_info'];
		$atts['radial_dist']       = absint( $atts['radial_dist'] ) > 10 && absint( $atts['radial_dist'] ) < 201 ? absint( $atts['radial_dist'] ) : 100;
		$atts['title']             = esc_html( $atts['title'] );

		$spinner_url     = plugin_dir_url( dirname( __FILE__ ) ) . 'css/loading.gif';
		$atts['spinner'] = $spinner_url;

		$out = "<section class='awfn-shortcode' data-atts='" . json_encode( $atts ) . "'><img class='sc-loading' src='{$spinner_url}'/></section>";

		return $out;

	}

	public static function ajax_weather_shortcode() {

		check_ajax_referer( 'shortcode-ajax', 'security' );

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

		$atts = wp_parse_args( $_POST['atts'], $defaults );

		$hours             = absint( $atts['hours'] ) <= 6 ? absint( $atts['hours'] ) : 1;
		$show_metar        = filter_var( $atts['show_metar'], FILTER_VALIDATE_BOOLEAN );
		$show_taf          = filter_var( $atts['show_taf'], FILTER_VALIDATE_BOOLEAN );
		$show_pireps       = filter_var( $atts['show_pireps'], FILTER_VALIDATE_BOOLEAN );
		$show_station_info = filter_var( $atts['show_station_info'], FILTER_VALIDATE_BOOLEAN );
		$distance          = absint( $atts['radial_dist'] );
		$title             = $atts['title'];

		// Calling this up here so we can set $icao and not have to call it again
		$station = new AwfnStation( $atts['apts'], $show_station_info );

		$icao = $station->station_exist() ? (string) $station->get_icao() : false;

		// The same shortcode can be used multiple times while being cached once based on its attributes
		$shortcode_id = SHORTCODE_SLUG . md5( serialize( $atts ) );

		// Check for cached output
		if ( $output = get_transient( $shortcode_id ) ) {
			Adds_Weather_Widget::log( 'info', 'Cached data found for shortcode id: ' . $shortcode_id );
			wp_send_json_success( $output );
		}

		Adds_Weather_Widget::log( 'info', 'No cached data found for shortcode id: ' . $shortcode_id );

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
			$pirep = new AwfnPirep( $station->get_icao(), $station->lat(), $station->lng(), $distance, $hours, $show_pireps );
			$pirep->go();

		} else {
			// We have no station data; say so.
			echo '<header>ICAO ' . esc_html( $atts['apts'] ) . ' not found<header>';
		}

		echo '</section>';

		$output = ob_get_clean();

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
		$output = wp_kses( $output, apply_filters( 'adds_kses', $kses_args ) );

		// Cache shortcode output
		set_transient( $shortcode_id, $output, EXPIRE_TIME );

		wp_send_json_success( $output );


	}

}
