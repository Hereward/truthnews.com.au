<?php

require_once dirname(__FILE__).'/controllers/base_controller.php';
require_once dirname(__FILE__).'/models/base_model.php';

class Tna_commerce {

    public $return_data = '';

    public function __construct()
    {
        $this->EE =& get_instance();
    }
	
	public function load($class='',$args='',$action='index') {
        if (!$action) {$action = 'index';}
		require_once dirname(__FILE__)."/controllers/$class.php";
        //die("ACTION = [$action]");
    	$obj = new $class();
    	return $obj->$action($args);
	}
	
     public function keyword_search($args='') {
    	 return $this->load(__FUNCTION__.'_controller',$args);
     }
	
    public function test($args='') {
    	 return $this->load(__FUNCTION__.'_controller',$args);
     }
     
     
     public function test_2($args='') {
    	 return $this->load(__FUNCTION__.'_controller',$args);
     }

    public function subscribe($args='') {
        //die('boooo');
        $action = $this->EE->TMPL->fetch_param('action');
       // if (!$action) {$action = '';}
        return $this->load(__FUNCTION__.'_controller',$args,$action);
    }
     
	
}
