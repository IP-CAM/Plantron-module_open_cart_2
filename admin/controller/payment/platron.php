<?php
class ControllerPaymentPlatron extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('payment/platron');

		$this->document->setTitle = $this->language->get('heading_title');

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('platron', $this->request->post);
			//var_dump($this->session);die();
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect(HTTPS_SERVER . 'index.php?route=payment/platron&token=' . $this->session->data['token']);
		}

		$date['heading_title'] = $this->language->get('heading_title');

		$date['text_enabled'] = $this->language->get('text_enabled');
		$date['text_disabled'] = $this->language->get('text_disabled');
		$date['text_all_zones'] = $this->language->get('text_all_zones');
		$date['text_yes'] = $this->language->get('text_yes');
		$date['text_no'] = $this->language->get('text_no');
        $date['entry_donate_me'] = $this->language->get('entry_donate_me');
		// platron ENTER
        $date['entry_payment_name'] = $this->language->get('entry_payment_name');
		$date['entry_merchant_id'] = $this->language->get('entry_merchant_id');
		$date['entry_secret_word'] = $this->language->get('entry_secret_word');
        $date['entry_lifetime'] = $this->language->get('entry_lifetime');

		// URL
		$date['copy_result_url'] 	= HTTP_CATALOG . 'index.php?route=payment/platron/callback';
		$date['copy_success_url']	= HTTP_CATALOG . 'index.php?route=payment/platron/success';
		$date['copy_fail_url'] 	= HTTP_CATALOG . 'index.php?route=payment/platron/fail';

		// TEST MODE
		$date['entry_test'] = $this->language->get('entry_test');

		$date['entry_order_status'] = $this->language->get('entry_order_status');
		$date['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$date['entry_status'] = $this->language->get('entry_status');
		$date['entry_sort_order'] = $this->language->get('entry_sort_order');

		$date['button_save'] = $this->language->get('button_save');
		$date['button_cancel'] = $this->language->get('button_cancel');

		$date['tab_general'] = $this->language->get('tab_general');

		//
		if (isset($this->error['warning'])) {
			$date['error_warning'] = $this->error['warning'];
		} else {
			$date['error_warning'] = '';
		}


		//
        if (isset($this->error['payment_name'])) {
            $date['error_payment_name'] = $this->error['payment_name'];
        } else {
            $date['error_payment_name'] = '';
        }

		if (isset($this->error['merchant_id'])) {
			$date['error_merchant_id'] = $this->error['merchant_id'];
		} else {
			$date['error_merchant_id'] = '';
		}

		if (isset($this->error['secret_word'])) {
			$date['error_secret_word'] = $this->error['secret_word'];
		} else {
			$date['error_secret_word'] = '';
		}

		$date['breadcrumbs'] = array();

   		$date['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$date['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$date['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/platron', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);



        $date['action'] = $this->url->link('payment/platron', 'token=' . $this->session->data['token'], 'SSL');

		$date['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		//
        if (isset($this->request->post['platron_payment_name'])) {
            $date['platron_payment_name'] = $this->request->post['platron_payment_name'];
        } else {
            $date['platron_payment_name'] = $this->config->get('platron_payment_name');
        }
		//
		if (isset($this->request->post['platron_merchant_id'])) {
			$date['platron_merchant_id'] = $this->request->post['platron_merchant_id'];
		} else {
			$date['platron_merchant_id'] = $this->config->get('platron_merchant_id');
		}


		//
		if (isset($this->request->post['platron_secret_word'])) {
			$date['platron_secret_word'] = $this->request->post['platron_secret_word'];
		} else {
			$date['platron_secret_word'] = $this->config->get('platron_secret_word');
		}

        if (isset($this->request->post['platron_test'])) {
            $date['platron_test'] = $this->request->post['platron_test'];
        } else {
            $date['platron_test'] = $this->config->get('platron_test');
        }

        if (isset($this->request->post['platron_lifetime'])) {
            $date['platron_lifetime'] = $this->request->post['platron_lifetime'];
        } else {
            $date['platron_lifetime'] = $this->config->get('platron_lifetime');
        }

		if (isset($this->request->post['platron_order_status_id'])) {
			$date['platron_order_status_id'] = $this->request->post['platron_order_status_id'];
		} else {
			$date['platron_order_status_id'] = $this->config->get('platron_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$date['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['platron_geo_zone_id'])) {
			$date['platron_geo_zone_id'] = $this->request->post['platron_geo_zone_id'];
		} else {
			$date['platron_geo_zone_id'] = $this->config->get('platron_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$date['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['platron_status'])) {
			$date['platron_status'] = $this->request->post['platron_status'];
		} else {
			$date['platron_status'] = $this->config->get('platron_status');
		}

		if (isset($this->request->post['platron_sort_order'])) {
			$date['platron_sort_order'] = $this->request->post['platron_sort_order'];
		} else {
			$date['platron_sort_order'] = $this->config->get('platron_sort_order');
		}

		$this->template = 'payment/platron.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$date['header'] = $this->load->controller('common/header');
		$date['column_left'] = $this->load->controller('common/column_left');
		$date['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->template, $date));
		//$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/platron')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        if (!$this->request->post['platron_payment_name']) {
            $this->error['payment_name'] = $this->language->get('error_payment_name');
        }

		if (!$this->request->post['platron_merchant_id']) {
			$this->error['merchant_id'] = $this->language->get('error_merchant_id');
		}

		if (!$this->request->post['platron_secret_word']) {
			$this->error['secret_word'] = $this->language->get('error_secret_word');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}