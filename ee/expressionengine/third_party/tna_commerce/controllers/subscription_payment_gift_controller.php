<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class subscription_payment_gift_controller extends Base_Controller {

    protected $EE;
    public $subscribe_stage;
    public $subscription_details;
    public $country_list;
    public $country_code;
    public $rebill_details;
    public $payment_method = 1;

    public function __construct($args = '') {
        //die('boo');

        parent::__construct($args);
        $this->EE->load->model('eway_model');

        //$this->member_id = $this->EE->session->userdata('member_id');
    }


    
    public function index() {
      
        $this->init();
        
        if ($this->EE->input->post('submit_payment')) {
            return $this->store();
        } else {
            return $this->create();
        }
    }

    public function init() {
        dev_log::write("init: 1");

        $this->gift = 1;
        $subscription_type = $this->EE->input->post('subscription_type');
        $this->subscription_details = $this->EE->subscribers_model->get_subscription_details($subscription_type);

        dev_log::write("init: 2");
        $this->country_list = $this->EE->tna_commerce_lib->get_countrylist();
        dev_log::write("init: 3");
        $this->country_code = 'AU'; //$this->EE->tna_commerce_lib->ip2location('countryCode');
        dev_log::write("init: 4");
    }

    public function delete_cookie() {
        setcookie('tna_subscribe_result_1', '', time() - 3600);
        setcookie('tna_subscribe_result_2', '', time() - 3600);
    }

    public function get_cookie() {
        $cookie = (isset($_COOKIE['tna_subscribe_result_1'])) ? $_COOKIE['tna_subscribe_result_1'] : '';

        if ($cookie) {
            $password_key = $_COOKIE['tna_subscribe_result_2'];
            $this->EE->tna_commerce_lib->set_password_key($password_key);
            $member_id = $this->EE->tna_commerce_lib->decrypt($cookie);
            return $member_id;
        } else {
            return '';
        }
    }

    public function subscribe_success() {
        dev_log::write('GIFT:subscribe_success: 1');
        //$this->member_id = $this->EE->uri->segment(3, 0);
        $cookie = $this->get_cookie();

        if ($cookie) {
            $this->member_id = $cookie;
            $this->delete_cookie();

            dev_log::write("decrypted cookie = [$this->member_id]");
            $auth_code = $this->EE->uri->segment(3, 0);
            $this->subscriber = $this->EE->subscribers_model->get_subscriber($this->member_id);
            

            $subscriber_gift_details = $this->EE->subscribers_model->subscriber_gift_details($this->member_id);

            $countrylist = $this->EE->tna_commerce_lib->get_countrylist();

            dev_log::write('subscribe_success: 2');

            //$vars['logged_in'] = $this->logged_in;
            // die("weee!");

            $vars = array(
                'subscriber' => $this->subscriber,
                'subscriber_gift_details' => $subscriber_gift_details,
                'countrylist' => $countrylist,
                'gateway_mode' => $this->EE->config->item('gateway_mode'),
            );

            $this->EE->tna_commerce_lib->send_gift_subscription_confirmation($vars);

            //dev_log::write('Subscription confirmation email sent.');

            return $this->EE->load->view('subscribe_gift_success', $vars, TRUE);
        } else {
            redirect($this->https_site_url . "subscribe");
        }
    }

   
    
     public function create() {
         redirect($this->https_site_url);
         die();
        dev_log::write("payment_gift_controller:create");
        $existing_subscriber = '';
        $errors = array();

        $vars = array();
        $this->subscribe_stage = 2;

        $this->member_id = $this->EE->input->post('member_id');
        $this->subscription_in_progress = true;

        $email = $this->EE->input->post('r_email');

        $duplicate = $this->EE->subscribers_model->find_duplicate($email);

        if ($email == 'editor@truthnews.com.au' && $duplicate) {
            dev_log::write("NUKING $email");
            $this->EE->subscribers_model->nuke_subscriber($duplicate->member_id);
            $existing_subscriber = false;
        } else {
            $existing_subscriber = ($duplicate) ? $this->EE->subscribers_model->is_subscriber($duplicate->member_id) : false;
        }

        if ($existing_subscriber) {
            dev_log::write("payment:create existing_subscriber > GO BACK");

            $errors[] = "The email address: [$duplicate->email] is already registered to a subscriber account. Please supply a different email adddress.";

            $vars['errors'] = $errors;
            $vars['member_id'] = $this->member_id;

            $view = 'subscribe_gift';
            
            $countrylist = $this->EE->tna_commerce_lib->get_countrylist();
            $vars['countrylist'] = $countrylist;
            $vars['countrycode']  = $this->country_code;
            
            //dev_log::write("load $view");
            $this->EE->subscribers_model->set_subscription_types();
            $this->subscribe_stage = 1;
            $this->set_defaults();

            return $this->EE->load->view($view, $vars, TRUE);
            exit();
        }

        $this->set_defaults();

        $vars['countrycode'] = $this->country_code;

        $vars['countrylist'] = $this->country_list;
        $vars['member_id'] = $this->member_id;
        $vars['subscription_details'] = $this->subscription_details;
        $vars['errors'] = $errors;

        return $this->EE->load->view('subscribe_payment_card_gift', $vars, TRUE);
    }
    

    public function delete_subscriber($member_id) {
        $output = true;
        //dev_log::write("delete_subscriber: 1");
        $customer = $this->EE->subscribers_model->eway_customer($member_id);
        //dev_log::write("delete_subscriber: 2");
        $this->EE->eway_model->delete_customer($customer->customer_id, $customer->rebill_id);
        //dev_log::write("delete_subscriber: 3");
        $this->EE->subscribers_model->create_cancelled_subscriber($member_id);
        $this->EE->subscribers_model->delete_subscriber($member_id);
        $this->EE->subscribers_model->update_subscriber_group($member_id, $this->member_group_id);

        // dev_log::write("delete_subscriber: 4");
        if ($this->EE->eway_model->eway_error) {
            $output = false;
        }

        return $output;
    }


    public function store() {
        redirect($this->https_site_url);
        die();
        dev_log::write("subscription_payment_gift_controller:store");
        //$generic_error = 'The transaction failed. Please check your credit card details and try again.';
        $eway_auth_code = '';
        $eway_init = $this->EE->eway_model->init();
        if (!$eway_init) {
            $vars = $this->process_eway_error('init');
            return $this->EE->load->view('subscribe_payment_card', $vars, TRUE);
            exit();
        }


        $RebillCustomerID = '';
        $RebillID = '';
        $this->subscribe_stage = 2;
        $errors = array();
        //$member_id = $this->EE->input->post('member_id');
        $params = array();

        $sd = print_r($this->subscription_details, true);
        dev_log::write("sd = $sd");
        
        $block = $this->EE->transactions_model->throttle_check();
        
        if ($block) {
             $vars = array(
                'email' => $this->EE->input->post('email'),
                'cc_name' => $this->EE->input->post('first_name') . ' ' . $this->EE->input->post('last_name')
            );
            $this->EE->tna_commerce_lib->send_fraud_warning($vars);
            redirect($this->https_site_url . "subscribe/fraud_warning");
            die();
        }
        
        $payment_good = $this->EE->eway_model->process_direct_payment($this->subscription_details);
        
        if ($payment_good) {
            $eway_auth_code = $this->EE->eway_model->eway_auth_code;
        }

        //$eway_auth_code = '';

        //dev_log::write("payment_controller: [$payment_good]");
        $ip_address = $this->EE->tna_commerce_lib->get_client_ip();
        
        $t_params = array(
            'success' => ($payment_good)?1:0,
            'eway_auth_code' => $eway_auth_code,
            'ip_address' => $ip_address,
            'email' => $this->EE->input->post('email')
        );
        
        $this->EE->transactions_model->create_transaction_data($t_params);
        
        

        if ($payment_good) {
            //$eway_auth_code = $this->EE->eway_model->eway_auth_code;
            //send_cc_confirmation
            
            /*
            $email_vars = array(
                'cc_auth' => $eway_auth_code,
                'cc_date' => date("j F, Y, g:i a"),
                'cc_name' => $this->EE->input->post('first_name') . ' ' . $this->EE->input->post('last_name'),
                'cc_amount' => $this->subscription_details->aud_price,
                'customer_email' => $this->EE->input->post('email'),
            );
             * 
             */

            $cc_result = '';
            $this->store_subscriber();
            //$this->EE->subscribers_model->update_tna_subscriber_details($member_id,$params);

            $pwgen = new PWGen();
            $password_key = $pwgen->generate();
            $this->EE->tna_commerce_lib->set_password_key($password_key);
            $id = $this->EE->tna_commerce_lib->encrypt($this->member_id);

            setcookie('tna_subscribe_result_1', $id, time() + 3600);

            setcookie('tna_subscribe_result_2', $password_key, time() + 3600);

            redirect($this->https_site_url . "subscribe/gift_success");
        } else {
            $vars = $this->process_eway_error('payment');
            $vars['subscription_details'] = $this->subscription_details;
            return $this->EE->load->view('subscribe_payment_card', $vars, TRUE);
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
        $vars['countrycode'] = $this->country_code;
        $vars['countrylist'] = $this->country_list;
        return $vars;
    }

    public function store_subscriber() {
        dev_log::write("store_subscriber");
        $errors = array();
        $member_id = '';
        $existing_member = false;
        //$existing_subscriber = false;

        $this->EE->load->helper('security');
        require_once("$this->default_site_path/includes/pwgen.class.php");
        //require_once("$this->default_site_path/includes/encryption_tnra.php");
        $fullname = $this->EE->input->post('first_name') . ' ' . $this->EE->input->post('last_name');
        $screen_name = ($this->EE->input->post('screen_name')) ? $this->EE->input->post('screen_name') : $fullname;
        //$enc = new Encryption_tnra();

        $pwgen = new PWGen();
        $password = $pwgen->generate();
        $this->password_key = $pwgen->generate();

        $data['username'] = $this->EE->input->post('r_email');
        $data['screen_name'] = $screen_name;
        //$data['password']    = do_hash($this->EE->input->post('password'));
        $data['password'] = md5($password); // do_hash($password);
        $data['email'] = $this->EE->input->post('r_email');
        $data['ip_address'] = $this->EE->input->ip_address();
        $data['unique_id'] = random_string('encrypt');
        $data['join_date'] = $this->EE->localize->now;
        $data['language'] = $this->EE->config->item('deft_lang');
        $data['timezone'] = ($this->EE->config->item('default_site_timezone') && $this->EE->config->item('default_site_timezone') != '') ? $this->EE->config->item('default_site_timezone') : $this->EE->config->item('server_timezone');
        //$data['daylight_savings'] = ($this->EE->config->item('default_site_dst') && $this->EE->config->item('default_site_dst') != '') ? $this->EE->config->item('default_site_dst') : $this->EE->config->item('daylight_savings');
        $data['time_format'] = ($this->EE->config->item('time_format') && $this->EE->config->item('time_format') != '') ? $this->EE->config->item('time_format') : 'us';
        $data['group_id'] = $this->subscriber_group_id;

        $duplicate = $this->EE->subscribers_model->find_duplicate($data['email']);
        
         dev_log::write("Registering this email address: [{$data['email']}]");

        if ($duplicate) {
            $existing_member = true;
            $member_id = $duplicate->member_id;
            $this->EE->subscribers_model->update_subscriber_group($member_id, $this->subscriber_group_id);
            dev_log::write("FOUND DUPLICATE: [$member_id]");

            //$this->EE->subscribers_model->create_ee_member($data);
        } else {
            $member_id = $this->EE->subscribers_model->create_ee_member($data);
        }


        $params = array(
            'member_id' => $member_id,
            'temp_password' => ($duplicate) ? '' : $password,
            'existing_member' => $existing_member,
            'type' => $this->EE->input->post('subscription_type'),
            'include_extras' => $this->EE->input->post('include_extras'),
            'tshirt_size' => $this->EE->input->post('r_tshirt_size')
        );
        
        $this->EE->subscribers_model->create_tna_subscriber($params);
        
        $params = array(
            'member_id' => $member_id,
            'first_name' => $this->EE->input->post('first_name'),
            'last_name' => $this->EE->input->post('last_name'),
            'email' => $this->EE->input->post('email'),
            'secret_gift' => $this->EE->input->post('secret_gift')
        );
        
        $this->EE->subscribers_model->create_subscriber_gift($params);

        //$subscriber_gift_details_fields = $this->EE->subscribers_model->get_details_gift_fields();
        $subscriber_details_fields = $this->EE->subscribers_model->get_details_fields();

        $params = array();
        
        foreach ($subscriber_details_fields as $field) {
            $params[$field] = $this->EE->input->post("r_$field");
        }
        
        $params['payment_method'] = $this->payment_method;

        //$params['status'] = 'active';

        $this->EE->subscribers_model->update_tna_subscriber_details($member_id, $params);
        //$this->EE->subscribers_model->set_rebill_details($member_id, $this->rebill_details);

        $this->member_id = $member_id;

        //$this->EE->input->post('last_name');
        //redirect($this->https_site_url."subscribe/payment/$member_id");
    }

    public function show() {
        
    }

    public function edit() {
        
    }

    public function update() {
        
    }

    public function destroy() {
        if ($this->logged_in) {
            $return_view = '';
            $vars = array();
            $errors = array();
            $eway_init = $this->EE->eway_model->init();
            if (!$eway_init) {
                $msg = $this->EE->eway_model->error_str['init'];
                $errors[] = $msg;
                $vars['errors'] = $errors;
                return $this->EE->load->view('cancel_subscription', $vars, TRUE);
                exit();
            }

            $return_view = 'cancel_subscription';
            $vars = array('member_id' => $this->member_id, 'comments' => '');
            if ($this->EE->input->post("confirm_cancellation_$this->member_id")) {
                $result = $this->delete_subscriber($this->member_id);
                if ($result) {
                    $return_view = 'cancel_subscription_complete';
                } else {

                    $errors[] = $this->EE->eway_model->eway_error;
                    $vars['errors'] = $errors;
                    $vars['comments'] = $this->EE->input->post('comments');
                }
            }

            return $this->EE->load->view($return_view, $vars, TRUE);
        }
    }

}
