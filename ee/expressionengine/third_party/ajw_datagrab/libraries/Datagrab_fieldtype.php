<?php
/**
 * DataGrab Fieldtype Class
 * 
 * Provides methods to interact with EE fieldtypes 
 *
 * @package   DataGrab
 * @author    Andrew Weaver <aweaver@brandnewbox.co.uk>
 * @copyright Copyright (c) Andrew Weaver
 **/

class Datagrab_fieldtype {
		
	/**
	 * Constructor
	 *
	 * @return void
	 */
	function Datagrab_fieldtype() {
		$this->EE =& get_instance();
	}
	
	function display_configuration() {
	}
	
	/**
	 * Prepare data for posting
	 *
	 * @param object $DG The DataGrab model object
	 * @param string $item The current row of data from the data source
	 * @param string $field_id The id of the field
	 * @param string $field The name of the field
	 * @param string $data The data array to insert into the channel
	 * @param string $update Update or insert?
	 */
	function prepare_post_data( $DG, $item, $field_id, $field, &$data, $update = FALSE ) {
		$data[ $field_id ] = $datatype->get_item( $item, $DG->settings[ $field ] );
	}
	
	function final_post_data( $DG, $item, $field_id, $field, &$data, $update = FALSE ) {
		// $data[ $field_id ] = $data[ $field_id ];
	}
	
}

?>