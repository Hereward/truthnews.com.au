<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Eway Model
//extends CI_Model

class Eway_model extends Base_model {

    public $my_var = 'My Var is EMPTY';
    public $orig = '[EMPTY GiGi]';
    public $eway_id;
    public $eway_name;
    public $eway_password;
    public $eway_error;

    /*
      protected $EE;
     */

    public function __construct() {
        parent::__construct();

        $this->eway_id = $this->EE->config->item('eway_id');
        $this->eway_name = $this->EE->config->item('eway_name');
        $this->eway_password = $this->EE->config->item('eway_password');

        require_once("$this->eway_path/lib/nusoap.php");

        $this->load_client();

        //$this->EE =& get_instance();
        //die('whizzzzz');
        //$this->orig = $this->my_var;
        //set a global object
        //$this->EE->tna_commerce = $this;
        // die("BOO!");
    }

    public function config_vars() {
        return array($this->eway_id, $this->eway_name, $this->eway_password);
    }

    public function load_client() {
        $this->client = new nusoap_client("https://www.eway.com.au/gateway/rebill/test/manageRebill_test.asmx", false);

        $err = $this->client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
            echo '<h2>Debug</h2><pre>' . htmlspecialchars($this->client->getDebug(), ENT_QUOTES) . '</pre>';
            exit();
        }

        // set namespace
        $this->client->namespaces['man'] = 'http://www.eway.com.au/gateway/rebill/manageRebill';
        // set SOAP header
        $headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $this->eway_id . "</man:eWAYCustomerID><man:Username>" . $this->eway_name . "</man:Username><man:Password>" . $this->eway_password . "</man:Password></man:eWAYHeader>";
        $this->client->setHeaders($headers);
    }

    public function create_customer() {
/*
        $requestbody = array(
            'man:customerCompany' => $this->EE->input->post('company'),
            'man:customerFirstName' => $this->EE->input->post('first_name'),
            'man:customerLastName' => $this->EE->input->post('last_name'),
            'man:customerAddress' => $this->EE->input->post('address') . ' ' . $this->EE->input->post('address_2'),
            'man:customerSuburb' => $this->EE->input->post('suburb'),
            'man:customerState' => $this->EE->input->post('state'),
            'man:customerPostCode' => $this->EE->input->post('postal_code'),
            'man:customerCountry' => $this->EE->input->post('country'),
            'man:customerEmail' => $this->EE->input->post('email')
        );
*/
        //"man:customerTitle" => "",
        $requestbody = array(
            "man:customerTitle" => "",
            "man:customerFirstName" => $this->EE->input->post('first_name'),
            "man:customerLastName" => $this->EE->input->post('last_name'),
            "man:customerAddress" => $this->EE->input->post('address') . ' ' . $this->EE->input->post('address_2'),
            "man:customerSuburb" => $this->EE->input->post('suburb'),
            "man:customerState" => $this->EE->input->post('state'),
            "man:customerCompany" => $this->EE->input->post('company'),
            "man:customerPostCode" => $this->EE->input->post('postal_code'),
            "man:customerCountry" => $this->EE->input->post('country'),
            "man:customerEmail" => $this->EE->input->post('email'),
            "man:customerFax" => "",
            "man:customerPhone1" => "",
            "man:customerPhone2" => "",
            "man:customerRef" => "",
            "man:customerJobDesc" => "",
            "man:customerComments" => "",
            "man:customerURL" => ""
        );

        log_message('info', print_r($requestbody, true));
        $soapaction = 'http://www.eway.com.au/gateway/rebill/manageRebill/CreateRebillCustomer';
        $result = $this->client->call('man:CreateRebillCustomer', $requestbody, '', $soapaction);
        $this->eway_error = $result['ErrorDetails'];
        //$this->get_eway_error($result);
        return $result;
    }

    function get_eway_error($result) {
        $output = '';
        if ($this->client->fault) {
            $output .= 'The request contains an invalid SOAP body.';
            //$output .= print_r($result,true).'</br>';
        } else {
            $err = $this->client->getError();
            if ($err) {
                $output .= '<pre>' . $err . '</pre>';
            }
        }
        return $output;
    }

    /*
     * 'man:customerTitle' => $_POST['customerTitle'],
      'man:customerFax' => $_POST['customerFax'],
      'man:customerPhone1' => $_POST['customerPhone1'],
      'man:customerPhone2' => $_POST['customerPhone2'],
      'man:customerRef' => $_POST['customerRef'],
      'man:customerJobDesc' => $_POST['customerJobDesc'],
      'man:customerComments' => $_POST['customerComments'],
      'man:customerURL' => $_POST['customerURL']
     */
}

// End Class
/* End of file eway_model.php */
/* Location: ./system/expressionengine/third_party/tna_commerce/models/ */
