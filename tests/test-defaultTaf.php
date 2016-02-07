<?php
/**
 * Created by PhpStorm.
 * User: markchouinard
 * Date: 2/7/16
 * Time: 12:07 AM
 */
class TestDefaultTaf extends WP_UnitTestCase {

	private $smf;


	public function setUp() {

		$this->smf = new AwfnTaf();
		$this->smf->go( true );
	}

	public function tearDown() {

	}

	function testDefaultTafHours() {
		$this->assertEquals( 1, $this->smf->get_hours() );
	}

	function testDefaultStationPublic() {
		$this->assertEquals( 'KSMF', $this->smf->station );
	}

	function testDefaultShow() {
		$this->assertEquals( true, $this->smf->get_show() );
	}

	function testDefaultTafXmlData() {
		$xmlData = $this->smf->xmlData;
		$this->assertEquals( 'KSMF', $xmlData['station_id'] );
		$this->assertEquals( 38.7, (float) $xmlData['latitude'] );
		$this->assertEquals( -121.6, (float) $xmlData['longitude'] );
//		print_r( $xmlData );
	}


}