<?php
//start session
session_start();
//require configuration file
require_once(dirname(__FILE__).'/setting/setup.php');
require_once(dirname(__FILE__).'/setting/common_setting.php');
require_once(dirname(__FILE__).'/functions/common/common_function.php');
require_once(dirname(__FILE__).'/functions/index/index_function.php');
require_once(dirname(__FILE__).'/functions/patient/patient_function.php');

//Get language By default_lang = 1
$resultLang = $common->find('language', $condition = ['default_lang' => 1], $type = 'one');
//Setting language
if(!empty($_GET['lang'])) $lang = $_GET['lang'];
if(empty($lang) and empty($_SESSION['lang'])) $lang = $resultLang['lang_name'];
if(!empty($lang) and empty($_SESSION['lang'])) $_SESSION['lang'] = $lang;
if(!empty($lang) and $lang != $_SESSION['lang']) $_SESSION['lang'] = $lang;
if(empty($lang) and $_SESSION['lang']) $lang = $_SESSION['lang'];

$smarty_appform->assign('mode', 'patient');
$smarty_appform->assign('patient_file', $patient_file);

//Smarty assign value
$smarty_appform->assign('lang_name', $resultLang['lang_name']);

//list menu language
$smarty_appform->assign('getLanguage', $common->find('language', $condition = null, $type = 'all'));
//Change language on template
$smarty_appform->assign('multiLang', getMultilang($lang));

$reListTestPatUncompleted = getListTestPatient($_SESSION['is_patient_login_id'], '', $tid = '', $status = 1, $tmpstus = 1, $f_date, $t_date, $lang);
$smarty_appform->assign('unCompleted', COUNT($reListTestPatUncompleted));
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
  $smarty_appfoirm->display('common/page_error_404.tpl');
  exit;
}

//Task: test_completed
if('test_completed' === $task)
{
  //Check & Clean String
  $tid = $common->clean_string($_GET['tid']);
  $smarty_appform->assign('getTestById', $common->find('test', $condition = ['id' => $tid, 'lang' => $lang], $type = 'one'));
  $smarty_appform->display('common/test_completed.tpl');
  exit;
}

//Task:Test Response
if ('test_response' === $task)
{
  $cate = !empty($_GET['category']) ? $_GET['category'] : '';
  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $test_response_result = get_test_response($kwd, $cate, $_SESSION['is_patient_login_id']);
  // var_dump($test_response_result);exit;
  $smarty_appform->assign('response_result', $test_response_result);
  $smarty_appform->display('patient/test_response.tpl');
  exit;
}

