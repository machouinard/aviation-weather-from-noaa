<?php
class StationTest extends WP_UnitTestCase {

	protected $station;

	public function setup() {
		$this->station = new AwfnStation('kord');
		$this->station->go( false );
	}

	function testSimpleFalse() {
		$this->assertFalse( $this->station->will_show() );
	}

	function testStaticInfo() {
		$icao = 'KOKC';
		// Check our stored option for matching ICAO data
		$stations = get_option( STORED_STATIONS_KEY, array() );
		// Our test station should not be found in the option
		$this->assertFalse( isset( $stations[$icao] ) );

		$info = AwfnStation::static_apt_info($icao);
		$this->assertArrayHasKey( 'station_id', $info );
		$this->assertArrayHasKey( 'latitude', $info );
		$this->assertArrayHasKey( 'longitude', $info );
		$this->assertArrayHasKey( 'country', $info );

		$stations = get_option( STORED_STATIONS_KEY, array() );
		// Our test station should have been added to the option for caching
		$this->assertTrue( isset( $stations[$icao] ) );
	}

	function testStationExists() {
		$this->assertTrue( $this->station->station_exist() );
	}

	function testStationNotExists() {
		$station = new AwfnStation('wxyz');
		$station->go( false );
		$this->assertFalse( $station->station_exist() );
	}

}
