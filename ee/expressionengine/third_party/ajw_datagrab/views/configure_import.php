<?php echo form_open( $form_action ); ?>

<?php 

$this->table->set_template($cp_table_template);
$this->table->set_heading("Default Fields", "Value");

$this->table->add_row(
	array(
		'colspan' => 2,
		'data' => 'Choose which values to use for the standard channel fields',
		'class' => 'box'
	)
);

/* Standard fields */

$this->table->add_row(
	form_label('Title', 'title') .
	'<div class="subtext">The entry\'s title.</div>', 
	form_dropdown("title", $data_fields, 
		isset($default_settings["title"]) ? $default_settings["title"] : '' )
);
$this->table->add_row(
	form_label('URL', 'url_title') .
	'<div class="subtext">The entry\'s URL title. If this is not set then the URL title will be derived from the entry\'s title.</div>', 
	form_dropdown("url_title", $data_fields, 
		isset($default_settings["url_title"]) ? $default_settings["url_title"] : '' )
);
$this->table->add_row(
	form_label('Date', 'date') .
	'<div class="subtext">Leave blank to set the entry\'s date to the time of import</div>', 
	form_dropdown("date", $data_fields, 
		isset($default_settings["date"]) ? $default_settings["date"] : '')
);
$this->table->add_row(
	form_label('Expiry date', 'expiry_date') .
	'<div class="subtext">Leave blank if you do not want to set an expiry date</div>', 
	form_dropdown("expiry_date", $data_fields, 
		isset($default_settings["expiry_date"]) ? $default_settings["expiry_date"] : '')
);

echo $this->table->generate();
echo $this->table->clear();

/* Custom fields */

$this->table->set_template($cp_table_template);
$this->table->set_heading("Custom Fields", "Value");

$this->table->add_row(
	array(
		'colspan' => 2,
		'data' => 'Assign values to use for the channel\'s custom fields. You can leave values blank.',
		'class' => 'box'
	)
);

foreach( $custom_fields as $field_name => $field_label) {

	switch( $field_types[$field_name] ) {

		case "playa":
			$this->table->add_row(
				form_label($field_label), 
				form_dropdown( $field_name, $data_fields, 
					isset($default_settings[$field_name]) ? $default_settings[$field_name] : '' )
					. "<br/<br/>Field to match: " . NBS .
					form_dropdown( 
						$field_name . "_playa_field", 
						$all_fields,  
						( isset($default_settings[$field_name . "_playa_field"]) ? $default_settings[$field_name . "_playa_field" ]: '' )
					) /*
					. "<br/<br/>Look for delimiter: " . NBS .
					form_input( 
						array(
							"name" => $field_name . "_playa_delimiter",
							"value" => ",",
							"size" => "4",
							"style" => "width: 40px"
						)
					)*/
			);		
			break;
			
		case "matrix":
			$this->table->add_row(
				form_label($field_label), 
				"Support in development"
			);
			/*
			$cells = form_hidden( $field_name, "1" );
			$settings = $field_settings[ $field_name ];
			if( isset( $default_settings[$field_name . "_matrix"] ) ) {
				$default_cells = $default_settings[$field_name . "_matrix"];
			} else {
				$default_cells = array();
			}
			foreach( $settings["col_ids"] as $col_id) {
				$cells .= "<tr><td>" . $matrix_columns[ $col_id ] . ": </td><td>" . 
					form_dropdown( $field_name . "_matrix[" . $col_id . "]", $data_fields, 
					isset($default_cells[ $col_id ]) ? $default_cells[ $col_id ] : '' ) . 
					"</td><td>" .
					 form_radio( $field_name . '_matrix_update', $col_id, (isset($default_settings[$field_name . "_matrix_update"]) && $col_id == $default_settings[$field_name . "_matrix_update"] ? TRUE : FALSE ) );
					"</td></tr>";
			}
		
			$this->table->add_row(
				form_label($field_label), 
				"<table class=\"noHeader\"><tr><td></td><td></td><td>Key</td></tr></tr>" . $cells . "</table>"
			);
			*/
			break;

			case "cartthrob_price_modifiers":
				$this->table->add_row(
					form_label($field_label), 
					"Support in development"
				);
			/*
				$cells = form_hidden( $field_name, "1" );
				$settings = $field_settings[ $field_name ];
				foreach( $ctpm_columns as $col_id => $col ) {
					$cells .= "<tr><td>" . $ctpm_columns[ $col_id ] . ": </td><td>" . 
						form_dropdown( $field_name . "_ctpm_" . $col_id, $data_fields, 
						isset($default_settings[$field_name. "_ctpm_" . $col_id]) ? $default_settings[$field_name. "_ctpm_" . $col_id] : '' ) . 
						"</td></tr>";
				}

				$this->table->add_row(
					form_label($field_label), 
					"<table class=\"noHeader\"><tr><td></td><td></td></tr></tr>" . $cells . "</table>"
				);
				*/
				break;
			
		default:
			$this->table->add_row(
				form_label($field_label), 
				form_dropdown( $field_name, $data_fields, 
					isset($default_settings[$field_name]) ? $default_settings[$field_name] : '' )
			);
	}

}

