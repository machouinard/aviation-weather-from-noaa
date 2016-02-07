<?php
/**
 * Created by PhpStorm.
 * User: markchouinard
 * Date: 2/7/16
 * Time: 12:30 AM
 */
class TestDefaultStation extends WP_UnitTestCase {

	private $smf;


	public function setUp() {

		$this->smf = new AwfnStation( 'KSMF' );
	}

	public function tearDown() {

	}

	public function testDefaultStationShow() {
		$this->assertFalse( $this->smf->get_show() );
	}

	public function testDefaultXml() {
		$this->smf->go( true );

//		print_r( $this->smf->xmlData );
		$xmlData = $this->smf->xmlData;
		$this->assertEquals( 'KSMF', $xmlData['station_id'] );
		$this->assertEquals( 38.7, (float) $xmlData['latitude'] );
		$this->assertEquals( -121.6, (float) $xmlData['longitude'] );
		$this->assertEquals( 'CA', $xmlData['state'] );
		$this->assertEquals( 'SACRAMENTO/METRO', $xmlData['site'] );
		$this->assertEquals( 'US', $xmlData['country'] );
		$this->assertArrayHasKey( 'site_type', $xmlData );
	}

}