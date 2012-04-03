<?php

/**
 * DataGrab Feed import class
 *
 * Allows RSS & ATOM imports using the SimplePie library
 * 
 * @package   DataGrab
 * @author    Andrew Weaver <aweaver@brandnewbox.co.uk>
 * @copyright Copyright (c) Andrew Weaver
 */
class Ajw_simplepie extends Datagrab_type {

	var $datatype_info = array(
		'name'		=> 'RSS/ATOM feed (using SimplePie)',
		'version'	=> '0.1'
	);
	
	var $settings = array(
		"filename" => ""
		);
	
	var $feed;
	var $items;
	
	function settings_form( $values = array() ) {
		
		$form = array(
			array(
				form_label('Filename or URL', 'filename'), 
				form_input(
					array(
						'name' => 'filename',
						'id' => 'filename',
						'value' => $this->get_value( $values, "filename" ),
						'size' => '50'
						)
					)
				)
			);
	
		return $form;		
	}
	
	function fetch() {
		
		if ( ! class_exists('SimplePie') ) {
			include('simplepie.inc');
		}
		
		// Parse it
		$this->feed = new SimplePie();
		$this->feed->set_feed_url( $this->settings["filename"] );
		$this->feed->enable_cache(false);
		$this->feed->init();
		$this->feed->handle_content_type();
		
		$this->items = $this->feed->get_items();
	
	}

	function next() {

		$item = current( $this->items );
		next( $this->items );

		return $item;
		
	}
	
	function fetch_columns() {

		$titles = array( 
			"title" => "Title",
			"author" => "Author",
			"content" => "Content",
			"categories" => "Categories",
			"date" => "Date",
			"id" => "ID",
			"link" => "Link",
			"permalink" => "Permalink"
			);

		$item = current( $this->items );
		next( $this->items );

		foreach( $titles as $idx => $title ) {
			$value = $this->get_item( $item, $idx );
			if ( strlen( $value ) > 64 ) {
				$value = substr( $value, 0, 64 ) . "...";
			}
			$title .= " (eg, " . $value . ")";
			$titles[ $idx ] = $title;
		}

		return $titles;
	}
	
	function get_item( $items, $id ) {

		switch( $id ) {

			case "title":
			return $items->get_title();
			break;

			case "author":
			return $items->get_author()->get_name();
			break;

			case "content":
			return $items->get_content();
			break;

			case "categories":
			$category = $items->get_category();
			return is_object( $category ) ? $category->get_label() : '';
			break;

			case "date":
			return $items->get_date();
			break;

			case "id":
			return $items->get_id();
			break;

			case "link":
			return $items->get_link();
			break;

			case "permalink":
			return $items->get_permalink();
			break;

			default:
			print $id;
			
		}
	
	}
	
}

?>