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
      $condition .= ' tpsy.psychologist_id = :psy_id ';
    }
    if(!empty($tid)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' tpsy.test_id = :testid ';
    }
    if(!empty($status)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' tpsy.status = :status ';
    }
    if(!empty($cid)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' t.category_id = :cat_id ';
    }

    if(!empty($condition)) $where .= ' WHERE '.$condition;
 // ttmp.status AS test_tmp_status,
    $sql =' SELECT tpsy.*, psy.username, t.category_id, t.title, t.description, c.name AS catName, ttmp.status AS test_tmp_status,
              (SELECT COUNT(*) FROM `test_psychologist` tpsy INNER JOIN psychologist psy ON psy.id = tpsy.psychologist_id INNER JOIN test t ON t.id = tpsy.test_id INNER JOIN category c ON c.id = t.category_id '.$where.') AS total_count
            FROM `test_psychologist` tpsy
              INNER JOIN psychologist psy ON psy.id = tpsy.psychologist_id
              INNER JOIN test t ON t.id = tpsy.test_id
              LEFT JOIN test_tmp ttmp ON ttmp.test_psychologist_id = tpsy.id
              INNER JOIN category c ON c.id = t.category_id'.$where.' ORDER BY tpsy.psychologist_id LIMIT :offset, :limit ';
  // LEFT JOIN test_tmp ttmp ON ttmp.test_psychologist_id = tpsy.id
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
 * @param  string $lang is language
 * @return array or bool
 */
