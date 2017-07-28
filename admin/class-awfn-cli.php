<?php

/**
 * A Class to verify and/or troubleshoot AWFN plugin functionality.  It returns the same formatted results that are displayed using the widget and shortcode; HTML tags and all.  To tell you the truth, I hadn't worked with WP-CLI in a couple years and wanted a basic refresher.
 */
class AwfnCli extends WP_CLI_Command {

	/**
	 * Returns airport info based on an ICAO code
	 *
	 * ## OPTIONS
	 *
	 * <icao>
	 * : An airport ICAO code
	 *
	 * ## EXAMPLES
	 * wp awfn info kzzv
	 *
	 * @subcommand info
	 * @alias info
	 *
	 */
	public function airport_info( $args ) {
		list( $icao ) = $args;

		$station = new AwfnStation( $icao );
		$station->go( false );
		if ( ! $station->station_exist() ) {
			WP_CLI::error( 'Invalid ICAO' );
			return;
		}
		$this->lat = $station->lat();
		$this->lng = $station->lng();

		$output = $station->build_display();

		if ( $output ) {
			WP_CLI::success( print_r( $output, true ) );
		} else {
			WP_CLI::error( 'No response. Is ICAO legit?' );
		}

	}

	/**
	 * Get METAR for given ICAO.  This returns the latest METAR data.  The default time is 1 hour before now.
	 * If no METAR is being returned, try increasing the hours argument.
	 *
	 * ## OPTIONS
	 *
	 * <icao>
	 * : Airport ICAO code
	 *
	 * [<hours>]
	 * : Hours before now
	 *
	 * ## EXAMPLES
	 * wp awfn metar kzzv 4
	 * wp awfn metar ksmf
	 *
	 * @subcommand metar
	 * @alias metar
	 *
	 */
	public function get_metar( $args ) {

		list( $icao, $hours ) = array_pad( $args, 2, null );
		$hours = null === $hours ? 2 : (int) $hours;
		$show = false;

		$metar = new AwfnMetar( $icao, $hours, $show );
		$metar->go( false );

		$output = $metar->display_data();

		if ( $output ) {
			WP_CLI::success( $output );
		} else {
			WP_CLI::error( 'There was no response.  Try increasing hours or verifying ICAO ( wp awfn info ' . $icao . ' ).' );
		}

	}

	/**
	 * Get TAF for given ICAO.  The default time is 1 hour before now.
	 * If no TAF is being returned, try increasing the hours argument.
	 *
	 * ## OPTIONS
	 *
	 * <icao>
	 * : Airport ICAO code
	 *
	 * [<hours>]
	 * : Hours before now
	 *
	 * ## EXAMPLES
	 * wp awfn taf kzzv 4
	 * wp awfn taf ksmf
	 *
	 * @subcommand taf
	 * @alias taf
	 *
	 */
	public function get_taf( $args ) {

		list( $icao, $hours ) = array_pad( $args, 2, null );
		$hours = null === $hours ? 2 : (int) $hours;
		$show = false;

		$taf = new AwfnTaf( $icao, $hours, $show );
		$taf->go( false );

		$output = $taf->display_data();

		if ( $output ) {
			WP_CLI::success( $output );
		} else {
			WP_CLI::error( 'Nothing was returned.  Try increasing hours or verifying ICAO ( wp awfn info ' . $icao . ' ).' );
		}

	}

	/**
	 * Get PIREPS for given ICAO.  The default distance is 100nm and default time is 1 hour.
	 * If no PIREPS are being returned, try increasing those arguments. Keep in mind, PIREPS are not guaranteed to be available.
	 *
	 * ## OPTIONS
	 *
	 * <icao>
	 * : Airport ICAO code
	 *
	 * [<distance>]
	 * : Radial distance from airport
	 *
	 * [<hours>]
	 * : Hours before now
	 *
	 * ## EXAMPLES
	 * wp awfn pirep kzzv 100 2
	 * wp awfn pirep ksmf 400
	 * wp awfn pirep kdtw
	 *
	 * @subcommand pirep
	 * @alias pirep
	 *
	 */
	public function get_pireps( $args ) {

		list( $icao, $distance, $hours ) = array_pad( $args, 3, null );
		$distance = ( null === $distance ) ? 100 : (int) $distance;
		$hours = null === $hours ? 2 : (int) $hours;
		$show = false;

		$airport = new AwfnStation( $icao );
		$airport->go( false );
		$lat    = $airport->lat();
		$lng    = $airport->lng();
		$pireps = new AwfnPirep( $icao, $lat, $lng, $distance, $hours, $show );
		$pireps->go( false );

		$output = $pireps->display_data();
		if ( $output ) {
			WP_CLI::success( $output );
		} else {
			WP_CLI::error( 'There was no output.  Try increasing distance/time or verifying ICAO ( wp awfn info ' . $icao . ' ).' );
		}

	}

	/**
	 * Displays shortcode usage with defaults.
	 *
	 * ## EXAMPLES
	 * wp awfn shortcode
	 *
	 * @subcommand shortcode
	 * @alias shortcode
	 */
	public function shortcode_usage() {
		WP_CLI::success( "[adds_weather apts='KSMF' hours=2 show_taf=1 show_pireps=1 show_station_info=1 radial_dist=100 title='']" );
	}


}

WP_CLI::add_command( 'awfn', 'AwfnCli' );

