<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// Eway Model
//extends CI_Model

class Subscribers_model extends Base_model {

	public $my_var = 'My Var is EMPTY';
	public $orig = '[EMPTY GiGi]';
    public $default_db_prefix;


	/*
	 protected $EE;
	 */
	public function __construct()
	{
        parent::__construct();

        $this->default_db_prefix = $this->db->dbprefix;


		//$this->EE =& get_instance();
		//die('whizzzzz');
		//$this->orig = $this->my_var;

		//set a global object
		//$this->EE->tna_commerce = $this;

       // die("BOO!");
	}

    public function restore_prefix() {
        $this->db->dbprefix = $this->default_db_prefix;
    }

    public function remove_prefix() {
        $this->db->dbprefix = '';
    }



    public function create_ee_member($data) {

        $member_id = $this->EE->member_model->create_member($data);

        // handle custom fields passed in POST
        $exp_member_fields_result = $this->EE->db->get('exp_member_fields');
        $fields = array();
        if ($exp_member_fields_result->num_rows() > 0) {
            foreach ($exp_member_fields_result->result_array() as $field) {
                $fields[$field['m_field_name']] = 'm_field_id_' . $field['m_field_id'];
            }

            $update_fields = array();

            foreach ($fields as $name => $column) {
                $update_fields[$column] = ($this->EE->input->post($name)) ? $this->EE->input->post($name) : '';
            }

            $this->EE->member_model->update_member_data($member_id, $update_fields);
        }

       // setcookie("tna_subscribe_tempdata_3",$member_id);


        $this->EE->member_model->delete_member($member_id);

        return $member_id;
    }
    
    
    public function get_subscriber($member_id = '') {
        $this->remove_prefix();
        $result = $this->EE->db->where('member_id',$member_id)->get('tna_subscribers');
        $error = '';
        $output = '';
        if ($result->num_rows() > 0) {
            $output = $result->row();
        }
        
        $this->restore_prefix();
        
        return $output;
    }
    

    public function find_duplicate_members($email = '') {
        $result = $this->EE->db->where('email',$email)->get('exp_members');
        $error = '';
        if ($result->num_rows() > 0) {

            $errors = 'Your email is already registered to an account.  Please login to your account if you have already registered.';
        }
        return $error;
    }


    public function create_tna_subscriber($member_id,$password){
        $this->remove_prefix();


        $now = date("Y-m-d H:i:s");

        $data = array(
            'member_id' => $member_id,
            'temp_password' => $password,
            'first_name' => $this->EE->input->post('first_name'),
            'last_name' => $this->EE->input->post('last_name'),
            'status' => 'pending',
            'created' => $now,
            'modified' => $now
        );

        $this->EE->db->insert('tna_subscribers', $data);
        $this->restore_prefix();

    }















}
// End Class
/* End of file subscribers.php */
/* Location: ./system/expressionengine/third_party/tna_commerce/models/ */
