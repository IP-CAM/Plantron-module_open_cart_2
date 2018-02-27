<?php

require_once DIR_SYSTEM . '/library/platron/PlatronSignature.php';
require_once DIR_SYSTEM . '/library/platron/ofd.php';

class ControllerExtensionPaymentPlatron extends Controller {
	public function index() {
		$this->language->load('payment/platron');

		$data['button_continue'] = $this->language->get('button_confirm');

		$data['continue'] = $this->url->link('extension/payment/platron/confirm', '', true);

		return $this->load->view('extension/payment/platron', $data);
	}

	public function confirm() {
		$this->language->load('extension/payment/platron');
		$this->load->model('account/order');
		$order_products = $this->model_account_order->getOrderProducts($this->session->data['order_id']);

		$product_descriptions = array();
		$ofdReceiptItems = array();
		foreach ($order_products as $product) {
			$product_descriptions[] = @$product["name"] . " " . @$product["model"] . " * " . @$product["quantity"];

			$ofdReceiptItem = new OfdReceiptItem();
			$ofdReceiptItem->label = substr(@$product["name"], 0, 128);
			$ofdReceiptItem->amount = @$product["total"] + @$product["tax"] * @$product["quantity"];
			$ofdReceiptItem->price = @$product["price"] + @$product["tax"];
			$ofdReceiptItem->quantity = @$product["quantity"];
			$ofdReceiptItem->vat = $this->config->get('platron_ofd_vat');
			$ofdReceiptItems[] = $ofdReceiptItem;
		}

		if (isset($this->session->data['shipping_method']['cost']) && $this->session->data['shipping_method']['cost'] > 0) {
			$ofdReceiptItem = new OfdReceiptItem();
			$ofdReceiptItem->label = 'Доставка';
			$ofdReceiptItem->amount = $this->session->data['shipping_method']['cost'];
			$ofdReceiptItem->price = $this->session->data['shipping_method']['cost'];
			$ofdReceiptItem->quantity = 1;
			$ofdReceiptItem->vat = $this->config->get('platron_ofd_vat') == 'none' ? 'none' : 18;
			$ofdReceiptItems[] = $ofdReceiptItem;
		}

		$order_description = implode(';', $product_descriptions);

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$merchant_id = $this->config->get('platron_merchant_id');
		$secret_word = $this->config->get('platron_secret_word');
		$lifetime = $this->config->get('platron_lifetime');

		$arrReq = array(
			'pg_amount' => (int) $order_info['total'],
			'pg_check_url' => $this->url->link('extension/payment/platron/check'),
			'pg_description' => $order_description,
			'pg_encoding' => 'UTF-8',
			'pg_currency' => $order_info['currency_code'],
			//'pg_user_ip' => $_SERVER['REMOTE_ADDR'],
			'pg_lifetime' => !empty($lifetime) ? $lifetime * 3600 : 86400,
			'pg_merchant_id' => $merchant_id,
			'pg_order_id' => $order_info['order_id'],
			'pg_result_url' => $this->url->link('extension/payment/platron/callback'),
			'pg_request_method' => 'POST',
			'pg_salt' => rand(21, 43433),
			'pg_success_url' => $this->url->link('checkout/success'),
			'pg_failure_url' => $this->url->link('checkout/failure'),
			'pg_user_phone' => $order_info['telephone'],
			'pg_user_contact_email' => $order_info['email'],
			'cms_payment_module' => 'OPENCART',
		);

		if ($this->config->get('platron_test') == 1) {
			$arrReq['pg_testing_mode'] = 1;
		}

		$this->load->model('extension/payment/platron');
		$arrReq['pg_sig'] = $this->model_extension_payment_platron->make('init_payment.php', $arrReq, $secret_word);

		$this->load->library('platron/platronClient');
		$init_payment_response = $this->platronClient->doRequest('https://www.platron.ru/init_payment.php', $arrReq);

		if (!$init_payment_response) {
			$this->session->data['error'] = $this->language->get('payment_failed');
			$this->response->redirect($this->url->link('checkout/checkout', '', true));
		}

		if (!PlatronSignature::checkXML('init_payment.php', $init_payment_response, $secret_word)) {
			$this->session->data['error'] = $this->language->get('payment_failed');
			$this->log->write('Platron init_payment response invalid signature');
			$this->response->redirect($this->url->link('checkout/checkout', '', true));
		}

		if ($init_payment_response->pg_status == 'error') {
			$this->session->data['error'] = $this->language->get('payment_failed');
			$this->log->write('Platron init_payment error: ' . $init_payment_response->pg_error_description);
			$this->response->redirect($this->url->link('checkout/checkout', '', true));
		}

		if ($this->config->get('platron_ofd_send_receipt') == 1) {
			$ofdReceiptRequest = new OfdReceiptRequest($merchant_id, $init_payment_response->pg_payment_id);
			$ofdReceiptRequest->items = $ofdReceiptItems;
			$ofdReceiptRequest->prepare();
			$ofdReceiptRequest->sign($secret_word);

			$receipt_response = $this->platronClient->doRequest('https://www.platron.ru/receipt.php', array('pg_xml' => $ofdReceiptRequest->asXml()));

			if (!$receipt_response) {
				$this->session->data['error'] = $this->language->get('payment_failed');
				$this->response->redirect($this->url->link('checkout/checkout', '', true));
			}

			if (!PlatronSignature::checkXML('receipt.php', $receipt_response, $secret_word)) {
				$this->session->data['error'] = $this->language->get('payment_failed');
				$this->log->write('Platron receipt response signature');
				$this->response->redirect($this->url->link('checkout/checkout', '', true));
			}

			if ($receipt_response->pg_status == 'error') {
				$this->session->data['error'] = $this->language->get('payment_failed');
				$this->log->write('Platron receipt error: ' . $receipt_response->pg_error_description);
				$this->response->redirect($this->url->link('checkout/checkout', '', true));
			}
		}

		if ($init_payment_response->pg_status == 'ok') {
			$this->response->redirect($init_payment_response->pg_redirect_url);
		}
	}

