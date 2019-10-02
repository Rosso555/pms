<?php
/**
 * admin_login
 * @param  string $username
 * @param  string $password
 * @return array or boolean
 */
function admin_login($username, $password)
{
  global $debug, $connected, $total_data;
  $result = true;

  try{
    $sql = ' SELECT * FROM `staff` WHERE name = :username AND password = :password AND status = 1 ';
    $query = $connected->prepare($sql);
    $query->bindValue(':username', (string)$username, PDO::PARAM_STR);
    $query->bindValue(':password', (string)$password, PDO::PARAM_STR);
    $query->execute();

    return $query->fetch();
  }catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: admin_login'.$e->getMessage();
  }

  return $result;
}
/**
 * is_key_lang_exist
 * @param  string  $key_lang
 * @return boolean
 * @author In khemarak
 */
function is_key_lang_exist($key_lang)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `multi_lang` WHERE key_lang = :key_lang ';
    $query = $connected->prepare($sql);
    $query->bindValue(':key_lang', (string)$key_lang, PDO::PARAM_STR);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: is_key_lang_exist'.$e->getMessage();
  }
  return $result;
}
/**
 * getMultilangBy unique_id
 * @param  int $unique_id
 * @return array or boolean
 * @author In khemarak
 */
function getMultilangByID($unique_id)
{
  global $debug, $connected;
  $result = true;
  try
  {
    $sql = 'SELECT * FROM `multi_lang` WHERE unique_id = :unique_id';
    $query = $connected->prepare($sql);
    $query->bindValue(':unique_id', $unique_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    return $rows;
  }
  catch (Exception $e)
  {
    if($debug) echo 'Error: getMultilangByID'. $e->getMessage();
  }
  return $result;
}
/**
 * updateDefaultLang for update default_lang to zero
 * @return true or false
 * @author In khemarak
 * @return boolean
 */
function updateDefaultLang()
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' UPDATE `language` SET `default_lang`= 0 WHERE default_lang = 1 ';
    $query = $connected->prepare($sql);
    $query->execute();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: updateDefaultLang'.$e->getMessage();
  }
  return $result;
}
/**
 * listMultiLang
 * @param  string $kwd is keywrd
 * @param  int $slimit is setLimit
 * @return array or boolean
 */
 function listMultiLang($kwd, $slimit)
 {
   global $debug, $connected, $limit, $offset, $total_data;
   $result = true;

   try {
     $condition = '';

     if(!empty($slimit)) $limit = $slimit;

     if(!empty($kwd)){
       $sql1 = ' SELECT * FROM `multi_lang` WHERE title LIKE :kwd ';
       $stmt1 = $connected->prepare($sql1);
       $stmt1->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
       $stmt1->execute();
       $row1 = $stmt1->fetchAll();
       $rWhereTitle = array_unique_by_key($row1, 'key_lang');

       if(!empty($rWhereTitle)){
         $where_in = '';
         foreach ($rWhereTitle as $key => $value) {
           if($key == 0){
             $where_in .= ' "'.$value['key_lang'].'" ';
           }else {
             $where_in .= ', "'.$value['key_lang'].'" ';
           }
         }//End fetch
         $condition .= ' WHERE ml.key_lang IN ('.$where_in.') ';
       }else {
         $condition .= ' WHERE ml.key_lang LIKE :kwd ';
       }
     }

     $sql = ' SELECT ml.*, l.title AS language_title, (SELECT COUNT(*) FROM `multi_lang` ml INNER JOIN language l ON l.id = ml.language_id '.$condition.') AS total
                 FROM `multi_lang` ml
                 INNER JOIN language l ON l.id = ml.language_id '.$condition.' ORDER BY ml.key_lang DESC LIMIT :offset, :limit ';

     $stmt = $connected->prepare($sql);
     if(empty($rWhereTitle) && !empty($kwd)) $stmt->bindValue(':kwd', $kwd .'%', PDO::PARAM_STR);
     $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
     $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
     $stmt->execute();
     $row = $stmt->fetchAll();
     if (count($row) > 0) $total_data = $row[0]['total'];

     return $row;
   } catch (Exception $e) {
     $result = false;
     if($debug)  echo 'Errors: listMultiLang'.$e->getMessage();
   }

   return $result;
 }
/**
 * listLanguage
 * @param  string $kwd
 * @return array or boolean
 * @author In khemarak
 */
function listLanguage($kwd)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';
    if(!empty($kwd))
    {
      $condition .= ' WHERE title LIKE :kwd ';
    }
    $sql = ' SELECT *, (SELECT COUNT(*) FROM `language` '.$condition.') AS total FROM `language` '.$condition.' ORDER BY id DESC LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);
    if (!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];

    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listLanguage'.$e->getMessage();
  }

  return $result;
}
/**
 * ListStaffRoleByPermission
 * @param string $kwd
 * @author In khemarak
 * @return array
 */
function ListStaffRoleByPermission($kwd = "")
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $sql = 'SELECT staff_permission.* ,staff_role.name AS staff_role_name
             FROM `staff_permission`
             INNER JOIN staff_role
             ON staff_permission.staff_role_id = staff_role.id
             GROUP BY staff_permission.staff_role_id';
    $stmt = $connected->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll();
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: ListStaffRoleByPermission'.$e->getMessage();
  }
  return $result;
}
/**
 * ListStaffPermission
 * @param string $kwd
 * @author In khemarak
 * @return array
 */
function ListStaffPermission($kwd = "", $srid)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $condition = '';
    if(!empty($kwd))
    {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' staff_role.name LIKE :kwd OR staff_function.title LIKE :kwd ';
    }
    if(!empty($srid))
    {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' staff_permission.staff_role_id = :srid ';
    }
    if(!empty($condition)) $where = ' WHERE '.$condition;

    $sql = 'SELECT staff_permission.* ,staff_role.name AS staff_role_name, staff_function.title AS staff_function_title,(SELECT COUNT(*) FROM `staff_permission`
              INNER JOIN staff_role
              ON staff_permission.staff_role_id = staff_role.id
              INNER JOIN staff_function
              ON staff_permission.staff_function_id = staff_function.id
              '.$where.') AS total
             FROM `staff_permission`
             INNER JOIN staff_role
             ON staff_permission.staff_role_id = staff_role.id
             INNER JOIN staff_function
             ON staff_permission.staff_function_id = staff_function.id
             '.$where.' ORDER BY id DESC LIMIT :offset, :limit';
    $stmt = $connected->prepare($sql);
    if(!empty($kwd)) $stmt->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    if(!empty($srid)) $stmt->bindValue(':srid', (int)$srid, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetchAll();
    if (count($row) > 0) $total_data = $row[0]['total'];
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: ListStaffPermission'.$e->getMessage();
  }
  return $result;
}
/**
 * listFunction
 * @param  string $kwd
 * @return array or boolean
 * @author in Khemarak
 */
function listFunction($kwd = "")
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $condition = '';
    if(!empty($kwd))
    {
      $condition .= ' WHERE title LIKE :kwd OR task_name LIKE :kwd OR action_name LIKE :kwd ';
    }

    $sql = 'SELECT * , (SELECT COUNT(*) FROM `staff_function` '.$condition.') AS total
             FROM `staff_function` '.$condition.' ORDER BY task_name DESC LIMIT :offset, :limit';
    $stmt = $connected->prepare($sql);
    if(!empty($kwd)) $stmt->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetchAll();
    if (count($row) > 0) $total_data = $row[0]['total'];
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: ListFunction'.$e->getMessage();
  }
  return $result;
}
/**
 * [ListStaffRole]
 * @param string $kwd
 * @author In khemarak
 * @return array
 */
function ListStaffRole($kwd = "")
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $condition = '';
    if(!empty($kwd))
    {
      $condition .= ' WHERE name LIKE :kwd ';
    }

    $sql = 'SELECT * , (SELECT COUNT(*) FROM `staff_role` '.$condition.') AS total
             FROM `staff_role` '.$condition.' ORDER BY id DESC LIMIT :offset, :limit';
    $stmt = $connected->prepare($sql);
    if(!empty($kwd)) $stmt->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetchAll();
    if (count($row) > 0) $total_data = $row[0]['total'];
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: ListStaffRole'.$e->getMessage();
  }
  return $result;
}
/**
 * ListStaffInfo
 * @param string $kwd
 * @author In khemarak
 * @return array
 */
function ListStaffInfo($kwd, $status)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $condition = '';
    if(!empty($kwd))
    {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' staff.name LIKE :kwd ';
    }
    if(!empty($status))
    {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' staff.status = :status ';
    }
    if(!empty($condition)) $where .= ' WHERE '.$condition;

    $sql = 'SELECT staff.*, staff_role.name AS role_name, (SELECT COUNT(*) FROM `staff`  INNER JOIN staff_role ON staff.staff_role_id = staff_role.id '.$where.') AS total
             FROM `staff`
             INNER JOIN staff_role
               ON staff.staff_role_id = staff_role.id
             '.$where.' ORDER BY staff.id DESC LIMIT :offset, :limit';

    $stmt = $connected->prepare($sql);
    if(!empty($kwd)) $stmt->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    if(!empty($status)) $stmt->bindValue(':status', (int)$status, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetchAll();
    if (count($row) > 0) $total_data = $row[0]['total'];
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: ListStaffInfo'.$e->getMessage();
  }
  return $result;
}
/**
 * listPatientAdmin
 * @param  string $kwd is keyword
 * @param  int $psychologist_id
 * @param  int $gender
 * @param  int $status
 * @return array or boolean
 */
