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
		$this->assertArrayHasKey( 'country', AwfnStation::static_apt_info('ksmf') );
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
