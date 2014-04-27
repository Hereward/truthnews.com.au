<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class subscribe_controller extends Base_Controller {

    protected $EE;

    public function __construct($args='') {
        //die('boo');

        parent::__construct($args);

        //$this->member_id = $this->EE->session->userdata('member_id');
    }


    public function index() {
        //$this->resolved = $this->EE->TMPL->fetch_param('resolved');
        //$this->EE->load->model('keyword_search_model');
        $this->EE->load->library('table');

        if ($this->EE->input->post('create_existing_member') && ($this->logged_in)) {
            redirect("/subscribe/payment/$this->member_id");
        } elseif ($this->EE->input->post('create_member')) {
            return $this->store();
        } elseif ($this->logged_in) {
            return $this->create_existing();
        } elseif (!$this->logged_in) {
            return $this->create();
        }


        //die("LOGGED_IN = $this->logged_in");


        //$this->return_data = $this->EE->TMPL->tagdata;

       // $variables[] = array('action' => 'soap.php');
       // return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);


        //$tbl_tmpl = array ( 'table_open'  => '<table border="1" cellpadding="5" cellspacing="1" class="mytable">' );
        //$this->EE->table->set_template($tbl_tmpl);


        //return $this->EE->load->view('resolve_classification', $vars, TRUE);

    }

    public function create() {
        $errors = array();
        $vars = array('site_url'=>$this->site_url, 'errors'=>$errors);
        return $this->EE->load->view('subscribe_new', $vars, TRUE);

    }

    public function create_existing() {
        $errors = array();
        //$this->EE->session->userdata->username;
        $vars = array('site_url'=>$this->site_url,
            'errors'=>$errors,
            'username'=>$this->EE->session->userdata['username'],
            'email'=>$this->EE->session->userdata['email'],
        );
        return $this->EE->load->view('subscribe_existing', $vars, TRUE);

    }

    public function payment() {

        $url_encoded_encrypted_password = $_COOKIE["tna_subscribe_tempdata"];

        $this->password_key = $_COOKIE["tna_subscribe_tempdata_2"];

        $encrypted_password = $_COOKIE["tna_subscribe_tempdata_1"];

        //$url_decoded_password = urldecode($url_encoded_encrypted_password);

        //$decrypted_url_decoded_password = $this->decrypt($url_decoded_password);

        $decrypted_password = $this->decrypt($encrypted_password);

        //die("$encrypted_password<br/>$decrypted_password");

        //$url_decoded_password = urldecode($url_encoded_encrypted_password);

        //$decrypted_url_decoded_password = $this->decrypt($encrypted_password);

        $vars = array('site_url'=>$this->site_url, 'decrypted_password'=>$decrypted_password);
        return $this->EE->load->view('subscribe_payment_card', $vars, TRUE);

    }

    public function store() {

        $errors = array();



        $this->EE->load->helper('security');
        require_once("$this->default_site_path/includes/pwgen.class.php");
        require_once("$this->default_site_path/includes/Encryption_tnra.php");

        $enc = new Encryption_tnra();

        $pwgen = new PWGen();
        $password = $pwgen->generate();
        $this->password_key = $pwgen->generate();

        $data['username']     = $this->EE->input->post('email');
        //$data['password']    = do_hash($this->EE->input->post('password'));
        $data['password']    = do_hash($password);
        $data['email']        = $this->EE->input->post('email');
        $data['ip_address']    = $this->EE->input->ip_address();
        $data['unique_id']    = random_string('encrypt');
        $data['join_date']    = $this->EE->localize->now;
        $data['language']     = $this->EE->config->item('deft_lang');
        $data['timezone']     = ($this->EE->config->item('default_site_timezone') && $this->EE->config->item('default_site_timezone') != '') ? $this->EE->config->item('default_site_timezone') : $this->EE->config->item('server_timezone');
        //$data['daylight_savings'] = ($this->EE->config->item('default_site_dst') && $this->EE->config->item('default_site_dst') != '') ? $this->EE->config->item('default_site_dst') : $this->EE->config->item('daylight_savings');
        $data['time_format'] = ($this->EE->config->item('time_format') && $this->EE->config->item('time_format') != '') ? $this->EE->config->item('time_format') : 'us';
        $data['group_id'] = 1;

        //$email = '';
        $email_query_result = $this->EE->db->where('email',$data['email'])->get('exp_members');
        //$user_query_result = $this->EE->db->where('username',$email)->get('exp_members');

        if ($email_query_result->num_rows() > 0) {
            //die("FUCK 2");
            $errors[] = 'Your email is already registered to an account.  Please login to your account if you have already registered.';
            $vars = array('site_url'=>$this->site_url, 'errors'=>$errors);
            return $this->EE->load->view('subscribe_new', $vars, TRUE);

/*
        if ($this->EE->member_model->get_members('', '', '', $data['username'], '', 'username')->num_rows() > 0)
        {
            $errors[] = "Username already {$data['username']} already exists.";
            $vars = array('site_url'=>$this->site_url, 'errors'=>$errors);
            return $this->EE->load->view('subscribe_new', $vars, TRUE);
            //$this->EE->output->show_user_error('submission', 'Username already exists!!!!');
*/
        } else {

            //die("FUCK 3");


            //$encrypted_password = $enc->encode($password);
            //$decrypted_password = $enc->decode($encrypted_password);




            $encrypted_password = $this->encrypt($password);

            //$url_encoded_encrypted_password = urlencode($encrypted_password);

           // $url_decoded_password = urldecode($url_encoded_encrypted_password);

           // $decrypted_url_decoded_password = $this->decrypt($url_decoded_password);

            setcookie("tna_subscribe_tempdata_1",$encrypted_password);

            setcookie("tna_subscribe_tempdata_2",$this->password_key);





            //die("$password<br/>$encrypted_password<br/>$url_encoded_encrypted_password<br/>$url_decoded_password<br/>$decrypted_url_decoded_password");


            //urldecode($encrypted_password);

            //$encrypted_password = $this->encrypt(urlencode($password));

           // $decrypted_password = urldecode($this->decrypt($encrypted_password));

           // die("PASS=[$password] ENC=[$encrypted_password] DEC=[$decrypted_password] KEY=[$this->password_key]");

            $member_id = $this->EE->member_model->create_member($data);

            //$this->EE->member_model->delete_member($member_id);

            redirect("/subscribe/payment/$member_id");
        }

        //$this->EE->member_model->delete_member($member_id);
    }

    public function show() {

    }

    public function edit() {

    }

    public function update() {

    }

    public function destroy() {

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





}
