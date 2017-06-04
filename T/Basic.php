<?php
namespace Df\Oro\T;
use Zend_Http_Client as C;
// 2017-06-01
final class Basic extends TestCase {
	/** 2017-06-01 */
	function t01_users() {
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

	/** 2017-06-03 */
	function t02_customers() {
		/** @var C $c */
		$c = (new C)
			->setConfig(['timeout' => 120])
			->setHeaders(['content-type' => 'application/json'] + df_oro_headers())
			->setMethod(C::GET)
			->setUri("https://localhost.com:848/app_dev.php/api/extenddfcustomers")
		;
		$c->setAdapter((new \Zend_Http_Client_Adapter_Socket)->setStreamContext([
			'ssl' => ['allow_self_signed' => true, 'verify_peer' => false]
		]));
		echo df_dump(df_json_decode($c->request()->getBody()));
	}

	/**
	 * 2017-06-04
	 * «How to apply a filter to a «get list» Web API request?» https://oplatform.club/t/103
	 */
	function t03_orders() {
		/** @var C $c */
		$c = (new C)
			->setConfig(['timeout' => 120])
			->setHeaders(['content-type' => 'application/json'] + df_oro_headers())
			->setMethod(C::GET)
			->setUri("https://localhost.com:848/app_dev.php/api/extenddforders?filter[product]=1")
		;
		$c->setAdapter((new \Zend_Http_Client_Adapter_Socket)->setStreamContext([
			'ssl' => ['allow_self_signed' => true, 'verify_peer' => false]
		]));
		echo df_json_encode_pretty(df_json_decode($c->request()->getBody()));
	}

	/**
	 * 2017-06-04
	 * «What is the difference beetween the «application/json» and «application/vnd.api+json»
	 * content types of a Web API response?» https://oplatform.club/t/104
	 */
	function t04_orders_vnd() {
		/** @var C $c */
		$c = (new C)
			->setConfig(['timeout' => 120])
			->setHeaders([
				'accept' => 'application/vnd.api+json'
				,'content-type' => 'application/vnd.api+json'
			] + df_oro_headers())
			->setMethod(C::GET)
			->setUri("https://localhost.com:848/app_dev.php/api/extenddforders?filter[product]=1")
		;
		$c->setAdapter((new \Zend_Http_Client_Adapter_Socket)->setStreamContext([
			'ssl' => ['allow_self_signed' => true, 'verify_peer' => false]
		]));
		echo df_json_encode_pretty(df_json_decode($c->request()->getBody()));
	}

	/** @test
	 * 2017-06-04
	 * How to apply a filter to a «get list» Web API request? 
	 * Inclusion Filters: https://www.orocrm.com/documentation/2.0/book/data-api#inclusion-filter-include
	 */
	function t05_orders_include() {
		/** @var C $c */
		$c = (new C)
			->setConfig(['timeout' => 120])
			->setHeaders([
				'accept' => 'application/vnd.api+json'
				,'content-type' => 'application/vnd.api+json'
			] + df_oro_headers())
			->setMethod(C::GET)
			->setUri("https://localhost.com:848/app_dev.php/api/extenddforders?filter[product]=1&include=product,website")
		;
		$c->setAdapter((new \Zend_Http_Client_Adapter_Socket)->setStreamContext([
			'ssl' => ['allow_self_signed' => true, 'verify_peer' => false]
		]));
		echo df_json_encode_pretty(df_json_decode($c->request()->getBody()));
	}
}