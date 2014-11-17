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
    
    public function get_postage_costs() {
        $data = array();
        $this->remove_prefix();
        
        $result = $this->db->get_where('tna_postage_costs', array('name' => 'standard_domestic'));
        $row = $result->row();
        $data['standard_domestic'] = $row->aud_price;
        
        $result = $this->db->get_where('tna_postage_costs', array('name' => 'standard_international'));
        $row = $result->row();
        $data['standard_international'] = $row->aud_price;
        
        $result = $this->db->get_where('tna_postage_costs', array('name' => 'standard_us'));
        $row = $result->row();
        $data['standard_us'] = $row->aud_price;
        
        $this->restore_prefix();
        return $data;
    }
    
     public function set_subscription_types() {
        $this->remove_prefix();
        
        $m_result = $this->db->get_where('tna_subscription_types', array('name' => 'monthly'));
        $this->subscription_types['monthly'] = $m_result->row();
        
        $y_result = $this->db->get_where('tna_subscription_types', array('name' => 'yearly'));
        $this->subscription_types['yearly'] = $y_result->row();
 
        $yc_result = $this->db->get_where('tna_subscription_types', array('name' => 'yearly_concession'));
        $this->subscription_types['yearly_concession'] = $yc_result->row();
 
        $this->restore_prefix();
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
    
    public function assign_member_to_group($member_id,$group_id) {
        $output = true;
        
        $params = array(
            'group_id' => $group_id
        );
       
        $this->EE->db->where('member_id', $member_id);
        $this->EE->db->update('exp_members', $params);

        if ($this->get_db_error()) {
            $output = false; 
        }  
   
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
        
        $this->EE->db->select('tna_subscriber_details.member_id, '
                . 'tna_subscriber_details.first_name, '
                . 'tna_subscriber_details.last_name, '
                . 'tna_subscriber_details.company, '
                . 'tna_subscriber_details.address, '
                . 'tna_subscriber_details.address_2, '
                . 'tna_subscriber_details.postal_code, '
                . 'tna_subscriber_details.suburb, '
                . 'tna_subscriber_details.state, '
                . 'tna_subscriber_details.country, '
                . 'tna_subscriber_details.payment_method, ',
                FALSE);
        
        $this->EE->db->select('exp_members.email, '
                . 'exp_members.screen_name, '
                . 'exp_members.username, ',
                FALSE);
        
        $this->EE->db->select('tna_subscriber_tshirts.tshirt_size, '
                . 'tna_subscriber_tshirts.tshirt_status, ',
                FALSE);
          
        
        
        $this->EE->db->from('tna_subscribers');
        $this->EE->db->where('tna_subscribers.member_id', $member_id); 
        $this->EE->db->join('tna_subscriber_details', 'tna_subscribers.member_id = tna_subscriber_details.member_id');
        $this->EE->db->join('exp_members', 'tna_subscribers.member_id = exp_members.member_id');
        $this->EE->db->join('tna_subscriber_tshirts', 'tna_subscribers.member_id = tna_subscriber_tshirts.member_id');
        
        $sql_string = $this->EE->db->_compile_select();
        
        //die("SQL = ".$sql_string);
                
        $result = $this->EE->db->get();
        
        $error = $this->get_db_error();
        
        //die("NUM ROWS = ". $result->num_rows() . " SQL = ".$sql_string);
        
        
        //$result = $this->EE->db->where('member_id',$member_id)->get('tna_subscribers');
        //$error = '';
        $output = '';
        if ($result->num_rows() > 0) {
            $output = $result->row();
        }
        
        $this->restore_prefix();
        
        dev_log::write("get_subscriber: email = $output->email");
        
        return $output;
    }
    
    public function nuke_subscriber($member_id='') { 
        dev_log::write("nuke subscriber: member_id = $member_id");
        $this->EE->member_model->delete_member($member_id);
        $this->remove_prefix();
        $this->EE->db->delete('tna_subscribers', array('member_id' => $member_id));
        $this->EE->db->delete('tna_subscriber_details', array('member_id' => $member_id));
        $this->EE->db->delete('tna_eway_customers', array('member_id' => $member_id));
        $this->EE->db->delete('tna_subscriber_tshirts', array('member_id' => $member_id));

        $this->restore_prefix();
    }
    
    public function delete_subscriber($member_id='') { 
        $output = true;
        $this->remove_prefix();
        
        $this->EE->db->delete('tna_subscribers', array('member_id' => $member_id));
        $this->EE->db->delete('tna_subscriber_details', array('member_id' => $member_id));
        $this->EE->db->delete('tna_eway_customers', array('member_id' => $member_id));
        $this->EE->db->delete('tna_subscriber_tshirts', array('member_id' => $member_id));
        /*
        $member_data = array(
            'group_id' => $target_group_id
        );
         *
         */
        
        $this->restore_prefix();
        
       // $this->EE->member_model->update_member($member_id, $member_data);
        
        dev_log::write("delete_subscriber");

        
        return $output;
        
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
    
     public function eway_customer($member_id = '') {
        $this->remove_prefix();
        $result = $this->EE->db->where('member_id',$member_id)->get('tna_eway_customers');
        //$error = '';
        $output = false;
        if ($result->num_rows() > 0) {
            $output = $result->row();

           // $errors = 'Your email is already registered to an account.  Please login to your account if you have already registered.';
        }
        $this->restore_prefix();
        return $output;
    }
    
    public function update_tna_subscriber_details($member_id,$params){
        $output = true;
        $this->remove_prefix();
        
        $params_str = print_r($params,true);
        
        dev_log::write("update_tna_subscriber_details: params_str = $params_str");

        $this->EE->db->where('member_id', $member_id);
        $this->EE->db->update('tna_subscriber_details', $params);

        if ($this->get_db_error()) {
            $output = false; 
        }  
         $this->restore_prefix();
         return $output;
        
    }
    
    
    public function update_subscriber_group($member_id='',$target_group_id='') {
        
        $member_data = array(
            'group_id' => $target_group_id
        );

        $this->EE->member_model->update_member($member_id, $member_data);

        dev_log::write("update_subscriber_group: > $member_id | $target_group_id");
        
    }
    
     public function set_rebill_details($member_id,$rebill_details) {
        $output = '';
        dev_log::write("set_rebill_details [$member_id]");
        
        $rebill_details_str = print_r($rebill_details,true);
        dev_log::write($rebill_details_str);
        
        
        $this->remove_prefix();
        $now = date("Y-m-d H:i:s");
        
        $cid = (isset($rebill_details['RebillCustomerID']))?$rebill_details['RebillCustomerID']:0;
        $rid = (isset($rebill_details['RebillID']))?$rebill_details['RebillID']:0;

        $data = array(
            'member_id' => $member_id,
            'customer_id' => $cid,
            'rebill_id' => $rid,
            'created' => $now,
            'modified' => $now
        );

        $this->EE->db->insert('tna_eway_customers', $data);

         
         //dev_log::write("SQL STRING = $this->sql_string");
         
        $this->restore_prefix();
        return $output;
     }
     
     
    public function create_cancelled_subscriber($member_id) {
        $subscriber = $this->get_subscriber($member_id);
        //$output = true;
        //$tshirt_size = $this->EE->input->post('tshirt_size');

        //die("FUCKING T_SHIRT: $tshirt_size");
        $this->remove_prefix();
        $now = date("Y-m-d H:i:s");

        $data = array(
            'member_id' => $subscriber->member_id,
            'first_name' => $subscriber->first_name,
            'last_name' => $subscriber->last_name,
            'email' => $subscriber->email,
            'company' => $subscriber->company,
            'comments' => $this->EE->input->post('comments'),
            'created' => $now,
            'modified' => $now
        );

        $this->EE->db->insert('tna_subscriber_cancellations', $data);

        if ($this->get_db_error()) {
            $this->restore_prefix();
            return false;
        }
        
        $this->restore_prefix();
    }
    
    public function create_subscriber_gift($params) {
        $this->remove_prefix();
        $now = date("Y-m-d H:i:s");

        $data = array(
            'r_member_id' => $params['member_id'],
            'first_name' => $params['first_name'],
            'last_name' => $params['last_name'],
            'email' => $params['email'],
            'secret_gift' => $params['secret_gift'],
            'created' => $now
        );

        $this->EE->db->insert('tna_subscriber_gifts', $data);

        if ($this->get_db_error()) {
            $this->restore_prefix();
            return false;
        }
        
        $this->restore_prefix();
    }
    
      public function subscriber_gift_details($member_id) {
        $this->remove_prefix();
        $result = $this->EE->db->where('r_member_id',$member_id)->get('tna_subscriber_gifts');
        
        if ($this->get_db_error()) {
            $this->restore_prefix();
            return false;
        }
        
        //$error = '';
        $output = false;
        if ($result->num_rows() > 0) {
            $output = $result->row();
        }
        
        $this->restore_prefix();
        return $output;
    }
    
    

    public function create_tna_subscriber($params) {
        $output = true;
        $tshirt_size = $params['tshirt_size'];
        //$this->EE->input->post('tshirt_size');

        //die("FUCKING T_SHIRT: $tshirt_size");
        $this->remove_prefix();
        $now = date("Y-m-d H:i:s");

        $data = array(
            'member_id' => $params['member_id'],
            'temp_password' => $params['temp_password'],
            'status' => 'active',
            'existing_member' => $params['existing_member'],
            'type' => $params['type'],
            'include_extras' => $params['include_extras'],
            'created' => $now,
            'modified' => $now
        );

        $this->EE->db->insert('tna_subscribers', $data);
        
        if ($this->get_db_error()) {
                $this->restore_prefix();
                return false;
      
        } else {
            $data = array(
                'member_id' => $params['member_id'],
                'created' => $now,
                'modified' => $now
            );

            $this->EE->db->insert('tna_subscriber_details', $data);
            if ($this->get_db_error()) {
                $this->restore_prefix();
                return false;
            }
            
            $data = array(
                'member_id' => $params['member_id'],
                'tshirt_size' => $tshirt_size,
                'tshirt_status' => 'pending',
                'created' => $now,
                'modified' => $now
            );

            $this->EE->db->insert('tna_subscriber_tshirts', $data);
            
            if ($this->get_db_error()) {
                $this->restore_prefix();
                return false;
            }
        }
        
         $this->restore_prefix();
         return $output;
    }

}
// End Class
/* End of file subscribers.php */
/* Location: ./system/expressionengine/third_party/tna_commerce/models/ */
