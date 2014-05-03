<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Tna_commerce_lib
{
	
	
	/**
	 * constructor
	 * 
	 * @return	void
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		
		//set a global object
		//$this->EE->tna_commerce = $this;
	}
	
	public function library_test() {
		return "Library test was successful";
	}
        
        
         function encrypt($string='') {
        //$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->password_key), $string, MCRYPT_MODE_CBC, md5(md5($this->password_key))));

        $iv = mcrypt_create_iv(
            mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC),
            MCRYPT_DEV_URANDOM
        );

        $encrypted = base64_encode(
            $iv .
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    hash('sha256', $this->password_key, true),
                    $string,
                    MCRYPT_MODE_CBC,
                    $iv
                )
        );



        return $encrypted;
    }

    function decrypt($encrypted_string='') {
        //$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->password_key), base64_decode($encrypted_string), MCRYPT_MODE_CBC, md5(md5($this->password_key))), "\0");


        $data = base64_decode($encrypted_string);
        $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC));

        $decrypted = rtrim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256,
                hash('sha256', $this->password_key, true),
                substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)),
                MCRYPT_MODE_CBC,
                $iv
            ),
            "\0"
        );

        return $decrypted;
    }

	
	
/*
$this->EE->load->helper('form');
		$this->EE->router->set_class('cp');
		$this->EE->load->library('cp');
		$this->EE->router->set_class('ee');
		$this->EE->load->library('javascript');
		$this->EE->load->library('api');
		$this->EE->load->library('form_validation');
		$this->EE->api->instantiate('channel_fields');
		$this->load_channel_standalone();
		
		$this->EE->lang->loadfile('content');
		$this->EE->lang->loadfile('upload');
		
		$this->EE->javascript->output('if (typeof SafeCracker == "undefined" || ! SafeCracker) { var SafeCracker = {markItUpFields:{}};}');
 */

}

/* End of file tna_commerce_lib.php */
/* Location: ./ppadmin/expressionengine/third_party/tna_commerce/libraries/tna_commerce_lib.php */
