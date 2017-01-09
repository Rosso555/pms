<?php
/**
 * [ListStaffRole]
 * @param string $kwd
 * @author In khemarak
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
 * [ListStaffInfo]
 * @param string $kwd
 * @author In khemarak
 */
function ListStaffInfo($kwd = "")
{
  global $debug, $connected, $limit, $offset, $total_data;
  $result = true;
  try {
    $condition = '';
    if(!empty($kwd))
    {
      $condition .= ' WHERE staff.name LIKE :kwd ';
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
