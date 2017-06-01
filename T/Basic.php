<?php
namespace Df\Oro\T;
use Df\Oro\Settings\General as S;
// 2017-06-01
final class Basic extends TestCase {
	/** @test 2017-06-01 */
	function t01() {echo S::s()->key();}
}