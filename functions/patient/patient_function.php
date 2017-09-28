<?php
function getCheckTestTmpQuestion($t_tmpid, $tqid, $type)
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try{

    $sql= ' SELECT ttq.*, q.type FROM `test_tmp_question` ttq INNER JOIN test_question tq ON tq.id = ttq.test_question_id INNER JOIN question q ON q.id = tq.question_id WHERE ttq.test_tmp_id = :t_tmpid AND ttq.test_question_id = :tqid ';
    $stmt = $connected->prepare($sql);
    $stmt->bindValue(':tqid', (int)$tqid, PDO::PARAM_INT);
    $stmt->bindValue(':t_tmpid', (int)$t_tmpid, PDO::PARAM_INT);
    $stmt->execute();
    if($type === 'one')
    {
      $rows = $stmt->fetch();
    } else {
      $rows = $stmt->fetchAll();
    }
    return $rows;
  } catch (Exception $e) {
    $result = false;
    if($debug)  echo 'Errors: getTestQuestionByTestQuesID'.$e->getMessage();
  }

  return $result;
}


?>
