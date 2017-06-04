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
	/** @var C $c */
	$c = (new C)
		->setConfig(['timeout' => 120])
		->setHeaders(df_oro_headers() + (!$vnd ? ['content-type' => 'application/json'] : array_fill_keys(
			['accept', 'content-type'], 'application/vnd.api+json'
		)))
		->setUri(
			'https://'
			. ($local ? 'localhost.com:848/app_dev.php' : 'erp.mage2.pro')
			. "/api/extenddf$entity"
		)
		->setParameterGet(df_clean(['filter' => $filter, 'include' => df_csv($include)]))
	;
	if ($local) {
		$c->setAdapter((new \Zend_Http_Client_Adapter_Socket)->setStreamContext([
			'ssl' => ['allow_self_signed' => true, 'verify_peer' => false]
		]));
	}
	return df_json_decode($c->request()->getBody());
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