<?php
namespace Df\Oro\T;
use Zend_Http_Client as C;
// 2017-06-01
final class Basic extends TestCase {
	/** @test 2017-06-01 */
	function t01() {
		/** @var C $c */
		$c = (new C)
			->setConfig(['timeout' => 120])
			->setHeaders(['content-type' => 'application/json'] + df_oro_headers())
			->setMethod(C::GET)
			->setUri("https://localhost.com:848/app_dev.php/api/rest/latest/users")
		;
		$c->setAdapter((new \Zend_Http_Client_Adapter_Socket)->setStreamContext([
			'ssl' => ['allow_self_signed' => true, 'verify_peer' => false]
		]));
		echo df_dump(df_json_decode($c->request()->getBody()));
	}
}