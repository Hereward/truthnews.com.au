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
    public $eway_payment_status = false;
    public $eway_auth_code = '';
    public $error_str = array(); 
    public $test_credit_card = '4444333322221111';
    public $gateway_mode;


    public function __construct() {
        parent::__construct();
        
        $this->gateway_mode = $this->EE->config->item('gateway_mode');
        
        if ($this->gateway_mode == 'live') {
            $this->eway_id = $this->EE->config->item('eway_id');
            $this->eway_name = $this->EE->config->item('eway_name');
            $this->eway_password = $this->EE->config->item('eway_password');
        } else {
            $this->eway_id = $this->EE->config->item('eway_id_sandbox');
            $this->eway_name = $this->EE->config->item('eway_name_sandbox');
            $this->eway_password = $this->EE->config->item('eway_password_sandbox');  
        }

        require_once("$this->eway_path/recurring/nusoap.php");
        require_once("$this->eway_path/direct/EwayPaymentLive.php");
        
        $this->error_str = array(
            'payment' => 'The transaction failed. Please check your credit card details and try again.',
            'init' => 'There was a problem contacting the payment gateway. Please try again in a few minutes.'
        );
        
        dev_log::write("Gateway Mode = [$this->gateway_mode]");

    }

    public function config_vars() {
        return array($this->eway_id, $this->eway_name, $this->eway_password);
    }

    public function init() {
        $result = $this->load_recurring_client();
        
        if (!$result) {
            dev_log::write("eWay recurring_client init failed");
            return false;
            
        } else {
            //$result_str = print_r($result,true);
            dev_log::write("eWay recurring_client init OK");
        }
        
        $result = $this->load_direct_client();
        
        if (!$result) {
            //return false;
            dev_log::write("eWay direct init failed");
        } else {
            //$result_str = print_r($result,true);
            dev_log::write("eWay direct init OK");
        }
        
        //$result_str = print_r($result,true);
        
        //dev_log::write("eWay direct init = $result_str");
        
        return $result;
        
        //$result = $this->load_direct_client();
    }

    public function load_direct_client() {

        $live_gateway_boolean = ($this->gateway_mode == 'live')?true:false;
        
        define('EWAY_DEFAULT_CUSTOMER_ID', $this->eway_id);
        define('EWAY_DEFAULT_PAYMENT_METHOD', 'REAL_TIME'); // possible values are: REAL_TIME, REAL_TIME_CVN, GEO_IP_ANTI_FRAUD
        define('EWAY_DEFAULT_LIVE_GATEWAY', $live_gateway_boolean); //<false> sets to testing mode, <true> to live mode
     
        
        //define script constants
	//define('REAL_TIME', 'REAL-TIME');
	//define('REAL_TIME_CVN', 'REAL-TIME-CVN');
	//define('GEO_IP_ANTI_FRAUD', 'GEO-IP-ANTI-FRAUD');
        

        //define URLs for payment gateway
        define('EWAY_PAYMENT_LIVE_REAL_TIME', 'https://www.eway.com.au/gateway/xmlpayment.asp');
        define('EWAY_PAYMENT_LIVE_REAL_TIME_TESTING_MODE', 'https://www.eway.com.au/gateway/xmltest/testpage.asp');
        define('EWAY_PAYMENT_LIVE_REAL_TIME_CVN', 'https://www.eway.com.au/gateway_cvn/xmlpayment.asp');
        define('EWAY_PAYMENT_LIVE_REAL_TIME_CVN_TESTING_MODE', 'https://www.eway.com.au/gateway_cvn/xmltest/testpage.asp');
        define('EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD', 'https://www.eway.com.au/gateway_beagle/xmlbeagle.asp');
        define('EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD_TESTING_MODE', 'https://www.eway.com.au/gateway_beagle/test/xmlbeagle_test.asp'); //in testing mode process with REAL-TIME
        define('EWAY_PAYMENT_HOSTED_REAL_TIME', 'https://www.eway.com.au/gateway/payment.asp');
        define('EWAY_PAYMENT_HOSTED_REAL_TIME_TESTING_MODE', 'https://www.eway.com.au/gateway/payment.asp');
        define('EWAY_PAYMENT_HOSTED_REAL_TIME_CVN', 'https://www.eway.com.au/gateway_cvn/payment.asp');
        define('EWAY_PAYMENT_HOSTED_REAL_TIME_CVN_TESTING_MODE', 'https://www.eway.com.au/gateway_cvn/payment.asp');

        $this->direct_client = new EwayPaymentLive($this->eway_id, 'REAL_TIME_CVN', $live_gateway_boolean);
        
        return true;
    }

    public function process_direct_payment($subscription_details) {
        $this->eway_error = '';
/*
        $txtFirstName = $_POST['txtFirstName'];
        $txtLastName = $_POST['txtLastName'];
        $txtEmail = $_POST['txtEmail'];
        $txtAddress = $_POST['txtAddress'];
        $txtPostcode = $_POST['txtPostcode'];
        $txtTxnNumber = $_POST['txtTxnNumber'];
        $txtInvDesc = $_POST['txtInvDesc'];
        $txtInvRef = $_POST['txtInvRef'];
        $txtOption1 = $_POST['txtOption1'];
        $txtOption2 = $_POST['txtOption2'];
        $txtOption3 = $_POST['txtOption3'];
        $txtCCNumber = $_POST['txtCCNumber'];
        $ddlExpiryMonth = $_POST['ddlExpiryMonth'];
        $ddlExpiryYear = $_POST['ddlExpiryYear'];
        $txtCCName = $_POST['txtCCName'];
        $txtCVN = $_POST['txtCVN'];
 
 */
        //$txtAmount = $_POST['txtAmount'];
        
        $txtAmount = $subscription_details->aud_price * 100;
        
        $postage_raw = $this->EE->input->post('postage_cost');
        
        $postage = $postage_raw*100;
        
        $final_amount = $txtAmount+$postage;
        
        
        
        //$txtAmount = '6.50';
        //dev_log::write("process_direct_payment: Base price = [$txtAmount]");
        //dev_log::write("process_direct_payment: Postage = [$postage]");
        //dev_log::write("process_direct_payment: Final Amount = [$final_amount]");
        
        $invoice_description = "TNRA Subscription ($subscription_details->name). Base Price: $" . $subscription_details->aud_price . " | Postage: $" . $postage_raw;
        
        dev_log::write("process_direct_payment: invoice_description = [$invoice_description] FINAL AMOUNT = [$final_amount]");
        
        //$this->eway_error = "Error: An invalid response was recieved from the payment gateway.";
        //return false;
        
        //die("FORCED TERMINATION");
        
        

        // Set the payment details
        //$eway = new EwayPaymentLive($eWAY_CustomerID, $eWAY_PaymentMethod, $eWAY_UseLive);

        $this->direct_client->setTransactionData("TotalAmount", $final_amount); //mandatory field
        $this->direct_client->setTransactionData("CustomerFirstName", $this->EE->input->post('first_name'));
        $this->direct_client->setTransactionData("CustomerLastName", $this->EE->input->post('last_name'));
        $this->direct_client->setTransactionData("CustomerEmail", $this->EE->input->post('email'));
        $this->direct_client->setTransactionData("CustomerAddress", $this->EE->input->post('address') . ' ' . $this->EE->input->post('address_2'));
        $this->direct_client->setTransactionData("CustomerPostcode", $this->EE->input->post('postal_code'));
        $this->direct_client->setTransactionData("CustomerInvoiceDescription", $invoice_description);
        $this->direct_client->setTransactionData("CustomerInvoiceRef", '');
        $this->direct_client->setTransactionData("CardHoldersName", $this->EE->input->post('first_name') . ' ' . $this->EE->input->post('last_name')); //mandatory field
        $this->direct_client->setTransactionData("CardNumber", $this->EE->input->post('cc_number')); //mandatory field
        $this->direct_client->setTransactionData("CardExpiryMonth", $this->EE->input->post('cc_expiry_month')); //mandatory field
        $this->direct_client->setTransactionData("CardExpiryYear", $this->EE->input->post('cc_expiry_year')); //mandatory field
        $this->direct_client->setTransactionData("CVN", $this->EE->input->post('cc_cvn')); //mandatory field
        $this->direct_client->setTransactionData("TrxnNumber", '');
        $this->direct_client->setTransactionData("Option1", '');
        $this->direct_client->setTransactionData("Option2", '');
        $this->direct_client->setTransactionData("Option3", '');
        
        //$td = print_r($this->direct_client->myTransactionData,true);
                
        //dev_log::write("TD = $td");

        $this->direct_client->setCurlPreferences(CURLOPT_SSL_VERIFYPEER, 0); // Require for Windows hosting
        // Send the transaction
        
        //dev_log::write("process_direct_payment:138");
        
        $response_obj = $this->direct_client->doPayment();
        
         //dev_log::write("process_direct_payment:142");
        
        
        $ewayTrxnStatus = (is_object($response_obj))?$response_obj->ewayTrxnStatus:'false';

        if (strtolower($ewayTrxnStatus) == "false") {
            $this->eway_error = "No response from payment gateway."; //$response_obj->ewayTrxnError;
            dev_log::write("ewayTrxnStatus = false | ".$this->EE->eway_model->eway_error);
            //trigger_error("The credit card transaction failed: $this->eway_error");
            
            //var_dump($ewayResponseFields);
            //$this->eway_error .= "Transaction Error: " . $ewayResponseFields["EWAYTRXNERROR"] . "<br>\n";
            /*
            foreach ($ewayResponseFields as $key => $value) {
                $this->eway_error .= "\n<br>$key = $value";
            }
             
             */
            return false;
        } else if (strtolower($ewayTrxnStatus) == "true") {
            // payment succesfully sent to gateway
            // Payment succeeded get values returned
            
            $this->eway_payment_status = true;
            $this->eway_auth_code = $response_obj->ewayAuthCode;
            
            /*
            $lblResult = " Result: " . $ewayResponseFields["EWAYTRXNSTATUS"] . "<br>";
            $lblResult .= " AuthCode: " . $ewayResponseFields["EWAYAUTHCODE"] . "<br>";
            $lblResult .= " Error: " . $ewayResponseFields["EWAYTRXNERROR"] . "<br>";
            $lblResult .= " eWAYInvoiceRef: " . $ewayResponseFields["EWAYTRXNREFERENCE"] . "<br>";
            $lblResult .= " Amount: " . $ewayResponseFields["EWAYRETURNAMOUNT"] . "<br>";
            $lblResult .= " Txn Number: " . $ewayResponseFields["EWAYTRXNNUMBER"] . "<br>";
            $lblResult .= " Option1: " . $ewayResponseFields["EWAYOPTION1"] . "<br>";
            $lblResult .= " Option2: " . $ewayResponseFields["EWAYOPTION2"] . "<br>";
            $lblResult .= " Option3: " . $ewayResponseFields["EWAYOPTION3"] . "<br>";
             * 
             */
       
            //$this->eway_payment_status = $lblResult;
            
            //$ps = print_r($ewayResponseFields,true);
            //dev_log::write("PS = $ps");
            return true;
                
       
        } else {
            // invalid response recieved from server.
            $this->eway_error = "Error: An invalid response was recieved from the payment gateway.";
            return false;
            //echo $lblResult;
        }
    }

    public function load_recurring_client() {
        $this->eway_error = '';
        
        if ($this->gateway_mode == 'live') {
            $this->client = $client = new nusoap_client("https://www.ewaygateway.com/gateway/rebill/manageRebill.asmx", false);
        } else {
            $this->client = new nusoap_client("https://www.eway.com.au/gateway/rebill/test/manageRebill_test.asmx", false);
        }
        //$this->client = $client = new nusoap_client("https://www.ewaygateway.com/gateway/rebill/manageRebill.asmx", false);

        $err = $this->client->getError();
        $message = '';
        if ($err) {
            $message .= 'eWay constructor error = '  . $err;
            //$message .= '<h2>Debug</h2><pre>' . htmlspecialchars($this->client->getDebug(), ENT_QUOTES) . '</pre>';
            $this->eway_error = $message;
            dev_log::write($this->eway_error);
            return false;
        }

        // set namespace
        $this->client->namespaces['man'] = 'http://www.eway.com.au/gateway/rebill/manageRebill';
        // set SOAP header
        $headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $this->eway_id . "</man:eWAYCustomerID><man:Username>" . $this->eway_name . "</man:Username><man:Password>" . $this->eway_password . "</man:Password></man:eWAYHeader>";
        $this->client->setHeaders($headers);
        return true;
    }
    
    public function delete_customer($id, $RebillID) {
        $requestbody = array(
            'man:RebillCustomerID' => $id,
            'man:RebillID' => $RebillID
        );
        $soapaction = 'http://www.eway.com.au/gateway/rebill/manageRebill/DeleteRebillEvent';
        $result = $this->client->call('man:DeleteRebillEvent', $requestbody, '', $soapaction);
        
        //dev_log::write("delete_customer: 1");

        $error = $this->get_eway_rebill_error($result);

        if (!$error) {
            $requestbody = array(
                'man:RebillCustomerID' => $id
            );
            $soapaction = 'http://www.eway.com.au/gateway/rebill/manageRebill/DeleteRebillCustomer';

            

            $result = $this->client->call('man:DeleteRebillCustomer', $requestbody, '', $soapaction);
            //dev_log::write("delete_customer: 2");
            $this->eway_error = $this->get_eway_rebill_error($result);
            //dev_log::write("delete_customer: 3");
            return $result;
        } else {
            return false;
        }
    }

    public function create_customer() {
        $this->eway_error = '';
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

        //log_message('info', print_r($requestbody, true));
        $soapaction = 'http://www.eway.com.au/gateway/rebill/manageRebill/CreateRebillCustomer';
        $result = $this->client->call('man:CreateRebillCustomer', $requestbody, '', $soapaction);
        $this->eway_error = $this->get_eway_rebill_error($result);
        //$result['ErrorDetails'];
     
        return $result;
    }

    function create_event($subscription_details, $RebillCustomerID) {
        $this->eway_error = '';
        $subscription_type = $this->EE->input->post('subscription_type');
        $start_offset = "+1 year";

        $RebillIntervalType = '';
        if ($subscription_type == 'yearly' || $subscription_type == 'yearly_concession') {
            $RebillIntervalType = 4;
        } elseif ($subscription_type == 'monthly') {
            $start_offset = "+1 month";
            $RebillIntervalType = 3;
        }

        $RebillInterval = 1;
        
        $start = date("d/m/Y", strtotime($start_offset));
        $end = date("d/m/Y", strtotime("+20 years"));
        $RebillRecurAmt = $subscription_details->aud_price * 100;
        
        dev_log::write("eway_model:create_event: RebillRecurAmt = $RebillRecurAmt");

        $requestbody = array(
            'man:RebillCustomerID' => $RebillCustomerID,
            'man:RebillInvRef' => '',
            'man:RebillInvDes' => '',
            'man:RebillCCName' => $this->EE->input->post('first_name') . ' ' . $this->EE->input->post('last_name'),
            'man:RebillCCNumber' => $this->EE->input->post('cc_number'),
            'man:RebillCCExpMonth' => $this->EE->input->post('cc_expiry_month'),
            'man:RebillCCExpYear' => $this->EE->input->post('cc_expiry_year'),
            'man:RebillInitAmt' => 0,
            'man:RebillInitDate' => $start,
            'man:RebillRecurAmt' => $RebillRecurAmt,
            'man:RebillStartDate' => $start,
            'man:RebillInterval' => $RebillInterval,
            'man:RebillIntervalType' => $RebillIntervalType,
            'man:RebillEndDate' => $end
        );
        
        $ce_body = print_r($requestbody,true);
        
        dev_log::write("eway_model:create_event: ce_body = $ce_body");
        
        
        //log_message('info', print_r($requestbody, true));

        //die('boo');

        $soapaction = 'http://www.eway.com.au/gateway/rebill/manageRebill/CreateRebillEvent';
        $result = $this->client->call('man:CreateRebillEvent', $requestbody, '', $soapaction);
        //log_message('info', print_r($result, true));
        $this->eway_error = $this->get_eway_rebill_error($result);
        return $result;
    }

    function get_eway_rebill_error($result) {
        //$result_str = print_r($result,true);
        //dev_log::write("eway_model:get_eway_rebill_error: result_str = $result_str");

        $output = '';
        if ($this->client->fault) {
            $output = 'There is a problem with the network. Please try again in a few minutes.';
        } else {
            $output = $result['ErrorDetails'];
        }
        if ($output) {
            dev_log::write("eWay Rebill error = $output");
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