function getListTestPatient($pat_id, $psy_id, $tid, $status, $tmpstus, $f_date, $t_date, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = $where = '';

    if(!empty($pat_id)) {
      $condition .= ' AND tpt.patient_id = :pat_id ';
    }
    if(!empty($psy_id)) {
      $condition .= ' AND pt.psychologist_id = :psy_id ';
    }
    if(!empty($tid)) {
      $condition .= ' AND tpt.test_id = :testid ';
    }
    if(!empty($status)) {
      $condition .= ' AND tpt.status = :status ';
    }
    if(!empty($tmpstus)) {
      //if eqaul 1 is pending
      if($tmpstus == 1) {
        $condition .= ' AND ttmp.status = :tmpstus ';
      }
      //if eqaul 2 is commpleted
      if($tmpstus == 2) {
        $condition .= ' AND ttmp.status = :tmpstus ';
      }
      //if eqaul 3 is new assign
      if($tmpstus == 3) {
        $condition .= ' AND ttmp.status IS NULL ';
      }
    }
    if(!empty($f_date) && empty($t_date)) {
      $condition .= ' AND DATE_FORMAT(tpt.created_at , "%Y-%m-%d") >= :f_date ';
    }
    if(empty($f_date) && !empty($t_date)) {
      $condition .= ' AND DATE_FORMAT(tpt.created_at , "%Y-%m-%d") <= :t_date ';
    }
    if(!empty($f_date) && !empty($t_date)) {
      $condition .= ' AND DATE_FORMAT(tpt.created_at , "%Y-%m-%d") BETWEEN :f_date AND :t_date ';
    }

    $sql =' SELECT tpt.*, pt.username, t.category_id, t.title, t.description, c.name AS catName, ttmp.status AS test_tmp_status,
              (SELECT COUNT(*) FROM `test_patient` tpt INNER JOIN patient pt ON pt.id = tpt.patient_id INNER JOIN test t ON t.id = tpt.test_id
              INNER JOIN category c ON c.id = t.category_id LEFT JOIN test_tmp ttmp ON ttmp.test_patient_id = tpt.id WHERE t.lang = :lang '.$condition.') AS total_count
            FROM `test_patient` tpt
              INNER JOIN patient pt ON pt.id = tpt.patient_id
              INNER JOIN test t ON t.id = tpt.test_id
              INNER JOIN category c ON c.id = t.category_id
              LEFT JOIN test_tmp ttmp ON ttmp.test_patient_id = tpt.id WHERE t.lang = :lang '.$condition.' ORDER BY tpt.created_at DESC LIMIT :offset, :limit ';

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
    $stmt->bindValue(':lang', $lang, PDO::PARAM_STR);
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
 * getListTestPsychologist
 * @param  int $pat_id is patient_id
 * @param  int $tid is test_id
 * @param  int $status
 * @param  int $tmpstus is test_tmp of status
 * @param  int $f_date is from date
 * @param  int $t_date is to date
 * @param  string $lang is language
 * @return array or bool
 */
// function getListTestPsychologist($pat_id, $psy_id, $tid, $status, $tmpstus, $f_date, $t_date, $lang)
// {
//   global $debug, $connected, $limit, $offset, $total_data;
//   $result = true;
//   try{
//     $condition = $where = '';
//
//     if(!empty($pat_id)) {
//       $condition .= ' AND tpt.patient_id = :pat_id ';
//     }
//     if(!empty($psy_id)) {
//       $condition .= ' AND pt.psychologist_id = :psy_id ';
//     }
//     if(!empty($tid)) {
//       $condition .= ' AND tpt.test_id = :testid ';
//     }
//     if(!empty($status)) {
//       $condition .= ' AND tpt.status = :status ';
//     }
//     if(!empty($tmpstus)) {
//       //if eqaul 1 is pending
//       if($tmpstus == 1) {
//         $condition .= ' AND ttmp.status = :tmpstus ';
//       }
//       //if eqaul 2 is commpleted
//       if($tmpstus == 2) {
//         $condition .= ' AND ttmp.status = :tmpstus ';
//       }
//       //if eqaul 3 is new assign
//       if($tmpstus == 3) {
//         $condition .= ' AND ttmp.status IS NULL ';
//       }
//     }
//     if(!empty($f_date) && empty($t_date)) {
//       $condition .= ' AND DATE_FORMAT(tpt.created_at , "%Y-%m-%d") >= :f_date ';
//     }
//     if(empty($f_date) && !empty($t_date)) {
//       $condition .= ' AND DATE_FORMAT(tpt.created_at , "%Y-%m-%d") <= :t_date ';
//     }
//     if(!empty($f_date) && !empty($t_date)) {
//       $condition .= ' AND DATE_FORMAT(tpt.created_at , "%Y-%m-%d") BETWEEN :f_date AND :t_date ';
//     }
//
//     $sql =' SELECT tpsy.*, psy.username, t.category_id, t.title, t.descripsyion, c.name AS catName, ttmp.status AS test_tmp_status,
//               (SELECT COUNT(*) FROM `test_psychologist` tpsy INNER JOIN psychologist psy ON psy.id = tpsy.psychologist_id INNER JOIN test t ON t.id = tpsy.test_id
//               INNER JOIN category c ON c.id = t.category_id LEFT JOIN test_tmp ttmp ON ttmp.test_psychologist_id = tpsy.id WHERE t.lang = :lang '.$condition.') AS total_count
//             FROM `test_patient` tpsy
//               INNER JOIN psychologist psy ON psy.id = tpsy.psychologist_id
//               INNER JOIN test t ON t.id = tpsy.test_id
//               INNER JOIN category c ON c.id = t.category_id
//               LEFT JOIN test_tmp ttmp ON ttmp.test_psychologist_id = tpsy.id WHERE t.lang = :lang '.$condition.' ORDER BY tpsy.created_at DESC LIMIT :offset, :limit ';
//
//     $stmt = $connected->prepare($sql);
//
//     if(!empty($tid))    $stmt->bindValue(':testid', $tid, PDO::PARAM_INT);
//     if(!empty($pat_id)) $stmt->bindValue(':pat_id', $pat_id, PDO::PARAM_INT);
//     if(!empty($psy_id)) $stmt->bindValue(':psy_id', $psy_id, PDO::PARAM_INT);
//     if(!empty($status)) $stmt->bindValue(':status', $status, PDO::PARAM_INT);
//     if(!empty($f_date)) $stmt->bindValue(':f_date', $f_date, PDO::PARAM_STR);
//     if(!empty($t_date)) $stmt->bindValue(':t_date', $t_date, PDO::PARAM_STR);
//     if(!empty($tmpstus))
//     {
//       if($tmpstus == 1 || $tmpstus = 2) $stmt->bindValue(':tmpstus', $tmpstus, PDO::PARAM_INT);
//     }
//     $stmt->bindValue(':lang', $lang, PDO::PARAM_STR);
//     $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//     $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//     $stmt->execute();
//     $rows = $stmt->fetchAll();
//     if (count($rows) > 0) $total_data = $rows[0]['total_count'];
//     return $rows;
//
//   } catch (Exception $e) {
//     $result = false;
//     if($debug)  echo 'Errors: getListTestPatient'.$e->getMessage();
//   }
//   return $result;
// }

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
    $sql =' SELECT tq.id AS tqid, q.title AS que_title, q.description, ga.g_answer_title, tqvo.id AS tq_view_order_id, tgq.id AS test_ques_group
            FROM `test_question` tq
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN group_answer ga ON ga.test_id = :test_id AND ga.test_question_id = tq.id
              LEFT JOIN test_question_view_order tqvo ON tqvo.test_id = :test_id AND tqvo.test_question_id = tq.id
              LEFT JOIN test_group_question tgq ON tgq.test_question_id = tq.id
            WHERE tq.test_id = :test_id AND ga.test_question_id IS NULL OR ga.flag = 1 ORDER BY (tqvo.view_order IS NULL), tqvo.view_order ASC ';
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

    if(!empty($rows)) {
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

    if(!empty($rTestQueNotViewOrder)) {

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
/**
 * getCheckTestPatientByPsyChologist
 * @param  int $psy_id is psychologist_id
 * @param  int $pat_id is patient_id
 * @param  int $test_id is test_id
 * @param  int $tpat_id is test_patient_id
 * @return array or boolean
 */
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

/**
 * getCheckTestPatientByPsyChologist
 * @param  int $psy_id is psychologist_id
 * @param  int $psy_id is psychologist_id
 * @param  int $test_id is test_id
 * @param  int $tpsy_id is test_psychologist_id
 * @return array or boolean
 */
function getCheckTestPsyChologistByPsyChologist($psy_id, $test_id, $tpsy_id)
{
  echo "Psychologist id: ".$psy_id."Test id : ".$test_id."Test Psychologist id : ". $tpsy_id;
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql =' SELECT tpsy.* FROM `psychologist` psy INNER JOIN test_psychologist tpsy ON tpsy.psychologist_id = psy.id WHERE psy.id = :psychologist_id AND tpsy.test_id = :test_id AND tpsy.id = :test_psychologist_id ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':psychologist_id', (int)$psy_id, PDO::PARAM_INT);
    $stmt->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $stmt->bindValue(':test_psychologist_id', (int)$tpsy_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();

    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getCheckTestPsyChologistByPsyChologist'.$e->getMessage();
  }
  return $result;
}

/**
 * getCheckTestPsyChologist
 * @param  int $psy_id is psychologist_id
 * @param  int $pat_id is patient_id
 * @param  int $test_id is test_id
 * @param  int $tpat_id is test_patient_id
 * @return array or boolean
 */
 function getCheckTestByPsyChologist($psy_id, $test_id, $tpsy_id)
 {
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql =' SELECT tpsy.* FROM `psychologist` psy INNER JOIN test_psychologist tpsy ON tpsy.psychologist_id = psy.id WHERE psy.id = :psychologist_id AND tpsy.test_id = :test_id AND tpsy.id = :test_psychologist_id ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':psychologist_id', (int)$psy_id, PDO::PARAM_INT);
    $stmt->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $stmt->bindValue(':test_psychologist_id', (int)$tpsy_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();

    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getCheckTestPatientByPsyChologist'.$e->getMessage();
  }
  return $result;
 }

/**
 * getTestTmpQuestion
 * @param  int $tpat_id is test_patient_id
 * @param  int $test_id
 * @return array or boolean
 */
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
/**
 * getTestTmpQuestionPsy
 * @param  int $tpat_id is test_psychologist_id
 * @param  int $test_id
 * @return array or boolean
 */
function getTestTmpQuestionPsy($tpsy_id, $test_id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql =' SELECT ttq.*, q.type, q.is_email, tq.id AS tqid, a.jump_to FROM `test_tmp` ttmp
              INNER JOIN test_tmp_question ttq ON ttq.test_tmp_id = ttmp.id
              INNER JOIN test_question tq ON tq.id = ttq.test_question_id
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN answer a ON a.id = ttq.answer_id
            WHERE ttmp.test_psychologist_id = :test_psychologist_id AND ttmp.test_id = :test_id ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $stmt->bindValue(':test_psychologist_id', (int)$tpsy_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    return $rows;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getTestTmpQuestion'.$e->getMessage();
  }
  return $result;
}
/**
 * getListQuestionByViewOrderGroupNonGroupJumpTo
 * @param  int $tid is test_id
 * @param  string $lang is language
 * @return array or boolean
 */
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

    return $newResultRetrun;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListQuestionByViewOrderGroupNonGroup'.$e->getMessage();
  }
  return $result;

}
/**
 * getJumpToTestQuestionById
 * @param  int $tqid is test_question_id
 * @return array or boolean
 */
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
/**
 * getListTestGroupByTmpQuestion
 * @param  int $test_id
 * @param  int $tpsy_id is test_psychologist_id
 * @param  int $status
 * @param  string $fetch_type for get data "one or all"
 * @param  int $slimit for setLimit
 * @return array or boolean
 */
function getListTestGroupByTmpQuestionPsy($test_id, $tpsy_id, $status, $fetch_type, $slimit)
{
  global $debug, $connected, $offset, $limit;
  $result = true;
  try{
    $sql1= ' SELECT * FROM `test_tmp` WHERE test_id = :test_id AND test_psychologist_id = :tpsy_id ';
    $query1 = $connected->prepare($sql1);
    $query1->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query1->bindValue(':tpsy_id', (int)$tpsy_id, PDO::PARAM_INT);
    $query1->execute();
    $row = $query1->fetch();

    if(!empty($row['id']))
    {
      $condition .= ' AND tmp.id = :tmp_id ';
    }

    if($status === 1)
    {
      $condition .= ' AND (ttmq.id IS NULL OR ttmq.status = 1) ';
      $orderBy .= ' ORDER BY tg.view_order ASC ';
    }
    if($status === 2 && !empty($row['id']))
    {
      $condition .= ' AND ttmq.status = 2 ';
      $orderBy .= ' ORDER BY tg.view_order DESC ';
    }
    if($status === 2 && empty($row['id']))
    {
      $condition .= ' AND tmp.id IS NULL ';
    }
    if(!empty($slimit))
    {
      $setLimit .= ' LIMIT 1';
    }


    $sql= ' SELECT tg.* FROM test_group tg
              LEFT JOIN test_tmp tmp ON tmp.test_id = tg.test_id
              LEFT JOIN test_tmp_question ttmq ON ttmq.test_group_id = tg.id AND ttmq.test_tmp_id = tmp.id
            WHERE tg.test_id = :test_id '.$condition.' GROUP BY tg.id '.$orderBy.$setLimit;

    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    if(!empty($row['id'])) $query->bindValue(':tmp_id', (int)$row['id'], PDO::PARAM_INT);
    $query->execute();

    if($fetch_type === 'one')
    {
      return $query->fetch();
    } else {
      return $query->fetchAll();
    }
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestGroupByTmpQuestion'.$e->getMessage();
  }

  return $result;
}
/**
 * getListTestGroupByTmpQuestion
 * @param  int $test_id
 * @param  int $tpid is test_patient_id
 * @param  int $status
 * @param  string $fetch_type for get data "one or all"
 * @param  int $slimit for setLimit
 * @return array or boolean
 */
function getListTestGroupByTmpQuestion($test_id, $tpid, $status, $fetch_type, $slimit)
{
  global $debug, $connected, $offset, $limit;
  $result = true;
  try{
    $sql1= ' SELECT * FROM `test_tmp` WHERE test_id = :test_id AND test_patient_id = :tpid ';
    $query1 = $connected->prepare($sql1);
    $query1->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query1->bindValue(':tpid', (int)$tpid, PDO::PARAM_INT);
    $query1->execute();
    $row = $query1->fetch();

    if(!empty($row['id']))
    {
      $condition .= ' AND tmp.id = :tmp_id ';
    }

    if($status === 1)
    {
      $condition .= ' AND (ttmq.id IS NULL OR ttmq.status = 1) ';
      $orderBy .= ' ORDER BY tg.view_order ASC ';
    }
    if($status === 2 && !empty($row['id']))
    {
      $condition .= ' AND ttmq.status = 2 ';
      $orderBy .= ' ORDER BY tg.view_order DESC ';
    }
    if($status === 2 && empty($row['id']))
    {
      $condition .= ' AND tmp.id IS NULL ';
    }
    if(!empty($slimit))
    {
      $setLimit .= ' LIMIT 1';
    }


    $sql= ' SELECT tg.* FROM test_group tg
              LEFT JOIN test_tmp tmp ON tmp.test_id = tg.test_id
              LEFT JOIN test_tmp_question ttmq ON ttmq.test_group_id = tg.id AND ttmq.test_tmp_id = tmp.id
            WHERE tg.test_id = :test_id '.$condition.' GROUP BY tg.id '.$orderBy.$setLimit;

    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    if(!empty($row['id'])) $query->bindValue(':tmp_id', (int)$row['id'], PDO::PARAM_INT);
    $query->execute();

    if($fetch_type === 'one')
    {
      return $query->fetch();
    } else {
      return $query->fetchAll();
    }
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestGroupByTmpQuestion'.$e->getMessage();
  }

  return $result;
}
/**
 * getResponseAnswerByTestPatient
 * @param  int $test_id
 * @param  int $tpid is test_patient_id
 * @return array or boolean
 */
function getResponseAnswerByTestPatient($test_id, $tpid)
{
  global $debug, $connected, $offset, $limit;
  $result = true;
  try{
    $sql =' SELECT ra.content, ra.answer_id, ans.jump_to, ans.title AS ans_title, q.type, q.title AS que_title, q.description, q.hide_title, ra.test_question_id AS tqid, tq.is_required
            FROM `response` r
              INNER JOIN response_answer ra ON ra.response_id = r.id
              INNER JOIN test_question tq ON tq.id = ra.test_question_id
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN answer ans ON ans.id = ra.answer_id
            WHERE r.test_id = :test_id AND r.test_patient_id = :tpid GROUP BY ra.test_question_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':tpid', (int)$tpid, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();

    $newResult = array();
    foreach ($rows as $k => $va) {
      if($va['type'] == 4)
      {
        $sql1 =' SELECT ans.title AS ans_title
                FROM `response` r
                  INNER JOIN response_answer ra ON ra.response_id = r.id
                  INNER JOIN test_question tq ON tq.id = ra.test_question_id
                  INNER JOIN question q ON q.id = tq.question_id
                  LEFT JOIN answer ans ON ans.id = ra.answer_id
                WHERE r.test_id = :test_id AND r.test_patient_id = :tpid AND ra.test_question_id = :tqid ';
        $query1 = $connected->prepare($sql1);
        $query1->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
        $query1->bindValue(':tpid', (int)$tpid, PDO::PARAM_INT);
        $query1->bindValue(':tqid', (int)$va['tqid'], PDO::PARAM_INT);
        $query1->execute();
        $rows1 = $query1->fetchAll();
      }

      $newResult[] = array('content'      => $va['content'],
                           'answer_id'    => $va['answer_id'],
                           'jump_to'      => $va['jump_to'],
                           'ans_title'    => $va['ans_title'],
                           'type'         => $va['type'],
                           'que_title'    => $va['que_title'],
                           'description'  => $va['description'],
                           'hide_title'   => $va['hide_title'],
                           'tqid'         => $va['tqid'],
                           'is_required'  => $va['is_required'],
                           'result_answer' => $rows1);
    }

    return $newResult;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getResponseAnswerByTestPatient'.$e->getMessage();
  }

  return $result;
}
/**
 * getResponseAnswerByTestPatient
 * @param  int $test_id
 * @param  int $tpsy_id is test_psychologist_id
 * @return array or boolean
 */
function getResponseAnswerByTestPsychologist($test_id, $tpsy_id)
{
  global $debug, $connected, $offset, $limit;
  $result = true;
  try{
    $sql =' SELECT ra.content, ra.answer_id, ans.jump_to, ans.title AS ans_title, q.type, q.title AS que_title, q.description, q.hide_title, ra.test_question_id AS tqid, tq.is_required
            FROM `response` r
              INNER JOIN response_answer ra ON ra.response_id = r.id
              INNER JOIN test_question tq ON tq.id = ra.test_question_id
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN answer ans ON ans.id = ra.answer_id
            WHERE r.test_id = :test_id AND r.test_psychologist_id = :tpsy_id GROUP BY ra.test_question_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':tpsy_id', (int)$tpsy_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();

    $newResult = array();
    foreach ($rows as $k => $va) {
      if($va['type'] == 4)
      {
        $sql1 =' SELECT ans.title AS ans_title
                FROM `response` r
                  INNER JOIN response_answer ra ON ra.response_id = r.id
                  INNER JOIN test_question tq ON tq.id = ra.test_question_id
                  INNER JOIN question q ON q.id = tq.question_id
                  LEFT JOIN answer ans ON ans.id = ra.answer_id
                WHERE r.test_id = :test_id AND r.test_psychologist_id = :tpsy_id AND ra.test_question_id = :tqid ';
        $query1 = $connected->prepare($sql1);
        $query1->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
        $query1->bindValue(':tpsy_id', (int)$tpsy_id, PDO::PARAM_INT);
        $query1->bindValue(':tqid', (int)$va['tqid'], PDO::PARAM_INT);
        $query1->execute();
        $rows1 = $query1->fetchAll();
      }

      $newResult[] = array('content'      => $va['content'],
                           'answer_id'    => $va['answer_id'],
                           'jump_to'      => $va['jump_to'],
                           'ans_title'    => $va['ans_title'],
                           'type'         => $va['type'],
                           'que_title'    => $va['que_title'],
                           'description'  => $va['description'],
                           'hide_title'   => $va['hide_title'],
                           'tqid'         => $va['tqid'],
                           'is_required'  => $va['is_required'],
                           'result_answer' => $rows1);
    }

    return $newResult;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getResponseAnswerByTestPsychologist'.$e->getMessage();
  }

  return $result;
}

/**
* Search Report Activity log
* @author Mr. Hong Syden
* @param  int $staff_id is staff_id
* @param  string $act_log_fdate is activity_log date
* @param  string $act_log_tdate is activity_log date
* @param  int $slimit is slimit for assign to limit
* @return array or boolean
*/
function psychologist_activity()
{
 global $debug, $connected, $total_data, $limit, $offset;
 $result = true;
 if(!empty($slimit)) $limit = $slimit;

 try {
   $sql = 'SELECT alog.content, alog.created_at, psy.*, (SELECT COUNT(*) FROM `activity_log` alog
             INNER JOIN psychologist psy ON psy.id = alog.psychologist_id ) AS total FROM `activity_log` alog
             INNER JOIN psychologist psy ON psy.id = alog.psychologist_id
           LIMIT :offset, :limit ';
   $stmt = $connected->prepare($sql);
   $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
   $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
   $stmt->execute();
   $rows = $stmt->fetchAll();
   if(count($rows) > 0) $total_data = $rows[0]['total'];

   return $rows;
 } catch (Exception $e) {
   $result = false;
   if($debug)  echo 'Errors: psychologist_activity '.$e->getMessage();
 }
 return $result;
}

