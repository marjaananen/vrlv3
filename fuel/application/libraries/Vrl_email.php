<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

	// If necessary, modify the path in the require statement below to refer to the 
// location of your Composer autoload.php file.

if($this->config->item('vrl_production')){

	require APPPATH.'/third_party/aws/aws-autoloader.php';
	
	use Aws\Ses\SesClient;
	use Aws\Exception\AwsException;

}

class Vrl_email
{
    
public function __construct()
	{

	}
	
	private function _signature($html = true){
		
		return '<p>Terveisin,<br />
			Virtuaalisen Ratsastajainliiton ylläpito<br />
			'.site_url().'<br />
			<br />
			Tämä on automaattisesti lähetetty viesti. Älä vastaa tähän viestiin!<br />
			</p>';
		
		
	}
    
public function send ($to, $subject, $message){
	return $this->aws_send($to, $subject, $message);
	return;
    /*
            $CI =& get_instance();

		// Check compat first
		$CI->config->load('ion_auth', TRUE);
		$CI->load->library(['email']);
		$CI->load->model('ion_auth_model');

	
		$email_config = $CI->config->item('email_config');

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
    
    */
    
}

function aws_send($to, $subject, $msg){


// Create an SesClient. Change the value of the region parameter if you're 
// using an AWS Region other than US West (Oregon). Change the value of the
// profile parameter if you want to use a profile in your credentials file
// other than the default.
$SesClient = new SesClient([
    'version' => '2010-12-01',
    'region'  => 'eu-west-1'
]);

// Replace sender@example.com with your "From" address.
// This address must be verified with Amazon SES.
$sender_email = 'virtuaalinenratsastajainliitto@gmail.com';

// Replace these sample addresses with the addresses of your recipients. If
// your account is still in the sandbox, these addresses must be verified.
$recipient_emails = array();

if(is_array($to)){
	$recipient_emails = $to;
}else {
	$recipient_emails[] = $to;
}

// Specify a configuration set. If you do not want to use a configuration
// set, comment the following variable, and the
// 'ConfigurationSetName' => $configuration_set argument below.
//$configuration_set = 'ConfigSet';

$plaintext_body = $msg ;
$html_body =  $msg;
$char_set = 'UTF-8';

try {
    $result = $SesClient->sendEmail([
        'Destination' => [
            'ToAddresses' => $recipient_emails,
        ],
        'ReplyToAddresses' => [$sender_email],
        'Source' => $sender_email,
        'Message' => [
          'Body' => [
              'Html' => [
                  'Charset' => $char_set,
                  'Data' => $html_body . $this->_signature(),
              ],
              'Text' => [
                  'Charset' => $char_set,
                  'Data' => $plaintext_body . $this->_signature(false),
              ],
          ],
          'Subject' => [
              'Charset' => $char_set,
              'Data' => '[VRL] '.$subject,
          ],
        ],
        // If you aren't using a configuration set, comment or delete the
        // following line
   //     'ConfigurationSetName' => $configuration_set,
    ]);
    $messageId = $result['MessageId'];
   // echo("Email sent! Message ID: $messageId"."\n");
	return true;
} catch (AwsException $e) {
    // output error message if fails
    echo $e->getMessage();
    echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
    echo "\n";
	return false;
}
	
	
}

 
}



