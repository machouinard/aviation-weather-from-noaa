<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class Awfn
 *
 * This class defines subclasses and retrieves XML data for subclasses
 *
 * @package    Aviation Weather from NOAA
 * @subpackage AWFN
 * @since      0.4.0
 */
abstract class Awfn {

	protected static $log_name;
	protected $log;
	protected $hours;
	public $station;
	protected $station_name;
	protected $show;
	protected $url;
	protected $data = false;
	protected $display_data = false;
	public $xmlData = false;
	protected $decoded = false;

	/**
	 * Awfn constructor.
	 *
	 * Set up logger for individual subclasses
	 *
	 * @since 0.4.0
	 */
	public function __construct() {
		// Prepare logger
//		$this->log = new Logger( static::$log_name );
//		$this->log->pushHandler( new StreamHandler( PLUGIN_ROOT . '/logs/development.log', Logger::DEBUG ) );
//		$this->log->pushHandler( new StreamHandler( PLUGIN_ROOT . '/logs/production.log', Logger::WARNING ) );

	}

	/**
	 * Wrapper for subclass functions
	 *
	 * @since 0.4.0
	 */
	public function go() {
		if ( $this->load_xml() ) {
			$this->decode_data();
			$this->build_display();
			$this->display_data();
		}

	}

	/**
	 * Abstract function for building HTML output
	 *
	 * @since 0.4.0
	 */
	abstract public function build_display();

	/**
	 * Abstract function for decoding XML data
	 *
	 * @since 0.4.0
	 */
	abstract public function decode_data();

	/**
	 * Outputs HTML built by subclasses
	 *
	 * @since 0.4.0
	 */
	public function display_data() {

		if ( $this->display_data && $this->show ) {
			echo $this->display_data;
		}

	}

	/**
	 * Retrieves XML data using URL provided by subclass and returns array converted from simplexmlelement
	 *
	 * SimpleXMLElement is returned to AwfnPirep without conversion for iteration
	 *
	 * @since 0.4.0
	 */
	public function load_xml() {
		$xml_raw = wp_remote_get( esc_url_raw( $this->url ) );
		if ( is_wp_error( $xml_raw ) ) {
//			$this->log->warn( $xml_raw->get_error_message() );
			$this->xmlData = false;

			return false;
		}
		$body = wp_remote_retrieve_body( $xml_raw );
		if ( is_wp_error( $body ) ) {
			return false;
		}
		$loaded = simplexml_load_string( $body );
		if ( ! empty( $loaded->errors ) ) {
//			$this->log->debug( (string) $loaded->errors->error );

			return false;
		}
		$atts = $loaded->data->attributes();
		if ( 0 < $atts['num_results'] ) {
			if ( 'AircraftReport' == static::$log_name ) {
				// maintain simplexmlelement to preserve all pireps
				$xml_array = $loaded->data->{static::$log_name};
			} else {
				$xml_array = json_decode( json_encode( $loaded->data->{static::$log_name} ), 1 );
			}
			$this->xmlData = $xml_array;

			return true;
		}

	}

}