function listPatientAdmin($kwd, $psychologist_id, $gender, $status){
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = $where = '';
    if(!empty($kwd)) {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' (p.code LIKE :kwd OR p.email LIKE :kwd OR p.age LIKE :kwd OR CONCAT(psy.first_name, " ", psy.last_name) LIKE :kwd) ';
    }
    if(!empty($psychologist_id)) {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' p.psychologist_id = :psychologist_id ';
    }
    if(!empty($gender)) {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' p.gender = :gender ';
    }
    if(!empty($status)) {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' p.status = :status ';
    }

    if(!empty($condition)) $where .= ' WHERE '.$condition;

    $sql = ' SELECT p.*, psy.username AS psy_name, psy.first_name, psy.last_name, (SELECT COUNT(*) FROM `patient` p INNER JOIN psychologist psy ON psy.id = p.psychologist_id '.$where.') AS total
             FROM `patient` p
              INNER JOIN psychologist psy ON psy.id = p.psychologist_id '.$where.' ORDER BY id DESC LIMIT :offset, :limit ';

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
    if($debug)  echo 'Errors: listPatientAdmin'.$e->getMessage();
  }

  return $result;
}
/**
 * listPsychologistAdmin
 * @param  string $kwd is keyword
 * @param  int $psychologist_id
 * @param  int $status is status
 * @return array or boolean
 */
function listPsychologistAdmin($kwd, $psychologist_id, $status){
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = $where = '';
    if(!empty($kwd)) {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' email LIKE :kwd ';
    }
    if(!empty($psychologist_id)) {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' id = :psychologist_id ';
    }
    if(!empty($status)) {
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' status = :status ';
    }

    if(!empty($condition)) $where .= ' WHERE '.$condition;

    $sql = ' SELECT *, (SELECT COUNT(*) FROM `psychologist` '.$where.') AS total
             FROM `psychologist` '.$where.' ORDER BY id DESC LIMIT :offset, :limit ';

    $query = $connected->prepare($sql);
    if (!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
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
    if($debug)  echo 'Errors: listPsychologistAdmin'.$e->getMessage();
  }

  return $result;
}
/**
 * listQuestion
 * @param  string $kwd is keyword
 * @param  string $lang is language
 * @return array or boolean
 */
function listQuestion($kwd, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';
    if(!empty($kwd))
    {
      $condition .= ' AND title LIKE :kwd ';
    }

    $sql = ' SELECT *, (SELECT COUNT(*) FROM `question` WHERE lang = :lang '.$condition.') AS total FROM `question` WHERE lang = :lang '.$condition.' ORDER BY id DESC LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);
    if (!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    $query->bindValue(':lang', $lang, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetchAll();
    if (count($row) > 0) $total_data = $row[0]['total'];
    return $row;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: ListQuestion'.$e->getMessage();
  }

  return $result;
}
/**
 * getQuestionByID
 * @param  int $id is question_id
 * @return array or boolean
 */
function getQuestionByID($id)
{
  global $debug, $connected;
  $result = true;
  try
  {
    $sql = 'SELECT * FROM `question` WHERE id = :id';
    $query = $connected->prepare($sql);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetch();
    return $row;
  }
  catch (Exception $e)
  {
    if($debug) echo 'Error: getQuestionByID'. $e->getMessage();
  }
  return $result;
}
/**
 * checkDeleteQuestion for delete question
 * @param  int $id is question_id
 * @return boolean
 */
function checkDeleteQuestion($id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count, q.title AS question_title
            FROM `test_question` tq
             INNER JOIN question q ON q.id = tq.question_id
            WHERE tq.question_id = :id';
    $query = $connected->prepare($sql);
    $query->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkDeleteQuestion'.$e->getMessage();
  }
  return $result;
}
/**
 * checkDeleteTopic
 * @param  int $id is topic_id
 * @return array or boolean
 */
function checkDeleteTopic($id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count, t.name FROM `result` r INNER JOIN topic t ON t.id = r.topic_id WHERE r.topic_id = :id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkDeleteTopic'.$e->getMessage();
  }
  return $result;
}
/**
 * checkDeleteTopicAnalysis
 * @param  int  $id is topic_analysis_id
 * @return boolean
 */
function checkDeleteTopicAnalysis($id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count, ts.name FROM `test_topic_analysis` tts INNER JOIN topic_analysis ts ON ts.id = tts.topic_analysis_id WHERE tts.topic_analysis_id = :id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkDeleteTopicAnalysis'.$e->getMessage();
  }
  return $result;
}
/**
 * Get listTest
 * @author Hong Syden
 * @param  string $kwd  is keyword
 * @param  string $lang is language
 * @return array or bolean
 */
function listTest($kwd, $catid, $testid, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';
    if(!empty($kwd))
    {
      $condition .= ' AND t.title LIKE :kwd ';
    }
    if(!empty($catid))
    {
      $condition .= ' AND t.category_id = :catid';
    }
    if(!empty($testid))
    {
      $condition .= ' AND t.id = :test_id';
    }
    $sql =' SELECT t.*, c.name AS category_name,
              (SELECT COUNT(*) FROM `test` t INNER JOIN category c ON c.id = t.category_id WHERE t.lang = :lang '.$condition.') AS total
            FROM `test` t
            INNER JOIN category c ON c.id = t.category_id
              WHERE t.lang = :lang '.$condition.'
            ORDER BY t.id DESC LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);

    if(!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    if(!empty($catid)) $query->bindValue(':catid', (int)$catid, PDO::PARAM_INT);
    if(!empty($testid)) $query->bindValue(':test_id', (int)$testid, PDO::PARAM_INT);
    $query->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetchAll();
    if (count($row) > 0) $total_data = $row[0]['total'];
    return $row;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: ListTest'.$e->getMessage();
  }
  return $result;
}
/**
 * listMailerlite
 * @param  string $kwd is keyword
 * @return array or boolean
 */
function listMailerlite($kwd)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    if(!empty($kwd))
    {
      $condition .= ' WHERE title LIKE :kwd ';
    }

    $sql =' SELECT *, (SELECT COUNT(*) FROM `mailerlite` '.$condition.') AS total FROM `mailerlite` '.$condition.' LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);
    if (!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listMailerlite'.$e->getMessage();
  }

  return $result;
}
/**
 * listResult
 * @param  int $tid is test_id
 * @param  int $topic_id is topic_id
 * @param  int $slimit for set limit
 * @return array or boolean
 */
function listResult($tid, $topic_id, $slimit)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';
    if(!empty($slimit)) {
      $limit = $slimit;
      $setLimit = ' LIMIT :offset, :limit ';
    }
    if(!empty($topic_id)) $condition = ' AND r.topic_id = :topic_id ';

    $sql =' SELECT r.*, t.name, (SELECT COUNT(*) FROM `result` r INNER JOIN topic t ON t.id = r.topic_id WHERE r.test_id = :test_id '.$condition.') AS total
            FROM `result` r
              INNER JOIN topic t ON t.id = r.topic_id
            WHERE r.test_id = :test_id '.$condition.' ORDER BY r.topic_id DESC '.$setLimit;
    $query = $connected->prepare($sql);
    if(!empty($topic_id)) $query->bindValue(':topic_id', (int)$topic_id, PDO::PARAM_INT);
    if(!empty($slimit)) {
      $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
      $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    }
    $query->bindValue(':test_id', (int)$tid, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetchAll();
    if (count($row) > 0) $total_data = $row[0]['total'];
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listResult'.$e->getMessage();
  }
  return $result;
}
/**
 * listResultCondition
 * @param  int $tid is test id
 * @param  int $rid is result id
 * @return array or boolean
 */
function listResultCondition($tid, $rid)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $sql =' SELECT rc.*, r.score_from, r.score_to, r.message, t.name FROM `result_condition` rc
              INNER JOIN result r ON r.id = rc.show_result_id
              INNER JOIN topic t ON t.id = r.topic_id
            WHERE rc.result_id = :rid AND r.test_id = :tid ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':rid', (int)$rid, PDO::PARAM_INT);
    $stmt->bindValue(':tid', (int)$tid, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listResultCondition'.$e->getMessage();
  }
  return $result;
}
/**
 * checkDeleteResult
 * @param  int $id is result_id
 * @return array or boolean
 */
function checkDeleteResult($id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql1= ' SELECT COUNT(*) AS total_count, r.score_from, r.score_to, t.name, r.test_id, r.topic_id
            FROM `result_condition` rc
              INNER JOIN result r ON r.id = rc.result_id
              INNER JOIN topic t ON t.id = r.topic_id
            WHERE rc.result_id = :id ';
    $query1 = $connected->prepare($sql1);
    $query1->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $query1->execute();
    $row1 = $query1->fetch();

    $sql2= ' SELECT COUNT(*) AS total_tque_hide FROM `test_question_topic_hide` WHERE test_id = :test_id AND topic_id = :topic_id OR test_id = :test_id ANd if_topic_id = :topic_id ';
    $query2 = $connected->prepare($sql2);
    $query2->bindValue(':test_id', (int)$row1['test_id'], PDO::PARAM_INT);
    $query2->bindValue(':topic_id', (int)$row1['topic_id'], PDO::PARAM_INT);
    $query2->execute();
    $row2 = $query2->fetch();

    $sql3= ' SELECT COUNT(*) total_topicAnlysis FROM `test_topic_analysis` WHERE test_id = :test_id AND topic_id = :topic_id ';
    $query3 = $connected->prepare($sql3);
    $query3->bindValue(':test_id', (int)$row1['test_id'], PDO::PARAM_INT);
    $query3->bindValue(':topic_id', (int)$row1['topic_id'], PDO::PARAM_INT);
    $query3->execute();
    $row3 = $query3->fetch();

    $sql4= ' SELECT COUNT(*) AS totalTopicAns FROM `test_topic_answer` WHERE test_id = :test_id AND topic_id = :topic_id ';
    $query4 = $connected->prepare($sql4);
    $query4->bindValue(':test_id', (int)$row1['test_id'], PDO::PARAM_INT);
    $query4->bindValue(':topic_id', (int)$row1['topic_id'], PDO::PARAM_INT);
    $query4->execute();
    $row4 = $query4->fetch();

    //Sum Total Count
    $totalDelete = $row1['total_count'] + $row2['total_tque_hide'] + $row3['total_topicAnlysis'] + $row4['totalTopicAns'];
    $newResult = array('score_from' => $row1['score_from'], 'score_to' => $row1['score_to'], 'name' => $row1['name'], 'total_count' => $totalDelete);
    return $newResult;

  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkDeleteResult'.$e->getMessage();
  }
  return $result;
}
/**
 * checkViewOrderTopic
 * @param  int $test_id is test_id
 * @return array or boolean
 */