function getMessageResultTopic($tpid, $test_id, $lang)
{
  global $debug, $connected;
  $result = true;
  try{

    //Query test_question_topic_hide by test_id
    $sql_data2 = ' SELECT * FROM `test_question_topic_hide` WHERE test_id = :test_id ORDER BY id ASC LIMIT 0, 1';
    $query4 = $connected->prepare($sql_data2);
    $query4->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query4->execute();
    $rowsTopic = $query4->fetch();

    $sql= ' SELECT rs.*, rsa.answer_id AS res_answer_id, atp.*, rst.view_order, t.name AS topic_title, tqth.if_topic_id,
              ROUND(SUM(IF(atp.assign_value = 0, atp.default_value, atp.assign_value * atp.weight_value)), 2) as amount
            FROM `response` rs
              INNER JOIN response_answer rsa ON rsa.response_id = rs.id
              INNER JOIN answer ans ON ans.id = rsa.answer_id AND ans.calculate = 0
              INNER JOIN answer_topic atp ON atp.answer_id = ans.id
              INNER JOIN topic t ON t.id = atp.topic_id
              LEFT JOIN (SELECT r.topic_id, r.view_order FROM result r WHERE r.test_id = :test_id GROUP BY r.topic_id) rst ON rst.topic_id = atp.topic_id
              LEFT JOIN test_question_topic_hide tqth ON tqth.test_id = rs.test_id AND tqth.topic_id = t.id
            WHERE rs.test_patient_id = :tpid AND t.lang = :lang GROUP BY atp.topic_id ORDER BY rst.view_order ASC ';

    $query = $connected->prepare($sql);
    $query->bindValue(':tpid', (int)$tpid, PDO::PARAM_INT);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetchAll();
    if(!empty($result) && COUNT($result) > 0) {
      foreach ($result as $key => $value) {
        $result1 = getResultTopicByAmount($value['test_id'], $value['topic_id'], $value['amount']);
        if(!empty($result1['message'])) {
          $newResult[] = array('result_id'=> $result1['id'], 'topic_id' => $result1['topic_id'], 'topic_title' => $value['topic_title'], 'amount' => $value['amount'], 'message' => $result1['message']);
        }
      }
    }

    if(!empty($newResult) && !empty($rowsTopic)){
      foreach ($newResult as $key => $va) {
        // Check if has test topic hide
        if($rowsTopic['topic_id'] == $va['topic_id'])
        {
          $newResultRe[] = array('result_id'=> $va['result_id'], 'topic_id' => $va['topic_id'], 'topic_title' => $va['topic_title'], 'amount' => $va['amount'], 'message' => $va['message']);
        }
      }

      //Query test_question_topic_hide
      $sql5 = ' SELECT * FROM `test_question_topic_hide` WHERE test_id = :test_id ORDER BY id ASC ';
      $query5 = $connected->prepare($sql5);
      $query5->bindValue(':test_id', $test_id, PDO::PARAM_INT);
      $query5->execute();
      $rTopicHide = $query5->fetchAll();

      foreach ($rTopicHide as $key => $va) {
        $result = getResultAnswerTopic($tpid, $test_id, '', $va['topic_id']);
        //Get check hide/show
        $testQueHideShow = getTestQuesHideShow($test_id, $va['topic_id'], $result['amount']);

        if(!empty($testQueHideShow)) {
          $resultAnsTopic_if = getResultAnswerTopic($tpid, $test_id, '', $testQueHideShow['if_topic_id']);

          $rGetResultTopicByAmount = getResultTopicByAmount($result['test_id'], $testQueHideShow['if_topic_id'], $resultAnsTopic_if['amount']);
          $newResultRe[] = array('result_id'=> $rGetResultTopicByAmount['id'], 'topic_id' => $rGetResultTopicByAmount['topic_id'], 'topic_title' => $resultAnsTopic_if['topic_title'], 'amount' => $resultAnsTopic_if['amount'], 'message' => $rGetResultTopicByAmount['message']);
        }
      }

    }else {
      $newResultRe = $newResult;
    }

      if(!empty($newResultRe)){
        foreach ($newResultRe as $key => $va) {
          //Get Result condition
          $sql3 ='SELECT rt.message, t.id AS test_id, tp.name AS topic_name, tp.id AS topic_id, rt.view_order
                  FROM `result_condition` rc
                    INNER JOIN result rt ON rt.id = rc.show_result_id
                    INNER JOIN test t ON t.id = rt.test_id
                    INNER JOIN topic tp ON tp.id = rt.topic_id
                  WHERE rc.result_id = :result_id GROUP BY rc.id ORDER BY rt.view_order ASC ';
          $stmt3 = $connected->prepare($sql3);
          $stmt3->bindValue(':result_id', (int)$va['result_id'], PDO::PARAM_INT);
          $stmt3->execute();
          $row3 = $stmt3->fetchAll();
          $newResultTest[] = array('topic_title' => $va['topic_title'], 'amount' => $va['amount'], 'message' => $va['message'], 're_condition' => $row3);
        }
      }

    return $newResultTest;

  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getMessageResultTopic'.$e->getMessage();
  }
  return $result;
}
function getMessageResultTopicPsy($tpsy_id, $test_id, $lang)
{
  global $debug, $connected;
  $result = true;
  try{

    //Query test_question_topic_hide by test_id
    $sql_data2 = ' SELECT * FROM `test_question_topic_hide` WHERE test_id = :test_id ORDER BY id ASC LIMIT 0, 1';
    $query4 = $connected->prepare($sql_data2);
    $query4->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query4->execute();
    $rowsTopic = $query4->fetch();

    $sql= ' SELECT rs.*, rsa.answer_id AS res_answer_id, atp.*, rst.view_order, t.name AS topic_title, tqth.if_topic_id,
              ROUND(SUM(IF(atp.assign_value = 0, atp.default_value, atp.assign_value * atp.weight_value)), 2) as amount
            FROM `response` rs
              INNER JOIN response_answer rsa ON rsa.response_id = rs.id
              INNER JOIN answer ans ON ans.id = rsa.answer_id AND ans.calculate = 0
              INNER JOIN answer_topic atp ON atp.answer_id = ans.id
              INNER JOIN topic t ON t.id = atp.topic_id
              LEFT JOIN (SELECT r.topic_id, r.view_order FROM result r WHERE r.test_id = :test_id GROUP BY r.topic_id) rst ON rst.topic_id = atp.topic_id
              LEFT JOIN test_question_topic_hide tqth ON tqth.test_id = rs.test_id AND tqth.topic_id = t.id
            WHERE rs.test_psychologist_id = :tpsy_id AND t.lang = :lang GROUP BY atp.topic_id ORDER BY rst.view_order ASC ';

    $query = $connected->prepare($sql);
    $query->bindValue(':tpsy_id', (int)$tpsy_id, PDO::PARAM_INT);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetchAll();
    if(!empty($result) && COUNT($result) > 0) {
      foreach ($result as $key => $value) {
        $result1 = getResultTopicByAmount($value['test_id'], $value['topic_id'], $value['amount']);
        if(!empty($result1['message'])) {
          $newResult[] = array('result_id'=> $result1['id'], 'topic_id' => $result1['topic_id'], 'topic_title' => $value['topic_title'], 'amount' => $value['amount'], 'message' => $result1['message']);
        }
      }
    }

    if(!empty($newResult) && !empty($rowsTopic)){
      foreach ($newResult as $key => $va) {
        // Check if has test topic hide
        if($rowsTopic['topic_id'] == $va['topic_id'])
        {
          $newResultRe[] = array('result_id'=> $va['result_id'], 'topic_id' => $va['topic_id'], 'topic_title' => $va['topic_title'], 'amount' => $va['amount'], 'message' => $va['message']);
        }
      }

      //Query test_question_topic_hide
      $sql5 = ' SELECT * FROM `test_question_topic_hide` WHERE test_id = :test_id ORDER BY id ASC ';
      $query5 = $connected->prepare($sql5);
      $query5->bindValue(':test_id', $test_id, PDO::PARAM_INT);
      $query5->execute();
      $rTopicHide = $query5->fetchAll();

      foreach ($rTopicHide as $key => $va) {
        $result = getResultAnswerTopic($tpsy_id, $test_id, '', $va['topic_id']);
        //Get check hide/show
        $testQueHideShow = getTestQuesHideShow($test_id, $va['topic_id'], $result['amount']);

        if(!empty($testQueHideShow)) {
          $resultAnsTopic_if = getResultAnswerTopic($tpsy_id, $test_id, '', $testQueHideShow['if_topic_id']);

          $rGetResultTopicByAmount = getResultTopicByAmount($result['test_id'], $testQueHideShow['if_topic_id'], $resultAnsTopic_if['amount']);
          $newResultRe[] = array('result_id'=> $rGetResultTopicByAmount['id'], 'topic_id' => $rGetResultTopicByAmount['topic_id'], 'topic_title' => $resultAnsTopic_if['topic_title'], 'amount' => $resultAnsTopic_if['amount'], 'message' => $rGetResultTopicByAmount['message']);
        }
      }

    }else {
      $newResultRe = $newResult;
    }

      if(!empty($newResultRe)){
        foreach ($newResultRe as $key => $va) {
          //Get Result condition
          $sql3 ='SELECT rt.message, t.id AS test_id, tp.name AS topic_name, tp.id AS topic_id, rt.view_order
                  FROM `result_condition` rc
                    INNER JOIN result rt ON rt.id = rc.show_result_id
                    INNER JOIN test t ON t.id = rt.test_id
                    INNER JOIN topic tp ON tp.id = rt.topic_id
                  WHERE rc.result_id = :result_id GROUP BY rc.id ORDER BY rt.view_order ASC ';
          $stmt3 = $connected->prepare($sql3);
          $stmt3->bindValue(':result_id', (int)$va['result_id'], PDO::PARAM_INT);
          $stmt3->execute();
          $row3 = $stmt3->fetchAll();
          $newResultTest[] = array('topic_title' => $va['topic_title'], 'amount' => $va['amount'], 'message' => $va['message'], 're_condition' => $row3);
        }
      }

    return $newResultTest;

  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getMessageResultTopic'.$e->getMessage();
  }
  return $result;
}

