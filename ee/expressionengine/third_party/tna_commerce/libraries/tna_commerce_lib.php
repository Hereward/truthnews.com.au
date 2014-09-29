<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tna_commerce_lib {

    public $default_site_path;
    public $tna_server_environment;
    public $admin_email;
    protected $password_key;
    protected $gateway_mode;

    /**
     * constructor
     * 
     * @return	void
     */
    public function __construct() {
        $this->EE = & get_instance();
        $this->default_site_path = $this->EE->config->item('default_site_path');
        require_once("$this->default_site_path/includes/phpmailer/PHPMailerAutoload.php");
        $this->admin_email = $this->EE->config->item('admin_email');
        $this->dispatch_email = $this->EE->config->item('dispatch_email');
        $this->tna_server_environment = $this->EE->config->item('tna_server_environment');
        $this->gateway_mode = $this->EE->config->item('gateway_mode');
        $this->password_key = "bazooka";

        //set a global object
        //$this->EE->tna_commerce = $this;
    }

    function debug_string_backtrace() {
        ob_start();
        debug_print_backtrace(0, 3);
        $trace = ob_get_contents();
        ob_end_clean();

        // Remove first item from backtrace as it's this function which 
        // is redundant. 
        $trace = preg_replace('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);

        // Renumber backtrace items. 
        $trace = preg_replace('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);

        return $trace;
    }

    public function set_password_key($password_key) {
        $this->password_key = $password_key;
    }

    public function email_test() {
        $message = "Line 1\r\nLine 2\r\nLine 3";

        // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap($message, 70, "\r\n");

        // Send
        $result = mail('editor@truthnews.com.au', 'Booojam My Friend!', $message);
        return $result;
    }

    public function email_test_2() {
        require_once("$this->default_site_path/includes/phpmailer/PHPMailerAutoload.php");

        $mail = new PHPMailer;

        $mail->From = 'hereward@planetonline.com.au'; //truth.news.australia@gmail.com hereward@planetonline.com.au
        $mail->FromName = 'Adolf Hitler';
        $mail->addAddress('editor@truthnews.com.au', 'Truth News Editor');     // Add a recipient
        //$mail->addAddress('ellen@example.com');               // Name is optional
        $mail->addReplyTo('hereward@planetonline.com.au', 'Adolf Hitler');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Achtung my Aryan Volk!';
        $mail->Body = 'We will annex the Sudentenland (HTML)!';
        $mail->AltBody = 'We will annex the Sudentenland (plain text for non-HTML mail clients)!';

        $msg = '';
        if (!$mail->send()) {
            $msg = 'PHPMailer test message could not be sent. ';
            $msg .= 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $msg = 'PHPMailer test message has been sent';
        }

        dev_log::write($msg);

        return $msg;
    }

    function email_test_3() {
        $visitor_name = "Dumb Stuff";
        $message = "You are an idiot.";
        //set the recipient email address, where to send emails to
        $to_email = 'editor@truthnews.com.au';
        //set the sender email address
        $your_email = 'editor@truthnews.com.au';
        //use your email address as the sender
        $header = "From: " . $your_email . "\r\n";
        //put the site visitor's address in the Reply-To header
        $header .= "Reply-To: " . $to_email . "\r\n";
        //set the email Subject using the site visitor's name
        $subject = "Pointless message from " . $visitor_name;
        //set the email body with all the site visitor's information
        $emailMessage = "Name: " . 'Boojam!' . "\r\n";
        $emailMessage .= "Email: " . $to_email . "\r\n";
        $emailMessage .= "Message: " . $message . "\r\n";
        //send the email
        mail($to_email, $subject, $emailMessage, $header);
    }

    function email_test_4() {
        $mail = new PHPMailer;

        if ($this->tna_server_environment == 'hosted') {
            $mail->isSMTP();
            $mail->Host = 'mail.truthnews.com.au';
            //$mail->Host = "localhost"; 
            $mail->SMTPAuth = true;
            $mail->Username = 'admin@truthnews.com.au';
            $mail->Password = 'pullit911';
            //$mail->SMTPSecure = 'tls';
        }




        $mail->From = 'admin@truthnews.com.au'; //truth.news.australia@gmail.com hereward@planetonline.com.au
        $mail->FromName = 'Adolf Hitler';
        $mail->addAddress('editor@truthnews.com.au', 'Truth News Editor');     // Add a recipient
        //$mail->addAddress('ellen@example.com');               // Name is optional
        $mail->addReplyTo('admin@truthnews.com.au', 'Adolf Hitler');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'New test Message';
        $mail->Body = 'We will annex the Sudentenland (HTML)!';
        $mail->AltBody = 'We will annex the Sudentenland (plain text for non-HTML mail clients)!';

        $msg = '';
        if (!$mail->send()) {
            $msg = 'PHPMailer test message 4 could not be sent. ';
            $msg .= 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $msg = 'Yay! PHPMailer test message 4 has been sent';
        }

        dev_log::write($msg);

        return $msg;
    }

    public function send_email_notification($params = array()) {
        dev_log::write('send_email_notification');
        $plain_path = $params['plain_path']; //'email/cc_confirmation_plain';
        $html_path = $params['html_path']; //'email/cc_confirmation_html';
        //$customer_subject = 'Credit Card Payment received!'
        //$admin_subject = "Credit Card Payment [{$vars['cc_email']}]";
        //var_dump($params);
        //die();
        $plain = $this->EE->load->view($plain_path, $params, TRUE);
        $html = $this->EE->load->view($html_path, $params, TRUE);

        $mail = new PHPMailer;

        if ($this->tna_server_environment == 'hosted') {
            $mail->isSMTP();
            $mail->Host = 'mail.truthnews.com.au';
            //$mail->Host = "localhost"; 
            $mail->SMTPAuth = true;
            $mail->Username = 'admin@truthnews.com.au';
            $mail->Password = 'pullit911';
            //$mail->SMTPSecure = 'tls';
        }

        $mail->From = $this->admin_email; //truth.news.australia@gmail.com hereward@planetonline.com.au
        $mail->FromName = 'Truth News Australia';
        $mail->addAddress($params['customer_email']);

        $mail->addBCC($this->admin_email);
        if ($this->gateway_mode == 'live') {
            $mail->addBCC($this->dispatch_email);
        }

        $mail->addReplyTo($this->admin_email, 'Truth News Australia');

        $mail->WordWrap = 70;                                 // Set word wrap to 50 characters
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = $params['subject'];
        $mail->Body = $html;
        $mail->AltBody = $plain;

        $msg = '';
        if (!$mail->send()) {
            $msg = "{$params['tag']} message to {$params['customer_email']} was not sent.";
            $msg .= 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $msg = "{$params['tag']} message to {$params['customer_email']} was sent.";
            //dev_log::write('send_email_notification: EMAIL SENT');
        }

        dev_log::write($msg);

        return $msg;
    }
    
    public function send_fraud_warning($params = array()) {
        $params['plain_path'] = 'email/fraud_warning_plain';
        $params['html_path'] = 'email/fraud_warning_html';
        $params['subject'] = "Suspected Fraud - Warning ({$params['cc_name']})";
        $params['customer_email'] = $params['email'];
        $params['tag'] = 'Fraud Warning';

        return $this->send_email_notification($params);
        
    }

    public function send_subscription_confirmation($params = array()) {
        //dev_log::write('send_subscription_confirmation');
        $params['plain_path'] = 'email/subscribe_success_plain';
        $params['html_path'] = 'email/subscribe_success_html';
        $params['subject'] = "Your Truth News Australia Subscription ({$params['subscriber']->first_name} {$params['subscriber']->last_name}: {$params['subscriber']->type})";
        $params['customer_email'] = $params['subscriber']->email;
        $params['tag'] = 'subscription confirmation';



        return $this->send_email_notification($params);

        //These details have also been sent to your email address: $subscriber->email
    }

    public function send_cc_confirmation($params = array()) {

        $params['plain_path'] = 'email/cc_confirmation_plain';
        $params['html_path'] = 'email/cc_confirmation_html';
        $params['subject'] = 'Credit Card payment received';
        $params['tag'] = 'payment confirmation';

        return $this->send_email_notification($params);

        /*
          $plain_path = 'email/cc_confirmation_plain';
          $html_path = 'email/cc_confirmation_html';
          //$customer_subject = 'Credit Card Payment received!'
          //$admin_subject = "Credit Card Payment [{$vars['cc_email']}]";
          $plain = $this->EE->load->view($plain_path, $vars, TRUE);
          $html = $this->EE->load->view($html_path, $vars, TRUE);

          $mail = new PHPMailer;

          $mail->From = $this->admin_email; //truth.news.australia@gmail.com hereward@planetonline.com.au
          $mail->FromName = 'Truth News Australia';
          $mail->addAddress($vars['cc_email']);
          $mail->addCC($this->admin_email);

          $mail->addReplyTo($this->admin_email, 'Truth News Australia');

          $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
          //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
          //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
          $mail->isHTML(true);                                  // Set email format to HTML

          $mail->Subject = 'Credit Card payment received';
          $mail->Body = $html;
          $mail->AltBody = $plain;

          $msg = '';
          if (!$mail->send()) {
          $msg = "Message to {$vars['cc_email']} was not sent.";
          $msg .= 'Mailer Error: ' . $mail->ErrorInfo;
          } else {
          $msg = "Message to {$vars['cc_email']} was sent.";
          }

          dev_log::write($msg);

          return $msg;
         * 
         */
    }

    public function library_test() {
        return "Library test was successful";
    }

    function encrypt($string = '') {
        //$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->password_key), $string, MCRYPT_MODE_CBC, md5(md5($this->password_key))));

        $iv = mcrypt_create_iv(
                mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM
        );

        $encrypted = base64_encode(
                $iv .
                mcrypt_encrypt(
                        MCRYPT_RIJNDAEL_256, hash('sha256', $this->password_key, true), $string, MCRYPT_MODE_CBC, $iv
                )
        );



        return $encrypted;
    }

    function decrypt($encrypted_string = '') {
        //$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->password_key), base64_decode($encrypted_string), MCRYPT_MODE_CBC, md5(md5($this->password_key))), "\0");


        $data = base64_decode($encrypted_string);
        $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC));

        $decrypted = rtrim(
                mcrypt_decrypt(
                        MCRYPT_RIJNDAEL_256, hash('sha256', $this->password_key, true), substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)), MCRYPT_MODE_CBC, $iv
                ), "\0"
        );

        return $decrypted;
    }

    function get_countrylist() {
        $country_list = array(
            "AF" => "Afghanistan",
            "AL" => "Albania",
            "DZ" => "Algeria",
            "AS" => "American Samoa",
            "AD" => "Andorra",
            "AO" => "Angola",
            "AI" => "Anguilla",
            "AQ" => "Antarctica",
            "AG" => "Antigua and Barbuda",
            "AR" => "Argentina",
            "AM" => "Armenia",
            "AW" => "Aruba",
            "AU" => "Australia",
            "AT" => "Austria",
            "AZ" => "Azerbaijan",
            "BS" => "Bahamas",
            "BH" => "Bahrain",
            "BD" => "Bangladesh",
            "BB" => "Barbados",
            "BY" => "Belarus",
            "BE" => "Belgium",
            "BZ" => "Belize",
            "BJ" => "Benin",
            "BM" => "Bermuda",
            "BT" => "Bhutan",
            "BO" => "Bolivia",
            "BA" => "Bosnia and Herzegovina",
            "BW" => "Botswana",
            "BV" => "Bouvet Island",
            "BR" => "Brazil",
            "BQ" => "British Antarctic Territory",
            "IO" => "British Indian Ocean Territory",
            "VG" => "British Virgin Islands",
            "BN" => "Brunei",
            "BG" => "Bulgaria",
            "BF" => "Burkina Faso",
            "BI" => "Burundi",
            "KH" => "Cambodia",
            "CM" => "Cameroon",
            "CA" => "Canada",
            "CT" => "Canton and Enderbury Islands",
            "CV" => "Cape Verde",
            "KY" => "Cayman Islands",
            "CF" => "Central African Republic",
            "TD" => "Chad",
            "CL" => "Chile",
            "CN" => "China",
            "CX" => "Christmas Island",
            "CC" => "Cocos [Keeling] Islands",
            "CO" => "Colombia",
            "KM" => "Comoros",
            "CG" => "Congo - Brazzaville",
            "CD" => "Congo - Kinshasa",
            "CK" => "Cook Islands",
            "CR" => "Costa Rica",
            "HR" => "Croatia",
            "CU" => "Cuba",
            "CY" => "Cyprus",
            "CZ" => "Czech Republic",
            "CI" => "Côte d’Ivoire",
            "DK" => "Denmark",
            "DJ" => "Djibouti",
            "DM" => "Dominica",
            "DO" => "Dominican Republic",
            "NQ" => "Dronning Maud Land",
            "DD" => "East Germany",
            "EC" => "Ecuador",
            "EG" => "Egypt",
            "SV" => "El Salvador",
            "GQ" => "Equatorial Guinea",
            "ER" => "Eritrea",
            "EE" => "Estonia",
            "ET" => "Ethiopia",
            "FK" => "Falkland Islands",
            "FO" => "Faroe Islands",
            "FJ" => "Fiji",
            "FI" => "Finland",
            "FR" => "France",
            "GF" => "French Guiana",
            "PF" => "French Polynesia",
            "TF" => "French Southern Territories",
            "FQ" => "French Southern and Antarctic Territories",
            "GA" => "Gabon",
            "GM" => "Gambia",
            "GE" => "Georgia",
            "DE" => "Germany",
            "GH" => "Ghana",
            "GI" => "Gibraltar",
            "GR" => "Greece",
            "GL" => "Greenland",
            "GD" => "Grenada",
            "GP" => "Guadeloupe",
            "GU" => "Guam",
            "GT" => "Guatemala",
            "GG" => "Guernsey",
            "GN" => "Guinea",
            "GW" => "Guinea-Bissau",
            "GY" => "Guyana",
            "HT" => "Haiti",
            "HM" => "Heard Island and McDonald Islands",
            "HN" => "Honduras",
            "HK" => "Hong Kong SAR China",
            "HU" => "Hungary",
            "IS" => "Iceland",
            "IN" => "India",
            "ID" => "Indonesia",
            "IR" => "Iran",
            "IQ" => "Iraq",
            "IE" => "Ireland",
            "IM" => "Isle of Man",
            "IL" => "Israel",
            "IT" => "Italy",
            "JM" => "Jamaica",
            "JP" => "Japan",
            "JE" => "Jersey",
            "JT" => "Johnston Island",
            "JO" => "Jordan",
            "KZ" => "Kazakhstan",
            "KE" => "Kenya",
            "KI" => "Kiribati",
            "KW" => "Kuwait",
            "KG" => "Kyrgyzstan",
            "LA" => "Laos",
            "LV" => "Latvia",
            "LB" => "Lebanon",
            "LS" => "Lesotho",
            "LR" => "Liberia",
            "LY" => "Libya",
            "LI" => "Liechtenstein",
            "LT" => "Lithuania",
            "LU" => "Luxembourg",
            "MO" => "Macau SAR China",
            "MK" => "Macedonia",
            "MG" => "Madagascar",
            "MW" => "Malawi",
            "MY" => "Malaysia",
            "MV" => "Maldives",
            "ML" => "Mali",
            "MT" => "Malta",
            "MH" => "Marshall Islands",
            "MQ" => "Martinique",
            "MR" => "Mauritania",
            "MU" => "Mauritius",
            "YT" => "Mayotte",
            "FX" => "Metropolitan France",
            "MX" => "Mexico",
            "FM" => "Micronesia",
            "MI" => "Midway Islands",
            "MD" => "Moldova",
            "MC" => "Monaco",
            "MN" => "Mongolia",
            "ME" => "Montenegro",
            "MS" => "Montserrat",
            "MA" => "Morocco",
            "MZ" => "Mozambique",
            "MM" => "Myanmar [Burma]",
            "NA" => "Namibia",
            "NR" => "Nauru",
            "NP" => "Nepal",
            "NL" => "Netherlands",
            "AN" => "Netherlands Antilles",
            "NT" => "Neutral Zone",
            "NC" => "New Caledonia",
            "NZ" => "New Zealand",
            "NI" => "Nicaragua",
            "NE" => "Niger",
            "NG" => "Nigeria",
            "NU" => "Niue",
            "NF" => "Norfolk Island",
            "KP" => "North Korea",
            "VD" => "North Vietnam",
            "MP" => "Northern Mariana Islands",
            "NO" => "Norway",
            "OM" => "Oman",
            "PC" => "Pacific Islands Trust Territory",
            "PK" => "Pakistan",
            "PW" => "Palau",
            "PS" => "Palestinian Territories",
            "PA" => "Panama",
            "PZ" => "Panama Canal Zone",
            "PG" => "Papua New Guinea",
            "PY" => "Paraguay",
            "YD" => "People's Democratic Republic of Yemen",
            "PE" => "Peru",
            "PH" => "Philippines",
            "PN" => "Pitcairn Islands",
            "PL" => "Poland",
            "PT" => "Portugal",
            "PR" => "Puerto Rico",
            "QA" => "Qatar",
            "RO" => "Romania",
            "RU" => "Russia",
            "RW" => "Rwanda",
            "RE" => "Réunion",
            "BL" => "Saint Barthélemy",
            "SH" => "Saint Helena",
            "KN" => "Saint Kitts and Nevis",
            "LC" => "Saint Lucia",
            "MF" => "Saint Martin",
            "PM" => "Saint Pierre and Miquelon",
            "VC" => "Saint Vincent and the Grenadines",
            "WS" => "Samoa",
            "SM" => "San Marino",
            "SA" => "Saudi Arabia",
            "SN" => "Senegal",
            "RS" => "Serbia",
            "CS" => "Serbia and Montenegro",
            "SC" => "Seychelles",
            "SL" => "Sierra Leone",
            "SG" => "Singapore",
            "SK" => "Slovakia",
            "SI" => "Slovenia",
            "SB" => "Solomon Islands",
            "SO" => "Somalia",
            "ZA" => "South Africa",
            "GS" => "South Georgia and the South Sandwich Islands",
            "KR" => "South Korea",
            "ES" => "Spain",
            "LK" => "Sri Lanka",
            "SD" => "Sudan",
            "SR" => "Suriname",
            "SJ" => "Svalbard and Jan Mayen",
            "SZ" => "Swaziland",
            "SE" => "Sweden",
            "CH" => "Switzerland",
            "SY" => "Syria",
            "ST" => "São Tomé and Príncipe",
            "TW" => "Taiwan",
            "TJ" => "Tajikistan",
            "TZ" => "Tanzania",
            "TH" => "Thailand",
            "TL" => "Timor-Leste",
            "TG" => "Togo",
            "TK" => "Tokelau",
            "TO" => "Tonga",
            "TT" => "Trinidad and Tobago",
            "TN" => "Tunisia",
            "TR" => "Turkey",
            "TM" => "Turkmenistan",
            "TC" => "Turks and Caicos Islands",
            "TV" => "Tuvalu",
            "UM" => "U.S. Minor Outlying Islands",
            "PU" => "U.S. Miscellaneous Pacific Islands",
            "VI" => "U.S. Virgin Islands",
            "UG" => "Uganda",
            "UA" => "Ukraine",
            "SU" => "Union of Soviet Socialist Republics",
            "AE" => "United Arab Emirates",
            "GB" => "United Kingdom",
            "US" => "United States",
            "ZZ" => "Unknown or Invalid Region",
            "UY" => "Uruguay",
            "UZ" => "Uzbekistan",
            "VU" => "Vanuatu",
            "VA" => "Vatican City",
            "VE" => "Venezuela",
            "VN" => "Vietnam",
            "WK" => "Wake Island",
            "WF" => "Wallis and Futuna",
            "EH" => "Western Sahara",
            "YE" => "Yemen",
            "ZM" => "Zambia",
            "ZW" => "Zimbabwe",
            "AX" => "Åland Islands",
        );

        return $country_list;
    }

    function ip2location($property) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        $result = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip))
                ->{"geoplugin_$property"};
        return $result <> NULL ? $result : "Unknown";
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

        if (empty($input))
            return "";
        //$value = ereg_replace("\[\]$","",$input);
        //$value = preg_replace("\[\]$","",$input);
        $value = preg_replace("/&#124;/", "|", $input);
        $value = stripslashes(html_entity_decode($value));
        return trim($value);
    }

    function get_variable_from_session($key) {
        if (array_key_exists($key, $_SESSION)) {
            if (isset($_SESSION[$key])) {
                return $_SESSION[$key];
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function session_keys_exist($keys = '') {
        if (!$keys) {
            $keys = $this->session_keys;
        }

        //var_dump($this->session_keys);
        // die();

        foreach ($keys as $key) {
            if (array_key_exists($key, $_SESSION)) {
                return TRUE;
            } else {
                log_message('info', "SESSION KEY MISSING: $key");
                //die("SESSION KEY MISSING: $key");
                return FALSE;
            }
        }
    }

    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
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
/* Location: ./ee/expressionengine/third_party/tna_commerce/libraries/tna_commerce_lib.php */
