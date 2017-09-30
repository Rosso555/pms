<?php
/**
* Check staff permission
*
* @param int $role_id Staff role id taken from staff_role_id of table staff
* @param string $task Task name. Ex: staff
* @param string $action Action name. Ex: edit
* @return boolean If it is true, it means that staff has permission to perform this function
*/
function auth($role_id, $task, $action = '')
{
 global $debug, $connected;

 //super admin
 if(isset($_SESSION['is_super_admin']) && !empty($_SESSION['is_super_admin'])) return true;

 $result = true;
 try
 {
   //Get function id
   $sql = "SELECT id FROM `staff_function` WHERE task_name = :task_name ";
   if(!empty($action)) $sql .= "AND action_name = :action_name";
   $stmt = $connected->prepare($sql);
   $stmt->bindValue(':task_name', $task, PDO::PARAM_STR);
   if(!empty($action)) $stmt->bindValue(':action_name', $action, PDO::PARAM_STR);
   $stmt->execute();
   $row = $stmt->fetch();
   //print_r($row);
   if(empty($row['id'])) return false;

   $stmt = $connected->prepare('SELECT count(*) AS total FROM `staff_permission` WHERE staff_role_id = :role_id AND staff_function_id = :function_id');
   $stmt->bindValue(':role_id', (int)$role_id, PDO::PARAM_INT);
   $stmt->bindValue(':function_id', (int)$row['id'], PDO::PARAM_INT);
   $stmt->execute();
   $row = $stmt->fetch();
   if(empty($row['total'])) return false;
   if(!empty($row['total']) && $row['total']>0) return true;

 } catch(PDOException $e) {

   $result = false;
   if ($debug)
   {
     echo 'ERROR: ' . $e->getMessage();
     exit;
   }

 }

 return $result;
}
/**
 * getMailerLiteByTestId
 * @param  int $testid is test_id
 * @return array or boolean
 */
function getMailerLiteByTestId($testid)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT m.*, apit.id AS transaction_id FROM `apitransaction` apit INNER JOIN mailerlite m ON m.id = apit.ml_id WHERE apit.test_id = :test_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$testid, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();

    return $rows;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getMailerLiteByTestId'.$e->getMessage();
  }
  return $result;
}
/**
 * getStaffPermissionData
 * @param  int $srid is staff role id
 * @return array or boolean
 */
function getStaffPermissionData($srid)
{
  global $debug, $connected;

  $result = true;
  try
  {
    //Get Task Name
    $sql = ' SELECT * FROM `staff_permission` sp INNER JOIN staff_function sf ON sf.id = sp.staff_function_id WHERE sp.staff_role_id = :staff_role_id GROUP BY sf.task_name ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':staff_role_id', $srid, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $newResult = array();
    foreach ($rows as $key => $value) {
      //Get Action Name
      $sql1 = ' SELECT action_name FROM `staff_permission` sp INNER JOIN staff_function sf ON sf.id = sp.staff_function_id WHERE sp.staff_role_id = :staff_role_id AND sf.action_name != "" AND task_name = :task_name ';
      $stmt1 = $connected->prepare($sql1);
      $stmt1->bindValue(':staff_role_id', $srid, PDO::PARAM_INT);
      $stmt1->bindValue(':task_name', $value['task_name'], PDO::PARAM_STR);
      $stmt1->execute();
      $rows1 = $stmt1->fetchAll();
      if(!empty($rows1)){
        foreach ($rows1 as $k => $va) {
          $newResult[$value['task_name']][] = array($va['action_name'] => $va['action_name']);
        }
      }else {
        $newResult[$value['task_name']][] = array();
      }

    }

    return $newResult;
  } catch(PDOException $e) {

    $result = false;
    if ($debug)
    {
      echo 'ERROR: getStaffPermissionData ' . $e->getMessage();
      exit;
    }

  }

  return $result;
}

/**
 * array_unique_by_key for multidimensional array
 * @param  mix $array is array value
 * @param  string $key is name of array key
 * @return array or boolean
 */
function array_unique_by_key(&$array, $key) {
  $tmp = array();
  $result = array();
  foreach ($array as $value) {
      if (!in_array($value[$key], $tmp)) {
          array_push($tmp, $value[$key]);
          array_push($result, $value);
      }
  }
  return $array = $result;
}
/**
 * getMultilang
 * @param  string $lang
 * @author In khemarak
 * @return array or boolean
 */
function getMultilang($lang)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT key_lang, title FROM `multi_lang` WHERE lang = :lang ';
    $query = $connected->prepare($sql);
    $query->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $query->execute();
    $rows = $query->fetchAll();
    $newResult = array();
    //Loop: insert to newarray
    foreach ($rows as $key => $row) {
      $newResult[$row['key_lang']] = $row['title'];
    }
    return $newResult;

  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getMultilang'.$e->getMessage();
  }
  return $result;

}
/**
 * is_staff_function_exist
 * @param  string  $task
 * @param  string  $action
 * @return boolean
 * @author In khemarak
 */
function is_staff_function_exits($task, $action)
{
  global $debug, $connected;
  $result = true;
  try{
    $condition = $where = '';

    if(!empty($task)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' task_name = :task ';
    }
    if(!empty($action)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' action_name = :action ';
    }
    if(!empty($condition)) $where .=' WHERE '.$condition;

    $sql= ' SELECT COUNT(*) AS total_count FROM `staff_function` '.$where;
    $query = $connected->prepare($sql);
    if(!empty($task)) $query->bindValue(':task', (string)$task, PDO::PARAM_STR);
    if(!empty($action)) $query->bindValue(':action', (string)$action, PDO::PARAM_STR);
    $query->execute();
    $rows = $query->fetch();

    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: is_staff_function_exist'.$e->getMessage();
  }

  return $result;
}
/**
 * is_staff_permission_exist
 * @param  string  $table_name
 * @param  string  $role_id
 * @param  int  $function_id
 * @return boolean
 * @author In khemarak
 */