	public function check() {

		$this->language->load('payment/platron');
		$this->load->model('checkout/order');
		$this->load->model('extension/payment/platron');

		$arrResponse = array();

		if (!empty($this->request->post))
			$data = $this->request->post;
		else
			$data = $this->request->get;

		$pg_sig = !empty($data['pg_sig']) ? $data['pg_sig'] : '';
		unset($data['pg_sig']);

		$secret_word = $this->config->get('platron_secret_word');

		if (!$this->model_extension_payment_platron->checkSig($pg_sig, 'index.php', $data, $secret_word)) {
			die('Incorrect signature!');
		}

		// Получаем информацию о заказе
		$order_id = $data['pg_order_id'];
		$order_info = $this->model_checkout_order->getOrder($order_id);

		$arrResponse['pg_salt'] = $data['pg_salt'];

		if (isset($order_info['order_id'])) {
			$arrResponse['pg_status'] = 'ok';
			$arrResponse['pg_description'] = '';
		} else {
			$arrResponse['pg_status'] = 'rejected';
			$arrResponse['pg_description'] = $this->language->get('err_order_not_found');
		}

		$arrResponse['pg_sig'] = $this->model_extension_payment_platron->make('index.php', $arrResponse, $secret_word);

		header('Content-type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
		echo "<response>\r\n";
		echo "<pg_salt>" . $arrResponse['pg_salt'] . "</pg_salt>\r\n";
		echo "<pg_status>" . $arrResponse['pg_status'] . "</pg_status>\r\n";
		echo "<pg_description>" . htmlentities($arrResponse['pg_description']) . "</pg_description>\r\n";
		echo "<pg_sig>" . $arrResponse['pg_sig'] . "</pg_sig>\r\n";
		echo "</response>";
	}

	public function callback() {

		$this->language->load('payment/platron');
		$this->load->model('extension/payment/platron');
		$this->load->model('checkout/order');

		$arrResponse = array();

		if (!empty($this->request->post))
			$data = $this->request->post;
		else
			$data = $this->request->get;

		$pg_sig = $data['pg_sig'];
		unset($data['pg_sig']);

		$secret_word = $this->config->get('platron_secret_word');

		if (!$this->model_extension_payment_platron->checkSig($pg_sig, 'index.php', $data, $secret_word)) {
			die('Incorrect signature!');
		}

		// Получаем информацию о заказе
		$order_id = $data['pg_order_id'];
		$order_info = $this->model_checkout_order->getOrder($order_id);

		$arrResponse['pg_salt'] = $data['pg_salt'];

		if ($data['pg_result'] != 1) {
			$arrResponse['pg_status'] = 'failed';
			$arrResponse['pg_error_description'] = '';
		} elseif (isset($order_info['order_id'])) {
			$arrResponse['pg_status'] = 'ok';
			$arrResponse['pg_error_description'] = '';
		} else {
			$arrResponse['pg_status'] = 'rejected';
			$arrResponse['pg_error_description'] = $this->language->get('err_order_not_found');
		}

		$arrResponse['pg_sig'] = $this->model_extension_payment_platron->make('index.php', $arrResponse, $secret_word);

		header('Content-type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
		echo "<response>\r\n";
		echo "<pg_salt>" . $arrResponse['pg_salt'] . "</pg_salt>\r\n";
		echo "<pg_status>" . $arrResponse['pg_status'] . "</pg_status>\r\n";
		echo "<pg_error_description>" . htmlentities($arrResponse['pg_error_description']) . "</pg_error_description>\r\n";
		echo "<pg_sig>" . $arrResponse['pg_sig'] . "</pg_sig>\r\n";
		echo "</response>\r\n";

		$order_info['customer_group_id'] = $this->customer->getGroupId();
		if ($arrResponse['pg_status'] == 'ok') {

			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('platron_order_status_id'), 'Platron', TRUE);
		}
		if ($arrResponse['pg_status'] == 'failed') {

			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('platron_order_status_id_fail'), 'Platron', TRUE);
		}
	}

}
