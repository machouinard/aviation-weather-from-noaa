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
	protected $log = false;
	protected $hours;
	public $station;
	protected $show;
	protected $url;
	protected $data = false;
	protected $display_data = false;
	public $xmlData = false;
	protected $decoded = false;

	public function get_hours() {
		return $this->hours;
	}

	public function get_show() {
		return $this->show;
	}

	public function has_data() {
		return $this->display_data;
	}

	/**
	 * Awfn constructor.
	 *
	 * Set up logger for individual subclasses.
	 * Due to permissin issues we use AWFN_DEBUG instead of WP_DEBUG in case that is set true for other reasons.
	 *
	 * TODO: Fix file permission issues
	 * Inside plugin dir: "sudo mkdir logs", "sudo chown `whoami` logs", "chmod 700 logs"
	 *
	 * @since 0.4.0
	 */
	public function __construct() {

		// Prepare logger
		if ( defined( 'AWFN_DEBUG' ) && AWFN_DEBUG ) {

			$dev_log_dir = PLUGIN_ROOT . 'logs';

			// Permissions for his one are up to you, for now. Sorry.
			if ( ! file_exists( $dev_log_dir ) ) {
				mkdir( $dev_log_dir, 0755, true );
			}
			$prod_log_dir = PLUGIN_ROOT . 'logs';
			if ( ! file_exists( $prod_log_dir ) ) {
				mkdir( $prod_log_dir, 0755, true );
			}
			$this->log = new Logger( static::$log_name );
			$this->log->pushHandler( new StreamHandler( PLUGIN_ROOT . 'logs/debug.log', Logger::DEBUG ) );
			$this->log->pushHandler( new StreamHandler( PLUGIN_ROOT . 'logs/warning.log', Logger::WARNING ) );
		}

	}

	/**
	 * Log debug or warning messages if our logger is set up.
	 * @param $severity     string debug | warning
	 * @param $msg          string Message to log
	 */
	protected function maybelog( $severity, $msg ) {

		if ( false !== $this->log ) {
			$this->log->$severity( $msg );
		}

	}

	/**
	 * Wrapper for subclass functions
	 *
	 * @since 0.4.0
	 */
	public function go( $display = true ) {

		if ( $this->load_xml() ) {

			$this->decode_data();
			$this->build_display();

			if ( $display ) {

				$this->display_data();
			}
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

			echo '<section id="' . strtolower( static::$log_name ) . '">';
			echo $this->display_data;
			echo '</section>';

		} else {
			return $this->display_data;
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
			$this->maybelog( 'warn', $xml_raw->get_error_message() );
			$this->xmlData = false;

			return false;
		}
		$body = wp_remote_retrieve_body( $xml_raw );
		if ( '' == $body || strpos( $body, '<!DOCTYPE' ) ) {
			$this->maybelog( 'debug', print_r( $xml_raw, true ) );
			$this->maybelog( 'debug', $body );

			return false;
		}

		$loaded = simplexml_load_string( $body );
		if ( ! empty( $loaded->errors ) ) {
			$this->maybelog( 'debug', (string) $loaded->errors->error );

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