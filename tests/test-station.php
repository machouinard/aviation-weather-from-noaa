<?php
class StationTest extends WP_UnitTestCase {

	protected $icao = 'kord';
	protected $expected_lat = 41.98;
	protected $expected_lng = -87.93;
	protected $station;

	public function setup() {
		$this->station = new AwfnStation( $this->icao );
		$this->station->go( false );
	}

	function testSimpleFalse() {
		$this->assertFalse( $this->station->will_show() );
	}

	function testLat() {
		$lat = $this->station->lat();
		$this->assertTrue( false !== $lat );
		$this->assertEquals( $this->expected_lat, $this->expected_lat );


	}

	function testLng() {
		$lng = $this->station->lng();
		$this->assertTrue( false !== $lng );
		$this->assertEquals( $this->expected_lng, $lng );
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

	function testBuildDisplayContainsCapIcao() {
		$display = $this->station->build_display();
		$this->assertSame( strtoupper( $this->icao ), $display['station_id'] );
	}

}
