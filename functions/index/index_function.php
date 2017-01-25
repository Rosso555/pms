<?php
function psychologist_login($username, $password)
{
  global $debug, $connected, $total_data;
  $result = true;
  try{

    $sql = ' SELECT * FROM `psychologist` WHERE username = :username AND password = :password AND status = 2 ';
    $query = $connected->prepare($sql);
    $query->bindValue(':username', (string)$username, PDO::PARAM_STR);
    $query->bindValue(':password', (string)$password, PDO::PARAM_STR);
    $query->execute();
    return $query->fetch();

  }catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: psychologist_login'.$e->getMessage();
  }

  return $result;

}

 ?>
