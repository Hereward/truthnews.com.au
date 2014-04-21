<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// Pink Pages Model
//extends CI_Model

class Tna_commerce_model extends Base_model {

	public $my_var = 'My Var is EMPTY';
	public $orig = '[EMPTY GiGi]';
	/*
	 protected $EE;
	 */
	public function __construct()
	{
		//$this->EE =& get_instance();

		//die('whizzzzz');
		
		$this->orig = $this->my_var;
		
		parent::__construct();
		//set a global object
		//$this->EE->tna_commerce = $this;
	}

	
	public function model_test_2() {
		return $this->my_var;
	}

	public function model_test() {
		$this->my_var = 'My Var has been transmofrigied X';
		$ppo_db = $this->EE->load->database('ppo', TRUE);

		$query = "SELECT * FROM `local_businesses` WHERE business_id = 10599250";
		$results = $ppo_db->query($query);

		$bname = $results->row('business_name');

		$res2 = $ppo_db->get('shire_names');
		$shirename = '[no data]';

		//$select_query = $ppo_db->get_compiled_select();

		$row = $res2->row_array(2);
		$shirename = $row['shirename_shirename'];

		/*
		 $row = $res2->row();
		 $shirename = $row->shirename_shirename;
		 */

		/*
		 foreach ($res2->result() as $row)
		 {
			$shirename = $row->shirename_shirename;
			break;
			}
			*/

		//$this->db->get_compiled_select();
		//$this->db->_reset_select();

		$select_query = $ppo_db->last_query();

		$output = "MODEL TEST: business_name = [$bname] [business_id = 10599250] shirename = [$shirename] select_query = [$select_query]";
		return $output;
	}


	/*
	 public function model_test() {
		$ppo_db = $this->EE->load->database('ppo', TRUE);

		$query = "SELECT * FROM `local_businesses` WHERE business_id = 10599250";
		$results = $ppo_db->query($query);

		$bname = $results->row('business_name');

		$res2 = $ppo_db->get('shire_names');
		$shirename = '[no data]';

		//$select_query = $ppo_db->get_compiled_select();

		$row = $res2->row_array(2);
		$shirename = $row['shirename_shirename'];


		//$row = $res2->row();
		//$shirename = $row->shirename_shirename;



		// foreach ($res2->result() as $row)
		// {
		//	$shirename = $row->shirename_shirename;
		//	break;
		// }


		//$this->db->get_compiled_select();
		//$this->db->_reset_select();

		$select_query = $ppo_db->last_query();

		$output = "MODEL TEST: business_name = [$bname] [business_id = 10599250] shirename = [$shirename] select_query = [$select_query]";
		return $output;
		}
		*/

}
// End Class
/* End of file tna_commerce_model.php */
/* Location: ./system/expressionengine/third_party/tna_commerce/models/tna_commerce_model */
