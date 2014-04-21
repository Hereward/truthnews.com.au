<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// Base Model
//extends CI_Model

class Base_model extends CI_Model {

    protected $EE;
    public $base_url;

    public function __construct()
    {
        $this->EE =& get_instance();
        $this->ppo_db = $this->EE->load->database('ppo', TRUE);
        $this->base_url = $this->EE->config->config['base_url'];
        //set a global object
        //$this->EE->tna_commerce = $this;
    }

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

    function get_resolved_classification_from_id($id) {

        $query = "SELECT localclassification_id,localclassification_name from local_classification WHERE localclassification_id='$id'";


        //dev_log::write("relatedClassLinks query = ".$query);
        // $res = $this->myDB->query($query);

        $res = $this->ppo_db->query($query)->result_array();

        $ret_array = $res[0];

        $ret_array['keyword'] = '';

        //var_dump($ret_array);
        //die();
        return $ret_array;

        /*
         array(4) {
            ["id"]=>
  string(5) "12070"
  ["localclassification_id"]=>
  string(4) "1898"
  ["keyword"]=>
  string(4) "HAIR"
  ["localclassification_name"]=>
  string(12) "HAIRDRESSERS"
}
         */



    }


}
// End Class
/* End of file base_model.php */
/* Location: ./system/expressionengine/third_party/tna_commerce/models/base_model */
