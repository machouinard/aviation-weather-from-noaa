<?php

class TestDefaultMetar extends WP_UnitTestCase {

	private $smf;


	public function setUp() {

		$this->smf = new AwfnMetar();
		$this->smf->go( true );
	}

	public function tearDown() {

	}

	function testDefaultMetarHours() {
		$this->assertEquals( 1, $this->smf->get_hours() );
	}

	function testDefaultStationPublic() {
		$this->assertEquals( 'KSMF', $this->smf->station );
	}

	function testDefaultShow() {
		$this->assertEquals( true, $this->smf->get_show() );
	}

	function testDefaultMetarXmlData() {
		$xmlData = $this->smf->xmlData;
		$this->assertEquals( 'KSMF', $xmlData['station_id'] );
		$this->assertEquals( 38.7, (float) $xmlData['latitude'] );
		$this->assertEquals( -121.6, (float) $xmlData['longitude'] );
		$this->assertEquals( 'METAR', $xmlData['metar_type'] );
	}

}