function checkViewOrderTopic($test_id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS count_view_order FROM `result` WHERE test_id = :test_id AND view_order IS NULL ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['count_view_order'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkViewOrderTopic'.$e->getMessage();
  }
  return $result;
}
/**
 * getListTestTopic
 * @param  int $test_id is test_id
 * @return array or boolean
 */
function getListTestTopic($test_id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql = ' SELECT r.*, t.name FROM `result` r INNER JOIN topic t ON t.id = r.topic_id WHERE test_id = :test_id GROUP BY r.topic_id ORDER BY r.view_order ASC ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll();

  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestTopic'.$e->getMessage();
  }
  return $result;
}
/**
 * getListTestQuestionTopicHide
 * @param  int $test_id is test id
 * @return array or boolean
 */
function getListTestQuestionTopicHide($test_id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $sql =' SELECT tqth.*, t.title, tp.name AS topic_name1, tp2.name AS topic_name2
            FROM `test_question_topic_hide` tqth
              INNER JOIN test t ON t.id = tqth.test_id
              INNER JOIN topic tp ON tp.id = tqth.topic_id
              INNER JOIN topic tp2 ON tp2.id = tqth.if_topic_id
            WHERE tqth.test_id = :id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':id', (int)$test_id, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetchAll();
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestQuestionTopicHide'.$e->getMessage();
  }
  return $result;
}
/**
 * getListTransaction
 * @param  int $mlid is mailerlite_id
 * @return array or boolean
 */
function getListTransaction($mlid, $testid, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';

    if(!empty($testid)) $condition = ' AND apit.test_id = :test_id';

    $sql =' SELECT apit.*, t.title AS test_title, COUNT(gm.transaction_id) AS count_group,
              (SELECT COUNT(*) FROM (SELECT COUNT(*) FROM `apitransaction` apit
                INNER JOIN mailerlite_group gm ON gm.transaction_id = apit.id
                INNER JOIN test t ON t.id = apit.test_id
              WHERE apit.ml_id = :mlid '.$condition.' GROUP BY apit.id) AS group_by) AS total
            FROM `apitransaction` apit
              INNER JOIN mailerlite_group gm ON gm.transaction_id = apit.id
              INNER JOIN test t ON t.id = apit.test_id
            WHERE apit.ml_id = :mlid AND t.lang = :lang '.$condition.' GROUP BY apit.id LIMIT :offset, :limit';
    $query = $connected->prepare($sql);

    if(!empty($testid)) $query->bindValue(':test_id', $testid, PDO::PARAM_INT);

    $query->bindValue(':mlid', $mlid, PDO::PARAM_INT);
    $query->bindValue(':lang', $lang, PDO::PARAM_STR);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTransaction'.$e->getMessage();
  }

  return $result;
}
/**
 * is_exist_transaction_test
 * @param  int  $testid test id
 * @param  int  $mlid   mailerlite_id
 * @return boolean
 */
function is_exist_transaction_test($testid, $mlid)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `apitransaction` WHERE test_id = :testid AND ml_id = :mlid ';
    $query = $connected->prepare($sql);
    $query->bindValue(':testid', (int)$testid, PDO::PARAM_INT);
    $query->bindValue(':mlid', (int)$mlid, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: is_exist_transaction_test'.$e->getMessage();
  }
  return $result;
}
/**
 * countMailerLiteGroup
 * @param  int $tid is translate_id
 * @return boolean
 */
function countMailerLiteGroup($tid)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `mailerlite_group` WHERE transaction_id = :tid ';
    $query = $connected->prepare($sql);
    $query->bindValue(':tid', (int)$tid, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: countMailerLiteGroup'.$e->getMessage();
  }
  return $result;
}
/**
 * getListTopicHide
 * @param  int $test_id is test id
 * @param  int $topic_id is topic_id
 * @return array or boolean
 */
function getListTestTopicHide($test_id, $topic_id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    if(!empty($topic_id)) $where = ' AND r.topic_id != :topic_id ';

    $sql= ' SELECT r.*, t.name, tqth.topic_id AS topic_hide_id
            FROM `result` r
              INNER JOIN topic t ON t.id = r.topic_id
              LEFT JOIN test_question_topic_hide tqth ON tqth.topic_id = r.topic_id AND r.test_id = tqth.test_id
            WHERE r.test_id = :test_id '.$where.' GROUP BY r.topic_id ORDER BY r.view_order ASC ';

    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    if(!empty($topic_id)) $query->bindValue(':topic_id', (int)$topic_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    return $rows;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTopicHide'.$e->getMessage();
  }
  return $result;
}
/**
 * getListTestTopicAnalysis
 * @param  int  $test_id
 * @param  string  $lang is language
 * @return boolean or array
 */
function getListTestTopicAnalysis($kwd, $test_id, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';

    if(!empty($kwd)) $condition = ' AND t.name LIKE :kwd ';

    $sql =' SELECT tta.*, t.name AS topic_name, ta.name AS topic_analysis_name,
              (SELECT COUNT(*) FROM `test_topic_analysis` tta
                INNER JOIN topic t ON t.id = tta.topic_id
                INNER JOIN topic_analysis ta ON ta.id = tta.topic_analysis_id
              WHERE tta.test_id = :test_id AND t.lang = :lang AND ta.lang = :lang '.$condition.') AS total
            FROM `test_topic_analysis` tta
              INNER JOIN topic t ON t.id = tta.topic_id
              INNER JOIN topic_analysis ta ON ta.id = tta.topic_analysis_id
            WHERE tta.test_id = :test_id AND t.lang = :lang AND ta.lang = :lang '.$condition.' ORDER BY tta.topic_id DESC LIMIT :offset, :limit ';

    $query = $connected->prepare($sql);
    if (!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetchAll();
    if (count($row) > 0) $total_data = $row[0]['total'];
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestTopicAnalysis'.$e->getMessage();
  }
  return $result;
}
/**
 * listTestTopicAnswer
 * @param  int $tid is test id
 * @param  string $kwd is keyword
 * @return array or boolean
 */
function listTestTopicAnswer($tid, $kwd)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    if(!empty($kwd))
    {
      $condition .= ' AND t.name LIKE :kwd ';
    }

    $sql =' SELECT tta.*, t.name, (SELECT COUNT(*) FROM `test_topic_answer` tta INNER JOIN topic t ON t.id = tta.topic_id WHERE tta.test_id = :test_id '.$condition.') AS total
            FROM `test_topic_answer` tta
              INNER JOIN topic t ON t.id = tta.topic_id WHERE tta.test_id = :test_id '.$condition.' LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);
    if (!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    $query->bindValue(':test_id', $tid, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listTestTopicAnswer'.$e->getMessage();
  }

  return $result;
}
/**
 * is_test_topic_answer_exist
 * @param  int  $test_id is test id
 * @param  int  $topic_id is topic id
 * @return boolean or array
 */
function is_test_topic_answer_exist($test_id, $topic_id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `test_topic_answer` WHERE test_id = :test_id AND topic_id = :topic_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':topic_id', (int)$topic_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: is_test_topic_answer_exist'.$e->getMessage();
  }
  return $result;
}
/**
 * getListGroupAnswer
 * @param  int $test_id is test id
 * @param  int $gans_id is group_answer_id
 * @param  int $flag for flag equal 1 is parent
 * @param  int $slimit
 * @return array or boolean
 */
