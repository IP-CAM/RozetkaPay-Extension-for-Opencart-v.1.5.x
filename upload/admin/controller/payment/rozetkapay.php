<?php
include_once DIR_SYSTEM .'library/payment/RozetkaPay/autoloader.php';
class ControllerPaymentRozetkaPay extends Controller {
    
    protected $version = '1.1.5';
    
    private $type = 'payment';
    private $code = 'rozetkapay';
    private $path = 'payment/rozetkapay';    
    private $prefix = '';    
    private $token_name = 'token';
    
    
    private $type_code = '';
    
    private $error = array();
    
    private $token_value = '';
    private $tokenUrl = '';
    
    
    
    
    private $log_file = 'rozetkapay';    
    private $extLog;
    
    public function initMultiCMS() {
        
        $versionInt = (int)str_replace(".", "", VERSION);
        
        if($versionInt < 2000){//1.5
            
            $this->prefix = '';
            $this->token_name = 'token';
            
        }elseif($versionInt < 2100){//2.0
            
            $this->prefix = '';
            $this->token_name = 'token';
            
        }elseif($versionInt < 2200){//2.1
            
            $this->prefix = '';
            $this->token_name = 'token';
            
        }elseif($versionInt < 2200){//2.2
            
            $this->prefix = '';
            $this->token_name = 'token';
            
        }elseif($versionInt < 3000){//2.3
            
            $this->prefix = '';
            $this->token_name = 'token';
            $this->path = 'extension/payment/rozetkapay';    
            
        }elseif($versionInt < 4000){//3.0
            
            $this->prefix = 'payment_';
            $this->token_name = 'user_token';
            $this->path = 'extension/payment/rozetkapay';
            
        }
        
        
        $this->type_code = $this->type . "_" . $this->code;
        
        
    }
    
    public function __construct($registry) {
        parent::__construct($registry);
        
        $this->initMultiCMS();
        $this->load->language($this->path);
        $this->token_value = $this->session->data[$this->token_name];
        $this->tokenUrl = '&' . $this->token_name . '=' . $this->token_value;
        
        
    }

    public function index() {
        
        $langs_key = [
            'heading_title', 'text_edit','text_enabled','text_disabled','text_all_zones',
            'entry_total','entry_order_status','entry_geo_zone','entry_status','entry_sort_order',
            'help_total','button_save','button_cancel','tab_general','text_tab_order_status',
            'entry_login','error_login','entry_password','error_password','text_order_status_init',
            'text_order_status_pending','text_order_status_success','text_order_status_failure','text_tab_test',
            'text_tab_test','entry_log_status','text_test_cards','text_test_log_title','text_tab_test',
            'entry_holding_status', 'entry_qrcode_status','button_log_clear','button_log_download'
        ];
        
        $this->data = array_merge($this->data, $this->SysloadLanguage($langs_key));
        
        $this->document->setTitle($this->language->get('heading_title'));        

        $this->save();
        
        $arr = array('warning', 'login', 'password', 'order_status_success', 'order_status_failure');
        
        foreach ($arr as $v)
            $this->data['error_' . $v] = (isset($this->error[$v])) ? $this->error[$v] : false;
        
        $this->data['breadcrumbs'] = $this->breadcrumbs();

        $this->data['action'] = $this->SysUrl($this->path, $this->tokenUrl, true);
        $this->data['cancel'] = $this->SysUrl('extension/payment', $this->tokenUrl . '&type=payment', true);

        $this->load->model('localisation/order_status');
        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $arr = array("rozetkapay_login", "rozetkapay_password", "rozetkapay_status", 
            "rozetkapay_sort_order", "rozetkapay_geo_zone_id", "rozetkapay_holding_status",
            "rozetkapay_order_status_init","rozetkapay_order_status_pending","rozetkapay_qrcode_status",
            "rozetkapay_order_status_success","rozetkapay_order_status_failure","rozetkapay_test_status", "rozetkapay_log_status");
        
        foreach ($arr as $v) {
            $this->data[$this->prefix.$v] = (isset($this->request->post[$this->prefix.$v])) ? $this->request->post[$this->prefix.$v] : $this->config->get($this->prefix.$v);            
        }
        
        $this->load->model('localisation/geo_zone');
        $this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();        
        
        //==========================================================================================
        
        $this->data['href_log_download'] = $this->SysUrl($this->path . '/logdownload', $this->tokenUrl, true);
        $this->data['href_log_clear'] =  $this->SysUrl($this->path . '/logclear', $this->tokenUrl, true);
		$this->data['log'] = '';

		$file = DIR_LOGS . $this->log_file;

		if (file_exists($file)) {
			$size = filesize($file);

			if ($size >= 5242880) {
				$suffix = array(
					'B',
					'KB',
					'MB',
					'GB',
					'TB',
					'PB',
					'EB',
					'ZB',
					'YB'
				);

				$i = 0;

				while (($size / 1024) > 1) {
					$size = $size / 1024;
					$i++;
				}

				$this->data['error_warning'] = sprintf($this->language->get('error_warning'), basename($file), round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]);
			} else {
				$this->data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			}
		}
        
