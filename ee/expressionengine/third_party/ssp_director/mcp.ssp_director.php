<?php if (! defined('BASEPATH')) exit('Invalid file request');



class ssp_director_mcp {

	var $update_fields;
	var $config;
	var $inited = FALSE;
	var $base_query_string;
	var $base_url;
	var $cp_base_url;

	function __construct()
	{
		$this->EE =& get_instance();
		
		$this->cp_base_url = dirname($this->EE->config->item('cp_url'));
                $main_db_name = $this->EE->config->item('database');
                $this->base_url = $this->cp_base_url.'/'.html_entity_decode(BASE);
		//die("FULL BASE URL = ".$this->base_url);
		
		if (!$this->inited) {
			//ini_set('include_path',ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/includes/pear');
			//require_once('HTML/QuickForm.php');
			$this->inited = TRUE;
			//$this->wl_lookup[0] = '_ALL';
		}
		
		if (isset($this->EE->cp))
		{
			$this->base_query_string = 'C=addons_modules&M=show_module_cp&module=ssp_director';
		}
		$this->EE->load->library('javascript'); 
		$this->EE->load->library('table');
        //$this->EE->load->helper('xml');
		$this->config = $this->EE->db->get('exp_ssp_director',1);
		
		$this->update_fields = array(
         'api_key'=>'API Key',
         'ssp_path'=>'SSP path',
         'ssp_url'=>'SSP URL',
         'ssp_url_fe'=>'Front End URL',
         'ssp_cache_time'=>'Cache time (eg "1 day")',
         'host'=>'DB Host',
         'user'=>'DB User',
         'password'=>'DB pass',
         'database'=>'Database Name'
         );
         
         //die("APPPATH = [".APPPATH."]");
		
	}

