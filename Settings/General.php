<?php
namespace Df\Oro\Settings;
# 2017-06-01
/** @method static General s() */
final class General extends \Df\Config\Settings {
	/**
	 * 2017-06-01
	 * @return string
	 */
	function key() {return $this->p();}

	/**
	 * 2017-06-02
	 * @return string
	 */
	function username() {return $this->v();}

	/**
	 * 2017-06-01
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'df_oro/general';}
}