//Task: Test Question
if('test_question' === $task)
{
  //Check & Clean String
  $tpid = $common->clean_string($_GET['id']);
  $tid  = $common->clean_string($_GET['tid']);

  $resultTestPatient = $common->find('test_patient', $condition = ['id' => $tpid, 'test_id' => $tid, 'patient_id' => $_SESSION['is_patient_login_id']], $type = 'one');

  if($resultTestPatient && $resultTestPatient['status'] == 2)
  {
    header('Location:'.$patient_file.'?task=test_completed&tid='.$resultTestPatient['test_id']);
    exit;
  }
  if(empty($resultTestPatient))
  {
    header('Location:'.$patient_file.'?task=page_not_found');
    exit;
  }

  //Get Test Group By test_id
  $resultTestGroup = listTestGroupByTestId($tid);

  if(empty($_SESSION['tgroupid'])) $_SESSION['tgroupid'] = array();
  $error = array();
  if($_POST)
  {
    //Check & Clean String
    $tpid         = $common->clean_string($_GET['id']);
    $tid          = $common->clean_string($_GET['tid']);
    $t_groupid    = $common->clean_string($_POST['test_group_id']);
    $content      = $common->clean_string_array($_POST['content']);
    $is_email     = $common->clean_string_array($_POST['is_email']);
    $answer_id    = $common->clean_string_array($_POST['answer_id']);
    $is_required  = $common->clean_string_array($_POST['is_required']);
    $testque_id   = $common->clean_string_array($_POST['tq_id']);

    if(!empty($is_required))
    {
      foreach ($is_required as $key => $value) {
        if($value > 0) $error[] = 1;
      }
    }

    if(count($error) == 0)
    {
      //Condition has test group, Save test_question_id is NULL, When skip by jump_to
      if(empty($answer_id) && COUNT($resultTestGroup) > 0)
      {
        //Get test_tmp By id
        $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $tid, 'test_patient_id' => $tpid], $type = 'one');
        $resultTestTmpQue = $common->find('test_tmp_question', $condition = ['test_tmp_id' => $resultTestTmp['id'], 'test_group_id' => $t_groupid], $type = 'all');
        if(!empty($resultTestTmpQue))
        {
          foreach ($resultTestTmpQue as $k => $va) {
            //Delete: test_tmp_question
            $common->delete('test_tmp_question', $field = ['id' => $va['id']]);
          }
        }
        if(!empty($resultTestTmp))
        {
          $common->save('test_tmp_question', $field =['test_tmp_id' => $resultTestTmp['id'], 'test_group_id' => $t_groupid, 'status' => 2]);
        }
      }

      //Condition has test group
      if(!empty($answer_id) && COUNT($resultTestGroup) > 0)
      {
        //Get test_tmp By id
        $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $tid, 'test_patient_id' => $tpid], $type = 'one');

        if(!empty($resultTestTmp))
        {
          $resultTestTmpQue = $common->find('test_tmp_question', $condition = ['test_tmp_id' => $resultTestTmp['id'], 'test_group_id' => $t_groupid], $type = 'all');
          if(!empty($resultTestTmpQue))
          {
            foreach ($resultTestTmpQue as $k => $va) {
              //Delete: test_tmp_question
              $common->delete('test_tmp_question', $field = ['id' => $va['id']]);
            }
          }
          $test_tmp_id = $resultTestTmp['id'];
        } else {
          $test_tmp_id = $common->save('test_tmp', $field = ['test_id' => $tid, 'test_patient_id' => $tpid]);
        }

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
          if(!empty($test_tmp_id))
          {
            $result = $common->save('test_tmp_question', $field =['test_tmp_id' => $test_tmp_id, 'test_question_id' => $testque_id[$key], 'answer_id' => $r_answer_id, 'content' => $r_content, 'test_group_id' => $t_groupid, 'status' => 2]);
          }//End check $responseid
        }
      }//End: Condition has test group

      //Get Test Group By Tmp Question
      $resultTestGroupTmpQue = getListTestGroupByTmpQuestion($tid, $tpid, $status = 1, $fetch_type = 'all', $slimit = '');
      //Condition test group for assign value to Result
      if(COUNT($resultTestGroupTmpQue) === 0 && COUNT($resultTestGroup) > 0)
      {
        //Get test_tmp By id
        $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $tid, 'test_patient_id' => $tpid], $type = 'one');
        $resultTestTmpQue = $common->find('test_tmp_question', $condition = ['test_tmp_id' => $resultTestTmp['id']], $type = 'all');
        //Save Data to table response
        $responseid = $common->save('response', $field = ['unique_id' => time(), 'test_id' => $tid, 'test_patient_id' => $tpid]);
        $subscribers = array();
        foreach ($resultTestTmpQue as $key => $va)
        {
          $resultTestQues = getTestQuestionByTestQuesID($tid, $va['test_question_id'], $lang);
          if($resultTestQues['is_email'] == 1)
          {
            $is_email = 1;
            $subscribers[] = array('email' => $va['content']);
          } else {
            $is_email = 0;
          }
          if(!empty($responseid) && !empty($va['test_question_id']))
          {
            $common->save('response_answer', $field = ['response_id' => $responseid,
                                                       'test_question_id' => $va['test_question_id'],
                                                       'answer_id'  => $va['answer_id'],
                                                       'content'    => $va['content'],
                                                       'is_email'   => $is_email]);
          }//End check $responseid
        }
        $common->update('test_patient', $field = ['status' => 2, 'pat_working_by' => NULL, 'completed_date' => date("Y-m-d")], $condition = ['id' => $tpid, 'patient_id' => $_SESSION['is_patient_login_id'], 'test_id' => $tid]);
        //Delete: test_tmp & test_tmp_question
        $common->delete('test_tmp', $field = ['id' => $resultTestTmp['id']]);
        $common->delete('test_tmp_question', $field = ['test_tmp_id' => $resultTestTmp['id']]);
        if(!empty($subscribers))
        {
          exec($path_exec.'/submitmail_by_test.php '.$responseid.' '.$tid.' > /dev/null &');
        }
        $patient_login_id = $_SESSION['is_patient_login_id'];
        exec($path_exec.'/send_mail.php '.$patient_login_id.' > /dev/null &');
        header('Location:'.$patient_file.'?task=result_patient&tid='.$tid);
        exit;
      }
      //Condition test no group
      if(!empty($answer_id) && COUNT($resultTestGroup) == 0)
      {
        //Save Data to table response
        $responseid = $common->save('response', $field = ['unique_id' => time(), 'test_id' => $tid, 'test_patient_id' => $tpid]);

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
            $subscribers[] = array('email' => $content[$key]);
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

        $common->update('test_patient', $field = ['status' => 2, 'pat_working_by' => NULL, 'completed_date' => date("Y-m-d")], $condition = ['id' => $tpid, 'patient_id' => $_SESSION['is_patient_login_id'], 'test_id' => $tid]);
        //Get test_tmp By id
        $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $tid, 'test_patient_id' => $tpid], $type = 'one');
        //Delete: test_tmp & test_tmp_question
        $common->delete('test_tmp', $field = ['id' => $resultTestTmp['id']]);
        $common->delete('test_tmp_question', $field = ['test_tmp_id' => $resultTestTmp['id']]);

        if(!empty($subscribers))
        {
          exec($path_exec.'/submitmail_by_test.php '.$responseid.' '.$tid.' > /dev/null &');
        }
        $patient_login_id = $_SESSION['is_patient_login_id'];
        exec($path_exec.'/send_mail.php '.$patient_login_id.' > /dev/null &');
        //Clear sesson
        unset($_SESSION['tgroupid']);
        header('Location:'.$patient_file.'?task=result_patient&tid='.$tid);
        exit;
      }//End Condition test no group

    } else {
      //Condition has submit error
      $newResultSubmitError = array();
      foreach ($answer_id as $key => $value)
      {
        //Get Test Question By Id
        $resultTestQues = getTestQuestionByTestQuesID($tid, $testque_id[$key], $lang);
        //Get answer
        $reAnswer = $common->find('answer', $condition = ['id' => $value], $type = 'one');
        //Assign value to array
        $newResultSubmitError[] = array('tqid' => $testque_id[$key], 'answer_id' => $value, 'content' => $content[$key], 'type' => $resultTestQues['type'], 'jump_to' => $reAnswer['jump_to']);
      }
    }
  }//End POST

  //Get Test Group By Tmp Question
  $resultTestGroupTmpQue = getListTestGroupByTmpQuestion($tid, $tpid, $status = 1, $fetch_type = 'all', $slimit = '');
  if(COUNT($resultTestGroup) > 0)
  {
    if(!empty($resultTestGroupTmpQue))
    {
      foreach ($resultTestGroupTmpQue as $k => $va) {
        $test_group_id = $va['id'];
        $result = listTestGroupQuestion($va['id'], $tid, $lang);
        $getTestByID = getTestGroupById($va['id'], $lang);
        break;
      }
    }

  } else {
    if(!empty($tid))
    {
      //List All Test Question No Group
      $result = ListTestQuestion($tid, $lang);
      $getTestByID = $common->find('test', $condition = ['id' => $tid, 'lang' => $lang], $type = 'one');
    }
  }

  //For Jumping To
  $resultsJumpTo = getListQuestionByViewOrderGroupNonGroupJumpTo($tid, $lang);

  $newResultJumpTo = array();
  if(!empty($resultsJumpTo['jump_to']))
  {
    foreach ($resultsJumpTo['jump_to'] as $k => $va)
    {
      foreach ($resultsJumpTo['question'] as $key => $value)
      {
        if($key > $va['key'])
        {
          if($value['test_question_id'] !== $va['jump_to'])
          {
            $newResultJumpTo[$va['jump_to'].'_'.$va['test_question_id']][] = $value['test_question_id'];
          }else {
            break;
          }
        }
      } //End foreach $resultsJumpTo
    }//End foreach $resultsJumpTo['jump_to']
  }

  if(empty($result) && COUNT($result) === 0 && empty($getTestByID))
  {
    header('Location:'.$patient_file.'?task=page_not_found');
    exit;
  }

  $sumStep = COUNT($resultTestGroup) - COUNT(getListTestGroupByTmpQuestion($tid, $tpid, $status = 2, $fetch_type = 'all', $slimit = ''));

  if(!empty($error))
  {
    $smarty_appform->assign('testTmpQuestion', $newResultSubmitError);
  } else {
    $smarty_appform->assign('testTmpQuestion', getTestTmpQuestion($tpid, $tid));
  }

  $smarty_appform->assign('testQuestionHideShowCondition', getTestQuestionHideShowCondition($tid, $tpid, ''));
