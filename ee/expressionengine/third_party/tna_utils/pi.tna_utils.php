<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Tna_utils {
    
	function __construct()
	{
		$this->EE =& get_instance();
		/*$this->return_data = "Hello World";*/

	}

	function get_media_properties() {
        die("cp_url dirname = ".dirname($this->EE->config->item('cp_url')));
		error_reporting(E_ALL & ~E_DEPRECATED);
		@ini_set('display_errors', 1);
		//die(APPPATH);
		//die($this->EE->config->item('system_path'));
		require_once("{$_SERVER['DOCUMENT_ROOT']}/includes/getid3/getid3.php");
		//require_once("{$_SERVER['DOCUMENT_ROOT']}/includes/getid3/getid3.lib.php");
        require_once ("{$_SERVER['DOCUMENT_ROOT']}/includes/getid3/extension.cache.mysql.php");
        
        $db = array();
        //die(APPPATH.'config/database.php');
        include(APPPATH.'config/database.php');
        //var_dump($db);
        //die($db['expressionengine']['database']);
        $getID3 = new getID3_cached_mysql($db['expressionengine']['hostname'], $db['expressionengine']['database'], $db['expressionengine']['username'], $db['expressionengine']['password']);
		
       // $getID3 = new getID3_cached_dbm('db2', "{$_SERVER['DOCUMENT_ROOT']}/includes/getid3/cache/getid3_cache.dbm","{$_SERVER['DOCUMENT_ROOT']}/includes/getid3/cache/getid3_cache.lock");
        $getID3->encoding = 'UTF-8';
		//getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'extension.cache.dbm.php', __FILE__, true);

		//$filename = $this->EE->TMPL->fetch_param('filename');
		$media_source = $this->EE->TMPL->fetch_param('media_source');
		$media_date = $this->EE->TMPL->fetch_param('media_date');
		$segment = $this->EE->TMPL->fetch_param('segment');
		$seg_suffix = '';
		if ($segment > 1) {
			$seg_suffix = "_$segment";
		}
		$filename = $media_source.'_'.$media_date.$seg_suffix.'.mp3';
		$param_path = $this->EE->TMPL->fetch_param('path');
		$duration = '';
		//$getID3 = new getID3;
		//return 3600;
		
		
		
		$full_path = '';
		if ($param_path) {
			$full_path = "{$_SERVER['DOCUMENT_ROOT']}/$param_path";
		} else {
			$full_path = "{$_SERVER['DOCUMENT_ROOT']}/radio/export/$filename";
		}
		//return 3600;
		$info = $getID3->analyze($full_path);
		if (array_key_exists('playtime_seconds', $info)) {
			$duration = $info['playtime_seconds'];
		}
		return round($duration);

	}

	function get_media_url() {
		$media_source = $this->EE->TMPL->fetch_param('media_source');
		$media_date = $this->EE->TMPL->fetch_param('media_date');
		$segment = $this->EE->TMPL->fetch_param('segment');
		// echo "[SEGMENT = $segment]";
		//$site_url = $this->EE->config->item('site_url');
		$site_url = "http://www.truthnews.com.au/web/radio/download/";
		$seg_suffix = '';
		if ($segment > 1) {
			$seg_suffix = "_$segment";
		}
		$full_url = $site_url.$media_source.'_'.$media_date.$seg_suffix;
		return $full_url;
	}

	function get_media_url_direct() {
		$media_source = $this->EE->TMPL->fetch_param('media_source');
		$media_date = $this->EE->TMPL->fetch_param('media_date');
		$segment = $this->EE->TMPL->fetch_param('segment');
		// echo "[SEGMENT = $segment]";
		$site_url =  $this->EE->config->item('site_url');
		//$site_url = "http://www.truthnews.com.au/web/radio/download/";
		$seg_suffix = '';
		if ($segment > 1) {
			$seg_suffix = "_$segment";
		}
		$full_url = $site_url.'radio/export/'.$media_source.'_'.$media_date.$seg_suffix.'.mp3';
		return $full_url;
	}


	function boo() {
		return "boo";
	}

}

/* End of file pi.plugin_name.php */
/* Location: ./system/expressionengine/third_party/plugin_name/pi.plugin_name.php */

