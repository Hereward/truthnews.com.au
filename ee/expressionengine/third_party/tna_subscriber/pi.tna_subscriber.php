<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$plugin_info = array(
    'pi_name'           => 'TNA Subscriber',
    'pi_version'        => '1.0',
    'pi_author'         => 'Hereward Fenton',
    'pi_author_url'     => 'http://www.truthnews.com.au',
    'pi_description'    => 'Determine if user is a TNA subscriber.',
    'pi_usage'          => tna_subscriber::usage()
);

class Tna_subscriber
{
    public $return_data = '';
    public $member_id;

    public function Tna_subscriber()
    {
        $this->EE =& get_instance();
        $this->member_id = $this->EE->session->userdata('member_id');

        if (!$this->member_id) {
            $this->return_data = ''; 
            return; 
        }
        
        $this->EE->load->model('member_model');
        $query = $this->EE->member_model->get_member_groups();
        
        $groups = array();

        foreach ($query->result_array() as $row) {
            $title = strtolower($row['group_title']);
            $id =  $row['group_id'];
            $groups[$title] = $id;
        }
        
        $subscriber_group_id = $groups['subscribers'];
       
        $query = $this->EE->member_model->get_member_data($this->member_id);
        $row = $query->row(); 
        
        if ($row->group_id == $subscriber_group_id) {
            $this->return_data = 1;
        } else {
           $this->return_data = '';
        }

        //$var = $this->EE->TMPL->fetch_param('var');
        return; 
    }

    public function usage()
    {
        ob_start();
?>
Simply paste in {exp:tna_subscriber} and it will return the subscriber status..
<?php
        $buffer = ob_get_contents();

        ob_end_clean();

        return $buffer;
    }
    // END
}