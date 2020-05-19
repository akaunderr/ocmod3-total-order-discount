<?php
/*
This file is part of "Total Order Discount" project and subject to the terms
and conditions defined in file "LICENSE.txt", which is part of this source
code package and also available on the project page: https://git.io/JvumR
*/

class ControllerExtensionTotalOrderDiscount extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/total/order_discount');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (('POST' == $this->request->server['REQUEST_METHOD']) && $this->validate()) {
			$this->model_setting_setting->editSetting('total_order_discount', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect(
				$this->url->link(
					'marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true
				)
			);
		}

		if (isset($this->error['permission'])) {
			$data['error_permission'] = $this->error['permission'];
		} else {
			$data['error_permission'] = '';
		}

		if (isset($this->error['discount_title'])) {
			$data['error_discount_title'] = $this->error['discount_title'];
		} else {
			$data['error_discount_title'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/total/order_discount', 'user_token=' . $this->session->data['user_token'], true),
		);

		$data['action'] = $this->url->link('extension/total/order_discount', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);

		if (isset($this->request->post['total_order_discount_status'])) {
			$data['status'] = $this->request->post['total_order_discount_status'];
		} else {
			$data['status'] = $this->config->get('total_order_discount_status');
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['total_order_discount'])) {
			$data['discount'] = $this->request->post['total_order_discount'];
		} else {
			$data['discount'] = $this->config->get('total_order_discount');
		}

		if (empty($data['discount']['sort_order'])) {
			$data['discount']['sort_order'] = $this->config->get('total_total_sort_order') - 1;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/total/order_discount', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/total/order_discount')) {
			$this->error['permission'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['total_order_discount']['title']) && is_array($this->request->post['total_order_discount']['title'])) {
			foreach ($this->request->post['total_order_discount']['title'] as $language_id => $discount_title) {
				if (utf8_strlen($discount_title) < 1 || utf8_strlen($discount_title) > 128) {
					$this->error['discount_title'] = $this->language->get('error_discount_title');
				}
			}
		} else {
			$this->error['discount_title'] = $this->language->get('error_discount_title');
		}

		return !$this->error;
	}
}
