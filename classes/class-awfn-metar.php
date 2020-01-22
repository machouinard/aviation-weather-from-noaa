<?php
require_once PLUGIN_ROOT . 'vendor/autoload.php';
use MetarDecoder\MetarDecoder;

/**
 * Class AwfnMetar
 *
 * This class retrieves the most current METAR report, decodes it and builds the HTML output
 *
 * @package     Aviation Weather from NOAA
 * @subclass    Metar
 * @since       0.4.0
 */
class AwfnMetar extends Awfn {

	/**
	 * AwfnMetar constructor.
	 *
	 * Setup log name - late static binding, build URL for Awfn::load_xml()
	 *
	 * @param string $icao
	 * @param int $hours
	 * @param bool $show
	 *
	 * @since 0.4.0
	 */
	public function __construct( $icao = 'KSMF', $hours = 2, $show = true ) {

		self::$log_name = 'METAR';

		parent::__construct();

		$base_url = 'https://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars';
		$base_url .= '&requestType=retrieve&format=xml&mostRecent=true&stationString=%s&hoursBeforeNow=%d';
		$this->url   = sprintf( $base_url, $icao, $hours );
		$this->icao  = $icao;
		$this->hours = $hours;
		$this->show  = $show;

		$this->maybelog('info', 'New for ' . $icao );

	}

	/**
	 * Assigns most recent metar to $this->data and begins the decoding process
	 */
	public function decode_data() {

		if ( $this->xmlData ) {
			$this->data = $this->xmlData['raw_text'];
			$this->decode_metar();
		} else {
			$this->maybelog( 'debug', 'No metar data returned for ' . $this->icao );
		}
	}

	/**
	 * Test a raw text Metar
	 *
	 * @since 0.4.1
	 *
	 */
	 public function decode_text_metar($metar = '') {

		 if ( $metar ) {
			 $this->data = $metar;
			 $this->decode_metar();
			 return $this->decoded;
		 } else {
			 $this->maybelog( 'debug', 'No metar data returned for ' . $this->icao );
		 }
	 }
	/**
	 * Decodes raw metar into friendlier display
	 *
	 * @since 0.4.0
	 */
	private function decode_metar() {
		$decoder = new MetarDecoder();
		$d       = $decoder->parse( $this->data );
		if ( $d->isValid() ) {
			$time                 = $d->getTime();
			$sw                   = $d->getSurfaceWind();
			$wind                 = $sw->getMeanDirection();
			$wind_dir             = null == $wind ? '' : $sw->getMeanDirection()->getValue();
			$speed                = $sw->getMeanSpeed();
			$wind_speed           = null == $speed ? '' : $sw->getMeanSpeed()->getValue();
			$wind_unit            = null == $speed ? '' : $sw->getMeanSpeed()->getUnit();
			$v                    = $d->getVisibility();
			$visibility           = null == $v ? '' : $v->getVisibility()->getValue();
			$v_units              = null == $v ? '' : $v->getVisibility()->getUnit();
			$cld                  = $d->getClouds();
			$cld_amount           = isset( $cld[0] ) ? $cld[0]->getAmount() : '';
			$cld_base_height      = ( isset( $cld[0] ) && null !== $cld[0] && null !== $cld[0]->getBaseHeight()) ? $cld[0]->getBaseHeight()->getValue() : '';
			$cld_base_height_unit = ( isset( $cld[0] ) && null !== $cld[0] && null !== $cld[0]->getBaseHeight()) ? $cld[0]->getBaseHeight()->getUnit() : '';
			$t                    = $d->getAirTemperature();
			$tmp                  = null == $t ? '' : $d->getAirTemperature()->getValue();
			$tmp_unit             = null == $t ? '' : $d->getAirTemperature()->getUnit();
			$dp                   = $d->getDewPointTemperature();
			$tmp_dewpoint         = null == $dp ? '' : $d->getDewPointTemperature()->getValue();
			$p                    = $d->getPressure();
			$pressure             = null == $p ? '' : $d->getPressure()->getValue();
			$pressure_unit        = null == $p ? '' : $d->getPressure()->getUnit();

			$tf = $this->to_farenheit( $tmp );
			$df = $this->to_farenheit( $tmp_dewpoint );

			$this->decoded
				= <<<MAC
<p>{$time}</p>
<p>Wind: {$wind_dir}&deg; {$wind_speed}{$wind_unit}</p>
<p>Visibility: {$visibility} {$v_units}</p>
<p>Sky: {$cld_amount} {$cld_base_height} {$cld_base_height_unit}</p>
<p>Temp: {$tmp} {$tmp_unit} / {$tf} deg F</p>
<p>Dewpoint: {$tmp_dewpoint} {$tmp_unit} / {$df} deg F</p>
<p>Pressure: {$pressure} {$pressure_unit}</p>
MAC;

		} else {
			$this->decoded = false;
			$this->maybelog( 'debug', 'Invalid METAR for ' . $this->icao );
			$this->maybelog( 'debug', $this->data );
		}
	}

	/**
	 * Builds HTML output for display on front-end
	 *
	 * @since 0.4.0
	 */
	public function build_display() {

		if ( $this->data ) {
			$this->display_data = '<header>METAR<span class="fas fa-sort-down"></span></header><article class="metar">'
			                      . esc_html( $this->data ) .
			                      '</article>';
			if ( $this->decoded ) {
				$this->display_data .= '<article class="decoded-metar">' . $this->decoded . '</article>';
			}
		} else {
			$this->display_data = '<article class="metar">' . __( 'No METAR returned', Adds_Weather_Widget::get_widget_slug() )
			                      . '</article>';
		}

	}


	private function to_farenheit( $c ) {
		return round( (float) $c * 9 / 5 + 32 );
	}


}
