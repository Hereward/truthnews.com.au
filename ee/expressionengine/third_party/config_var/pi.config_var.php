<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$plugin_info = array(
    'pi_name'           => 'Config Vars',
    'pi_version'        => '1.0',
    'pi_author'         => 'Carl Crawley',
    'pi_author_url'     => 'http://www.madebyhippo.com',
    'pi_description'    => 'Returns Config var based on input',
    'pi_usage'          => config_var::usage()
);

class Config_var
{
    public $return_data = '';

    public function Config_var()
    {
        $this->EE =& get_instance();

        $var = $this->EE->TMPL->fetch_param('var');

        $this->return_data = $this->EE->config->item($var); 

        return; 
    }

    public function usage()
    {
        ob_start();
?>
Simply pass the variable you want to return in the tag {exp:config_var var="xxx"} and it will return the variable from the config.
<?php
        $buffer = ob_get_contents();

        ob_end_clean();

        return $buffer;
    }
    // END
}