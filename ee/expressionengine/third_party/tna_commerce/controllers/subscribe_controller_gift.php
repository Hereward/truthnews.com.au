<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class subscribe_controller_gift extends Base_Controller {

    protected $EE;

    public $subscribe_stage;

    public function __construct($args='') {

        parent::__construct($args);

    }
    
    public function init() {
      $this->EE->subscribers_model->set_subscription_types();
      $this->country_code = 'AU'; 
      // $this->EE->tna_commerce_lib->ip2location('countryCode');

    }



    public function index() {
        $this->gift == 1;
        $this->init();
        $this->subscribe_stage = 1;

         return $this->create_gift();
        
    }
    
    public function create_gift() {
     
        dev_log::write("subscribe_controller:create_gift");

        $errors = array();
        $this->set_defaults();
        
        $countrylist = $this->EE->tna_commerce_lib->get_countrylist();
        $vars = array(
            'site_url'=>$this->site_url, 
            'errors'=>$errors, 
            'countrylist' => $countrylist,
            'countrycode' => $this->country_code
        );
        
        return $this->EE->load->view('subscribe_new', $vars, TRUE);
    }



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
        $data['password']    = md5($password); // do_hash($password);
        $data['email']        = $this->EE->input->post('email');
        $data['ip_address']    = $this->EE->input->ip_address();
        $data['unique_id']    = random_string('encrypt');
        $data['join_date']    = $this->EE->localize->now;
        $data['language']     = $this->EE->config->item('deft_lang');
        $data['timezone']     = ($this->EE->config->item('default_site_timezone') && $this->EE->config->item('default_site_timezone') != '') ? $this->EE->config->item('default_site_timezone') : $this->EE->config->item('server_timezone');
        //$data['daylight_savings'] = ($this->EE->config->item('default_site_dst') && $this->EE->config->item('default_site_dst') != '') ? $this->EE->config->item('default_site_dst') : $this->EE->config->item('daylight_savings');
        $data['time_format'] = ($this->EE->config->item('time_format') && $this->EE->config->item('time_format') != '') ? $this->EE->config->item('time_format') : 'us';
        $data['group_id'] = $this->subscriber_group_id;


        $duplicate = $this->EE->subscribers_model->find_duplicate_members($data['email']);

        
        if ($duplicate) {
            $this->EE->subscribers_model->nuke_subscriber($member_id);
            
        }

        
        $member_id = $this->EE->subscribers_model->create_ee_member($data);
        
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
