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





}
// End Class
/* End of file tna_commerce_model.php */
/* Location: ./system/expressionengine/third_party/tna_commerce/models/tna_commerce_model */
