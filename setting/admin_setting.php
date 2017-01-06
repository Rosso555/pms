<?php
//require other files to use in project
require_once('internal_libs/database.class.php');
require_once('internal_libs/common.class.php');
require_once('external_libs/thumbnail.php');
require_once('external_libs/Smarty-3.1.14/libs/SmartyPaginate.class.php');
//create common class object
$common = new common();
//set initalize parameter
$task = $action = $kwd = null;
$next = '';
if(!empty($_GET['task'])) $task = $_GET['task'];
if(!empty($_GET['action'])) $action = $_GET['action'];
if(!empty($_GET['kwd'])) $kwd = $_GET['kwd'];
if(!empty($_GET['next'])) $next = $_GET['next'];

//Smarty class
$smarty_appform = new PMS_SMARTY();
$smarty_appform->assign('smpaginate','common/paginate.tpl');
$smarty_appform->assign('mode', 'admin');
$smarty_appform->assign('admin_file', $admin_file);
$smarty_appform->assign('site_url', $site_url);
/* Paginate */
if(empty($_SESSION['task'])) 		$_SESSION['task'] = $task;
if(empty($_SESSION['action'])) 	$_SESSION['action'] = $action;
if(empty($_SESSION['kwd']))			$_SESSION['kwd'] = $kwd;
SmartyPaginate::disconnect();
SmartyPaginate::connect();
SmartyPaginate::setLimit($limit);
if(SmartyPaginate::getCurrentIndex())		$offset = SmartyPaginate::getCurrentIndex();
if(SmartyPaginate::getLimit())					$limit = SmartyPaginate::getLimit();
//url of pagination
$urlq =  $_SERVER["QUERY_STRING"];
$urlq = preg_replace('/(&)next=(\d+)/', '', $urlq);
SmartyPaginate::setUrl('?'.$urlq);
/* End Paginate */
//varriable initialize
$total_data = null;
$error = '';
// Database connection
$database_connect 		 		= new database(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE_NAME);
$database_connect->debug 	= $debug;
$connected 				 	 			= & $database_connect->_Connect; ?>
