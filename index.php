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
$smarty_appform->assign('mode', 'index');
$smarty_appform->assign('index_file', $index_file);

//Smarty assign value
$smarty_appform->assign('lang_name', $result['lang_name']);

//list menu language
$smarty_appform->assign('getLanguage', $common->find('language', $condition = null, $type = 'all'));
//Change language on template
$smarty_appform->assign('multiLang', getMultilang($lang));


if('user_register' === $task)
{
  $error = array();
  if($_POST){
    //get value from form
    $username = $common->clean_string($_POST['username']);
    $password = $common->clean_string($_POST['password']);
    $re_password = $common->clean_string($_POST['re_password']);
    $email    = $common->clean_string($_POST['email']);
    $job      = $common->clean_string($_POST['job']);
    $address  = $common->clean_string($_POST['address']);
    $secretkey = time();
    //add value to session to use in template
    $_SESSION['user_register'] = $_POST;
    //form validation
    if(empty($username))  $error['username']  = 1;
    if(empty($password))  $error['password']  = 1;
    if(empty($re_password))  $error['re_password']  = 1;
    if(empty($email))     $error['email']  = 1;
    if(empty($job))       $error['job']  = 1;
    if(empty($address))   $error['address']  = 1;
    if(!empty($password) && !empty($re_password) && $password !== $re_password){
      $error['not_match_password'] = 1;
    }
    if(!empty($password) && !$common->checkPassword($password)){
      $error['less_password'] = 1;
    }
    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
		   $error['invalid_email'] = 1;
		}
    if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
      $existed = check_psychologist_email($email);
      if($existed > 0){
        $error['exist_email'] = 1;
      }
    }
    //save data
    if(0 === count($error)){

      $psy_id = $common->save('psychologist', $field = ['username' => $username,
                                                        'password' => $password,
                                                        'email'   => $email,
                                                        'job'     => $job,
                                                        'address' => $address,
                                                        'secretkey' => $secretkey]);
    //Send email
    $body = 'Dear '.$username.'

Welcome to Psychology Management System (PMS). Your username and password is:

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Username：'.$username.'
Password：'.$password.'

To verify your identity, please click the link below for confirm your email:

'.$site_url.$index_file.'?task=user_register&action=verify&secretkey='.$secretkey.'&psy_id='.$psy_id.'

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Thank you for registering with us.

Best Regard,
    '.$mail_signature;
        $message = Swift_Message::newInstance()
                ->setSubject('Your account need verify at Psychology Management System (PMS)')
                ->setFrom(array('noreply@e-khmer.com' => 'Psychology Management System (PMS)'))
                ->setTo(array($email => $username))
                ->setBody($body);

      $result 	= $mailer->send($message);

      setcookie('completed', 'user_register', time() + 50);
      //unset session
      unset($_SESSION['user_register']);
      //Redirect
      header('location: '.$index_file.'?task=completed');
      exit;
    }
  }

  if('verify' === $action)
  {
    $existSecretkey = checkSecretkeyPsychologist($_GET['psy_id'] ,$_GET['secretkey']);

    if($_GET['secretkey'] && $_GET['psy_id'] && $existSecretkey > 0){
      $common->update('psychologist', $field = ['secretkey' => NULL, 'status' => 2], $condition = ['id' => $_GET['psy_id']]);
      header('location: '.$index_file);
      exit;
    }else {
      setcookie('page_error', 1, time() + 50);
      header('location: '.$index_file.'?task=page_not_found');
      exit;
    }
  }

  $smarty_appform->assign('error', $error);
  $smarty_appform->display('index/register.tpl');
  exit;
}
//Task: froget
if('forget' === $task){
  $error = array();
  if($_POST)
  {
    //get value from form
    $email     = $common->clean_string($_POST['email']);
    $user_role = $common->clean_string($_POST['user_role']);
    $secretkey = time();
    //add value to session to use in template
    $_SESSION['forget'] = $_POST;
    //form validation
    if(empty($email))     $error['email']  = 1;
    if(empty($user_role)) $error['user_role']  = 1;

    if(!empty($user_role) && !empty($email) && $user_role == 1 && check_patient_email($email) == 0) $error['wrong_email']  = 1;
    if(!empty($user_role) && !empty($email) && $user_role == 2 && check_psychologist_email($email) == 0) $error['wrong_email']  = 1;

    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
		   $error['invalid_email'] = 1;
		}

    if(0 === count($error)){

      $result = getDataByUserRole($user_role, $email);

      if($user_role == 1){
        $common->update('patient', $field = ['secretkey' => $secretkey], $condition = ['id' => $result['id']]);
      }else {
        $common->update('psychologist', $field = ['secretkey' => $secretkey], $condition = ['id' => $result['id']]);
      }

      //Send email
      $body = 'Dear '.$result['username'].'

Welcome to Psychology Management System (PMS).

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

You recently requested to reset your PMS password. Please click on the link below:

'.$site_url.$index_file.'?task=new_password&secretkey='.$secretkey.'&user_role='.$user_role.'&id='.$result['id'].'

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Best Regard,
      '.$mail_signature;
          $message = Swift_Message::newInstance()
                  ->setSubject('Reset your PMS Password')
                  ->setFrom(array('noreply@e-khmer.com' => 'Psychology Management System (PMS)'))
                  ->setTo(array($email => $username))
                  ->setBody($body);

        $result 	= $mailer->send($message);

      setcookie('completed', 'forgot_password', time() + 50);
      //unset session
      unset($_SESSION['forget']);
      //Redirect
      header('location: '.$index_file.'?task=completed');
      exit;
    }
  }

  $smarty_appform->assign('error', $error);
  $smarty_appform->display('index/forget.tpl');
  exit;
}

