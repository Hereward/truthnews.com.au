<?php

define('DATAGRAB_URL', BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ajw_datagrab');
define('DATAGRAB_PATH', 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ajw_datagrab');

/**
 * DataGrab MCP Class
 *
 * DataGrab Module Control Panel class to handle all CP requests
 * 
 * @package   DataGrab
 * @author    Andrew Weaver <aweaver@brandnewbox.co.uk>
 * @copyright Copyright (c) Andrew Weaver
 */
class Ajw_datagrab_mcp {
	
	var $version = '1.6.1';
	var $module_name = "AJW_Datagrab";
	
	var $settings;
	
	function Ajw_datagrab_mcp() {
		$this->EE =& get_instance();
		
		$this->EE->load->model('datagrab_model', 'datagrab');
		
		// Global right hand side navigation
		$this->EE->cp->set_right_nav(array(
			'Documentation' => "http://brandnewbox.co.uk/products/datagrab/"
		));
		
		// Set breadcrumb globally
		$this->EE->cp->set_breadcrumb( DATAGRAB_URL, $this->EE->lang->line('ajw_datagrab_module_name') );
		
	}
	
	/*
	
	CONTROLLER FUNCTIONS
	
	*/
	
	function index() {
		
		// Clear session data
		$this->_get_session('settings');

		// Set page title
		$this->EE->cp->set_variable('cp_page_title', "DataGrab" );
		
		// Load helpers
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		$this->EE->load->library('javascript'); 
		
		// Round buttons
		$this->EE->javascript->output($this->EE->jquery->corner('.cp_button a')); 
		$this->EE->javascript->compile(); 

		// Set data
		$data["title"] = "DataGrab";
		$data["content"] = 'index';
		
		$data["types"] = $this->EE->datagrab->fetch_datatype_names();

		$this->EE->db->select('id, name, description');
		$this->EE->db->where('site_id', $this->EE->config->item('site_id') );
		$query = $this->EE->db->get('exp_ajw_datagrab');
		$data["saved_imports"] = array();
		foreach($query->result_array() as $row) {
			$id = $row["id"];
			$row["name"] = '<a href="'.DATAGRAB_URL.AMP.'method=save'.AMP.'id='.$row["id"].'">' . $row["name"] . '</a>';
			$row[] = '<a href="'.DATAGRAB_URL.AMP.'method=load'.AMP.'id='.$row["id"].'">Configure</a>';
			$row[] = '<a href="'.DATAGRAB_URL.AMP.'method=run'.AMP.'id='.$row["id"].'">Run</a>';
			$row[] = '<a href="'.DATAGRAB_URL.AMP.'method=delete'.AMP.'id='.$row["id"].'">Delete</a>';
			$data["saved_imports"][ $id ] = $row;
		}

		$data["form_action"] = DATAGRAB_PATH.AMP.'method=settings';

		$data["action_url"] = $this->EE->functions->fetch_site_index(0, 0) . QUERY_MARKER . 'ACT=' . $this->EE->cp->fetch_action_id('Ajw_datagrab', 'run_action') . AMP . "id=";
		
		// Load view
		return $this->EE->load->view('_wrapper', $data, TRUE);
	}

	function settings() {
		
		// Handle form input
		$this->_get_input();

		// Set page title
		$this->EE->cp->set_variable('cp_page_title', "Settings" );
		
		// $this->cp->add_to_head('<style type="text/css">.tablesize{height:45px!important;}</style>');
		
		// Load helpers
		$this->EE->load->library('table');
		$this->EE->load->helper('form');

		// Set data
		$data["title"] = "Settings";
		$data["content"] = 'settings';
		
		// Fetch channel name
		$this->EE->db->select('channel_id, channel_title');
		$this->EE->db->where('site_id', $this->EE->config->item('site_id') );
		$query = $this->EE->db->get('exp_channels');
		$data["channels"] = array();
		foreach($query->result_array() as $row) {
			$data["channels"][$row["channel_id"]] = $row["channel_title"];
		}
		$data["channel"] = isset( $this->settings["channel"] ) ? $this->settings["channel"] : '';
		
		// Get settings form for type
		$data["settings"] = $this->EE->datagrab->datatypes[ $this->settings["type"] ]->settings_form( $this->settings );

		// Form action URL
		$data["form_action"] = DATAGRAB_PATH.AMP.'method=check_settings';
		
		// Load view
		return $this->EE->load->view('_wrapper', $data, TRUE);
	}

	function check_settings() {

		// Handle form input
		$this->_get_input();

		// Set page title
		$this->EE->cp->set_variable('cp_page_title', "Check Settings" );

		// Load helpers
		$this->EE->load->library('table');
		$this->EE->load->helper('form');

		// Set data
		$data["title"] = "Check Settings";
		$data["content"] = 'check_settings';

		$data["rows"] = array();
		$data["errors"] = array();

		$this->EE->datagrab->datatypes[ $this->settings["type"] ]->initialise( $this->settings );
		$ret = $this->EE->datagrab->datatypes[ $this->settings["type"] ]->fetch();
		if( $ret != -1 ) {
			$titles = $this->EE->datagrab->datatypes[ $this->settings["type"] ]->fetch_columns();
			if( $titles != "" ) {
				foreach( $titles as $key => $value ) {
					$data["rows"][] = array( $value );
				}
			}
		} else {
			$data["errors"] = $this->EE->datagrab->datatypes[ $this->settings["type"] ]->errors;
		}

		// Form action URL
		$data["form_action"] = DATAGRAB_PATH.AMP.'method=configure_import';

		// Load view
		return $this->EE->load->view('_wrapper', $data, TRUE);
	}

	function configure_import() {
		
		// Handle form input
		
		$this->_get_input();
	
		// Set page title
		
		$this->EE->cp->set_variable('cp_page_title', "Configure Import" );

		// Load helpers
		
		$this->EE->load->library('table');
		$this->EE->load->helper('form');

		// Set data
		
		$data["title"] = "Configure Import";
		$data["content"] = 'configure_import';

		// Get custom fields for the selected channel
		
		$this->EE->db->select("field_group, cat_group");
		if( is_numeric($this->settings["channel"]) ) {
			$this->EE->db->where( 'channel_id', $this->settings["channel"] );
		} else {
			$this->EE->db->where( 'channel_name', $this->settings["channel"] );
			$this->EE->db->where('site_id', $this->EE->config->item('site_id') );
		}
		$query = $this->EE->db->get( 'exp_channels' );
		$row = $query->row_array();
		$field_group = $row["field_group"];
		$cat_group = $row["cat_group"];
	
		$this->EE->db->select( 'field_name, field_label, field_type, field_settings' );
		$this->EE->db->where( 'group_id', $field_group );
		$this->EE->db->order_by( 'field_order' );
		$query = $this->EE->db->get( 'exp_channel_fields' );
		
		$data["custom_fields"] = array();
		$data["unique_fields"] = array();
		$data["field_settings"] = array();
		$data["unique_fields"][ "" ] = "";
		$data["unique_fields"][ "title" ] = "Title";
		$data["field_types"] = array();

		if( $query->num_rows() > 0 ) {
			foreach( $query->result_array() as $row ) {
				$data["custom_fields"][ $row["field_name"] ] = $row["field_label"];
				$data["unique_fields"][ $row["field_name"] ] = $row["field_label"];
				$data["field_types"][ $row["field_name"] ] = $row["field_type"];
				$data["field_settings"][ $row["field_name"] ] = unserialize(base64_decode( $row["field_settings"] ));
			}
		}

		$this->EE->db->select( 'field_name, field_label' );
		$this->EE->db->where( 'group_id', $field_group );
		$query = $this->EE->db->get( 'exp_channel_fields' );
		
		// Get category groups
		
		$this->EE->db->select( 'group_id, group_name' );
		$this->EE->db->where_in( 'group_id', explode( "|", $cat_group ) );
		$query = $this->EE->db->get( 'exp_category_groups' );
		
		$data["category_groups"] = array();
		// $data["category_groups"][ 0 ] = "";
		if( $query->num_rows() > 0 ) {
			foreach( $query->result_array() as $row ) {
				$data["category_groups"][ $row["group_id"] ] = $row["group_name"];
			}
		}
		
		// Get list of fields from the datatype

		$this->EE->datagrab->datatypes[ $this->settings["type"] ]->initialise( $this->settings );
		$this->EE->datagrab->datatypes[ $this->settings["type"] ]->fetch();
		$data["data_fields"][""] = "";
		$fields = $this->EE->datagrab->datatypes[ $this->settings["type"] ]->fetch_columns();
		foreach( $fields as $key => $value ) {
			$data["data_fields"][ $key ] = $value;
		}

		// Get list of authors
		// @todo: filter this list by member groups
		
		$data["authors"] = array();

		$this->EE->db->select( 'member_id, screen_name' );
		// $this->EE->db->where( 'group_id', "1" );
		$query = $this->EE->db->get( 'exp_members' );
		if( $query->num_rows() > 0 ) {
			foreach( $query->result_array() as $row ) {
				$data["authors"][ $row["member_id"] ] = $row["screen_name"];
			}
		}
		
		$data["author_fields"] = array(
			"member_id" => "ID",
			"username" => "Username",
			"screen_name" => "Screen Name",
			"email" => "Email address"
		);
		
		// Get statuses
		
		$data["status_fields"] = array(
			"default" => "Channel default",
			"open" => "Open",
			"closed" => "Closed"
		);
		
		$data["status_fields"] = array_merge( $data["status_fields"], $data["data_fields"] );
		
		// Allow comments - check datatype ?
		
		$allow_comments = isset( $this->EE->datagrab->datatypes[ $this->settings["type"] ]->datatype_info["allow_comments"] ) ? 
			$this->EE->datagrab->datatypes[ $this->settings["type"] ]->datatype_info["allow_comments"] : FALSE;

		// $this->EE->cp->load_package_js('ajw_datagrab');

		if( $allow_comments ) {
			
			$data["allow_comments"] = TRUE;

		} else {

			$data["allow_comments"] = FALSE;

		}
		
		// Solspace Tags
		if (array_key_exists('tag', $this->EE->addons->get_installed('modules'))) {
			$data["tags_installed"] = TRUE;
		} else {
			$data["tags_installed"] = FALSE;
		}
		
		// P&T Matrix
		/*
		if (array_key_exists('matrix', $this->EE->addons->get_installed('fieldtypes'))) {
			$this->EE->db->select( "col_id, col_label" );
			$query = $this->EE->db->get( "exp_matrix_cols" );
			$data["matrix_columns"] = array();
			foreach( $query->result_array() as $row ) {
				$data["matrix_columns"][ $row["col_id"] ] = $row["col_label"];
			}
		}
		*/
		
		// Cartthrob
		/*
		if (array_key_exists('cartthrob', $this->EE->addons->get_installed('modules'))) {
			$data["ctpm_columns"] = array();
			$data["ctpm_columns"][ "option_value" ] = "Value";
			$data["ctpm_columns"][ "option_name" ] = "Name";
			$data["ctpm_columns"][ "price" ] = "Price";
		}
		*/
		
		// SEO Lite
		if (array_key_exists('seo_lite', $this->EE->addons->get_installed('modules'))) {
			$data["seo_lite_installed"] = TRUE;
		} else {
			$data["seo_lite_installed"] = FALSE;
		}
		
		$this->EE->db->select( 'field_id, field_label, group_name' );
		$this->EE->db->join( 'exp_field_groups', 'exp_field_groups.group_id = exp_channel_fields.group_id' );
		$this->EE->db->order_by( 'group_name, 	field_order' );
		$query = $this->EE->db->get( 'exp_channel_fields' );
		
		$data["all_fields"] = array();
		$data["all_fields"]["title"] = "Title";
		$data["all_fields"]["exp_channel_titles.entry_id"] = "Entry ID";

		if( $query->num_rows() > 0 ) {
			foreach( $query->result_array() as $row ) {
				$data["all_fields"][ $row["group_name"] ][ "field_id_".$row["field_id"] ] = $row["field_label"];
			}
		}
		
		
		// Default settings
		
		if( isset ( $this->EE->datagrab->datatypes[ $this->settings["type"] ]->config_defaults ) ) {
			foreach( $this->EE->datagrab->datatypes[ $this->settings["type"] ]->config_defaults as $field => $value ) {
				if( !isset( $this->settings[ $field ] ) ) {
					$this->settings[ $field ] = $value;
				}
			}
		}
		$data['default_settings'] = $this->settings;
		
		// Form action URL
		
		$data["form_action"] = DATAGRAB_PATH.AMP.'method=import';

		$data["back_link"] = DATAGRAB_URL.AMP.'method=settings';

		// Load view
		return $this->EE->load->view('_wrapper', $data, TRUE);
	}


	function import() {
		
		$this->_get_input();

		// Set page title
		$this->EE->cp->set_variable('cp_page_title', "Results" );

		// Load helpers
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		$this->EE->load->library('javascript');

		// Round buttons
		$this->EE->javascript->output($this->EE->jquery->corner('.cp_button a')); 
		$this->EE->javascript->compile(); 

		// Set data
		$data["title"] = "Results";
		$data["content"] = 'results';
		
		$this->settings = array_merge( $this->settings, $_POST );

		// Allow modifications via get variables
		if( $this->EE->input->get('skip') !== FALSE ) {
			$this->settings["skip"] = $this->EE->input->get('skip');
		}
		if( $this->EE->input->get('limit') !== FALSE ) {
			$this->settings["limit"] = $this->EE->input->get('limit');
		}
		
		$data["results"] = $this->EE->datagrab->do_import( 
			$this->EE->datagrab->datatypes[ $this->settings["type"] ], 
			$this->settings 
			);

		// Set variables for batch imports
		$data["batch"] = $this->EE->datagrab->batch_limit_completed;
		$data["skip"] = isset($this->settings["skip"]) ? $this->settings["skip"] : 0;
		$data["limit"] = isset($this->settings["limit"]) ? $this->settings["limit"] : "";
		$data["batch_action"] = DATAGRAB_URL.AMP.'method=import';
		$data['cp_theme_url'] = $this->EE->config->slash_item('theme_folder_url').'cp_themes/default/';

		// Form action URL
		if( isset( $this->settings["id"] ) ) {
			$data["id"] = $this->settings["id"];
		} else {
			$data["id"] = 0;
		}
		
		// Form action URL
		$data["form_action"] = DATAGRAB_PATH.AMP.'method=save';
		
		// Load view
		return $this->EE->load->view('_wrapper', $data, TRUE);
		
	}

	function save() {
		
		$id = $this->EE->input->get_post("id");
		
		$this->_get_input();
		
		// Load helpers
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		$this->EE->load->library('javascript');

		// Round buttons
		$this->EE->javascript->output($this->EE->jquery->corner('.cp_button a')); 
		$this->EE->javascript->compile(); 

		// Set data
		if ( $id == 0 ) {
			
			$this->EE->cp->set_variable('cp_page_title', "Save import" );
			$data["title"] = "Save import";
			$name = "";
			$description = "";

		} else {

			$this->EE->cp->set_variable('cp_page_title', "Update import" );
			$data["title"] = "Update import";
			
			$this->EE->db->where('id', $id );
			$query = $this->EE->db->get('exp_ajw_datagrab');
			$row = $query->row_array();
			
			$name = $row["name"];
			$description = $row["description"];
			
		}
		
		$data["content"] = 'save';
		
		$data["form"] = array(
		array( 
			form_label('Name', 'name'), 
			form_input(
				array(
					'name' => 'name',
					'id' => 'name',
					'value' => $name,
					'size' => '50'
					)
				) 
			),
		array( 
			form_label('Description', 'description'), 
			form_textarea(
				array(
					'name' => 'description',
					'id' => 'description',
					'value' => $description,
					'rows' => '4',
					'cols' => '64'
					)
				)
			)
		);

		$data["id"] = $id;
		
		// Form action URL
		$data["form_action"] = DATAGRAB_PATH.AMP.'method=do_save';
		
		// Load view
		return $this->EE->load->view('_wrapper', $data, TRUE);
	}

	function do_save() {

		$this->_get_input();

		$this->EE->load->helper('date');

		$id = $this->EE->input->post("id");

		$data = array(
			'name' => $this->EE->input->post( "name" ),
			'description' => $this->EE->input->post( "description" ),
			'last_run' => now()
		);
		
		if( isset( $this->settings["type"] ) ) {
			$data['settings'] = serialize( $this->settings );
		} else {
			// Fetch settings from database
			$this->EE->db->select('settings');
			$this->EE->db->where('id', $id);
			$query = $this->EE->db->get('exp_ajw_datagrab');
			$row = $query->row_array();
			$data['settings'] = $row["settings"];
			$this->settings = unserialize( $data['settings'] );
		}

		// Get site_id from channel label
		$this->EE->db->select('site_id');
		if( is_numeric($this->settings["channel"]) ) {
			$this->EE->db->where( 'channel_id', $this->settings["channel"] );
		} else {
			$this->EE->db->where( 'channel_name', $this->settings["channel"] );
			$this->EE->db->where('site_id', $this->EE->config->item('site_id') );
		}
		$query = $this->EE->db->get('exp_channels');
		$channel_defaults = $query->row_array();
		$data["site_id"] = $channel_defaults["site_id"];
		
		if( $id == "" OR $id == "0" ) {
			$this->EE->db->insert('exp_ajw_datagrab', $data);
		} else {
			$this->EE->db->where('id', $id );
			$this->EE->db->update('exp_ajw_datagrab', $data);	
		}

		$this->EE->session->set_flashdata('message_success', "Import saved.");

		$this->EE->functions->redirect(DATAGRAB_URL.AMP."method=index"); 
		
	}

	function load() {

		if ( $this->EE->input->get( "id" ) != 0 ) {
			$this->EE->db->where('id', $this->EE->input->get( "id" ) );
			$query = $this->EE->db->get('exp_ajw_datagrab');
			$row = $query->row_array();
			$this->settings = unserialize($row["settings"]);
			$this->settings["id"] = $this->EE->input->get( "id" );
			$this->_set_session( 'settings', serialize( $this->settings ) );
		}
				
				
		$this->EE->functions->redirect(DATAGRAB_URL.AMP."method=configure_import"); 
	}

	function run() {

		if ( $this->EE->input->get( "id" ) != 0 ) {
			$this->EE->db->where('id', $this->EE->input->get( "id" ) );
			$query = $this->EE->db->get('exp_ajw_datagrab');
			$row = $query->row_array();
			$this->settings = unserialize($row["settings"]);
			$this->settings["id"] = $this->EE->input->get( "id" );
			$this->_set_session( 'settings', serialize( $this->settings ) );
		}

		$this->EE->functions->redirect(DATAGRAB_URL.AMP."method=import"); 
	}

	function delete() {
		
		$id = $this->EE->input->get( "id" );

		// Set page title
		$this->EE->cp->set_variable('cp_page_title', "Confirm delete" );

		// Load helpers
		$this->EE->load->helper('form');
		$this->EE->load->library('javascript');

		// Round buttons
		$this->EE->javascript->output($this->EE->jquery->corner('.cp_button a')); 
		$this->EE->javascript->compile(); 

		// Set data
		$data["title"] = "Confirm delete";
		$data["content"] = 'delete';
		
		$data["id"] = $id;
		
		// Form action URL
		$data["form_action"] = DATAGRAB_PATH.AMP.'method=do_delete';
		
		// Load view
		return $this->EE->load->view('_wrapper', $data, TRUE);
		
	}

	function do_delete() {
		
		$id = $this->EE->input->post("id");

		if( $id != "" && $id != "0" ) {
			$this->EE->db->where('id', $id );
			$this->EE->db->delete('exp_ajw_datagrab');	
		}
		
		$this->EE->session->set_flashdata('message_success', "Deleted");

		$this->EE->functions->redirect(DATAGRAB_URL.AMP."method=index");
		
	}

	/* 
	
	HELPER FUNCTIONS
	
	*/

	/**
	 * Add $data to user session
	 *
	 * @param string $key 
	 * @param string $data 
	 * @return void
	 */
	function _set_session( $key, $data ) {
		@session_start();
		if ( !isset( $_SESSION[ $this->module_name ] ) ) {
			$_SESSION[ $this->module_name ] = array();
		}
		$_SESSION[ $this->module_name ][ $key ] = $data;
	}

	/**
	 * Retrieve data from session. Data is removed from session unless $keep is
	 * set to TRUE
	 *
	 * @param string $key 
	 * @param string $keep 
	 * @return void $data
	 */
	function _get_session( $key, $keep = FALSE ) {
		@session_start();  
		if( isset( $_SESSION[ $this->module_name ] ) ) {
			if( isset( $_SESSION[ $this->module_name ][ $key ] ) ) {
				$data = $_SESSION[ $this->module_name ][ $key ];
				if ( $keep != TRUE ) {
		    	unset($_SESSION[ $this->module_name ][ $key ]); 
		    	unset($_SESSION[ $this->module_name ]); 
				}
				return( $data );
			}
		}
		return "";
	}

	/**
	 * Handle input from forms, sessions
	 * 
	 * Collects data from forms, query strings and sessions. Only keeps relevant data
	 * for the current import data type. Stores in session to allow back-and-forth
	 * through 'wizard'
	 *
	 */
	function _get_input() {
		
		// Get current settings from session
		$this->settings = unserialize( $this->_get_session( 'settings' ) );
	
		$allowed_settings = array(
			"type",
			"channel",
			"update",
			"unique",
			"author",
			"author_field",
			"author_check",
			"offset",
			"limit",
			"title",
			"url_title",
			"date",
			"expiry_date",
			"timestamp",
			"delete_old",
			"category_value",
			"cat_field",
			"cat_group",
			"cat_delimiter",
			"id",
			"status",
			"import_comments",
			"comment_author",
			"comment_email",
			"comment_date",
			"comment_url",
			"comment_body",
			"ajw_entry_id"
			);
	
		// Look through permitted settings, check whether a new POST var exists, and update
		foreach( $allowed_settings as $setting ) {
			if( $this->EE->input->post( $setting ) !== FALSE ) {
				$this->settings[ $setting ] = $this->EE->input->post( $setting );
			}
		}

		// Hack to handle checkboxes (whose post vars are not set if unchecked)
		if( $this->EE->input->get("method") == "import" ) {
			$checkboxes = array("update", "delete_old", "import_comments");
			foreach( $checkboxes as $check ) {
				if( !isset( $this->settings[ $check ] ) ) {
					$this->settings[ $check ] = $this->EE->input->post( $check );
				}
			}
		}

		// Check datatype specific settings
		if( isset( $this->settings["type"] ) && $this->settings["type"] != "" ) {

			$this->EE->datagrab->initialise_types();
			$datatype_settings = $this->EE->datagrab->datatypes[ $this->settings["type"] ]->settings;

			foreach( $datatype_settings as $option => $value ) {
				if( $this->EE->input->post( $option ) !== FALSE ) {
					$this->settings[ $option ] = $this->EE->input->post( $option );
				}
			}

		}

		// Check for custom field settings
		if( isset($this->settings["channel"]) && $this->settings["channel"] != "" ) {
			
			$this->EE->db->select('field_name');
			$this->EE->db->from('exp_channel_fields');
			$this->EE->db->join('exp_channels', 'exp_channels.field_group = exp_channel_fields.group_id');
			if( is_numeric($this->settings["channel"]) ) {
				$this->EE->db->where( 'channel_id', $this->settings["channel"] );
			} else {
				$this->EE->db->where( 'channel_name', $this->settings["channel"] );
				// $this->EE->db->where('site_id', $this->EE->config->item('site_id') );
			}
			$query = $this->EE->db->get();

			// todo: put these settings somewhere else - fields can clash with settings
			foreach ( $query->result_array() as $row ) {

				if( $this->EE->input->post( $row["field_name"] ) !== FALSE ) {
					$this->settings[ $row["field_name"] ] = $this->EE->input->post( $row["field_name"] );
				}

				// todo: abstract this into Datagrab_fieldtype
				if( $this->EE->input->post( $row["field_name"] . "_playa_field" ) !== FALSE ) {
					$this->settings[ $row["field_name"]. "_playa_field" ] = $this->EE->input->post( $row["field_name"]. "_playa_field" );
				}

				/*
				if( $this->EE->input->post( $row["field_name"] . "_matrix" ) !== FALSE ) {
					$this->settings[ $row["field_name"]. "_matrix" ] = $this->EE->input->post( $row["field_name"]. "_matrix" );
				}

				if( $this->EE->input->post( $row["field_name"] . "_matrix_update" ) !== FALSE ) {
					$this->settings[ $row["field_name"]. "_matrix_update" ] = $this->EE->input->post( $row["field_name"]. "_matrix_update" );
				}

				if( $this->EE->input->post( $row["field_name"] . "_ctpm_option_name" ) !== FALSE ) {
					$this->settings[ $row["field_name"]. "_ctpm_option_name" ] = $this->EE->input->post( $row["field_name"]. "_ctpm_option_name" );
				}

				if( $this->EE->input->post( $row["field_name"] . "_ctpm_option_value" ) !== FALSE ) {
					$this->settings[ $row["field_name"]. "_ctpm_option_value" ] = $this->EE->input->post( $row["field_name"]. "_ctpm_option_value" );
				}

				if( $this->EE->input->post( $row["field_name"] . "_ctpm_price" ) !== FALSE ) {
					$this->settings[ $row["field_name"]. "_ctpm_price" ] = $this->EE->input->post( $row["field_name"]. "_ctpm_price" );
				}
				*/

			}
			
		}

		// Get saved import id
		if( $this->EE->input->get( "id" ) !== FALSE ) {
			$this->settings[ "id" ] = $this->EE->input->get( "id" );
		}

		// Store settings in session
		$this->_set_session( 'settings', serialize( $this->settings ) );
	}

}

/* End of file mcp.ajw_datagrab.php */