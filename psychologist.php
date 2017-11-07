<?php
//start session
session_start();
//require configuration file
require_once(dirname(__FILE__).'/setting/setup.php');
require_once(dirname(__FILE__).'/setting/common_setting.php');
require_once(dirname(__FILE__).'/functions/common/common_function.php');
require_once(dirname(__FILE__).'/functions/psychologist/psychologist_function.php');

//Get language By default_lang = 1
$resultLang = $common->find('language', $condition = ['default_lang' => 1], $type = 'one');
//Setting language
if(!empty($_GET['lang'])) $lang = $_GET['lang'];
if(empty($lang) and empty($_SESSION['lang'])) $lang = $resultLang['lang_name'];
if(!empty($lang) and empty($_SESSION['lang'])) $_SESSION['lang'] = $lang;
if(!empty($lang) and $lang != $_SESSION['lang']) $_SESSION['lang'] = $lang;
if(empty($lang) and $_SESSION['lang']) $lang = $_SESSION['lang'];
$smarty_appform->assign('mode', 'psychologist');
$smarty_appform->assign('psychologist_file', $psychologist_file);

//Smarty assign value
$smarty_appform->assign('lang_name', $resultLang['lang_name']);

//list menu language
$smarty_appform->assign('getLanguage', $common->find('language', $condition = null, $type = 'all'));
//Change language on template
$smarty_appform->assign('multiLang', getMultilang($lang));

//task: logout by clear session
if('logout' === $task) {
  $act_data = ['psychologist_id' => $_SESSION['is_psycho_login_id'], 'content' => 'LOGOUT'];
  @$common->save('activity_log', $act_data);
  unset($_SESSION['is_psycho_login_id']);
  header('Location:'.$index_file.'?task=login');
  exit;
}