// var_dump($result);
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('ResultJumpTo', json_encode($newResultJumpTo));
  $smarty_appform->assign('totalAnswer', $total_data);
  $smarty_appform->assign('resultQueIdJumpTo', $resultQueIdJumpTo);
  $smarty_appform->assign('getTestById', $getTestByID);
  $smarty_appform->assign('result', $result);
  $smarty_appform->assign('test_group_id', $test_group_id);
  $smarty_appform->assign('testGroupIDTmpQue', getListTestGroupByTmpQuestion($tid, $tpid, $status = 2, $fetch_type = 'one', $slimit = 1)); //For get "Test_Group_Id" Back Step
  //Check Step
  $smarty_appform->assign('resultTestGroupTmpQue', COUNT(getListTestGroupByTmpQuestion($tid, $tpid, $status = 2, $fetch_type = 'all', $slimit = ''))); //For Check Show Button Next Or Finish
  $smarty_appform->assign('testQueGroup', COUNT($resultTestGroup));
  $smarty_appform->assign('resultStep', $sumStep);
  $smarty_appform->display('common/test_question_responsive.tpl');
  exit;
}

//Task: Back Step Test Question
if('back_step' === $task && !empty($_GET['tid']))
{
  $tpid = $common->clean_string($_GET['id']);
  $tgid = $common->clean_string($_GET['tgid']);
  $tid  = $common->clean_string($_GET['tid']);
  //Get Test Patient
  $resultTestPatient = $common->find('test_patient', $condition = ['id' => $tpid, 'test_id' => $tid, 'patient_id' => $_SESSION['is_patient_login_id']], $type = 'one');

  if(empty($resultTestPatient)){
    header('Location:'.$patient_file.'?task=page_not_found');
    exit;
  }
  if(!empty($resultTestPatient) && $resultTestPatient['status'] == 2)
  {
    header('Location:'.$patient_file.'?task=test_completed&tid='.$resultTestPatient['test_id']);
    exit;
  }
  //Get test_tmp By id
  $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $tid, 'test_patient_id' => $tpid], $type = 'one');
  $resultTestTmpQue = $common->find('test_tmp_question', $condition = ['test_tmp_id' => $resultTestTmp['id'], 'test_group_id' => $tgid], $type = 'all');

  $common->update('test_tmp_question', $field = ['status' => 1], $condition = ['test_tmp_id' => $resultTestTmp['id'], 'test_group_id' => $tgid]);
  header('Location:'.$patient_file.'?task=test_question&tid='.$tid.'&id='.$tpid);
  exit;
}