function is_staff_permission_exist($table_name, $role_id, $function_id)
{
  //these values are set in setup.php
  global $debug, $connected;
  $result = true;
  try
  {
    $sql = 'SELECT COUNT(*) as total FROM '.$table_name.' WHERE staff_role_id = :staff_role_id && staff_function_id = :staff_function_id';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':staff_role_id', (int)$role_id, PDO::PARAM_INT);
    $stmt->bindValue(':staff_function_id', (int)$function_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row['total'];
  } catch(PDOException $e) {
    $result = false;
    if ($debug)
    {
      echo 'ERROR:  is_staff_role_exist' . $e->getMessage();
      exit;
    }
  }
  return $result;
}
/**
 * is_staff_role_exist
 * @param  string  $table_name
 * @param  int $id is staff_role_id
 * @return boolean
 * @author In khemarak
 */
function is_staff_role_exist($id)
{
  //these values are set in setup.php
  global $debug, $connected;
  $result = true;
  try
  {
    $sql = 'SELECT COUNT(*) as total FROM staff WHERE staff_role_id = :id';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();

    $sql1 = 'SELECT COUNT(*) as total FROM staff_permission WHERE staff_role_id = :id';
    $stmt1 = $connected->prepare($sql1);
    $stmt1->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $stmt1->execute();
    $row1 = $stmt1->fetch();
    $totalCount = $row['total'] + $row1['total'];

    return $totalCount;
  } catch(PDOException $e) {
    $result = false;
    if ($debug)
    {
      echo 'ERROR:  is_staff_role_exist' . $e->getMessage();
      exit;
    }
  }

  return $result;
}
/**
 * is_staff_function_exist
 * @param  string  $table_name
 * @param  int $id
 * @return boolean
 * @author In khemarak
 */
function is_staff_function_exist($table_name, $id)
{
  //these values are set in setup.php
  global $debug, $connected;
  $result = true;
  try
  {
    $sql = 'SELECT COUNT(*) as total FROM '.$table_name.' WHERE staff_function_id = :id';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row['total'];
  } catch(PDOException $e) {
    $result = false;
    if ($debug)
    {
      echo 'ERROR:  is_staff_function_exist' . $e->getMessage();
      exit;
    }
  }
  return $result;
}
/**
 * is_lang_name_exist
 * @param  type  $lang_name
 * @return boolean
 * @author In khemarak
 */
function is_lang_name_exist($lang_name)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `language` WHERE lang_name = :lang_name ';
    $query = $connected->prepare($sql);
    $query->bindValue(':lang_name', (string)$lang_name, PDO::PARAM_STR);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: is_lang_name_exist'.$e->getMessage();
  }
  return $result;
}
/**
 * listCategory
 * @param  string $kwd keyword
 * @return array or boolean
 */
function listCategory($kwd, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';
    if(!empty($kwd))
    {
      $condition .= ' AND name LIKE :kwd ';
    }

    $sql = ' SELECT *, (SELECT COUNT(*) FROM `category` WHERE lang = :lang '.$condition.') AS total FROM `category` WHERE lang = :lang '.$condition.' ORDER BY id DESC LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);
    if (!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    $query->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listCategory'.$e->getMessage();
  }

  return $result;
}
/**
 * checkDeleteCategory
 * @param  int $id is category_id
 * @return array or boolean
 */
function checkDeleteCategory($id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count, c.name FROM `test` t INNER JOIN category c ON c.id = t.category_id WHERE t.category_id = :id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkDeleteCategory'.$e->getMessage();
  }
  return $result;
}
/**
 * check_psychologist_email
 * @param  string $email is email address
 * @return boolean
 */
function check_psychologist_email($email){
  global $debug, $connected, $total_data;
  $result = true;
  try{
    $sql = ' SELECT COUNT(*) AS total_count FROM `psychologist` WHERE email = :email ';
    $query = $connected->prepare($sql);
    $query->bindValue(':email', (string)$email, PDO::PARAM_STR);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];

  }catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: check_psychologist_email'.$e->getMessage();
  }

  return $result;
}
/**
 * check_patient_email
 * @param  string $email is email address
 * @return boolean
 */
function check_patient_email($email){
  global $debug, $connected, $total_data;
  $result = true;
  try{
    $sql = ' SELECT COUNT(*) AS total_count FROM `patient` WHERE email = :email ';
    $query = $connected->prepare($sql);
    $query->bindValue(':email', (string)$email, PDO::PARAM_STR);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];

  }catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: check_patient_email'.$e->getMessage();
  }

  return $result;
}
/**
 * getDataByUserRole
 * @param  int $user_role is check for select from table
 * @param  string $email
 * @return array or boolean
 */
function getDataByUserRole($user_role, $email){
  global $debug, $connected, $total_data;
  $result = true;
  try{
    if($user_role == 1){
      $sql = ' SELECT * FROM `patient` WHERE email = :email ';
    }else {
      $sql = ' SELECT * FROM `psychologist` WHERE email = :email ';
    }
    $query = $connected->prepare($sql);
    $query->bindValue(':email', (string)$email, PDO::PARAM_STR);
    $query->execute();
    return $query->fetch();

  }catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getDataByUserRole'.$e->getMessage();
  }

  return $result;
}
/**
 * checkSecretkeyPsychologist
 * @param  int $psy_id is psychologist_id
 * @param  string $secretkey
 * @return array or boolean
 */
