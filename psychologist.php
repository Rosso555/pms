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
  unset($_SESSION['is_psycho_login_id']);
  header('Location:'.$index_file.'?task=login');
  exit;
}
//redirect if no session
if(empty($_SESSION['is_psycho_login_id'])){
  header('Location:'.$index_file.'?task=login');
  exit;
}
//Task: page not found
if('page_not_found' === $task){
  $smarty_appform->display('psychologist/page_error_404.tpl');
  exit;
}
//Task: patient
if('patient' === $task)
{
  $error = array();
  if($_POST){
    //get value from form
    $id     = $common->clean_string($_POST['id']);
    $username = $common->clean_string($_POST['username']);
    $email  = $common->clean_string($_POST['email']);
    $phone  = $common->clean_string($_POST['phone']);
    $gender = $common->clean_string($_POST['gender']);
    $age    = $common->clean_string($_POST['age']);
    $password = $common->clean_string($_POST['password']);
    //add value to session to use in template
    $_SESSION['patient'] = $_POST;
    //form validation
    if(empty($username))  $error['username']  = 1;
    if(empty($email))   $error['email']   = 1;
    if(empty($phone))   $error['phone']   = 1;
    if(empty($gender))  $error['gender']  = 1;
    if(empty($age))     $error['age']     = 1;
    if(empty($password))  $error['password']  = 1;
    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
		   $error['invalid_email'] = 1;
		}
    if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
      $existed = check_patient_email($email);
      if($existed > 0){
        $error['exist_email'] = 1;
      }
    }
    //Save
    if(empty($id) && COUNT($error) === 0){
      $common->save('patient', $field =['psychologist_id' => $_SESSION['is_psycho_login_id'],
                                        'username'=> $username,
                                        'email'   => $email,
                                        'phone'   => $phone,
                                        'gender'  => $gender,
                                        'age'     => $age,
                                        'password'=> $password]);
    //unset session
    unset($_SESSION['patient']);
    //Redirect
    header('location: '.$psychologist_file.'?task=patient');
    exit;
    }
    //Update
    if(!empty($id) && COUNT($error) === 0){
      $common->update('patient', $field= ['username' => $username,
                                          'email'    => $email,
                                          'phone'    => $phone,
                                          'gender'   => $gender,
                                          'age'      => $age,
                                          'password' => $password],
                                 $condition = ['id' => $_GET['id'], 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
    //unset session
    unset($_SESSION['patient']);
    //Redirect
    header('location: '.$psychologist_file.'?task=patient');
    exit;
    }
  }
  //Change staus patient
  if('change_status' === $action && !empty($_GET['id']))
  {
    if(!empty($_GET['status'] == 1))
    {
      $common->update('patient', $field = ['status' => 2], $condition = ['id' => $_GET['id'], 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
    }elseif (!empty($_GET['status'] == 2)) {
      $common->update('patient', $field = ['status' => 1], $condition = ['id' => $_GET['id'], 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
    }
    header('location:'.$psychologist_file.'?task=patient');
    exit;
  }
  //action delete staff role
  if('delete' === $action && !empty($_GET['id']))
  {
    $deleted_at = date("Y-m-d");
    $common->update('patient', $field = ['deleted_at' => $deleted_at], $condition = ['id' => $_GET['id'], 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
    header('location:'.$psychologist_file.'?task=patient');
    exit;
  }
  //Action: edit
  if('edit' === $action && !empty($_GET['id']))
  {
    $resutlGetPatientById = getPatientByID($_GET['id'], $_SESSION['is_psycho_login_id']);

    if(!empty($resutlGetPatientById) && COUNT($resutlGetPatientById) > 0){
      $smarty_appform->assign('editPatient', $resutlGetPatientById);
    }else {
      header('location:'.$psychologist_file.'?task=page_not_found');
      exit;
    }
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $gender = !empty($_GET['gender']) ? $_GET['gender'] : '';
  $status = !empty($_GET['status']) ? $_GET['status'] : '';
  $result = listPatient($kwd, $_SESSION['is_psycho_login_id'], $gender, $status);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listPatient', $result);
  $smarty_appform->display('psychologist/patient.tpl');
  exit;
}



$smarty_appform->display('psychologist/index.tpl');
exit;

?>
