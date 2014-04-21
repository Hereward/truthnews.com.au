<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class keyword_search_controller extends Base_Controller {

    protected $EE;
    public $resolved;


    public function __construct($args='') {
        //die('boo');
        parent::__construct($args);
    }

    public function index2() {

    }
    public function index() {

        $this->resolved = $this->EE->TMPL->fetch_param('resolved');
        $this->EE->load->model('keyword_search_model');
        $this->EE->load->library('table');
        $tbl_tmpl = array ( 'table_open'  => '<table border="1" cellpadding="5" cellspacing="1" class="mytable">' );
        $this->EE->table->set_template($tbl_tmpl);
        $vars = '';
        $c_vars = '';
        $l_vars = '';
        //log_message('info', 'The purpose of some variable is to provide some value.');
        if ($this->resolved) {

            if ($this->EE->keyword_search_model->session_keys_exist()) {
                $this->EE->keyword_search_model->get_session();
                //$this->EE->keyword_search_model->resolved_classification = $_SESSION['resolved_classification'];
                //$vars = $this->prepare_display_vars($_SESSION['related_classifications_from_keyword'],$_SESSION['keyword'],$_SESSION['related_classifications']);
            } else {
                $this->EE->keyword_search_model->resolve_classification_from_url();
                $this->EE->keyword_search_model->resolve_location_from_url();
            }

            $c_vars = $this->classification_search();
            $l_vars = $this->location_search();
        } else {
            $this->EE->keyword_search_model->resolve_classification();
            $this->EE->keyword_search_model->resolve_location();
            $this->EE->keyword_search_model->set_session();


        }
        $c_vars = $this->prepare_classification_vars();
        $l_vars = $this->prepare_location_vars();
        $vars = array_merge($c_vars,$l_vars);

        //var_dump($c_vars);
        //die();

        $url_params =  $this->EE->keyword_search_model->build_url_params();
        //var_dump($url_params);
        //die();
        $url = $this->EE->keyword_search_model->get_url($url_params);
        $vars['url'] = $url;

        return $this->EE->load->view('resolve_classification', $vars, TRUE);

    }




    function prepare_location_vars() {
        $vars['state'] = $this->EE->keyword_search_model->resolved_location['state'];
        $vars['identifier'] = $this->EE->keyword_search_model->resolved_location['identifier'];
        return $vars;

    }

    function prepare_classification_vars() {

        $classies = $this->EE->keyword_search_model->related_classifications_from_keyword;
        $keyword = $this->EE->keyword_search_model->keyword;
        $related_classifications = $this->EE->keyword_search_model->related_classifications;

        //die("Hairdressers - Men's ".urlencode("Hairdressers - Men's"));

        $vars = array();
        $vars['resolved_state'] = ($this->resolved)?"RESOLVED":"NOT RESOLVED";
        //$url_params = array();
        //$url_params['state'] = 'nsw';
        //$url_params['region'] = 'rose bay-2029';

        $word_array = explode(' ',$keyword);
        $short_key = $word_array[0];
        if ($this->EE->keyword_search_model->resolved_classification) {

           // die("BOOOO!");
            $c_id = $this->EE->keyword_search_model->resolved_classification['localclassification_id'];


            $vars['c_id'] = $c_id;
            $c_label = $this->EE->keyword_search_model->resolved_classification['localclassification_name'];
           // $url_params['classification'] = $c_label;
           // $url_params['id'] = $c_id;
            $vars['c_label'] = $c_label;

           // $url = $this->EE->keyword_search_model->get_url($url_params);


            /*
            //$this->keyword_search_model->get_variable_from_session('related_classifications');
            $related_classifications = ($this->keyword_search_model->session_key_exists($key))?
                $this->keyword_search_model->get_variable_from_session('related_classifications'):
                $this->EE->keyword_search_model->related_classifications($c_id);
            */

            if (count($related_classifications)) {
                $related_classifications_tbl = $this->EE->table->generate($related_classifications);
            }
        }



        $t_count = count($classies);
        $res_01 = array();
        $functions = array('c_match', 'r_match', 'k_match');

        foreach ($functions as $function) {
            if ($t_count) {
                $res_01[$function] = ($classies['full'][$function])?$this->EE->table->generate($classies['full'][$function]):'<span style = "color:red">Zero Matches</span>';
                $vars[$function] = $res_01[$function];
            } else {
                $vars[$function] = '';

            }

        }

        if ($t_count > 1) {
            $res_01 = array();
            $functions = array('c_match', 'r_match', 'k_match');
            foreach ($functions as $function) {
                $res_01[$function] = ($classies['short'][$function])?$this->EE->table->generate($classies['short'][$function]):'<span style = "color:red">Zero Matches</span>';
                $vars[$function.'_short'] = $res_01[$function];
            }
        }

        $vars['keyword'] = $keyword;
        $vars['short_key'] = $short_key;
        //$vars['area'] = $location;
        $vars['t_count'] = $t_count;
        $vars['related_classifications'] = $related_classifications_tbl;

        //$vars['url'] = $url;

        return $vars;
    }

    public function classification_search() {


        return '';

    }

    /*

public function categorySearch()
    {

        dev_log::cur_url();
        if(isset($_GET['shire_name'])  &&  !empty($_GET['shire_name'])){
            $regionAlias        = $_GET['shire_name'];
            $_GET['shire_name'] = $this->listingFacade->getShireNameFromAlias($_GET['shire_name']);
        }




        $shire_name							= (!empty($_GET['shire_name']))?$_GET['shire_name']:NULL;
        $shire_town							= (!empty($_GET['shire_town']))?$_GET['shire_town']:NULL;
        $state                              = (!empty($_GET['state']))?$_GET['state']:NULL;
        $location = '';

        if($state == ''){
            $state = ($this->listingFacade->isStateExistsBySuburb($shire_town)) ? $this->listingFacade->isStateExistsBySuburb($shire_town) : $this->listingFacade->isStateExistsByRegion($shire_name);
        }

        $selectArray 						= array();

        $do         						= $_GET['do'];
        $action								= $_GET['action'];
        $this->page->assign("do",$do);
        $this->page->assign("action",$action);

        if(isset($_GET['shire_name']) && $_GET['shire_name'] !='') {
            $location = $_GET['shire_name'];
            //dev_log::write("categorySearch: LOCATION = $location");
            $searchSuburbs    = $this->searchRefineFacade->getSuburbsByRegion($location);
            $suburbURLs       = array();
            foreach($searchSuburbs as $searchSuburb){
                $suburb = urlencode($searchSuburb['shiretown_townname']);
                $suburbURLs[] = array($searchSuburb['shiretown_townname']=>$this->request->createURL("Listing", "categorySearch", "category=".urlencode($this->request->getAttribute('category'))."&state={$state}&shire_town={$suburb}&search={$this->request->getAttribute('search')}"));
            }
            $searchArea     = (count($searchSuburbs)>0) ? 'region' : '';
            $suburbCount = count($searchSuburbs);
            $this->page->assign("suburbCount", $suburbCount);
            $this->page->assign("suburbURLs", $suburbURLs);
            //$this->page->assign("suburb_change", "javascript:window.location='".$this->request->createURL("Listing","categorySearch", "search='")."+this.value");
            $this->page->assign("suburb_change", "javascript:window.location=this.value");
        }
        elseif(isset($_GET['postcode'])) {
            $location = $_GET['postcode'];
        }
        elseif(isset($_GET['shire_town']) && $_GET['shire_town'] !='') {
            $location = $_GET['shire_town'];
            $state    = $_GET['state'];
            $searchArea       = 'suburb';
            $searchRegions    = $this->searchRefineFacade->getRegionBySuburb($location, $state);
            $regionURLs       = array();
            foreach($searchRegions as $searchRegion){
                $regionURLs[]   =  array($searchRegion['shirename_shirename']=>$this->request->createURL("Listing", "categorySearch", "category=" . urlencode($this->request->getAttribute('category')) ."&state={$state}"."&shire_name=" . urlencode($searchRegion['url_alias']) . "&search={$this->request->getAttribute('search')}"));
            }
            $this->page->assign("regionCount", count($regionURLs));
            $this->page->assign("regionURLs", $regionURLs);
        }
        else {

            if ($state) {
                if ($state == 'NSW') {
                    $location = 'All Sydney';
                } elseif ($state == 'VIC') {
                    $location = 'All Melbourne';
                } elseif ($state == 'QLD') {
                    $location = 'All Brisbane';
                } elseif ($state == 'ACT') {
                    $location = 'Canberra Region';
                } elseif ($state == 'NT') {
                    $location = 'All Darwin';
                } elseif ($state == 'WA') {
                    $location = 'All Perth';
                } elseif ($state == 'SA') {
                    $location = 'All Adelaide';
                } elseif ($state == 'TAS') {
                    $location = 'All Hobart';
                } else {
                    $location = 'All States';
                }

            } else {
                $location = 'All Sydney';

            }

        }

        //Assign suburb/region search area
        if(isset($searchArea)){
            $this->page->assign("searchArea", $searchArea);
        }

        //Get the Classificiation Description
        $classificationID = $this->request->getAttribute('search');
        $page     = ($this->request->getAttribute('pnum')) ? $this->request->getAttribute('pnum') : 1;

        $description =  $this->cf->getClassificationDescription($classificationID);
        $this->page->assign("description", $description);

        //Get the Classification Snippets
        $snippets = $this->cf->getClassificationSnippet($classificationID, $page);
        $this->page->assign("snippets", $snippets);

        $bannerArray=$this->listingFacade->getBanner("4");
        $this->page->assign("bannerArray",$bannerArray);



        $bannerArrayA=$this->listingFacade->getBannerA("5");
        $this->page->assign("bannerArrayA",$bannerArrayA);

        $bannerArrayB=$this->listingFacade->getBannerB("5");
        $this->page->assign("bannerArrayB",$bannerArrayB);

        $bannerArrayC=$this->listingFacade->getBannerC("5");
        $this->page->assign("bannerArrayC",$bannerArrayC);

        $bannerArrayD=$this->listingFacade->getBannerD("5");
        $this->page->assign("bannerArrayD",$bannerArrayD);

        $bannerArrayE=$this->listingFacade->getBannerE("5");
        $this->page->assign("bannerArrayE",$bannerArrayE);

        $this->page->assign("home",$this->request->createURL("Affiliate", "showhomePageAffiliate"));
        //$this->listingFacade->categorySearchCount($_GET); // Hereward 20121003 - remove redundant code
        $res = $this->listingFacade->categorySearchResult($this->request->getAttribute("fr"), $this->request->getAttribute("pg_size"), $_GET);

        if($res['is_exclude']) {
            $this->page->assign("is_exclude",1);
            $this->page->assign("exclude_count",$res['exclude_count']);
            $this->page->assign("exclude_url",SITE_PATH."main.php?".$this->request->replaceQS($_SERVER['QUERY_STRING'], array("exclude"=>1, "fr"=>0, "pnum"=>1)));
            $this->page->assign("include_url",SITE_PATH."main.php?".$this->request->replaceQS($_SERVER['QUERY_STRING'], array("exclude"=>0, "fr"=>0, "pnum"=>1)));
        }
        $this->page->assign("brandArray",$res['brands']);
        $this->page->assign("fetch_service",$res['services']);
        $this->page->assign("fetch_payment",$res['payments']);
        $this->page->assign("fetch_hours",$res['hours']);
        $this->page->assign("current_page",$page);


        if(isset($_GET['service']))$selectArray['service'] 		= $_GET['service'];
        if(isset($_GET['hours']))$selectArray['hours'] 		= $_GET['hours'];
        if(isset($_GET['payment']))$selectArray['payment'] 		= $_GET['payment'];
        if(isset($_GET['keyword']))$selectArray['keyword'] 	= $_GET['keyword'];
        if(isset($_GET['brand']))$selectArray['brand'] 		= $_GET['brand'];
        $this->page->assign("selectArray",$selectArray);

        $this->page->addJsFile("bsn.AutoSuggest_2.1.3.js");
        $this->page->addCssStyle("autosuggest_inquisitor.css");

        $category = urldecode(ucwords(strtolower($_GET['category'])));
        //$default_keyword  = urldecode(ucwords(strtolower($_GET['category'])));
        $default_keyword  = $this->resolve_keyword($location,true);
        $adult = 0;

        if ($category == 'Adult Entertainment' || $category == 'Escorts') {
            $adult = 1;
            //die("ADULT!!!! [$category]");
        }


        $location = ucwords(strtolower($location));
        $keyword = $this->resolve_keyword($location,false);

        $this->page->assign("category", $category);
        $this->page->assign("keyword" , $keyword);
        $this->page->assign("default_keyword" , $default_keyword);

        $this->page->assign("adult" , $adult);


        $this->page->assign("location", $location);

        $sortby			= (!empty($_GET['sortby']))?$_GET['sortby']:NULL;
        $this->page->assign("sortby",$sortby);

        $state			= (!empty($_GET['state']))?$_GET['state']:NULL;
        $state			= explode("__",$state);

        $shirename		= (!empty($_GET['shirename']))?$_GET['shirename']:NULL;
        $shirename		= explode("__",$shirename);

        $shiretown		= (!empty($_GET['shiretown']))?$_GET['shiretown']:NULL;
        $shiretownval	= explode("__",$shiretown);

        $locationRes	= $this->listingFacade->locationDisplay();
        $refineDisplay	= $this->listingFacade->refineDisplay($_GET);

        $this->page->assign("refineDisplay",$refineDisplay);
        if(count($state)>0)
        {
            $shirenameRes = $this->listingFacade->regionDisplay($state);
            $this->page->assign("shirenameRes",$shirenameRes);
        }
        if(count($shirename)>0)
        {
            $shiretown = $this->listingFacade->shireTownDisplay($shirename);
            $this->page->assign("shiretown",$shiretown);
        }
        $this->page->assign("locationRes",$locationRes);
        $this->page->assign("state", $state[0]);
        $this->page->assign("shirename", $shirename[0]);
        $this->page->assign("shiretownval", $shiretownval[0]);

        $CountResult		=count($res);
        $this->page->assign("CountResult",$CountResult);
        $normalcount		= count($res['blogs']);
        $this->page->assign("normalcount",$normalcount);

        $this->page->assign("values", $res['blogs']);
        $this->page->assign("paging", $res['paging']);

        $this->page->assign("CountResult",$res['paging']['totalRecords']);

        $hrs = array();
        for($i=1;$i<=24;$i++){

            $hrs[] = $i;

        }
        $this->page->assign("hrs",$hrs);

        //Assign Metatags and Page Title
        $cnt           = (empty($res['paging']['totalRecords'])) ? $normalcount : $res['paging']['totalRecords'];
        if($shire_name != '')
        {
            $canonicalType = 'region';
            $canonicalUrl  = $this->url->getCanonical($canonicalType, $_GET);
            $this->page->pageTitle 			= $category . " Listing in " . $location . ", " . $state[0] . "&#58; Pink Pages Australia";
            $this->page->addMetaDescription("Looking for $category located in the $location? Pink Pages Australia has " . $cnt . " " . $category . " listing in " . $location . ".");
            if($canonicalUrl){
                $this->page->addCanonical(SITE_PATH . $canonicalUrl);
            }

            if($location && $state[0] && $category){
                $region  = $location;
                $state   = $state[0];
                $details = array("region" => $region, "state" => $state, "category" => $category);

                $this->url->setListingDetails($details);
            }

        }elseif($shire_town != '')
        {
            $canonicalType = 'suburb';
            $canonicalUrl  = $this->url->getCanonical($canonicalType, $_GET);
            $region        = $this->url->getRegionFromSuburb($location);
            $this->page->pageTitle 			= $category . " Listing in " . $location . "," . $region . ", " . $state[0] . "&#58; Pink Pages Australia";
            $this->page->addMetaDescription("Looking for $category located in $location of $region, $state[0]? Pink Pages Australia has " . $cnt . " " . $category . " listings in " . $location . ", as well as many in the surrounding " . $region);
            if($canonicalUrl){
                $this->page->addCanonical(SITE_PATH . $canonicalUrl);
            }

            if($location && $region && $state[0] && $category){
                $suburb  = $location;
                $state   = $state[0];
                $details = array("suburb" => $suburb, "region" => $region, "state" => $state, "category" => $category);

                $this->url->setListingDetails($details);
            }

        }else{
            $this->page->pageTitle 			= $category . " Business Listings&#58; Pink Pages Australia";
            $canonicalType = 'state';
            $canonicalUrl  = $this->url->getCanonical($canonicalType, $_GET);
            $this->page->addMetaDescription("Looking for ".$category."? Pink Pages Australia has an extensive directory of ".$category." listings in all major capitals and throughout regional Australia.");
            if($canonicalUrl){
                $this->page->addCanonical(SITE_PATH . $canonicalUrl);
            }

            //Reset Search Session Variables
            $this->url->setListingDetails();
        }

        $this->page->addMetaTags("robots", "noodp,noydir");

        $category_search = $this->request->createURL("Listing", "categorySearch", "category");
        $this->page->assign("category_search",$category_search);
        $this->page->assign("service_change", "javascript:window.location='".$this->request->createNaturalURL("Listing","categorySearch", 		 "search={$this->request->getAttribute('search')}&category={$this->request->getAttribute('category')}&shire_town={$this->request->getAttribute('shire_town')}&shire_name={$this->request->getAttribute('shire_name')}&hours={$this->request->getAttribute('hours')}&payment={$this->request->getAttribute('payment')}&brand={$this->request->getAttribute('brand')}&val={$this->request->getAttribute('val')}&service='")."+this.value");
        $this->page->assign("hour_change", "javascript:window.location='".$this->request->createNaturalURL("Listing", "categorySearch", 	 "search={$this->request->getAttribute('search')}&category={$this->request->getAttribute('category')}&shire_town={$this->request->getAttribute('shire_town')}&shire_name={$this->request->getAttribute('shire_name')}&val={$this->request->getAttribute('val')}& payment={$this->request->getAttribute('payment')}&brand={$this->request->getAttribute('brand')}&service={$this->request->getAttribute('service')}&hours='")."+this.value");
        $this->page->assign("payment_change", "javascript:window.location='".$this->request->createNaturalURL("Listing", "categorySearch", 	"search={$this->request->getAttribute('search')}&category={$this->request->getAttribute('category')}&shire_town={$this->request->getAttribute('shire_town')}&shire_name={$this->request->getAttribute('shire_name')}&val={$this->request->getAttribute('val')}&	service={$this->request->getAttribute('service')}&hours={$this->request->getAttribute('hours')}&brand={$this->request->getAttribute('brand')}&payment='")."+this.value");
        $this->page->assign("keyword_change", "javascript:window.location='".$this->request->createNaturalURL("Listing", "categorySearch", "search={$this->request->getAttribute('search')}&category={$this->request->getAttribute('category')}&val={$this->request->getAttribute('val')}&service={$this->request->getAttribute('service')}&hours={$this->request->getAttribute('hours')}&payment={$this->request->getAttribute('payment')}&keyword='")."+this.value");
        $this->page->assign("brand_change", "javascript:window.location='".$this->request->createNaturalURL("Listing", "categorySearch", "search={$this->request->getAttribute('search')}&category={$this->request->getAttribute('category')}&shire_town={$this->request->getAttribute('shire_town')}&shire_name={$this->request->getAttribute('shire_name')}&val={$this->request->getAttribute('val')}& service={$this->request->getAttribute('service')}&hours={$this->request->getAttribute('hours')}&payment={$this->request->getAttribute('payment')}&keyword={$this->request->getAttribute('keyword')}&brand='")."+this.value");

        $this->page->assign("contactUs",$this->request->createURL("Listing", "contactUs","ID"));
        $_GET['pnum'] = (isset($_GET['pnum']) && $_GET['pnum'])?$_GET['pnum']:1;
        $relatedClassLinks = $this->relatedClassLinks($classificationID,'','','',$location);
        $related_class_count = 0;
        $related_classifications = '';
        if ($relatedClassLinks) {
            $related_class_count = count($relatedClassLinks['classifications']);
            $related_classifications = $relatedClassLinks['classifications'];
        }

        $this->page->assign("related_class_count", $related_class_count);
        //$this->page->assign("relatedClassLinks", $relatedClassLinks);
        $this->page->assign("relatedClassLinks", $related_classifications);

        $this->page->assign('allow_sidebar', 1);

        //dev_log::cur_url("Listing::categorySearch");
        $this->page->getPage('category_result.tpl');
    }
     */

}
