<?php
class ControllerExtensionPaymentSnapio extends Controller {

  private $error = array();

  public function index() {
    $this->load->language('extension/payment/snapio');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('setting/setting');
    $this->load->model('localisation/order_status');
	  $this->config->get('curency');


    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->model_setting_setting->editSetting('payment_snapio', $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
    }

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->error['display_name'])) {
      $data['error_display_name'] = $this->error['display_name'];
    } else {
      $data['error_display_name'] = '';
    }
    
    if (isset($this->error['merchant_id'])) {
      $data['error_merchant_id'] = $this->error['merchant_id'];
    } else {
      $data['error_merchant_id'] = '';
    }
    
    if (isset($this->error['server_key'])) {
      $data['error_server_key'] = $this->error['server_key'];
    } else {
      $data['error_server_key'] = '';
    }

    if (isset($this->error['client_key'])) {
      $data['error_client_key'] = $this->error['client_key'];
    } else {
      $data['error_client_key'] = '';
    }

    if (isset($this->error['min_txn'])) {
      $data['error_min_txn'] = $this->error['min_txn'];
    } else {
      $data['error_min_txn'] = '';
    }

    if (isset($this->error['currency_conversion'])) {
      $data['error_currency_conversion'] = $this->error['currency_conversion'];
    } else {
      $data['error_currency_conversion'] = '';
    }


    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_extension'),
      'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('extension/payment/snapio', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['action'] = $this->url->link('extension/payment/snapio', 'user_token=' . $this->session->data['user_token'], true);

    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=payment', true);

    $inputs = array(
      'payment_snapio_status',
      'payment_snapio_display_name',
      'payment_snapio_environment',
      'payment_snapio_merchant_id',
      'payment_snapio_server_key',
      'payment_snapio_client_key',
      'payment_snapio_geo_zone_id',
      'payment_snapio_sort_order',
      'payment_snapio_min_txn',
      'payment_snapio_acq_bank',
      'payment_snapio_installment_term',
      'payment_snapio_number',
      'payment_snapio_custom_field1',
      'payment_snapio_custom_field2',
      'payment_snapio_custom_field3',
      'payment_snapio_mixpanel',
      'payment_snapio_redirect'
    );

    foreach ($inputs as $input) {
      if (isset($this->request->post[$input])) {
        $data[$input] = $this->request->post[$input];
      } else {
        $data[$input] = $this->config->get($input);
      }
    }

    if (isset($this->request->post['payment_snapio_status_success'])) {
      $data['payment_snapio_status_success'] = $this->request->post['payment_snapio_status_success'];
    } elseif ($this->config->get('payment_snapio_status_success')) {
      $data['payment_snapio_status_success'] = $this->config->get('payment_snapio_status_success');
    } else {
      $data['payment_snapio_status_success'] = '2';
    }

    if (isset($this->request->post['payment_snapio_status_pending'])) {
      $data['payment_snapio_status_pending'] = $this->request->post['payment_snapio_status_pending'];
    } elseif ($this->config->get('payment_snapio_status_pending')) {
      $data['payment_snapio_status_pending'] = $this->config->get('payment_snapio_status_pending');
    } else {
      $data['payment_snapio_status_pending'] = '1';
    }

    if (isset($this->request->post['payment_snapio_status_failure'])) {
      $data['payment_snapio_status_failure'] = $this->request->post['payment_snapio_status_failure'];
    } elseif ($this->config->get('payment_snapio_status_failure')) {
      $data['payment_snapio_status_failure'] = $this->config->get('payment_snapio_status_failure');
    } else {
      $data['payment_snapio_status_failure'] = '7';
    }

    if (isset($this->request->post['payment_snapio_currency_conversion'])) {
      $data['payment_snapio_currency_conversion'] = $this->request->post['payment_snapio_currency_conversion'];
    } elseif ($this->config->get('payment_snapio_currency_conversion')) {
      $data['payment_snapio_currency_conversion'] = $this->config->get('payment_snapio_currency_conversion');
    } else {
      $data['payment_snapio_currency_conversion'] = 1;
    }

    $this->load->model('localisation/order_status');

    $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

    $this->load->model('localisation/geo_zone');

    $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
    
    // $this->template = 'exension/payment/snap.tpl';
  	$data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
  	$data['footer'] = $this->load->controller('common/footer');
  	
	
	if(!$this->currency->has('IDR'))
	{
		$data['curr'] = true;
	}
	else
	{
		$data['curr'] = false;
	}

	$this->response->setOutput($this->load->view('extension/payment/snapio',$data));
	
  }

  protected function validate() {

    if (!$this->user->hasPermission('modify', 'extension/payment/snapio')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    // check for empty values
    if (!$this->request->post['payment_snapio_display_name']) {
      $this->error['display_name'] = $this->language->get('error_display_name');
    }
    
      // default values
    if (!$this->request->post['payment_snapio_environment'])
      $this->request->post['snapinst_environment'] = 1;

      // check for empty values
    if (!$this->request->post['payment_snapio_merchant_id']) {
       $this->error['merchant_id'] = $this->language->get('error_merchant_id');
    }

    if (!$this->request->post['payment_snapio_client_key']) {
       $this->error['client_key'] = $this->language->get('error_client_key');
    }

    if (!$this->request->post['payment_snapio_server_key']) {
      $this->error['server_key'] = $this->language->get('error_server_key');
    }

    if (!$this->request->post['payment_snapio_min_txn']) {
      $this->error['min_txn'] = $this->language->get('error_min_txn');
    }
    // currency conversion to IDR
    if (!$this->request->post['payment_snapio_currency_conversion'])
      $this->error['currency_conversion'] = $this->language->get('error_currency_conversion');

    return !$this->error;
  }
}
?>