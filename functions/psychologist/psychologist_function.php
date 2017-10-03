<?php
function getListTestPsychologistCompleted($psy_id, $lang)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{
    $sql ='   SELECT t.* FROM `test_psychologist` tpg INNER JOIN test t ON t.id = tpg.test_id WHERE tpg.psychologist_id = :psy_id AND tpg.status = 2 AND t.lang = :lang GROUP BY t.id ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':psy_id', (int)$psy_id, PDO::PARAM_INT);
    $stmt->bindValue(':lang', (string)$lang, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetchAll();
    return $row;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getListTestPsychologistCompleted'.$e->getMessage();
  }
  return $result;
}


 ?>
