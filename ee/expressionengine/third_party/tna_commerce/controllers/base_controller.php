<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

abstract class Base_Controller {

    protected $EE;
    protected $member_id = 0;
    protected $logged_in = false;
    protected $site_url;
    protected $https_site_url;
    protected $default_site_path;
    protected $password_key;
    protected $encrypted_password;
    protected $subscriber_group_id = 0;
    protected $member_group_id = 0;
    protected $subscription_in_progress = false;
    protected $subscriber = '';
    protected $subscribe_stage;
    protected $uri_string;
    protected $member_groups;
    protected $postage_costs;
    protected $gift = 0;
  

    protected function __construct() {

        //die('weeeeeee');
        //error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        
        $this->EE = & get_instance();
        $this->EE->load->add_package_path(PATH_THIRD . '/tna_commerce');

        $this->EE->load->library('tna_commerce_lib');
        //$this->EE->load->model('member_model');

        $this->member_id = $this->EE->session->userdata('member_id');

        //print_r($this->EE->session->userdata);
        //die();

        if ($this->member_id) {
            $this->logged_in = true;
        }
        
        $this->site_url = $this->EE->config->item('site_url');
        $this->uri_string = $this->EE->uri->uri_string();
        $this->https_site_url = $this->EE->config->item('https_site_url');
      
        
        $globals = array(
            'site_url' => $this->site_url,
            'https_site_url' => $this->https_site_url,
            'uri_string' => $this->uri_string, 
        );
        $this->EE->load->vars($globals);

        $this->EE->load->helper('url');
        $this->default_site_path = $this->EE->config->item('default_site_path');
        $this->password_key = "bazooka";

        $this->EE->load->library('table');
        
        $this->EE->load->model('subscribers_model');
        $this->EE->load->model('transactions_model');
        $this->member_groups = $this->get_member_groups();
        
        $this->subscriber_group_id = $this->member_groups['subscribers'];
        $this->member_group_id = $this->member_groups['members'];
        
        $this->postage_costs = $this->EE->subscribers_model->get_postage_costs();
        
        $this->EE->load->vars($this->postage_costs);
        
        
        
        //$msg = "Is Subscriber? ".$this->is_subscriber();
        //die($msg);
        
        //$this->EE->load->model('member_model');
    }
    
    public function is_subscriber() {
        
        if (!$this->logged_in) {
            return false;
        }
        $query = $this->EE->member_model->get_member_data($this->member_id);
        $row = $query->row(); 
        
        if ($row->group_id = $this->subscriber_group_id) {
            return true;
        } else {
            return false;
        }
    }
    
    function get_member_groups() {
        $query = $this->EE->member_model->get_member_groups();
        $groups = array();

        foreach ($query->result_array() as $row) {
            $title = strtolower($row['group_title']);
            $id =  $row['group_id'];
            $groups[$title] = $id;
        }
        
        return $groups;
    }

    function get_option($name) {
        $val = '';
        $defaults = array();
        
        if (isset($defaults[$name])) {
            $val = $defaults[$name];
        }

        if ($this->EE->input->post($name)) {
            $val = $this->EE->input->post($name);
        }

        return $val;
    }

    function set_defaults() {
        
        if ($this->subscribe_stage == 1) {
            $country = 'AU';
            
            if ($this->EE->input->post('country')) {
                $country = $this->EE->input->post('country');
                
            }

            $form_defaults = array(
                'email' => $this->get_option('email'),
                'yearly_amount' => $this->EE->subscribers_model->subscription_types['yearly']->aud_price,
                'yearly_concession_amount' => $this->EE->subscribers_model->subscription_types['yearly_concession']->aud_price,
                'monthly_amount' => $this->EE->subscribers_model->subscription_types['monthly']->aud_price,
                'country' => $country
            );
        } elseif ($this->subscribe_stage == 2) {
            
            $years = array('2014','2015','2016','2017','2018','2019','2020','2021','2022','2023','2024');
            $months = array('01','02','03','04','05','06','07','08','09','10','11','12');
            $this->EE->load->vars(array('months'=>$months, 'years'=>$years));
         
            
             $form_default_fields = array(
                'RebillCustomerID',
                'subscription_type',
                'tshirt_size',
                'email',
                'first_name',
                'last_name',
                'cc_number',
                'cc_expiry_month',
                'cc_expiry_year',
                'cc_cvn',
                'company',
                'address',
                'address_2',
                'suburb',
                'state',
                'country',
                'postal_code',
                'state',
                'postage_cost',
                'include_extras',
                'total_cost',
                'aud_price'
            );
             
             $form_defaults = array();
             
             foreach ($form_default_fields as $field) {
                 $form_defaults[$field] = $this->get_option($field);
             }
        }


        $this->EE->load->vars($form_defaults);
        
        if ($this->gift) {
            
            $form_default_fields = array(
                'r_tshirt_size',
                'r_email',
                'r_first_name',
                'r_last_name',
                'r_address',
                'r_address_2',
                'r_suburb',
                'r_state',
                'r_country',
                'r_postal_code',
                'r_state',
            );
             
             $form_defaults = array();
             
             foreach ($form_default_fields as $field) {
                 $form_defaults[$field] = $this->get_option($field);
             }
             
             $this->EE->load->vars($form_defaults);
       
        }
        
    }

}
