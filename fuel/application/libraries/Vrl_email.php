<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


class Vrl_email
{

    
public function __construct()
	{

	}
    
public function send ($to, $subject, $message){
    
            $CI =& get_instance();

		// Check compat first
		$CI->config->load('ion_auth', TRUE);
		$CI->load->library(['email']);
		$CI->load->model('ion_auth_model');

	
		$email_config = $CI->config->item('email_config', 'ion_auth');

		if ($CI->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config))
		{
			$CI->email->initialize($email_config);
		}
    
	$CI->email->clear();
	$CI->email->from($CI->config->item('admin_email', 'ion_auth'), $CI->config->item('site_title', 'ion_auth'));
	$CI->email->to($to);
	$CI->email->subject($CI->config->item('email_title', 'ion_auth') . ' ' . $subject);
	$CI->email->message($message);
	if ($CI->email->send() === TRUE)
    	{
		return TRUE;
    	}
                
    return FALSE;
    
}

 
}



