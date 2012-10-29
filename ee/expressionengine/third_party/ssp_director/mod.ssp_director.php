<?php if (! defined('BASEPATH')) exit('Invalid file request');


/*
=====================================================
 SSP_Director - News Digital Media (Carsguide)
-----------------------------------------------------
 written by: Hereward Fenton
-----------------------------------------------------
 Copyright (c) 2008 News Digital Media
=====================================================
 THIS IS COPYRIGHTED SOFTWARE
=====================================================
 File: mod.ssp_director.php
-----------------------------------------------------
 Purpose: Interface to SlideShowPro Director
=====================================================
*/

class Ssp_director {

	var $director;
	var $config;
	var $_db = '';
	var $_in = '';
	var $_dsp = '';
	var $_lang = '';
	var $_sess = '';
	var $_tmpl = '';
	var $ssp_api_url;
	var $root_url;

	function Ssp_director() {
		//error_reporting(E_ERROR | E_WARNING | E_PARSE);
		//error_reporting(0);
		ini_set('display_errors', '0');
		//error_reporting(E_ALL);
        $this->EE =& get_instance();
		$this->init_module();
		$this->ssp_init();
	}

/*
	function get_config() {
		$query = "SELECT * from exp_ssp_director WHERE active = 'yes'";
		$rowset = $this->_db->query($query);
		return $rowset;
	}
*/
	function ssp_init() {
		require_once ("{$this->config->row('ssp_path')}/api/classes/DirectorPHP.php");
		
		$this->director = new Director($this->config->row('api_key'), $this->ssp_api_url);
		$cache_time = $this->config->row('ssp_cache_time');
		$this->director->cache->set('cg_gallery',"+$cache_time");
		
		
		//die("{$this->config->row('ssp_path')}/api/classes/DirectorPHP.php");

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

		# We can also request the album preview at a certain size
		$this->director->format->preview(array (
			'width' => '100',
			'height' => '50',
			'crop' => 1,
			'quality' => 85,
			'sharpening' => 1
		));

	}
	
	function resolve_album($album_id) {
		$album = $this->director->album->get($album_id);
	    if ($album) {
	    	return $album;
	    	
	    } else {	
	    	return '';
	    	//die("BAD URL"); 
	    	//header("Location: $this->root_url/not_found");
	    	/*
	        header("HTTP/1.0 404 Not Found");
	    	include "{$_SERVER['DOCUMENT_ROOT']}/404.html";
	    	*/
	    } 	
	}
	
	function fault_check() {
		$album_id = $this->EE->uri->segment('3'); 
		$article_id = $this->EE->uri->segment('5');
		$album = $this->director->album->get($album_id);
		if (!$album || !$article_id) {
			header("HTTP/1.0 404 Not Found");
	    	include "{$_SERVER['DOCUMENT_ROOT']}/404.html";
	    	exit();
		} 
	}

	function init_module() {

		$this->_tmpl = $this->EE->TMPL;
		$this->_db = $this->EE->db;
		$this->_in = $this->EE->input;
		$this->config = $this->EE->db->get('exp_ssp_director',1);
		//$this->config = $this->get_config();

		//$this->_in = $this->EE->input;
		//$this->_dsp = $DSP;
		//$this->_lang = $LANG;
		//$this->_sess = $SESS;
		//$this->_prefs = $PREFS;
		//$this->system_path = PATH;
		$this->root_url = "http://{$_SERVER['SERVER_NAME']}";
		//$this->root_url = $this->EE->config->item('site_url');
		$this->ssp_api_url = $this->ssp_api_url();
		//$this->module_path = PATH_MOD . 'ssp_director';
		//$this->cp_url = $this->_prefs->ini('cp_url');
		//$this->system_base_url = dirname($this->cp_url);
		//$this->module_view_url = dirname($this->cp_url) . '/modules/ssp_director/views';
		//$this->system_path = PATH;
	}

	function ssp_api_url() {
		return str_replace(array (
			'http://',
			'https://'
		), '', $this->config->row('ssp_url_fe'));
	}

