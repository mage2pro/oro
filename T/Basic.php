<?php
namespace Df\Oro\T;
// 2017-06-01
final class Basic extends TestCase {
	/** @test
	 * 2017-06-04
	 * «How to include related entities to a response on a Web API «get list» request?»
	 * https://oplatform.club/t/105
	 */
	function t01_orders() {echo df_json_encode_pretty(df_oro_get_list(
		'orders', ['product' => 1], ['product', 'website'], true
	));}
}