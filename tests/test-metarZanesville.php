<?php

class TestMetarZanesville extends WP_UnitTestCase {

	private $zzv;


	public function setUp() {

		$this->zzv = new AwfnMetar( 'kzzv', 4, false );
	}

	public function tearDown() {

	}

	function testDefaultMetarHours() {
		$this->assertEquals( 4, $this->zzv->get_hours() );
	}

	function testStationCase() {
		$this->assertEquals( 'kzzv', $this->zzv->station );
	}

	function testStationShow() {
		$this->assertEquals( false, $this->zzv->get_show() );
	}

	function testMetarXmlData() {
		$this->zzv->go( true );

		$xmlData = $this->zzv->xmlData;
		$this->assertEquals( 'KZZV', $xmlData['station_id'] );
		$this->assertEquals( 39.95, (float) $xmlData['latitude'] );
		$this->assertEquals( -81.9, (float) $xmlData['longitude'] );
		$this->assertEquals( 'METAR', $xmlData['metar_type'] );
	}

}