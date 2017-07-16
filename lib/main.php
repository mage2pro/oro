<?php
use Df\Oro\Settings\General as S;
use Zend_Http_Client as C;
/**
 * 2017-06-04
 * @param string $entity
 * «How to apply a filter to a «get list» Web API request?» https://oplatform.club/t/103
 * @param array(string => mixed) $filter [optional]
 * «How to include the related entities to a response on a Web API «get list» request?»
 * https://oplatform.club/t/105
 * @param string[] $include [optional]
 * @param bool $local [optional]
 * «What is the difference between the «application/json» and «application/vnd.api+json»
 * content types of a Web API response?» https://oplatform.club/t/104
 * @param bool $vnd [optional]
 * @return array(string => mixed)
 */
function df_oro_get_list(
	$entity, array $filter = [], array $include = [], $local = false, $vnd = true
) {
	// 2017-06-28
	// Due to a Oro Platform bug, a Web API request can randomly fail
	// with the «Unauthorized» response message.
	/** @var $attempt */
	$attempt = 1;
	/** @var $maxAttempts */
	$maxAttempts = 10;
	/** @var string|null $raw */
	$raw = null;
	$c = null; /** @var C $c */
	while (!$raw && $attempt++ <= $maxAttempts)  {
		$c = df_zf_http('https://'
			. ($local ? 'localhost.com:848/app_dev.php' : 'erp.mage2.pro')
			. "/api/extenddf$entity"
		)
			// 2017-06-28
			// Due to a Oro Platform bug, the «content-type» headers is required for the «vnd» case,
			// even it does not have any sense here.
			// «Difference between the Accept and Content-Type HTTP headers»
			// https://webmasters.stackexchange.com/questions/31212
			->setHeaders(df_oro_headers() + (!$vnd ? ['accept' => 'application/json'] : array_fill_keys(
				['accept', 'content-type'], 'application/vnd.api+json'
			)))
			->setParameterGet(df_clean(['filter' => $filter, 'include' => df_csv($include)]))
		;
		$raw = $c->request()->getBody();
	}
	if (!$raw) {
		/** @var \Zend_Http_Response $res */
		$res = $c->getLastResponse();
		df_error("The last Oro Web API request fails with the message «{$res->getMessage()}».\n"
			."The response headers:\n%s\n.The request:\n%s\n."
			,$res->getHeadersAsString(), $c->getLastRequest()
		);
	}
	return df_json_decode($raw);
}

/**
 * 2017-06-02
 * «How is «oro:wsse:generate-header» implemented?» https://oplatform.club/t/84
 * @param string|null $username [optional]
 * @param string|null $key [optional]
 * @return array(string => string)
 */
function df_oro_headers($username = null, $key = null) {
	/** @var string $created */
	$created = date('c');
	/** @var string $nonce */
	$nonce = base64_encode(substr(md5(uniqid(gethostname() . '_', true)), 0, 16));
	return [
		'Authorization' => 'WSSE profile="UsernameToken"'
		,'X-WSSE' => 'UsernameToken ' . df_csv_pretty(df_map_k(function($k, $v) {return
		"$k=\"$v\""
	;}, [
		'Username' => $username ?: S::s()->username()
		,'PasswordDigest' => base64_encode(sha1(implode([
			base64_decode($nonce), $created, $key ?: S::s()->key()
		]), true))
		,'Nonce' => $nonce
		,'Created' => $created
	]))];
}