function checkSecretkeyPsychologist($psy_id, $secretkey){
  global $debug, $connected, $total_data;
  $result = true;
  try{
    $sql = ' SELECT COUNT(*) AS total_count FROM `psychologist` WHERE id = :psy_id AND secretkey = :secretkey ';
    $query = $connected->prepare($sql);
    $query->bindValue(':secretkey', (string)$secretkey, PDO::PARAM_STR);
    $query->bindValue(':psy_id', $psy_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];

  }catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkSecretkeyPsychologist'.$e->getMessage();
  }

  return $result;
}
/**
 * checkSecretkeyPatient
 * @param  int $p_id
 * @param  string $secretkey
 * @return array or boolean
 */
function checkSecretkeyPatient($p_id, $secretkey){
  global $debug, $connected, $total_data;
  $result = true;
  try{
    $sql = ' SELECT COUNT(*) AS total_count FROM `patient` WHERE id = :p_id AND secretkey = :secretkey ';
    $query = $connected->prepare($sql);
    $query->bindValue(':secretkey', (string)$secretkey, PDO::PARAM_STR);
    $query->bindValue(':p_id', $p_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];

  }catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkSecretkeyPatient'.$e->getMessage();
  }

  return $result;
}
/**
 * listPatient
 * @param  string $kwd is keyword
 * @return array or boolean
 */
function listPatient($kwd, $psychologist_id, $gender, $status){
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = $where = '';
    if(!empty($kwd)) {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' username LIKE :kwd ';
    }
    if(!empty($psychologist_id)) {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' psychologist_id = :psychologist_id AND deleted_at IS NULL ';
    }
    if(!empty($gender)) {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' gender = :gender ';
    }
    if(!empty($status)) {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' status = :status ';
    }

    if(!empty($condition)) $where .= ' WHERE '.$condition;

    $sql = ' SELECT *, (SELECT COUNT(*) FROM `patient` '.$where.') AS total FROM `patient` '.$where.' ORDER BY id DESC LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);
    if (!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    if (!empty($gender)) $query->bindValue(':gender', (string)$gender, PDO::PARAM_STR);
    if (!empty($status)) $query->bindValue(':status', (int)$status, PDO::PARAM_INT);
    if (!empty($psychologist_id)) $query->bindValue(':psychologist_id', (int)$psychologist_id, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listPatient'.$e->getMessage();
  }

  return $result;
}
/**
 * getPatientByID
 * @param  int $id is patient_id
 * @param  int $psychologist_id is psychologist_id
 * @return array or boolean
 */
function getPatientByID($id, $psychologist_id){
  global $debug, $connected, $total_data;
  $result = true;
  try{
    $sql = ' SELECT * FROM `patient` WHERE id = :id AND psychologist_id = :psychologist_id AND deleted_at IS NULL ';
    $query = $connected->prepare($sql);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->bindValue(':psychologist_id', $psychologist_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch();

  }catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getPatientByID'.$e->getMessage();
  }

  return $result;
}
/**
 * listTopic
 * @param  string $kwd is keyword
 * @param  string $lang is language
 * @return array or boolean
 */
function listTopic($kwd, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';
    if(!empty($kwd)) $condition .= ' AND name LIKE :kwd ';

    $sql = ' SELECT *, (SELECT COUNT(*) FROM `topic` WHERE lang = :lang '.$condition.') AS total FROM `topic` WHERE lang = :lang '.$condition.' ORDER BY id DESC LIMIT :offset, :limit ';

    $query = $connected->prepare($sql);
    if (!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    $query->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listTopic'.$e->getMessage();
  }

  return $result;
}
/**
 * listTopicAnalysis
 * @param  string $kwd is keyword
 * @param  string $lang is language
 * @return array or boolean
 */
function listTopicAnalysis($kwd, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';
    if(!empty($kwd))
    {
      $condition .= ' AND name LIKE :kwd ';
    }

    $sql = ' SELECT *, (SELECT COUNT(*) FROM `topic_analysis` WHERE lang = :lang '.$condition.') AS total FROM `topic_analysis` WHERE lang = :lang '.$condition.' ORDER BY id DESC LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);
    if (!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    $query->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listTopicAnalysis'.$e->getMessage();
  }

  return $result;
}
/**
 * getResultAnswerTopic
 * @param  int $unique_id is unique_id
 * @param  int $test_id is test_id
 * @param  int $if_topic_id is if_topic_id
 * @return array or boolean
 */
function getResultAnswerTopic($unique_id, $test_id, $if_topic_id, $topic_id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    if(!empty($if_topic_id)) $where .= ' AND atp.topic_id = :if_topic_id ';
    if(!empty($topic_id)) $where .= ' AND atp.topic_id = :topic_id ';

    $sql =' SELECT rs.*, rsa.answer_id AS res_answer_id, atp.*, rst.view_order, t.name AS topic_title, tqth.if_topic_id,
              ROUND(SUM(IF(atp.assign_value = 0, atp.default_value, atp.assign_value * atp.weight_value)), 2) as amount
            FROM `response` rs
              INNER JOIN response_answer rsa ON rsa.response_id = rs.id
              INNER JOIN answer ans ON ans.id = rsa.answer_id AND ans.calculate = 0
              INNER JOIN answer_topic atp ON atp.answer_id = ans.id
              INNER JOIN topic t ON t.id = atp.topic_id
              LEFT JOIN (SELECT r.topic_id, r.view_order FROM result r WHERE r.test_id = :test_id GROUP BY r.topic_id) rst ON rst.topic_id = atp.topic_id
              LEFT JOIN test_question_topic_hide tqth ON tqth.test_id = rs.test_id AND tqth.topic_id = t.id
            WHERE rs.unique_id = :unique_id '.$where.' GROUP BY atp.topic_id ORDER BY rst.view_order ASC ';

    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':unique_id', (int)$unique_id, PDO::PARAM_INT);
    $stmt->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    if(!empty($topic_id)) $stmt->bindValue(':topic_id', (int)$topic_id, PDO::PARAM_INT);
    if(!empty($if_topic_id)) $stmt->bindValue(':if_topic_id', (int)$if_topic_id, PDO::PARAM_INT);
    $stmt->execute();
    if(!empty($if_topic_id) || !empty($topic_id)){
      $rows = $stmt->fetch();
    }else {
      $rows = $stmt->fetchAll();
    }
    return $rows;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getResultAnswerTopic'.$e->getMessage();
  }
  return $result;
}
/**
 * getListTestPsychologist
 * @param  int $psy_id is psychologist_id
 * @param  int $tid is test_id
 * @param  int $cid is category_id
 * @param  string $status
 * @return array or boolean
 */
