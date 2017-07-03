<?php
//start session
session_start();
//require configuration file
require_once(dirname(__FILE__).'/setting/setup.php');
require_once(dirname(__FILE__).'/setting/common_setting.php');
require_once(dirname(__FILE__).'/functions/common/common_function.php');
require_once(dirname(__FILE__).'/functions/psychologist/psychologist_function.php');

//Get language By default_lang = 1
$result = $common->find('language', $condition = ['default_lang' => 1], $type = 'one');
//Setting language
if(!empty($_GET['lang'])) $lang = $_GET['lang'];
if(empty($lang) and empty($_SESSION['lang'])) $lang = $result['lang_name'];
if(!empty($lang) and empty($_SESSION['lang'])) $_SESSION['lang'] = $lang;
if(!empty($lang) and $lang != $_SESSION['lang']) $_SESSION['lang'] = $lang;
if(empty($lang) and $_SESSION['lang']) $lang = $_SESSION['lang'];
$smarty_appform->assign('mode', 'psychologist');
$smarty_appform->assign('psychologist_file', $psychologist_file);

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

//Task: Test Patient
if('test_patient' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['test_patient']);

  $error = array();
  if($_POST)
  {
    //get value from form
    if(!empty($_POST['id'])){
      $testid   = $common->clean_string($_POST['test']);
    }else {
      $testid   = $common->clean_string_array($_POST['test']);
    }
    $pat_tid  = $common->clean_string($_POST['pat_id']);
    $id       = $common->clean_string($_POST['id']);

    //add value to session to use in template
    $_SESSION['test_psy'] = $_POST;
    //form validation
    if(empty($testid))  $error['testid']  = 1;
    if(empty($pat_tid))  $error['psy_id']  = 1;

    //Add test group
    if(0 === count($error) && empty($id))
    {
      foreach ($testid as $key => $va) {
        $common->save('test_patient', $field = ['patient_id' => $pat_tid, 'test_id' => $va]);
      }
      //unset session
      unset($_SESSION['test_patient']);
      //Redirect
      header('location: '.$psychologist_file.'?task=test_patient');
      exit;
    }
    //update test group
    if(0 === count($error) && !empty($id))
    {
      $common->update('test_patient', $field = ['patient_id' => $pat_tid, 'test_id' => $testid], $condition = ['id' => $id]);
      //unset session
      unset($_SESSION['test_patient']);
      //Redirect
      header('location: '.$psychologist_file.'?task=test_patient');
      exit;
    }
  }
  //Delete: test psychologist
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('test_patient', $field = ['id' => $_GET['id']]);
    header('location: '.$psychologist_file.'?task=test_patient');
    exit;
  }
  //get edit apitransaction
  if('edit' === $action && !empty($_GET['id']))
  {
    $smarty_appform->assign('getTestPat', $common->find('test_patient', $condition = ['id' => $_GET['id']], $type = 'one'));
  }

  $tid    = !empty($_GET['tid']) ? $_GET['tid'] : '';
  $pat_id = !empty($_GET['pat_id']) ? $_GET['pat_id'] : '';
  $status = !empty($_GET['status']) ? $_GET['status'] : '';

  $results = getListTestPatient($pat_id, $_SESSION['is_psycho_login_id'], $tid, $status);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('testPatient', $results);
  $smarty_appform->assign('test', getListTestPsychologistCompleted($_SESSION['is_psycho_login_id']));
  $smarty_appform->assign('patient', $common->find('patient', $condition = ['psychologist_id' => $_SESSION['is_psycho_login_id']], $type = 'all'));
  $smarty_appform->display('psychologist/test_patient.tpl');
  exit;
}

