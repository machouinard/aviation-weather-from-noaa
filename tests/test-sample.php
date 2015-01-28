<?php

class SampleTest extends WP_UnitTestCase {

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
		$this->assertEquals( $count, 4 );
	}

	/**
	 * @covers Machouinard_Adds_Weather_Widget::get_pireps()
	 */
	function testGet_pireps() {
		$pireps = Machouinard_Adds_Weather_Widget::get_pireps( 'KSEA', 120, 5 );
		$count = count ( $pireps );
		$this->assertGreaterThan( 0, $count );
	}

}

