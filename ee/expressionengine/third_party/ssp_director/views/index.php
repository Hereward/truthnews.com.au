
<?php
	$this->table->set_template($cp_table_template);
    $this->table->set_heading(array('colspan'=>1, 'data'=>lang('ssp_director_module_name')));
    $this->table->add_row('<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ssp_director'.AMP.'method=edit_config'.'">Edit Config</a>'); 
	$this->table->add_row('<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ssp_director'.AMP.'method=show_albums'.'">Show Albums</a>'); 
		
	echo $this->table->generate();
?>