//redirect if no session
if(empty($_SESSION['is_psycho_login_id']))
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
//Task: patient
if('patient' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['patient']);

  $error = array();
  if ('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $id     = $common->clean_string($_POST['id']);
      $username = $common->clean_string($_POST['username']);
      $email  = $common->clean_string($_POST['email']);
      $phone  = $common->clean_string($_POST['phone']);
      $gender = $common->clean_string($_POST['gender']);
      $age    = $common->clean_string($_POST['age']);
      $code   = $common->clean_string($_POST['code']);
      $password = $common->clean_string($_POST['password']);

      $re_code_pat = getPsychologistByIdCodePat($_SESSION['is_psycho_login_id']);
      $r_code = $re_code_pat['code_pat'].$code;

      //add value to session to use in template
      $_SESSION['patient'] = $_POST;
      //form validation
      if(empty($username))$error['username']  = 1;
      if(empty($email))   $error['email'] = 1;
      if(empty($phone))   $error['phone'] = 1;
      if(empty($gender))  $error['gender']  = 1;
      if(empty($age))     $error['age'] = 1;
      if(empty($code))    $error['code']  = 1;
      if(empty($password))$error['password']  = 1;
      if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
  		   $error['invalid_email'] = 1;
  		}
      if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
        $result = $common->find('patient', $condition = ['id' => $_GET['id']], $type='one');
        if($result['email'] !== $email && check_patient_email($email) > 0){
          $error['exist_email'] = 1;
        }
      }
      if(check_code_pat($r_code) > 0) $error['code_existed']  = 1;

      //Save
      if(empty($id) && COUNT($error) === 0)
      {
        $common->save('patient', $field =['psychologist_id' => $_SESSION['is_psycho_login_id'],
                                          'username'=> $username,
                                          'email'   => $email,
                                          'phone'   => $phone,
                                          'gender'  => $gender,
                                          'age'     => $age,
                                          'password'=> $password,
                                          'code'    => $r_code]);
        //unset session
        unset($_SESSION['patient']);
        //Redirect
        header('location: '.$psychologist_file.'?task=patient');
        exit;
      }
    }
  }
  //Change staus patient
  if('change_status' === $action && !empty($_GET['id']))
  {
    $id = $common->clean_string($_GET['id']);
    //Get Patient: and check patient's psychologist
    $resutlGetPatientById = getPatientByID($id, $_SESSION['is_psycho_login_id']);

    if(!empty($resutlGetPatientById) && COUNT($resutlGetPatientById) > 0)
    {
      if(!empty($_GET['status'] == 1))
      {
        $common->update('patient', $field = ['status' => 2], $condition = ['id' => $id, 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
      } elseif (!empty($_GET['status'] == 2)) {
        $common->update('patient', $field = ['status' => 1], $condition = ['id' => $id, 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
      }
    } else {
      header('location:'.$psychologist_file.'?task=page_not_found');
      exit;
    }
    header('location:'.$psychologist_file.'?task=patient');
    exit;
  }
  //action delete staff role
  if('delete' === $action && !empty($_GET['id']))
  {
    $id = $common->clean_string($_GET['id']);
    //Get Patient: and check patient's psychologist
    $resutlGetPatientById = getPatientByID($id, $_SESSION['is_psycho_login_id']);

    if(!empty($resutlGetPatientById) && COUNT($resutlGetPatientById) > 0)
    {
      $deleted_at = date("Y-m-d");
      $common->update('patient', $field = ['deleted_at' => $deleted_at], $condition = ['id' => $id, 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
    } else {
      header('location:'.$psychologist_file.'?task=page_not_found');
      exit;
    }
    header('location:'.$psychologist_file.'?task=patient');
    exit;
  }
  //Action: edit
  if('edit' === $action && !empty($_GET['id']))
  {
    $id = $common->clean_string($_GET['id']);
    //Get Patient: and check patient's psychologist
    $resutlGetPatientById = getPatientByID($id, $_SESSION['is_psycho_login_id']);
    if(empty($resutlGetPatientById)) {
      header('location:'.$psychologist_file.'?task=page_not_found');
      exit;
    }

    if($_POST)
    {
      //get value from form
      $id     = $common->clean_string($_POST['id']);
      $username = $common->clean_string($_POST['username']);
      $email  = $common->clean_string($_POST['email']);
      $phone  = $common->clean_string($_POST['phone']);
      $gender = $common->clean_string($_POST['gender']);
      $age    = $common->clean_string($_POST['age']);
      $code   = $common->clean_string($_POST['code']);
      $password = $common->clean_string($_POST['password']);
      $re_code_pat = getPsychologistByIdCodePat($_SESSION['is_psycho_login_id']);
      $r_code = $re_code_pat['code_pat'].$code;

      //add value to session to use in template
      $_SESSION['patient'] = $_POST;
      //form validation
      if(empty($username))$error['username']  = 1;
      if(empty($email))   $error['email']   = 1;
      if(empty($phone))   $error['phone']   = 1;
      if(empty($gender))  $error['gender']  = 1;
      if(empty($age))     $error['age']     = 1;
      if(empty($code))    $error['code']  = 1;
      if(empty($password))$error['password']  = 1;
      if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
  		   $error['invalid_email'] = 1;
  		}
      if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
        $result = $common->find('patient', $condition = ['id' => $_GET['id']], $type='one');
        if($result['email'] !== $email && check_patient_email($email) > 0){
          $error['exist_email'] = 1;
        }
      }
      $resultPat = $common->find('patient', $condition = ['id' => $id], $type = 'one');
      if($resultPat['code'] !== $r_code && check_code_pat($r_code) > 0) $error['code_existed']  = 1;

      //Update
      if(!empty($id) && COUNT($error) === 0)
      {
        $common->update('patient', $field= ['username' => $username,
                                            'email'    => $email,
                                            'phone'    => $phone,
                                            'gender'   => $gender,
                                            'age'      => $age,
                                            'password' => $password,
                                            'code'     => $r_code],
                                   $condition = ['id' => $id, 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
        //unset session
        unset($_SESSION['patient']);
        //Redirect
        header('location: '.$psychologist_file.'?task=patient');
        exit;
      }
    }
    $smarty_appform->assign('editPatient', $resutlGetPatientById);
  }

  $kwd = !empty($_GET['kwd']) ? $common->clean_string($_GET['kwd']) : '';
  $gender = !empty($_GET['gender']) ? $common->clean_string($_GET['gender']) : '';
  $status = !empty($_GET['status']) ? $common->clean_string($_GET['status']) : '';
  $result = listPatient($kwd, $_SESSION['is_psycho_login_id'], $gender, $status);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listPatient', $result);
  $smarty_appform->assign('psyCodePat', getPsychologistByIdCodePat($_SESSION['is_psycho_login_id']));
  $smarty_appform->display('psychologist/patient.tpl');
  exit;
}
//Task: Test Patient for assig test to patient
if('test_patient' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['test_patient']);

  $error = array();
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      if(!empty($_POST['id'])) {
        $testid   = $common->clean_string($_POST['test']);
      } else {
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
    }
  }

  //Delete: test psychologist
  if('delete' === $action && !empty($_GET['id']))
  {
    $tpid = $common->clean_string($_GET['id']);
    $tid  = $common->clean_string($_GET['tid']);
    $pat_id = $common->clean_string($_GET['pat_id']);

    $resultTestPsychologist = getCheckTestPatientByPsyChologist($_SESSION['is_psycho_login_id'], $pat_id, $tid, $tpid);
    if(empty($resultTestPsychologist)) {
      header('Location:'.$psychologist_file.'?task=page_not_found');
      exit;
    }
    $common->delete('test_patient', $field = ['id' => $tpid]);
    header('location: '.$psychologist_file.'?task=test_patient');
    exit;
  }
  //get edit apitransaction
  if('edit' === $action && !empty($_GET['id']))
  {
    $tpid = $common->clean_string($_GET['id']);
    $tid  = $common->clean_string($_GET['tid']);
    $pat_id = $common->clean_string($_GET['pat_id']);

    $resultTestPsychologist = getCheckTestPatientByPsyChologist($_SESSION['is_psycho_login_id'], $pat_id, $tid, $tpid);

    if(empty($resultTestPsychologist)) {
      header('Location:'.$psychologist_file.'?task=page_not_found');
      exit;
    }
    if($_POST)
    {
      //get value from form
      if(!empty($_POST['id'])) {
        $testid   = $common->clean_string($_POST['test']);
      } else {
        $testid   = $common->clean_string_array($_POST['test']);
      }
      $pat_tid  = $common->clean_string($_POST['pat_id']);
      $id       = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['test_psy'] = $_POST;
      //form validation
      if(empty($testid))  $error['testid']  = 1;
      if(empty($pat_tid))  $error['psy_id']  = 1;

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
    $smarty_appform->assign('getTestPat', $common->find('test_patient', $condition = ['id' => $tpid], $type = 'one'));
  }

  if(empty($action))
  {
    $s_tid    = !empty($_GET['tid']) ? $common->clean_string($_GET['tid']) : '';
    $s_pat_id = !empty($_GET['pat_id']) ? $common->clean_string($_GET['pat_id']) : '';
    $status = !empty($_GET['status']) ? $common->clean_string($_GET['status']) : '';
  }

  $results = getListTestPatient($s_pat_id, $_SESSION['is_psycho_login_id'], $s_tid, $status, '', '', '', $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('testPatient', $results);
  $smarty_appform->assign('test', getListTestPsychologistCompleted($_SESSION['is_psycho_login_id'], $lang));
  $smarty_appform->assign('patient', $common->find('patient', $condition = ['psychologist_id' => $_SESSION['is_psycho_login_id']], $type = 'all'));
  $smarty_appform->display('psychologist/test_patient.tpl');
  exit;
}
//Task: Test Question
if('test_question_psychologist' === $task)
{
  //Check & Clean String
  $psy_id = $common->clean_string($_GET['psy_id']);
  $tpsy_id  = $common->clean_string($_GET['id']);
  $tid      = $common->clean_string($_GET['tid']);
  $resultTestPsychologist = $common->find('test_psychologist', $condition = ['id' => $tpsy_id, 'test_id' => $tid, 'psychologist_id' => $psy_id], $type = 'one');

  if(empty($resultTestPsychologist))
  {
    header('Location:'.$psychologist_file.'?task=page_not_found');
    exit;
  }
  if(!empty($resultTestPsychologist) && $resultTestPsychologist['status'] == 2)
  {
    header('Location:'.$patient_file.'?task=test_completed&tid='.$resultTestPsychologist['test_id']);
    exit;
  }
  //Get Test Group By test_id
  $resultTestGroup = listTestGroupByTestId($tid);

  if(empty($_SESSION['tgroupid'])) $_SESSION['tgroupid'] = array();
  $error = array();
  if ('add' === $action) {
    if($_POST)
    {
      //Check & Clean String
      $tpsy_id      = $common->clean_string($_GET['id']);
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
          $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $tid, 'test_psychologist_id' => $tpsy_id], $type = 'one');
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
          $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $tid, 'test_psychologist_id' => $tpsy_id], $type = 'one');

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
            $test_tmp_id = $common->save('test_tmp', $field = ['test_id' => $tid, 'test_psychologist_id' => $tpsy_id]);
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
        $resultTestGroupTmpQue = getListTestGroupByTmpQuestionPsy($tid, $tpsy_id, $status = 1, $fetch_type = 'all', $slimit = '');

        //Condition test group for assign value to Result
        if(COUNT($resultTestGroupTmpQue) === 0 && COUNT($resultTestGroup) > 0)
        {
          //Get test_tmp By id
          $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $tid, 'test_psychologist_id' => $tpsy_id], $type = 'one');
          $resultTestTmpQue = $common->find('test_tmp_question', $condition = ['test_tmp_id' => $resultTestTmp['id']], $type = 'all');
          //Save Data to table response
          $responseid = $common->save('response', $field = ['unique_id' => time(), 'test_id' => $tid, 'test_psychologist_id' => $tpsy_id]);
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

          $common->update('test_psychologist', $field = ['status' => 2, 'psy_working_by' => NULL, 'completed_date' => date("Y-m-d")], $condition = ['id' => $tpsy_id, 'psychologist_id' => $_SESSION['is_psycho_login_id'], 'test_id' => $tid]);
          //Delete: test_tmp & test_tmp_question
          $common->delete('test_tmp', $field = ['id' => $resultTestTmp['id']]);
          $common->delete('test_tmp_question', $field = ['test_tmp_id' => $resultTestTmp['id']]);
          if(!empty($subscribers))
          {
            exec($path_exec.'/submitmail_by_test.php '.$responseid.' '.$tid.' > /dev/null &');
          }
          header('Location:'.$psychologist_file.'?task=result_test_psychologist&tid='.$tid.'&psy_id='.$psy_id.'&id='.$tpsy_id);
          exit;
        }
        //Condition test no group
        if(!empty($answer_id) && COUNT($resultTestGroup) == 0)
        {
          //Save Data to table response
          $responseid = $common->save('response', $field = ['unique_id' => time(), 'test_id' => $tid, 'test_psychologist_id' => $tpsy_id]);

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

          $common->update('test_psychologist', $field = ['status' => 2, 'psy_working_by' => NULL, 'completed_date' => date("Y-m-d")], $condition = ['id' => $tpsy_id, 'psychologist_id' => $_SESSION['is_psycho_login_id'], 'test_id' => $tid]);
          //Get test_tmp By id
          $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $tid, 'test_psychologist_id' => $tpsy_id], $type = 'one');
          //Delete: test_tmp & test_tmp_question
          $common->delete('test_tmp', $field = ['id' => $resultTestTmp['id']]);
          $common->delete('test_tmp_question', $field = ['test_tmp_id' => $resultTestTmp['id']]);

          if(!empty($subscribers))
          {
            exec($path_exec.'/submitmail_by_test.php '.$responseid.' '.$tid.' > /dev/null &');
          }
          //Clear sesson
          unset($_SESSION['tgroupid']);
          header('Location:'.$psychologist_file.'?task=result_test_psychologist&tid='.$tid.'&psy_id='.$psy_id.'&rsid='.$responseid.'&id='.$tpsy_id);
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
  }
  //Get Test Group By Tmp Question
  $resultTestGroupTmpQue = getListTestGroupByTmpQuestionPsy($tid, $tpsy_id, $status = 1, $fetch_type = 'all', $slimit = '');

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
    header('Location:'.$psychologist_file.'?task=page_not_found');
    exit;
  }

  $sumStep = COUNT($resultTestGroup) - COUNT(getListTestGroupByTmpQuestionPsy($tid, $tpsy_id, $status = 2, $fetch_type = 'all', $slimit = ''));

  if(!empty($error))
  {
    $smarty_appform->assign('testTmpQuestion', $newResultSubmitError);
  } else {
    $smarty_appform->assign('testTmpQuestion', getTestTmpQuestionPsy($tpsy_id, $tid));
  }

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('ResultJumpTo', json_encode($newResultJumpTo));
  $smarty_appform->assign('totalAnswer', $total_data);
  $smarty_appform->assign('resultQueIdJumpTo', $resultQueIdJumpTo);
  $smarty_appform->assign('getTestById', $getTestByID);
  $smarty_appform->assign('result', $result);
  $smarty_appform->assign('test_group_id', $test_group_id);
  $smarty_appform->assign('testGroupIDTmpQue', getListTestGroupByTmpQuestionPsy($tid, $tpsy_id, $status = 2, $fetch_type = 'one', $slimit = 1)); //For get "Test_Group_Id" Back Step
  //Check Step
  $smarty_appform->assign('resultTestGroupTmpQue', COUNT(getListTestGroupByTmpQuestionPsy($tid, $tpsy_id, $status = 2, $fetch_type = 'all', $slimit = ''))); //For Check Show Button Next Or Finish
  $smarty_appform->assign('testQueGroup', COUNT($resultTestGroup));
  $smarty_appform->assign('resultStep', $sumStep);
  $smarty_appform->display('psychologist/test_question_responsive_psychologist.tpl');
  exit;
}
//Task: test save draft
if('test_save_draft' === $task)
{
  if($_POST)
  {
    $test_id        = $common->clean_string($_POST['test_id']);
    $test_group_id  = $common->clean_string($_POST['test_group_id']);
    $test_psy_id    = $common->clean_string($_POST['test_psy_id']);
    $test_que_data  = json_decode($_POST['test_que_data']);

    if(!empty($test_group_id))
    {
      $r_test_group_id = $test_group_id;
      //Get test_tmp By id
      $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $test_id, 'test_psychologist_id' => $test_psy_id], $type = 'one');

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
        $test_tmp_id = $common->save('test_tmp', $field = ['test_id' => $test_id, 'test_psychologist_id' => $test_psy_id]);
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
      $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $test_id, 'test_psychologist_id' => $test_psy_id], $type = 'one');
      //Delete: test_tmp & test_tmp_question
      $common->delete('test_tmp', $field = ['id' => $resultTestTmp['id']]);
      $common->delete('test_tmp_question', $field = ['test_tmp_id' => $resultTestTmp['id']]);
      $test_tmp_id = $common->save('test_tmp', $field = ['test_id' => $test_id, 'test_psychologist_id' => $test_psy_id]);

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

  }
  header('Content-type: application/json');
  echo json_encode($resultValue);
  exit;
}
//Task: Back Step Test Question
if('back_step' === $task && !empty($_GET['tid']))
{
  $tpsy_id = $common->clean_string($_GET['id']);
  $psy_id =  $common->clean_string($_GET['psy_id']);
  $tgid = $common->clean_string($_GET['tgid']);
  $tid  = $common->clean_string($_GET['tid']);
  //Get Test Psychologist
  $resultTestPsychologist = $common->find('test_psychologist', $condition = ['id' => $tpsy_id, 'test_id' => $tid, 'psychologist_id' => $_SESSION['is_psycho_login_id']], $type = 'one');

  if(empty($resultTestPsychologist)){
    header('Location:'.$psychologist_file.'?task=page_not_found');
    exit;
  }
  if(!empty($resultTestPsychologist) && $resultTestPsychologist['status'] == 2)
  {
    header('Location:'.$psychologist_file.'?task=test_completed&tid='.$resultTestPsychologist['test_id']);
    exit;
  }
  //Get test_tmp By id
  $resultTestTmp = $common->find('test_tmp', $condition = ['test_id' => $tid, 'test_psychologist_id' => $tpsy_id], $type = 'one');
  $resultTestTmpQue = $common->find('test_tmp_question', $condition = ['test_tmp_id' => $resultTestTmp['id'], 'test_group_id' => $tgid], $type = 'all');

  $common->update('test_tmp_question', $field = ['status' => 1], $condition = ['test_tmp_id' => $resultTestTmp['id'], 'test_group_id' => $tgid]);
  header('Location:'.$psychologist_file.'?task=test_question_psychologist&tid='.$tid.'&psy_id='.$psy_id.'&id='.$tpsy_id);
  exit;
}
//Task: test_psychologist
if('test_psychologist' === $task)
{
  $tid    = !empty($_GET['tid']) ? $_GET['tid'] : '';
  $cid    = !empty($_GET['cid']) ? $_GET['cid'] : '';
  $status = !empty($_GET['status']) ? $_GET['status'] : '1';

  $results = getListTestPsychologist($_SESSION['is_psycho_login_id'], $tid, $cid, $status);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('listTestPsychologist', $results);
  $smarty_appform->assign('test', $common->find('test', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->assign('category', $common->find('category', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->display('psychologist/test_psychologist.tpl');
  exit;
}
//Task: Result psychologist
if('result_test_psychologist' === $task)
{
    //Check & Clean String
    $tpsy_id  = $common->clean_string($_GET['id']);
    $tid      = $common->clean_string($_GET['tid']);
    $psy_id   = $common->clean_string($_GET['psy_id']);
    $sumTotal = 0; $sumAssignWeight = 0; $sumDefault = 0; $diagram_width = 820;
    $space_height = 50; $margin_left = 450; $space_row_col = 50;
    $moveTo_left  = $margin_left + 90;
    $moveTo_top   = 60;

    $resultTestPsychologist = getCheckTestPsyChologistByPsyChologist($_SESSION['is_psycho_login_id'], $tid, $tpsy_id);
    if(empty($resultTestPsychologist)) {
      header('Location:'.$psychologist_file.'?task=page_not_found');
      exit;
    }

    //Get Result Answer Topic
    $getResultTopic = getResultAnswerTopic('', $tpsy_id, $tid, '', '');
    // var_dump($getResultTopic);
    //Get List Topic in diagram second
    $resultTopicDiagramSecond = getListTopicDiagramSecond($getResultTopic, 10, 240, $lang);
    //Get List Topic Analysis
    $resultTopicAnalysis= listTopicAnalysisDiagram($margin_left + 25, 203, $space_row_col, $tid);
    //Get List Topic in diagram first
    $resultTopicDiagram = getListTopicDiagram('', $tpsy_id, $tid,10, 140);
    //Calculate width and height on canvas2
    $resultWidthHeightSecond = calWidthHeightDiagramSecond(COUNT($resultTopicDiagramSecond), COUNT($resultTopicAnalysis), $space_height + 160, $margin_left, $space_result_topic = 150);
    //Drawing Line Result Diagram Second
    $resultDrawingLineResultDiagramSecond = drawingPointLineResultDiagramSecond($resultTopicDiagramSecond, $resultTopicAnalysis, $margin_left + 25, $margin_top = 235, $resultWidthHeightSecond['width'] - 145);

    //Assign value to diagram first
    $smarty_appform->assign('listXlineDiagram', listXlineDiagram(COUNT($resultTopicDiagram), $diagram_width, $space_height + 60));//Horizontal Line
    $smarty_appform->assign('listXdiagramCenter', listXlineDiagramCenter(COUNT($resultTopicDiagram), $diagram_width - 70, $space_height + 85, $margin_left));//List Diagram Horizontal line center
    $smarty_appform->assign('getWidthHeight', calWidthHeightDiagram(COUNT($resultTopicDiagram), $diagram_width, $space_height));//Calculate width and Height on canvas
    $smarty_appform->assign('listNumberMinMax', listNumberMinMax($margin_left, $margin_top = 90));//List Text Number Min & Max
    $smarty_appform->assign('listTextMinMax', listTextMinMax($margin_left, $margin_top = 75));//List Text Min & Max
    $smarty_appform->assign('listBackgroudColor', listBackgroundColorDiagram($margin_left, 110, 50));//List backgroud diagram
    $smarty_appform->assign('drawingPointLine', drawingPointLineResult($resultTopicDiagram, $margin_left - 100, $margin_top = 135, $tid));//Drawing point line
    $smarty_appform->assign('listYLineDiagram', listYLineDiagram($margin_left, 50 + 50));//Vertical Line
    $smarty_appform->assign('listYLineDiagramCenter', listYLineDiagramCenter($margin_left, 50 + 50));//Vertical Line center
    $smarty_appform->assign('listSmallYLineDiagram', listSmall_YlineDiagram($margin_left, 90));//Vertical small Line
    $smarty_appform->assign('listTopicDiagram', $resultTopicDiagram);
    //end

    //Assign value to diagram second
    $smarty_appform->assign('reponseAnswerByTestPsyt', getResponseAnswerByTestPsychologist($tid, $tpsy_id));
    $smarty_appform->assign('getWidthHeightSecond', $resultWidthHeightSecond);
    $smarty_appform->assign('listTopicDiagramSecond', $resultTopicDiagramSecond);
    $smarty_appform->assign('listTopicAnalysis', $resultTopicAnalysis);
    $smarty_appform->assign('drawingLineResultDiagramSecond', $resultDrawingLineResultDiagramSecond);
    $smarty_appform->assign('listBackgroudColorSecond', listBackgroundColorDiagramSecond(COUNT($resultTopicAnalysis), $margin_left, $space_height + 160, $space_row_col));//List background color Diagram Second
    $smarty_appform->assign('listXlineDiagramSecond', listXlineDiagram(COUNT($resultTopicDiagramSecond), $resultWidthHeightSecond['width'], $space_height + 160));//Horizontal line
    $smarty_appform->assign('listXlineDiagramSecondCenter', listXlineDiagramCenter(COUNT($resultTopicDiagramSecond), $resultWidthHeightSecond['width'] - 150, $space_height + 185, $margin_left)); //List Diagram Second Horizontal line center
    $smarty_appform->assign('listRotateLineDiagramSecond', listRotateLineDiagramSecond(COUNT($resultTopicAnalysis), $margin_left, $space_height + 160, $moveTo_left, $moveTo_top, $space_row_col));//List Rotate Line Diagram Second
    $smarty_appform->assign('listYLineDiagramSecond', listYLineDiagramSecond(COUNT($resultTopicAnalysis), $margin_left, $space_height + 160));//Vertical Line
    //end

    // var_dump(getMessageResultTopic('', $tpsy_id, $tid, $lang));
    $smarty_appform->assign('messageResultTopic', getMessageResultTopic('', $tpsy_id, $tid, $lang));
    $smarty_appform->assign('psychologist', $common->find('psychologist', $condition = ['id' => $_SESSION['is_psycho_login_id']], $type = 'one'));
    $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $tid, 'lang' => $lang], $type = 'one'));
    $smarty_appform->display('psychologist/result_test_psychologist.tpl');
    exit;
}
//Task: result_test_patient
if('result_test_patient' === $task)
{
  //Check & Clean String
  $tpid = $common->clean_string($_GET['id']);
  $tid  = $common->clean_string($_GET['tid']);
  $pat_id = $common->clean_string($_GET['pat_id']);
  $sumTotal = 0; $sumAssignWeight = 0; $sumDefault = 0; $diagram_width = 820;
  $space_height = 50; $margin_left = 450; $space_row_col = 50;
  $moveTo_left  = $margin_left + 90;
  $moveTo_top   = 60;

  $resultTestPsychologist = getCheckTestPatientByPsyChologist($_SESSION['is_psycho_login_id'], $pat_id, $tid, $tpid);

  if(empty($resultTestPsychologist)) {
    header('Location:'.$psychologist_file.'?task=page_not_found');
    exit;
  }

  //Get Result Answer Topic
  $getResultTopic = getResultAnswerTopic($tpid, '', $tid, '', '');
  //Get List Topic in diagram second
  $resultTopicDiagramSecond = getListTopicDiagramSecond($getResultTopic, 10, 240, $lang);
  //Get List Topic Analysis
  $resultTopicAnalysis= listTopicAnalysisDiagram($margin_left + 25, 203, $space_row_col, $tid);
  //Get List Topic in diagram first
  $resultTopicDiagram = getListTopicDiagram($tpid, '', $tid,10, 140);

  //Calculate width and height on canvas2
  $resultWidthHeightSecond = calWidthHeightDiagramSecond(COUNT($resultTopicDiagramSecond), COUNT($resultTopicAnalysis), $space_height + 160, $margin_left, $space_result_topic = 150);
  //Drawing Line Result Diagram Second
  $resultDrawingLineResultDiagramSecond = drawingPointLineResultDiagramSecond($resultTopicDiagramSecond, $resultTopicAnalysis, $margin_left + 25, $margin_top = 235, $resultWidthHeightSecond['width'] - 145);

  //Assign value to diagram first
  $smarty_appform->assign('listXlineDiagram', listXlineDiagram(COUNT($resultTopicDiagram), $diagram_width, $space_height + 60));//Horizontal Line
  $smarty_appform->assign('listXdiagramCenter', listXlineDiagramCenter(COUNT($resultTopicDiagram), $diagram_width - 70, $space_height + 85, $margin_left));//List Diagram Horizontal line center
  $smarty_appform->assign('getWidthHeight', calWidthHeightDiagram(COUNT($resultTopicDiagram), $diagram_width, $space_height));//Calculate width and Height on canvas
  $smarty_appform->assign('listNumberMinMax', listNumberMinMax($margin_left, $margin_top = 90));//List Text Number Min & Max
  $smarty_appform->assign('listTextMinMax', listTextMinMax($margin_left, $margin_top = 75));//List Text Min & Max
  $smarty_appform->assign('listBackgroudColor', listBackgroundColorDiagram($margin_left, 110, 50));//List backgroud diagram
  $smarty_appform->assign('drawingPointLine', drawingPointLineResult($resultTopicDiagram, $margin_left - 100, $margin_top = 135, $tid));//Drawing point line
  $smarty_appform->assign('listYLineDiagram', listYLineDiagram($margin_left, 50 + 50));//Vertical Line
  $smarty_appform->assign('listYLineDiagramCenter', listYLineDiagramCenter($margin_left, 50 + 50));//Vertical Line center
  $smarty_appform->assign('listSmallYLineDiagram', listSmall_YlineDiagram($margin_left, 90));//Vertical small Line
  $smarty_appform->assign('listTopicDiagram', $resultTopicDiagram);
  //end

  //Assign value to diagram second
  $smarty_appform->assign('getWidthHeightSecond', $resultWidthHeightSecond);
  $smarty_appform->assign('listTopicDiagramSecond', $resultTopicDiagramSecond);
  $smarty_appform->assign('listTopicAnalysis', $resultTopicAnalysis);
  $smarty_appform->assign('drawingLineResultDiagramSecond', $resultDrawingLineResultDiagramSecond);
  $smarty_appform->assign('listBackgroudColorSecond', listBackgroundColorDiagramSecond(COUNT($resultTopicAnalysis), $margin_left, $space_height + 160, $space_row_col));//List background color Diagram Second
  $smarty_appform->assign('listXlineDiagramSecond', listXlineDiagram(COUNT($resultTopicDiagramSecond), $resultWidthHeightSecond['width'], $space_height + 160));//Horizontal line
  $smarty_appform->assign('listXlineDiagramSecondCenter', listXlineDiagramCenter(COUNT($resultTopicDiagramSecond), $resultWidthHeightSecond['width'] - 150, $space_height + 185, $margin_left)); //List Diagram Second Horizontal line center
  $smarty_appform->assign('listRotateLineDiagramSecond', listRotateLineDiagramSecond(COUNT($resultTopicAnalysis), $margin_left, $space_height + 160, $moveTo_left, $moveTo_top, $space_row_col));//List Rotate Line Diagram Second
  $smarty_appform->assign('listYLineDiagramSecond', listYLineDiagramSecond(COUNT($resultTopicAnalysis), $margin_left, $space_height + 160));//Vertical Line
  //end

  // var_dump(getMessageResultTopic($tpid, $tid, $lang));

  $smarty_appform->assign('messageResultTopic', getMessageResultTopic($tpid, '', $tid, $lang));
  $smarty_appform->assign('reponseAnswerByTestPat', getResponseAnswerByTestPatient($tid, $tpid));
  $smarty_appform->assign('patient', $common->find('patient', $condition = ['id' => $pat_id,'psychologist_id' => $_SESSION['is_psycho_login_id']], $type = 'one'));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $tid, 'lang' => $lang], $type = 'one'));
  $smarty_appform->display('psychologist/result_test_patient.tpl');
  exit;
}
//Task: Test Question
if('test_question_patient' === $task)
{
  //Check & Clean String
  $tpid = $common->clean_string($_GET['id']);
  $tid  = $common->clean_string($_GET['tid']);
  $pat_id  = $common->clean_string($_GET['pat_id']);

  $resultTestPsychologist = getCheckTestPatientByPsyChologist($_SESSION['is_psycho_login_id'], $pat_id, $tid, $tpid);
  if(empty($resultTestPsychologist))
  {
    header('Location:'.$psychologist_file.'?task=page_not_found');
    exit;
  }

  //Get Test Group By test_id
  $resultTestGroup = listTestGroupByTestId($tid);

  if(empty($_SESSION['tgroupid'])) $_SESSION['tgroupid'] = array();
  $error = array();
  if ('add' === $action) {
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
            if(!empty($responseid))
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
          header('Location:'.$psychologist_file.'?task=result_patient&tid='.$tid);
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
          //Clear sesson
          unset($_SESSION['tgroupid']);
          header('Location:'.$psychologist_file.'?task=result_patient&tid='.$tid);
          exit;
        }//End Condition test no group

      } else {
        //Condition has submit error

      }

    }//End POST
  }
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

  if(empty($result) && COUNT($result) === 0 && empty($getTestByID)){
    header('Location:'.$psychologist_file.'?task=page_not_found');
    exit;
  }

  $sumStep = COUNT($resultTestGroup) - COUNT(getListTestGroupByTmpQuestion($tid, $tpid, $status = 2, $fetch_type = 'all', $slimit = ''));

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('ResultJumpTo', json_encode($newResultJumpTo));
  $smarty_appform->assign('testTmpQuestion', getTestTmpQuestion($tpid, $tid));
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
  $smarty_appform->display('common/test_question_responsive_patient.tpl');
  exit;
}
//task: town_village
if('town_village' === $task)
{
  if(empty($_POST)) unset($_SESSION['category']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $name = $common->clean_string($_POST['name']);
      //add value to session to use in template
      $_SESSION['town_village'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //Add test
      if(0 === count($error))
      {
        $common->save('village', $field = ['name' => $name, 'lang' => $lang]);
        //unset session
        unset($_SESSION['town_village']);
        //Redirect
        header('location: '.$psychologist_file.'?task=town_village');
        exit;
      }
    }
  }

  //action edit
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $name = $common->clean_string($_POST['name']);
      $id   = $common->clean_string($_POST['id']);
      //add value to session to use in template
      $_SESSION['town_village'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //update test
      if(0 === count($error) && !empty($id))
      {
        $common->update('village', $field = ['name' => $name, 'lang' => $lang], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['town_village']);
        //Redirect
        header('location: '.$psychologist_file.'?task=town_village');
        exit;
      }
    }
    $smarty_appform->assign('getVillageByID', $common->find('village', $condition = ['id' => $_GET['id']], $type = 'one'));
  }

  //action delete category
  if('delete' === $action && !empty($_GET['id']))
  {
    $result = checkDeleteVillage($_GET['id']);

    if('0' === $result['total_count'])
    {
      $common->delete('village', $field = ['id' => $_GET['id']]);
    }else {
      setcookie('checkVillage', $result['name'], time() + 5);
    }
    header('location: '.$psychologist_file.'?task=town_village');
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $result = listVillage($kwd, $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('listVillage', $result);
  $smarty_appform->display('psychologist/town_village.tpl');
  exit;
}

$tid    = !empty($_GET['tid']) ? $_GET['tid'] : '';
$pat_id = !empty($_GET['pat_id']) ? $_GET['pat_id'] : '';

$results = getListTestPatient($pat_id, $_SESSION['is_psycho_login_id'], $tid, $status = 1, $tmpstus = 1, '', '', $lang);

(0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
SmartyPaginate::assign($smarty_appform);

$smarty_appform->assign('error', $error);
$smarty_appform->assign('testPatient', $results);
$smarty_appform->assign('test', getListTestPsychologistCompleted($_SESSION['is_psycho_login_id'], $lang));
$smarty_appform->assign('patient', $common->find('patient', $condition = ['psychologist_id' => $_SESSION['is_psycho_login_id']], $type = 'all'));
$smarty_appform->display('psychologist/index.tpl');
exit;
?>