function getListTestPsychologist($psy_id, $tid, $cid, $status)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = $where = '';

    if(!empty($psy_id)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' tps.psychologist_id = :psy_id ';
    }
    if(!empty($tid)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' tps.test_id = :testid ';
    }
    if(!empty($status)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' tps.status = :status ';
    }
    if(!empty($cid)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' t.category_id = :cat_id ';
    }

    if(!empty($condition)) $where .= ' WHERE '.$condition;

    $sql =' SELECT tps.*, psy.username, t.category_id, t.title, c.name AS catName,
              (SELECT COUNT(*) FROM `test_psychologist` tps INNER JOIN psychologist psy ON psy.id = tps.psychologist_id INNER JOIN test t ON t.id = tps.test_id INNER JOIN category c ON c.id = t.category_id '.$where.') AS total_count
            FROM `test_psychologist` tps
              INNER JOIN psychologist psy ON psy.id = tps.psychologist_id
              INNER JOIN test t ON t.id = tps.test_id
              INNER JOIN category c ON c.id = t.category_id '.$where.' ORDER BY tps.psychologist_id LIMIT :offset, :limit ';

    $stmt = $connected->prepare($sql);

    if(!empty($tid))    $stmt->bindValue(':testid', $tid, PDO::PARAM_INT);
    if(!empty($cid))    $stmt->bindValue(':cat_id', $cid, PDO::PARAM_INT);
    if(!empty($psy_id)) $stmt->bindValue(':psy_id', $psy_id, PDO::PARAM_INT);
    if(!empty($status)) $stmt->bindValue(':status', $status, PDO::PARAM_INT);

    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total_count'];
    return $rows;

  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestPsychologist'.$e->getMessage();
  }
  return $result;
}
/**
 * getListTestPatient
 * @param  int $pat_id is patient_id
 * @param  int $tid is test_id
 * @param  int $status
 * @param  int $tmpstus is test_tmp of status
 * @param  int $f_date is from date
 * @param  int $t_date is to date
 * @return array or bool
 */
function getListTestPatient($pat_id, $psy_id, $tid, $status, $tmpstus, $f_date, $t_date)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = $where = '';

    if(!empty($pat_id)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' tpt.patient_id = :pat_id ';
    }
    if(!empty($psy_id)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' pt.psychologist_id = :psy_id ';
    }
    if(!empty($tid)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' tpt.test_id = :testid ';
    }
    if(!empty($status)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' tpt.status = :status ';
    }
    if(!empty($tmpstus)){
      if(!empty($condition)) $condition .= ' AND ';
      //if eqaul 1 is pending
      if($tmpstus == 1) {
        $condition .= ' ttmp.status = :tmpstus ';
      }
      //if eqaul 2 is commpleted
      if($tmpstus == 2) {
        $condition .= ' ttmp.status = :tmpstus ';
      }
      //if eqaul 3 is new assign
      if($tmpstus == 3) {
        $condition .= ' ttmp.status IS NULL ';
      }
    }
    if(!empty($f_date) && empty($t_date)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' DATE_FORMAT(tpt.created_at , "%Y-%m-%d") >= :f_date ';
    }
    if(empty($f_date) && !empty($t_date)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' DATE_FORMAT(tpt.created_at , "%Y-%m-%d") <= :t_date ';
    }
    if(!empty($f_date) && !empty($t_date)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' DATE_FORMAT(tpt.created_at , "%Y-%m-%d") BETWEEN :f_date AND :t_date ';
    }


    if(!empty($condition)) $where .= ' WHERE '.$condition;

    $sql =' SELECT tpt.*, pt.username, t.category_id, t.title, t.description, c.name AS catName, ttmp.status AS test_tmp_status,
              (SELECT COUNT(*) FROM `test_patient` tpt INNER JOIN patient pt ON pt.id = tpt.patient_id INNER JOIN test t ON t.id = tpt.test_id
              INNER JOIN category c ON c.id = t.category_id LEFT JOIN test_tmp ttmp ON ttmp.test_patient_id = tpt.id '.$where.') AS total_count
            FROM `test_patient` tpt
              INNER JOIN patient pt ON pt.id = tpt.patient_id
              INNER JOIN test t ON t.id = tpt.test_id
              INNER JOIN category c ON c.id = t.category_id
              LEFT JOIN test_tmp ttmp ON ttmp.test_patient_id = tpt.id '.$where.' ORDER BY tpt.created_at DESC LIMIT :offset, :limit ';

    $stmt = $connected->prepare($sql);

    if(!empty($tid))    $stmt->bindValue(':testid', $tid, PDO::PARAM_INT);
    if(!empty($pat_id)) $stmt->bindValue(':pat_id', $pat_id, PDO::PARAM_INT);
    if(!empty($psy_id)) $stmt->bindValue(':psy_id', $psy_id, PDO::PARAM_INT);
    if(!empty($status)) $stmt->bindValue(':status', $status, PDO::PARAM_INT);
    if(!empty($f_date)) $stmt->bindValue(':f_date', $f_date, PDO::PARAM_STR);
    if(!empty($t_date)) $stmt->bindValue(':t_date', $t_date, PDO::PARAM_STR);
    if(!empty($tmpstus))
    {
      if($tmpstus == 1 || $tmpstus = 2) $stmt->bindValue(':tmpstus', $tmpstus, PDO::PARAM_INT);
    }

    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total_count'];
    return $rows;

  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestPatient'.$e->getMessage();
  }
  return $result;
}
/**
 * listTestGroupByTestId
 * @param  int $id is test_id
 * @return array or boolean
 */
