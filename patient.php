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
        //Save Data to table response
        $responseid = $common->save('response', $field = ['unique_id' => time(), 'test_id' => $_GET['id'], 'test_patient_id' => $_GET['id']]);

        foreach ($resultTestGroup as $key => $v)
        {
          if(COUNT($_SESSION['content_'.$v['id']]) > 0 && !empty($_SESSION['content_'.$v['id']]))
          {
            //fetch session content
            foreach ($_SESSION['content_'.$v['id']] as $key => $value)
            {
              if($value['content'] == 'NULL') {
                $r_content = NULL;
              } else {
                $r_content = $value['content'];
              }
              if($value['id'] == 'NULL') {
                $r_answer_id = NULL;
              } else {
                $r_answer_id = $value['id'];
              }
              if($value['is_email'] == 1)
              {
                $username = explode("@", $value['content']);
                $subscribers[] = array('email' => $value['content'], 'name' => $username[0]);
              }

              if(!empty($responseid))
              {
                $common->save('response_answer', $field = ['response_id' => $responseid,
                                                           'test_question_id' => $value['test_question_id'],
                                                           'answer_id'  => $r_answer_id,
                                                           'content'    => $r_content,
                                                           'is_email'   => $value['is_email']]);
              }//End check $responseid

              //Clear session
              unset($_SESSION['answer_'.$v['id']]);
              unset($_SESSION['testque_id_'.$v['id']]);
              unset($_SESSION['content_'.$v['id']]);
              unset($_SESSION['contentBack'.$v['id']]);
            }//End foreach $_SESSION['content_'.$v['id']]

          }

        }//End foreach $resultTestGroup
        $common->update('test_patient', $field = ['status' => 2, 'pat_working_by' => NULL], $condition = ['id' => $_GET['id'], 'patient_id' => $_SESSION['is_patient_login_id'], 'test_id' => $_GET['tid']]);
        // Get List Mailer Lite By Test Id
        $resultMailer = getMailerLiteByTestId($_GET['id']);
        //fetch mailer lite
        foreach ($resultMailer as $key => $value) {
          $groupsApi = (new \MailerLiteApi\MailerLite($value['api_key']))->groups();

          $mailerGroup = $common->find('mailerlite_group', $condition = ['transaction_id' => $value['transaction_id']], $type = 'all');
          //fetch Mailer Lite Group
          foreach ($mailerGroup as $k => $v) {
            //Add mail to mailer lite
            $groupsApi->importSubscribers($v['group_id'], $subscribers);
          }
        }

        //Clear sesson
        unset($_SESSION['tgroupid']);
        header('Location:'.$patient_file.'?task=result_patient&tid='.$_GET['tid']);
        exit;
      }
      //Condition test no group
      if(!empty($answer_id) && COUNT($resultTestGroup) == 0)
      {
        //Save Data to table response
        $responseid = $common->save('response', $field = ['unique_id' => time(), 'test_id' => $_GET['id'], 'test_patient_id' => $_GET['id']]);

        foreach ($answer_id as $key => $value)
        {
          if($content[$key] == 'NULL') {
            $r_content = NULL;
          } else {
            $r_content = $content[$key];
          }
          if($value == 'NULL') {
            $r_answer_id = NULL;
          } else {
            $r_answer_id = $value;
          }
          if($is_email[$key] == 1)
          {
            $username = explode("@", $content[$key]);
            $subscribers[] = array('email' => $content[$key], 'name' => $username[0]);
          }

          if(!empty($responseid))
          {
            $common->save('response_answer', $field = ['response_id' => $responseid,
                                                       'test_question_id' => $testque_id[$key],
                                                       'answer_id'  => $r_answer_id,
                                                       'content'    => $r_content,
                                                       'is_email'   => $is_email[$key]]);
          }//End check $responseid
        }

        $common->update('test_patient', $field = ['status' => 2, 'pat_working_by' => NULL], $condition = ['id' => $_GET['id'], 'patient_id' => $_SESSION['is_patient_login_id'], 'test_id' => $_GET['tid']]);
        // Get List Mailer Lite By Test Id
        $resultMailer = getMailerLiteByTestId($_GET['id']);
        //fetch mailer lite
        foreach ($resultMailer as $key => $value) {
          $groupsApi = (new \MailerLiteApi\MailerLite($value['api_key']))->groups();

          $mailerGroup = $common->find('mailerlite_group', $condition = ['transaction_id' => $value['transaction_id']], $type = 'all');
          //fetch Mailer Lite Group
          foreach ($mailerGroup as $k => $v) {
            //Add mail to mailer lite
            $groupsApi->importSubscribers($v['group_id'], $subscribers);
          }
        }

        //Clear sesson
        unset($_SESSION['tgroupid']);
        header('Location:'.$patient_file.'?task=result_patient&tid='.$_GET['tid']);
        exit;
      }//End Condition test no group

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


  $resultsJumpTo = getListQuestionByViewOrderGroupNonGroupJumpTo($_GET['tid'], $lang);

  $newResultJumpTo = [];

  foreach ($resultsJumpTo as $key => $value)
  {
    if($key > 2)
    {
      if($value['test_question_id'] != 12)
      {
        $newResultJumpTo['12'][] = $value['test_question_id'];echo "<br>";
      } else {
        break;
      }
    }
  }

  // var_dump($newResultJumpTo);

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
  $smarty_appform->assign('ResultJumpTo', json_encode($newResultJumpTo));
  $smarty_appform->assign('sumAnswerCol', $sumAnswerCol);
  $smarty_appform->assign('testTmpQuestion', getTestTmpQuestion($_GET['id'], $_GET['tid']));
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
  $smarty_appform->display('common/test_question_responsive_patient.tpl');
  exit;
}