function getListGroupAnswer($test_id, $gans_id, $flag, $slimit){
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;

  try{
    if(!empty($slimit)){
      $limit = $slimit;
      $setLimit = ' LIMIT :offset, :limit ';
    }
    if(!empty($flag))   $where = ' AND ga.flag = 1 ';
    if(!empty($gans_id)) $where = ' AND ga.sub_id = :id ';

    $sql =' SELECT q.*, ga.*, (SELECT COUNT(*) FROM `group_answer` ga INNER JOIN test_question tq ON tq.id = ga.test_question_id INNER JOIN question q ON q.id = tq.question_id WHERE ga.test_id = :test_id '.$where.') AS total
            FROM `group_answer` ga
             INNER JOIN test_question tq ON tq.id = ga.test_question_id
             INNER JOIN question q ON q.id = tq.question_id
            WHERE ga.test_id = :test_id '.$where.' ORDER BY ga.flag DESC '.$setLimit;
    $query = $connected->prepare($sql);
    if(!empty($gans_id)) $query->bindValue(':id', $gans_id, PDO::PARAM_INT);
    $query->bindValue(':test_id', $test_id, PDO::PARAM_INT);

    if(!empty($slimit)){
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
    if($debug)  echo 'Errors: getListGroupAnswer'.$e->getMessage();
  }

  return $result;
}
/**
 * updateGroupAnswerBySubID
 * @param  int $id is group_answer id for update sub_id
 * @param  int $flag_id is sub_id
 * @return true or false
 */
function updateGroupAnswerBySubID($id, $flag_id){
  global $debug, $connected;
  $result = true;

  try{
    $sql =' UPDATE `group_answer` SET `sub_id` = :id WHERE sub_id = :flag_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->bindValue(':flag_id', $flag_id, PDO::PARAM_INT);
    $query->execute();
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: updateGroupAnswerBySubID'.$e->getMessage();
  }

  return $result;
}
/**
 * updateGroupAnswer for update sub_id
 * @param  int $test_id is test
 * @param  int $g_ans_id group answer_id
 * @return true or false
 */
function updateGroupAnswer($test_id, $g_ans_id){
  global $debug, $connected;
  $result = true;

  try{
    $sql =' UPDATE `group_answer` SET `sub_id`= :g_ans_id WHERE test_id = :test_id AND sub_id IS NULL ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query->bindValue(':g_ans_id', $g_ans_id, PDO::PARAM_INT);
    $query->execute();
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: updateGroupAnswer'.$e->getMessage();
  }

  return $result;
}
/**
 * is_exist_group_answer_question
 * @param  int  $test_id is test_id
 * @param  int  $t_que_id is question_id
 * @return boolean
 */
function is_exist_group_answer_question($test_id, $t_que_id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `group_answer` WHERE test_id = :test_id AND test_question_id = :t_que_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':t_que_id', (int)$t_que_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: is_exist_group_answer_question'.$e->getMessage();
  }
  return $result;
}
/**
 * is_exist_test_question
 * @param  int  $test_id
 * @param  int  $que_id is question_id
 * @return boolean
 */
function is_exist_test_question($test_id, $que_id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `test_question` WHERE test_id = :test_id AND question_id = :que_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':que_id', (int)$que_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: is_exist_test_question'.$e->getMessage();
  }
  return $result;
}
/**
 * is_exist_answer_topic
 * @param  int  $ansid is answer_id
 * @param  int  $topic_id is topic_id
 * @return boolean
 */
function is_exist_answer_topic($ansid, $topic_id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `answer_topic` WHERE answer_id = :answer_id AND topic_id = :topic_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':answer_id', (int)$ansid, PDO::PARAM_INT);
    $query->bindValue(':topic_id', (int)$topic_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: is_exist_answer_topic'.$e->getMessage();
  }
  return $result;
}
/**
 * checkTopicResult
 * @param  int $test_id
 * @param  int $topic_id
 * @return boolean
 */
function checkTopicResult($test_id, $topic_id){
  global $debug, $connected;
  $result = true;
   try{
    $sql=' SELECT COUNT(*) AS total FROM `result` WHERE test_id = :test_id AND topic_id = :topic_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':topic_id', $topic_id, PDO::PARAM_INT);
    $query->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetch();
    return $row['total'];
   }
   catch (Exception $e) {
     $result = false;
     if($debug)  echo 'Errors: checkTopicResult'.$e->getMessage();
   }
   return $result;
}
/**
 * checkDeleteTestQuestion
 * @param  int $tqid test_question_id
 * @return array or boolean
 */
function checkDeleteTestQuestion($tqid, $tid)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql =' SELECT COUNT(*) AS total_count, q.title AS que_title, t.title AS test_title FROM `response_answer` ra
              INNER JOIN response r ON r.id = ra.response_id
              INNER JOIN test_question tq ON tq.id = ra.test_question_id
              INNER JOIN question q ON q.id = tq.question_id
              INNER JOIN test t ON t.id = tq.test_id
            WHERE ra.test_question_id = :tqid AND r.test_id = :tid ';

    $query = $connected->prepare($sql);
    $query->bindValue(':tqid', (int)$tqid, PDO::PARAM_INT);
    $query->bindValue(':tid', (int)$tid, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkDeleteTestQuestion'.$e->getMessage();
  }
  return $result;
}
/**
 * sumResponseAnswer
 * @param  int $rid is response_id
 * @param  int $test_id is test_id
 * @return boolean
 */
function sumResponseAnswer($rid, $test_id){
  global $debug, $connected;
   $result = true;

   try{
     $sql=' SELECT ROUND(SUM(IF(atp.assign_value = 0, atp.default_value, atp.assign_value * atp.weight_value)), 2) as amount
            FROM `response` r
             INNER JOIN response_answer ra ON ra.response_id = r.id
             INNER JOIN answer ans ON ans.id = ra.answer_id AND ans.calculate = 0
             INNER JOIN answer_topic atp ON atp.answer_id = ans.id
            WHERE r.test_id = :test_id AND r.id = :rid ';
                 $query = $connected->prepare($sql);
     $query->bindValue(':rid', $rid, PDO::PARAM_INT);
     $query->bindValue(':test_id', $test_id, PDO::PARAM_INT);
     $query->execute();
    $row = $query->fetch();
    return $row['amount'];
   }
   catch (Exception $e) {
     $result = false;
     if($debug)  echo 'Errors: getTestQuestionTopView'.$e->getMessage();
   }

   return $result;
}
/**
 * getTopicResult
 * @param  int $tid is test_id
 * @return array or boolean
 */
function getTopicResult($tid){
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $sql =' SELECT r.*, t.name FROM `result` r INNER JOIN topic t ON t.id = r.topic_id WHERE r.test_id = :test_id GROUP BY r.topic_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', $tid, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll();
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getTopicResult'.$e->getMessage();
  }

  return $result;
}
/**
 * checkDeleteAnswer
 * @param  int $id is answer_id
 * @return array or boolean
 */
function checkDeleteAnswer($id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) total_count, a.title FROM `response_answer` ra INNER JOIN answer a ON a.id = ra.answer_id WHERE ra.answer_id = :id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkDeleteAnswer'.$e->getMessage();
  }
  return $result;
}
/**
 * Get listAnswer
 * @param  int $tqid is test_question_id
 * @param  string $kwd keyword
 * @return array or bolean
 */
function listAnswer($tqid, $kwd)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';
    if(!empty($kwd))
    {
      $condition .= ' AND title LIKE :kwd ';
    }

    $sql = ' SELECT an.*, COUNT(atp.answer_id) AS count_ans_topic, (SELECT COUNT(*) FROM `answer` an WHERE an.test_question_id = :test_question_id '.$condition.' ) AS total
              FROM `answer` an
              LEFT JOIN answer_topic atp ON atp.answer_id = an.id
             WHERE an.test_question_id = :test_question_id '.$condition.' GROUP BY an.id ORDER BY an.view_order ASC LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);

    if (!empty($kwd)) $query->bindValue(':kwd', '%'. $kwd .'%', PDO::PARAM_STR);
    $query->bindValue(':test_question_id', (int)$tqid, PDO::PARAM_INT);
    $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetchAll();
    if (count($row) > 0) $total_data = $row[0]['total'];
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listAnswer'.$e->getMessage();
  }
  return $result;
}
/**
 * getListAnswerTopic
 * @param  int $answer_id
 * @return array or boolean
 */
function getListAnswerTopic($answer_id){
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $sql =' SELECT atp.*, t.name FROM `answer_topic` atp INNER JOIN topic t ON t.id = atp.topic_id WHERE atp.answer_id = :answer_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':answer_id', $answer_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll();
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getAnswerTopic'.$e->getMessage();
  }

  return $result;
}
/**
 * getTestQuestionTopView
 * @param  int $test_id is test_id
 * @return array or boolean
 */
function getTestQuestionTopView($test_id){
  global $debug, $connected;
  $result = true;

  try{
    $sql =' SELECT * FROM `test_question` WHERE test_id = :test_id ORDER BY view_order DESC LIMIT 0, 1 ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch();
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getTestQuestionTopView'.$e->getMessage();
  }

  return $result;
}
/**
 * getCheckViewOrder
 * @param  int $test_id is test id
 * @param  int $view_order is view_order
 * @return array or boolean
 */
function getCheckViewOrder($test_id, $view_order)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;

  try{
    $sql =' SELECT ans.*, tq2.view_order AS t_que_view_order, tq.view_order AS jump_to_view_order, tq.test_id
            FROM `answer` ans
              INNER JOIN test_question tq ON tq.id = ans.jump_to
              INNER JOIN test_question tq2 ON tq2.id = ans.test_question_id
            WHERE tq.test_id = :test_id AND ans.jump_to != 0 AND tq2.view_order < :view_order ORDER BY tq.view_order DESC LIMIT 0, 1 ';

    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':view_order', (int)$view_order, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetch();
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getCheckViewOrder'.$e->getMessage();
  }
  return $result;

}
/**
 * getListNewResult
 * @param  int $test_id
 * @param  string $lang
 * @return array
 */
function getListNewResult($test_id, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;

  try{
    $sql =' SELECT nr.*, t.name, COUNT(tqc.id) AS testQueCount, (SELECT COUNT(*) FROM `new_result` nr INNER JOIN topic t ON t.id = nr.topic_id WHERE nr.test_id = :testid AND t.lang = :lang) AS total
              FROM `new_result` nr
               INNER JOIN topic t ON t.id = nr.topic_id
               LEFT JOIN test_question_condition tqc ON tqc.new_result_id = nr.id
              WHERE nr.test_id = :testid AND t.lang = :lang GROUP BY nr.id ORDER BY nr.view_order ASC LIMIT :offset, :limit';
    $query = $connected->prepare($sql);
    $query->bindValue(':testid', $test_id, PDO::PARAM_INT);
    $query->bindValue(':lang', $lang, PDO::PARAM_STR);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListNewResult'.$e->getMessage();
  }
  return $result;
}
/**
 * checkTopicNewResult
 * @param  int $test_id
 * @param  int $topic_id
 * @return boolean
 */
