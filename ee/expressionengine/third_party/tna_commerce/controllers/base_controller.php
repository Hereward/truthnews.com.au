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

    protected function __construct() {

        //die('weeeeeee');
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
        $this->https_site_url = $this->EE->config->item('https_site_url');
        $globals = array('https_site_url' => $this->https_site_url);
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

        if ($this->subscriber) {
            $defaults = array(
                'first_name' => $this->subscriber->first_name,
                'last_name' => $this->subscriber->last_name,
                'screen_name' => "",
            );
        }

        if (isset($defaults[$name])) {
            $val = $defaults[$name];
        }

        if (isset($_POST[$name])) {
            $val = $_POST[$name];
        }

        return $val;
    }

    function set_defaults() {
        
        if ($this->subscribe_stage == 1) {

            $form_defaults = array(
                'first_name' => $this->get_option('first_name'),
                'last_name' => $this->get_option('last_name'),
                'screen_name' => $this->get_option('screen_name'),
                'email' => $this->get_option('email'),
            );
        } elseif ($this->subscribe_stage == 2) {
           
            $form_defaults = array(
                'first_name' => $this->get_option('first_name'),
                'last_name' => $this->get_option('last_name'),
                'email' => $this->get_option('email'),
            );
        }


        $this->EE->load->vars($form_defaults);
    }

}