/**
 * getResultTopicByAmount
 * @param  int $test_id
 * @param  int $topic_id
 * @param  string $amount
 * @return array or boolean
 */
function getResultTopicByAmount($test_id, $topic_id, $amount)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $sql= ' SELECT * FROM `result` WHERE test_id = :test_id AND topic_id = :topic_id AND score_from <= :amount AND score_to >= :amount ORDER BY score_to DESC LIMIT 0, 1 ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':topic_id', (int)$topic_id, PDO::PARAM_INT);
    $query->bindValue(':amount', (string)$amount, PDO::PARAM_STR);
    $query->execute();

    return $query->fetch();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getResultTopicByAmount'.$e->getMessage();
  }

  return $result;
}
/**
 * getResultAnswerTopic
 * @param  int $tpid is test_patient_id
 * @param  int $test_id is test_id
 * @param  int $if_topic_id is if_topic_id
 * @return array or boolean
 */
function getResultAnswerTopic($tpid, $test_id, $if_topic_id, $topic_id)
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
            WHERE rs.test_patient_id = :tpid '.$where.' GROUP BY atp.topic_id ORDER BY rst.view_order ASC ';

    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':tpid', (int)$tpid, PDO::PARAM_INT);
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
 * getResultAnswerTopic
 * @param  int $tpsy_id is test_psychologist_id
 * @param  int $test_id is test_id
 * @param  int $if_topic_id is if_topic_id
 * @return array or boolean
 */