	private function _set_page_title($line = 'ssp_director_module_name')
	{
		
		if ($line != 'ssp_director_module_name')
		{
			$this->EE->cp->set_breadcrumb(BASE.AMP.$this->base_query_string, $this->EE->lang->line('ssp_director_module_name'));
		}
		

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line($line));
	}
	
	function ssp_init() {
		//$this->config = $this->get_config();
		$this->system_path = BASEPATH;
		
		$this->root_url = $this->EE->config->item('site_url'); //"http://{$_SERVER['SERVER_NAME']}";
		$this->module_path = PATH_THIRD.'ssp_director';
		//$this->cp_url = $PREFS->ini('cp_url');
		$this->cp_url = $this->EE->config->item('cp_url');
		
		//$this->system_base_url = dirname($this->cp_url);
		$this->module_view_url = dirname($this->cp_url).'/expressionengine/third_party/ssp_director/views';
		$this->ssp_api_url = $this->ssp_api_url();
		//die($this->ssp_api_url);
		

	}


	function index()
	{
		$this->_set_page_title();
		$vars = array();
		return $this->EE->load->view('index', $vars, TRUE);
	}
	
	

	function edit_config() {
		$vars = array();
		$this->_set_page_title('config_page_name');
		$this->EE->load->helper('form');
		$this->EE->load->library('table');

		$query = $this->EE->db->get('exp_ssp_director',1);
		//$results = $this->EE->db->query("SELECT * FROM exp_ssp_director  LIMIT 0,1");

		if ($query->num_rows() == 0) {
			$mini = array();
			foreach ($this->update_fields as $key=>$value) {
				$mini[$key] = "foobar";
			}
			$vars['config'] = $mini;
		} else {
			$res_array = $query->result_array();
			$vars['config'] = $res_array[0];
		}

		$vars['action_url'] = 'C=addons_modules&M=show_module_cp&module=ssp_director&method=update_config';
		$vars['fields'] = $this->update_fields;

		$vars['form_hidden'] = NULL;

		return $this->EE->load->view('config', $vars, TRUE);

	}

	function update_config() {
		$this->_set_page_title('config_update_page_name');
		$vars = array();
		//$this->EE->load->library('javascript');


		$type = '';

		$query = $this->EE->db->get('exp_ssp_director',1);

		if ($query->num_rows() == 0) {
			$data = $this->get_sql_fields('insert');
			$sql = $this->EE->db->insert_string('exp_ssp_director', $data);
			//die("SQL (INSERT) = $sql");
			$this->EE->db->query($sql);

		} else {
		   $data = $this->get_sql_fields('update');
           $sql = $this->EE->db->update_string('exp_ssp_director', $data, "active = 'yes'");
           $this->EE->db->query($sql);

		}

		$this->redirect_after_update();

		return $this->EE->load->view('update_config', $vars, TRUE);
	}


	function redirect_after_update() {
		$target_url = $this->base_url.'&'.$this->base_query_string;
		//trigger_error("TARGET URL=$target_url");
		//$this->EE->load->library('javascript');
		$this->EE->javascript->output(array(
		 "setTimeout('window.location=\'$target_url\'', 1000);"
		));
		//var target = "$target_url";
		//\'/system/index.php?S=0&D=cp&C=addons_modules\'
		$this->EE->javascript->compile();
	}

	function get_sql_fields($type) {
		
		if (count($_POST)==0) {
			die("POST ARRAY CONTAINED NO DATA");
		}
		$output = array();
		
		foreach ($this->update_fields as $key=> $val) {
			$data = (isset($_POST[$key]))?$_POST[$key]:'boojam';
			$output["`$key`"]=$data;
		}
		if ($type == 'insert') {
			$output['active'] = 'yes';
		}
		return $output;
	}
	
	function show_albums() {

		$this->_set_page_title('show_albums_page_name');
		$this->ssp_init();
		$this->ssp_director_init();
        //$alerts = '';
		//$search_form = $this->search_form();
		
        if (isset($_POST['general_search']) && !$_POST['general_search']) {$this->pagination->clear_cookie();}
		$search_default = (isset($_POST['general_search']))?$_POST['general_search']:$this->pagination->get_search_term();
		$search_data = array(
          'input_name'=>'general_search',
          'submit_name'=>'submit_album_search',
          'action_url'=>$this->base_url.'&C=addons_modules&M=show_module_cp&module=ssp_director&method=show_albums',
          'search_default'=>$search_default
          );
		$search_form = $this->EE->load->view('search_form', $search_data, TRUE);
        $path_data = array('module_view_url'=>$this->module_view_url);
        // $date_js = $this->_dsp->view('date_js', $path_data, TRUE);
        $date_js = $this->EE->load->view('date_js', $path_data, TRUE);
        
		$tv_data = array('director'=>$this->director, 
          'alerts'=>'', 
          'db_config'=>$this->config, 
          'Pagination'=>$this->pagination,
          'module_view_url'=>$this->module_view_url,
          'title'=>$this->EE->lang->line('ssp_director_name'), 
          'root_url'=>$this->root_url,
          'db'=>$this->EE->db,
          'main_db_name'=>$this->EE->db->database
          );
          
        //$this->_dsp->extra_header .= $this->_dsp->view('extra_header', $path_data, TRUE);
       $table_view = $this->EE->load->view('table_view', $tv_data, TRUE);
       $sa_data = array(
            'date_js'=>$date_js, 
            'search_form'=>$search_form,
            'table_view'=>$table_view
        );
       $this->EE->cp->add_to_head($this->EE->load->view('extra_header', $path_data, TRUE));
       return $this->EE->load->view('show_albums', $sa_data, TRUE);

	}
	
	function ssp_api_url() {
		return str_replace(array('http://','https://'), '', $this->config->row('ssp_url'));
	}
	
	/*
	function show_albums_js() {
		$target_url = BASE.AMP.$this->base_query_string;

		$this->EE->javascript->output(array(
		 "alert('URL=$target_url'); setTimeout('window.location=$target_url', 1000);"
		));
		$this->EE->javascript->compile();
	}
	*/
	
	function search_form() {
		$form = $this->form_create('index.php?S=0&C=modules&M=ssp_director&P=show_albums');
		
		$form->setDefaults(
	        array(
            'general_search' => $this->pagination->get_search_term()
	        )
	    );
		$form->addElement('text', 'general_search', 'Search:', "id='album_name'");
		$form->addElement('submit', 'submit_album_search',' GO! ');
		
		$form_data = $form->toHtml();
		return $form_data;
		
	}
	
	function form_create($def_target='') {
		$target = 'index.php?S=0&C=modules&M=ssp_director';
		if ($def_target) {
			$target = $def_target;
		}
		
		$member_id = $this->EE->session->userdata('member_id');
		
		$form = new HTML_QuickForm('mf_query', 'post', $target);
		//$form->setDefaults(array('name' => 'Joe User'));
		//$form->addElement('html',"<table class='cg_meta_filter'><tr valign='top'>");
		$form->addElement('hidden', 'member_id', $member_id);
		
		return $form;
	}
	
	function ssp_director_init() {
		require_once ("{$this->config->row('ssp_path')}/api/classes/DirectorPHP.php");
		$this->director = new Director($this->config->row('api_key'), $this->ssp_api_url);
		$this->director->cache->set('cg_gallery',"+5 minutes");

		# When your application is live, it is a good idea to enable caching.
		# You need to provide a string specific to this page and a time limit 
		# for the cache. Note that in most cases, Director will be able to ping
		# back to clear the cache for you after a change is made, so don't be 
		# afraid to set the time limit to a high number.
		# 
		# $director->cache->set('myrandomstring', '+30 minutes');

		# What sizes do we want?
		$this->director->format->add(array (
			'name' => 'thumb',
			'width' => '104',
			'height' => '71',
			'crop' => 1,
			'quality' => 75,
			'sharpening' => 1
		));
		$this->director->format->add(array (
			'name' => 'large',
			'width' => '665',
			'height' => '456',
			'crop' => 0,
			'quality' => 95,
			'sharpening' => 1
		));
		
	   require_once("$this->module_path/views/Pagination.php");

       $this->pagination = new Pagination($this->module_view_url);
	}
	

	 function dump($obj) {
        ob_start();
        var_dump($obj);
        $ret_val = ob_get_contents();
        ob_end_clean();
        return $ret_val;
        //return "<textarea rows='20' cols='150'>$ret_val</textarea>";
    }

}



// END

