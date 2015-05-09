<?php

class WeatherWidgetTest extends WP_UnitTestCase {

	/**
	 * @group default
	 */
	public function setUp() {

		\WP_Mock::setUp();

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

	function testSample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	/**
	 * @covers  Machouinard_Adds_Weather_Widget::clean_icao()
	 */
	function testClean_icao() {
		$icao = Machouinard_Adds_Weather_Widget::clean_icao( 'smf' );
		$expected = 'KSMF';
		$this->assertEquals( $icao, $expected );
	}

	/**
	 * @covers  Machouinard_Adds_Weather_Widget::clean_icao()
	 */
	function testClean_icao_two() {
		$icao = Machouinard_Adds_Weather_Widget::clean_icao( 'dtw' );
		$expected = 'KDTW';
	}

	/**
	 * @covers  Machouinard_Adds_Weather_Widget::get_metar()
	 */
	function testGet_metar_one() {
		$metar = Machouinard_Adds_Weather_Widget::get_metar( 'KSMF', 4 );
		$this->assertArrayHasKey( 'metar', $metar );
		$count = count( $metar['metar'] );
//		$this->assertGreaterThan(1, $count, '1 should be greater than ' . $count );;
	}

	/**
	 * @covers Machouinard_Adds_Weather_Widget::get_pireps()
	 */
	function testGet_pireps() {
		$pireps = Machouinard_Adds_Weather_Widget::get_pireps( 'KSEA', 120, 5 );
		$count = count ( $pireps );
		$this->assertGreaterThanOrEqual( 0, $count );
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
	}

}

