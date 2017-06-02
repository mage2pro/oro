<?php
use Df\Oro\Settings\General as S;
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