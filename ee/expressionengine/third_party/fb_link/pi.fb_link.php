<?php

/*
=====================================================
 File: pi.fb_link.php
-----------------------------------------------------
 Purpose: Purpose of plugin
=====================================================
*/

$plugin_info = array(
	'pi_name' => 'Facebook Link',
	'pi_version' => '1.0',
	'pi_author' => 'Ron Hickson',
	'pi_author_url' => 'http://www.hicksondesign.com',
	'pi_description' => 'The Facebook Link plugin connects your site and Facebook.  The simple purpose is to bring a Facebook Page\'s wall over for display seamlessly into your website.  There is a difference between a Facebook individual user and a Facebook Page (i.e. Pages are usually businesses and organizations).  This only works with Pages.  Future versions will expand the functionality.  If you have suggestions then let me know at <a href="http://www.hicksondesign.com">Hickson Design</a>.',
	'pi_usage' => Fb_link::usage()
	);

/** ----------------------------------------
/** Plugin
/** ----------------------------------------*/

class Fb_link {

	function __construct()
	{
		$this->EE =& get_instance();
	}
	
// Returns the latest wall posts			
	function wall()
		{
		
		// Parameters
    	$limit = (! $this->EE->TMPL->fetch_param('limit')) ? 5 : $this->EE->TMPL->fetch_param('limit');
    	$pageId = $this->EE->TMPL->fetch_param('page');
			
		// Page feed data and display
		$pageUrl = 'https://graph.facebook.com/'.$pageId.'/feed?limit='.$limit;
		$page = file_get_contents($pageUrl);
		$obj = json_decode($page);
		
		$output = '';
			
		foreach ($obj->data as $data) {
			// Display outpu if the type of Facebook entry is a status update
			if (($data->type) == 'status' && isset($data->from->id) && isset($data->from->name)) {
				$HTMLpattern = "/http:\/\/([a-z0-9\-]*)(\.)+(com|net|org)/";
				
				// This checks for apps that post and include HTML links.  It will format the link and display.  Else statement posts message as is.
				if (isset($data->application->id) && preg_match($HTMLpattern, ($data->message))) {
					$words = explode(' ', ($data->message));
					foreach ($words as &$text) {
						if (preg_match('/^http:\/\//', $text)) {
							$text = '<a href="'.$text.'">'.$text.'</a>';
						}
					}
					$message = implode(' ', $words);
					$output .= '<br /><div class="fb_status"><span class="fb_profile_link"><a href="http://www.facebook.com/profile.php?id='."{$data->from->id}".'">'."{$data->from->name}".'</a></span>  '."<div class='fb_message'>$message</div>".'<br /></div>';
				} else {
					$output .= '<br /><div class="fb_status"><span class="fb_profile_link"><a href="http://www.facebook.com/profile.php?id='."{$data->from->id}".'">'."{$data->from->name}".'</a></span>  '."<div class='fb_message'>{$data->message}</div>".'<br /></div>';
				}
			}
			
			// Display output if the type of Facebook entry is a photo or video
			if (($data->type) == 'photo' || ($data->type) == 'video' && isset($data->from->id) && isset($data->from->name)) {
				$output .= '<br /><div class="fb_status"><span class="fb_profile_link"><a href="http://www.facebook.com/profile.php?id='."{$data->from->id}".'">'."{$data->from->name}".'</a></span> ';
				if (isset($data->message)) {
					$output .= "<div class='fb_message'>{$data->message}</div>\n".'<br />';
					$output .= '<div class="fb_photo"><a href="'."{$data->link}".'"><img src="'."{$data->picture}".'" /></a></div></div>';
				} else {
					$output .= '<div class="fb_photo"><a href="'."{$data->link}".'"><img src="'."{$data->picture}".'" /></a></div></div>';
				}
			}
			
			// Display output if the type of Facebook entry is a link
			if (($data->type) == 'link' && isset($data->from->id) && isset($data->from->name)) {
				$output .= '<br /><div class="fb_status"><span class="fb_profile_link"><a href="http://www.facebook.com/profile.php?id='."{$data->from->id}".'">'."{$data->from->name}".'</a></span>  ';
				if (isset($data->message)) {
					$output .= "<div class='fb_message'>{$data->message}</div>\n".'<br />';
					$output .= '<div class="fb_link"><a href="'."{$data->link}".'">'."{$data->name}\n".'</a></div></div>';

				} else {
					$output .= '<div class="fb_link"><a href="'."{$data->link}".'">'."{$data->name}\n".'</a></div></div>';
				}
			}
			
			// Display date information for posts				
			$postTime = date('M d, Y',strtotime($data->created_time));
			$output .= '<div class="fb_time">'.$postTime.'</div>';
		}
			
		return $output;
		}
		

/* END */

// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.

function usage()
{
ob_start(); 
?>
PARAMETERS:

1) page = Required.  Pass the page name or ID for the Facebook Page wall you want to pull from.

2) limit = Optional.  Limit the number of wall posts to display.  By default the limit is 5.

USAGE:

Place the {exp:fb_link:wall page='YOUR PAGE NAME OR ID' limit='10'} anywhere in your template.

The following CSS classes are applied to the content and can be styled as you choose...
"fb_status" - The entire status message wrapped in a DIV with this class.
"fb_photo" - This class applies to photos or videos in the Facebook status.
"fb_link" - This class applies to links shared using Facebook's link feature when posting a status update.
"fb_time" - The date can be formatted with this class.


<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
/* END */


}
// END CLASS
	
?>