if('new_password' === $task)
{
  $error = array();
  if($_POST)
  {
    //get value from form
    $id        = $common->clean_string($_POST['id']);
    $user_role = $common->clean_string($_POST['user_role']);
    $secretkey = $common->clean_string($_POST['user_role']);
    $password    = $common->clean_string($_POST['password']);
    $re_password = $common->clean_string($_POST['re_password']);
    //add value to session to use in template
    $_SESSION['new_password'] = $_POST;
    //form validation
    if(empty($id))       $error['id']  = 1;
    if(empty($user_role))   $error['user_role'] = 1;
    if(empty($password))    $error['password']  = 1;
    if(empty($re_password)) $error['re_password']  = 1;
    if(!empty($password) && !empty($re_password) && $password !== $re_password)
    {
      $error['not_match_password'] = 1;
    }
    if(!empty($password) && !$common->checkPassword($password))
    {
      $error['less_password_not_letter'] = 1;
    }

    if(COUNT($error) == 0){
      if($user_role == 1){
        $common->update('patient', $field = ['password' => $password, 'secretkey' => NULL], $condition = ['id' => $id]);
      }else {
        $common->update('psychologist', $field = ['password' => $password, 'secretkey' => NULL], $condition = ['id' => $id]);
      }

      setcookie('completed', 'new_password', time() + 50);
      //unset session
      unset($_SESSION['new_password']);
      //Redirect
      header('location: '.$index_file.'?task=completed');
      exit;
    }

  }

  if($_GET['user_role'] == 1){
    $existSecretkey = checkSecretkeyPatient($_GET['id'] ,$_GET['secretkey']);
  }else {
    $existSecretkey = checkSecretkeyPsychologist($_GET['id'] ,$_GET['secretkey']);
  }

  if($_GET['secretkey'] && $_GET['id'] && $existSecretkey == 0){
    setcookie('page_error', 'new_password', time() + 50);
    header('location: '.$index_file.'?task=page_not_found');
    exit;

  }
  $smarty_appform->assign('error', $error);
  $smarty_appform->display('index/new_password.tpl');
  exit;
}

//Task: completed
if('completed' === $task){
  $smarty_appform->display('index/completed.tpl');
  exit;
}
//Task: page not found
if('page_not_found' === $task){
  $smarty_appform->display('index/page_error_404.tpl');
  exit;
}
//task: login
if('login' === $task)
{
  $error = array();
  if($_POST)
  {
    //get value from form
    $email      = $common->clean_string($_POST['email']);
    $password   = $common->clean_string($_POST['password']);
    $user_role  = $common->clean_string($_POST['user_role']);
    //add value to session to use in template
    $_SESSION['user_login'] = $_POST;
    //form validation
    if(empty($email))     $error['email']  = 1;
    if(empty($password))  $error['password']  = 1;
    if(empty($user_role)) $error['user_role']  = 1;


    if(0 === count($error))
    {
      //$user_role eqaul 1 patient
      if($user_role == 1)
      {
        $patient_login = patient_login($email, $password);

        if(!empty($patient_login))
        {
          //assign value to session
          $_SESSION['is_patient_login_id'] = $patient_login['id'];
          $_SESSION['is_patient_username'] = $patient_login['username'];
          $_SESSION['is_patient_email']    = $patient_login['email'];
          //remove session to clear data
          unset($_SESSION['user_login']);
          //redirect to admin.php
          header('Location:'.$patient_file);
          exit;
        }else {
          //wrong username and password
          $error['login_error'] = 1;
        }

      }else {
        $psy_login = psychologist_login($email, $password);

        if(!empty($psy_login)){
          //Record Activity
          $act_data = ['psychologist_id' => $psy_login['id'], 'content' => 'LOGIN'];
          @$common->save('activity_log', $act_data);

          //assign value to session
          $_SESSION['is_psycho_login_id'] = $psy_login['id'];
          $_SESSION['is_psycho_username'] = $psy_login['username'];
          $_SESSION['is_psycho_email']    = $psy_login['email'];
          //remove session to clear data
          unset($_SESSION['user_login']);
          //redirect to admin.php
          header('Location:'.$psychologist_file);
          exit;
        }else {
          //wrong username and password
          $error['login_error'] = 1;
        }

      }

    }

  }//End post

  //default of login task
  $smarty_appform->assign('error', $error);
  $smarty_appform->display('index/login.tpl');
  exit;
}
// //task: logout by clear session
if('logout' === $task){
  unset($_SESSION['is_patient_login_id']);
  header('Location:'.$index_file.'?task=login');
  exit;
}
//redirect to psychologist.php if has $_SESSION['is_psycho_login_id']
if(empty($_SESSION['is_psycho_login_id']) && !empty($_SESSION['is_patient_login_id'])){
  header('Location:'.$patient_file);
  exit;
}
//redirect to psychologist.php if has $_SESSION['is_psycho_login_id']
if(!empty($_SESSION['is_psycho_login_id']) && empty($_SESSION['is_patient_login_id'])){
  header('Location:'.$psychologist_file);
  exit;
}
//redirect if no session
if(empty($_SESSION['is_psycho_login_id']) && empty($_SESSION['is_patient_login_id'])){
  header('Location:'.$index_file.'?task=login');
  exit;
}else {
  header('Location:'.$index_file.'?task=login');
  exit;
}

//task home
$smarty_appform->display('index/index.tpl');
exit;
?>
