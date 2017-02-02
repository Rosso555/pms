<?php
//start session
session_start();
//require configuration file
require_once(dirname(__FILE__).'/setting/setup.php');
require_once(dirname(__FILE__).'/setting/common_setting.php');
require_once(dirname(__FILE__).'/functions/common/common_function.php');
require_once(dirname(__FILE__).'/functions/index/index_function.php');

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

//task: logout by clear session
if('logout' === $task){
  unset($_SESSION['is_patient_login_id']);
  header('Location:'.$index_file.'?task=login');
  exit;
}
//redirect if no session
if(empty($_SESSION['is_patient_login_id'])){
  header('Location:'.$index_file.'?task=login');
  exit;
}
//Task: page not found
if('page_not_found' === $task){
  $smarty_appform->display('patient/page_error_404.tpl');
  exit;
}





$smarty_appform->display('patient/index.tpl');
exit;
?>
