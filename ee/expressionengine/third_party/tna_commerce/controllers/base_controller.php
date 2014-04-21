<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class Base_Controller {
	
	/**
	 * @var	object
	 */
	protected $EE;
    public $member_id = 0;

    public $logged_in = false;
    public $site_url;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{

		//die('weeeeeee');
		$this->EE =& get_instance();
		$this->EE->load->add_package_path(PATH_THIRD.'/tna_commerce');
		$this->EE->load->library('tna_commerce_lib');

        $this->member_id = $this->EE->session->userdata('member_id');

        if ($this->member_id) {
            $this->logged_in = true;
        }

        $this->site_url = $this->EE->config->item('site_url');

	}






}
