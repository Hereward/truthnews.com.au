<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// Eway Model
//extends CI_Model

class Transactions_model extends Base_model {

        public $throttle_limit = 6;
        public $throttle_window = '-24 hours';
        public $default_db_prefix;

	public function __construct()
	{
        parent::__construct();

        $this->default_db_prefix = $this->db->dbprefix;

    }

    public function restore_prefix() {
        $this->db->dbprefix = $this->default_db_prefix;
    }

    public function remove_prefix() {
        $this->db->dbprefix = '';
    }
    


    public function create_transaction_data($params) {
        $this->remove_prefix();
        $now = date("Y-m-d H:i:s");
        
        $data = array(
            'success' => $params['success'],
            'eway_auth_code' => $params['eway_auth_code'],
            'ip_address' => $params['ip_address'],
            'email' => $params['email'],
            'created' => $now
        );
        
        $this->EE->db->insert('tna_eway_transactions', $data);
        
        dev_log::write("create_transaction_data: success={$params['success']} "
        . "eway_auth_code: {$params['eway_auth_code']} ip_address={$params['ip_address']} email={$params['email']}");
        
        $this->restore_prefix();
    }
    
    public function throttle_check() {
        $this->remove_prefix();
        $cutoff = strtotime($this->throttle_window);
        $now = date("Y-m-d H:i:s");
        $effective_cut_off = date("Y-m-d H:i:s",$cutoff);
        $result = $this->EE->db->where('created >',$cutoff)->get('tna_eway_transactions');
        $num_rows = $result->num_rows();
        dev_log::write("throttle_check: window=[$this->throttle_window] limit=[$this->throttle_limit] "
                . "effective_cut_off=[$effective_cut_off] now=[$now] num_rows=[$num_rows]");
        $this->restore_prefix();
        if ($num_rows > $this->throttle_limit) {
            return 1;
        }  else {
            return 0;
        }
    }

   

}
// End Class
/* End of file transactions_model.php */
/* Location: ./system/expressionengine/third_party/tna_commerce/models/ */
