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
 * @param  string $kwd
 * @return array
 * @author In khemarak
 * @return array or boolean
 */
 function listMultiLang($kwd = "", $slimit)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $condition = '';

    if(!empty($slimit)) $limit = $slimit;
    if(!empty($kwd)) $condition .= ' WHERE key_lang LIKE :kwd ';

    $sql = ' SELECT ml.*, l.title AS language_title, (SELECT COUNT(*) FROM `multi_lang` ml INNER JOIN language l ON l.id = ml.language_id '.$condition.') AS total
                FROM `multi_lang` ml
                INNER JOIN language l ON l.id = ml.language_id '.$condition.' ORDER BY key_lang DESC LIMIT :offset, :limit ';
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
function ListStaffPermission($kwd = "")
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $condition = '';
    if(!empty($kwd))
    {
      $condition .= ' WHERE staff_role.name LIKE :kwd OR staff_function.title LIKE :kwd';
    }
    $sql = 'SELECT staff_permission.* ,staff_role.name AS staff_role_name, staff_function.title AS staff_function_title,(SELECT COUNT(*) FROM `staff_permission`
              INNER JOIN staff_role
              ON staff_permission.staff_role_id = staff_role.id
              INNER JOIN staff_function
              ON staff_permission.staff_function_id = staff_function.id
              '.$condition.') AS total
             FROM `staff_permission`
             INNER JOIN staff_role
             ON staff_permission.staff_role_id = staff_role.id
             INNER JOIN staff_function
             ON staff_permission.staff_function_id = staff_function.id
             '.$condition.' ORDER BY id DESC LIMIT :offset, :limit';
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
function ListStaffInfo($kwd = "")
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $condition = '';
    if(!empty($kwd))
    {
      $condition .= ' WHERE staff.name LIKE :kwd OR staff.status LIKE :kwd ';
    }
    $sql = 'SELECT staff.*, staff_role.name AS role_name, (SELECT COUNT(*) FROM `staff`  INNER JOIN staff_role ON staff.staff_role_id = staff_role.id '.$condition.') AS total
             FROM `staff`
             INNER JOIN staff_role
               ON staff.staff_role_id = staff_role.id
             '.$condition.' ORDER BY staff.id DESC LIMIT :offset, :limit';
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
    if($debug)  echo 'Errors: listStaff'.$e->getMessage();
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
