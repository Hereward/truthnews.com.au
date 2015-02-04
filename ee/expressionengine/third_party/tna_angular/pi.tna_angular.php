<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
$plugin_info = array(
    'pi_name' => 'TNA Angular',
    'pi_version' => '1.0',
    'pi_author' => 'Hereward Fenton',
    'pi_author_url' => 'http://www.truthnews.com.au',
    'pi_description' => 'TNA angular stuff',
    'pi_usage' => tna_angular::usage()
);

class Tna_angular {

    public $return_data = '';
    
    public $function = '';
    public $type = '';

    public function __construct() {
        $this->EE = & get_instance();
        //dev_log::write("TNA ANGULAR construct!");
        
        $this->EE->load->add_package_path(PATH_THIRD . '/tna_commerce');
        //$model_path = PATH_THIRD . 'tna_commerce/models/subscribers_model';
        //dev_log::write("TNA ANGULAR model_path = [$model_path]!");
        require PATH_THIRD . 'tna_commerce/models/base_model.php';
        $this->EE->load->model('subscribers_model');
        
        $this->function = 'validate'; //$this->EE->uri->segment(3);
        $this->type = 'email'; //$this->EE->uri->segment(4);
        dev_log::write("TNA ANGULAR: function=[$this->function], type=[$this->type]");

        //$this->return_data = '';
        //$var = $this->EE->TMPL->fetch_param('var');
        // return; 
    }

    public function validate() {
        /*
        $data = array();
        $data['isUnique'] = false;
        return json_encode($data);
         */
        
        
        
        if ($this->type=='email') {
            $data = $this->email();
            $data_str = var_export($data,true);
            dev_log::write("data_str=[$data_str]");
            return json_encode($data);
            //die();
            //$this->return_data =
        }
        return;
         
         
    }
    
    public function email() {
       
	$_POST = json_decode(file_get_contents("php://input"), true);

        $post = var_export($_POST,true);
        dev_log::write("POST=[$post]");
        
        $errors = array(); // array to hold validation errors
        $data = array(); // array to pass back data

        $email = $this->EE->input->post('email');
        
        if (!$email) {
            $data['isUnique'] = true;
            dev_log::write("EMAIL FIELD EMPTY");
            //$data_str = var_export($data,true);
            //dev_log::write("data_str0=[$data_str]");
            return $data;
        }
        dev_log::write("TNA ANGULAR validate email: [$email]");
        $duplicate = $this->EE->subscribers_model->find_duplicate($email);
        
        if ($duplicate) {
            $data['isUnique'] = false;
            dev_log::write("DUPLICATE EMAIL: $email");
        } else {
            $data['isUnique'] = true;
            dev_log::write("UNIQUE EMAIL: $email");
        }

        return $data;
    }

    public function usage() {
        ob_start();
        ?>
        Simply paste in {exp:tna_angular} and it will return the angular status..
        <?php
        $buffer = ob_get_contents();

        ob_end_clean();

        return $buffer;
    }

    // END
}
