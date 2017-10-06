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
/**
*@author: Mr. Viech Sross
*@param $kwd, $cate, $patient_id
*@return Array or boolean
*function get response answer for patient
*/
function get_test_response($kwd, $cate, $patient_id)
{
        global $debug, $connected, $limit, $offset, $total_data;
        $result = true;
        try {
                $condition = '';
                if (!empty($kwd)) {
                        $condition = " AND test.title LIKE :kwd ";
                }
                if(!empty($cate)){
                        $condition = " AND cate.id = :cate ";
                }
                $join = " INNER JOIN test on test.id = res.test_id
                          INNER JOIN test_patient AS tp ON tp.id = res.test_patient_id
                          INNER JOIN category as cate on cate.id = test.category_id WHERE tp.patient_id = :patient_id ";
                $sql = " SELECT res.*, cate.id AS cate_id, cate.name AS cate_name, test.title AS title, test.description AS description, (SELECT count(*) FROM response AS res ".$join.$condition.") AS total_count
                         FROM response AS res ".$join.$condition." ORDER BY res.id DESC LIMIT :offset, :limit ";
                $stmt = $connected->prepare($sql);
                $stmt->bindValue(':patient_id', (int)$patient_id, PDO::PARAM_INT);
                $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                if(!empty($cate)) $stmt->bindvalue(':cate', $cate);
                if(!empty($kwd)) $stmt->bindvalue(':kwd', '%'.$kwd. '%', PDO::PARAM_STR);
                $stmt->execute();
                $rows = $stmt->fetchAll();
                if(COUNT($rows > 0)) $total_data = $rows[0]['total_count'];
                return $rows;
        } catch (Exception $e) {
                $result = false;
                if($debug)  echo 'Errors: get_test_response'.$e->getMessage();
        }
        return $result;
}
/**
*@author: Mr. Viech Sross
*@param $patient_id
*@return Array or boolean
*function get psychologist by patient for sent email
*/
function get_psychologist_by_patient($patient_id)
{
        global $debug, $connected;
        $result = true;
        try {
                $join = " INNER JOIN psychologist AS psy ON psy.id = pat.psychologist_id WHERE pat.id = :patient_id ";
                $sql  = " SELECT pat.*,psy.username AS psy_username, psy.email AS psy_email FROM patient AS pat ".$join." ";
                $stmt = $connected->prepare($sql);
                $stmt->bindValue(':patient_id', (int)$patient_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch();
        } catch (Exception $e) {
                $result = false;
                if($debug)  echo 'Errors: get_psychologist_by_patient'.$e->getMessage();
        }
        return $result;
}
/**
*@author: Mr. Viech Sross
*@param $patient_id
*@return Array or boolean
*function get last response id WHERE patient login ID
*/
function get_last_response_id($patient_id)
{
        global $debug, $connected;
        $result = true;
        try {
                $sql = " SELECT res.*, res.test_patient_id AS tpid, tp.test_id AS tid FROM response AS res
                         INNER JOIN test_patient AS tp ON tp.id = res.test_patient_id
                         WHERE tp.patient_id = :patient_id ORDER BY res.id DESC LIMIT 1 ";
                $stmt = $connected->prepare($sql);
                $stmt->bindValue(':patient_id', (int)$patient_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch();
        } catch (Exception $e) {
                $result = false;
                if($debug)  echo 'Errors: get_last_response_id'.$e->getMessage();
        }
        return $result;
}
?>
