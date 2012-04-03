<?php

class Ssp_director_upd { 

    var $version        = '1.0'; 
     
    function __construct() 
    { 
		$this->EE =& get_instance();
    }
    
    
    function install() {
	$this->EE->load->dbforge();

	$data = array(
		'module_name' => 'Ssp_director' ,
		'module_version' => $this->version,
		'has_cp_backend' => 'y',
		'has_publish_fields' => 'n'
	);

	$this->EE->db->insert('modules', $data);
	
	if (! $this->EE->db->table_exists('exp_ssp_director')) {
			$this->EE->load->dbforge();
			
			$this->EE->dbforge->add_field(array(
			    'ssp_director_id' => array('type' => 'int', 'constraint' => 6, 'unsigned' => TRUE, 'auto_increment' => TRUE),
			    'api_key' => array('type' => 'varchar', 'constraint' => 200),
			    'ssp_path' => array('type' => 'varchar', 'constraint' => 200),
			    'ssp_url' => array('type' => 'varchar', 'constraint' => 200),
			    'ssp_url_fe' => array('type' => 'varchar', 'constraint' => 200),
			    'ssp_cache_time' => array('type' => 'varchar', 'constraint' => 200),
			    'host' => array('type' => 'varchar', 'constraint' => 200),
			    'user' => array('type' => 'varchar', 'constraint' => 200),
			    'password' => array('type' => 'varchar', 'constraint' => 200),
			    'database' => array('type' => 'varchar', 'constraint' => 200),
			    'active' => array('type' => 'varchar', 'constraint' => 200)
			));

			$this->EE->dbforge->add_key('ssp_director_id', TRUE);
			$this->EE->dbforge->create_table('ssp_director');
		} 
		
		return TRUE;
		
    }
    
	
	function update($current = '') {
	if ($current == $this->version)
	{
		return FALSE;
	}
		
	if ($current < 2.0) 
	{
		// Do your update code here
	} 
	
	return TRUE; 
	}


function uninstall() {
		// remove row from exp_modules
		$this->EE->db->delete('modules', array('module_name' => 'Ssp_director'));
        $sql = 'drop table exp_ssp_director';	
		$this->EE->db->query($sql);

		return TRUE;
	}
	
	
	
}


// END

