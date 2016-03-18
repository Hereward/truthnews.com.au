<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
  'pi_name' => 'EE Hive Hacksaw',
  'pi_version' => '1.07',
  'pi_author' => 'EE Hive - Brett DeWoody',
  'pi_author_url' => 'http://www.ee-hive.com/add-ons/hacksaw',
  'pi_description' => 'Allows you to create excerpts of your entries by removing HTML tags and limited the excerpt by character count, word count or a specific marker you insert into your content.',
  'pi_usage' => Eehive_hacksaw::usage()
  );

/**
 * Buzzsaw Class
 *
 * @package			ExpressionEngine
 * @category		Plugin
 * @author			Brett DeWoody - ee hive
 * @copyright		Copyright (c) 2010, Brett DeWoody
 * @link			http://www.ee-hive.com/add-ons/hacksaw
 */

class Eehive_hacksaw
{

var $return_data = "";

	// --------------------------------------------------------------------

	/**
	 * Hacksaw
	 *
	 * This function strips HTML and cuts the content off at a specific character count, word or cutoff marker
	 *
	 * @access	public
	 * @return	string
	 */

  function Eehive_hacksaw() {
    $this->EE =& get_instance();
    
	$tag_content = $this->EE->TMPL->tagdata;
	
	$chars = $this->EE->TMPL->fetch_param('chars');
	$chars_start = ($this->EE->TMPL->fetch_param('chars_start') ? $this->EE->TMPL->fetch_param('chars_start') : 0);
	$words = $this->EE->TMPL->fetch_param('words');
	$cutoff = $this->EE->TMPL->fetch_param('cutoff');
	$append = $this->EE->TMPL->fetch_param('append', '');
	$allow = $this->EE->TMPL->fetch_param('allow');
      $html_cut = $this->EE->TMPL->fetch_param('html_cut');
      $html_max_length = $this->EE->TMPL->fetch_param('html_max_length');
	
	if(isset($cutoff) && $cutoff != "") {
		$cutoff_content = $this->_truncate_cutoff($tag_content, $cutoff, $words, $allow, $append);
		// Strip the HTML
		$new_content = (strpos($tag_content, $cutoff) ? strip_tags($cutoff_content, $allow) : strip_tags($cutoff_content, $allow));
	} elseif (isset($chars) && $chars != "") {
		// Strip the HTML
		$stripped_content = strip_tags($tag_content, $allow);
		$new_content = (strlen($stripped_content) <= $chars ? $stripped_content : $this->_truncate_chars($stripped_content, $chars_start, $chars, $append));
	} elseif (isset($words) && $words != "") {
		// Strip the HTML
		$stripped_content = strip_tags($tag_content, $allow);
		$raw_content = (str_word_count($stripped_content) <= $words ? $stripped_content : $this->_truncate_words($stripped_content, $words, $append));
/*
        $dom= new DOMDocument();
        $dom->loadHTML($raw_content);
        $xpath = new DOMXPath($dom);
        $body = $xpath->query('/html/body');
        $parsed_content = $dom->saveXml($body->item(0));
*/

        $new_content = $raw_content;

    } elseif (isset($html_cut) && $html_cut != "") {
        //$new_content = $this->html_cut($tag_content, $html_max_length);
        $new_content = $this->html_truncate($tag_content, $html_max_length);

	} else {
        // Strip the HTML
		$stripped_content = strip_tags($tag_content, $allow);
                $new_content = $stripped_content;
        }
	
	// Return the new content
    $this->return_data = $new_content;
    
  }
  
  // Helper Function - Truncate by Word Limit
  function _truncate_words($content, $limit, $append) {
    $num_words = str_word_count($content, 0);
	if ($num_words > $limit) {
	  $words = str_word_count($content, 2);
      $pos = array_keys($words);
      $content = substr($content, 0, ($pos[$limit]-1)) . $append;
    }
    return $content;
    }
	
  // Helper Function - Truncate by Character Limit
  function _truncate_chars($content, $chars_start, $limit, $append) {
    // Removing the below to see how it effect UTF-8. 
    $content = preg_replace('/\s+?(\S+)?$/', '', substr($content, $chars_start, ($limit+1))) . $append;
    return $content;
  }
  
  // Helper Function - Truncate by Cutoff Marker
  function _truncate_cutoff($content, $cutoff, $words, $allow, $append) {
    $pos = strpos($content, $cutoff);
	if ($pos != FALSE) {
		$content = substr($content, 0, $pos) . $append;
	} elseif ($words != "") {
		$content = $this->_truncate_words(strip_tags($content, $allow), $words, '') . $append;
	}
    return $content;
  }


