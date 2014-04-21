<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class Base_Module {
	
	/**
	 * @var	object
	 */
	protected $EE;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->EE->load->add_package_path(PATH_THIRD.'/tna_commerce');
		$this->EE->load->library('tna_commerce_lib');

	}
	
	
     public function test() {
     	$output = "this is a test function of the TNA commerce module - YAY IT WORKS!";
     	$lib_test = $this->EE->tna_commerce_lib->library_test();
     	$mod_test = $this->EE->tna_commerce_model->model_test();
     	
     	
     	
     	return "$output<br/>$lib_test<br/>$mod_test";
         	
     }
	
}

abstract class Base_Module_UPD {
	
	/**
	 * @var	string
	 */
	public $version = '2.0';
	
	/**
	 * @var	string
	 */
	protected $has_cp_backend = 'y';
	
	/**
	 * @var	string
	 */
	protected $has_publish_fields = 'n';
	
	/**
	 * @var	object
	 */
	protected $EE;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	/**
	 * Install the module
	 */
	public function install()
	{
		$this->EE->load->dbforge();
		$data = array(
			'module_name' => str_replace('_upd', '', get_class($this)),
			'module_version' => $this->version,
			'has_cp_backend' => $this->has_cp_backend,
			'has_publish_fields' => $this->has_publish_fields,
		);
		
		$this->EE->db->insert('modules', $data);
		
		return TRUE;
	}
	
	/**
	 * Update the module
	 *
	 * @param	string
	 * @return	bool
	 */
	public function update($current = '')
	{
		// no need to update
		if($current == '' || $current == $this->version)
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Uninstall the module
	 *
	 * @return	bool
	 */
	public function uninstall()
	{
		$this->EE->db->where('module_name', str_replace('_upd', '', get_class($this)))->delete('modules');
		
		return TRUE;
	}
	
}

abstract class Base_Module_MCP {
	
	/**
	 * @var	object
	 */
	protected $EE;
	
	/**
	 * @var	string
	 */
	protected $base_url = '';
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->library('javascript'); 
		$this->EE->load->library('table');
		
		// set the base control panel url for this module
		$this->base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=' . strtolower(str_replace('_mcp', '', get_class($this))) . AMP.'method=';
	
		$this->EE->cp->set_right_nav(array(
            'button 1'  => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'
                .AMP.'module=tna_commerce'.AMP.'method=index',
              'button 2'  => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'
                .AMP.'module=tna_commerce'.AMP.'method=index'
            ));     
        
	}
	
    public function index()
	{
		$this->EE->load->library('javascript');
		$this->EE->load->library('table');
		$this->EE->load->helper('form');

		$this->EE->cp->set_variable('cp_page_title', 'Pink Pages');

		$vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=download'.AMP.'method=edit_downloads';
		$vars['form_hidden'] = NULL;
		$vars['files'] = array();

		$vars['options'] = array(
                'edit'  => 'Edit Selected',
                'delete'    => 'Delete Selected'
                );

        return $this->EE->load->view('index', $vars, TRUE);
	}
	
}