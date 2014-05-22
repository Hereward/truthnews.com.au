<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class info_controller {

    protected $EE;
    protected $subscriber_group_id = 0;
    protected $member_group_id = 0;
    protected $member_id = 0;
    protected $member_groups;
    protected $logged_in = false;
    protected $subscriber;
 
    public function __construct($args = '') {
        //parent::__construct($args);
        $this->EE = & get_instance();
        $this->member_id = $this->EE->session->userdata('member_id');
        
        if ($this->member_id) {
            $this->logged_in = true;
        }
        
        //$this->EE->load->add_package_path(PATH_THIRD . '/tna_commerce'); 
    }
    
    function get_member_groups() {
        $this->EE->load->model('member_model');
        $query = $this->EE->member_model->get_member_groups();
        $groups = array();

        foreach ($query->result_array() as $row) {
            $title = strtolower($row['group_title']);
            $id =  $row['group_id'];
            $groups[$title] = $id;
        }
        
        return $groups;
    }
    
    public function subscriber_key_details() {
        //return "boo";
        if (!$this->logged_in) {
            return '';
        }
        $this->EE->load->model('subscribers_model');
        $this->subscriber = $this->EE->subscribers_model->get_subscriber($this->member_id);
        $tshirt_status = $this->subscriber->tshirt_status;
        $msg = "Your subscription is {$this->subscriber->type}.";
        
        if ($tshirt_status) {
            $msg .= " <br>Your t-shirt status is: $tshirt_status.";
        }
        
        return $msg;
    }
    
    
    
    public function is_subscriber() {
        if (!$this->logged_in) {
            return '';
        }
        
        $this->member_groups = $this->get_member_groups();
        $this->subscriber_group_id = $this->member_groups['subscribers'];
        $this->member_group_id = $this->member_groups['members'];
        $query = $this->EE->member_model->get_member_data($this->member_id);
        $row = $query->row(); 
        
        if ($row->group_id == $this->subscriber_group_id) {
            return 1;
        } else {
            return '';
        }
    }
    
    

    public function index() {
       
    }


}
