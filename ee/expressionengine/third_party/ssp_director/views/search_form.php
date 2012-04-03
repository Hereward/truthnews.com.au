<?=form_open($action_url)?>

<?php
$this->table->set_template($cp_table_template);
$this->table->set_heading(array (
	'colspan' => 1,
	'data' => 'Search'
));



	$this->table->add_row(array (
		'style' => 'width: 40%;',
		'data' => form_input($input_name, $search_default, "id='$input_name'")), 
		array (
		'style' => 'padding-right: 13px',
		'data' => form_submit(array('name' => $submit_name, 'value' => ' GO! ', 'class' => 'submit'))
	));
	
echo $this->table->generate();
?>

<?=form_close()?>





