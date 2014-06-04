<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class subscribe_controller extends Base_Controller {

    protected $EE;

    public $subscribe_stage;

    public function __construct($args='') {
        //die('boo');

        parent::__construct($args);



        //$this->member_id = $this->EE->session->userdata('member_id');
    }
    
    public function init() {
      $this->EE->subscribers_model->set_subscription_types();

    }


    public function index() {
        //$this->EE->tna_commerce_lib->email_test();
        $this->EE->tna_commerce_lib->email_test_4();
        
        //print_r($groups->result_array());
        //die();
        //$email_result = $this->EE->tna_commerce_lib->email_test_2();
        //$msg = ($email_result)?"email_test: success":"email_test: fail";
        //dev_log::write($email_result);
        
        $this->init();
        //$this->resolved = $this->EE->TMPL->fetch_param('resolved');
        //$this->EE->load->model('keyword_search_model');
        //$str = $this->EE->eway_model->config_vars();
        //die(var_dump($str));
        $this->subscribe_stage = 1;
        
        /*
         if ($this->EE->input->post('create_existing_member') && ($this->logged_in)) {
            redirect($this->https_site_url."subscribe/payment/$this->member_id");
        } elseif ($this->EE->input->post('create_member')) {
            return $this->store();
         */

        
        if ($this->logged_in) {
            return $this->create_existing();
        } else {
            return $this->create();
        }

        //die("LOGGED_IN = $this->logged_in");
        //$this->return_data = $this->EE->TMPL->tagdata;
        // $variables[] = array('action' => 'soap.php');
        // return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);
        //$tbl_tmpl = array ( 'table_open'  => '<table border="1" cellpadding="5" cellspacing="1" class="mytable">' );
        //$this->EE->table->set_template($tbl_tmpl);
        //return $this->EE->load->view('resolve_classification', $vars, TRUE);

    }

    public function create() {
        //$socket_timeout = ini_get('default_socket_timeout');
        dev_log::write("subscribe_controller:create");
        //error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        //error_reporting(0);
        //@ini_set('log_errors', 1);
        //@ini_set('display_errors',0);
        //trigger_error("BLAHHHHHHHHHHHHHH",E_USER_ERROR);
        //trigger_er
        
        //dev_log::write("subscribe_controller:socket_timeout = [$socket_timeout]");
        
       // $this->EE->member_model->delete_member(19992);
        

        $errors = array();
        $this->set_defaults();
        $vars = array('site_url'=>$this->site_url, 'errors'=>$errors);
        return $this->EE->load->view('subscribe_new', $vars, TRUE);

    }

    public function create_existing() {
        $errors = array();
        //$this->EE->session->userdata->username;
        $this->set_defaults();
        $vars = array(
            'errors'=>$errors,
            'username'=>$this->EE->session->userdata['username'],
            'email'=>$this->EE->session->userdata['email'],
            'member_id'=>$this->member_id
        );
        
        return $this->EE->load->view('subscribe_existing', $vars, TRUE);

    }
    /*
        public function payment() {

            //$url_decoded_password = urldecode($url_encoded_encrypted_password);
            //$decrypted_url_decoded_password = $this->decrypt($url_decoded_password);
           // $decrypted_password = $this->decrypt($encrypted_password);
            //die("$encrypted_password<br/>$decrypted_password");
            //$url_decoded_password = urldecode($url_encoded_encrypted_password);
            //$decrypted_url_decoded_password = $this->decrypt($encrypted_password);

            $this->subscribe_stage = 2;

            if (!$this->logged_in) {
                $this->member_id = $this->uri->segment(3, 0);
                $this->subscription_in_progress = ($this->member_id)?true:false;
            }


            $countrylist = $this->EE->eway_model->get_countrylist();

            $cc = $this->EE->eway_model->ip2location('countryCode');

            // $cc = $this->EE->eway_model->visitor_country();

            //die("CC =[$cc]");

            $vars = array('site_url'=>$this->site_url,
                'countrycode' => $cc,
                'countrylist' => $countrylist,
            );
            return $this->EE->load->view('subscribe_payment_card', $vars, TRUE);

        }

     */

    /*
        public function get_cookies() {
            $this->encrypted_password = $_COOKIE["tna_subscribe_tempdata_1"];
            $this->password_key = $_COOKIE["tna_subscribe_tempdata_2"];
            $this->member_id = $_COOKIE["tna_subscribe_tempdata_3"];
        }
    */

    public function store() {
        $errors = array();
        $member_id = '';
        $existing_member = 0;

        $this->EE->load->helper('security');
        require_once("$this->default_site_path/includes/pwgen.class.php");
        require_once("$this->default_site_path/includes/encryption_tnra.php");
        $fullname = $this->EE->input->post('first_name').' '.$this->EE->input->post('last_name');
        $screen_name =  ($this->EE->input->post('screen_name'))?$this->EE->input->post('screen_name'):$fullname;
        //$enc = new Encryption_tnra();

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

        $duplicate = $this->EE->subscribers_model->find_duplicate_members($data['email']);
        //$user_query_result = $this->EE->db->where('username',$email)->get('exp_members');
        
        if ($duplicate) {
            $this->EE->subscribers_model->nuke_subscriber($member_id);
            
        }

        
        $member_id = $this->EE->subscribers_model->create_ee_member($data);
        
        //die("BOOO ".$member_id);
            //$this->EE->member_model->delete_member($member_id);
       
            
         // $member_id = $duplicate->member_id;
         // $existing_member = 1;

      
        //$data['member_id'] = $member_id;

        $params = array(
            'member_id' => $member_id,
            'password' => $password,
            'existing_member' => $existing_member,
            'type' => $this->EE->input->post('subscription_type'),
        );

        //$this->EE->input->post('last_name');

        $this->EE->subscribers_model->create_tna_subscriber($params);
        redirect($this->https_site_url."subscribe/payment/$member_id");
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
