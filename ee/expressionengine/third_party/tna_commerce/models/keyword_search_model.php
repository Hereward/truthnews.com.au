<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// Pink Pages Model
//extends CI_Model

class keyword_search_model extends Base_model {

    public $my_var = 'My Var is EMPTY';
    public $orig = '[EMPTY GiGi]';
    public $resolved_classification;
    public $resolved_location;
    public $keyword;
    public $related_classifications_from_keyword;
    public $related_classifications;
    public $session_keys;

    /*
     protected $EE;
     */
    public function __construct()
    {
        $this->orig = $this->my_var;
        $this->session_keys = array('location', 'keyword','related_classifications_from_keyword','resolved_classification','related_classifications');
        parent::__construct();
    }

    public function get_url($args=array()){

        $state = $this->urlify($args['state']);
        $classification = $this->urlify($args['classification']);
        $identifier = $this->urlify($args['identifier']);
        $id = $this->urlify($args['id']);

        //$keyword
        return $this->base_url."web/search/keyword/$state/$identifier/$classification/$id";
    }

    function build_url_params() {
        $c_id = $this->resolved_classification['localclassification_id'];
        $c_label = $this->resolved_classification['localclassification_name'];
        $url_params['classification'] = $c_label;
        $url_params['id'] = $c_id;
        $url_params['state'] = $this->resolved_location['state'];
        $url_params['identifier'] = $this->resolved_location['identifier'];

        return $url_params;
    }



    public function c_match($keyword) {
        //$first_word_pattern =  "^[[:<:]]{$this->ppo_db->escape_str($keyword)}.*$";

        $w = $this->ppo_db->escape_str($keyword);
        $first_word_pattern =  "^({$w}|{$w}[[.space.]]+.*)$";

        $query = "SELECT
					localclassification_id, localclassification_name
				FROM
					local_classification
				WHERE
					localclassification_name REGEXP '".$first_word_pattern."'";

        //echo $query. '<br/>';

        $results = $this->ppo_db->query($query);

        if ($results->num_rows()) {
            return $results;
        } else {
            return FALSE;
        }
    }

    public function r_match($keyword) {
        $query = "
		SELECT 
		   id,local_classification.localclassification_id,keyword,localclassification_name 
		FROM 
		   keyword_resolve,local_classification 
		WHERE 
		   keyword_resolve.keyword = ".$this->ppo_db->escape($keyword)." 
		AND 
		   local_classification.localclassification_id = keyword_resolve.localclassification_id";

        $results = $this->ppo_db->query($query);

        if ($results->num_rows()) {
            return $results;
        } else {
            return FALSE;
        }
    }


    public function k_match($keyword) {
        //$first_word_pattern =  "^[[:<:]]{$this->ppo_db->escape_str($keyword)}.*$";
        $w = $this->ppo_db->escape_str($keyword);
        $first_word_pattern =  "^({$w}|{$w}[[.space.]]+.*)$";
        //^(car|car[[.space.]]+.*)$
        $query = "SELECT
					id, keywords.localclassification_id, keywords.keyword, local_classification.localclassification_name
				FROM
					keywords,local_classification
				WHERE
					keywords.keyword REGEXP '".$first_word_pattern."' 
				AND 
				    keywords.localclassification_id = local_classification.localclassification_id" ;

        $results = $this->ppo_db->query($query);

        if ($results->num_rows()) {
            return $results;
        } else {
            return FALSE;
        }
    }


    function set_session() {
        $_SESSION['location'] = $this->resolved_location;
        $_SESSION['keyword'] = $this->keyword;
        $_SESSION['related_classifications_from_keyword'] = $this->related_classifications_from_keyword;
        $_SESSION['resolved_classification'] = $this->resolved_classification;
        $_SESSION['related_classifications'] = $this->related_classifications;
    }

    function get_session() {
        $this->resolved_location = (isset($_SESSION['location']))?$_SESSION['location']:'';
        $this->keyword = (isset($_SESSION))?$_SESSION['keyword']:'';
        $this->related_classifications_from_keyword = (isset($_SESSION))?$_SESSION['related_classifications_from_keyword']:'';
        $this->resolved_classification = (isset($_SESSION))?$_SESSION['resolved_classification']:'';
        $this->related_classifications = (isset($_SESSION))?$_SESSION['related_classifications']:'';
    }