function listTestGroupByTestId($id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $sql =' SELECT tg.*, t.title AS test_title, t.description, COUNT(tgq.id) AS count_tgroup_que
            FROM `test_group` tg
              INNER JOIN test t ON t.id = tg.test_id
              INNER JOIN test_group_question tgq ON tgq.test_group_id = tg.id
            WHERE tg.test_id = :id GROUP BY tg.id ORDER BY tg.title ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetchAll();
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listTestGroupByTestId'.$e->getMessage();
  }
  return $result;
}
/**
 * getTestQuestionBy
 * @param  int $tid is test_id
 * @param  int $tqid is test_question_id
 * @param  int $sub_id is sub_id
 * @param  string $lang is language
 * @return array or boolean
 */
function getTestQuestionByTestQuesID($tid, $tqid, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql= ' SELECT q.*, q.title AS que_title, tq.*, COUNT(asw.id) AS count_answer, tq.id AS test_question_id, ga.test_question_id AS g_ans_que_id, ga.flag, ga.sub_id, ga.id AS g_answer_id, ga.g_answer_title
            FROM test_question tq
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN answer asw ON asw.test_question_id = tq.id
              LEFT JOIN group_answer ga ON ga.test_id = tq.test_id AND tq.id = ga.test_question_id
            WHERE tq.test_id = :tid AND tq.id = :tqid AND q.lang = :lang  GROUP BY tq.id ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':tqid', (int)$tqid, PDO::PARAM_INT);
    $stmt->bindValue(':tid', (int)$tid, PDO::PARAM_INT);
    $stmt->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetch();

    return $rows;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getTestQuestionByTestQuesID'.$e->getMessage();
  }
  return $result;
}
/**
 * getTestQuestionBySubID
 * @param  int $tid is test_id
 * @param  int $sub_id
 * @param  string $lang is language
 * @return array or boolean
 */
function getTestQuestionBySubID($tid, $sub_id, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql= ' SELECT q.*, q.title AS que_title, tq.*, COUNT(asw.id) AS count_answer, tq.id AS test_question_id, ga.test_question_id AS g_ans_que_id, ga.flag, ga.sub_id, ga.id AS g_answer_id, ga.g_answer_title
            FROM test_question tq
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN answer asw ON asw.test_question_id = tq.id
              LEFT JOIN group_answer ga ON ga.test_id = tq.test_id AND tq.id = ga.test_question_id
            WHERE tq.test_id = :tid AND ga.sub_id = :sub_id AND  q.lang = :lang GROUP BY tq.id ORDER BY ga.flag DESC';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':sub_id', (int)$sub_id, PDO::PARAM_INT);
    $stmt->bindValue(':tid', (int)$tid, PDO::PARAM_INT);
    $stmt->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    foreach ($rows as $key => $va) {
      $dataanwser = listAnswerByTestQuesId($va['test_question_id']);

      $newResult[] = array('id'     => $va['id'],
                      'title'       => $va['title'],
                      'description' => $va['description'],
                      'type'        => $va['type'],
                      'is_email'    => $va['is_email'],
                      'hide_title'  => $va['hide_title'],
                      'lang'        => $va['lang'],
                      'que_title'   => $va['que_title'],
                      'test_id'     => $va['test_id'],
                      'question_id' => $va['question_id'],
                      'is_required' => $va['is_required'],
                      'view_order'  => $va['view_order'],
                      'jump_condition_answer' => $va['jump_condition_answer'],
                      'jump_condition_value'  => $va['jump_condition_value'],
                      'parent'      => $va['parent'],
                      'count_answer'      => $va['count_answer'],
                      'test_question_id'  => $va['test_question_id'],
                      'g_ans_que_id'      => $va['g_ans_que_id'],
                      'flag'           => $va['flag'],
                      'g_answer_title' => $va['g_answer_title'],
                      'answer'         => $dataanwser);
    }

    return $newResult;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getTestQuestionBySubID'.$e->getMessage();
  }
  return $result;
}
/**
 * listAnswerByTestQuesId
 * @param  int $test_questionid is test_question_id
 * @return array or boolean
 */
function listAnswerByTestQuesId($test_questionid)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $sql= ' SELECT ans.*, tq.view_order AS q_jump_to_view_order
              FROM answer ans
              LEFT JOIN test_question tq ON tq.id = ans.jump_to
              INNER JOIN answer_topic atp ON atp.answer_id = ans.id
            WHERE ans.test_question_id = :test_question_id GROUP BY ans.id ORDER BY ans.view_order ASC ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':test_question_id', (int)$test_questionid, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetchAll();
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listAnswerByTestQuesId'.$e->getMessage();
  }
  return $result;
}
/**
 * getTestQuestionViewOrder
 * @param  int $test_id
 * @return array or boolean
 */