function checkTopicNewResult($test_id, $topic_id)
{
  global $debug, $connected;
  $result = true;
   try{
    $sql=' SELECT COUNT(*) AS total FROM `new_result` WHERE test_id = :test_id AND topic_id = :topic_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':topic_id', $topic_id, PDO::PARAM_INT);
    $query->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetch();
    return $row['total'];
   }
   catch (Exception $e) {
     $result = false;
     if($debug)  echo 'Errors: checkTopicResult'.$e->getMessage();
   }
   return $result;
}
/**
 * getListQuestionJumpTo
 * @param  int $answer_id is answer_id
 * @return array or boolean
 */
function getListQuestionJumpTo($answer_id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $sql =' SELECT ans.*, tq.id, tq.question_id, q.title AS title_que
            FROM `answer` ans
             INNER JOIN test_question tq ON tq.id = ans.jump_to
             INNER JOIN question q ON q.id = tq.question_id
            WHERE ans.id = :answer_id ';

    $query = $connected->prepare($sql);
    $query->bindValue(':answer_id', (int)$answer_id, PDO::PARAM_INT);
    $query->execute();
    $row = $query->fetch();
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListQuestionJumpTo'.$e->getMessage();
  }
  return $result;

}
/**
 * getListViewOrderTestQuestion
 * @param  int $test_id
 * @return array or boolean
 */
function getListViewOrderTestQuestion($test_id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;

  try{
    $sql =' SELECT tqvo.*, q.title AS que_title, q.description, ga.g_answer_title, (SELECT COUNT(*) FROM `test_question_view_order` tqvo INNER JOIN test_question tq ON tq.id = tqvo.test_question_id INNER JOIN question q ON q.id = tq.question_id LEFT JOIN group_answer ga ON ga.test_question_id = tqvo.test_question_id WHERE tqvo.test_id = :test_id) AS total
            FROM `test_question_view_order` tqvo
              INNER JOIN test_question tq ON tq.id = tqvo.test_question_id
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN group_answer ga ON ga.test_question_id = tqvo.test_question_id
            WHERE tqvo.test_id = :test_id ORDER BY tqvo.view_order ASC LIMIT :offset, :limit';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListViewOrderTestQuestion'.$e->getMessage();
  }
  return $result;
}
/**
 * is_view_order_exist
 * @param  int  $test_id
 * @param  int $test_que_id
 * @return boolean
 */
function is_view_order_exist($test_id, $test_que_id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `test_question_view_order` WHERE test_id = :test_id AND test_question_id = :test_que_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':test_que_id', (int)$test_que_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: is_view_order_exist'.$e->getMessage();
  }
  return $result;
}
/**
 * getListResponseByTopic
 * @param  int $test_id
 * @param  int $pat_id is patient_id
 * @param  int $psy_id is psychologist_id
 * @param  int $slimit for setLimit
 * @return array or boolean
 */
function getListResponseByTopic($test_id, $pat_id, $psy_id, $slimit)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $result = getListResponse($test_id, $pat_id, $psy_id, $slimit);

    foreach ($result as $key => $value) {
      $sql =' SELECT rs.*, rsa.answer_id AS res_answer_id, atp.*, t.name AS topic_title,
                ROUND(SUM(IF(atp.assign_value = 0, atp.default_value, atp.assign_value * atp.weight_value)), 2) as amount
              FROM `response` rs
                INNER JOIN response_answer rsa ON rsa.response_id = rs.id
                INNER JOIN answer ans ON ans.id = rsa.answer_id AND ans.calculate = 0
                INNER JOIN answer_topic atp ON atp.answer_id = ans.id
                INNER JOIN topic t ON t.id = atp.topic_id
               WHERE rs.id = :rid GROUP BY atp.topic_id, rs.id ORDER BY rs.id DESC ';

      $query = $connected->prepare($sql);
      $query->bindValue(':rid', (int)$value['id'], PDO::PARAM_INT);
      $query->execute();
      $rows = $query->fetchAll();
      $newResult[] = array('id' => $value['id'],
                           'unique_id'  => $value['unique_id'],
                           'patient_id' => $value['patient_id'],
                           'psychologist_id'  => $value['psychologist_id'],
                           'created_at' => $value['created_at'],
                           'pat_name'   => $value['pat_name'],
                           'first_name' => $value['first_name'],
                           'last_name'  => $value['last_name'],
                           'topic_result' => $rows);
    }

    return $newResult;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListResponseByTopic'.$e->getMessage();
  }
  return $result;
}
/**
 * getListResponse
 * @param  int $test_id is test id
 * @param  int $slimit for set limit
 * @return array or boolean
 */
function getListResponse($test_id, $pat_id, $psy_id, $slimit)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';
    if(!empty($slimit)) $limit = $slimit;
    if(!empty($pat_id)) $condition .= ' AND tp.patient_id = :pat_id ';
    if(!empty($psy_id)) $condition .= ' AND tps.psychologist_id = :psy_id ';
    if(!empty($slimit)) $setLimit = ' LIMIT :offset, :limit ';

    // $sql =' SELECT *, (SELECT COUNT(*) FROM `response` WHERE test_id = :test_id) AS total FROM `response` WHERE test_id = :test_id ORDER BY created_at DESC '.$setLimit;
    $sql =' SELECT r.*, p.username AS pat_name, psy.first_name, psy.last_name, tp.patient_id, tps.psychologist_id,
              (SELECT COUNT(*) FROM `response` r
                LEFT JOIN test_patient tp ON tp.id = r.test_patient_id
                LEFT JOIN test_psychologist tps ON tps.id = r.test_psychologist_id
                LEFT JOIN patient p ON p.id = tp.patient_id
                LEFT JOIN psychologist psy ON psy.id = tps.psychologist_id
              WHERE r.test_id = :test_id) AS total
            FROM `response` r
              LEFT JOIN test_patient tp ON tp.id = r.test_patient_id
              LEFT JOIN test_psychologist tps ON tps.id = r.test_psychologist_id
              LEFT JOIN patient p ON p.id = tp.patient_id
              LEFT JOIN psychologist psy ON psy.id = tps.psychologist_id
            WHERE r.test_id = :test_id '.$condition.' ORDER BY r.created_at DESC '.$setLimit;

    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);

    if(!empty($slimit)) {
      $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
      $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    }
    if(!empty($pat_id)) $query->bindValue(':pat_id', (int)$pat_id, PDO::PARAM_INT);
    if(!empty($psy_id)) $query->bindValue(':psy_id', (int)$psy_id, PDO::PARAM_INT);

    $query->execute();
    $row = $query->fetchAll();
    if (count($row) > 0) $total_data = $row[0]['total'];
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListResponse'.$e->getMessage();
  }
  return $result;
}

/**
 * getResponseAnswerCSVdownload
 * @param  int $test_id is test_id
 * @return array or boolean
 */
function getResponseAnswerCSVdownload($test_id)
{
  global $total_data;
  $resultListResponse = getListResponse($test_id, '', '', '');
  if(!empty($resultListResponse)){
    foreach ($resultListResponse as $key => $value) {
      $newResult[] = array('id' => $value['id'], 'score' => $value['score']);
      $result = getListResponseAnswer($value['id']);

      foreach ($result as $key => $va) {

        $newResult['answer'.$value['id']][] =  $va['que_title'];

        if(!empty($va['content'])){
          $newResult['answer'.$value['id']][] =  $va['content'];
        }else {
          if($va['calculate'] == 1){
            $newResult['answer'.$value['id']][] =  $va['amount'].' /No Calculate';
          }else {
            $newResult['answer'.$value['id']][] =  $va['amount'].' /Calculate';
          }
        }

      }

    }

    //Get max value of count result answer for gerate header csv
    $max_value = 0;
    foreach ($newResult as $key => $value) {
      if (count($newResult['answer'.$value['id']]) >= $max_value && count($newResult['answer'.$value['id']]) !== 0){
        $max_value = count($newResult['answer'.$value['id']]);
      }
    }
    $total_data = $max_value / 2;
  }

  return $newResult;
}
/**
 * listTestGroup
 * @param  int $testid is test_id
 * @param  string $lang is language
 * @return array or boolean
 */
function listTestGroup($testid, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';

    if(!empty($testid)) $condition .= ' AND tg.test_id = :test_id ';

    $sql =' SELECT tg.*, t.title AS title_test, COUNT(tgq.id) AS total_group_question,
              (SELECT COUNT(*) FROM `test_group` tg INNER JOIN test t ON t.id = tg.test_id WHERE t.lang = :lang '.$condition.') AS total
            FROM `test_group` tg
              INNER JOIN test t ON t.id = tg.test_id
              LEFT JOIN test_group_question tgq ON tgq.test_group_id = tg.id
            WHERE t.lang = :lang '.$condition.'
            GROUP BY tg.id ORDER BY tg.test_id ASC, tg.view_order ASC LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);

    if(!empty($testid)) $query->bindValue(':test_id', $testid, PDO::PARAM_INT);
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
    if($debug)  echo 'Errors: listTestGroup'.$e->getMessage();
  }

  return $result;
}
/**
 * listTestGroupQuestion
 * @param  int $tgroupid is test_group_id
 * @return array or boolean
 */