    function resolve_location() {
        $vars = array();
        $this->resolved_location['location_string_raw'] = $this->sanitize($_POST['Search2']);
        $this->resolved_location['state'] = 'NSW';
        $this->resolved_location['identifier'] = 'BONDI-2026';
    }


    function resolve_location_from_url() {
        return '';
    }

    function resolve_classification_from_url() {
        $c_id = $this->EE->uri->segment(6);
        //$_SESSION['keyword'] = '';
        $classies = array();
        //$_SESSION['related_classifications_from_keyword'] = $classies;
        $keyword = '';

        $this->EE->keyword_search_model->resolved_classification = $this->EE->keyword_search_model->get_resolved_classification_from_id($c_id);
        //$_SESSION['resolved_classification'] = $this->EE->keyword_search_model->resolved_classification;
        $related_classifications = $this->EE->keyword_search_model->related_classifications($c_id);
        // $_SESSION['related_classifications'] = $related_classifications;
        //$vars = $this->prepare_display_vars($classies, $keyword, $related_classifications);

    }

    function resolve_classification() {
        $keyword = $this->sanitize($_POST['Search1']);
        $this->keyword = $keyword;
        $classies = $this->retrieve_classifications($keyword);
        $this->related_classifications_from_keyword = $classies;
        $this->resolved_classification = $this->resolve_classification_by_type();
        $c_id = $this->resolved_classification['localclassification_id'];
        $this->related_classifications = $this->related_classifications($c_id);

        //$vars = $this->prepare_display_vars($classies,$keyword,$related_classifications);

        //return $vars;
    }


    /*
        public function related_class_links($class_id='',$shire_name='', $shire_town='', $state='', $passed_location='') {
            $defaultLocation = $this->defaultLocation;
            //$location = GeneralUtils::handle_input($_GET['Search2']);
            $location = '';

            if (isset($_GET['shire_town'])) {
                $location = $_GET['shire_town'];
            } elseif (isset($_GET['shire_name'])) {
                $location = $_GET['shire_name'];
            } elseif ($passed_location) {
                $location = $passed_location;
            }

            //$location = (isset($_GET['shire_town']))?$_GET['shire_town']:'';
            //$location = GeneralUtils::handle_input($_GET['shire_town']);
            $location = ($location=="") ? $defaultLocation : $location;
            //$location_tpl = (empty($location))? $defaultLocation : $location;
            //$this->page->assign("location",$location_tpl);

            $classifications = '';
            $classification_ids = array();
            //dev_log::write("relatedClassLinks class_id = ".$class_id);
            $classification_ids = $this->related_class_ids($class_id);

            $str = ($classification_ids)?implode(',', $classification_ids):'';

            // return $str;

            if($location != $defaultLocation){

                $classifications = $this->listingFacade->getClassificationCountByLocation($location, $classification_ids, $this->request->getAttribute("fr"), $this->request->getAttribute("pg_size"));
            } else {

                $classifications = $this->listingFacade->getClassificationCountByAlpha($location, $classification_ids, $this->request->getAttribute("fr"), $this->request->getAttribute("pg_size"));
            }



            //die($str);

            return $classifications;

        }
    */

    public function related_classifications($class_id) {
        //$output = array('classifications'=>'');
        //$output['classifications'] = array();
        $output = array();
        $query = "SELECT * from class_relationships WHERE class_id='$class_id'";


        //dev_log::write("relatedClassLinks query = ".$query);
        // $res = $this->myDB->query($query);

        $res = $this->ppo_db->query($query)->result_array();
        //var_dump($results_raw);
        //die();
        $list = '';

        //$results = $results_raw->result_array();
        $ids = array();
        if (isset($res[0]['related'])) {
            $list = $res[0]['related'];
            //dev_log::write("relatedClassLinks list = ".$list);
            // $ids = explode(',', $list);


            $query = "SELECT
					localclassification_id, localclassification_name
				FROM
					local_classification
				WHERE
					localclassification_id IN ($list)";

            $output[] = array('ID','NAME');
            $output = array_merge($output, $this->ppo_db->query($query)->result_array());
            //$output = $this->ppo_db->query($query)->result_array();

        }



        //var_dump($output);
        //die();
        return $output;
    }