function getResultAnswerTopicPsy($tpsy_id, $test_id, $if_topic_id, $topic_id)
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
            WHERE rs.test_psychologist_id = :tpsy_id '.$where.' GROUP BY atp.topic_id ORDER BY rst.view_order ASC ';

    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':tpsy_id', (int)$tpsy_id, PDO::PARAM_INT);
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
 * getTestQuesHideShow
 * @param  int $test_id
 * @param  int $topic_id
 * @param  string $amount
 * @return array or boolean
 */
function getTestQuesHideShow($test_id, $topic_id, $amount)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {

    $sql =' SELECT * FROM `test_question_topic_hide` WHERE test_id = :test_id AND topic_id = :topic_id AND less_than <= :amount AND bigger_than >= :amount ';

    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $stmt->bindValue(':topic_id', (int)$topic_id, PDO::PARAM_INT);
    $stmt->bindValue(':amount', (string)$amount, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetch();

    return $rows;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getTestQuesHideShow'.$e->getMessage();
  }

  return $result;
}
/**
 * getListTopicDiagramSecond for list topic in diagram second
 * @param  mix $resultsTopic is array of result topic
 * @param  int $margin_left is margin left
 * @param  int $margin_top is margint top
 * @param  string $lang is language
 * @return array or boolean
 */