echo $this->table->generate();
echo $this->table->clear();

if( $seo_lite_installed ) {

$this->table->set_template($cp_table_template);
$this->table->set_heading("SEO Lite", "Value");

$this->table->add_row(
	array(
		'colspan' => 2,
		'data' => 'Add tags to the SEO Lite module',
		'class' => 'box'
	)
);
$field_name = "ajw_seo_lite_title";
$this->table->add_row(
	form_label( "SEO Lite Title" ), 
	form_dropdown( $field_name, $data_fields, 
		isset($default_settings[$field_name]) ? $default_settings[$field_name] : '' )
);
$field_name = "ajw_seo_lite_keywords";
$this->table->add_row(
	form_label( "SEO Lite Keywords" ), 
	form_dropdown( $field_name, $data_fields, 
		isset($default_settings[$field_name]) ? $default_settings[$field_name] : '' )
);
$field_name = "ajw_seo_lite_description";
$this->table->add_row(
	form_label( "SEO Lite Description" ), 
	form_dropdown( $field_name, $data_fields, 
		isset($default_settings[$field_name]) ? $default_settings[$field_name] : '' )
);

echo $this->table->generate();
echo $this->table->clear();

}

if( $tags_installed ) {

$this->table->set_template($cp_table_template);
$this->table->set_heading("Tags", "Value");

$this->table->add_row(
	array(
		'colspan' => 2,
		'data' => 'Add tags to the Solspace Tag module',
		'class' => 'box'
	)
);
$field_name = "ajw_solspace_tag";
$this->table->add_row(
	form_label( "Tags" ), 
	form_dropdown( $field_name, $data_fields, 
		isset($default_settings[$field_name]) ? $default_settings[$field_name] : '' )
);


echo $this->table->generate();
echo $this->table->clear();

}

/* Categories */

$this->table->set_template($cp_table_template);
$this->table->set_heading("Categories", "Value");

$this->table->add_row(
	array(
		'colspan' => 2,
		'data' => 'Add categories to the entry',
		'class' => 'box'
	)
);

$this->table->add_row(
	form_label("Default category value") .
	'<div class="subtext">Assign this category to every entry</div>', 
	form_input("category_value",  
		isset($default_settings["category_value"]) ? $default_settings["category_value"] : '' )
);

$this->table->add_row(
	form_label("Category field") .
	'<div class="subtext">Assign categories from this value to the entry</div>', 
	form_dropdown("cat_field", $data_fields, 
		isset($default_settings["cat_field"]) ? $default_settings["cat_field"] : '')
);

$this->table->add_row(
	form_label("Add new categories to this group")  .
	'<div class="subtext">Choose which group new categories should be added to</div>', 
	form_dropdown("cat_group", $category_groups, 
		isset($default_settings["cat_group"]) ? $default_settings["cat_group"] : '' )
);

$this->table->add_row(
	form_label("Category delimiter") .
	'<div class="subtext">eg, "One, Two, Three" will create 3 categories if the delimiter is a comma</div>', 
	form_input("cat_delimiter",  
		isset($default_settings["cat_delimiter"]) ? $default_settings["cat_delimiter"] : ',' )
);


echo $this->table->generate();
echo $this->table->clear();

/* Duplicate entries/updates */

$this->table->set_template($cp_table_template);
$this->table->set_heading("Check for duplicate entries", "Value");

$this->table->add_row(
	array(
		'colspan' => 2,
		'data' => 'Determine what happens if the import is run again',
		'class' => 'box'
	)
);
/*
$this->table->add_row(
	form_label("Entry id") .
	'<div class="subtext">Specify the entry\'s id.</div>', 
	form_dropdown("ajw_entry_id", $data_fields, 
		isset($default_settings["ajw_entry_id"]) ? $default_settings["ajw_entry_id"] : '' )
);
*/
$this->table->add_row(
	form_label("Use this field to check for duplicates") .
	'<div class="subtext">If an entry with this field\'s value already exists, do not create a new entry</div>', 
	form_dropdown("unique", $unique_fields, 
		isset($default_settings["unique"]) ? $default_settings["unique"] : '' )
);
$this->table->add_row(
	form_label("Update existing entries") .
	'<div class="subtext">If the unique field matches, then update the original entry, otherwise ignore it</div>', 
	form_checkbox("update", "y", (isset($default_settings["update"]) && $default_settings["update"] == "y" ? TRUE : FALSE) )
);
$this->table->add_row(
	form_label("Add a timestamp to this field") .
	'<div class="subtext">Add the time of the import to this custom field.</div>', 
	form_dropdown("timestamp", $unique_fields,
	 	isset($default_settings["timestamp"]) ? $default_settings["timestamp"] : '' )
);
$this->table->add_row(
	form_label("Delete old entries") .
	'<div class="subtext">Delete entries from this channel whose timestamp has not been updated</div>', 
	form_checkbox("delete_old", "y", (isset($default_settings["delete_old"]) && $default_settings["delete_old"] == "y" ? TRUE : FALSE) )
);

