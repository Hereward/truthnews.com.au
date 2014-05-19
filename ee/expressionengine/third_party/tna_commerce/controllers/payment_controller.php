<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class payment_controller extends Base_Controller {

    protected $EE;
    public $subscribe_stage;
    public $subscription_details;
    public $country_list;
    public $country_code;
    public $rebill_details;

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
        $eway_init = $this->EE->eway_model->init();

        if (!$eway_init) {
            $vars = array();
            $errors = array();
            $errors[] = $this->EE->eway_model->eway_error;
            $vars['errors'] = $errors;
            return $this->EE->load->view('subscribe_new', $vars, TRUE);
            exit();
        }
        $subscription_type = $this->EE->input->post('subscription_type');
        $this->subscription_details = $this->EE->subscribers_model->get_subscription_details($subscription_type);
        $this->country_list = $this->EE->tna_commerce_lib->get_countrylist();
        $this->country_code = $this->EE->tna_commerce_lib->ip2location('countryCode');
    }
    
    public function subscribe_success() {
        
        $this->member_id = $this->EE->uri->segment(3, 0);
        $auth_code = $this->EE->uri->segment(3, 0);
        $this->subscriber = $this->EE->subscribers_model->get_subscriber($this->member_id);
        
       // die("weee!");
        
        $vars = array(
            'subscriber' => $this->subscriber
        );
        
        return $this->EE->load->view('subscribe_success', $vars, TRUE);
        
    }

    public function create() {
        dev_log::write("payment_controller:create");
        $errors = array();

        //$url_decoded_password = urldecode($url_encoded_encrypted_password);
        //$decrypted_url_decoded_password = $this->decrypt($url_decoded_password);
        // $decrypted_password = $this->decrypt($encrypted_password);
        //die("$encrypted_password<br/>$decrypted_password");
        //$url_decoded_password = urldecode($url_encoded_encrypted_password);
        //$decrypted_url_decoded_password = $this->decrypt($encrypted_password);

        $vars = array();
        $this->subscribe_stage = 2;

        if (!$this->logged_in) {
            //$this->member_id = $this->EE->uri->segment(3, 0);
            $this->member_id = $this->EE->input->post('member_id');
            $this->subscription_in_progress = true;
        }


        //$this->subscriber = $this->EE->subscribers_model->get_subscriber($this->member_id);

        $email = $this->EE->input->post('email');

        $duplicate = $this->EE->subscribers_model->find_duplicate($email);
        $existing_subscriber = ($duplicate) ? $this->EE->subscribers_model->is_subscriber($duplicate->member_id) : false;


        if ($existing_subscriber) {
            if ($email == 'editor@truthnews.com.au') {
                $this->EE->subscribers_model->nuke_subscriber($duplicate->member_id);
            } else {
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

                return $this->EE->load->view($view, $vars, TRUE);
                exit();
            }
        }

        //$subscription_type = $this->EE->input->post('subscription_type');
        //$subscription_details = $this->EE->subscribers_model->get_subscription_details($subscription_type);
        //$tshirt_size = $this->EE->input->post('tshirt_size');

        $this->set_defaults();

        //$countrylist = $this->EE->eway_model->get_countrylist();
        //$cc = $this->EE->eway_model->ip2location('countryCode');
        // $cc = $this->EE->eway_model->visitor_country();
        //die("CC =[$cc]");
        //$terms = $this->EE->load->view('terms', $vars, TRUE);


        $vars['countrycode'] = $this->country_code;
        ;
        $vars['countrylist'] = $this->country_list;
        $vars['member_id'] = $this->member_id;
        //$vars['subscriber'] = $this->subscriber;
        //$vars['subscription_type'] = $subscription_type;
        $vars['subscription_details'] = $this->subscription_details;

        //$vars['email'] = $email;
        //$vars['tshirt_size'] = $tshirt_size;
        $vars['errors'] = $errors;

        return $this->EE->load->view('subscribe_payment_card', $vars, TRUE);
    }

    /*
      public function get_cookies() {
      $this->encrypted_password = $_COOKIE["tna_subscribe_tempdata_1"];
      $this->password_key = $_COOKIE["tna_subscribe_tempdata_2"];
      $this->member_id = $_COOKIE["tna_subscribe_tempdata_3"];
      }
     */

    public function store() {
        //dev_log::write("payment_controller:store");
        $RebillCustomerID = '';
        $RebillID = '';
        $this->subscribe_stage = 2;
        $errors = array();
        //$member_id = $this->EE->input->post('member_id');
        $params = array();
        
        $sd = print_r($this->subscription_details,true);
        dev_log::write("sd = $sd");
        $payment_good = $this->EE->eway_model->process_direct_payment($this->subscription_details);
        
        $eway_auth_code = '';
        
        //dev_log::write("payment_controller: [$payment_good]");

        if ($payment_good) {
            $eway_auth_code = $this->EE->eway_model->eway_auth_code;
            //send_cc_confirmation
             $email_vars = array(
                 'cc_auth' => $eway_auth_code,
                 'cc_date' => date("j F, Y, g:i a"),
                 'cc_name' => $this->EE->input->post('first_name') . ' ' . $this->EE->input->post('last_name'),
                 'cc_amount' => $this->subscription_details->aud_price,  
                 'cc_email' => $this->EE->input->post('email'),
             );
             
             $email_result = $this->EE->tna_commerce_lib->send_cc_confirmation($email_vars);
            
            $cc_result = '';
            $RebillCustomerID = $this->EE->input->post('RebillCustomerID');

            if (!$RebillCustomerID) {
                $cc_result = $this->EE->eway_model->create_customer();
                dev_log::write('payment_controller:store create_customer() = DONE');

                if ($this->EE->eway_model->eway_error) {
                    dev_log::write($this->EE->eway_model->eway_error);
                    //trigger_error($this->EE->eway_model->eway_error);
                   // log_message('error', $this->EE->eway_model->eway_error);
                    //$vars = $this->process_eway_error();
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
                dev_log::write('ERRORRRR = ['.$this->EE->eway_model->eway_error.']');
                if ($this->EE->eway_model->eway_error) {
                     //trigger_error($this->EE->eway_model->eway_error);
                     dev_log::write($this->EE->eway_model->eway_error);
                    //$vars = $this->process_eway_error();
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
            redirect($this->https_site_url . "subscribe/success/$this->member_id/$eway_auth_code");
        } else {
            $vars = $this->process_eway_error();
            return $this->EE->load->view('subscribe_payment_card', $vars, TRUE);
        }
    }

    public function process_eway_error() {
        $errors = array();
        $vars = array();
        $this->set_defaults();
        $errors[] = $this->EE->eway_model->eway_error;
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
        require_once("$this->default_site_path/includes/encryption_tnra.php");
        $fullname = $this->EE->input->post('first_name') . ' ' . $this->EE->input->post('last_name');
        $screen_name = ($this->EE->input->post('screen_name')) ? $this->EE->input->post('screen_name') : $fullname;
        $enc = new Encryption_tnra();

        $pwgen = new PWGen();
        $password = $pwgen->generate();
        $this->password_key = $pwgen->generate();

        $data['username'] = $this->EE->input->post('email');
        $data['screen_name'] = $screen_name;
        //$data['password']    = do_hash($this->EE->input->post('password'));
        $data['password'] = do_hash($password);
        $data['email'] = $this->EE->input->post('email');
        $data['ip_address'] = $this->EE->input->ip_address();
        $data['unique_id'] = random_string('encrypt');
        $data['join_date'] = $this->EE->localize->now;
        $data['language'] = $this->EE->config->item('deft_lang');
        $data['timezone'] = ($this->EE->config->item('default_site_timezone') && $this->EE->config->item('default_site_timezone') != '') ? $this->EE->config->item('default_site_timezone') : $this->EE->config->item('server_timezone');
        //$data['daylight_savings'] = ($this->EE->config->item('default_site_dst') && $this->EE->config->item('default_site_dst') != '') ? $this->EE->config->item('default_site_dst') : $this->EE->config->item('daylight_savings');
        $data['time_format'] = ($this->EE->config->item('time_format') && $this->EE->config->item('time_format') != '') ? $this->EE->config->item('time_format') : 'us';
        $data['group_id'] = $this->subscriber_group_id;

        //$email = '';
        //$email_query_result = $this->EE->db->where('email',$data['email'])->get('exp_members');



        $duplicate = $this->EE->subscribers_model->find_duplicate($data['email']);

        //$existing_subscriber = ($duplicate) ? $this->EE->subscribers_model->is_subscriber($duplicate->member_id) : false;
/*
        if ($existing_subscriber) {
            $errors[] = "The email address: [$duplicate->email] is already registered to a subscriber account. Please supply a different email adddress";
            $vars['errors'] = $errors;
            return $this->EE->load->view('subscribe_new', $vars, TRUE);
            exit();
        }
 */
        //$user_query_result = $this->EE->db->where('username',$email)->get('exp_members');

        if ($duplicate) {
            //$this->EE->subscribers_model->nuke_subscriber($member_id);
            $existing_member = true;
            $member_id = $duplicate->member_id;

            /*
              $member_id = $duplicate->member_id;
              $existing_subscriber = $this->EE->subscribers_model->is_subscriber($member_id);

              if ($existing_subscriber) {
              $errors = 'This email address is already registered to a subscriber account. Please supply a different email adddress';
              }
             */

            //$member_id = $this->EE->subscribers_model->create_ee_member($data);
        } else {
            $member_id = $this->EE->subscribers_model->create_ee_member($data);
        }

        //$this->EE->member_model->delete_member($member_id);
        // $member_id = $duplicate->member_id;
        // $existing_member = 1;
        //$data['member_id'] = $member_id;

        $params = array(
            'member_id' => $member_id,
            'temp_password' => $password,
            'existing_member' => $existing_member,
            'type' => $this->EE->input->post('subscription_type'),
        );
        $this->EE->subscribers_model->create_tna_subscriber($params);

        $subscriber_details_fields = $this->EE->subscribers_model->get_details_fields();

        $params = array();
        foreach ($subscriber_details_fields as $field) {
            $params[$field] = $this->EE->input->post($field);
        }

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
        
    }

}
