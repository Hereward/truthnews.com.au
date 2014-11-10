<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// Base Model
//extends CI_Model

class Base_model extends CI_Model {

    protected $EE;
    public $eway_path;
    public $country_list;
    public $db_error = '';
    public $subscriber_details_fields;
    public $subscriber_details_gift_fields;
    public $sql_string;
    //public $base_url;

    public function __construct()
    {
        $this->EE =& get_instance();

        $this->eway_path = PATH_THIRD.'tna_commerce/gateways/eway';
        

        $this->EE->load->model('member_model');
        
        $this->set_details_fields_template();
        
        $this->set_details_fields_gift_template();

        //$this->ppo_db = $this->EE->load->database('ppo', TRUE);
        //$this->base_url = $this->EE->config->config['base_url'];
        //set a global object
        //$this->EE->tna_commerce = $this;
    }
    
    public function set_details_fields_template() {

        $this->subscriber_details_fields = array(
            'first_name',
            'last_name',
            'company',
            'address',
            'address_2',
            'suburb',
            'state',
            'postal_code',
            'country',
            'payment_method',
        );
    }
    
    public function set_details_fields_gift_template() {

        $this->subscriber_details_gift_fields = array(
            'r_first_name',
            'r_last_name',
            'r_company',
            'r_address',
            'r_address_2',
            'r_suburb',
            'r_state',
            'r_postal_code',
            'r_country',
            'payment_method',
        );
    }
    
    public function get_details_fields() {
        return $this->subscriber_details_fields;
    }
    
    public function get_details_gift_fields() {
        return $this->subscriber_details_gift_fields;
    }
    
    
    
    public function get_db_error() {
        
        if ($this->EE->db->_error_message()) {
            $error_num = $this->db->_error_number();
            $this->db_error = "DB error ($error_num): ".$this->EE->db->_error_message();
            $this->sql_string = $this->EE->db->last_query();
            $backtrace = $this->EE->tna_commerce_lib->debug_string_backtrace();
            dev_log::write($this->db_error);
            dev_log::write($backtrace);
            
           // $this->sql_string = $this->EE->db->_compile_select();
            return 1;
        } else {
            $this->db_error = '';
            return '';
        }
        
    }


  

/*
    function visitor_country() {
        $ip = $_SERVER["REMOTE_ADDR"];
        if(filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        if(filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        $result = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip))
            ->geoplugin_countryCode;
        return $result <> NULL ? $result : "Unknown";
    }
*/

/*
    function ip2location($property) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if(filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        if(filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        $result = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip))
            ->{"geoplugin_$property"};
        return $result <> NULL ? $result : "Unknown";
    }
*/



    public function urlify($input) {
        $str = preg_replace("/[^A-Za-z0-9\- ]/", '', $input);
        $str = preg_replace('!\s+!', ' ', $input);
        $str = preg_replace('!\'!', '', $input);
        $str_unencoded = strtolower($str);
        $str = urlencode($str_unencoded);

        $root_url = "http://{$_SERVER['SERVER_NAME']}";
        //print_r($this->EE->config->config);
        //die();
        //$root = $_SERVER['DOCUMENT_ROOT'];
        return $str;

    }

    public function sanitize($input) {
        $value = preg_replace("/[^A-Za-z0-9&'\\- ]/", '', $input);
        $value = preg_replace('!\s+!', ' ', $value);
        $value = trim($value);
        //$value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
        //$value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        //die("[$value]");

        //$value = urlencode($value);
        // die("[$value]");
        return $value;
    }

    public function handle_input($input) {

        if(empty($input)) return "";
        //$value = ereg_replace("\[\]$","",$input);
        //$value = preg_replace("\[\]$","",$input);
        $value = preg_replace("/&#124;/", "|", $input);
        $value = stripslashes( html_entity_decode( $value ) );
        return trim($value);
    }

    function get_variable_from_session($key) {
        if (array_key_exists($key,$_SESSION)) {
            if (isset($_SESSION[$key])) {
                return $_SESSION[$key];
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function session_keys_exist($keys='') {
        if (!$keys) {
            $keys = $this->session_keys;
        }

        //var_dump($this->session_keys);
       // die();

        foreach ($keys as $key) {
            if (array_key_exists($key,$_SESSION)) {
                return TRUE;
            } else {
                log_message('info',"SESSION KEY MISSING: $key");
                //die("SESSION KEY MISSING: $key");
                return FALSE;
            }
        }
    }




}
// End Class
/* End of file base_model.php */
/* Location: ./system/expressionengine/third_party/tna_commerce/models/ */