function getListTopicDiagramSecond($resultsTopic, $margin_left, $margin_top, $lang)
{
  global $debug, $connected;
  $result = true;
  try{
    $sum_margin_top = $margin_top;
    $newResult = array();
    foreach ($resultsTopic as $key => $value) {
      $sql= ' SELECT tta.*, ts.* FROM `test_topic_analysis` tta
                INNER JOIN topic_analysis ts ON ts.id = tta.topic_analysis_id
              WHERE tta.test_id = :test_id AND tta.topic_id = :topic_id AND tta.less_than <= :tsocre AND tta.bigger_than >= :tsocre AND ts.lang = :lang ORDER BY tta.id DESC LIMIT 0, 1 ';
      $query = $connected->prepare($sql);
      $query->bindValue(':tsocre', (string)$value['amount'], PDO::PARAM_STR);
      $query->bindValue(':test_id', (int)$value['test_id'], PDO::PARAM_INT);
      $query->bindValue(':topic_id', (int)$value['topic_id'], PDO::PARAM_INT);
      $query->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
      $query->execute();
      $rows = $query->fetch();
      if(!empty($rows['topic_analysis_id']))
      {
        $newResult[] = array('topic_title' => $value['topic_title'], 'topic_analysis_id' => $rows['topic_analysis_id'], 'topic_analysis_title' => $rows['name'], 'margin_left' => $margin_left, 'margin_top' => $sum_margin_top);
        $sum_margin_top += 50;
      }
    }

    return $newResult;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTopicDiagramSecond'.$e->getMessage();
  }

  return $result;
}
/**
 * listTopicAnalysisDiagram
 * @param  int  $margin_left
 * @param  int  $margin_top
 * @param  int  $space_row_col
 * @param  string  $lang
 * @return array or boolean
 */
function listTopicAnalysisDiagram($margin_left, $margin_top, $space_row_col, $test_id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sum_margin_left = $margin_left;

    $sql= ' SELECT tta.*, tas.name
            FROM `test_topic_analysis` tta
              INNER JOIN topic_analysis tas ON tas.id = tta.topic_analysis_id
            WHERE tta.test_id = :test_id GROUP BY tta.topic_analysis_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetchAll();

    if(!empty($row))
    {
      foreach ($row as $key => $value) {
        if(!empty($value['id'])){
          $newResult[] = array('topic_analysis_id' => $value['topic_analysis_id'], 'topic_analysis_title' => $value['name'], 'margin_left' => $sum_margin_left, 'margin_top' => $margin_top);
          $sum_margin_left += $space_row_col;
        }
      }
    }

    return $newResult;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listTopicAnalysisDiagram'.$e->getMessage();
  }

  return $result;
}
/**
 * getListTopicDiagram for list data in diagram
 * @param  int $xleft is number of margin left
 * @param  int $xtop is number of margin top
 * @param  string $lang is language
 * @return array or boolean
 */
function getListTopicDiagram($tpid, $test_id, $xleft, $xtop)
{
  global $debug, $connected;
  $result = true;
  try{
    $sum = $xtop;
    $resultTopic = getResultAnswerTopic($tpid, $test_id, '', '');

    //Query test_question_topic_hide by test_id
    $sql_data2 = ' SELECT * FROM `test_question_topic_hide` WHERE test_id = :test_id ORDER BY id ASC LIMIT 0, 1';
    $query2 = $connected->prepare($sql_data2);
    $query2->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query2->execute();
    $rowsTopic = $query2->fetch();

    if(!empty($resultTopic) && COUNT($resultTopic) > 0)
    {
      foreach ($resultTopic as $key => $value) {
        //Get test topic answer calculate average score
        $averageScore = getTestTopicAnswerValueCalculate($value['test_id'], $value['topic_id'], $value['amount']);
        // Check if has test topic hide
        if(!empty($averageScore) && !empty($result) && $rowsTopic['topic_id'] == $value['topic_id'])
        {
          $newResult[] = array('test_id' => $value['test_id'], 'topic_id' => $value['topic_id'], 'topic_title' => $value['topic_title'], 'amount' => $averageScore, 'margin_left' => $xleft, 'margin_top' => $sum);
          $sum += 50;
        }
        if(!empty($averageScore) && empty($rowsTopic['topic_id'])) {
          $newResult[] = array('test_id' => $value['test_id'], 'topic_id' => $value['topic_id'], 'topic_title' => $value['topic_title'], 'amount' => $averageScore, 'margin_left' => $xleft, 'margin_top' => $sum);
          $sum += 50;
        }
      }
      //Query test_question_topic_hide
      $sql5 = ' SELECT * FROM `test_question_topic_hide` WHERE test_id = :test_id ORDER BY id ASC ';
      $query5 = $connected->prepare($sql5);
      $query5->bindValue(':test_id', $test_id, PDO::PARAM_INT);
      $query5->execute();
      $rTopicHide = $query5->fetchAll();

      foreach ($rTopicHide as $key => $va) {
        $result = getResultAnswerTopic($tpid, $test_id, '', $va['topic_id']);
        //Get check hide/show
        $testQueHideShow = getTestQuesHideShow($test_id, $va['topic_id'], $result['amount']);
        // check $testQueHideShow, if it has
        if(!empty($testQueHideShow)){
          $topic_id = $va['if_topic_id'];
          $resultAnsTopic_if = getResultAnswerTopic($tpid, $test_id, '', $testQueHideShow['if_topic_id']);
          //Get test topic answer calculate average score
          $averageScore = getTestTopicAnswerValueCalculate($resultAnsTopic_if['test_id'], $resultAnsTopic_if['topic_id'], $resultAnsTopic_if['amount']);
          if(!empty($averageScore)){
            $newResult[] = array('test_id' => $resultAnsTopic_if['test_id'], 'topic_id' => $resultAnsTopic_if['topic_id'], 'topic_title' => $resultAnsTopic_if['topic_title'], 'amount' => $averageScore, 'margin_left' => $xleft, 'margin_top' => $sum);
            $sum += 50;
          }
        }// end check $testQueHideShow, if it has
      }//end fetch $rTopicHide
    }

    return $newResult;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTopicDiagram'.$e->getMessage();
  }

  return $result;
}
/**
 * calWidthHeightDiagramSecond
 * @param  int $countTopic is count topic data
 * @param  int $countTopicAnalysis is count topic analysis
 * @param  int $height is height of canvas
 * @param  int $margin_left
 * @param  int $space_result_topic
 * @return array or boolean
 */
