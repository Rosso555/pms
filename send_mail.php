<?php
//start session
session_start();
//require configuration file
require_once(dirname(__FILE__).'/setting/setup.php');
require_once(dirname(__FILE__).'/setting/common_setting.php');
require_once(dirname(__FILE__).'/functions/common/common_function.php');
require_once(dirname(__FILE__).'/functions/patient/patient_function.php');

// Database connection
$database_connect 		 		= new database(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE_NAME);
$database_connect->debug 	= $debug;
$connected 				 	 			= & $database_connect->_Connect;

//get param value
$patient_login_id = $argv[1];

$get_psy_info = get_psychologist_by_patient($patient_login_id);
$username = $get_psy_info['psy_username'];
$email    = $get_psy_info['psy_email'];
//get last insert id
$get_last_response_id = get_last_response_id($patient_login_id);
$tpid = $get_last_response_id['tpid'];
$tid = $get_last_response_id['tid'];
//Send email
$body = 'Dear '.$username.'

There is patient has test completed.

To view the patient, please click the link below for detail:

'.$site_url.$psychologist_file.'?task=result_test_patient&tid='.$tid.'&pat_id='.$patient_login_id.'&id='.$tpid.'


Thanks you for test.

Best Regard,
'.$mail_signature;
    $message = Swift_Message::newInstance()
            ->setSubject('Psychology Management System (PMS)')
            ->setFrom(array('noreply@e-khmer.com' => 'Psychology Management System (PMS)'))
            ->setTo($email)
            ->setBody($body);

  $result 	= $mailer->send($message);

  setcookie('completed', 'user_register', time() + 50);
  //unset session
  unset($_SESSION['user_register']);

?>
