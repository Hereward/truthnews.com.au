<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class donate_controller extends Base_Controller {
    protected $EE;
    public $country_code;

    public function __construct($args='') {
        //die('boo');

        parent::__construct($args);
        $this->EE->load->model('eway_model');

        //$this->member_id = $this->EE->session->userdata('member_id');
    }
    
    public function init() {
      //$this->EE->subscribers_model->set_subscription_types();
      $this->country_code = 'AU'; //$this->EE->tna_commerce_lib->ip2location('countryCode');
    }


    public function index() {   
        $this->init();    
        if ($this->EE->input->post('submit_email')) {
            return $this->create();
        } elseif ($this->EE->input->post('submit_payment')) {
            return $this->store();
        } else {
            return $this->donate_landing();
        }
    }
    
    public function donate_landing() {
        dev_log::write("donate_controller:donate_landing");
        $vars = array();
        return $this->EE->load->view('donate_landing', $vars, TRUE);
    }
    

    public function create() {
        redirect($this->https_site_url);
        die();
        dev_log::write("donate_controller:create");
        $email = $this->EE->input->post('email');
         if (!$email) {
            redirect($this->https_site_url . "donate");
            die();
        }
        $errors = array();
        $countrylist = $this->EE->tna_commerce_lib->get_countrylist();
        $vars = array(
            'site_url'=>$this->site_url, 
            'errors'=>$errors, 
            'countrylist' => $countrylist,
            'countrycode' => $this->country_code
        );
        
        //var_dump()
        $this->set_defaults();
        return $this->EE->load->view('donate_card', $vars, TRUE);
    }
    
    
    function set_defaults() {
        $years = array('2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024');
        $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        $this->EE->load->vars(array('months' => $months, 'years' => $years));


        $form_default_fields = array(
            'CardHoldersName',
            'cc_number',
            'cc_expiry_month',
            'cc_expiry_year',
            'cc_cvn',
            'aud_price',
            'email'
        );

        $form_defaults = array();

        foreach ($form_default_fields as $field) {
            $form_defaults[$field] = $this->get_option($field);
        }
        
        if (!$this->EE->input->post('aud_price')) {
             $form_defaults['aud_price'] = 2000;
        }
        
        $this->EE->load->vars($form_defaults);
    }


    public function submit_email() {
        
    }
    
    public function store() {
        redirect($this->https_site_url);
        die();
       //$generic_error = 'The transaction failed. Please check your credit card details and try again.';
        dev_log::write("donate_controller:store");
        $eway_auth_code = '';
        
        $email = $this->EE->input->post('email');
        
        if (!$email) {
            redirect($this->https_site_url . "donate");
            die();
        }
        
        $block = $this->EE->transactions_model->throttle_check();
        
        if ($block) {
             $vars = array(
                'email' => $this->EE->input->post('email'),
                'cc_name' => $this->EE->input->post('CardHoldersName')
            );
            $this->EE->tna_commerce_lib->send_fraud_warning($vars);
            redirect($this->https_site_url . "subscribe/fraud_warning");
            die();
        }
        
        $eway_init = $this->EE->eway_model->init();
        if (!$eway_init) {
            $vars = $this->process_eway_error('init');
            return $this->EE->load->view('donate_card', $vars, TRUE);
            exit();
        }
        
        $errors = array();
        $params = array();
        
        $payment_good = $this->EE->eway_model->process_donation();
        
        if ($payment_good) {
            $eway_auth_code = $this->EE->eway_model->eway_auth_code;
        }

        //dev_log::write("payment_controller: [$payment_good]");
        $ip_address = $this->EE->tna_commerce_lib->get_client_ip();
        
        $t_params = array(
            'success' => ($payment_good)?1:0,
            'eway_auth_code' => $eway_auth_code,
            'ip_address' => $ip_address,
            'email' => $email
        );
        
        $this->EE->transactions_model->create_transaction_data($t_params);

       
        if ($payment_good) {
            //$eway_auth_code = $this->EE->eway_model->eway_auth_code;

            redirect($this->https_site_url . "donate/success");
        } else {
            $vars = $this->process_eway_error('payment');
            //$vars['subscription_details'] = $this->subscription_details;
            return $this->EE->load->view('donate_card', $vars, TRUE);
        }
    }
    
    public function process_eway_error($type = '') {
        if (!$type) {
            $type = 'payment';
        }

        $errors = array();
        $vars = array();
        $this->set_defaults();
        $error_str = $this->EE->eway_model->eway_error;
        dev_log::write("eWay Error: $error_str");

        $msg = $this->EE->eway_model->error_str[$type];

        $errors[] = $msg;
        $vars['member_id'] = $this->member_id;
        $vars['errors'] = $errors;
       
        return $vars;
    }
    
    public function donate_success() {
        dev_log::write('donate_success: 1');
        return $this->EE->load->view('donate_success', $vars, TRUE); 
    }

    public function show() {

    }

    public function edit() {

    }

    public function update() {

    }

    public function destroy() {

    }









}