function getTestQuestionViewOrder($test_id)
{
  global $debug, $connected;
  $result = true;

  try{
    $sql =' SELECT tq.id AS tqid, q.title AS que_title, q.description, ga.g_answer_title, tqvo.id AS tq_view_order_id
            FROM `test_question` tq
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN group_answer ga ON ga.test_id = :test_id AND ga.test_question_id = tq.id
              LEFT JOIN test_question_view_order tqvo ON tqvo.test_id = :test_id AND tqvo.test_question_id = tq.id
            WHERE tq.test_id = :test_id AND ga.test_question_id IS NULL AND ga.id IS NULL AND tqvo.id IS NULL ORDER BY tq.view_order ASC ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll();
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getTestQuestionViewOrder'.$e->getMessage();
  }

  return $result;
}
/**
 * ListTestQuestion
 * @param int $tid is test_id
 * @param int $lang is language
 * @param array or boolean
 */
function ListTestQuestion($tid, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql= ' SELECT * FROM `test_question_view_order` WHERE test_id = :tid ORDER BY view_order ASC ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':tid', (int)$tid, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $newdata = array();

    if(!empty($rows)){

      foreach ($rows as $key => $va) {
        $result1 = getTestQuestionByTestQuesID($tid, $va['test_question_id'], $lang);

        if($result1['flag'] == 1){
          $row2 = getTestQuestionBySubID($tid, $result1['g_answer_id'], $lang);

          $dataanwser = listAnswerByTestQuesId($result1['test_question_id']);

          $newdata[] = array('id'       => $result1['id'],
                          'title'       => $result1['title'],
                          'description' => $result1['description'],
                          'type'        => $result1['type'],
                          'is_email'    => $result1['is_email'],
                          'hide_title'  => $result1['hide_title'],
                          'lang'        => $result1['lang'],
                          'que_title'   => $result1['que_title'],
                          'test_id'     => $result1['test_id'],
                          'question_id' => $result1['question_id'],
                          'is_required' => $result1['is_required'],
                          'view_order'  => $result1['view_order'],
                          'jump_condition_answer' => $result1['jump_condition_answer'],
                          'jump_condition_value'  => $result1['jump_condition_value'],
                          'parent'      => $result1['parent'],
                          'count_answer'      => $result1['count_answer'],
                          'test_question_id'  => $result1['test_question_id'],
                          'g_ans_que_id'      => $result1['g_ans_que_id'],
                          'flag'        => $result1['flag'],
                          'g_answer_title' => $result1['g_answer_title'],
                          'answer'       => $dataanwser,
                          'group_answer' => $row2);
          if(!empty($dataanwser)) $total_data = COUNT($dataanwser);
        } else {
          $dataanwser = listAnswerByTestQuesId($result1['test_question_id']);

          $newdata[] = array('id'       => $result1['id'],
                          'title'       => $result1['title'],
                          'description' => $result1['description'],
                          'type'        => $result1['type'],
                          'is_email'    => $result1['is_email'],
                          'hide_title'  => $result1['hide_title'],
                          'lang'        => $result1['lang'],
                          'que_title'   => $result1['que_title'],
                          'test_id'     => $result1['test_id'],
                          'question_id' => $result1['question_id'],
                          'is_required' => $result1['is_required'],
                          'view_order'  => $result1['view_order'],
                          'jump_condition_answer' => $result1['jump_condition_answer'],
                          'jump_condition_value'  => $result1['jump_condition_value'],
                          'parent'      => $result1['parent'],
                          'count_answer'      => $result1['count_answer'],
                          'test_question_id'  => $result1['test_question_id'],
                          'g_ans_que_id'      => $result1['g_ans_que_id'],
                          'flag'        => 0,
                          'g_answer_title' => $result1['g_answer_title'],
                          'answer'      => $dataanwser,
                          'group_answer' => '');
          if(!empty($dataanwser)) $total_data = COUNT($dataanwser);
        }

      }//End fetch rows
    }
    //Get Test Question No in View Order
    $rTestQueNotViewOrder = getTestQuestionViewOrder($tid);

    if(!empty($rTestQueNotViewOrder)){

      foreach ($rTestQueNotViewOrder as $key => $va) {
        if(empty($va['tq_view_order_id'])){

          $result1 = getTestQuestionByTestQuesID($tid, $va['tqid'], $lang);

          if($result1['flag'] == 1){
            $row2 = getTestQuestionBySubID($tid, $result1['g_answer_id'], $lang);

            $dataanwser = listAnswerByTestQuesId($result1['test_question_id']);

            $newdata[] = array('id'       => $result1['id'],
                            'title'       => $result1['title'],
                            'description' => $result1['description'],
                            'type'        => $result1['type'],
                            'is_email'    => $result1['is_email'],
                            'hide_title'  => $result1['hide_title'],
                            'lang'        => $result1['lang'],
                            'que_title'   => $result1['que_title'],
                            'test_id'     => $result1['test_id'],
                            'question_id' => $result1['question_id'],
                            'is_required' => $result1['is_required'],
                            'view_order'  => $result1['view_order'],
                            'jump_condition_answer' => $result1['jump_condition_answer'],
                            'jump_condition_value'  => $result1['jump_condition_value'],
                            'parent'      => $result1['parent'],
                            'count_answer'      => $result1['count_answer'],
                            'test_question_id'  => $result1['test_question_id'],
                            'g_ans_que_id'      => $result1['g_ans_que_id'],
                            'flag'        => $result1['flag'],
                            'g_answer_title' => $result1['g_answer_title'],
                            'answer'       => $dataanwser,
                            'group_answer' => $row2);
            if(!empty($dataanwser)) $total_data = COUNT($dataanwser);
          }else {
            $dataanwser = listAnswerByTestQuesId($result1['test_question_id']);

            $newdata[] = array('id'       => $result1['id'],
                            'title'       => $result1['title'],
                            'description' => $result1['description'],
                            'type'        => $result1['type'],
                            'is_email'    => $result1['is_email'],
                            'hide_title'  => $result1['hide_title'],
                            'lang'        => $result1['lang'],
                            'que_title'   => $result1['que_title'],
                            'test_id'     => $result1['test_id'],
                            'question_id' => $result1['question_id'],
                            'is_required' => $result1['is_required'],
                            'view_order'  => $result1['view_order'],
                            'jump_condition_answer' => $result1['jump_condition_answer'],
                            'jump_condition_value'  => $result1['jump_condition_value'],
                            'parent'      => $result1['parent'],
                            'count_answer'      => $result1['count_answer'],
                            'test_question_id'  => $result1['test_question_id'],
                            'g_ans_que_id'      => $result1['g_ans_que_id'],
                            'flag'        => 0,
                            'g_answer_title' => $result1['g_answer_title'],
                            'answer'      => $dataanwser,
                            'group_answer' => '');
            if(!empty($dataanwser)) $total_data = COUNT($dataanwser);
          }
        }
      }//End fetch rows
    }

    // print_r($newdata);
    return $newdata;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: ListTestQuestion'.$e->getMessage();
  }
  return $result;
}
/**
 * istTestGroupQuestion at index
 * @param  int $id is test_group_id
 * @param  string $lang language
 * @return array or b
 */
