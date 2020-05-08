<?php
/*
This file is part of "Total Order Discount" project and subject to the terms
and conditions defined in file "LICENSE.txt", which is part of this source
code package and also available on the project page: https://git.io/JvumR
*/

class ModelExtensionTotalOrderDiscount extends Model {
	public function getTotal($total) {
		if ($this->config->get('total_order_discount_status') && $this->cart->hasProducts()) {
			if ($this->config->get('total_tax_status')) {
				$cart_total = $this->cart->getSubTotal() + array_sum($this->cart->getTaxes());
			} else {
				$cart_total = $this->cart->getSubTotal();
			}

			$discount = $this->config->get('total_order_discount');

			if ($discount['condition'] === 'quantity') {
				$discount['condition'] = $this->cart->countProducts();
			} else  {
				// $discount['condition'] === 'price'
				$discount['condition'] = $cart_total;
			}

			$total_discount = 0;

			if (($discount['condition'] >= $discount['key'] && $discount['value'] > 0) ||
				($discount['condition'] < $discount['key'] && $discount['value'] < 0) ||
				$discount['key'] == 0
			) {
				$total_discount = ($discount['type'] === 'fixed')
					? (float)$discount['value']
					: $cart_total * (float)$discount['value'] / 100;
			}

			if ($total_discount) {
				$this->load->language('extension/total/order_discount');

				$total_discount = -$total_discount;

				$total['totals'][] = array(
					'code'       => 'order_discount',
					'title'      => $discount['title'][$this->config->get('config_language_id')],
					'value'      => $total_discount,
					'sort_order' => $discount['sort_order'],
				);

				$total['total'] += $total_discount;
			}
		}
	}
}
