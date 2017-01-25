<?php
/**
 * [getMultilang]
 * @param  [string] $lang
 * @author In khemarak
 * @return [array]
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
 * [is_staff_function_exist ]
 * @param  [string]  $task
 * @param  [string]  $action
 * @return boolean
 * @author In khemarak
 */
function is_staff_function_exits($task, $action)
{
  global $debug, $connected;
  $result = true;
  try{
    $sql= ' SELECT COUNT(*) AS total_count FROM `staff_function` WHERE task_name = :task AND action_name= :action';
    $query = $connected->prepare($sql);
    $query->bindValue(':task', (string)$task, PDO::PARAM_STR);
    $query->bindValue(':action', (string)$action, PDO::PARAM_STR);
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
 * [is_staff_permission_exist description]
 * @param  [string]  $table_name
 * @param  [string]  $role_id
 * @param  [int]  $function_id
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
 * [is_staff_role_exist ]
 * @param  [string]  $table_name
 * @param  [int]  $id
 * @return boolean
 * @author In khemarak
 */
function is_staff_role_exist($table_name, $id)
{
  //these values are set in setup.php
  global $debug, $connected;
  $result = true;
  try
  {
    $sql = 'SELECT COUNT(*) as total FROM '.$table_name.' WHERE staff_role_id = :id';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
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
 * [is_staff_function_exist ]
 * @param  [string]  $table_name
 * @param  [int]  $id
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
 * [is_lang_name_exist]
 * @param  [type]  $lang_name
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
 * check_user_email
 * @param  string $email is email address
 * @return boolean
 */
function check_user_email($email){
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
    if($debug)  echo 'Errors: check_user_email'.$e->getMessage();
  }

  return $result;
}
/**
 * checkSecretkey
 * @param  string $secretkey
 * @return boolean
 */
function checkSecretkey($psy_id ,$secretkey){
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
    if($debug)  echo 'Errors: checkSecretkey'.$e->getMessage();
  }

  return $result;
}



?>