    function html_truncate($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
        if (is_array($ending)) {
            extract($ending);
        }
        if ($considerHtml) {
            if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            $totalLength = mb_strlen($ending);
            $openTags = array();
            $truncate = '';
            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                    if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                        array_unshift($openTags, $tag[2]);
                    } else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                        $pos = array_search($closeTag[1], $openTags);
                        if ($pos !== false) {
                            array_splice($openTags, $pos, 1);
                        }
                    }
                }
                $truncate .= $tag[1];

                $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
                if ($contentLength + $totalLength > $length) {
                    $left = $length - $totalLength;
                    $entitiesLength = 0;
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entitiesLength <= $left) {
                                $left--;
                                $entitiesLength += mb_strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
                    break;
                } else {
                    $truncate .= $tag[3];
                    $totalLength += $contentLength;
                }
                if ($totalLength >= $length) {
                    break;
                }
            }

        } else {
            if (mb_strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = mb_substr($text, 0, $length - strlen($ending));
            }
        }
        if (!$exact) {
            $spacepos = mb_strrpos($truncate, ' ');
            if (isset($spacepos)) {
                if ($considerHtml) {
                    $bits = mb_substr($truncate, $spacepos);
                    preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                    if (!empty($droppedTags)) {
                        foreach ($droppedTags as $closingTag) {
                            if (!in_array($closingTag[1], $openTags)) {
                                array_unshift($openTags, $closingTag[1]);
                            }
                        }
                    }
                }
                $truncate = mb_substr($truncate, 0, $spacepos);
            }
        }

        $truncate .= $ending;

        if ($considerHtml) {
            foreach ($openTags as $tag) {
                $truncate .= '</'.$tag.'>';
            }
        }

        return $truncate;
    }


    function html_cut($text, $max_length)
    {
        $tags   = array();
        $result = "";

        $is_open   = false;
        $grab_open = false;
        $is_close  = false;
        $in_double_quotes = false;
        $in_single_quotes = false;
        $tag = "";

        $i = 0;
        $stripped = 0;

        $stripped_text = strip_tags($text);

        while ($i < strlen($text) && $stripped < strlen($stripped_text) && $stripped < $max_length)
        {
            $symbol  = $text{$i};
            $result .= $symbol;

            switch ($symbol)
            {
                case '<':
                    $is_open   = true;
                    $grab_open = true;
                    break;

                case '"':
                    if ($in_double_quotes)
                        $in_double_quotes = false;
                    else
                        $in_double_quotes = true;

                    break;

                case "'":
                    if ($in_single_quotes)
                        $in_single_quotes = false;
                    else
                        $in_single_quotes = true;

                    break;

                case '/':
                    if ($is_open && !$in_double_quotes && !$in_single_quotes)
                    {
                        $is_close  = true;
                        $is_open   = false;
                        $grab_open = false;
                    }

                    break;

                case ' ':
                    if ($is_open)
                        $grab_open = false;
                    else
                        $stripped++;

                    break;

                case '>':
                    if ($is_open)
                    {
                        $is_open   = false;
                        $grab_open = false;
                        array_push($tags, $tag);
                        $tag = "";
                    }
                    else if ($is_close)
                    {
                        $is_close = false;
                        array_pop($tags);
                        $tag = "";
                    }

                    break;

                default:
                    if ($grab_open || $is_close)
                        $tag .= $symbol;

                    if (!$is_open && !$is_close)
                        $stripped++;
            }

            $i++;
        }

        while ($tags)
            $result .= "</".array_pop($tags).">";

        return $result;
    }
  

	// --------------------------------------------------------------------

	/**
	 * Usage
	 *
	 * This function describes how the plugin is used.
	 *
	 * @access	public
	 * @return	string
	 */
	
  //  Make sure and use output buffering

  function usage()
  {
  ob_start(); 
  ?>
Hacksaw allows you to create excerpts of your content
like no other. It strips the HTML from your content and
limits the excerpts by character count, word count or
cutoff marker.

{exp:eehive_hacksaw
	chars = "" // Limit by number of characters
	chars_start = "" // Used with the 'chars' parameter, this starts the excerpt at X characters from the beginning of the content
    words = "" // Limit by number of words
    cutoff = "" // Limit by a specific cutoff string
    append = "" // String to append to the end of the excerpt
    allow = "" // HTML tags you want to allow. Ex allow="<b><a>"
}{your_content}{/exp:eehive_hacksaw}

You can also leave off any truncating parameters (chars, 
words, cutoff) and Hacksaw will strip the HTML and 
return the entire contents of the tag.

  <?php
  $buffer = ob_get_contents();
	
  ob_end_clean(); 

  return $buffer;
  }
  // END

}
/* End of file pi.memberlist.php */ 
/* Location: ./system/expressionengine/third_party/eehive_buzzsaw/pi.eehive_buzzsaw.php */