<?php

/**
 * Class AwfnStation
 *
 * This class retrieves, caches and builds HTML output for individual airports based on ICAO code
 *
 * @package     Aviation Weather from NOAA
 * @subpackage  Station
 * @since       0.4.0
 */
class AwfnStation extends Awfn {

	/**
	 * AwfnStation constructor.
	 *
	 * Builds URL for Awfn::load_xml()
	 *
	 * @param      $icao
	 * @param bool $show
	 *
	 * @since 0.4.0
	 */
	public function __construct( $icao, $show = false ) {

		self::$log_name = 'Station';

		parent::__construct();

		$this->icao = strtoupper( sanitize_text_field( $icao ) );
		$this->show    = (bool) $show;

		$base = 'https://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=stations';
		$base .= '&requestType=retrieve&format=xml&stationString=%s';
		$this->url = sprintf( $base, $this->icao );

		$this->clean_icao();

	}

	public function go( $display = true ) {
		if ( $this->get_apt_info() ) {
			$this->decode_data();
			$this->build_display();

			if ( $display ) {
				$this->display_data();
			}
		}
	}

	/**
	 * Does airport exist?
	 *
	 * @return bool
	 * @since 0.4.0
	 */
	public function station_exist() {
		return $this->xmlData ? true : false;
	}

	public function get_icao() {
		return $this->icao;
	}

	/**
	 * Return airport latitude if available
	 *
	 * @return bool|string
	 * @since 0.4.0
	 */
	public function lat() {
		return $this->xmlData ? (float) $this->xmlData['latitude'] : false;
	}

	/**
	 * Return airport longitude if available
	 *
	 * @return bool|string
	 * @since 0.4.0
	 */
	public function lng() {
		return $this->xmlData ? (float) $this->xmlData['longitude'] : false;
	}

	/**
	 * Static wrapper for clean_icao()
	 *
	 * @param $icao
	 *
	 * @return string
	 * @since 0.4.0
	 */
	// TODO: SELF?
	public static function static_clean_icao( $icao ) {
		$airport = new self( $icao );

		if ( $airport->station_exist() ) {
			return $airport->xmlData['station_id'];
		} else {
			return false;
		}

	}

	/**
	 * Validates potential ICAO
	 * If given ICAO is only 3 chars it will cycle through to check for US, CA, AU and GB matches.
	 * filterable
	 *
	 * @since 0.4.0
	 */
	public function clean_icao() {

		if ( ! preg_match( '/^[A-Za-z]{3,4}$/', $this->icao, $matches ) ) {
			// $this->station has no chance of being legit
//			$this->station = '';

			return false;
		}

		// If ICAO is only 3 chars we'll check some possibilities; filterable
		if ( strlen( $matches[0] ) == 3 ) {
			foreach ( apply_filters( 'awfn_icao_search_array', array( 'K', 'C', 'M' ) ) as $first_letter ) {
				$this->icao = $first_letter . $matches[0];
				if ( $this->get_apt_info() ) {
					break;
				}
			}
		} else {

			// We have a 4 char ICAO so let's see if we can find a match
			$this->get_apt_info();
		}

		// No match found
		if ( false === $this->xmlData ) {
//			$this->station = '';
			return false;
		}

	}

	/**
	 * Retrieves airport information and caches in option using ICAO as key
	 *
	 * @return bool
	 * @since 0.4.0
	 */
	public function get_apt_info() {

		// If we don't have a possible match, bail
		if ( ! preg_match( '~^[A-Za-z0-9]{4,4}$~', $this->icao, $matches ) ) {
			return false;
		}

//		$station_name = strtoupper( $this->icao );

		// Check our stored option for matching ICAO data
		$stations = get_option( STORED_STATIONS_KEY, array() );

		if ( isset( $stations[ $this->icao ] ) ) {
			// Use cached station data
			$this->xmlData = $stations[ $this->icao ];
		} else {
			// No match found in option so we need to go external
			$this->url
				= sprintf( 'http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=stations&requestType=retrieve&format=xml&stationString=%s',
				$this->icao );

			$this->load_xml();

			if ( $this->xmlData ) {
				// Update option with new station data
				$stations[ $this->icao ] = $this->xmlData;
				update_option( STORED_STATIONS_KEY, $stations );
			}
		}

		return $this->xmlData ? true : false;
	}

	/**
	 * Static wrapper for get_apt_info()
	 *
	 * @param $icao
	 *
	 * @return bool
	 *
	 * @since 0.4.0
	 */
	public static function static_apt_info( $icao ) {
		$airport = new self( $icao );
		$airport->clean_icao();
		$airport->get_apt_info();

		return $airport->xmlData;
	}

	/**
	 * Copy xmlData to data in order to match functionality among subclasses
	 *
	 * @since 0.4.0
	 */
	public function decode_data() {
		// doing this to match other sub-classes functionality when building display
		$this->data = $this->xmlData;
	}

	/**
	 * Build HTML output for display on front-end
	 * currently uses city and state
	 *
	 * @since 0.4.0
	 */
	public function build_display() {

		// TODO: improve
		if ( $this->data && $this->show ) {
			$keys = array( 'site', 'state' );
			foreach ( $keys as $key ) {
				if ( isset( $this->data[ $key ] ) ) {
					$location_array[] = $this->data[ $key ];
				}
			}

			$location           = implode( ', ', array_filter( $location_array ) );
			$this->display_data = '<header>' . esc_html( $location ) . '</header>';
		} else {
			return $this->data;
		}

	}
}