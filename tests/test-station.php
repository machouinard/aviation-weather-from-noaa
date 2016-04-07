<?php
class StationTest extends WP_UnitTestCase {

	protected $station;

	public function setup() {
		$this->station = new AwfnStation('kord');
		$this->station->go( false );
	}

	function testStationExists() {
		$this->assertTrue( $this->station->station_exist() );
	}

	function testStationOutputHasKeys() {

		$this->assertArrayHasKey( 'station_id', $this->station->xmlData );
		$this->assertArrayHasKey( 'latitude', $this->station->xmlData );
		$this->assertArrayHasKey( 'longitude', $this->station->xmlData );
		$this->assertArrayHasKey( 'country', $this->station->xmlData );
	}
}