if('result_patient' === $task)
{

  $smarty_appform->assign('getTestById', $common->find('test', $condition = ['id' => $_GET['tid'], 'lang' => $lang], $type = 'one'));
  $smarty_appform->display('common/test_result_patient.tpl');
  exit;
}

if('test_save_draft' === $task)
{
  if($_POST)
  {
    $test_id        = $common->clean_string($_POST['test_id']);
    $test_pat_id    = $common->clean_string($_POST['test_pat_id']);
    $test_que_data  = json_decode($_POST['test_que_data']);

    //Get test_tmp By id
    $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $test_id, 'test_patient_id' => $test_pat_id], $type = 'one');

    if(empty($resultTestTmp)){
      $test_tmp_id = $common->save('test_tmp', $field = ['test_id' => $test_id, 'test_patient_id' => $test_pat_id]);

      if(!empty($test_que_data)){
        foreach ($test_que_data as $v)
        {
          if($v->answer_id === 'NULL')
          {
            $answer_id = NULL;
          } else {
            $answer_id = $v->answer_id;
          }
          if($v->content === 'NULL')
          {
            $content = NULL;
          } else {
            $content = $v->content;
          }
        	$result = $common->save('test_tmp_question', $field =['test_tmp_id'  => $test_tmp_id,
                                                                'test_question_id'  => $v->test_que_id,
                                                                'answer_id' => $answer_id,
                                                                'content'   => $content]);
        }
        $resultValue = true;
  		}

    } else {
      if(!empty($test_que_data))
      {
        foreach ($test_que_data as $v)
        {
          //Get test_tmp By id
          $rTestTmpQue = $common->find('test_tmp_question', $condition = ['test_tmp_id' => $resultTestTmp['id'], 'test_question_id' => $v->test_que_id], $type = 'one');

          if($v->answer_id === 'NULL')
          {
            $answer_id = NULL;
          } else {
            $answer_id = $v->answer_id;
          }
          if($v->content === 'NULL')
          {
            $content = NULL;
          } else {
            $content = $v->content;
          }

          if(empty($rTestTmpQue))
          {
            $result = $common->save('test_tmp_question', $field =['test_tmp_id'  => $resultTestTmp['id'],
                                                                  'test_question_id'  => $v->test_que_id,
                                                                  'answer_id' => $answer_id,
                                                                  'content'   => $content]);
          }else {
            $common->update('test_tmp_question', $field = ['answer_id' => $answer_id, 'content' => $content], $condition = ['id' => $rTestTmpQue['id']]);
          }

        }//End foreach
        $resultValue = true;
      }
    }


  }
  header('Content-type: application/json');
  echo json_encode($resultValue);
  exit;
}

$tmpstus= !empty($_GET['stus']) ? $_GET['stus'] : '';
$f_date = !empty($_GET['f_date']) ? $_GET['f_date'] : '';
$t_date = !empty($_GET['t_date']) ? $_GET['t_date'] : '';

$results = getListTestPatient($_SESSION['is_patient_login_id'], '', $tid, $status = 1, $tmpstus, $f_date, $t_date);

(0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
SmartyPaginate::assign($smarty_appform);

$smarty_appform->assign('error', $error);
$smarty_appform->assign('testPatient', $results);
$smarty_appform->display('patient/index.tpl');
exit;
?>