if('test_question' === $task)
{
  // if(!empty($_SESSION['testid'])){
  //   header('Location:'.$index_file);
  //   exit;
  // }
  //Get Test Group By test_id
  $resultTestGroup = listTestGroupByTestId($_GET['tid']);

  if(empty($_SESSION['tgroupid'])) $_SESSION['tgroupid'] = array();
  $error = array();
  if($_POST)
  {
    $ques_answer  = $_POST['answer'];
    $t_groupid    = $_POST['test_group_id'];
    $content      = $_POST['content'];
    $is_email     = $_POST['is_email'];
    $answer_id    = $_POST['answer_id'];
    $is_required  = $_POST['is_required'];
    $testque_id   = $_POST['tq_id'];

    if(!empty($is_required))
    {
      foreach ($is_required as $key => $value) {
        if($value > 0) $error[] = 1;
      }
    }

    //Clear session anser and content
    unset($_SESSION['answer_'.$t_groupid]);
    unset($_SESSION['testque_id_'.$t_groupid]);
    unset($_SESSION['content_'.$t_groupid]);
    unset($_SESSION['contentBack'.$t_groupid]);
    unset($_SESSION['sessionAnswerIdError']);
    unset($_SESSION['contentError']);

    if(count($error) == 0){
      //Add test group id to session
      $_SESSION['tgroupid'][] = $t_groupid;

      //Condition has test group
      if(!empty($answer_id) && COUNT($resultTestGroup) > 0)
      {
        foreach ($answer_id as $key => $value) {
          //Add Anser Id to session
          $_SESSION['answer_'.$t_groupid][] = $value;
        }
      }
      //Condition has test group
      if(!empty($testque_id) && COUNT($resultTestGroup) > 0){
        foreach ($testque_id as $key => $value) {
          //Add test_question_id Id to session
          $_SESSION['testque_id_'.$t_groupid][] = $value;
        }
      }
      //Condition has test group
      if(!empty($content) && COUNT($resultTestGroup) > 0)
      {
        foreach ($content as $key => $value) {
          //Add Content to session
          $_SESSION['content_'.$t_groupid][] = array('id' => $answer_id[$key], 'is_email' => $is_email[$key] , 'content' => $value);
          $_SESSION['contentBack'.$t_groupid][] = array('tqid' => $testque_id[$key], 'content' => $value);
        }
      }
      //Condition test group for assign value to Result
      if(COUNT($_SESSION['tgroupid']) === COUNT($resultTestGroup) && COUNT($resultTestGroup) > 0)
      {
        foreach ($resultTestGroup as $key => $v) {

          if(!empty($_SESSION['answer_'.$v['id']])){
            //fetch session answer
            foreach ($_SESSION['answer_'.$v['id']] as $key => $value) {
              //Add answer id to new session answer_id
              $_SESSION['answer_id'][] = $value;
              //Clear session answer
              unset($_SESSION['answer_'.$v['id']]);
            }
          }

          if(!empty($_SESSION['testque_id_'.$v['id']])){
            foreach ($_SESSION['testque_id_'.$v['id']] as $key => $value) {
              //Add test_question_id id to new session testque_id
              $_SESSION['testque_id'][] = $value;
              //Clear session answer
              unset($_SESSION['testque_id_'.$v['id']]);
            }
          }

          if(COUNT($_SESSION['content_'.$v['id']]) > 0 && !empty($_SESSION['content_'.$v['id']]))
          {
            //fetch session content
            foreach ($_SESSION['content_'.$v['id']] as $key => $value) {
              //Add content to new session content
              $_SESSION['content'][] = array('id' => $value['id'], 'is_email' => $value['is_email'] , 'content' => $value['content']);
              //Clear session content
              unset($_SESSION['content_'.$v['id']]);
              unset($_SESSION['contentBack'.$v['id']]);
            }
          }

        }
        //Clear sesson
        unset($_SESSION['tgroupid']);
        header('Location:'.$index_file.'?task=result&id='.$_GET['id']);
        exit;
      }
      //Condition test no group
      if(!empty($answer_id) && COUNT($resultTestGroup) == 0)
      {
        foreach ($answer_id as $key => $value) {
          //Add Content to session
          $_SESSION['content'][] = array('id' => $value, 'is_email' => $is_email[$key] , 'content' => $content[$key]);
          $_SESSION['testque_id'] = $testque_id;
        }
        //Clear sesson
        unset($_SESSION['tgroupid']);
        header('Location:'.$index_file.'?task=result&id='.$_GET['id']);
        exit;
      }

    }else {
      //Condition has test group
      if(!empty($answer_id))
      {
        foreach ($answer_id as $key => $value) {
          //Add Anser Id to session
          $_SESSION['sessionAnswerIdError'][] = $value;
        }
      }

      if(!empty($testque_id)){
        foreach ($testque_id as $key => $value) {
          $_SESSION['contentError'][] = array('tqid' => $value, 'content' => $content[$key]);
        }
      }

    }

  }//End POST

  if(COUNT($resultTestGroup) > 0)
  {
    //List Test Question By Group
    foreach ($resultTestGroup as $v) {
      if(!in_array($v['id'], $_SESSION['tgroupid']) && $v['count_tgroup_que'] > 0){
        $test_group_id = $v['id'];
        $result = listTestGroupQuestion($v['id'], $_GET['tid'], $lang);
        $getTestByID = getTestGroupById($v['id'], $lang);
        break;
      }
    }

  }else {
    if(!empty($_GET['tid']))
    {
      //List All Test Question No Group
      $result = ListTestQuestion($_GET['tid'], $lang);
      $getTestByID = $common->find('test', $condition = ['id' => $_GET['tid'], 'lang' => $lang], $type = 'one');
    }
  }

  if(empty($result) && COUNT($result) === 0 && empty($getTestByID)){
    header('Location:'.$psychologist_file.'?task=page_error');
    exit;
  }

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('totalAnswer', $total_data);
  $smarty_appform->assign('resultQueIdJumpTo', $resultQueIdJumpTo);
  $smarty_appform->assign('contentError', $_SESSION['contentError']);
  $smarty_appform->assign('sessionAnswerIdError', $_SESSION['sessionAnswerIdError']);
  $smarty_appform->assign('resultViewOrderJumpTo', $resultViewOrderJumpTo);
  $smarty_appform->assign('getTestById', $getTestByID);
  $smarty_appform->assign('result', $result);
  $smarty_appform->assign('test_group_id', $test_group_id);
  $smarty_appform->assign('sessionAnswerId', $_SESSION['answer_'.$test_group_id]);
  $smarty_appform->assign('sessionContent', $_SESSION['content_'.$test_group_id]);
  $smarty_appform->assign('sessionContentBack', $_SESSION['contentBack'.$test_group_id]);
  $smarty_appform->assign('testQueGroup', $resultTestGroup);
  $smarty_appform->assign('countTestGroupSession', COUNT($_SESSION['tgroupid']) + 1);
  $smarty_appform->display('psychologist/test_question.tpl');
  exit;
}


$tid    = !empty($_GET['tid']) ? $_GET['tid'] : '';
$cid    = !empty($_GET['cid']) ? $_GET['cid'] : '';
$status = !empty($_GET['status']) ? $_GET['status'] : '';

$results = getListTestPsychologist($_SESSION['is_psycho_login_id'], $tid, $cid, $status);

(0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
SmartyPaginate::assign($smarty_appform);

$smarty_appform->assign('listTestPsychologist', $results);
$smarty_appform->assign('test', $common->find('test', $condition = ['lang' => $lang], $type = 'all'));
$smarty_appform->assign('category', $common->find('category', $condition = ['lang' => $lang], $type = 'all'));
$smarty_appform->display('psychologist/index.tpl');
exit;

?>
