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
    protected $subscriber_group_id = 12;
    protected $subscription_in_progress = false;
    protected $subscriber = '';
    protected $subscribe_stage;
    protected $uri_string;

    protected function __construct() {

        //die('weeeeeee');
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
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
        $this->EE->load->model('eway_model');
        $this->EE->load->model('subscribers_model');
        //$this->EE->load->model('member_model');
    }

    function get_option($name) {
        $val = '';
        $defaults = array();
        
        /*
        if ($this->subscriber) {
            $defaults = array(
                'first_name' => $this->subscriber->first_name,
                'last_name' => $this->subscriber->last_name,
                'screen_name' => "",
            );
        }
         
        */
        if (isset($defaults[$name])) {
            $val = $defaults[$name];
        }
/*
        if (isset($_POST[$name])) {
            $val = $_POST[$name];
        }
*/
        if ($this->EE->input->post($name)) {
            $val = $this->EE->input->post($name);
        }

        return $val;
    }

    function set_defaults() {
        
        if ($this->subscribe_stage == 1) {

            $form_defaults = array(
                'email' => $this->get_option('email'),
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
                'company',
                'address',
                'address_2',
                'suburb',
                'state',
                'country',
                'postal_code',
                'state'
            );
             
             $form_defaults = array();
             
             foreach ($form_default_fields as $field) {
                 $form_defaults[$field] = $this->get_option($field);
             }

        }


        $this->EE->load->vars($form_defaults);
        
    }

}
