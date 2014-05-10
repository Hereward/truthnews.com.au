<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class payment_controller extends Base_Controller {

    protected $EE;

    public $subscribe_stage;

    public function __construct($args='') {
        //die('boo');

        parent::__construct($args);



        //$this->member_id = $this->EE->session->userdata('member_id');
    }


    public function index() {
        
         if ($this->EE->input->post('submit_payment')) {
            return $this->store();
         } else {
             return $this->create();
         }
   
        
    }
    
 

    public function create() {
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
        $existing_subscriber = ($duplicate)?$this->EE->subscribers_model->is_subscriber($duplicate->member_id):false;
        
        
        if ($existing_subscriber) {
            $errors[] = "The email address: [$duplicate->email] is already registered to a subscriber account. Please supply a different email adddress."; 
            $vars['errors'] = $errors;
            return $this->EE->load->view('subscribe_new', $vars, TRUE);
            exit();
            
        }
        
        
        
        $subscription_type = $this->EE->input->post('subscription_type');
        $subscription_details = $this->EE->subscribers_model->get_subscription_details($subscription_type);
        $tshirt_size = $this->EE->input->post('tshirt_size');
        
        $this->set_defaults();


        $countrylist = $this->EE->eway_model->get_countrylist();

        $cc = $this->EE->eway_model->ip2location('countryCode');
        
        
        

        // $cc = $this->EE->eway_model->visitor_country();
        //die("CC =[$cc]");
        //$terms = $this->EE->load->view('terms', $vars, TRUE);
        
       
        $vars['countrycode'] = $cc;
        $vars['countrylist'] = $countrylist;
        $vars['member_id'] = $this->member_id;
        //$vars['subscriber'] = $this->subscriber;
        $vars['subscription_type'] = $subscription_type;
        $vars['subscription_details'] = $subscription_details;
        
        $vars['email'] = $email;
        $vars['tshirt_size'] = $tshirt_size;
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
        $member_id = $this->EE->input->post('member_id');
        
        $params = array();
        
        
        
        /*
        foreach ($subscriber_details_fields as $field) {
            $params[$field] = $this->EE->input->post($field);
        }
         
         */
        
        //die($this->EE->input->post('country_id'));
        
        $this->store_subscriber();

        //$this->EE->subscribers_model->update_tna_subscriber_details($member_id,$params);
        redirect($this->https_site_url."subscribe/success/$member_id");
    }
    
    
     public function store_subscriber() {
        $errors = array();
        $member_id = '';
        $existing_member = false;
        $existing_subscriber = false;

        $this->EE->load->helper('security');
        require_once("$this->default_site_path/includes/pwgen.class.php");
        require_once("$this->default_site_path/includes/encryption_tnra.php");
        $fullname = $this->EE->input->post('first_name').' '.$this->EE->input->post('last_name');
        $screen_name =  ($this->EE->input->post('screen_name'))?$this->EE->input->post('screen_name'):$fullname;
        $enc = new Encryption_tnra();

        $pwgen = new PWGen();
        $password = $pwgen->generate();
        $this->password_key = $pwgen->generate();

        $data['username']     = $this->EE->input->post('email');
        $data['screen_name']     = $screen_name;
        //$data['password']    = do_hash($this->EE->input->post('password'));
        $data['password']    = do_hash($password);
        $data['email']        = $this->EE->input->post('email');
        $data['ip_address']    = $this->EE->input->ip_address();
        $data['unique_id']    = random_string('encrypt');
        $data['join_date']    = $this->EE->localize->now;
        $data['language']     = $this->EE->config->item('deft_lang');
        $data['timezone']     = ($this->EE->config->item('default_site_timezone') && $this->EE->config->item('default_site_timezone') != '') ? $this->EE->config->item('default_site_timezone') : $this->EE->config->item('server_timezone');
        //$data['daylight_savings'] = ($this->EE->config->item('default_site_dst') && $this->EE->config->item('default_site_dst') != '') ? $this->EE->config->item('default_site_dst') : $this->EE->config->item('daylight_savings');
        $data['time_format'] = ($this->EE->config->item('time_format') && $this->EE->config->item('time_format') != '') ? $this->EE->config->item('time_format') : 'us';
        $data['group_id'] = $this->subscriber_group_id;

        //$email = '';
        //$email_query_result = $this->EE->db->where('email',$data['email'])->get('exp_members');
        
       

        $duplicate = $this->EE->subscribers_model->find_duplicate($data['email']);
        
        $existing_subscriber = ($duplicate)?$this->EE->subscribers_model->is_subscriber($duplicate->member_id):false;
 
        if ($existing_subscriber) {
            $errors[] = "The email address: [$duplicate->email] is already registered to a subscriber account. Please supply a different email adddress"; 
            $vars['errors'] = $errors;
            return $this->EE->load->view('subscribe_new', $vars, TRUE);
            exit();
            
        }
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
        
        $this->EE->subscribers_model->update_tna_subscriber_details($member_id,$params);

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
