<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tna_utils {

  function __construct()
  {
	$this->EE =& get_instance(); 
	/*$this->return_data = "Hello World";*/
	
  }
  
  function get_media_properties() {
  	require_once ("{$_SERVER['DOCUMENT_ROOT']}/includes/getid3/getid3.php");
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
  	$getID3 = new getID3;
        $full_path = '';
        if ($param_path) {
        $full_path = "{$_SERVER['DOCUMENT_ROOT']}/$param_path";
        } else {
  	$full_path = "{$_SERVER['DOCUMENT_ROOT']}/radio/export/$filename";
        }
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

