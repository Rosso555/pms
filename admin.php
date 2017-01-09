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
if(!empty($_GET['lang'])) $lang = $_GET['lang'];
if(empty($lang) and empty($_SESSION['lang'])) $lang = 'en';
if(!empty($lang) and empty($_SESSION['lang'])) $_SESSION['lang'] = $lang;
if(!empty($lang) and $lang != $_SESSION['lang']) $_SESSION['lang'] = $lang;
if(empty($lang) and $_SESSION['lang']) $lang = $_SESSION['lang'];

//Smarty
$smarty_appform = new PMS_SMARTY();
$smarty_appform->assign('admin_file', $admin_file);
$smarty_appform->assign('mode', 'admin');

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

//task: login
if('login' === $task){
  $error = array();
  if($_POST)
  {
    //get value from form
    $username = $common->clean_string($_POST['username']);
    $password = $common->clean_string($_POST['password']);
    //add value to session to use in template
    $_SESSION['admin'] = $_POST;
    //form validation
    if(empty($username))  $error['username']  = 1;
    if(empty($password))  $error['password']  = 1;

    if(0 === count($error)){
      //compare username and password in form
      if($admin_username  === $username && $admin_password === md5($password)){
        //assign value to session
        $_SESSION['is_admin_login'] = 'admin';
        //remove session to clear data
        unset($_SESSION['admin']);
        //redirect to admin.php
        header('Location:'.$admin_file);
        exit;
      }
      //wrong username and password
      $error['login'] = 1;
    }
  }
  //default of login task
  $smarty_appform->assign('error', $error);
  $smarty_appform->display('admin/login.tpl');
  exit;
}
//task: logout by clear session
if('logout' === $task){
  unset($_SESSION['is_admin_login']);
  header('Location:'.$admin_file.'?task=login');
  exit;
}
//redirect if no session
if(empty($_SESSION['is_admin_login'])){
  header('Location:'.$admin_file.'?task=login');
  exit;
}
//task staff Role
if('staff_role' === $task)
{
  $error = array();
		if($_POST)
		{
      $id    = $common->clean_string($_POST['id']);
			$title = $common->clean_string($_POST['name']);
      $_SESSION['staff_role'] = $_POST;
			if(empty($title)) $error['title'] = 1;
			if(empty($id) && 0 === count($error))
			{
				$common->save('staff_role',$field = ['name' => $title]);
        $_SESSION['staff_role'] = '';
        unset($_SESSION['staff_role']);
				//Redirect
				header('location:'.$admin_file_name.'?task=staff_role');
				exit;
			}
      if(!empty($id) && 0 === count($error))
      {
        $common->update('staff_role', $field = ['name' => $title], $condition = ['id' => $id]);
        $_SESSION['staff_role'] = '';
        unset($_SESSION['staff_role']);
        //Redirect
        header('location:'.$admin_file_name.'?task=staff_role');
        exit;
      }
    $smarty_appform->assign('error', $error);
	}
  //action delete staff role
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete("staff_role", $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=staff_role');
    exit;
  }
  if('edit' === $action && !empty($_GET['id']))
  {
    $smarty_appform->assign('edit',$common->find('staff_role', $condition = ['id' => $_GET['id']], $type='one'));
  }
  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
	$results = ListStaffRole($kwd);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('list_staff_role', $results);
  $smarty_appform->display('admin/staff_role.tpl');
  exit;
}
//task staff information
if('staff_info' === $task)
{
  $error = array();
		if($_POST)
		{
      $id       = $common->clean_string($_POST['id']);
			$name     = $common->clean_string($_POST['name']);
      $pass     = $common->clean_string($_POST['password']);
      $gender   = $common->clean_string($_POST['gender']);
      $phone   = $common->clean_string($_POST['phone']);
      $staff_role   = $common->clean_string($_POST['staff_role']);
      $_SESSION['staff_info'] = $_POST;
      //check validate form
			if(empty($name))   $error['name'] = 1;
      if(empty($pass))   $error['pass'] = 1;
      if(empty($phone))  $error['phone'] = 1;
      if(empty($staff_role))  $error['staff_role'] = 1;
      if(!empty($password) && !$common->checkPassword($password))
      {
        $error['password'] = 2;
      }
			if(empty($id) && 0 === count($error))
			{
				$common->save('staff',$field = ['name' => $name,'password' => $pass,'gender' => $gender,'phone' => $phone,'staff_role_id'=>$staff_role]);
        $_SESSION['staff_info'] = '';
        unset($_SESSION['staff_info']);
				//Redirect
				header('location:'.$admin_file_name.'?task=staff_info');
				exit;
			}
      if(!empty($id) && 0 === count($error))
      {
        $common->update('staff_role', $field = ['name' => $title], $condition = ['id' => $id]);
        $_SESSION['staff_role'] = '';
        unset($_SESSION['staff_role']);
        //Redirect
        header('location:'.$admin_file_name.'?task=staff_role');
        exit;
      }
    $smarty_appform->assign('error', $error);
	}
  //action delete staff role
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete("staff_role", $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=staff_role');
    exit;
  }
  if('edit' === $action && !empty($_GET['id']))
  {
    $smarty_appform->assign('edit',$common->find('staff_role', $condition = ['id' => $_GET['id']], $type='one'));
  }
  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
	$results = ListStaffInfo($kwd);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('list_staff', $results);
  $smarty_appform->assign('staff_role', $common->find('staff_role', $condition = null, $type = 'all'));
  $smarty_appform->display('admin/staff_info.tpl');
  exit;
}
//task home
$smarty_appform->display('admin/index.tpl');
exit;
 ?>