function calWidthHeightDiagramSecond($countTopic, $countTopicAnalysis, $height, $margin_left, $space_result_topic)
{
  $sum_height = $height;
  $sum_width = $margin_left + $space_result_topic;
  for ($i=0; $i < $countTopic; $i++) {
    $sum_height += 50;
  }
  for ($i=0; $i < $countTopicAnalysis; $i++) {
    $sum_width += 50;
  }

  return $newResult = array('width' => $sum_width, 'height' => $sum_height);
}
/**
 * drawingPointLineResultDiagramSecond for drawing line in diagram second
 * @param  mix $result_topic_first is array of topic
 * @param  mix $result_topic_analysis is array of topic analysis
 * @param  int $margin_left is margin left
 * @param  int $margin_top is margin top
 * @param  int $width is width of canvas
 * @return array or boolen
 */
function drawingPointLineResultDiagramSecond($result_topic_first, $result_topic_analysis, $margin_left, $margin_top, $width)
{
  $sum_margin_top = $margin_top;
  if(!empty($result_topic_first) && !empty($result_topic_analysis)){
    foreach ($result_topic_first as $key => $value) {
      foreach ($result_topic_analysis as $k => $v) {
        if($value['topic_analysis_id'] === $v['topic_analysis_id']){
          $newResult[] = array('topic_analysis_title' => $value['topic_analysis_title'], 'drawing_margin_left' => $v['margin_left'], 'drawing_margin_top' => $sum_margin_top, 'result_margin_left' => $width);
        }
      }
      $sum_margin_top += 50;
    }
  }

  return $newResult;
}
/**
 * listXlineDiagram for list Horizontal line diagram
 * @param  int $countTopic is number of countTopic
 * @param  int $diagram_width is number of canvas width
 * @param  int $space_height is number of canvas height
 * @return array or boolean
 */
function listXlineDiagram($countTopic, $diagram_width, $space_height)
{
  $result = $countTopic + 1;
  $sum = $space_height;
  for ($i=0; $i < $result; $i++) {
    $newResult[] = array('xwidth' => $diagram_width, 'xmargin_top' => $sum);
    $sum += 50;
  }

  return $newResult;
}
/**
 * listXlineDiagramCenter for list Horizontal medium line diagram
 * @param  [int $numValue is number of count topic or test
 * @param  int $diagram_width is number of canvas width
 * @param  int $space_height is number of canvas height
 * @param  int $margin_left is number of margin left
 * @return array or boolean
 */
function listXlineDiagramCenter($numValue, $diagram_width, $space_height, $margin_left)
{
  $sum = $space_height;
  $sum_diagram_width = $diagram_width;
  for ($i=0; $i < $numValue; $i++) {
    $newResult[] = array('xwidth' => $sum_diagram_width, 'xmargin_left' => $margin_left, 'xmargin_top' => $sum);
    $sum += 50;
  }

  return $newResult;
}
/**
 * calWidthHeightDiagram is Calculate get width and height for canvas size
 * @param   int $countTopic is number of countTopic
 * @param  int $width is number of canvas width
 * @param  int $height is number of canvas height
 * @return array or boolean
 */
function calWidthHeightDiagram($countTopic, $width, $height)
{
  $result = $countTopic + 2;
  $sum_height = 60;
  for ($i=0; $i < $result; $i++) {
    $sum_height += $height;
  }
  return $newResult = array('width' => $width, 'height' => $sum_height);
}
/**
 * listNumberMinMax for list text of number show above Vertical line
 * @param  int $margin_left is number of margin left
 * @param  int $margin_top is number of margin top
 * @return top
 */
function listNumberMinMax($margin_left, $margin_top)
{
  $sum = $margin_left;
  $text_number = 20;
  for ($i=0; $i < 7; $i++) {
    $newResult[] = array('text_number' => $text_number, 'margin_left' => $sum, 'margin_top' => $margin_top);
    $sum += 50;
    $text_number += 10;
  }

  return $newResult;
}
/**
 * listTextMinMax for list text show above number
 * @param  int $margin_left is number of margin left
 * @param  int $margin_top is number of margin top
 * @return array of boolean
 */
function listTextMinMax($margin_left, $margin_top)
{
  $text = array('min', '', '-s', 'm', '+s', '', 'max');
  $sum = $margin_left;
  foreach ($text as $key => $value) {
    $newResult[] = array('text_header' => $value, 'margin_left' => $sum, 'margin_top' => $margin_top);
    $sum += 50;
  }

  return $newResult;
}
/**
 * listBackgroudColorDiagram
 * @param  int $margin_left is number of margin left
 * @param  int $margin_top is number of margin top
 * @param  int $width  is number of space width each Vertical line
 * @return array or boolean
 */
function listBackgroundColorDiagram($margin_left, $margin_top, $width)
{
  $color = array('rgba(188, 119, 91, 0.34)', 'rgba(244, 246, 174, 0.13)', 'rgba(146, 151, 210, 0.33)', 'rgba(146, 151, 210, 0.33)', 'rgba(244, 246, 174, 0.13)', 'rgba(188, 119, 91, 0.34)');
  $sum_margin_left = $margin_left;
  foreach ($color as $key => $value) {
    $newResult[] = array('margin_left' => $sum_margin_left, 'margin_top' => $margin_top, 'width' => $width, 'color' => $value);
    $sum_margin_left += 50;
  }

  return $newResult;
}
/**
 * drawingPointLineResult
 * @param   mix $resultsTopic is array of result topic
 * @param  int $margin_left is margin left
 * @param  int $margin_top is margint top
 * @return array or boolean
 */
