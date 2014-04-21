<?php

abstract class Base_Extension {
	
	/**
	 * @var	string
	 */
	public $name = '';
	
	/**
	 * @var	string
	 */
	public $version = '';
	
	/**
	 * @var	string
	 */
	public $description = '';
	
	/**
	 * @var	string
	 */
	public $settings_exist = 'n';
	
	/**
	 * @var	string
	 */
	public $docs_url = '';
	
	/**
	 * @var	array
	 */
	public $settings = array();
	
	/**
	 * @var	object
	 */
	protected $EE;
	
	/**
	 * Constructor
	 *
	 * @param	array
	 */
	public function __construct($settings = array())
	{
		$this->EE =& get_instance();
		
		$this->settings = $settings;
	}
	
	/**
	 * Activate the extension
	 */
	abstract public function activate_extension();
	
	/**
	 * Run the actual activation
	 *
	 * @param	array
	 */
	protected function activate($hooks)
	{
		$this->EE->db->insert_batch('extensions', $hooks);
	}
	
	/**
	 * Update the extension
	 *
	 * @param	string
	 */
	public function update_extension($current = '')
	{
		// no need to update
		if($current == '' || $current == $this->version)
		{
			return FALSE;
		}
		
		// update version number
		$this->EE->db->where('class', get_class($this))->update('extensions', array(
			'version' => $this->version,
		));
		
		return TRUE;
	}
	
	/**
	 * Disable the extension
	 */
	public function disable_extension()
	{
		$this->EE->db->where('class', get_class($this))->delete('extensions');
	}
	
}