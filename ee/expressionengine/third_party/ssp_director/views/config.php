<?=form_open($action_url, '', $form_hidden)?>
   

<?php
$this->table->set_template($cp_table_template);
$this->table->set_heading(array (
	'colspan' => 1,
	'data' => lang('edit_config_page_heading')
));

foreach ($fields as $name => $label) {
	$this->table->add_row(array (
		'style' => 'width: 40%;',
		'data' => $label
	), array (
		'style' => 'padding-right: 13px',
		'data' => form_input($name, $config[$name], "id='$name'"))
	);
}

echo $this->table->generate();
?>
<p><?php echo form_submit(array('name' => 'submit', 'value' => 'Update', 'class' => 'submit')) ?></p>
<?=form_close()?>





