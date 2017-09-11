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
$smarty_appform->assign('mode', 'patient');
$smarty_appform->assign('patient_file', $patient_file);

//Smarty assign value
$smarty_appform->assign('lang_name', $result['lang_name']);

//list menu language
$smarty_appform->assign('getLanguage', $common->find('language', $condition = null, $type = 'all'));
//Change language on template
$smarty_appform->assign('multiLang', getMultilang($lang));

//task: logout by clear session
if('logout' === $task)
{
  unset($_SESSION['is_patient_login_id']);
  header('Location:'.$index_file.'?task=login');
  exit;
}
//redirect if no session
if(empty($_SESSION['is_patient_login_id']))
{
  header('Location:'.$index_file.'?task=login');
  exit;
}
//Task: page not found
if('page_not_found' === $task)
{
  $smarty_appform->display('common/page_error_404.tpl');
  exit;
}
//Task: Test Question
if('test_question' === $task)
{
  $resultTestPatient = $common->find('test_patient', $condition = ['id' => $_GET['id'], 'test_id' => $_GET['tid'], 'patient_id' => $_SESSION['is_patient_login_id']], $type = 'one');

  if(empty($resultTestPatient)){
    header('Location:'.$patient_file.'?task=page_not_found');
    exit;
  }

  if(!empty($_SESSION['testid'])){
    header('Location:'.$patient_file);
    exit;
  }
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
        header('Location:'.$psychologist_file.'?task=result&tid='.$_GET['tid'].'&id='.$_GET['id']);
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
        header('Location:'.$patient_file.'?task=result&tid='.$_GET['tid'].'&id='.$_GET['id']);
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
    header('Location:'.$patient_file.'?task=page_not_found');
    exit;
  }

  $sumAnswerCol = 0;
  foreach ($result as $key => $value) {
    if($value['flag'] == 1){
      if($value['answer'] > $sumAnswerCol) $sumAnswerCol = COUNT($value['answer']);
    }
  }

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('sumAnswerCol', $sumAnswerCol);
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
  $smarty_appform->display('common/test_question_responsive.tpl');
  exit;
}

$results = getListTestPatient($_SESSION['is_patient_login_id'], '', $tid, $status = 1);

(0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
SmartyPaginate::assign($smarty_appform);

$smarty_appform->assign('error', $error);
$smarty_appform->assign('testPatient', $results);
$smarty_appform->display('patient/index.tpl');
exit;
?>