function listTestGroupQuestionAdmin($tgroupid)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql =' SELECT tgq.*, q.title AS que_title, q.type, q.description, ga.g_answer_title,
              (SELECT COUNT(*) FROM `test_group_question` tgq
                INNER JOIN test_question tq ON tq.id = tgq.test_question_id
                INNER JOIN question q ON q.id = tq.question_id
                LEFT JOIN group_answer ga ON ga.test_question_id = tgq.test_question_id
              WHERE tgq.test_group_id = :test_group_id) AS total
            FROM `test_group_question` tgq
              INNER JOIN test_question tq ON tq.id = tgq.test_question_id
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN group_answer ga ON ga.test_question_id = tgq.test_question_id
            WHERE tgq.test_group_id = :test_group_id LIMIT :offset, :limit';
    $query = $connected->prepare($sql);

    $query->bindValue(':test_group_id', $tgroupid, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: listTestGroupQuestion'.$e->getMessage();
  }

  return $result;
}
/**
 * is_exist_test_group_question
 * @param  int  $test_id
 * @param  int  $t_que_id is test question_id
 * @return boolean
 */
function is_exist_test_group_question($test_id, $t_que_id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `test_group` tg INNER JOIN test_group_question tgq ON tgq.test_group_id = tg.id WHERE tg.test_id = :test_id AND tgq.test_question_id = :t_que_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':t_que_id', (int)$t_que_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();

    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: is_exist_test_group_question'.$e->getMessage();
  }

  return $result;
}
/**
 * getStaffFunctionIsNullStaffPer
 * @param  int $srid is staff_role_id
 * @return array or boolean
 */
function getStaffFunctionIsNullStaffPer($srid)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT sf.id, sf.title FROM `staff_function` sf LEFT JOIN staff_permission sp ON sp.staff_function_id = sf.id AND sp.staff_role_id = :staff_role_id WHERE sp.id IS NULL ';
    $query = $connected->prepare($sql);
    $query->bindValue(':staff_role_id', (int)$srid, PDO::PARAM_INT);
    $query->execute();

    return $query->fetchAll();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getStaffFunctionIsNullStaffPer'.$e->getMessage();
  }

  return $result;
}
/**
 * checkDeleteMailerLite
 * @param  int $id mailerlite_id
 * @return boolean
 */
function checkDeleteMailerLite($id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count, ml.title FROM `apitransaction` apit
            INNER JOIN mailerlite ml ON ml.id = apit.ml_id WHERE apit.ml_id = :id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $query->execute();

    return $query->fetch();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkDeleteMailerLite'.$e->getMessage();
  }

  return $result;
}
/**
 * getListDownloadCSVfile
 * @param  int  $testid
 * @param  boolean $is_email
 * @param  int $status
 * @return array or boolean
 */
function getListDownloadCSVfile($testid, $is_email, $status)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;

  try{
    $condition = $where = '';
    if(!empty($testid)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' test_id = :test_id ';
    }
    if(!empty($is_email)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' is_email = :is_email ';
    }
    if(!empty($status)){
      if(!empty($condition)) $condition .= ' AND ';
      $condition .= ' status = :status ';
    }

    if(!empty($condition)) $where .= ' WHERE '.$condition;

    $sql =' SELECT *, (SELECT COUNT(*) FROM `download` '.$where.') AS total_count FROM `download` '.$where.' ORDER BY id DESC LIMIT :offset, :limit';
    $query = $connected->prepare($sql);

    if(!empty($testid)) $query->bindValue(':test_id', $testid, PDO::PARAM_INT);
    if(!empty($is_email)) $query->bindValue(':is_email', $is_email, PDO::PARAM_INT);
    if(!empty($status)) $query->bindValue(':status', $status, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total_count'];

    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListDownloadCSVfile'.$e->getMessage();
  }

  return $result;
}
/**
 * getViewCondtionTestQuestion
 * @param  int $nrid is new_result_id
 * @return array or boolean
 */
function getViewCondtionTestQuestion($nrid)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;

  try{
    $resultTestQCon = getListTestQuesCondition($nrid, $group_by = 1, '');
    $sumRowsTestQCon = COUNT($resultTestQCon) - 1;

    $ReCondional = '';

    foreach ($resultTestQCon as $key => $value) {

      $sql =' SELECT tqc.*, q.title AS q_title, q.description
            FROM `test_question_condition` tqc
             INNER JOIN test_question tq ON tq.id = tqc.test_question_id
             INNER JOIN question q ON q.id = tq.question_id
            WHERE tqc.new_result_id = :nrid AND tqc.test_question_id = :tqid ';
      $query = $connected->prepare($sql);
      $query->bindValue(':nrid', $nrid, PDO::PARAM_INT);
      $query->bindValue(':tqid', $value['test_question_id'], PDO::PARAM_INT);
      $query->execute();
      $rows = $query->fetchAll();

      $sumRow = COUNT($rows) - 1;

      $condition = '';

      foreach ($rows as $k => $va) {
        if($va['operator'] == 1){

          if($va['conditional'] == 1){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' > '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' > '.$va['value_condition'].' AND ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' > '.$va['value_condition'].') AND ';
              }else {
                $condition .= $va['q_title'].' > '.$va['value_condition'].' AND ';
              }

            }

          }//End conditional eq 1

          if($va['conditional'] == 2){

            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' < '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' < '.$va['value_condition'].' AND ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' < '.$va['value_condition'].') AND ';
              }else {
                $condition .= $va['q_title'].' < '.$va['value_condition'].' AND ';
              }
            }

          }//End conditional eq 2

          if($va['conditional'] == 3){

            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' = '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' = '.$va['value_condition'].' AND ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' = '.$va['value_condition'].') AND ';
              }else {
                $condition .= $va['q_title'].' = '.$va['value_condition'].' AND ';
              }
            }

          }//End conditional eq 3

          if($va['conditional'] == 4){

            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' >= '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' >= '.$va['value_condition'].' AND ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' >= '.$va['value_condition'].') AND ';
              }else {
                $condition .= $va['q_title'].' >= '.$va['value_condition'].' AND ';
              }
            }

          }//End conditional eq 4

          if($va['conditional'] == 5){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' <= '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' <= '.$va['value_condition'].' AND ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' <= '.$va['value_condition'].') AND ';
              }else {
                $condition .= $va['q_title'].' <= '.$va['value_condition'].' AND ';
              }
            }

          }//End conditional eq 5

        }else {

          if($va['conditional'] == 1){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' > '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' > '.$va['value_condition'].' OR ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' > '.$va['value_condition'].') OR ';
              }else {
                $condition .= $va['q_title'].' > '.$va['value_condition'].' OR ';
              }
            }

          }//End conditional eq 1

          if($va['conditional'] == 2){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' < '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' < '.$va['value_condition'].' OR ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' < '.$va['value_condition'].') OR ';
              }else {
                $condition .= $va['q_title'].' < '.$va['value_condition'].' OR ';
              }
            }

          }//End conditional eq 2

          if($va['conditional'] == 3){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' = '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' = '.$va['value_condition'].' OR ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' = '.$va['value_condition'].') OR ';
              }else {
                $condition .= $va['q_title'].' = '.$va['value_condition'].' OR ';
              }
            }

          }//End conditional eq 3

          if($va['conditional'] == 4){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' >= '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' >= '.$va['value_condition'].' OR ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' >= '.$va['value_condition'].') OR ';
              }else {
                $condition .= $va['q_title'].' >= '.$va['value_condition'].' OR ';
              }
            }

          }//End conditional eq 3

          if($va['conditional'] == 5){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' <= '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' <= '.$va['value_condition'].' OR ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' <= '.$va['value_condition'].') OR ';
              }else {
                $condition .= $va['q_title'].' <= '.$va['value_condition'].' OR ';
              }
            }

          }//End conditional eq 5

        }
      }//End foreach

      $ReCondional .= '('.$condition;
    }
    return $ReCondional;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getViewCondtionTestQuestion'.$e->getMessage();
  }
  return $result;
}
/**
 * checkDeleteTest
 * @param  string $id is test_id
 * @return array or boolean
 */
