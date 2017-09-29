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

//Get value
$rid = $argv[1];
$tid = $argv[2];

// Get List Mailer Lite By Test Id
$resultMailer = getMailerLiteByTestId($tid);
//Get email from response_answer
$reSponse = $common->find('response_answer', $condition = ['response_id' => $rid, 'is_email' => 1], $type = 'all');

$subscribers = array();
if(!empty($reSponse))
{
  foreach ($reSponse as $key => $value) {
    $username = explode("@", $value['content']);
    $subscribers[] = array('email' => $value['content'], 'name' => $username[0]);
  }
}

if(!empty($resultMailer) && !empty($subscribers))
{
  //fetch mailer lite
  foreach ($resultMailer as $key => $value)
  {
    $groupsApi = (new \MailerLiteApi\MailerLite($value['api_key']))->groups();

    $mailerGroup = $common->find('mailerlite_group', $condition = ['transaction_id' => $value['transaction_id']], $type = 'all');
    //fetch Mailer Lite Group
    foreach ($mailerGroup as $k => $v)
    {
      //Add mail to mailer lite
      $groupsApi->importSubscribers($v['group_id'], $subscribers);
    }
  }
}

?>
