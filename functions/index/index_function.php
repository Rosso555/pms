<?php
/**
 * psychologist_login
 * @param  string email
 * @param  string $password
 * @return array or boolean
 */
function psychologist_login($email, $password)
{
  global $debug, $connected, $total_data;
  $result = true;

  try{

    $sql = ' SELECT * FROM `psychologist` WHERE email = :email AND password = :password AND status = 2 ';
    $query = $connected->prepare($sql);
    $query->bindValue(':email', (string)$email, PDO::PARAM_STR);
    $query->bindValue(':password', (string)$password, PDO::PARAM_STR);
    $query->execute();

    return $query->fetch();
  }catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: psychologist_login'.$e->getMessage();
  }

  return $result;
}

/**
 * patient_login
 * @param  string $email
 * @param  string $password
 * @return array or boolean
 */
function patient_login($email, $password)
{
  global $debug, $connected, $total_data;
  $result = true;

  try{

    $sql = ' SELECT * FROM `patient` WHERE email = :email AND password = :password AND status = 1 ';
    $query = $connected->prepare($sql);
    $query->bindValue(':email', (string)$email, PDO::PARAM_STR);
    $query->bindValue(':password', (string)$password, PDO::PARAM_STR);
    $query->execute();

    return $query->fetch();
  }catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: patient_login'.$e->getMessage();
  }

  return $result;
}

?>