function checkDeleteTest($id)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql1= ' SELECT COUNT(*) AS total_count, t.title AS test_title FROM `result` r INNER JOIN test t ON t.id = r.test_id WHERE r.test_id = :id';
    $query1 = $connected->prepare($sql1);
    $query1->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $query1->execute();
    $row1 = $query1->fetch();

    $sql2= ' SELECT COUNT(*) AS total_response FROM `response` WHERE test_id = :id';
    $query2 = $connected->prepare($sql2);
    $query2->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $query2->execute();
    $row2 = $query2->fetch();

    $sql3= ' SELECT COUNT(*) total_count FROM `apitransaction` WHERE test_id = :id';
    $query3 = $connected->prepare($sql3);
    $query3->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $query3->execute();
    $row3 = $query3->fetch();

    $sum_total = $row1['total_count'] + $row2['total_response'] + $row3['total_count'];

    return array('test_title' => $row1['test_title'], 'total_response' => $row2['total_response'], 'total_count' => $sum_total);

  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: checkDeleteTest'.$e->getMessage();
  }
  return $result;
}
/**
* Search Report Activity log
* @param  int $staff_id is staff_id
* @param  string $act_log_fdate is activity_log date
* @param  string $act_log_tdate is activity_log date
* @param  int $slimit is slimit for assign to limit
* @return array or boolean
*/
function psychologist_activity($kwd, $psy_id, $gender)
{
 global $debug, $connected, $total_data, $limit, $offset;
 $result = true;
 if(!empty($slimit)) $limit = $slimit;

 try {
   $condition = '';
   if(!empty($kwd))
   {
     if(!empty($condition)) $condition .= ' AND ';
     $condition .= ' alog.content LIKE :kwd OR psy.username LIKE :kwd OR psy.address LIKE :kwd OR psy.job LIKE :kwd OR psy.email LIKE :kwd ';
   }
   if(!empty($psy_id))
   {
     if(!empty($condition)) $condition .= ' AND ';
     $condition .= ' alog.psychologist_id = :psy_id ';
   }
   if(!empty($gender))
   {
     if(!empty($condition)) $condition .= ' AND ';
     $condition .= ' psy.gender = :gender ';
   }

   if(!empty($condition)) $where .= ' WHERE '.$condition;

   $sql = 'SELECT alog.content, alog.created_at AS activity_date, psy.*, (SELECT COUNT(*) FROM `activity_log` alog
             INNER JOIN psychologist psy ON psy.id = alog.psychologist_id '.$where.' ) AS total FROM `activity_log` alog
             INNER JOIN psychologist psy ON psy.id = alog.psychologist_id '.$where.' ORDER BY alog.id DESC LIMIT :offset, :limit ';
   $stmt = $connected->prepare($sql);
   $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
   $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

   if(!empty($kwd))     $stmt->bindValue(':kwd', '%'.$kwd.'%', PDO::PARAM_STR);
   if(!empty($psy_id))  $stmt->bindValue(':psy_id', $psy_id, PDO::PARAM_INT);
   if(!empty($gender))  $stmt->bindValue(':gender', $gender, PDO::PARAM_INT);

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
/**
 * getListSection
 * @param  string $kwd
 * @param  int $parent_id
 * @return array or boolean
 */
function getListSection($kwd, $parent_id)
{
  global $debug, $connected, $total_data, $limit, $offset;
  $result = true;
  try
  {
    $condition = '';
    if(!empty($kwd))
    {
      $condition .=' AND name LIKE :kwd ';
    }

    $sql =' SELECT *, id AS parent, (SELECT COUNT(*) FROM section WHERE parent_id = parent) AS members,
              (SELECT COUNT(*) FROM section WHERE parent_id = :parent_id '.$condition.') AS total
            FROM `section`
            WHERE parent_id = :parent_id '.$condition.' LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);
    if(!empty($kwd)) $query->bindValue(':kwd', '%'.$kwd.'%', PDO::PARAM_STR);
    $query->bindValue(':parent_id', $parent_id, PDO::PARAM_INT);
    $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if(count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e)
  {
    if($debug) echo 'Error: getListSection'. $e->getMessage();
  }
  return $result;
}
/**
 * getListSectionAndSub
 * @param  int $parent_id
 * @return string
 */
function getListSectionAndSub($parent_id)
{
  global $debug, $connected, $total_data, $limit, $offset;
  $result = true;
  try
  {
    $sql =' SELECT * FROM `section` ';
    $query = $connected->prepare($sql);
    $query->execute();
    $rows = $query->fetchAll();

    $section = array('section' => array(), 'parent_sec' => array());

    foreach ($rows as $key => $row)
    {
      $section['section'][$row['id']] = $row;
      $section['parent_sec'][$row['parent_id']][] = $row['id'];
    }

    return buildSectionUlHTML($parent_id, $section);
  }
  catch (Exception $e)
  {
    if($debug) echo 'Error: getListSectionAndSub'. $e->getMessage();
  }
  return $result;
}

function buildSectionUlHTML($parent, $section)
{
  $html = "";
  // if (isset($section['parent_sec'][$parent]))
  // {
  //     $html .= "<ul>\n";
  //     foreach ($section['parent_sec'][$parent] as $cat_id)
  //     {
  //         if (!isset($section['parent_sec'][$cat_id]))
  //         {
  //             $html .= "<li>\n  <a href='" . $section['section'][$cat_id]['id'] . "'> A" . $section['section'][$cat_id]['name'] . "</a>\n</li> \n";
  //         }
  //
  //         if (isset($section['parent_sec'][$cat_id]))
  //         {
  //             $html .= "<li>\n  <a href='" . $section['section'][$cat_id]['id'] . "'> B" . $section['section'][$cat_id]['name'] . "</a> \n";
  //             $html .= buildCategory($cat_id, $section);
  //             $html .= "</li> \n";
  //         }
  //
  //     }
  //     $html .= "</ul> \n";
  // }

  if (isset($section['parent_sec'][$parent]))
  {
    $html .= "<ul class='ul_section'>\n";
    foreach ($section['parent_sec'][$parent] as $sec_id)
    {
      if (!isset($section['parent_sec'][$sec_id]))
      {
        $html .= "<li>\n <div class='radio' id='div_sec_".$section['section'][$sec_id]['id']."'><label><input type='radio' id='sec_".$section['section'][$sec_id]['id']."' name='section_id' value='".$section['section'][$sec_id]['id']."'>".$section['section'][$sec_id]['name']."</label></div> \n</li> \n";
      }

      if (isset($section['parent_sec'][$sec_id]))
      {
        $html .= "<li>\n <div class='radio' id='div_sec_".$section['section'][$sec_id]['id']."'><label><input type='radio' id='sec_".$section['section'][$sec_id]['id']."' name='section_id' value='".$section['section'][$sec_id]['id']."'>".$section['section'][$sec_id]['name']."</label></div> ";
        $html .= buildSectionUlHTML($sec_id, $section);
        $html .= "</li> \n";
      }
    }
    $html .= "</ul> \n";
  }
  return $html;
}
/**
 * getListSection
 * @param  string $kwd
 * @param  int $parent_id
 * @return string
 */
function getListSectionTestQuestion($kwd)
{
  global $debug, $connected, $total_data, $limit, $offset;
  $result = true;
  try
  {
    if(!empty($kwd)) $where .= ' WHERE s.name LIKE :kwd OR t.title LIKE :kwd OR q.title LIKE :kwd ';

    $sql =' SELECT stq.*, s.name, t.title AS test_title, q.title AS que_title, ga.g_answer_title,
              (SELECT COUNT(*) FROM `section_test_question` stq
                INNER JOIN section s ON s.id = stq.section_id
                INNER JOIN test_question tq ON tq.id = stq.test_question_id
                INNER JOIN test t ON t.id = tq.test_id
                INNER JOIN question q ON q.id = tq.question_id
                LEFT JOIN group_answer ga ON ga.test_question_id = stq.test_question_id '.$where.') AS total
            FROM `section_test_question` stq
              INNER JOIN section s ON s.id = stq.section_id
              INNER JOIN test_question tq ON tq.id = stq.test_question_id
              INNER JOIN test t ON t.id = tq.test_id
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN group_answer ga ON ga.test_question_id = stq.test_question_id '.$where;
    $query = $connected->prepare($sql);
    if(!empty($kwd)) $query->bindValue(':kwd', '%'.$kwd.'%', PDO::PARAM_STR);
    $query->execute();
    $rows = $query->fetchAll();
    if(count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e)
  {
    if($debug) echo 'Error: getListSectionTestQuestion'. $e->getMessage();
  }
  return $result;
}

/**
 * getListTestQuestionHide
 * @param  int $test_id
 * @return array or boolean
 */
function getListTestQuestionHide($test_id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;

  try{
    $sql =' SELECT tqh.*, q.title AS que_title, q.description, ga.g_answer_title, COUNT(tqhc.id) AS total_tqh,
              (SELECT COUNT(*) FROM `test_question_hide` tqh
                INNER JOIN test_question tq ON tq.id = tqh.test_question_id
                INNER JOIN question q ON q.id = tq.question_id
                LEFT JOIN group_answer ga ON ga.test_question_id = tqh.test_question_id
              WHERE tqh.test_id = :test_id) AS total
            FROM `test_question_hide` tqh
              INNER JOIN test_question tq ON tq.id = tqh.test_question_id
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN group_answer ga ON ga.test_question_id = tqh.test_question_id
              LEFT JOIN test_question_hide_condition tqhc ON tqhc.test_question_hide_id = tqh.id
            WHERE tqh.test_id = :test_id GROUP BY tqh.id LIMIT :offset, :limit ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', $test_id, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestQuestionHide'.$e->getMessage();
  }
  return $result;
}
/**
 * is_view_order_exist
 * @param  int  $test_id
 * @param  int  $test_que_id
 * @return array or boolean
 */
function isTestQuestionHideExist($test_id, $test_que_id)
{
  global $debug, $connected;
  $result = true;
  try
  {
    $sql= ' SELECT COUNT(*) AS total_count FROM `test_question_hide` WHERE test_id = :test_id AND test_question_id = :test_que_id ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':test_que_id', (int)$test_que_id, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: isTestQuestionHideExist'.$e->getMessage();
  }
  return $result;
}
/**
 * getListTestQuesHideCondition
 * @param  int $tqh_id is test_question_hide_id
 * @param  int $group_by for setting group
 * @param  int $slimit for setting limit
 * @return array or boolean
 */
function getListTestQuesHideCondition($tqh_id, $group_by, $slimit)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;

  try
  {
    if(!empty($slimit)) $limit = $slimit;
    if(!empty($group_by)) $set_group_by .= ' GROUP BY test_question_id ';
    if(!empty($slimit)) $setLimit .= ' LIMIT :offset, :limit ';

    $sql =' SELECT tqhc.*, q.title AS q_title, q.description, q.type, q.is_email, q.hide_title, q.lang, tq.view_order,
            (SELECT COUNT(*) FROM `test_question_hide_condition` tqhc INNER JOIN test_question tq ON tq.id = tqhc.test_question_id INNER JOIN question q ON q.id = tq.question_id WHERE tqhc.test_question_hide_id = :tqh_id) AS total
            FROM `test_question_hide_condition` tqhc
             INNER JOIN test_question tq ON tq.id = tqhc.test_question_id
             INNER JOIN question q ON q.id = tq.question_id
            WHERE tqhc.test_question_hide_id = :tqh_id '.$set_group_by.' ORDER BY tq.id ASC '.$setLimit;
    $query = $connected->prepare($sql);
    $query->bindValue(':tqh_id', $tqh_id, PDO::PARAM_INT);
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
    if($debug)  echo 'Errors: getListTestQuesHideCondition'.$e->getMessage();
  }
  return $result;
}
/**
 * getViewTestQuestionHideCondition
 * @param  int $tqh_id is test_question_hide_id
 * @return array or boolean
 */
function getViewTestQuestionHideCondition($tqh_id)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;

  try
  {
    $resultTestQHideCon = getListTestQuesHideCondition($tqh_id, $group_by = 1, '');
    $sumRowsTestQCon = COUNT($resultTestQHideCon) - 1;

    $ReCondional = '';

    foreach ($resultTestQHideCon as $key => $value) {

      $sql =' SELECT tqhc.*, q.title AS q_title, q.description
              FROM `test_question_hide_condition` tqhc
                INNER JOIN test_question tq ON tq.id = tqhc.test_question_id
                INNER JOIN question q ON q.id = tq.question_id
              WHERE tqhc.test_question_hide_id = :tqh_id AND tqhc.test_question_id = :tqid ORDER BY tqhc.id ASC ';
      $query = $connected->prepare($sql);
      $query->bindValue(':tqh_id', $tqh_id, PDO::PARAM_INT);
      $query->bindValue(':tqid', $value['test_question_id'], PDO::PARAM_INT);
      $query->execute();
      $rows = $query->fetchAll();

      $sumRow = COUNT($rows) - 1;

      $condition = '';

      foreach ($rows as $k => $va) {
        if($va['operator'] == 1){

          if($va['conditional'] == 1){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' > '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' > '.$va['value_condition'].' AND ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' > '.$va['value_condition'].') AND ';
              }else {
                $condition .= $va['q_title'].' > '.$va['value_condition'].' AND ';
              }

            }

          }//End conditional eq 1

          if($va['conditional'] == 2){

            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' < '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' < '.$va['value_condition'].' AND ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' < '.$va['value_condition'].') AND ';
              }else {
                $condition .= $va['q_title'].' < '.$va['value_condition'].' AND ';
              }
            }

          }//End conditional eq 2

          if($va['conditional'] == 3){

            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' = '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' = '.$va['value_condition'].' AND ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' = '.$va['value_condition'].') AND ';
              }else {
                $condition .= $va['q_title'].' = '.$va['value_condition'].' AND ';
              }
            }

          }//End conditional eq 3

          if($va['conditional'] == 4){

            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' >= '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' >= '.$va['value_condition'].' AND ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' >= '.$va['value_condition'].') AND ';
              }else {
                $condition .= $va['q_title'].' >= '.$va['value_condition'].' AND ';
              }
            }

          }//End conditional eq 4

          if($va['conditional'] == 5){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' <= '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' <= '.$va['value_condition'].' AND ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' <= '.$va['value_condition'].') AND ';
              }else {
                $condition .= $va['q_title'].' <= '.$va['value_condition'].' AND ';
              }
            }

          }//End conditional eq 5

        }else {

          if($va['conditional'] == 1){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' > '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' > '.$va['value_condition'].' OR ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' > '.$va['value_condition'].') OR ';
              }else {
                $condition .= $va['q_title'].' > '.$va['value_condition'].' OR ';
              }
            }

          }//End conditional eq 1

          if($va['conditional'] == 2){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' < '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' < '.$va['value_condition'].' OR ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' < '.$va['value_condition'].') OR ';
              }else {
                $condition .= $va['q_title'].' < '.$va['value_condition'].' OR ';
              }
            }

          }//End conditional eq 2

          if($va['conditional'] == 3){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' = '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' = '.$va['value_condition'].' OR ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' = '.$va['value_condition'].') OR ';
              }else {
                $condition .= $va['q_title'].' = '.$va['value_condition'].' OR ';
              }
            }

          }//End conditional eq 3

          if($va['conditional'] == 4){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' >= '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' >= '.$va['value_condition'].' OR ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' >= '.$va['value_condition'].') OR ';
              }else {
                $condition .= $va['q_title'].' >= '.$va['value_condition'].' OR ';
              }
            }

          }//End conditional eq 3

          if($va['conditional'] == 5){
            if($sumRowsTestQCon == $key){

              if($k == $sumRow){
                $condition .= $va['q_title'].' <= '.$va['value_condition'].') ';
              }else {
                $condition .= $va['q_title'].' <= '.$va['value_condition'].' OR ';
              }

            }else {

              if($k == $sumRow){
                $condition .= $va['q_title'].' <= '.$va['value_condition'].') OR ';
              }else {
                $condition .= $va['q_title'].' <= '.$va['value_condition'].' OR ';
              }
            }

          }//End conditional eq 5

        }
      }//End foreach

      $ReCondional .= '('.$condition;
    }
    return $ReCondional;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getViewCondtionTestQuestion'.$e->getMessage();
  }
  return $result;
}
/**
 * getListTestQuestionByNonHide
 * @param  int $test_id
 * @return array or boolean
 */
