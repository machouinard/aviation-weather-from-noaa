<?php

class CloudChunkDecoderTest extends WP_UnitTestCase {
  private $AwfnMetarDecoder;

  public function __construct() {
          $this->AwfnMetarDecoder = new AwfnMetar();
  }

  /**
   * Test a problematic METAR
   */
  public function testParseFoggyMetar() {
    $metar = "KUAO 111653Z 00000KT 1/4SM FG VV000 10/09 A3000 RMK AO2 SLP158 T01000094";
    $result = $this->AwfnMetarDecoder->decode_text_metar($metar);

		$exp_result = "<p>16:53 UTC</p>
<p>Wind: 0&deg; 0kt</p>
<p>Visibility: 0.25 SM</p>
<p>Sky: VV  </p>
<p>Temp: 10 deg C / 50 deg F</p>
<p>Dewpoint: 9 deg C / 48 deg F</p>
<p>Pressure: 30 inHg</p>";

	$this->assertEquals($exp_result, $result);
  }
}
