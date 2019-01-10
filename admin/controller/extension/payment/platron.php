<?php
class ControllerExtensionPaymentPlatron extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('extension/payment/platron');

		$this->document->setTitle = $this->language->get('heading_title');

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('platron', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_lifetime_tooltip'] = $this->language->get('text_lifetime_tooltip');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
        $data['entry_donate_me'] = $this->language->get('entry_donate_me');
		// platron ENTER
        $data['entry_payment_name'] = $this->language->get('entry_payment_name');
		$data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
		$data['entry_secret_word'] = $this->language->get('entry_secret_word');
        $data['entry_lifetime'] = $this->language->get('entry_lifetime');
        $data['entry_ofd_send_receipt'] = $this->language->get('entry_ofd_send_receipt');
        $data['entry_ofd_vat'] = $this->language->get('entry_ofd_vat');

		// URL
		$data['copy_result_url'] 	= $this->url->link('extension/payment/platron/callback');
		$data['copy_success_url'] 	= $this->url->link('extension/payment/platron/success');
		$data['copy_fail_url'] 	= $this->url->link('extension/payment/platron/fail');

		// TEST MODE
		$data['entry_test'] = $this->language->get('entry_test');

		$data['ofd_vat_types'] = array(
			'none' => $this->language->get('entry_ofd_vat_none'),
			'0' => $this->language->get('entry_ofd_vat_0'),
			'10' => $this->language->get('entry_ofd_vat_10'),
			'20' => $this->language->get('entry_ofd_vat_20'),
			'110' => $this->language->get('entry_ofd_vat_110'),
			'120' => $this->language->get('entry_ofd_vat_120'),
		);

		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_order_status_fail'] = $this->language->get('entry_order_status_fail');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

		//
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}


		//
        if (isset($this->error['payment_name'])) {
            $data['error_payment_name'] = $this->error['payment_name'];
        } else {
            $data['error_payment_name'] = '';
        }

		if (isset($this->error['merchant_id'])) {
			$data['error_merchant_id'] = $this->error['merchant_id'];
		} else {
			$data['error_merchant_id'] = '';
		}

		if (isset($this->error['secret_word'])) {
			$data['error_secret_word'] = $this->error['secret_word'];
		} else {
			$data['error_secret_word'] = '';
		}

		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/platron', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);



        $data['action'] = $this->url->link('extension/payment/platron', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		//
        if (isset($this->request->post['platron_payment_name'])) {
            $data['platron_payment_name'] = $this->request->post['platron_payment_name'];
        } else {
            $data['platron_payment_name'] = $this->config->get('platron_payment_name');
        }
		//
		if (isset($this->request->post['platron_merchant_id'])) {
			$data['platron_merchant_id'] = $this->request->post['platron_merchant_id'];
		} else {
			$data['platron_merchant_id'] = $this->config->get('platron_merchant_id');
		}


		//
		if (isset($this->request->post['platron_secret_word'])) {
			$data['platron_secret_word'] = $this->request->post['platron_secret_word'];
		} else {
			$data['platron_secret_word'] = $this->config->get('platron_secret_word');
		}

        if (isset($this->request->post['platron_test'])) {
            $data['platron_test'] = $this->request->post['platron_test'];
        } else {
            $data['platron_test'] = $this->config->get('platron_test');
        }

        if (isset($this->request->post['platron_lifetime'])) {
            $data['platron_lifetime'] = $this->request->post['platron_lifetime'];
        } else {
            $data['platron_lifetime'] = $this->config->get('platron_lifetime');
        }

		if (isset($this->request->post['platron_order_status_id'])) {
			$data['platron_order_status_id'] = $this->request->post['platron_order_status_id'];
		} else {
			$data['platron_order_status_id'] = $this->config->get('platron_order_status_id');
		}
		
		if (isset($this->request->post['platron_order_status_id_fail'])) {
			$data['platron_order_status_id_fail'] = $this->request->post['platron_order_status_id_fail'];
		} else {
			$data['platron_order_status_id_fail'] = $this->config->get('platron_order_status_id_fail');
		}

		if (isset($this->request->post['platron_ofd_send_receipt'])) {
			$data['platron_ofd_send_receipt'] = $this->request->post['platron_ofd_send_receipt'];
		} else {
			$data['platron_ofd_send_receipt'] = $this->config->get('platron_ofd_send_receipt');
		}
		if (isset($this->request->post['platron_ofd_vat'])) {
			$data['platron_ofd_vat'] = $this->request->post['platron_ofd_vat'];
		} else {
			$data['platron_ofd_vat'] = $this->config->get('platron_ofd_vat');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$data['order_statuses_fail'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['platron_geo_zone_id'])) {
			$data['platron_geo_zone_id'] = $this->request->post['platron_geo_zone_id'];
		} else {
			$data['platron_geo_zone_id'] = $this->config->get('platron_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['platron_status'])) {
			$data['platron_status'] = $this->request->post['platron_status'];
		} else {
			$data['platron_status'] = $this->config->get('platron_status');
		}

		if (isset($this->request->post['platron_sort_order'])) {
			$data['platron_sort_order'] = $this->request->post['platron_sort_order'];
		} else {
			$data['platron_sort_order'] = $this->config->get('platron_sort_order');
		}

		$this->template = 'extension/payment/platron.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->template, $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/platron')) {
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