        //==========================================================================================
                
        $this->template = $this->path . '.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
        
    }
    
    private function validate() {
        if (!$this->user->hasPermission('modify', $this->path)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        if(isset($this->request->post['rozetkapay_status']) && $this->request->post[$this->prefix . 'rozetkapay_status'] != "1"){

            if(isset($this->request->post[$this->prefix . 'rozetkapay_login'])){
                $this->request->post['rozetkapay_login'] = trim($this->request->post[$this->prefix . 'rozetkapay_login']);
            }
            
            if(isset($this->request->post[$this->prefix . 'rozetkapay_password'])){
                $this->request->post['rozetkapay_password'] = trim($this->request->post[$this->prefix . 'rozetkapay_password']);
            }
            
            //======================================================================================
            
            if (empty($this->request->post[$this->prefix . 'rozetkapay_login'])) {
                $this->error['login'] = $this->language->get('error_login');
            }

            if (empty($this->request->post[$this->prefix . 'rozetkapay_password'])) {
                $this->error['password'] = $this->language->get('error_password');
            }

        }
        
        
        return  !$this->error;
    }
    
    private function save() {
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting($this->prefix . $this->code, $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->SysUrl($this->path, $this->tokenUrl, true));
        }
        
    }
    
    public function breadcrumbs() {
        
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->SysUrl('common/dashboard', $this->tokenUrl, true),
            'separator' => false
            
        );

        $breadcrumbs[] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->SysUrl('extension/payment', '&type=payment'. $this->tokenUrl, true),
            'separator' => "::"
        );

        $breadcrumbs[] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->SysUrl($this->path, $this->tokenUrl, true),
            'separator' => "::"
        );
        
        return $breadcrumbs;
        
    }
    
    public function logdownload() {
        
		$this->load->language('tool/log');

		$file = DIR_LOGS . $this->log_file;

		if (file_exists($file) && filesize($file) > 0) {
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename="' . $this->config->get('config_name') . '_' . date('Y-m-d_H-i-s', time()) . $this->log_file .'.log"');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->response->setOutput(file_get_contents($file, FILE_USE_INCLUDE_PATH, null));
		} else {
			$this->session->data['error'] = sprintf($this->language->get('error_warning'), basename($file), '0B');

			$this->response->redirect($this->SysUrl($this->path, $this->tokenUrl, true));
		}
	}
    
    
	public function logclear() {
        
		$this->load->language('tool/log');

        $file = DIR_LOGS . $this->log_file;

        $handle = fopen($file, 'w+');

        fclose($handle);

        $this->session->data['success'] = $this->language->get('text_success');

		$this->response->redirect($this->SysUrl($this->path, $this->tokenUrl, true));
        
	}
    
    
    public function order() { 
                
        $order_id = isset($this->request->get['order_id']) ?$this->request->get['order_id'] : false;
        
        $rpay = new \Payment\RozetkaPay\RozetkaPay();
        
        if($this->config->get($this->prefix . 'rozetkapay_test_status') === "1"){
            $rpay->setBasicAuthTest();
        }else{
            $rpay->setBasicAuth($this->config->get($this->prefix . 'rozetkapay_login'), 
                    $this->config->get($this->prefix . 'rozetkapay_password'));
        }
        
        $this->data['order_id'] = $order_id;        
        $this->data['token'] = $this->token_value;
        
        $this->load->model('sale/order');
        
        $order_info = $this->model_sale_order->getOrder($order_id);
        $this->data['total'] = $order_info['total'];                
        
        return $this->load->view($this->path . '_order', $this->data);
        
    }
    
    
    public function payRefund() {
        
        $json = [];
        
        $json['ok'] = false;
        $json['error'] = [];
        
        if(isset($this->request->post['order_id'])){
            $order_id = (int)$this->request->post['order_id'];
        }else{
            $json['error']['error_order_id'] = $this->language->get('text_payRefund_error_order_id');
        }
        
        if(isset($this->request->post['total'])){
            $total = (float)$this->request->post['total'];
        }else{
            $json['error']['error_total'] = $this->language->get('text_payRefund_error_total');
        }
        
        if($total <= 0){
            $json['error']['error_total'] = $this->language->get('text_payRefund_error_total');
        }
        
        if(empty($this->error)){
            
            $this->load->model('sale/order');
            
            $order_info = $this->model_sale_order->getOrder($order_id);

            $rpay = new Payment\RozetkaPay\RozetkaPay();

            if($this->config->get('payment_rozetkapay_test_status') === "1"){
                $rpay->setBasicAuthTest();
            }else{
                $rpay->setBasicAuth($this->config->get('payment_rozetkapay_login'), $this->config->get('payment_rozetkapay_password'));
            }

            $rpay->setCallbackURL($this->SysUrl($this->path.'/callback', 'order_id=' . $order_id . '&refund'));

            $this->dataPay = new Payment\RozetkaPay\Model\PaymentRequest();

            $this->dataPay->external_id = (string)$order_id;        
            $this->dataPay->amount = $total;   
            $this->dataPay->currency = $order_info['currency_code'];
            
            
            
            list($status, $error) = $rpay->paymentRefund($this->dataPay);
            
            if($error !== false){
                $json['error'][$error->code] = $error->message;
            }
            
            $json['ok'] = $status;
            
        
        }
        
        if($json['ok']){
            $json['alert'] = $this->language->get('text_success');
        }else{
            $json['alert'] = $this->language->get('text_failure');
        }
        
        $this->response->addHeader('Content-Type: application/json');        
        $this->response->setOutput(json_encode($json));
        
    }
    
    
    public function payInfo() {
        
        $json = [];
        
        $json['ok'] = false;
        $json['details'] = [];
        $json['error'] = [];
        
        if(isset($this->request->post['order_id'])){
            $order_id = (int)$this->request->post['order_id'];
        }else{
            $json['error']['error_order_id'] = $this->language->get('text_pay_error_order_id');
        }
        
        
        if(empty($this->error)){        
            
            $this->load->model('sale/order');
            
            $order_info = $this->model_sale_order->getOrder($order_id);

            $rpay = new Payment\RozetkaPay\RozetkaPay();

            if($this->config->get('payment_rozetkapay_test_status') === "1"){
                $rpay->setBasicAuthTest();
            }else{
                $rpay->setBasicAuth($this->config->get('payment_rozetkapay_login'), $this->config->get('payment_rozetkapay_password'));
            }

            $rpay->setCallbackURL($this->SysUrl($this->path.'/callback'));

            list($results, $json['error']) = $rpay->paymentInfo((string)$order_id);
            
            $details = [];
            if(empty($json['error'])){
                if(isset($results['purchase_details']) && !empty($results['purchase_details'])){
                    foreach ($results['purchase_details'] as $detail) {
                        $details[] = [
                            'amount' => $detail->amount,
                            'currency' => $detail->currency,
                            'status' => $detail->status,
                            'created_at' => (new DateTime($detail->created_at))->getTimestamp(),
                            'type' => 'purchase'
                        ];
                    }
                }

                if(isset($results['confirmation_details']) && !empty($results['purchase_details'])){
                    foreach ($results['confirmation_details'] as $detail) {
                        $details[] = [
                            'amount' => $detail->amount,
                            'currency' => $detail->currency,
                            'status' => $detail->status,
                            'created_at' => (new DateTime($detail->created_at))->getTimestamp(),
                            'type' => 'confirmation'
                        ];
                    }
                }

                if(isset($results['cancellation_details']) && !empty($results['purchase_details'])){
                    foreach ($results['cancellation_details'] as $detail) {
                        $details[] = [
                            'amount' => $detail->amount,
                            'currency' => $detail->currency,
                            'status' => $detail->status,
                            'created_at' => (new DateTime($detail->created_at))->getTimestamp(),
                            'type' => 'cancellation'
                        ];
                    }
                }

                if(isset($results['refund_details']) && !empty($results['purchase_details'])){
                    foreach ($results['refund_details'] as $detail) {
                        $details[] = [
                            'amount' => $detail->amount,
                            'currency' => $detail->currency,
                            'status' => $detail->status,
                            'created_at' => (new DateTime($detail->created_at))->getTimestamp(),
                            'type' => 'refund'
                        ];
                    }
                }
            }
            
            $sort_order = array();

            foreach ($details as $key => $value) {
                $sort_order[$key] = $value['created_at'];
            }

            array_multisort($sort_order, SORT_DESC, $details);
            
            $dat = new DateTime();
            foreach ($details as $key => $detail) {
                $details[$key]['created_at'] = $dat->setTimestamp($detail['created_at'])->format($this->language->get('datetime_format'));
            }
            
            $json['ok'] = true;
            $json['details'] = $details;            
            $json['alert'] = $this->language->get('text_success');
            
        
        }else{
            
            $json['alert'] = $this->language->get('text_error');
            
        }
        
        $json['debug'] = $rpay->debug;
        
        $this->response->addHeader('Content-Type: application/json');        
        $this->response->setOutput(json_encode($json));
        
    }
    
    
    public function SysloadLanguage($langs_key) {
        $results = [];
        foreach ($langs_key as $key) {
            $results[$key] = $this->language->get($key);
        }
        return $results;
    }
    
    public function SysUrl($route, $args = '', $secure = false) {
        
        return  str_replace("&amp;","&", $this->url->link($route, $args, "SSL"));
        
    }
    

}