function drawingPointLineResult($resultsTopic, $margin_left, $margin_top, $test_id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $sumMaginTop = $margin_top;
    $sumResultMarginLeft = $margin_left + 435;

    if(!empty($resultsTopic))
    {
      foreach ($resultsTopic as $key => $value)
      {
        $calDrawingLine = $value['amount'] * 5;
        if($value['amount'] <= 20 || $value['amount'] == 0){
          $sumDrawingLine = $margin_left + 100;
        }elseif ($value['amount'] >= 80) {
          $sumDrawingLine = $margin_left + 400;
        }else {
          $sumDrawingLine = $calDrawingLine + $margin_left;
        }
        $newResult[] = array('amount_result' => $value['amount'], 'drawing_margin_left' => $sumDrawingLine, 'drawing_margin_top' => $sumMaginTop, 'result_margin_left' => $sumResultMarginLeft);
        $sumMaginTop += 50;
      }
    }

    return $newResult;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: drawingPointLineResult'.$e->getMessage();
  }

  return $result;
}
/**
 * listYLineDiagram for list Vertical line in diagram
 * @param  int $yleft is number of margin left
 * @param  int $ytop is number of margin top
 * @return array or boolean
 */
function listYLineDiagram($yleft, $ytop)
{
  $result = array();
  $sum = $yleft;
  for ($i=0; $i < 7; $i++) {;
    $result[] = array('ymargin_left' => $sum, 'ymargin_top' => $ytop);
    $sum += 50;
  }

  return $result;
}
/**
 * listYLineDiagramCenter for list Vertical medium line center
 * @param  int $yleft is number of margin left
 * @param  int $ytop is number of margin top
 * @return array or boolean
 */
function listYLineDiagramCenter($yleft, $ytop)
{
  $result = array();
  $sum = $yleft + 25;
  for ($i=0; $i < 6; $i++) {;
    $result[] = array('ymargin_left_center' => $sum, 'ymargin_top_center' => $ytop);
    $sum += 50;
  }

  return $result;
}
/**
 * listSmall_YlineDiagram for list Vertical small line above
 * @param  int $yleft is number of margin left
 * @param  int $ytop is number of margin top
 * @return array or boolean
 */
function listSmall_YlineDiagram($yleft, $ytop)
{
  $sum = $yleft;
  for ($i=0; $i <= 60 ; $i++) {
    $result[] = array('y_small_margin_left' => $sum, 'y_small_margin_top' => $ytop);
    $sum += 5;
  }

  return $result;
}
/**
 * listBackgroundColorDiagramSecond
 * @param  int $value_count
 * @param  int $margin_left
 * @param  int $margin_top
 * @param  int $width
 * @return array or boolean
 */
function listBackgroundColorDiagramSecond($value_count, $margin_left, $margin_top, $width)
{
  $color = array('red' => 'rgba(188, 119, 91, 0.34)', 'blue' => 'rgba(146, 151, 210, 0.33)');
  // echo $value_count;
  $sum_margin_left = $margin_left;
  for ($i=0; $i < $value_count; $i++) {
    $result = $i % 2;
    if($result == 0){
      $newResult[] = array('margin_left' => $sum_margin_left, 'margin_top' => $margin_top, 'width' => $width, 'color' => $color['red']);
    }else {
      $newResult[] = array('margin_left' => $sum_margin_left, 'margin_top' => $margin_top, 'width' => $width, 'color' => $color['blue']);
    }
    $sum_margin_left += 50;
  }

  return $newResult;
}
/**
 * listRotateLineDiagramSecond
 * @param  int $countTopic is count topic data
 * @param  int $lineTo_left
 * @param  int $space_height
 * @param  int $moveTo_left
 * @param  int $moveTo_top
 * @param  int $space_row_col
 * @return array or boolean
 */
function listRotateLineDiagramSecond($countTopic, $lineTo_left, $space_height, $moveTo_left, $moveTo_top, $space_row_col)
{
  $result = array();
  $sum_moveTo_left = $moveTo_left;
  $sum_lineTo_left = $lineTo_left;

  for ($i=0; $i <= $countTopic; $i++) {;
    $result[] = array('move_to_left' => $sum_moveTo_left, 'move_to_top' => $moveTo_top, 'line_to_left' => $sum_lineTo_left, 'line_to_top' => $space_height);
    $sum_moveTo_left += $space_row_col;
    $sum_lineTo_left += $space_row_col;
  }
  return $result;

}
/**
 * listYLineDiagramSecond
 * @param  int $countTopic is count topic data
 * @param  int $yleft is margin_left
 * @param  int $ytop is margin_top
 * @return array or boolean
 */
function listYLineDiagramSecond($countTopic, $yleft, $ytop)
{
  $result = array();
  $sum = $yleft;
  for ($i=0; $i <= $countTopic; $i++) {;
    $result[] = array('ymargin_left' => $sum, 'ymargin_top' => $ytop);
    $sum += 50;
  }
  return $result;
}
/**
 * getTestTopicAnswerValueCalculate
 * @param  int $test_id is test_id
 * @param  int $topic_id is topic_id
 * @param  string $amount
 * @return number
 */
function getTestTopicAnswerValueCalculate($test_id, $topic_id, $amount)
{

  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $sql =' SELECT tta.*, tp.name AS topic_title FROM `test_topic_answer` tta
                    INNER JOIN test t ON t.id = tta.test_id
                    INNER JOIN topic tp ON tp.id = tta.topic_id
                  WHERE tta.test_id = :test_id AND tta.topic_id = :topic_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query->bindValue(':topic_id', $topic_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();

    // Calculate average
    if (COUNT($rows) > 0 && $rows['average'] > 0 && $rows['stdd'] > 0 && $rows['multiplier'] > 0 && $rows['constant'] > 0){
      $zScore = ($amount - $rows['average']) / $rows['stdd'];
      $averageScore = round($zScore * $rows['multiplier'] + $rows['constant'], 2);
    }else {
      $averageScore = 0.00;
    }

    return $averageScore;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getTestTopicAnswerValueCalculate'.$e->getMessage();
  }

  return $result;
}
/**
 * getListTestQuesCondition
 * @param   $nrid is new_result_id
 * @return array or boolean
 */
function getListTestQuesCondition($nrid, $group_by, $slimit)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;

  try{
    if(!empty($slimit)) $limit = $slimit;
    if(!empty($group_by)) $set_group_by .= ' GROUP BY test_question_id ';
    if(!empty($slimit)) $setLimit .= ' LIMIT :offset, :limit ';

    $sql =' SELECT tqc.*, q.title AS q_title, q.description, q.type, q.is_email, q.hide_title, q.lang, tq.view_order, (SELECT COUNT(*) FROM `test_question_condition` tqc INNER JOIN test_question tq ON tq.id = tqc.test_question_id INNER JOIN question q ON q.id = tq.question_id WHERE tqc.new_result_id = :nrid) AS total
            FROM `test_question_condition` tqc
             INNER JOIN test_question tq ON tq.id = tqc.test_question_id
             INNER JOIN question q ON q.id = tq.question_id
            WHERE tqc.new_result_id = :nrid '.$set_group_by.' ORDER BY tq.id ASC '.$setLimit;
    $query = $connected->prepare($sql);
    $query->bindValue(':nrid', $nrid, PDO::PARAM_INT);
    if(!empty($slimit))
    {
      $query->bindValue(':offset', $offset, PDO::PARAM_INT);
      $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    }
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestQuesCondition'.$e->getMessage();
  }
  return $result;
}

?>
