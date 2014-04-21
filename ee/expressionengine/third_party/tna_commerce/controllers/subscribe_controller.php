<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class subscribe_controller extends Base_Controller {

    protected $EE;






    public function __construct($args='') {
        //die('boo');

        parent::__construct($args);

        //$this->member_id = $this->EE->session->userdata('member_id');
    }





    public function index() {

        //$this->resolved = $this->EE->TMPL->fetch_param('resolved');
        //$this->EE->load->model('keyword_search_model');
        $this->EE->load->library('table');

        if (!$this->logged_in) {
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
        $vars = array('site_url'=>$this->site_url);
        return $this->EE->load->view('subscribe_new', $vars, TRUE);

    }

    public function payment() {
        $vars = array('site_url'=>$this->site_url);
        return $this->EE->load->view('subscribe_payment', $vars, TRUE);

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