	function test() {
		$album = $this->resolve_album(6);
		$output = '';
		# Set images variable for easy access
		$contents = $album->contents[0];
		//$tagdata = $this->_tmpl->swap_var_single($val, strval($new_val), $tagdata);

		foreach ($contents as $image) {
			$large_url = $this->draft_fix_url($image->large->url);
			$thumb_url = $this->draft_fix_url($image->thumb->url);
			$output .= sprintf("<a href='%s' rel='lightbox[road]' title='Uploaded by %s;'>
			            <img src='%s' width='%s' height='%s' alt='' /></a>", $large_url, $image->creator->display_name, $thumb_url, $image->thumb->width, $image->thumb->height);
			$output .= "<br/>";
		}

		return $output;

	}

	function get_prev_link() {

		$link_type = $this->_tmpl->fetch_param('link_type');
		$output = '';
		$img_id = $this->EE->uri->segment('4');

		if ($img_id == "1") {
			if ($link_type == "worded") { $output .= sprintf('<a class="scroll-text arrow-inactive" id="rew-link-2"></a>'); }
			else if ($link_type == "overlay") { $output .= sprintf('<a id="CG_gallery_hover_prev" class="CG_gallery_hover CG_gallery_hover_prev arrow-inactive"><div class="anchor-fill"></div><div>&nbsp;</div><div class="anchor-fill"></div></a>'); }
		} else {
		    $album_title_enc = $this->EE->uri->segment('2');
			$album_id = $this->EE->uri->segment('3');
			$channel_id = $this->EE->uri->segment('5');

			$img_id--;
			if ($link_type == "worded") { $output .= sprintf('<a title="View previous photo" class="scroll-text" href="%s/pics/%s/%s/%s/%s/" id="rew-link-2"></a>',
				$this->root_url, $album_title_enc, $album_id, $img_id, $channel_id). "\n"; }
			else if ($link_type == "overlay") { $output .= sprintf('<a id="CG_gallery_hover_prev" title="View previous photo" class="CG_gallery_hover CG_gallery_hover_prev" href="%s/pics/%s/%s/%s/%s/"><div class="anchor-fill"></div><div>&nbsp;</div><div class="anchor-fill"></div></a>',
				$this->root_url, $album_title_enc, $album_id, $img_id, $channel_id). "\n"; }
		}
	    return $output;
	}

	function get_next_link() {

		$link_type = $this->_tmpl->fetch_param('link_type');
		$output = '';

		$album_title_enc = $this->EE->uri->segment('2');
		$album_id = $this->EE->uri->segment('3');
		$img_id = $this->EE->uri->segment('4');
		$channel_id = $this->EE->uri->segment('5');

		$img_id++;
		if ($link_type == "worded") { $output .= sprintf('<a title="View next photo" class="scroll-text" href="%s/pics/%s/%s/%s/%s/" id="ffw-link-2"></a>',
			$this->root_url, $album_title_enc, $album_id, $img_id, $channel_id). "\n"; }
		else if ($link_type == "overlay") { $output .= sprintf('<a title="View next photo" class="CG_gallery_hover CG_gallery_hover_next" href="%s/pics/%s/%s/%s/%s/"><div class="anchor-fill"></div><div>&nbsp;</div><div class="anchor-fill"></div></a>',
			$this->root_url, $album_title_enc, $album_id, $img_id, $channel_id). "\n"; }

	    return $output;
	}

	function album_total() {
		$seg = $this->EE->uri->segment('3');
		$album_id = $seg;
		$album = $this->resolve_album($album_id);
		$contents = $album->contents[0];

		return count($contents);

	}

	function album_title() {
		$seg = $this->EE->uri->segment('3');
		$album_id = $seg;
		$album = $this->resolve_album($album_id);
		$contents = $album->name;
        return $contents;
	}

	function album_title_from_id($album_id='') {
	
	    if (!$album_id) {
	    	$album_id = $this->_tmpl->fetch_param('album_id');
	    }
	    
		if ($album_id > 0) {
		    $album = $this->resolve_album((int)$album_id);
		    $contents = $album->name;
		    return $contents;
		} else {
		    return "";
		}
    }
    
    function dump($obj) {
        ob_start();
        var_dump($obj);
        $ret_val = ob_get_contents();
        ob_end_clean();
        return $ret_val;
        //return "<textarea rows='20' cols='150'>$ret_val</textarea>";
    }

    function get_large_image() {
		$seg = $this->EE->uri->segment('3');
		$img_id = $this->EE->uri->segment('4');
		$album_id = $seg;
		$album = $this->resolve_album($album_id);
		$contents = $album->contents[0];
		$output = '';
        $count = 0;
        
		foreach ($contents as $image) {
			$count++;
		    if ($count == $img_id) {
		    	$url = $this->draft_fix_url($image->large->url);
				$output .= sprintf('%s',$url). "\n";
			}
		}

	    return $output;
	}

    function get_image_caption() {
		$seg = $this->EE->uri->segment('3');
		$img_id = $this->EE->uri->segment('4');
		$album_id = $seg;
		$album = $this->resolve_album($album_id);
		$contents = $album->contents[0];
		$output = '';
        $count = 0;
		foreach ($contents as $image) {
			$count++;
		    if ($count == $img_id) {
				$output .= sprintf('%s',$image->caption). "\n";
			}
		}

	    return $output;
	}

	function get_image_modified_date() {
		$seg = $this->EE->uri->segment('3');
		$img_id = $this->EE->uri->segment('4');
		$album_id = $seg;
		$album = $this->resolve_album($album_id);
		$contents = $album->contents[0];
		$output = '';
        $count = 0;
        
		foreach ($contents as $image) {
			$count++;
			if ($count == $img_id) {
			    $modified = $image->modified;
			    $modified_int = (int)$modified;
				$output .= sprintf(date('d M Y', $modified_int)). "\n";
			}
		}

		return $output;
	}
	
	function get_thumb() {
		
		$album_id = $this->_tmpl->fetch_param('album_id');
		$album = $this->director->album->get($album_id);
		
		$contents = $album->contents[0];
		$thumb_url = '';
		$image = $contents[0];
		$thumb_url = $contents->content[0]->thumb->url;
		$thumb_url = $this->draft_fix_url($thumb_url);
		return $thumb_url;

}

    function get_image_title() {
		$seg = $this->EE->uri->segment('3');
		$img_id = $this->EE->uri->segment('4');
		$album_id = $seg;
		$album = $this->resolve_album($album_id);
		$contents = $album->contents[0];
		$output = '';
        $count = 0;
		foreach ($contents as $image) {
			$count++;
		    if ($count == $img_id) {
				$output .= sprintf('%s',$image->title). "\n";
			}
		}

	    return $output;
	}

	function thumbnail_slider_arrows() {
		$slider_left_inactive = '';
		$slider_right_inactive = '';
		$album_id = $this->EE->uri->segment('3');
		$album = $this->resolve_album($album_id);
		$contents = $album->contents[0];
		$img_count = count($album->contents[0]);
		$img_id = $this->EE->uri->segment('4');

		if ($img_count <= 5 || $img_id < 4) { $slider_left_inactive = sprintf(' arrow-inactive'); }
		if ($img_count <= 5 || $img_count - $img_id < 3) { $slider_right_inactive = sprintf(' arrow-inactive'); }

		$output = sprintf('<img class="scroll-arrow%s" id="rew-link" src="/img/icons/arrow-scroll-left.png" alt="Previous image" />
						   <img class="scroll-arrow%s" id="ffw-link" src="/img/icons/arrow-scroll-right.png" alt="Next image" />',
						   $slider_left_inactive, $slider_right_inactive);

		return $output;
	}


	function thumbnail_slider() {
	    $album_title_enc = $this->EE->uri->segment('2');
		$seg = $this->EE->uri->segment('3');
		$img_id = $this->EE->uri->segment('4');
		$channel_id = $this->EE->uri->segment('5');
		$album_id = $seg;
		$album = $this->resolve_album($album_id);
		$contents = $album->contents[0];

		
		$thumbnail_width = $this->_tmpl->fetch_param('thumbnail_total_width');
		$img_count = count($album->contents[0]);
		$total_positions = $img_count - 5;

		$img_count_temp = 5;
		if ($img_count > 5) { $img_count_temp = $img_count; }
		$slider_width_css = sprintf('width:'.$img_count_temp * $thumbnail_width.'px;');

		$slider_position = 0;
		if ($img_count <= 5 || $img_id <= 3) { $slider_position = 0; }
		elseif ($img_id > $img_count - 3) { $slider_position = $total_positions; }
		else { $slider_position = $img_id - 3; }

		$slider_offset_css = sprintf('left:-'.$slider_position * $thumbnail_width.'px;');

		$output = '<div id="thumbnail-slider" style="'.$slider_width_css.$slider_offset_css.'" slider_pos_init="'.$slider_position.'">';
		$count = 1;
		foreach ($contents as $image) {
			$selected_thumb_class = '';
			$zoom_icon_style = '';
			if ($img_id == $count) {
				$selected_thumb_class = sprintf(' class="selected-finalist"');
				$zoom_icon_style = sprintf(' style="display: block;"');
			}
            $url = $this->draft_fix_url($image->thumb->url);
			$output .= sprintf('<a href="%s/pics/%s/%s/%s/%s/"'.$selected_thumb_class.'><img src="%s"><div class="zoom-icon"'.$zoom_icon_style.'><img src="/img/features/coty2010/white-pixel.gif"></div></a>',
	        $this->root_url, $album_title_enc, $album_id, $count, $channel_id, $url). "\n";
			$count++;
		}
		$output .= '</div>';
		return $output;

	}

	function gallery_url_title($album_id) {
    	$gallery_title = $this->album_title_from_id($album_id);
    	$gallery_title = str_replace(" ", "-", $gallery_title);  // convert spaces to hyphens
    	$gallery_title = str_replace("&", "and", $gallery_title);
    	$gallery_title = str_replace("'", "", $gallery_title);  // remove single quotes
    	$gallery_title = str_replace('"', '', $gallery_title);  // remove double quotes
    	return strtolower($gallery_title);

    }


    function resolve_field_name($name) {
        
        $id = '';
        $query = "SELECT * from exp_channel_fields WHERE field_name = '$name'";
        $rowset = $this->_db->query($query);
        $id = $rowset->row('field_id');
        return $id;
    }

    function draft_fix_url($url) {
		 $url = str_replace('http://draft.carsguide.com.au', 'http://www.carsguide.com.au', $url);
		 $output = str_replace('http://draft.uat.carsguide.com.au', 'http://uat.carsguide.com.au', $url);
		 return $output;

	}

    function features_gallery_extras() {

        $gallery_ids = $this->_tmpl->fetch_param('gallery_ids');


        $ret = '';
        //$ret .= "<ul class='donut xp'>";

        $lines = preg_split("/\s+/", trim($gallery_ids));

        if (count($lines) < 1) {
            return '';
        }
       
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line) {

            	$current_id = $line;

                $query = "SELECT * FROM exp_channel_titles, exp_channel_data WHERE exp_channel_titles.entry_id = exp_channel_data.entry_id AND exp_channel_titles.entry_id =$current_id";
                $result = $this->_db->query($query);


                $path = 'news-and-reviews/story';
                $channel_id = $result->row('channel_id');
      
                $url_title = $result->row('url_title');

                $img_s_id_int = $this->resolve_field_name('img_s');
                $ssp_int = $this->resolve_field_name('ssp_album_id');

                //$title_int = $this->resolve_field_name('gallery_title');
                //return "TITLE INT = [$title_int]";

                $ssp_id = $result->row("field_id_$ssp_int");
                $gallery_title = $this->album_title_from_id($ssp_id);
                //return "GALLERY TITLE = [$gallery_title] | ssp_id = [$ssp_id] | SSP_INT = [$ssp_int]";
                $url_title = $this->gallery_url_title($ssp_id);

                $url = "$this->root_url/pics/$url_title/$ssp_id/0/$current_id";

                $ret .= "<li id='pic'><a href='$url'>$gallery_title</a></li> \n";
            }
        }
        //$ret .= "</ul>";
        return $ret;
    }

}

