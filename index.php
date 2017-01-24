<?php
//start session
session_start();
//require configuration file
require_once(dirname(__FILE__).'/setting/setup.php');
require_once(dirname(__FILE__).'/setting/admin_setting.php');
require_once(dirname(__FILE__).'/functions/admin/admin_function.php');
require_once(dirname(__FILE__).'/functions/common/common_function.php');

//create common class object
$common = new common();

$task = $action = $kwd = null;
if(!empty($_GET['task'])) $task = $_GET['task'];
if(!empty($_GET['action'])) $action = $_GET['action'];
if(!empty($_GET['kwd'])) $kwd = $_GET['kwd'];

//Smarty
$smarty_appform = new PMS_SMARTY();
$smarty_appform->assign('admin_file', $index_file);
$smarty_appform->assign('mode', 'index');

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
SmartyPaginate::setUrl('?'.$_SERVER['QUERY_STRING']);
/* End Paginate */
// Database connection
$database_connect 		 		= new database(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE_NAME);
$database_connect->debug 	= $debug;
$connected 				 	 			= & $database_connect->_Connect;
//Get language By default_lang = 1
$result = $common->find('language', $condition = ['default_lang' => 1], $type = 'one');
//Setting language
if(!empty($_GET['lang'])) $lang = $_GET['lang'];
if(empty($lang) and empty($_SESSION['lang'])) $lang = $result['lang_name'];
if(!empty($lang) and empty($_SESSION['lang'])) $_SESSION['lang'] = $lang;
if(!empty($lang) and $lang != $_SESSION['lang']) $_SESSION['lang'] = $lang;
if(empty($lang) and $_SESSION['lang']) $lang = $_SESSION['lang'];
//Smarty assign value
$smarty_appform->assign('lang_name', $result['lang_name']);

//list menu language
$smarty_appform->assign('getLanguage', $common->find('language', $condition = null, $type = 'all'));
//Change language on template
$smarty_appform->assign('multiLang', getMultilang($lang));
if(!empty($_GET['deflang']))
{
  //Update default_lang to zero
  updateDefaultLang();
  if(!empty($_GET['lid'])) $common->update('language', $field = ['default_lang' => 1], $condition = ['id' => $_GET['lid']]);
  header('location: '.$index_file);
  exit;
}
//task home
$smarty_appform->display('index/index.tpl');
exit;
 ?>
