<?php
/**
 * Created by PhpStorm.
 * User: markchouinard
 * Date: 2/7/16
 * Time: 12:14 AM
 */
class TestDefaultPirep extends WP_UnitTestCase {

	private $smf;


	public function setUp() {

		$this->smf = new AwfnPirep( 38.7, -121.6 );
		$this->smf->go( true );
	}

	public function tearDown() {

	}

	function testDefaultPirepHours() {
		$this->assertEquals( 1, $this->smf->get_hours() );
	}

	function testDefaultShow() {
		$this->assertEquals( true, $this->smf->get_show() );
	}

}