    public function resolve_classification_by_type() {
        //die();
        //$this->related_classifications_from_keyword
        $output = '';
        $match = array('r_match','c_match', 'k_match');
        $types = array('full','short');
        foreach ($types as $type) {
            foreach ($match as $m) {
                if (isset($this->related_classifications_from_keyword[$type][$m][1]['localclassification_id'])) {

                        $output = $this->related_classifications_from_keyword[$type][$m][1];
                        //die("HELLO ".var_dump($output));
                        break 2;

                }
            }
        }

        //var_dump($output);
        //die();

        return $output;
    }

    public function retrieve_classifications($keyword='')
    {
        //$keyword = $this->sanitize($keyword);
        //$keyword = $this->handle_input($keyword);

        $word_array = explode(' ',$keyword);
        //var_dump($word_array);
        // die();
        $short_key = $word_array[0];

        $w_list = array('full'=>$keyword);

        if (count($word_array)>1) {
            $w_list['short'] = $short_key;
        }


        $output = array();

        foreach ($w_list as $label=>$key) {
            $classifications = array();
            $functions = array('c_match', 'r_match', 'k_match');

            foreach ($functions as $f) {
                $classifications[$f] = FALSE;
                $results = $this->$f($key);

                if ($results) {
                    $classifications[$f][] = array('ID','CLASS','KEYWORD','C_NAME');
                    $classifications[$f] = array_merge($classifications[$f], $results->result_array());
                }
            }
            /*
                        if ($label == 'short') {
                            var_dump($classifications);
                            die();
                        }
            */
            $output[$label] = $classifications;
        }

        //var_dump($output);
        //die();

        return $output;

    }



    public function model_test_2() {
        return $this->my_var;
    }

    public function model_test() {
        $this->my_var = 'My Var has been transmofrigied X';
        $ppo_db = $this->EE->load->database('ppo', TRUE);

        $query = "SELECT * FROM `local_businesses` WHERE business_id = 10599250";
        $results = $ppo_db->query($query);

        $bname = $results->row('business_name');

        $res2 = $ppo_db->get('shire_names');
        $shirename = '[no data]';

        //$select_query = $ppo_db->get_compiled_select();

        $row = $res2->row_array(2);
        $shirename = $row['shirename_shirename'];

        /*
         $row = $res2->row();
         $shirename = $row->shirename_shirename;
         */

        /*
         foreach ($res2->result() as $row)
         {
            $shirename = $row->shirename_shirename;
            break;
            }
            */

        //$this->db->get_compiled_select();
        //$this->db->_reset_select();

        $select_query = $ppo_db->last_query();

        $output = "MODEL TEST: business_name = [$bname] [business_id = 10599250] shirename = [$shirename] select_query = [$select_query]";
        return $output;
    }


    /*
     public function model_test() {
        $ppo_db = $this->EE->load->database('ppo', TRUE);

        $query = "SELECT * FROM `local_businesses` WHERE business_id = 10599250";
        $results = $ppo_db->query($query);

        $bname = $results->row('business_name');

        $res2 = $ppo_db->get('shire_names');
        $shirename = '[no data]';

        //$select_query = $ppo_db->get_compiled_select();

        $row = $res2->row_array(2);
        $shirename = $row['shirename_shirename'];


        //$row = $res2->row();
        //$shirename = $row->shirename_shirename;



        // foreach ($res2->result() as $row)
        // {
        //	$shirename = $row->shirename_shirename;
        //	break;
        // }


        //$this->db->get_compiled_select();
        //$this->db->_reset_select();

        $select_query = $ppo_db->last_query();

        $output = "MODEL TEST: business_name = [$bname] [business_id = 10599250] shirename = [$shirename] select_query = [$select_query]";
        return $output;
        }
        */

}
// End Class
/* End of file tna_commerce_model.php */
/* Location: ./system/expressionengine/third_party/tna_commerce/models/tna_commerce_model */
