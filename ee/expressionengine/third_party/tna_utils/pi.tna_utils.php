<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Tna_utils {
    
	function __construct()
	{
		$this->EE =& get_instance();
		/*$this->return_data = "Hello World";*/

	}

    function is_platform($type='') {
        $default_site_path = $this->EE->config->item('default_site_path');
        require_once "$default_site_path/includes/Mobile_Detect.php";
        $detect = new Mobile_Detect;

        $type = ($type)?$type:$this->EE->TMPL->fetch_param('type');

        if ($type == 'mobile') {
            return ($detect->isMobile())?1:'';
        } elseif ($type == 'computer') {
            return ($detect->isMobile())?'':1;
        } elseif ($type == 'tablet') {
            return ($detect->isTablet())?1:'';
        } elseif ($type == 'phone') {
            return ($detect->isMobile() && !$detect->isTablet())?1:'';
        } else {
            return '';
        }

    }

	function get_media_properties() {
        //die("cp_url dirname = ".dirname($this->EE->config->item('cp_url')));
        $default_site_path = $this->EE->config->item('default_site_path');
		error_reporting(E_ALL & ~E_DEPRECATED);
		@ini_set('display_errors', 1);
		//die(APPPATH);
		//die($this->EE->config->item('system_path'));
		require_once("$default_site_path/includes/getid3/getid3.php");
		//require_once("{$_SERVER['DOCUMENT_ROOT']}/includes/getid3/getid3.lib.php");
        require_once ("$default_site_path/includes/getid3/extension.cache.mysql.php");
        
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
			$full_path = "$default_site_path/$param_path";
		} else {
			$full_path = "$default_site_path/radio/export/$filename";
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


    function get_url_contents($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    function latest_show_date() {
        $date = strtotime('last Wednesday');
        return $date;
    }

    function media_date_of_last_show() {

        $date = $this->latest_show_date();
        $output = date("Ymd", $date);
        return $output;
    }

    function pretty_date_of_last_show() {
        $date = $this->latest_show_date();
        $output = date("F j, Y", $date);
        return $output;
    }

    function get_latest_show() {
        $lsd_db_output = '';
        $lsd_db = $this->EE->TMPL->fetch_param('lsd_db');
        if ($lsd_db) {
            $lsd_db_parsed = strtotime($lsd_db);
            $lsd_db_output = date("F j, Y", $lsd_db_parsed);
        }


        //die("$lsd_db|$lsd_db_output");
        //die($lsd_db);
        $feed_url = 'http://feeds.feedburner.com/LogosRadioNetworkTruthNewsRadioAustralia?format=xml';
        $tagdata = $this->EE->TMPL->tagdata;
        $vars = array(
            'pretty_date' => '',
            'mp3_url' => '',
            'last_show_date'=>'',
            'lsd_db_output' => $lsd_db_output
        );
        //libxml_use_internal_errors(true);

        //$opts = array('http' => array('timeout' => 1));

        //$context = stream_context_create($opts);
        //libxml_set_streams_context($context);

        $raw_feed = $this->get_url_contents($feed_url);

       // $feed = simplexml_load_file();
        if (!$raw_feed) {
            $output = $this->EE->TMPL->parse_variables_row($tagdata, $vars);
            return $output;
        }
        libxml_use_internal_errors(true);
        $feed = simplexml_load_string($raw_feed);
        if ($feed === false) {
            $output = $this->EE->TMPL->parse_variables_row($tagdata, $vars);
            return $output;
        }

        $item = $feed->channel->item[0];
        $content_links = $item->children('http://purl.org/rss/1.0/modules/content/');
        $links = $item->children('http://rssnamespace.org/feedburner/ext/1.0');
        $fb_links = $item->children('http://rssnamespace.org/feedburner/ext/1.0');
        $raw_date = $item->pubDate;
        $pubdate = str_replace('+0000', '', $raw_date);
        $mp3_url = $fb_links->origEnclosureLink;
        //echo "<div>origEnclosureLink = [$mp3_url]</div>";
        preg_match('/TNRA_(.+)_64k\.mp3$/', $mp3_url, $matches);

        if (!count($matches)) {
            preg_match('/TNRA_(.+)_16k\.mp3$/', $mp3_url, $matches);
        }
        if (!count($matches)) {
            $output = $this->EE->TMPL->parse_variables_row($tagdata, $vars);
            return $output;
        }

        $pod_date_raw = $matches[1];
        $parsed_date = strtotime($pod_date_raw);
        $pretty_date = date("F j, Y", $parsed_date);
        $last_show_date = date("Ymd", $parsed_date);

        //die("mp3_url = [$mp3_url]  last_show_date = [$last_show_date]");

        $output = '';

        //$output .= "DESCRIPTION = ". $item->description ."<br/>\n";
        /*
        $output .= "DATE = ". $pubdate."<br/>\n";
        $output .= "origEnclosureLink = ". $mp3_url ."<br/>\n";
        $output .= "POD DATE = ". $pod_date_raw ."<br/>\n";
        $output .= "PRETTY DATE = ". $pretty_date ."<br/>\n";
        */

        $vars['pretty_date'] = $pretty_date;
        $vars['mp3_url'] = $mp3_url;
        $vars['last_show_date'] = $last_show_date;


        $output = $this->EE->TMPL->parse_variables_row($tagdata, $vars);

        return $output;

        //die($output);

        /*
        foreach($feed->channel->item as $item) {
            $title = $item->title;
            $raw_author = $item->author;
            //$author = str_replace('gabriele.romanato@gmail.com', '', $raw_author);
            //$author = str_replace('(', '', $author);
            //$author = str_replace(')', '', $author);
            $links = $item->children('http://rssnamespace.org/feedburner/ext/1.0');
            $link = $links->origLink;
            $raw_date = $item->pubDate;
            $pubdate = str_replace('+0000', '', $raw_date);
            die("LINK = $link");

        }
        */

    }


	function boo() {
		return "boo";
	}

    function strip_lines() {
        $string = $this->EE->TMPL->fetch_param('string');
        $string = trim(preg_replace('/\s\s+/', ' ', $string));
        return $string;
    }

}

/* End of file pi.plugin_name.php */
/* Location: ./system/expressionengine/third_party/plugin_name/pi.plugin_name.php */

