<?php
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
      $condition .= ' WHERE title LIKE :kwd ';
    }

    $sql = 'SELECT * , (SELECT COUNT(*) FROM `staff_function` '.$condition.') AS total
             FROM `staff_function` '.$condition.' ORDER BY id DESC LIMIT :offset, :limit';
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
      $condition .= ' p.username LIKE :kwd ';
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

    $sql = ' SELECT p.*, psy.username AS psy_name, (SELECT COUNT(*) FROM `patient` p INNER JOIN psychologist psy ON psy.id = p.psychologist_id '.$where.') AS total
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
    $sql= ' SELECT COUNT(*) AS total_count, t.name FROM `test_question_topic` tqt INNER JOIN topic t ON t.id = tqt.topic_id WHERE tqt.topic_id = :id ';
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
