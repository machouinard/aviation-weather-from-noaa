<?php

class TestMetar extends WP_UnitTestCase {

	function testMetarFound() {
		$metar = new AwfnMetar( 'KLAS', 4, false );
		$metar->go( false );

		$output = $metar->display_data();
		$this->assertTrue( false !== $output );
	}

	function testMetarNotFound() {
		$metar = new AwfnMetar( 'KHHH', 4, false );
		$metar->go( false );

		$output = $metar->display_data();
		$this->assertFalse( $output );
	}

}