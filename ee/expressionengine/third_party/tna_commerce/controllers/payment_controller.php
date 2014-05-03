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
   
        return $this->create();
    }

 

    public function create() {

        //$url_decoded_password = urldecode($url_encoded_encrypted_password);
        //$decrypted_url_decoded_password = $this->decrypt($url_decoded_password);
       // $decrypted_password = $this->decrypt($encrypted_password);
        //die("$encrypted_password<br/>$decrypted_password");
        //$url_decoded_password = urldecode($url_encoded_encrypted_password);
        //$decrypted_url_decoded_password = $this->decrypt($encrypted_password);

       
        $this->subscribe_stage = 2;

        if (!$this->logged_in) {
            $this->member_id = $this->EE->uri->segment(3, 0);
            $this->subscription_in_progress = ($this->member_id)?true:false;
        }
        
        $this->subscriber = $this->EE->subscribers_model->get_subscriber($this->member_id);
       
         $this->set_defaults();


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

/*
    public function get_cookies() {
        $this->encrypted_password = $_COOKIE["tna_subscribe_tempdata_1"];
        $this->password_key = $_COOKIE["tna_subscribe_tempdata_2"];
        $this->member_id = $_COOKIE["tna_subscribe_tempdata_3"];
    }
*/

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
