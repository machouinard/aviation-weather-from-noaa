<?php
class StationTest extends WP_UnitTestCase {

	function testStationExists() {
		$station = new AwfnStation('ksmf');
		$station->go( false );
		$this->assertTrue( $station->station_exist() );
	}

	function testStationNotExists() {
		$station = new AwfnStation('khhh');
		$station->go( false );
		$this->assertFalse( $station->station_exist() );
	}

	function testStationOutputHasKeys() {
		$airport = new AwfnStation( 'kdtw' );
		$airport->clean_icao();
		$airport->get_apt_info();

		$this->assertArrayHasKey( 'station_id', $airport->xmlData );
		$this->assertArrayHasKey( 'wmo_id', $airport->xmlData );
		$this->assertArrayHasKey( 'latitude', $airport->xmlData );
		$this->assertArrayHasKey( 'longitude', $airport->xmlData );
		$this->assertArrayHasKey( 'country', $airport->xmlData );
	}
}
