<?php

/**
 * DataGrab CSV import class
 *
 * Allows CSV imports
 * 
 * @package   DataGrab
 * @author    Andrew Weaver <aweaver@brandnewbox.co.uk>
 * @copyright Copyright (c) Andrew Weaver
 */
class Ajw_csv extends Datagrab_type {

	var $datatype_info = array(
		'name'		=> 'CSV',
		'version'	=> '0.1'
		);

	var $settings = array(
		"filename" => "",
		"delimiter" => "",
		"encloser" => "",
		"skip" => 0
		);

	function settings_form( $values = array() ) {

		$form = array(
		array( 
			form_label('Filename or URL', 'filename') .
			'<div class="subtext">Can be a file on the local file system or from a website site url (http://...)</div>', 
			form_input(
				array(
					'name' => 'filename',
					'id' => 'filename',
					'value' => $this->get_value( $values, "filename" ),
					'size' => '50'
					)
				) 
			),
		array( 
			form_label('Delimiter', 'delimiter') .
			'<div class="subtext">The character used to separate fields in the file. Use TAB for a tab-delimited file.</div>', 
			form_input(
				array(
					'name' => 'delimiter',
					'id' => 'delimiter',
					'value' => $this->get_value( $values, "delimiter" ),
					'size' => '4'
					)
				)
			),
		array( 
			form_label('Encloser', 'encloser') .
			'<div class="subtext">If in doubt, or if the data has no encloser, use the default "</div>', 
			form_input(
				array(
					'name' => 'encloser',
					'id' => 'encloser',
					'value' => $this->get_value( $values, "encloser" ),
					'size' => '4'
					)
				)
			),
		array(
			form_label('Use first row as titles', 'skip') .
			'<div class="subtext">Select this if the first row of the file contains titles and should not be imported</div>',
			form_checkbox('skip', '1', ( $this->get_value( $values, "skip" ) == 1 ? TRUE : FALSE ), ' id="skip"')
			)
		);

		return $form;
	}

	function fetch() {
		
		ini_set('auto_detect_line_endings', true);
		
		if ( !isset( $this->settings["filename"] ) ) {
			$this->errors[] = "You must supply a filename/url.";
			return -1;
		}

		// Open CSV file and save handle
		$this->handle = @fopen($this->settings["filename"], "r");

		if ( $this->handle === FALSE ) {
			$this->errors[] = "Cannot open the file/url: " . $this->settings["filename"] . ".  <br/>If you are trying to access this file 
			using http://, try accessing directly as a file.";
			return -1;
		}
		
	}

	function next() {
		
		if ( $this->settings['delimiter'] == '\t' OR $this->settings['delimiter'] == 'TAB' ) {
			$this->settings['delimiter'] = "\t";
		}

		if ( $this->settings['encloser'] == '' ) {
			$this->settings['encloser'] = '"';
		}

		
		// Get next line of CSV file
		$item = fgetcsv($this->handle, 10000, $this->settings["delimiter"], $this->settings["encloser"]);

		return $item;
		
	}

	function fetch_columns() {
		
		// Get first line of file
		$this->fetch();
		$columns = $this->next();
		
		// Loop through fields, adding Column # and truncating any long labels
		$titles = array();
		$count = 0;
		foreach( $columns as $title ) {
			if ( strlen( $title ) > 32 ) {
				$title = substr( $title, 0, 32 ) . "...";
			}
			$titles[] = "Column " . ++$count . " - eg, " . $title;
		}

		return $titles;
		
	}

}

?>