echo $this->table->generate();
echo $this->table->clear();

/* Comments */

if( $allow_comments ) {

	$this->table->set_template($cp_table_template);
	$this->table->set_heading("Comments", "Value");

	$this->table->add_row(
		array(
			'colspan' => 2,
			'data' => 'Import comments. NOTE: comments are only added when an entry in imported for the first time. Running a subsequent import will update the entry, but not the comments. Please delete the entry to force new comments to be added.',
			'class' => 'box'
		)
	);

	$this->table->add_row(
		form_label("Import comments?") .
		'<div class="subtext">Add comments for this entry</div>', 
		form_checkbox("import_comments", "y", (isset($default_settings["import_comments"]) && $default_settings["import_comments"] == "y" ? TRUE : FALSE) )
	);
	
	$this->table->add_row(
		form_label("Comment Author"), 
		form_dropdown( 'comment_author', $data_fields, 
			isset($default_settings['comment_author']) ? $default_settings['comment_author'] : '' )
	);

	$this->table->add_row(
		form_label("Comment Author Email"), 
		form_dropdown( 'comment_email', $data_fields, 
			isset($default_settings['comment_email']) ? $default_settings['comment_email'] : '' )
	);

	$this->table->add_row(
		form_label("Comment Author URL"), 
		form_dropdown( 'comment_url', $data_fields, 
			isset($default_settings['comment_url']) ? $default_settings['comment_url'] : '' )
	);

	$this->table->add_row(
		form_label("Comment Date"), 
		form_dropdown( 'comment_date', $data_fields, 
			isset($default_settings['comment_date']) ? $default_settings['comment_date'] : '' )
	);

	$this->table->add_row(
		form_label("Comment Body"), 
		form_dropdown( 'comment_body', $data_fields, 
			isset($default_settings['comment_body']) ? $default_settings['comment_body'] : '' )
	);


	echo $this->table->generate();
	echo $this->table->clear();
	
}

/* Other parameters */

$this->table->set_template($cp_table_template);
$this->table->set_heading("Other settings", "Value");

$this->table->add_row(
	array(
		'colspan' => 2,
		'data' => 'Some additional options',
		'class' => 'box'
	)
);
$this->table->add_row(
	form_label("Default Author") .
	'<div class="subtext">By default, assign entries to this author</div>', 
	form_dropdown("author", $authors, 
	 	isset($default_settings["author"]) ? $default_settings["author"] : '1' )
);
$this->table->add_row(
	form_label("Author") .
	'<div class="subtext">Assign the entry to the member in this field.<br/>Note: members will not be created. If the member does not exist the default author will be used.</div>', 
	form_dropdown("author_field", $data_fields, 
	 	isset($default_settings["author_field"]) ? $default_settings["author_field"] : '' )
);
$this->table->add_row(
	form_label("Author Field Value") .
	'<div class="subtext">Select the type of member data that the author field contains</div>', 
	form_dropdown("author_check", $author_fields, 
	 	isset($default_settings["author_check"]) ? $default_settings["author_check"] : 'screen_name' )
);

$this->table->add_row(
	form_label("Status") .
	'<div class="subtext">Choose the entry\'s status</div>', 
	form_dropdown("status", $status_fields, 
	 	isset($default_settings["status"]) ? $default_settings["status"] : 'default' )
);


$this->table->add_row(
	form_label("Offset (in seconds)") .
	'<div class="subtext">Apply an offset to the publish date</div>', 
	form_input("offset", 
	 	isset($default_settings["offset"]) ? $default_settings["offset"] : '0' )
);

/*
$this->table->add_row(
	form_label("Import in batches") .
	'<div class="subtext">Limits the size of the import when you have memory or time-out difficulties</div>', 
	form_input("limit", 
	 	isset($default_settings["limit"]) ? $default_settings["limit"] : '0' )
);
*/

echo $this->table->generate();
echo $this->table->clear();

?>

<input type="submit" value="Do Import" class="submit" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="<?php echo $back_link ?>">Back to Settings</a>

<?php echo form_close(); ?>
