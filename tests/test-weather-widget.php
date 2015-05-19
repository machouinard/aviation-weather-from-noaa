<?php

class WeatherWidgetTest extends WP_UnitTestCase {

	/**
	 * @group default
	 */
	public function setUp() {

		\WP_Mock::setUp();

		add_action( 'widgets_init', 'machouinard_adds_register_widget' );

		\WP_Mock\Handler::register_handler(
			'sanitize_text_field',
			function( $str ){
				return $str;
			}
		);

	}

	/**
	 * @group default
	 */
	public function tearDown() {
		\WP_Mock::tearDown();
	}

	/**
	 * @covers  Machouinard_Adds_Weather_Widget::clean_icao()
	 */
	function testClean_icao() {
		$actual = Machouinard_Adds_Weather_Widget::clean_icao( 'gll' );
		$expected = 'EGLL';
		$this->assertEquals( $actual, $expected );
	}

	/**
	 * @covers  Machouinard_Adds_Weather_Widget::clean_icao()
	 */
	function testClean_icao_two() {
		$actual = Machouinard_Adds_Weather_Widget::clean_icao( 'yvr' );
		$expected = 'CYVR';
		$this->assertEquals( $actual, $expected );
	}

	/**
	 * @covers  Machouinard_Adds_Weather_Widget::clean_icao()
	 */
	function testClean_icao_three() {
		$actual = Machouinard_Adds_Weather_Widget::clean_icao( 'qqq' );
		$expected = 'KQQQ';
		$this->assertNotEquals( $actual, $expected );
	}

	/**
	 * @covers  Machouinard_Adds_Weather_Widget::clean_icao()
	 */
	function testClean_icao_four() {
		$actual = Machouinard_Adds_Weather_Widget::clean_icao( 'qqq' );
		$expected = 'QQQ';
		$this->assertEquals( $actual, $expected );
	}

	/**
	 * @covers  Machouinard_Adds_Weather_Widget::clean_icao()
	 */
	function testClean_icao_five() {
		$actual = Machouinard_Adds_Weather_Widget::clean_icao( 'KDTW' );
		$expected = 'KDTW';
		$this->assertEquals( $actual, $expected );
	}

	/**
	 * @covers  Machouinard_Adds_Weather_Widget::clean_icao()
	 */
	function testClean_icao_six() {
		$actual = Machouinard_Adds_Weather_Widget::clean_icao( 'ssy' );
		$expected = 'YSSY';
		$this->assertEquals( $actual, $expected );
	}

	/**
	 * @covers  Machouinard_Adds_Weather_Widget::clean_icao()
	 */
	function testClean_icao_lowercase() {
		$actual = Machouinard_Adds_Weather_Widget::clean_icao( 'dtw' );
		$expected = 'kdtw';
		$this->assertNotEquals( $actual, $expected );
	}

	/**
	 * @covers  Machouinard_Adds_Weather_Widget::get_metar()
	 */
	function testGet_metar_one() {
		$metar = Machouinard_Adds_Weather_Widget::get_metar( 'KSMF', 4 );
		$this->assertArrayHasKey( 'metar', $metar );
		$count = count( $metar['metar'] );

		$expected = 1;
		$this->assertGreaterThanOrEqual( $expected, $count );;
	}

	/**
	 * @covers Machouinard_Adds_Weather_Widget::get_pireps()
	 */
	function testGet_pireps() {
		$pireps = Machouinard_Adds_Weather_Widget::get_pireps( 'KSEA', 120, 5 );
		$count = count ( $pireps );
		$this->assertGreaterThanOrEqual( 0, $count );
		$this->assertArrayHasKey( 0, $pireps );
	}


		/**
	 * @covers Machouinard_Adds_Weather_Widget::get_apt_info()
	 */
	function testGet_apt_info() {
		$apt = Machouinard_Adds_Weather_Widget::get_apt_info( 'kzzv' );
		$bad_apt = Machouinard_Adds_Weather_Widget::get_apt_info( 'zzv' );
		$count = count( $apt );
		$bad_count = count( $bad_apt );
		$expected = 4;
		$this->assertEquals( $count, $expected );
		$this->assertNotEquals($expected, $bad_count );
		$expected_city = 'ZANESVILLE';
		$this->assertEquals( $apt['city'], $expected_city );
		$this->assertArrayHasKey( 'station_id', $apt );
	}

	function testGet_apt_info_two() {
		$apt = 'EGLL';
		$info = Machouinard_Adds_Weather_Widget::get_apt_info( $apt );
		$count = count( $info );
		$this->assertEquals( 4, $count );
		$this->assertArrayHasKey( 'city', $info );
		$this->assertEquals( $info['city'], 'LONDON/HEATHROW' );
	}

	function testGet_apt_info_three() {
		$apt = 'QQQQ';
		$info = Machouinard_Adds_Weather_Widget::get_apt_info( $apt );
		$this->assertFalse( $info );
	}

	function testLoad_xml() {
		$url = 'http://chouinard.me';
		$result = Machouinard_Adds_Weather_Widget::load_xml( $url );
		$this->assertFalse( $result );
	}

}