function listTestGroupQuestion($id, $tid, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $sql= ' SELECT tgq.*, tqvo.view_order FROM `test_group_question` tgq
              LEFT JOIN test_question_view_order tqvo ON tqvo.test_question_id = tgq.test_question_id
            WHERE tgq.test_group_id = :id ORDER BY ISNULL(tqvo.view_order) ASC, tqvo.view_order ASC ';

    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $newdata = array();

    if(!empty($rows)){
      foreach ($rows as $key => $va) {
        $result1 = getTestQuestionByTestQuesID($tid, $va['test_question_id'], $lang);

        if($result1['flag'] == 1){
          $row2 = getTestQuestionBySubID($tid, $result1['g_answer_id'], $lang);

          $dataanwser = listAnswerByTestQuesId($result1['test_question_id']);

          $newdata[] = array('id'       => $result1['id'],
                          'title'       => $result1['title'],
                          'description' => $result1['description'],
                          'type'        => $result1['type'],
                          'is_email'    => $result1['is_email'],
                          'hide_title'  => $result1['hide_title'],
                          'lang'        => $result1['lang'],
                          'que_title'   => $result1['que_title'],
                          'test_id'     => $result1['test_id'],
                          'question_id' => $result1['question_id'],
                          'is_required' => $result1['is_required'],
                          'view_order'  => $result1['view_order'],
                          'jump_condition_answer' => $result1['jump_condition_answer'],
                          'jump_condition_value'  => $result1['jump_condition_value'],
                          'parent'      => $result1['parent'],
                          'count_answer'      => $result1['count_answer'],
                          'test_question_id'  => $result1['test_question_id'],
                          'g_ans_que_id'      => $result1['g_ans_que_id'],
                          'flag'        => $result1['flag'],
                          'g_answer_title' => $result1['g_answer_title'],
                          'answer'       => $dataanwser,
                          'group_answer' => $row2);
          if(!empty($dataanwser)) $total_data = COUNT($dataanwser);
        }else {
          $dataanwser = listAnswerByTestQuesId($result1['test_question_id']);

          $newdata[] = array('id'       => $result1['id'],
                          'title'       => $result1['title'],
                          'description' => $result1['description'],
                          'type'        => $result1['type'],
                          'is_email'    => $result1['is_email'],
                          'hide_title'  => $result1['hide_title'],
                          'lang'        => $result1['lang'],
                          'que_title'   => $result1['que_title'],
                          'test_id'     => $result1['test_id'],
                          'question_id' => $result1['question_id'],
                          'is_required' => $result1['is_required'],
                          'view_order'  => $result1['view_order'],
                          'jump_condition_answer' => $result1['jump_condition_answer'],
                          'jump_condition_value'  => $result1['jump_condition_value'],
                          'parent'      => $result1['parent'],
                          'count_answer'      => $result1['count_answer'],
                          'test_question_id'  => $result1['test_question_id'],
                          'g_ans_que_id'      => $result1['g_ans_que_id'],
                          'flag'        => 0,
                          'g_answer_title' => $result1['g_answer_title'],
                          'answer'      => $dataanwser,
                          'group_answer' => '');
          if(!empty($dataanwser)) $total_data = COUNT($dataanwser);
        }

      }//End fetch rows
    }

    return $newdata;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listTestGroupQuestion'.$e->getMessage();
  }
  return $result;
}
/**
 * getTestGroupById
 * @param  int $id is test group id
 * @return array or boolean
 */
function getTestGroupById($id, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql =' SELECT tg.*, t.title AS test_title, t.description, t.link_title1, t.link_title2, t.link1, t.link2 FROM `test_group` tg
              INNER JOIN test t ON t.id = tg.test_id
            WHERE tg.id = :id AND t.lang = :lang';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $stmt->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getTestGroupById'.$e->getMessage();
  }
  return $result;
}