//Task Result Patient
if('result_patient' === $task)
{
  //Check & Clean String
  $tid = $common->clean_string($_GET['tid']);

  $smarty_appform->assign('getTestById', $common->find('test', $condition = ['id' => $tid, 'lang' => $lang], $type = 'one'));
  $smarty_appform->display('common/test_result_patient.tpl');
  exit;
}

//Task: test save draft
if('test_save_draft' === $task)
{
  if($_POST)
  {
    $test_id        = $common->clean_string($_POST['test_id']);
    $test_group_id  = $common->clean_string($_POST['test_group_id']);
    $test_pat_id    = $common->clean_string($_POST['test_pat_id']);
    $test_que_data  = json_decode($_POST['test_que_data']);

    if(!empty($test_group_id))
    {
      $r_test_group_id = $test_group_id;
      //Get test_tmp By id
      $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $test_id, 'test_patient_id' => $test_pat_id], $type = 'one');

      if(!empty($resultTestTmp))
      {
        $resultTestTmpQue = $common->find('test_tmp_question', $condition = ['test_tmp_id' => $resultTestTmp['id'], 'test_group_id' => $test_group_id], $type = 'all');
        if(!empty($resultTestTmpQue))
        {
          foreach ($resultTestTmpQue as $k => $va) {
            //Delete: test_tmp_question
            $common->delete('test_tmp_question', $field = ['id' => $va['id']]);
          }
        }
        $test_tmp_id = $resultTestTmp['id'];
      } else {
        $test_tmp_id = $common->save('test_tmp', $field = ['test_id' => $test_id, 'test_patient_id' => $test_pat_id]);
      }

      if(!empty($test_que_data))
      {
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
          $result = $common->save('test_tmp_question', $field =['test_tmp_id' => $test_tmp_id, 'test_question_id' => $v->test_que_id, 'answer_id' => $answer_id, 'content' => $content, 'test_group_id' => $r_test_group_id, 'status' => 1]);
        }
        $resultValue = true;
      }

    } else {
      $r_test_group_id = NULL;
      //Get test_tmp By id
      $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $test_id, 'test_patient_id' => $test_pat_id], $type = 'one');
      //Delete: test_tmp & test_tmp_question
      $common->delete('test_tmp', $field = ['id' => $resultTestTmp['id']]);
      $common->delete('test_tmp_question', $field = ['test_tmp_id' => $resultTestTmp['id']]);
      $test_tmp_id = $common->save('test_tmp', $field = ['test_id' => $test_id, 'test_patient_id' => $test_pat_id]);

      if(!empty($test_que_data))
      {
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
          $result = $common->save('test_tmp_question', $field =['test_tmp_id' => $test_tmp_id, 'test_question_id' => $v->test_que_id, 'answer_id' => $answer_id, 'content' => $content, 'test_group_id' => $r_test_group_id]);
        }
        $resultValue = true;
      }//End foreach
    }
   $reListTestPatUncompleted = getListTestPatient($_SESSION['is_patient_login_id'], '', $tid = '', $status = 1, $tmpstus = 1, $f_date, $t_date, $lang);
  }

  header('Content-type: application/json');
  echo json_encode(array('status' => $resultValue, 'unCompleted' => COUNT($reListTestPatUncompleted)));
  exit;
}

if('test_uncompleted' === $task)
{
  // $tmpstus= !empty($_GET['stus']) ? $_GET['stus'] : '3';
  $f_date = !empty($_GET['f_date']) ? $_GET['f_date'] : '';
  $t_date = !empty($_GET['t_date']) ? $_GET['t_date'] : '';

  $results = getListTestPatient($_SESSION['is_patient_login_id'], '', $tid, $status = 1, $tmpstus = 1, $f_date, $t_date, $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('testPatient', $results);
  $smarty_appform->display('patient/index.tpl');
  exit;
}

// $tmpstus= !empty($_GET['stus']) ? $_GET['stus'] : '3';
$f_date = !empty($_GET['f_date']) ? $_GET['f_date'] : '';
$t_date = !empty($_GET['t_date']) ? $_GET['t_date'] : '';

$results = getListTestPatient($_SESSION['is_patient_login_id'], '', $tid, $status = 1, $tmpstus = 3, $f_date, $t_date, $lang);

(0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
SmartyPaginate::assign($smarty_appform);

$smarty_appform->assign('error', $error);
$smarty_appform->assign('testPatient', $results);
$smarty_appform->display('patient/index.tpl');
exit;
?>
