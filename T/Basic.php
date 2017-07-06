<?php
namespace Df\Oro\T;
// 2017-06-01
final class Basic extends TestCase {
	/**
	 * 2017-06-04
	 * «How to include related entities to a response on a Web API «get list» request?»
	 * https://oplatform.club/t/105
	 */
	function t01_orders() {echo df_json_encode(df_oro_get_list(
		'orders', ['product' => 1], ['product', 'website'], true
	));}

	/** @test */
	function t02_orders_stripe() {
		$customers = df_map_r(function(array $a) {return [
			$a['id'], $a['country']						
		];}, df_oro_get_list('customers', [], [], true, false));
		$websites = array_values(df_map(
			df_sort_names(
				array_filter(
					df_oro_get_list('orders', ['product' => 1], ['website'], true)['included']
					,function(array $a) {return
						'extenddfwebsites' === $a['type']
						&& 'magento_2' === dfa_deep($a, 'relationships/platform/data/id')
					;}
				), null, function(array $a) {return dfa_deep($a, 'attributes/domain');}
			), function(array $a) use($customers) {$at = $a['attributes']; return [
				'country' => $customers[dfa_deep($a, 'relationships/dfcustomer_websites/data/id')]
				,'edition' => $at['m2_is_enterprise']  ? 'Enterprise' : 'Community'
				,'url' => ($u = $at['m2_version_url']) ? df_trim_text_right($u, '/magento_version') :
					"http://{$at['domain']}"
				,'version' => $at['m2_version']
			];
		}
		));
		echo df_json_encode($websites);
	}
}