function getCheckTestPatientByPsyChologist($psy_id, $pat_id, $test_id, $tpat_id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql =' SELECT tp.* FROM `patient` p INNER JOIN test_patient tp ON tp.patient_id = p.id WHERE p.psychologist_id = :psychologist_id AND p.id = :patient_id AND tp.test_id = :test_id AND tp.id = :test_patient_id ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':psychologist_id', (int)$psy_id, PDO::PARAM_INT);
    $stmt->bindValue(':patient_id', (int)$pat_id, PDO::PARAM_INT);
    $stmt->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $stmt->bindValue(':test_patient_id', (int)$tpat_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();

    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getCheckTestPatientByPsyChologist'.$e->getMessage();
  }
  return $result;
}

function getTestTmpQuestion($tpat_id, $test_id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql =' SELECT ttq.*, q.type, q.is_email, tq.id AS tqid, a.jump_to FROM `test_tmp` ttmp
              INNER JOIN test_tmp_question ttq ON ttq.test_tmp_id = ttmp.id
              INNER JOIN test_question tq ON tq.id = ttq.test_question_id
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN answer a ON a.id = ttq.answer_id
            WHERE ttmp.test_patient_id = :test_patient_id AND ttmp.test_id = :test_id ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $stmt->bindValue(':test_patient_id', (int)$tpat_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    return $rows;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getTestTmpQuestion'.$e->getMessage();
  }
  return $result;
}

function getListQuestionByViewOrderGroupNonGroupJumpTo($tid, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql= ' SELECT * FROM `test_question_view_order` WHERE test_id = :tid ORDER BY view_order ASC ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':tid', (int)$tid, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $newdata = array();

    if(!empty($rows)){
      foreach ($rows as $key => $va) {
        $result1 = getTestQuestionByTestQuesID($tid, $va['test_question_id'], $lang);

        if($result1['flag'] == 1){
          $row2 = getTestQuestionBySubID($tid, $result1['g_answer_id'], $lang);

          foreach ($row2 as $k => $va) {
            $resultJumpto = getJumpToTestQuestionById($va['test_question_id']);

            $newdata[] = array('id'       => $va['id'],
                            'title'       => $va['title'],
                            'description' => $va['description'],
                            'type'        => $va['type'],
                            'view_order'  => $va['view_order'],
                            'test_question_id'  => $va['test_question_id'],
                            'jump_to'     => $resultJumpto);
          }//End foreach

        } else {
          $resultJumpto = getJumpToTestQuestionById($result1['test_question_id']);
          $newdata[] = array('id'       => $result1['id'],
                          'title'       => $result1['title'],
                          'description' => $result1['description'],
                          'type'        => $result1['type'],
                          'view_order'  => $result1['view_order'],
                          'test_question_id'  => $result1['test_question_id'],
                          'jump_to'     => $resultJumpto);
        }


      }//End fetch rows
    }
    //Get Test Question No in View Order
    $rTestQueNotViewOrder = getTestQuestionViewOrder($tid);

    if(!empty($rTestQueNotViewOrder)){
      foreach ($rTestQueNotViewOrder as $key => $va) {
        if(empty($va['tq_view_order_id'])){
          $result1 = getTestQuestionByTestQuesID($tid, $va['tqid'], $lang);
          $resultJumpto = getJumpToTestQuestionById($va['tqid']);
          $newdata[] = array('id'       => $result1['id'],
                          'title'       => $result1['title'],
                          'description' => $result1['description'],
                          'type'        => $result1['type'],
                          'view_order'  => $result1['view_order'],
                          'test_question_id'  => $result1['test_question_id'],
                          'jump_to'     => $resultJumpto);
        }
      }//End fetch rows
    }
    $newResultRetrun = array();

    foreach ($newdata as $key => $value)
    {
      if(!empty($value['jump_to']))
      {
        foreach ($value['jump_to'] as $k => $va) {
          $newResultRetrun['jump_to'][] = array('key' => $key, 'jump_to' => $va['jump_to'], 'test_question_id' => $value['test_question_id']);
        }
      }
      $newResultRetrun['question'][] =array('id'          => $value['id'],
                                'title'       => $value['title'],
                                'description' => $value['description'],
                                'type'        => $value['type'],
                                'view_order'  => $value['view_order'],
                                'test_question_id'  => $value['test_question_id']);
    }

    // print_r($newdata);
    return $newResultRetrun;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListQuestionByViewOrderGroupNonGroup'.$e->getMessage();
  }
  return $result;

}

function getJumpToTestQuestionById($tqid)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT * FROM `answer` WHERE test_question_id = :tqid AND jump_to IS NOT NULL  ';
    $query = $connected->prepare($sql);
    $query->bindValue(':tqid', (int)$tqid, PDO::PARAM_INT);
    $query->execute();

    return $query->fetchAll();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getJumpToTestQuestionById'.$e->getMessage();
  }

  return $result;
}

function getListTestGroupByTmpQuestion($test_id, $status)
{
  global $debug, $connected;
  $result = true;
  try{
    if($status === 1)
    {
      $condition .= ' AND (ttmp.id IS NULL OR ttmp.status = 1) ';
      $orderBy .= ' ORDER BY tg.view_order ASC LIMIT 1 ';
    }
    if($status === 2)
    {
      $condition .= ' AND ttmp.status = 2 ';
      $orderBy .= ' ORDER BY tg.view_order DESC LIMIT 1 ';
    }

    $sql= ' SELECT tg.* FROM `test_group` tg LEFT JOIN test_tmp_question ttmp ON ttmp.test_group_id = tg.id WHERE tg.test_id = :test_id '.$condition.' GROUP BY tg.id '.$orderBy;
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->execute();

    if($status === 1) {
      return $query->fetchAll();
    } else {
      return $query->fetch();
    }
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestGroupByTmpQuestion'.$e->getMessage();
  }

  return $result;
}


?>