function getListTestQuestionByNonHide($test_id, $tqid)
{
  global $debug, $connected;
  $result = true;
  try
  {
    $sql= ' SELECT q.*, tqh.*, tq.id AS tq_id FROM `test_question` tq
              INNER JOIN question q ON q.id = tq.question_id
              LEFT JOIN test_question_hide tqh ON tqh.test_question_id = tq.id
            WHERE tq.test_id = :test_id AND q.type = 3 AND (tqh.test_question_id != :tqid OR tqh.id IS NULL) ';
    $query = $connected->prepare($sql);
    $query->bindValue(':test_id', (int)$test_id, PDO::PARAM_INT);
    $query->bindValue(':tqid', (int)$tqid, PDO::PARAM_INT);
    $query->execute();

    return $query->fetchAll();
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestQuestionByNonHide'.$e->getMessage();
  }
  return $result;
}
/**
 * [getListEmailTransaction description]
 * @param  int $mlid  is mailerLite_id
 * @param  int $catid is category_id
 * @param  string $lang is language
 * @return array or boolean
 */
function getListEmailTransaction($mlid, $catid, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $condition = '';

    if(!empty($catid)) $condition = ' AND apit.category_id = :catid';

    $sql =' SELECT apit.*, c.name AS cat_title, COUNT(gm.transaction_email_id) AS count_group,
              (SELECT COUNT(*) FROM (SELECT COUNT(*) FROM `apitransaction_email` apit
                INNER JOIN mailerlite_group_email gm ON gm.transaction_email_id = apit.id
                INNER JOIN category c ON c.id = apit.category_id
              WHERE apit.ml_id = :mlid '.$condition.' GROUP BY apit.id) AS group_by) AS total
            FROM `apitransaction_email` apit
              INNER JOIN mailerlite_group_email gm ON gm.transaction_email_id = apit.id
              INNER JOIN category c ON c.id = apit.category_id
            WHERE apit.ml_id = :mlid AND c.lang = :lang '.$condition.' GROUP BY apit.id LIMIT :offset, :limit';
    $query = $connected->prepare($sql);
    // echo $sql;
    if(!empty($catid)) $query->bindValue(':catid', $catid, PDO::PARAM_INT);

    $query->bindValue(':mlid', $mlid, PDO::PARAM_INT);
    $query->bindValue(':lang', $lang, PDO::PARAM_STR);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll();
    if (count($rows) > 0) $total_data = $rows[0]['total'];
    return $rows;
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListEmailTransaction'.$e->getMessage();
  }

  return $result;
}
/**
 * [is_exist_transaction_email description]
 * @param  int  $catid is category_id
 * @param  int  $mlid is mailerLite_id
 * @return boolean
 */
function is_exist_transaction_email($catid, $mlid)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `apitransaction_email` WHERE category_id = :catid AND ml_id = :mlid ';
    $query = $connected->prepare($sql);
    $query->bindValue(':catid', (int)$catid, PDO::PARAM_INT);
    $query->bindValue(':mlid', (int)$mlid, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: is_exist_transaction_email'.$e->getMessage();
  }
  return $result;
}
function countMailerLiteGroupEmail($trid)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `mailerlite_group_email` WHERE transaction_email_id = :trid ';
    $query = $connected->prepare($sql);
    $query->bindValue(':trid', (int)$trid, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetch();
    return $rows['total_count'];
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: countMailerLiteGroupEmail'.$e->getMessage();
  }
  return $result;
}
function getCountEmailTestCategory($catid)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;

  try{
    $sql =' SELECT COUNT(*) AS total_data FROM `response` r
            INNER JOIN response_answer ra ON ra.response_id = r.id
            INNER JOIN test t ON t.id = r.test_id
            WHERE t.category_id = :catid AND ra.is_email = 1 AND ra.content IS NOT NULL ';
    $query = $connected->prepare($sql);
    $query->bindValue(':catid', $catid, PDO::PARAM_INT);
    $query->execute();

    return $query->fetch();
  }
  catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getCountEmailTestCategory'.$e->getMessage();
  }

  return $result;
}
