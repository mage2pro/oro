<?php
namespace Dfe\Oro\Settings;
# 2017-06-01
/** @method static General s() */
final class General extends \Df\Config\Settings {
	/**
	 * 2017-06-01
	 * @used-by df_oro_headers()
	 */
	function key():string {return $this->p();}

	/**
	 * 2017-06-02
	 * @used-by df_oro_headers()
	 */
	function username():string {return $this->v();}

	/**
	 * 2017-06-01
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 */
	protected function prefix():string {return 'df_oro/general';}
}