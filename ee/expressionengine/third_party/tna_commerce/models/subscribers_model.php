<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// Eway Model
//extends CI_Model

class Subscribers_model extends Base_model {

	public $my_var = 'My Var is EMPTY';
	public $orig = '[EMPTY GiGi]';
        public $default_db_prefix;
        public $subscription_types = array();
        


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
        //var_dump($data);
        //die();

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


        //$this->EE->member_model->delete_member($member_id);

        return $member_id;
    }
    
    public function get_subscription_details($type) {
        $this->remove_prefix();
        $result = $this->db->get_where('tna_subscription_types', array('name' => $type));
        $this->restore_prefix();
        return $result->row();
    }
    
     public function set_subscription_types() {
        $this->remove_prefix();
        $m_result = $this->db->get_where('tna_subscription_types', array('name' => 'monthly'));
        $this->subscription_types['monthly'] = $m_result->row();
        $y_result = $this->db->get_where('tna_subscription_types', array('name' => 'yearly'));
        $this->subscription_types['yearly'] = $y_result->row();
        $this->restore_prefix();
        //return $result;
    }
    
    
    
    public function is_subscriber($member_id = '') {
        $this->remove_prefix();
        $result = $this->EE->db->where('member_id',$member_id)->get('tna_subscribers');
        
         $output = false;
        if ($result->num_rows() > 0) {
            $output = $result->row();
        }
        $this->restore_prefix();
        return $output;
    }
    
    
    public function get_subscriber($member_id = '') {
        $this->remove_prefix();
        //$this->EE->db->select('*');
        $this->EE->db->select('tna_subscribers.*', FALSE);
       // $this->EE->db->select('tna_subscriber_details.*', FALSE);
        //$this->EE->db->select('tna_subscriber_details.id AS sd_id', FALSE);
        //$this->EE->db->select('tna_subscriber_details.created AS sd_created', FALSE);
        //$this->EE->db->select('tna_subscriber_details.modified AS sd_modified', FALSE);
        
        $this->EE->db->select('tna_subscriber_details.first_name, '
                . 'tna_subscriber_details.last_name, '
                . 'tna_subscriber_details.company, '
                . 'tna_subscriber_details.address, '
                . 'tna_subscriber_details.address_2, '
                . 'tna_subscriber_details.postal_code, '
                . 'tna_subscriber_details.suburb, '
                . 'tna_subscriber_details.state, '
                . 'tna_subscriber_details.payment_method, '
                . 'tna_subscriber_details.tshirt_size, ',
                FALSE);
        
        $this->EE->db->select('exp_members.email, '
                . 'exp_members.screen_name, ',
                FALSE);
          
        
        
        $this->EE->db->from('tna_subscribers');
        $this->EE->db->where('tna_subscribers.member_id', $member_id); 
        $this->EE->db->join('tna_subscriber_details', 'tna_subscribers.member_id = tna_subscriber_details.member_id');
        $this->EE->db->join('exp_members', 'tna_subscribers.member_id = exp_members.member_id');
        
        $sql_string = $this->EE->db->_compile_select();
        
        //die("SQL = ".$sql_string);
                
        $result = $this->EE->db->get();
        
        //die("NUM ROWS = ". $result->num_rows() . " SQL = ".$sql_string);
        
        
        //$result = $this->EE->db->where('member_id',$member_id)->get('tna_subscribers');
        //$error = '';
        $output = '';
        if ($result->num_rows() > 0) {
            $output = $result->row();
        }
        
        $this->restore_prefix();
        
        return $output;
    }
    
    public function nuke_subscriber($member_id='') { 
        $this->EE->member_model->delete_member($member_id);
        $this->remove_prefix();
        $this->EE->db->delete('tna_subscribers', array('member_id' => $member_id));
        $this->EE->db->delete('tna_subscriber_details', array('member_id' => $member_id));
        $this->EE->db->delete('tna_eway_customers', array('member_id' => $member_id));
        
        $this->restore_prefix();
    }
    

    public function find_duplicate($email = '') {
        $result = $this->EE->db->where('email',$email)->get('exp_members');
        //$error = '';
        $output = false;
        if ($result->num_rows() > 0) {
            $output = $result->row();

           // $errors = 'Your email is already registered to an account.  Please login to your account if you have already registered.';
        }
        return $output;
    }
    
    public function update_tna_subscriber_details($member_id,$params){
        $output = true;
        $this->remove_prefix();

        $this->EE->db->where('member_id', $member_id);
        $this->EE->db->update('tna_subscriber_details', $params);

        if ($this->get_db_error()) {
            $output = false; 
        }  
         $this->restore_prefix();
         return $output;
        
    }
    
     public function set_rebill_details($member_id,$rebill_details) {
         
        dev_log::write("set_rebill_details [$member_id]");
        
        $rebill_details_str = print_r($rebill_details,true);
        dev_log::write($rebill_details_str);
        
        
        $this->remove_prefix();
        $now = date("Y-m-d H:i:s");

        $data = array(
            'member_id' => $member_id,
            'customer_id' => $rebill_details['RebillCustomerID'],
            'rebill_id' => $rebill_details['RebillID'],
            'created' => $now,
            'modified' => $now
        );

        $this->EE->db->insert('tna_eway_customers', $data);
        
         if ($this->get_db_error()) {
             dev_log::write("DB ERROR = $this->db_error");
             
         }
         
         dev_log::write("SQL STRING = $this->sql_string");
         
        $this->restore_prefix();
        return $output;
     }
    

    public function create_tna_subscriber($params) {
        $output = true;
        $tshirt_size = $this->EE->input->post('tshirt_size');

        //die("FUCKING T_SHIRT: $tshirt_size");
        $this->remove_prefix();
        $now = date("Y-m-d H:i:s");

        $data = array(
            'member_id' => $params['member_id'],
            'temp_password' => $params['temp_password'],
            'status' => 'pending',
            'existing_member' => $params['existing_member'],
            'type' => $params['type'],
            'created' => $now,
            'modified' => $now
        );

        $this->EE->db->insert('tna_subscribers', $data);

        if ($this->get_db_error()) {
            $output = false;
        } else {
            $data = array(
                'member_id' => $params['member_id'],
                'tshirt_size' => $tshirt_size,
                'created' => $now,
                'modified' => $now
            );

            $this->EE->db->insert('tna_subscriber_details', $data);
            if ($this->get_db_error()) {
                $output = false;
            }
        }
        
         $this->restore_prefix();
         return $output;
    }

}
// End Class
/* End of file subscribers.php */
/* Location: ./system/expressionengine/third_party/tna_commerce/models/ */
