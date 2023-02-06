<?php
use Df\Core\Exception as DFE;
use Df\Oro\Settings\General as S;
use Zend_Http_Client as C;
/**
 * 2017-06-04
 * @used-by dfe_portal_stripe_customers()
 * «How to apply a filter to a «get list» Web API request?» https://oplatform.club/t/103
 * @param array(string => mixed) $filter [optional]
 * «How to include the related entities to a response on a Web API «get list» request?»
 * https://oplatform.club/t/105
 * @param string[] $include [optional]
 * «What is the difference between the «application/json» and «application/vnd.api+json»
 * content types of a Web API response?» https://oplatform.club/t/104
 * @return array(string => mixed)
 * @throws DFE
 */
function df_oro_get_list(string $entity, array $filter = [], array $include = [], bool $local = false):array {
	# 2017-06-28 Due to a Oro Platform bug, a Web API request can randomly fail with the «Unauthorized» response message.
	$attempt = 1; /** @var $attempt */
	$maxAttempts = 10; /** @var $maxAttempts */
	$raw = null; /** @var string|null $raw */
	$c = null; /** @var C $c */
	while (!$raw && $attempt++ <= $maxAttempts)  {
		$c = df_zf_http('https://'
			. ($local ? 'localhost.com:848/index_dev.php' : 'erp.mage2.pro')
			. "/api/extenddf$entity"
		)
			# 2017-06-28
			# Due to a Oro Platform bug, the «content-type» headers is required for the «vnd» case,
			# even it does not have any sense here.
			# «Difference between the Accept and Content-Type HTTP headers»
			# https://webmasters.stackexchange.com/questions/31212
			->setHeaders(df_oro_headers() + (array_fill_keys(['accept', 'content-type'], 'application/vnd.api+json')))
			->setParameterGet(df_clean(['filter' => $filter, 'include' => df_csv($include), 'page' => ['size' => -1]]))
		;
		$raw = $c->request()->getBody();
	}
	$res = $c->getLastResponse(); /** @var \Zend_Http_Response $res */
	if (!$raw || $res->isError()) {
		df_error("The last Oro Web API request fails with the message «{$res->getMessage()}».\n"
			."The response headers:\n%s\n.The request:\n%s\n."
			,$res->getHeadersAsString(), $c->getLastRequest()
		);
	}
	return df_json_decode($raw);
}

/**
 * 2017-06-02 «How is «oro:wsse:generate-header» implemented?» https://oplatform.club/t/84
 * @used-by df_oro_get_list()
 * @return array(string => string)
 */
function df_oro_headers(string $username = '', string $key = ''):array {
	$created = date('c'); /** @var string $created */
	$nonce = base64_encode(substr(md5(uniqid(gethostname() . '_', true)), 0, 16)); /** @var string $nonce */
	return [
		'Authorization' => 'WSSE profile="UsernameToken"'
		,'X-WSSE' => 'UsernameToken ' . df_csv_pretty(df_map_k(function($k, $v) {return
		"$k=\"$v\""
	;}, [
		'Username' => $username ?: S::s()->username()
		,'PasswordDigest' => base64_encode(sha1(implode([base64_decode($nonce), $created, $key ?: S::s()->key()]), true))
		,'Nonce' => $nonce
		,'Created' => $created
	]))];
}