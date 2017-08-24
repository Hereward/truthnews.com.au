<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class subscription_payment_controller extends Base_Controller {

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
        //$eway_init = $this->EE->eway_model->init();
        /*
          if (!$eway_init) {
          $vars = array();
          $errors = array();
          $errors[] = $this->EE->eway_model->eway_error;
          $vars['errors'] = $errors;
          return $this->EE->load->view('subscribe_new', $vars, TRUE);
          exit();
          }
         */
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
        dev_log::write('subscribe_success: 1');
        //$this->member_id = $this->EE->uri->segment(3, 0);
        $cookie = $this->get_cookie();

        if ($cookie) {
            $this->member_id = $cookie;
            $this->delete_cookie();

            dev_log::write("decrypted cookie = [$this->member_id]");
            $auth_code = $this->EE->uri->segment(3, 0);
            $this->subscriber = $this->EE->subscribers_model->get_subscriber($this->member_id);

            $countrylist = $this->EE->tna_commerce_lib->get_countrylist();

            dev_log::write('subscribe_success: 2');

            //$vars['logged_in'] = $this->logged_in;
            // die("weee!");

            $vars = array(
                'subscriber' => $this->subscriber,
                'countrylist' => $countrylist,
                'logged_in' => $this->logged_in,
                'gateway_mode' => $this->EE->config->item('gateway_mode'),
            );

            $this->EE->tna_commerce_lib->send_subscription_confirmation($vars);

            //dev_log::write('Subscription confirmation email sent.');

            return $this->EE->load->view('subscribe_success', $vars, TRUE);
        } else {
            redirect($this->https_site_url . "subscribe");
        }
    }

    public function create() {
        redirect($this->https_site_url);
        die();
        dev_log::write("payment_controller:create");
        $existing_subscriber = '';
        $errors = array();

        $vars = array();
        $this->subscribe_stage = 2;

        if (!$this->logged_in) {
            //$this->member_id = $this->EE->uri->segment(3, 0);
            $this->member_id = $this->EE->input->post('member_id');
            $this->subscription_in_progress = true;
        }


        $email = $this->EE->input->post('email');

        $duplicate = $this->EE->subscribers_model->find_duplicate($email);

        if ($email == 'editor@truthnews.com.au' && $duplicate) {
            $this->EE->subscribers_model->nuke_subscriber($duplicate->member_id);
            $existing_subscriber = false;
        } else {
            $existing_subscriber = ($duplicate) ? $this->EE->subscribers_model->is_subscriber($duplicate->member_id) : false;
        }

        if ($existing_subscriber) {
            dev_log::write("payment:create existing_subscriber > GO BACK");

            $view = '';
            if ($this->logged_in) {
                $errors[] = "Your email address: [$duplicate->email] is already registered to a subscriber account. Please <strong><a href='$this->https_site_url?ACT=10&return=%2Fsubscribe'>log out</a></strong> and supply a different email adddress.";
            } else {
                $errors[] = "The email address: [$duplicate->email] is already registered to a subscriber account. Please supply a different email adddress.";
            }
            $vars['errors'] = $errors;
            $vars['member_id'] = $this->member_id;

            if ($this->logged_in) {
                $vars['username'] = $this->EE->session->userdata['username'];
                $vars['email'] = $this->EE->session->userdata['email'];
                $view = 'subscribe_existing';
            } else {
                $view = 'subscribe_new';
            }
            
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

        return $this->EE->load->view('subscribe_payment_card', $vars, TRUE);
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

    /*
      public function get_cookies() {
      $this->encrypted_password = $_COOKIE["tna_subscribe_tempdata_1"];
      $this->password_key = $_COOKIE["tna_subscribe_tempdata_2"];
      $this->member_id = $_COOKIE["tna_subscribe_tempdata_3"];
      }
     */

    public function store() {
        redirect($this->https_site_url);
        die();
        //$generic_error = 'The transaction failed. Please check your credit card details and try again.';
        $eway_auth_code = '';
        $eway_init = $this->EE->eway_model->init();
        if (!$eway_init) {
            $vars = $this->process_eway_error('init');
            return $this->EE->load->view('subscribe_payment_card', $vars, TRUE);
            exit();
        }

        //dev_log::write("payment_controller:store");
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
            $email_vars = array(
                'cc_auth' => $eway_auth_code,
                'cc_date' => date("j F, Y, g:i a"),
                'cc_name' => $this->EE->input->post('first_name') . ' ' . $this->EE->input->post('last_name'),
                'cc_amount' => $this->subscription_details->aud_price,
                'customer_email' => $this->EE->input->post('email'),
            );

            //$email_result = $this->EE->tna_commerce_lib->send_cc_confirmation($email_vars);

            $cc_result = '';
            $RebillCustomerID = $this->EE->input->post('RebillCustomerID');

            if (!$RebillCustomerID) {
                $cc_result = $this->EE->eway_model->create_customer();
                dev_log::write('payment_controller:store create_customer() = DONE');

                if ($this->EE->eway_model->eway_error) {
                    dev_log::write($this->EE->eway_model->eway_error);
                    //trigger_error($this->EE->eway_model->eway_error);
                    // log_message('error', $this->EE->eway_model->eway_error);
                    //return $this->EE->load->view('subscribe_payment_card', $vars, TRUE);
                    //exit();
                } else {
                    dev_log::write('payment_controller:store create_customer() = SUCCESS');
                }
                $RebillCustomerID = $cc_result['RebillCustomerID'];
                $RebillID = $cc_result['RebillCustomerID'];
            }
            // dev_log::write("payment_controller:166");


            if ($RebillCustomerID) {
                $ce_result = $this->EE->eway_model->create_event($this->subscription_details, $RebillCustomerID);
                $this->rebill_details = $ce_result;
                dev_log::write('payment_controller:store create_event() = DONE');

                if ($this->EE->eway_model->eway_error) {
                    //trigger_error($this->EE->eway_model->eway_error);
                    //dev_log::write('eWay Rebill error = [' . $this->EE->eway_model->eway_error . ']');
                    //$vars['RebillCustomerID'] = $RebillCustomerID;
                    //return $this->EE->load->view('subscribe_payment_card', $vars, TRUE);
                    //exit();
                } else {
                    dev_log::write('payment_controller:store create_event() = SUCCESS');
                }
            }
            // dev_log::write("payment_controller:180");
            //if $cc_result

            $this->store_subscriber();
            //$this->EE->subscribers_model->update_tna_subscriber_details($member_id,$params);

            $pwgen = new PWGen();
            $password_key = $pwgen->generate();
            $this->EE->tna_commerce_lib->set_password_key($password_key);
            $id = $this->EE->tna_commerce_lib->encrypt($this->member_id);

            setcookie('tna_subscribe_result_1', $id, time() + 3600);

            setcookie('tna_subscribe_result_2', $password_key, time() + 3600);

            redirect($this->https_site_url . "subscribe/success");
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

        $data['username'] = $this->EE->input->post('email');
        $data['screen_name'] = $screen_name;
        //$data['password']    = do_hash($this->EE->input->post('password'));
        $data['password'] = md5($password); // do_hash($password);
        $data['email'] = $this->EE->input->post('email');
        $data['ip_address'] = $this->EE->input->ip_address();
        $data['unique_id'] = random_string('encrypt');
        $data['join_date'] = $this->EE->localize->now;
        $data['language'] = $this->EE->config->item('deft_lang');
        $data['timezone'] = ($this->EE->config->item('default_site_timezone') && $this->EE->config->item('default_site_timezone') != '') ? $this->EE->config->item('default_site_timezone') : $this->EE->config->item('server_timezone');
        //$data['daylight_savings'] = ($this->EE->config->item('default_site_dst') && $this->EE->config->item('default_site_dst') != '') ? $this->EE->config->item('default_site_dst') : $this->EE->config->item('daylight_savings');
        $data['time_format'] = ($this->EE->config->item('time_format') && $this->EE->config->item('time_format') != '') ? $this->EE->config->item('time_format') : 'us';
        $data['group_id'] = $this->subscriber_group_id;

        $duplicate = $this->EE->subscribers_model->find_duplicate($data['email']);

        if ($duplicate) {
            $existing_member = true;
            $member_id = $duplicate->member_id;
            $this->EE->subscribers_model->update_subscriber_group($member_id, $this->subscriber_group_id);

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
            'tshirt_size' => $this->EE->input->post('tshirt_size')
        );
        $this->EE->subscribers_model->create_tna_subscriber($params);

        $subscriber_details_fields = $this->EE->subscribers_model->get_details_fields();

        $params = array();
        foreach ($subscriber_details_fields as $field) {
            $params[$field] = $this->EE->input->post($field);
        }
        
        $params['payment_method'] = $this->payment_method;

        //$params['status'] = 'active';

        $this->EE->subscribers_model->update_tna_subscriber_details($member_id, $params);
        $this->EE->subscribers_model->set_rebill_details($member_id, $this->rebill_details);

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
