<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class subscribe_gift_controller extends Base_Controller {

    protected $EE;

    public $subscribe_stage;

    public function __construct($args='') {
        parent::__construct($args);
        
    }
    
    public function init() {
      $this->gift = 1;
      $this->EE->subscribers_model->set_subscription_types();
      $this->country_code = 'AU'; 
      // $this->EE->tna_commerce_lib->ip2location('countryCode');

    }



    public function index() {
        
        $this->init();
        $this->subscribe_stage = 1;

         return $this->create();
        
    }
    
    public function create() {
     
        dev_log::write("subscribe_gift_controller:create");

        $errors = array();
        $this->set_defaults();
        
        $countrylist = $this->EE->tna_commerce_lib->get_countrylist();
        $vars = array(
            'site_url'=>$this->site_url, 
            'errors'=>$errors, 
            'countrylist' => $countrylist,
            'countrycode' => $this->country_code
        );
        
        return $this->EE->load->view('subscribe_gift', $vars, TRUE);
    }



    public function store() {
      
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
