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
}
