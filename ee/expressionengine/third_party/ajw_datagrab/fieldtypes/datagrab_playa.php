<?php

/**
 * DataGrab Playa fieldtype class
 *
 * @package   DataGrab
 * @author    Andrew Weaver <aweaver@brandnewbox.co.uk>
 * @copyright Copyright (c) Andrew Weaver
 */
class Datagrab_playa extends Datagrab_fieldtype {

	function prepare_post_data( $DG, $item, $field_id, $field, &$data, $update = FALSE ) {
		
		// Fetch fieldtype settings
		$fs = $this->EE->api_channel_fields->settings[ $field_id ]["field_settings"];
		$field_settings = (unserialize(base64_decode($fs)));
		
		// Initialise playa post data
		$data[ "field_id_" . $field_id ] = array();
		// $data[ "field_id_" . $field_id ]["old"] = "";
		$data[ "field_id_" . $field_id ]["selections"] = array();
		//$data[ "field_id_" . $field_id ]["selections"][] = "";

		// Fetch fieldtype settings
		$DG->_get_channel_fields_settings( $field_id );
		$fs = $this->EE->api_channel_fields->settings[ $field_id ]["field_settings"];
		$field_settings = (unserialize(base64_decode($fs)));
		
		if( $DG->datatype->datatype_info["name"] == "XML" ) {
		
			// Loop over items in data source
			$no_elements = $DG->datatype->get_item( $item, $DG->settings[ $field ] . "#" );
			if( $no_elements == 0 ) { $no_elements = 1; }
			if( $no_elements > 0 ) {
				for( $i=0; $i<$no_elements; $i++ ) {
					if ( $i > 0 ) {
						$suffix = '#' . ($i+1);
					} else {
						$suffix = '';
					}
					$subfield = $DG->settings[ $field ] . $suffix;

					// Check whether item matches a valid entry and create a playa relationship
					$this->EE->db->select( 'exp_channel_titles.entry_id' );
					$this->EE->db->join( 'exp_channel_data', 'exp_channel_titles.entry_id = exp_channel_data.entry_id' );
					if( isset( $field_settings["channels"] ) ) {
						$this->EE->db->where_in( 'exp_channel_titles.channel_id', $field_settings["channels"] );
					}
					if( !isset( $DG->settings[ $field . "_playa_field" ] ) ) {
						$this->EE->db->where( 'title', $DG->datatype->get_item( $item, $subfield ) );
					} else {
						$this->EE->db->where( $DG->settings[ $field . "_playa_field" ], $DG->datatype->get_item( $item, $subfield ) );
					}
					$query = $this->EE->db->get( 'exp_channel_titles' );
					if( $query->num_rows() > 0 ) {
						$row = $query->row_array();
						$data[ "field_id_" . $field_id ]["selections"][] = $row[ "entry_id" ];
					}

				}	
			}
			
		} else {
		
			$related = $DG->datatype->get_item( $item, $DG->settings[ $field ] );

			if( $related != "" ) {
				$values = explode(",", $related);
				
				$this->EE->db->select( 'exp_channel_titles.entry_id' );
				$this->EE->db->join( 'exp_channel_data', 'exp_channel_titles.entry_id = exp_channel_data.entry_id' );
				if( isset( $field_settings["channels"] ) ) {
					$this->EE->db->where_in( 'exp_channel_titles.channel_id', $field_settings["channels"] );
				}
				if( !isset( $DG->settings[ $field . "_playa_field" ] ) ) {
					$this->EE->db->where_in( 'title', $values );
				} else {
					$this->EE->db->where_in( $DG->settings[ $field . "_playa_field" ], $values );
				}
				$query = $this->EE->db->get( 'exp_channel_titles' );
								
				if( $query->num_rows() > 0 ) {
					foreach ( $query->result_array() as $row ) {
						$data[ "field_id_" . $field_id ]["selections"][] = $row[ "entry_id" ];
					}
				
				}
			}
	
		}

	}

}

?>