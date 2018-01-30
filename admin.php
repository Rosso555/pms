<?php
//start session
session_start();

//require configuration file
require_once(dirname(__FILE__).'/setting/setup.php');
require_once(dirname(__FILE__).'/setting/common_setting.php');
require_once(dirname(__FILE__).'/functions/admin/admin_function.php');
require_once(dirname(__FILE__).'/functions/common/common_function.php');

//Get language By default_lang = 1
$result = $common->find('language', $condition = ['default_lang' => 1], $type = 'one');
//Setting language
if(!empty($_GET['lang'])) $lang = $_GET['lang'];
if(empty($lang) and empty($_SESSION['lang'])) $lang = $result['lang_name'];
if(!empty($lang) and empty($_SESSION['lang'])) $_SESSION['lang'] = $lang;
if(!empty($lang) and $lang != $_SESSION['lang']) $_SESSION['lang'] = $lang;
if(empty($lang) and $_SESSION['lang']) $lang = $_SESSION['lang'];
$smarty_appform->assign('mode', 'admin');
$smarty_appform->assign('admin_file', $admin_file);

//Smarty assign value
$smarty_appform->assign('lang_name', $result['lang_name']);

//list menu language
$smarty_appform->assign('getLanguage', $common->find('language', $condition = null, $type = 'all'));
//Change language on template
$smarty_appform->assign('multiLang', getMultilang($lang));

if(!empty($_GET['deflang']))
{
  //Update default_lang to zero
  updateDefaultLang();
  if(!empty($_GET['lid'])) $common->update('language', $field = ['default_lang' => 1], $condition = ['id' => $_GET['lid']]);
  header('location: '.$admin_file);
  exit;
}
//task: login
if('login' === $task)
{
  $error = array();
  if($_POST)
  {
    //get value from form
    $username = $common->clean_string($_POST['username']);
    $password = $common->clean_string($_POST['password']);
    //add value to session to use in template
    $_SESSION['admin'] = $_POST;
    //form validation
    if(empty($username))  $error['username']  = 1;
    if(empty($password))  $error['password']  = 1;

    if(0 === count($error))
    {
      $resultAdminLogin = admin_login($username, $password);
      //compare username and password in form
      if(!empty($resultAdminLogin))
      {
        //assign value to session
        $_SESSION['is_admin_login'] = $resultAdminLogin['id'];
        $_SESSION['staff_login_role'] = $resultAdminLogin['staff_role_id'];
        //Check is super admin
        if($resultAdminLogin['staff_role_id'] == 1){
          $_SESSION['is_super_admin'] = $resultAdminLogin['staff_role_id'];
        } else {
          $_SESSION['is_super_admin'] = '';
        }
        //remove session from post
        unset($_SESSION['admin']);
        //redirect to admin.php
        header('Location:'.$admin_file);
        exit;
      } else {
        //wrong username and password
        $error['login'] = 1;
      }
    }
  }
  //default of login task
  $smarty_appform->assign('error', $error);
  $smarty_appform->display('admin/login.tpl');
  exit;
}
//task: logout by clear session
if('logout' === $task)
{
  unset($_SESSION['is_admin_login']);
  unset($_SESSION['is_super_admin']);
  header('Location:'.$admin_file.'?task=login');
  exit;
}
//redirect if no session
if(empty($_SESSION['is_admin_login']))
{
  header('Location:'.$admin_file.'?task=login');
  exit;
}

//Check Staff Permission On template
$smarty_appform->assign('staffPermission', getStaffPermissionData($_SESSION['staff_login_role']));
//redirect if no permission
if($task == 'perror')
{
  $smarty_appform->assign('mode_file', $admin_file);
  $smarty_appform->display('common/permission_error.tpl');
  exit;
}

//Check permission
$permission = auth($_SESSION['staff_login_role'], $task, $action);
if(!empty($task) && false === $permission)
{
  //Check: If ajax request
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
  {
    header('Content-type: application/json');
    echo json_encode(false);
    exit;
  }
  header('location:'.$admin_file.'?task=perror');
  exit;
}

//task multi_language
if('multi_language' === $task)
{
  if(empty($_POST)) unset($_SESSION['translate']);

  $error = array();
  //Action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $key_lang   = $common->clean_string($_POST['key_lang']);
      $unique_id  = time();
      //add value to session to use in template
      $_SESSION['translate'] = $_POST;
      //form validation
      if(empty($key_lang))    $error['key_lang']  = 1;
      if(!empty($key_lang) && is_key_lang_exist($key_lang) > 0) $error['is_key_lang_exist'] = 2;

      //Add question
      if(0 === count($error))
      {
        foreach ($_POST['title'] as $k => $v) {
          $common->save('multi_lang', $field = ['unique_id' => $unique_id, 'language_id' => $_POST['language_id'][$k], 'title' => $_POST['title'][$k], 'key_lang' => $key_lang, 'lang' => $_POST['lang'][$k]]);
        }
        //unset session
        unset($_SESSION['translate']);
        //Redirect
        header('location: '.$admin_file.'?task=multi_language');
        exit;
      }
    }
  }//End Action add

  //get edit question
  if('edit' === $action && !empty($_GET['unique_id']))
  {
    if($_POST)
    {
      //get value from form
      $key_lang       = $common->clean_string($_POST['key_lang']);
      $unique_id      = time();
      //add value to session to use in template
      $_SESSION['translate'] = $_POST;
      //form validation
      if(empty($key_lang))    $error['key_lang']  = 1;
      $result = $common->find('multi_lang', $condition = ['unique_id' => $_GET['unique_id']], $type = 'one');
      if(!empty($key_lang) && $result['key_lang'] !== $key_lang && is_key_lang_exist($key_lang) > 0) $error['is_key_lang_exist'] = 2;

      //update question
      if(0 === count($error))
      {
        foreach ($_POST['title'] as $k => $v) {
          $common->update('multi_lang', $field = ['title' => $_POST['title'][$k], 'key_lang' => $key_lang, 'lang' => $_POST['lang'][$k]], $condition = ['id' => $_POST['id'][$k]]);
        }
        //unset session
        unset($_SESSION['translate']);
        //Redirect
        header('location: '.$admin_file.'?task=multi_language');
        exit;
      }
    }
    $smarty_appform->assign('getMultilangByUID', getMultilangByID($_GET['unique_id']));
  }

  //action delete question
  if('delete' === $action && !empty($_GET['unique_id']))
  {
    $common->delete('multi_lang', $field = ['unique_id' => $_GET['unique_id']]);
    header('location: '.$admin_file.'?task=multi_language');
    exit;
  }

  //Calculate for set limit
  $result_language = $common->find('language', $condition = null, $type = 'all');
  $slimit = COUNT($result_language) * 5;
  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $langs = listMultiLang($kwd, $slimit);
  // var_dump($langs);exit;
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  SmartyPaginate::setLimit($slimit);
  $smarty_appform->assign('langs', $langs);
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('language', $result_language);
  $smarty_appform->display('admin/multi_language.tpl');
  exit;
}
//Task Add Language
if('add_language' === $task)
{
  if(empty($_POST)) unset($_SESSION['add_language']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $title      = $common->clean_string($_POST['title']);
      $language   = $common->clean_string($_POST['language']);
      //add value to session to use in template
      $_SESSION['add_language'] = $_POST;
      //form validation
      if(empty($title))     $error['title'] = 1;
      if(empty($language))  $error['language'] = 1;
      if(!empty($language) && is_lang_name_exist($language) > 0) $error['is_lang_name_exist'] = 2;

      //Add language
      if(0 === count($error))
      {
        $language_id = $common->save('language', $field = ['title' => $title, 'lang_name' => $language]);
        $result_multil_lang = $common->find('multi_lang', $condition = ['lang' => 'en'], $type = 'all');
        foreach ($result_multil_lang as $key => $value) {
          $common->save('multi_lang', $field = ['unique_id' => $value['unique_id'], 'language_id' => $language_id, 'title' => $value['title'], 'key_lang' => $value['key_lang'], 'lang' => $language]);
        }
        //unset session
        unset($_SESSION['add_language']);
        //Redirect
        header('location: '.$admin_file.'?task=add_language');
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
  }//End action add

  //action exit edit language
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $id         = $common->clean_string($_POST['id']);
      $title      = $common->clean_string($_POST['title']);
      $language   = $common->clean_string($_POST['language']);
      //add value to session to use in template
      $_SESSION['add_language'] = $_POST;
      //form validation
      if(empty($title))     $error['title'] = 1;
      if(empty($language))  $error['language'] = 1;
      $result = $common->find('language', $condition = ['id' => $id], $type = 'one');
      if(!empty($language) && $result['lang_name'] !== $language && is_lang_name_exist($language) > 0) $error['is_lang_name_exist'] = 2;

      //update language
      if(0 === count($error) && !empty($id))
      {
        $common->update('language', $field = ['title' => $title, 'lang_name'  => $language], $condition = ['id' => $id]);
        $common->update('multi_lang', $field = ['lang' => $language], $condition = ['language_id' => $id]);
        //unset session
        unset($_SESSION['add_language']);
        //Redirect
        header('location: '.$admin_file.'?task=add_language');
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
    $smarty_appform->assign('getLanguageByID', $common->find('language', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action edit

  //action delete language
  if('delete' === $action && !empty($_GET['id']))
  {
    // $result = $common->find('language', $condition = ['id' => $_GET['id']], $type = 'one');
    $common->delete('multi_lang', $field = ['language_id' => $_GET['id']]);
    $common->delete('language', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=add_language');
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $result = listLanguage($kwd);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('listLanguage', $result);
  $smarty_appform->display('admin/add_language.tpl');
  exit;
}
//task category
if('category' === $task)
{
  if(empty($_POST)) unset($_SESSION['category']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $name = $common->clean_string($_POST['name']);
      //add value to session to use in template
      $_SESSION['category'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //Add test
      if(0 === count($error))
      {
        $common->save('category', $field = ['name' => $name, 'lang' => $lang]);
        //unset session
        unset($_SESSION['category']);
        //Redirect
        header('location: '.$admin_file.'?task=category');
        exit;
      }
    }
  }

  //action edit
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $name = $common->clean_string($_POST['name']);
      $id   = $common->clean_string($_POST['id']);
      //add value to session to use in template
      $_SESSION['category'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //update test
      if(0 === count($error) && !empty($id))
      {
        $common->update('category', $field = ['name' => $name, 'lang' => $lang], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['category']);
        //Redirect
        header('location: '.$admin_file.'?task=category');
        exit;
      }
    }
    $smarty_appform->assign('getCategoryByID', $common->find('category', $condition = ['id' => $_GET['id']], $type = 'one'));
  }

  //action delete category
  if('delete' === $action && !empty($_GET['id']))
  {
    $result = checkDeleteCategory($_GET['id']);
    if('0' === $result['total_count'])
    {
      $common->delete('category', $field = ['id' => $_GET['id']]);
    }else {
      setcookie('checkCategory', $result['name'], time() + 5);
    }
    header('location: '.$admin_file.'?task=category');
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $result = listCategory($kwd, $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('listCategory', $result);
  $smarty_appform->display('admin/category.tpl');
  exit;
}
//task: town_village
if('town_village' === $task)
{
  if(empty($_POST)) unset($_SESSION['category']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $name = $common->clean_string($_POST['name']);
      //add value to session to use in template
      $_SESSION['town_village'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //Add test
      if(0 === count($error))
      {
        $common->save('village', $field = ['name' => $name, 'lang' => $lang]);
        //unset session
        unset($_SESSION['town_village']);
        //Redirect
        header('location: '.$admin_file.'?task=town_village');
        exit;
      }
    }
  }

  //action edit
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $name = $common->clean_string($_POST['name']);
      $id   = $common->clean_string($_POST['id']);
      //add value to session to use in template
      $_SESSION['town_village'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //update test
      if(0 === count($error) && !empty($id))
      {
        $common->update('village', $field = ['name' => $name, 'lang' => $lang], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['town_village']);
        //Redirect
        header('location: '.$admin_file.'?task=town_village');
        exit;
      }
    }
    $smarty_appform->assign('getVillageByID', $common->find('village', $condition = ['id' => $_GET['id']], $type = 'one'));
  }

  //action delete category
  if('delete' === $action && !empty($_GET['id']))
  {
    $result = checkDeleteVillage($_GET['id']);

    if('0' === $result['total_count'])
    {
      $common->delete('village', $field = ['id' => $_GET['id']]);
    }else {
      setcookie('checkVillage', $result['name'], time() + 5);
    }
    header('location: '.$admin_file.'?task=town_village');
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $result = listVillage($kwd, $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('listVillage', $result);
  $smarty_appform->display('admin/admin_town_village.tpl');
  exit;
}
//task permission
if('staff_permission' === $task)
{
  $getsrid = (isset($_GET['sr_id']))? $_GET['sr_id']:'';
  //Clear session
  if(empty($_POST)) unset($_SESSION['staff_permission']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      $staff_function_id = $common->clean_string_array($_POST['staff_function_id']);
      $staff_role_id     = $common->clean_string($_POST['staff_role_id']);

      if(empty($staff_function_id)) $error['staff_function_id'] = 1;
      if(empty($staff_role_id))     $error['staff_role_id'] = 1;
      //Check existed
      if(!empty($staff_function_id) && empty($_POST['id'])){
        $existed_per = 0;
        foreach ($staff_function_id as $key => $value) {
          $existed_per += is_staff_permission_exist('staff_permission', $staff_role_id, $value);
        }
        if($existed_per > 0) $error['existed_per'] = 1;
      }
      $_SESSION['staff_permission'] = $_POST;

      if(0 === count($error))
      {
        foreach ($staff_function_id as $key => $value) {
          $common->save('staff_permission', $field = ['staff_function_id'=> $value, 'staff_role_id'=> $staff_role_id]);
        }
        //reset session
        unset($_SESSION['staff_permission']);
        //Redirect
        header('location: '.$admin_file.'?task=staff_permission');
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
  }//End action add

  //action edit staff permission
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      $spid = $common->clean_string($_POST['id']);
      $staff_function_id = $common->clean_string($_POST['staff_function_id']);
      $staff_role_id     = $common->clean_string($_POST['staff_role_id']);

      if(empty($staff_function_id)) $error['staff_function_id'] = 1;
      if(empty($staff_role_id))     $error['staff_role_id'] = 1;
      //Check existed
      if(!empty($staff_function_id) && !empty($_POST['id'])){
        $result = $common->find('staff_permission', $condition = ['id' => $_GET['id']], $type = 'one');
        if(($result['staff_function_id'] != $staff_function_id || $result['staff_role_id'] != $staff_role_id) && is_staff_permission_exist('staff_permission', $staff_role_id, $staff_function_id) > 0)
        {
          $error['existed_per'] = 1;
        }
      }
      $_SESSION['staff_permission'] = $_POST;

      if(0 === count($error) && !empty($spid))
      {
        $common->update('staff_permission', $field = ['staff_function_id' => $staff_function_id, 'staff_role_id' => $staff_role_id], $conditon = ['id' => $spid]);
        //reset session
        unset($_SESSION['staff_permission']);
        header('location: '.$admin_file.'?task=staff_permission');
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
    $smarty_appform->assign('edit_staff_permission', $common->find('staff_permission', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action edit

  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('staff_permission', $field = ['id'=>$_GET['id']]);
    header('location: '.$admin_file.'?task=staff_permission');
    exit;
  }
  //action get staff function is null in staff_permission
  if('staff_fun_permistion' === $action)
  {
    $results = getStaffFunctionIsNullStaffPer($_GET['srid']);
    header('Content-type: application/json');
    echo json_encode($results);
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $srid = !empty($_GET['srid']) ? $_GET['srid'] : '';

  $all_staffpermission = ListStaffPermission($kwd, $srid);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('search_by_role', ListStaffRoleByPermission());
  $smarty_appform->assign('list_Staff_Permission', $all_staffpermission);
  $smarty_appform->assign('list_staff_role', $common->find('staff_role', $condition = null, $type = 'all'));
  $smarty_appform->assign('list_staff_function', $common->find('staff_function', $condition = null, $type = 'all'));
  $smarty_appform->display('admin/staff_permission.tpl');
  exit;
}
//task staff Role
if('staff_role' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['staff_role']);

  $error = array();
  //Action add
  if('add' === $action)
  {
    if($_POST)
    {
      $id    = $common->clean_string($_POST['id']);
      $title = $common->clean_string($_POST['name']);
      $_SESSION['staff_role'] = $_POST;
      if(empty($title)) $error['title'] = 1;
      if(empty($id) && 0 === count($error))
      {
        $common->save('staff_role',$field = ['name' => $title]);
        $_SESSION['staff_role'] = '';
        unset($_SESSION['staff_role']);
        //Redirect
        header('location:'.$admin_file.'?task=staff_role');
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
  }//End action add

  //action edit
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      $id    = $common->clean_string($_POST['id']);
      $title = $common->clean_string($_POST['name']);
      $_SESSION['staff_role'] = $_POST;
      if(empty($title)) $error['title'] = 1;

      if(!empty($id) && 0 === count($error))
      {
        $common->update('staff_role', $field = ['name' => $title], $condition = ['id' => $id]);
        $_SESSION['staff_role'] = '';
        unset($_SESSION['staff_role']);
        //Redirect
        header('location:'.$admin_file.'?task=staff_role');
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
    $smarty_appform->assign('edit',$common->find('staff_role', $condition = ['id' => $_GET['id']], $type='one'));
  }//End action edit

  //action delete staff role
  if('delete' === $action && !empty($_GET['id']))
  {
    if(!empty($lang) && is_staff_role_exist($_GET['id']) > 0)
    {
      $error['exist_delete'] = 1;
    }else{
      $common->delete("staff_role", $field = ['id' => $_GET['id']]);
      header('location: '.$admin_file.'?task=staff_role');
      exit;
    }
  }

  $smarty_appform->assign('error', $error);
  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $results = ListStaffRole($kwd);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('list_staff_role', $results);
  $smarty_appform->display('admin/staff_role.tpl');
  exit;
}
//task staff information
if('staff_info' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['staff_info']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      $id       = $common->clean_string($_POST['id']);
      $name     = $common->clean_string($_POST['name']);
      $pass     = $common->clean_string($_POST['password']);
      $gender   = $common->clean_string($_POST['gender']);
      $phone    = $common->clean_string($_POST['phone']);
      $staff_role   = $common->clean_string($_POST['staff_role']);
      $old_fle = $common->clean_string($_POST['old_file']);

      //add value to session to use in template
      $_SESSION['staff_info'] = $_POST;
      //check validate form
      if(empty($name))   $error['name'] = 1;
      if(empty($pass))   $error['pass'] = 1;
      if(empty($phone))  $error['phone'] = 1;
      if(empty($staff_role))  $error['staff_role'] = 1;
      if(!empty($pass) && !$common->checkPassword($pass))
      {
        $error['password'] = 2;
      }
      if(!empty($_POST['old_file'])){
        $image = $old_fle;
      }

      if(!empty($_FILES['image']['name']))
      {
        if($_FILES['image']['size'] > $allows['SIZE'][0])  $error['size'] = 1;
        if(!in_array($_FILES['image']['type'], $allows['TYPE']['image'])) $error['type'] = 1;
      }
      if(empty($id) && 0 === count($error))
      {
        if(!empty($_FILES['image']) && !empty($_FILES['image']['name']))
        {
          $image = $common->uploadFile($_FILES, time(), IMAGE_PATH, 'image');
          if($_FILES['image']['error'] > 0)  $error['error'] = 1;
          //Generate thumbnail
          $images = new Zubrag_image;
          $images->max_x        = $thumbnail_width;
          $images->max_y        = $thumbnail_height;
          $images->save_to_file = 1;
          $images->image_type   = '-1';
          $thumbnail_image = IMAGE_PATH.'thumbnail__'.$image;
          $images->GenerateThumbFile(IMAGE_PATH.$image, $thumbnail_image);
        }

        $common->save('staff', $field =['name'      => $name,
        'password'  => $pass,
        'gender'    => $gender,
        'phone'     => $phone,
        'status'    => 1,
        'photo'     => $image,
        'staff_role_id' =>$staff_role]);
        $_SESSION['staff_info'] = '';
        unset($_SESSION['staff_info']);
        //Redirect
        header('location:'.$admin.'?task=staff_info');
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
  }//End action add

  //action edit
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      $id       = $common->clean_string($_POST['id']);
      $name     = $common->clean_string($_POST['name']);
      $pass     = $common->clean_string($_POST['password']);
      $gender   = $common->clean_string($_POST['gender']);
      $phone    = $common->clean_string($_POST['phone']);
      $staff_role   = $common->clean_string($_POST['staff_role']);
      $old_fle = $common->clean_string($_POST['old_file']);

      //add value to session to use in template
      $_SESSION['staff_info'] = $_POST;
      //check validate form
      if(empty($name))   $error['name'] = 1;
      if(empty($pass))   $error['pass'] = 1;
      if(empty($phone))  $error['phone'] = 1;
      if(empty($staff_role))  $error['staff_role'] = 1;
      if(!empty($pass) && !$common->checkPassword($pass))
      {
        $error['password'] = 2;
      }
      if(!empty($_POST['old_file'])){
        $image = $old_fle;
      }

      if(!empty($_FILES['image']['name']))
      {
        if($_FILES['image']['size'] > $allows['SIZE'][0])  $error['size'] = 1;
        if(!in_array($_FILES['image']['type'], $allows['TYPE']['image'])) $error['type'] = 1;
      }
      if(!empty($id) && 0 === count($error))
      {
        if(empty($_POST['old_fle'])){
          if(!empty($_FILES['image']) && !empty($_FILES['image']['name']))
          {
            $photo = $common->find('staff', $condition = ['id' => $id], $type = 'one');
            if(!empty($photo['photo']))
            {
              @unlink(IMAGE_PATH.$photo['photo']);
              @unlink(IMAGE_PATH.'thumbnail__'.$photo['photo']);
            }
            $image = $common->uploadFile($_FILES, time(), IMAGE_PATH, 'image');
            if($_FILES['image']['error'] > 0)  $error['error'] = 1;
            //Generate thumbnail
            $images = new Zubrag_image;
            $images->max_x        = $thumbnail_width;
            $images->max_y        = $thumbnail_height;
            $images->save_to_file = 1;
            $images->image_type   = '-1';
            $thumbnail_image = IMAGE_PATH.'thumbnail__'.$image;
            $images->GenerateThumbFile(IMAGE_PATH.$image, $thumbnail_image);
          }
        }
        $common->update('staff', $field =['name'      => $name,
        'password'  => $pass,
        'gender'    => $gender,
        'phone'     => $phone,
        'photo'     => $image,
        'staff_role_id' =>$staff_role], $condition = ['id' => $id]);
        $_SESSION['staff_info'] = '';
        unset($_SESSION['staff_info']);
        //Redirect
        header('location:'.$admin_file.'?task=staff_info');
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
    $smarty_appform->assign('edit',$common->find('staff', $condition = ['id' => $_GET['id']], $type='one'));
  }//action edit

  //action change status
  if('change_status' === $action && !empty($_GET['id']))
  {
    if(!empty($_GET['status'] == 1))
    {
      $common->update('staff', $field = ['status' => 2], $condition = ['id' => $_GET['id']]);
    }elseif (!empty($_GET['status'] == 2)) {
      $common->update('staff', $field = ['status' => 1], $condition = ['id' => $_GET['id']]);
    }
    header('location:'.$admin_file.'?task=staff_info');
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $status = !empty($_GET['status']) ? $_GET['status'] : '';
  $results = ListStaffInfo($kwd, $status);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('list_staff', $results);
  $smarty_appform->assign('staff_role', $common->find('staff_role', $condition = null, $type = 'all'));
  $smarty_appform->display('admin/staff_info.tpl');
  exit;
}
//task staff function
if('staff_function' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['staff_function']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      $id       = $common->clean_string($_POST['id']);
      $title    = $common->clean_string($_POST['title']);
      $task     = $common->clean_string($_POST['task']);
      $action   = $common->clean_string($_POST['action']);
      $_SESSION['staff_function'] = $_POST;
      //check validate form
      if(empty($title))   $error['title'] = 1;
      if(empty($task))    $error['task'] = 1;
      if(!empty($task) && empty($action) && is_staff_function_exits($task, '') > 0) $error['is_staff_fun_exist_task'] = 2;
      if(!empty($task) && !empty($action) && is_staff_function_exits($task, $action) > 0) $error['is_staff_fun_exist_action'] = 2;

      if(!empty($action)){
        $rAction = $action;
      } else {
        $rAction = NULL;
      }
      if(count($error) === 0 && empty($id))
      {
        $common->save('staff_function',$field = ['title' => $title, 'task_name' => $task, 'action_name' => $action]);
        $_SESSION['staff_function'] = '';
        unset($_SESSION['staff_function']);
        //Redirect
        header('location:'.$admin_file.'?task=staff_function');
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
  }//End action add

  //action edit
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      $id       = $common->clean_string($_POST['id']);
      $title    = $common->clean_string($_POST['title']);
      $task     = $common->clean_string($_POST['task']);
      $action   = $common->clean_string($_POST['action']);
      $_SESSION['staff_function'] = $_POST;
      //check validate form
      if(empty($title))   $error['title'] = 1;
      if(empty($task))    $error['task'] = 1;
      //Get Staff Function
      $resultSFun = $common->find('staff_function', $condition = ['id' => $id], $type='one');
      if($resultSFun['task_name'] !== $task || $resultSFun['action_name'] !== $action){
        if(!empty($task) && empty($action) && is_staff_function_exits($task, '') > 0) $error['is_staff_fun_exist_task'] = 2;
        if(!empty($task) && !empty($action) && is_staff_function_exits($task, $action) > 0) $error['is_staff_fun_exist_action'] = 2;
      }

      if(!empty($action)){
        $rAction = $action;
      } else {
        $rAction = NULL;
      }

      if(count($error) === 0 && !empty($id))
      {
        $common->update('staff_function', $field = ['title' => $title, 'task_name' => $task, 'action_name' => $action], $condition = ['id' => $id]);
        $_SESSION['staff_function'] = '';
        unset($_SESSION['staff_function']);
        //Redirect
        header('location:'.$admin_file.'?task=staff_function');
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
    $smarty_appform->assign('edit', $common->find('staff_function', $condition = ['id' => $_GET['id']], $type='one'));
  }

  //action delete staff function
  if('delete' === $action && !empty($_GET['id']))
  {
    if(!empty($lang) && is_staff_function_exist('staff_permission', $_GET['id']) > 0)
    {
      $error['exist_delete'] = 1;
    }else{
      $common->delete("staff_function", $field = ['id' => $_GET['id']]);
      header('location: '.$admin_file.'?task=staff_function');
      exit;
    }
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $list_function = listFunction($kwd);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('list_function', $list_function);
  $smarty_appform->display('admin/staff_function.tpl');
  exit;
}
//Task: patient
if('patient' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['patient']);

  $error = array();
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $id     = $common->clean_string($_POST['id']);
      $psy_id = $common->clean_string($_POST['psy_id']);
      $username = $common->clean_string($_POST['username']);
      $email  = $common->clean_string($_POST['email']);
      $phone  = $common->clean_string($_POST['phone']);
      $gender = $common->clean_string($_POST['gender']);
      $age    = $common->clean_string($_POST['age']);
      $code   = $common->clean_string($_POST['code']);
      $password = $common->clean_string($_POST['password']);
      $re_code_pat = getPsychologistByIdCodePat($psy_id);
      $r_code = $re_code_pat['code_pat'].$code;

      //add value to session to use in template
      $_SESSION['patient'] = $_POST;
      //form validation
      if(empty($username))  $error['username']  = 1;
      if(empty($email))   $error['email']   = 1;
      if(empty($phone))   $error['phone']   = 1;
      if(empty($gender))  $error['gender']  = 1;
      if(empty($age))     $error['age']     = 1;
      if(empty($code))    $error['code']  = 1;
      if(empty($password))$error['password']  = 1;
      if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error['invalid_email'] = 1;
      }
      if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
        $result = $common->find('patient', $condition = ['id' => $_GET['id']], $type='one');
        if($result['email'] !== $email && check_patient_email($email) > 0){
          $error['exist_email'] = 1;
        }
      }
      if(check_code_pat($r_code) > 0) $error['code_existed']  = 1;

      //Save
      if(empty($id) && COUNT($error) === 0){
        $common->save('patient', $field =['psychologist_id' => $psy_id,
                                          'username'=> $username,
                                          'email'   => $email,
                                          'phone'   => $phone,
                                          'gender'  => $gender,
                                          'age'     => $age,
                                          'password'=> $password,
                                          'code'    => $r_code]);
        //unset session
        unset($_SESSION['patient']);
        //Redirect
        header('location: '.$admin_file.'?task=patient');
        exit;
      }
      //if submit error.
      $smarty_appform->assign('psyCodePat', getPsychologistByIdCodePat($_SESSION['patient']['psy_id']));
    }
  }//End action add

  //Action: edit
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $id     = $common->clean_string($_POST['id']);
      $psy_id = $common->clean_string($_POST['psy_id']);
      $username = $common->clean_string($_POST['username']);
      $email  = $common->clean_string($_POST['email']);
      $phone  = $common->clean_string($_POST['phone']);
      $gender = $common->clean_string($_POST['gender']);
      $age    = $common->clean_string($_POST['age']);
      $code   = $common->clean_string($_POST['code']);
      $password = $common->clean_string($_POST['password']);
      $re_code_pat = getPsychologistByIdCodePat($psy_id);
      $r_code = $re_code_pat['code_pat'].$code;
      //add value to session to use in template
      $_SESSION['patient'] = $_POST;
      //form validation
      if(empty($username))$error['username']  = 1;
      if(empty($email))   $error['email']   = 1;
      if(empty($phone))   $error['phone']   = 1;
      if(empty($gender))  $error['gender']  = 1;
      if(empty($age))     $error['age']     = 1;
      if(empty($code))    $error['code']  = 1;
      if(empty($password))$error['password']  = 1;
      if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error['invalid_email'] = 1;
      }
      if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
        $result = $common->find('patient', $condition = ['id' => $_GET['id']], $type='one');
        if($result['email'] !== $email && check_patient_email($email) > 0){
          $error['exist_email'] = 1;
        }
      }
      //Update
      if(!empty($id) && COUNT($error) === 0){
        $common->update('patient', $field= ['psychologist_id' => $psy_id,
                                            'username' => $username,
                                            'email'    => $email,
                                            'phone'    => $phone,
                                            'gender'   => $gender,
                                            'age'      => $age,
                                            'password' => $password,
                                            'code'     => $r_code], $condition = ['id' => $_GET['id']]);
        //unset session
        unset($_SESSION['patient']);
        //Redirect
        header('location: '.$admin_file.'?task=patient');
        exit;
      }
    }
    $resultPat = $common->find('patient', $condition = ['id' => $_GET['id']], $type='one');
    $smarty_appform->assign('psyCodePat', getPsychologistByIdCodePat($resultPat['psychologist_id']));
    $smarty_appform->assign('editPatient', $resultPat);
  }

  //Change staus patient
  if('change_status' === $action && !empty($_GET['id']))
  {
    if(!empty($_GET['status'] == 1))
    {
      $common->update('patient', $field = ['status' => 2], $condition = ['id' => $_GET['id']]);
    }elseif (!empty($_GET['status'] == 2)) {
      $common->update('patient', $field = ['status' => 1], $condition = ['id' => $_GET['id']]);
    }
    header('location:'.$admin_file.'?task=patient');
    exit;
  }

  //action delete staff role
  if('delete' === $action && !empty($_GET['id']))
  {
    $deleted_at = date("Y-m-d");
    $common->update('patient', $field = ['deleted_at' => $deleted_at], $condition = ['id' => $_GET['id']]);
    header('location:'.$admin_file.'?task=patient');
    exit;
  }

  if('get_code_pwd' === $action)
  {
    $psy_id = $common->clean_string($_GET['psy_id']);

    $results = getPsychologistByIdCodePat($psy_id);
    header('Content-type: application/json');
    echo json_encode($results);
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $gender = !empty($_GET['gender']) ? $_GET['gender'] : '';
  $status = !empty($_GET['status']) ? $_GET['status'] : '';
  $psy_id = !empty($_GET['psy_id']) ? $_GET['psy_id'] : '';
  $result = listPatientAdmin($kwd, $psy_id, $gender, $status);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listPatient', $result);
  $smarty_appform->assign('listPsychologist', $common->find('psychologist', $condition = null, $type = 'all'));
  $smarty_appform->display('admin/admin_patient.tpl');
  exit;
}
//Task: psychologist
if('psychologist' === $task)
{
  //Clear session
  if(!$_POST) unset($_SESSION['psychologist']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $id     = $common->clean_string($_POST['id']);
      $first_name = $common->clean_string($_POST['first_name']);
      $last_name  = $common->clean_string($_POST['last_name']);
      $village  = $common->clean_string($_POST['village']);
      $gender   = $common->clean_string($_POST['gender']);
      $age      = $common->clean_string($_POST['age']);
      $password = $common->clean_string($_POST['password']);
      $email    = $common->clean_string($_POST['email']);
      $job      = $common->clean_string($_POST['job']);
      $address  = $common->clean_string($_POST['address']);

      //add value to session to use in template
      $_SESSION['psychologist'] = $_POST;
      //form validation
      if(empty($first_name))$error['first_name']  = 1;
      if(empty($last_name)) $error['last_name']  = 1;
      if(empty($village))   $error['village']  = 1;
      if(empty($gender))    $error['gender']  = 1;
      if(empty($age))       $error['age']  = 1;
      if(empty($password))  $error['password']  = 1;
      if(empty($email))     $error['email']   = 1;
      if(empty($job))       $error['job']   = 1;
      if(empty($address))   $error['address']   = 1;

      if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error['invalid_email'] = 1;
      }
      if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
        $result = $common->find('psychologist', $condition = ['id' => $_GET['id']], $type='one');
        if($result['email'] !== $email &&  check_psychologist_email($email) > 0){
          $error['exist_email'] = 1;
        }
      }
      //Save
      if(empty($id) && COUNT($error) === 0){
        $common->save('psychologist', $field = ['first_name'  => $first_name,
                                                'last_name'   => $last_name,
                                                'village_id'  => $village,
                                                'gender'  => $gender,
                                                'age'     => $age,
                                                'email'   => $email,
                                                'job'     => $job,
                                                'password'=> $password,
                                                'address' => $address]);
        //unset session
        unset($_SESSION['psychologist']);
        //Redirect
        header('location: '.$admin_file.'?task=psychologist');
        exit;
      }
    }
  }//End action add

  //Action: edit
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $id     = $common->clean_string($_POST['id']);
      $first_name = $common->clean_string($_POST['first_name']);
      $last_name  = $common->clean_string($_POST['last_name']);
      $village  = $common->clean_string($_POST['village']);
      $gender   = $common->clean_string($_POST['gender']);
      $age      = $common->clean_string($_POST['age']);
      $password = $common->clean_string($_POST['password']);
      $email    = $common->clean_string($_POST['email']);
      $job      = $common->clean_string($_POST['job']);
      $address  = $common->clean_string($_POST['address']);

      //add value to session to use in template
      $_SESSION['psychologist'] = $_POST;
      //form validation
      if(empty($first_name))$error['first_name']  = 1;
      if(empty($last_name)) $error['last_name']  = 1;
      if(empty($village))   $error['village']  = 1;
      if(empty($gender))    $error['gender']  = 1;
      if(empty($age))       $error['age']  = 1;
      if(empty($password))  $error['password']  = 1;
      if(empty($email))     $error['email']   = 1;
      if(empty($job))       $error['job']   = 1;
      if(empty($address))   $error['address']   = 1;

      if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error['invalid_email'] = 1;
      }
      if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
        $result = $common->find('psychologist', $condition = ['id' => $_GET['id']], $type='one');
        if($result['email'] !== $email &&  check_psychologist_email($email) > 0){
          $error['exist_email'] = 1;
        }
      }
      //Update
      if(!empty($id) && COUNT($error) === 0){
        $common->update('psychologist', $field=['username' => $username,
        'email'    => $email,
        'job'      => $job,
        'password' => $password,
        'address'  => $address], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['psychologist']);
        //Redirect
        header('location: '.$admin_file.'?task=psychologist');
        exit;
      }
    }
    $smarty_appform->assign('editPsychologist', $common->find('psychologist', $condition = ['id' => $_GET['id']], $type='one'));
  }//End action edit

  //Change staus psychologist
  if('change_status' === $action && !empty($_GET['id']))
  {
    if(!empty($_GET['status'] == 1))
    {
      $common->update('psychologist', $field = ['status' => 2], $condition = ['id' => $_GET['id']]);
    }elseif (!empty($_GET['status'] == 2)) {
      $common->update('psychologist', $field = ['status' => 1], $condition = ['id' => $_GET['id']]);
    }
    header('location:'.$admin_file.'?task=psychologist');
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $status = !empty($_GET['status']) ? $_GET['status'] : '';
  $psy_id = !empty($_GET['psy_id']) ? $_GET['psy_id'] : '';

  $result = listPsychologistAdmin($kwd, $psy_id, $status);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listPsychologistData', $result);
  $smarty_appform->assign('listPsychologist', $common->find('psychologist', $condition = null, $type = 'all'));
  $smarty_appform->assign('village', $common->find('village', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->display('admin/admin_psychologist.tpl');
  exit;
}
//task question
if('question' === $task)
{
  //Clear session
  if(!$_POST) unset($_SESSION['question']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $id     = $common->clean_string($_POST['id']);
      $title  = $common->clean_string($_POST['title']);
      $type   = $common->clean_string($_POST['type']);
      $desc   = $common->clean_string($_POST['description']);
      $is_email   = $common->clean_string($_POST['is_email']);
      $hide_title = $common->clean_string($_POST['hide_title']);

      //add value to session to use in template
      $_SESSION['question'] = $_POST;
      //form validation
      if(empty($title))   $error['title'] = 1;
      if(empty($type))    $error['type']  = 1;
      if(empty($desc))    $error['desc']  = 1;

      //Add question
      if(0 === count($error) && empty($id))
      {
        $common->save('question', $field = ['title' => $title, 'description' => $desc, 'type' => $type, 'is_email' => $is_email, 'hide_title' => $hide_title, 'lang' => $lang]);
        //unset session
        unset($_SESSION['question']);
        //Redirect
        header('location: '.$admin_file.'?task=question');
        exit;
      }
    }
  }//End action add

  //get edit question
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $id     = $common->clean_string($_POST['id']);
      $title  = $common->clean_string($_POST['title']);
      $type   = $common->clean_string($_POST['type']);
      $desc   = $common->clean_string($_POST['description']);
      $is_email   = $common->clean_string($_POST['is_email']);
      $hide_title = $common->clean_string($_POST['hide_title']);

      //add value to session to use in template
      $_SESSION['question'] = $_POST;
      //form validation
      if(empty($title))   $error['title'] = 1;
      if(empty($type))    $error['type']  = 1;
      if(empty($desc))    $error['desc']  = 1;

      //update question
      if(0 === count($error) && !empty($id))
      {
        $common->update('question', $field = ['title' => $title, 'description' => $desc, 'type' => $type, 'is_email' => $is_email, 'hide_title' => $hide_title], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['question']);
        //Redirect
        header('location: '.$admin_file.'?task=question');
        exit;
      }
    }
    $smarty_appform->assign('getQuestionByID', getQuestionByID($_GET['id']));
  }//End action edit

  //action delete question
  if('delete' === $action && !empty($_GET['id']))
  {
    $result = checkDeleteQuestion($_GET['id']);
    if('0' === $result['total_count'])
    {
      $common->delete('question', $field = ['id' => $_GET['id'], 'lang' => $lang]);
    }else {
      setcookie('checkQuestion', $result['question_title'], time() + 10);
    }

    header('location: '.$admin_file.'?task=question');
    exit;

  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $listQuestion = listQuestion($kwd, $lang);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listQuestion', $listQuestion);
  $smarty_appform->display('admin/admin_question.tpl');
  exit;
}
//Task test question
if('test_question' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['test_question']);

  $error = array();
  //acton add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $test_question_id = $common->clean_string($_POST['test_question_id']);
      $test_id      = $common->clean_string($_POST['test']);
      $question_id  = $common->clean_string($_POST['question']);
      $view_order   = $common->clean_string($_POST['view_order']);
      $required     = $common->clean_string($_POST['required']);
      $copy_test_que= $common->clean_string($_POST['copy_test_que']);

      //add value to session to use in template
      $_SESSION['test_question'] = $_POST;
      //form validation
      if(empty($test_id))     $error['test'] = 1;
      if(empty($question_id)) $error['question'] = 1;
      if(empty($view_order))  $error['view_order'] = 1;
      if(is_exist_test_question($test_id, $question_id) > 0) $error['is_exist_test_que'] = 1;

      //Add test question
      if(0 === count($error) && empty($test_question_id))
      {
        $tque_id = $common->save('test_question', $field = ['test_id'     => $test_id,
        'question_id' => $question_id,
        'view_order'  => $view_order,
        'is_required' => $required]);
        // //Get answer
        $rAnswer = $common->find('answer', $condition = ['test_question_id' => $copy_test_que], $type = 'all');
        //Get question
        $rQuestion = $common->find('question', $condition = ['id' => $question_id], $type = 'one');
        //Get test question
        $rTestues = $common->find('test_question', $condition = ['id' => $copy_test_que], $type = 'one');

        if(!empty($rAnswer) && $rQuestion['type'] == 3 || $rQuestion['type'] == 4){
          //Fetch anser
          foreach ($rAnswer as $key => $value) {
            //Get answer_topic
            $rAnswerTopic = $common->find('answer_topic', $condition = ['answer_id' => $value['id']], $type = 'all');
            if(!empty($rAnswerTopic)){
              foreach ($rAnswerTopic as $key => $va) {
                if(checkTopicResult($test_id, $va['topic_id']) == 0){
                  $rResult = $common->find('result', $condition = ['test_id' => $rTestues['test_id'], 'topic_id' => $va['topic_id']], $type = 'all');
                  //fetch save result
                  foreach ($rResult as $key => $v) {
                    $common->save('result', $field = ['test_id' => $test_id, 'topic_id' => $v['topic_id'], 'score_from' => $v['score_from'], 'score_to' => $v['score_to'], 'message' => $v['message']]);
                  }//end fetch $rResult
                }
              }//end fetch $rAnswerTopic
            }

            // Save answer
            $answer_id = $common->save('answer', $field = ['title' => $value['title'], 'test_question_id' => $tque_id, 'view_order' => $value['view_order'], 'calculate' => $value['calculate']]);
            if(!empty($rAnswerTopic)){
              foreach ($rAnswerTopic as $key => $va) {
                //Save answer_topic
                $common->save('answer_topic', $field = ['answer_id' => $answer_id, 'topic_id'  => $va['topic_id'], 'default_value' => $va['default_value'], 'weight_value' => $va['weight_value']]);
              }//end fetch $rAnswerTopic
            }
          }//end Fetch anser

        }
        //unset session
        unset($_SESSION['test_question']);
        //Redirect
        if($_GET['kwd'] || $_GET['tid'] || $_GET['next']){
          header('location: '.$admin_file.'?task=test_question&tid='.$_GET['tid'].'&kwd='.$_GET['kwd'].'&next='.$_GET['next']);
        }else {
          header('location: '.$admin_file.'?task=test_question');
        }
        exit;
      }
      $smarty_appform->assign('listAnswer', $common->find('answer', $condition = ['test_question_id' => $copy_test_que], $type = 'all'));
    }
  }//End action add

  //get edit test question
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $test_question_id = $common->clean_string($_POST['test_question_id']);
      $test_id      = $common->clean_string($_POST['test']);
      $question_id  = $common->clean_string($_POST['question']);
      $view_order   = $common->clean_string($_POST['view_order']);
      $required     = $common->clean_string($_POST['required']);
      $copy_test_que= $common->clean_string($_POST['copy_test_que']);

      //add value to session to use in template
      $_SESSION['test_question'] = $_POST;
      //form validation
      if(empty($test_id))     $error['test'] = 1;
      if(empty($question_id)) $error['question'] = 1;
      if(empty($view_order))  $error['view_order'] = 1;
      //Check is existed topic
      $rTestQues = $common->find('test_question', $condition = ['id' => $test_question_id], $type = 'one');
      if($rTestQues['test_id'] !== $test_id && $rTestQues['question_id'] !== $question_id && is_exist_test_question($test_id, $question_id) > 0) {
        $error['is_exist_test_que']   = 1;
      }

      //update test question
      if(0 === count($error) && !empty($test_question_id))
      {
        $common->update('test_question', $field =['test_id'     => $test_id,
        'question_id' => $question_id,
        'view_order'  => $view_order,
        'is_required' => $required], $condition = ['id' => $test_question_id]);
        //unset session
        unset($_SESSION['test_question']);
        //Redirect
        if($_GET['kwd'] || $_GET['tid'] || $_GET['next']){
          header('location: '.$admin_file.'?task=test_question&tid='.$_GET['tid'].'&kwd='.$_GET['kwd'].'&next='.$_GET['next']);
        }else {
          header('location: '.$admin_file.'?task=test_question');
        }
        exit;
      }
    }
    $smarty_appform->assign('getTestQByID', $common->find('test_question', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action edit

  //action delete
  if('delete' === $action && !empty($_GET['id']))
  {
    $tqid = $common->clean_string($_GET['id']);
    $tid  = $common->clean_string($_GET['tid']);
    $kwd  = $common->clean_string($_GET['kwd']);
    $next = $common->clean_string($_GET['next']);

    $result = checkDeleteTestQuestion($tqid, $tid);
    if('0' === $result['total_count'])
    {
      $rTestQues = $common->find('answer', $condition = ['test_question_id' => $tqid], $type = 'all');
      if(!empty($rTestQues)){
        foreach ($rTestQues as $key => $value) {
          $common->delete('answer_topic', $field = ['answer_id' => $value['id']]);
        }
      }
      $common->delete('answer', $field = ['test_question_id' => $tqid]);
      $common->delete('test_question', $field = ['id' => $tqid]);
      $common->delete('test_group_question', $field = ['test_question_id' => $tqid]);
      $common->delete('group_answer', $field = ['test_question_id' => $tqid]);
      $common->delete('test_question_topic', $field = ['test_question_id' => $tqid]);
      $common->delete('test_question_view_order', $field = ['test_question_id' => $tqid]);
      $common->delete('test_question_condition', $field = ['test_question_id' => $tqid]);
      $common->delete('test_tmp_question', $field = ['test_question_id' => $tqid]);
    }else {
      setcookie('test_que_id', $tqid, time() + 10);
      setcookie('checkTestQues_Qtitle', $result['que_title'], time() + 10);
      setcookie('checkTestQues_Ttitle', $result['test_title'], time() + 10);
    }
    //Redirect
    if($kwd || $tid || $next){
      header('location: '.$admin_file.'?task=test_question&tid='.$tid.'&kwd='.$kwd.'&next='.$next);
    }else {
      header('location: '.$admin_file.'?task=test_question');
    }
    exit;
  }

  //action delete permanently
  if('delete_permanently' === $action && !empty($_GET['id']))
  {
    $tqid = $common->clean_string($_GET['id']);
    $tid  = $common->clean_string($_GET['tid']);
    $kwd  = $common->clean_string($_GET['kwd']);
    $next = $common->clean_string($_GET['next']);

    //Get response answer
    $rResponseAns = $common->find('response_answer', $condition = ['test_question_id' => $tqid], $type = 'all');
    //Get Answer
    $rTestQues = $common->find('answer', $condition = ['test_question_id' => $tqid], $type = 'all');
    if(!empty($rTestQues)){
      foreach ($rTestQues as $key => $value) {
        $common->delete('answer_topic', $field = ['answer_id' => $value['id']]);
      }
    }
    $common->delete('answer', $field = ['test_question_id' => $tqid]);
    $common->delete('test_question', $field = ['id' => $tqid]);
    $common->delete('test_group_question', $field = ['test_question_id' => $tqid]);
    $common->delete('group_answer', $field = ['test_question_id' => $tqid]);
    $common->delete('test_question_topic', $field = ['test_question_id' => $tqid]);
    $common->delete('test_question_view_order', $field = ['test_question_id' => $tqid]);
    $common->delete('test_question_condition', $field = ['test_question_id' => $tqid]);
    $common->delete('test_tmp_question', $field = ['test_question_id' => $tqid]);
    $common->delete('response_answer', $field = ['test_question_id' => $tqid]);

    setcookie('test_que_id', $tqid, time() - 10);
    setcookie('checkTestQues_Qtitle', $result['que_title'], time() - 10);
    setcookie('checkTestQues_Ttitle', $result['test_title'], time() - 10);

    //Redirect
    if($kwd || $tid || $next) {
      header('location: '.$admin_file.'?task=test_question&tid='.$tid.'&kwd='.$kwd.'&next='.$next);
    } else {
      header('location: '.$admin_file.'?task=test_question');
    }
    exit;
  }

  //action list_answer
  if('list_answer' === $action)
  {
    $tqid = $common->clean_string($_GET['tqid']);
    $results = $common->find('answer', $condition = ['test_question_id' => $tqid], $type = 'all');
    header('Content-type: application/json');
    echo json_encode($results);
    exit;
  }

  //action check_que_type
  if('check_que_type' === $action)
  {
    $results = $common->find('question', $condition = ['id' => $_GET['qid']], $type = 'one');
    header('Content-type: application/json');
    echo json_encode($results);
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $testid = !empty($_GET['tid']) ? $_GET['tid'] : '';

  $result = getListTestQuestion($kwd, $testid, '', '', $lang, $slimit = 10);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listTestQuestion', $result);
  $smarty_appform->assign('listTestQuestionCopy', getListTestQuestion('', '', '', $type_check = 3, $lang, ''));
  $smarty_appform->assign('test', $common->find('test', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->assign('question', $common->find('question', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->display('admin/admin_test_question.tpl');
  exit;
}
//task answer
if('answer' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['answer']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $title  = $common->clean_string($_POST['title']);
      $tqid   = $common->clean_string($_POST['tqid']);
      $asid   = $common->clean_string($_POST['asid']);
      $qid    = $common->clean_string($_POST['qid']);
      $view_order = $common->clean_string($_POST['view_order']);
      $calculate  = $common->clean_string($_POST['calculate']);
      $default_value  = $view_order;
      $weight_value   = 1;

      //add value to session to use in template
      $_SESSION['answer'] = $_POST;
      //form validation
      if(empty($tqid))  $error['tqid']  = 1;
      if(empty($title)) $error['title']  = 1;
      if(empty($view_order))  $error['view_order'] = 1;

      $checkTypeQues  = $common->find('question', $condition = ['id' => $qid], $type = 'one');
      if(!empty($checkTypeQues['type'] == 1) || !empty($checkTypeQues['type'] == 2))
      {
        $checkAnswer  = $common->find('answer', $condition = ['test_question_id' => $tqid], $type = 'one');
        if(!empty($checkAnswer) && count($checkAnswer) > 1 && empty($asid))  $error['checkAnswer']  = 1;
      }

      //Add Answer
      if(0 === count($error) && empty($asid))
      {
        $result = getTopicResult($_GET['tid']);
        $answer_id = $common->save('answer', $field = ['title' => $title, 'test_question_id' => $tqid, 'calculate' => $calculate, 'view_order' => $view_order]);
        if(!empty($result)){
          foreach ($result as $key => $value) {
            $common->save('answer_topic', $field = ['answer_id' => $answer_id,
            'topic_id'  => $value['topic_id'],
            'default_value' => $default_value,
            'weight_value'  => $weight_value]);
          }
        }
        //unset session
        unset($_SESSION['answer']);
        //Redirect
        header('location: '.$admin_file.'?task=answer&tid='.$_GET['tid'].'&qid='.$_GET['qid'].'&tqid='.$tqid);
        exit;
      }
    }
  }//End action add

  //get edit answer
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $title  = $common->clean_string($_POST['title']);
      $tqid   = $common->clean_string($_POST['tqid']);
      $asid   = $common->clean_string($_POST['asid']);
      $qid    = $common->clean_string($_POST['qid']);
      $view_order = $common->clean_string($_POST['view_order']);
      $calculate  = $common->clean_string($_POST['calculate']);
      $default_value  = $view_order;
      $weight_value   = 1;

      //add value to session to use in template
      $_SESSION['answer'] = $_POST;
      //form validation
      if(empty($tqid))  $error['tqid']  = 1;
      if(empty($title)) $error['title']  = 1;
      if(empty($view_order))  $error['view_order'] = 1;

      $checkTypeQues  = $common->find('question', $condition = ['id' => $qid], $type = 'one');
      if(!empty($checkTypeQues['type'] == 1) || !empty($checkTypeQues['type'] == 2))
      {
        $checkAnswer  = $common->find('answer', $condition = ['test_question_id' => $tqid], $type = 'one');
        if(!empty($checkAnswer) && count($checkAnswer) > 1 && empty($asid))  $error['checkAnswer']  = 1;
      }

      //update Answer
      if(0 === count($error) && !empty($asid))
      {
        $common->update('answer', $field = ['title' => $title, 'calculate' => $calculate, 'view_order' => $view_order], $condition = ['id' => $asid]);
        $common->update('answer_topic', $field = ['default_value' => $default_value], $condition = ['answer_id' => $asid]);
        //unset session
        unset($_SESSION['answer']);
        //Redirect
        header('location: '.$admin_file.'?task=answer&tid='.$_GET['tid'].'&qid='.$_GET['qid'].'&tqid='.$tqid);
        exit;
      }
    }
    $smarty_appform->assign('getAnswerByID', $common->find('answer', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action edit

  //action delete answer
  if('delete' === $action && !empty($_GET['id']))
  {
    $result = checkDeleteAnswer($_GET['id']);
    if('0' === $result['total_count'])
    {
      $common->delete('answer', $field = ['id' => $_GET['id']]);
      $common->delete('answer_topic', $field = ['answer_id' => $_GET['id']]);
    }else {
      setcookie('answer_id', $_GET['id'], time() + 10);
      setcookie('checkAnswer', $result['title'], time() + 10);
    }

    header('location: '.$admin_file.'?task=answer&tid='.$_GET['tid'].'&qid='.$_GET['qid'].'&tqid='.$_GET['tqid']);
    exit;
  }

  //action delete permanently answer
  if('delete_permanently' === $action && !empty($_GET['id']))
  {
    //Get response answer
    $rResponseAns = $common->find('response_answer', $condition = ['answer_id' => $_GET['id']], $type = 'all');

    $common->delete('answer', $field = ['id' => $_GET['id']]);
    $common->delete('answer_topic', $field = ['answer_id' => $_GET['id']]);
    $common->delete('response_answer', $field = ['answer_id' => $_GET['id']]);

    foreach ($rResponseAns as $key => $value) {
      $rSumResAns = sumResponseAnswer($value['response_id'], $_GET['tid']);
      $common->update('response', $field = ['score' => $rSumResAns], $condition = ['id' => $value['response_id']]);
    }

    setcookie('answer_id', $_GET['id'], time() - 10);
    setcookie('checkAnswer', $result['title'], time() - 10);

    header('location: '.$admin_file.'?task=answer&tid='.$_GET['tid'].'&qid='.$_GET['qid'].'&tqid='.$_GET['tqid']);
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $listAnswer = listAnswer($_GET['tqid'], $kwd);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listAnswer', $listAnswer);
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
  $smarty_appform->assign('question', $common->find('question', $condition = ['id' => $_GET['qid']], $type = 'one'));
  $smarty_appform->assign('list_question', $common->find('question', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->display('admin/admin_answer.tpl');
  exit;
}
//task answer
if('answer_topic' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['answer_topic']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $default_value  = $common->clean_string($_POST['default_value']);
      $assign_value   = $common->clean_string($_POST['assign_value']);
      $weight_value   = $common->clean_string($_POST['weight_value']);
      $topic_id       = $common->clean_string($_POST['topic']);
      $answer_id      = $common->clean_string($_POST['ans_id']);
      $id = $common->clean_string($_POST['id']);

      if(empty($weight_value)) $weight_value = 1;
      //add value to session to use in template
      $_SESSION['answer_topic'] = $_POST;
      //form validation
      if(empty($answer_id)) $error['asid']  = 1;
      if(empty($topic_id))  $error['topic_id']  = 1;
      if(empty($default_value)) $error['default_value'] = 1;
      //Check is existed topic
      if(!empty($answer_id) && !empty($topic_id) && is_exist_answer_topic($answer_id, $topic_id) > 0) {
        $error['is_exist_topic']   = 1;
      }

      //Add Answer
      if(0 === count($error) && empty($id))
      {
        $common->save('answer_topic', $field = ['answer_id' => $answer_id,
        'topic_id'  => $topic_id,
        'default_value' => $default_value,
        'assign_value'  => $assign_value,
        'weight_value'  => $weight_value]);
        //unset session
        unset($_SESSION['answer_topic']);
        //Redirect
        header('location: '.$admin_file.'?task=answer_topic&tid='.$_GET['tid'].'&qid='.$_GET['qid'].'&tqid='.$_GET['tqid'].'&ans_id='.$_GET['ans_id']);
        exit;
      }
    }
  }//End action add

  //get edit answer topic
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $default_value  = $common->clean_string($_POST['default_value']);
      $assign_value   = $common->clean_string($_POST['assign_value']);
      $weight_value   = $common->clean_string($_POST['weight_value']);
      $topic_id       = $common->clean_string($_POST['topic']);
      $answer_id      = $common->clean_string($_POST['ans_id']);
      $id = $common->clean_string($_POST['id']);

      if(empty($weight_value)) $weight_value = 1;
      //add value to session to use in template
      $_SESSION['answer_topic'] = $_POST;
      //form validation
      if(empty($answer_id)) $error['asid']  = 1;
      if(empty($topic_id)) $error['topic_id']  = 1;
      if(empty($default_value)) $error['default_value'] = 1;
      //Check is existed topic
      $rAnsTopic = $common->find('answer_topic', $condition = ['id' => $id], $type = 'one');
      if($rAnsTopic['topic_id'] !== $topic_id && is_exist_answer_topic($answer_id, $topic_id) > 0) {
        $error['is_exist_topic']   = 1;
      }

      //update Answer
      if(0 === count($error) && !empty($id))
      {
        $common->update('answer_topic', $field = ['answer_id' => $answer_id,
        'topic_id'  => $topic_id,
        'default_value' => $default_value,
        'assign_value'  => $assign_value,
        'weight_value'  => $weight_value], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['answer_topic']);
        //Redirect
        header('location: '.$admin_file.'?task=answer_topic&tid='.$_GET['tid'].'&qid='.$_GET['qid'].'&tqid='.$_GET['tqid'].'&ans_id='.$_GET['ans_id']);
        exit;
      }
    }
    $smarty_appform->assign('getAnswerTopicByID', $common->find('answer_topic', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action edit

  //action delete answer topic
  if('delete' === $action && !empty($_GET['id']))
  {
    $rAnswerTopic = $common->find('answer_topic', $condition = ['id' => $_GET['id']], $type = 'one');
    $rTopic = $common->find('topic', $condition = ['id' => $rAnswerTopic['topic_id']], $type = 'one');
    $result = checkDeleteAnswer($rAnswerTopic['answer_id']);

    if('0' === $result['total_count'])
    {
      $common->delete('answer_topic', $field = ['id' => $_GET['id']]);
    }else {
      setcookie('checkAnswer', $result['title'], time() + 10);
      setcookie('topicTitle', $rTopic['name'], time() + 10);
      setcookie('ans_topic_id', $_GET['id'], time() + 10);
    }

    header('location: '.$admin_file.'?task=answer_topic&tid='.$_GET['tid'].'&qid='.$_GET['qid'].'&tqid='.$_GET['tqid'].'&ans_id='.$_GET['ans_id']);
    exit;
  }

  //action delete permanently answer topic
  if('delete_permanently' === $action && !empty($_GET['id']))
  {
    $common->delete('answer_topic', $field = ['id' => $_GET['id']]);
    $rAnswerTopic = $common->find('answer_topic', $condition = ['answer_id' => $_GET['ans_id']], $type = 'all');
    //Get response answer
    $rResponseAns = $common->find('response_answer', $condition = ['answer_id' => $_GET['ans_id']], $type = 'all');
    foreach ($rResponseAns as $key => $value) {
      $rSumResAns = sumResponseAnswer($value['response_id'], $_GET['tid']);
      $common->update('response', $field = ['score' => $rSumResAns], $condition = ['id' => $value['response_id']]);
    }

    if(COUNT($rAnswerTopic) === 0){
      deleteResponseAnswer($_GET['tid'], $_GET['ans_id']);
    }

    setcookie('checkAnswer', $result['title'], time() - 10);
    setcookie('topicTitle', $rTopic['title'], time() - 10);
    setcookie('ans_topic_id', $_GET['id'], time() - 10);

    header('location: '.$admin_file.'?task=answer_topic&tid='.$_GET['tid'].'&qid='.$_GET['qid'].'&tqid='.$_GET['tqid'].'&ans_id='.$_GET['ans_id']);
    exit;
  }

  $listAnsTopic = getListAnswerTopic($_GET['ans_id']);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listAnswerTopic', $listAnsTopic);
  $smarty_appform->assign('topicResult', getTopicResult($_GET['tid']));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
  $smarty_appform->assign('answer', $common->find('answer', $condition = ['id' => $_GET['ans_id']], $type = 'one'));
  $smarty_appform->assign('question', $common->find('question', $condition = ['id' => $_GET['qid']], $type = 'one'));
  $smarty_appform->display('admin/admin_answer_topic.tpl');
  exit;
}
//Task jump_to
if('jump_to' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['session_jump']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $question_id  = $common->clean_string($_POST['question']);
      //add value to session to use in template
      $_SESSION['session_jump'] = $_POST;

      //form validation
      if(empty($question_id)) $error['question'] = 1;
      //update test question
      if(0 === count($error) && !empty($_GET['ans_id']))
      {
        $common->update('answer', $field = ['jump_to' => $question_id], $condition = ['id' => $_GET['ans_id']]);
        //unset session
        unset($_SESSION['session_jump']);
        //Redirect
        header('location: '.$admin_file.'?task=jump_to&tid='.$_GET['tid'].'&qid='.$_GET['qid'].'&tqid='.$_GET['tqid'].'&ans_id='.$_GET['ans_id']);
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
  }//End action add

  // check condition when it has parent_id
  if('edit' === $action && !empty($_GET['ans_id']))
  {
    if($_POST)
    {
      //get value from form
      $question_id  = $common->clean_string($_POST['question']);
      //add value to session to use in template
      $_SESSION['session_jump'] = $_POST;

      //form validation
      if(empty($question_id)) $error['question'] = 1;
      //update test question
      if(0 === count($error) && !empty($_GET['ans_id']))
      {
        $common->update('answer', $field = ['jump_to' => $question_id], $condition = ['id' => $_GET['ans_id']]);
        //unset session
        unset($_SESSION['session_jump']);
        //Redirect
        header('location: '.$admin_file.'?task=jump_to&tid='.$_GET['tid'].'&qid='.$_GET['qid'].'&tqid='.$_GET['tqid'].'&ans_id='.$_GET['ans_id']);
        exit;
      }
      $smarty_appform->assign('error', $error);
    }
    $smarty_appform->assign('jump_toByID', $common->find('answer', $condition = ['id' => $_GET['ans_id']], $type = 'one'));
  }//End action edit

  //action delete category
  if('delete' === $action && !empty($_GET['ans_id']))
  {
    $common->update('answer', $field = ['jump_to' => NULL], $condition = ['id' => $_GET['ans_id']]);
    header('location: '.$admin_file.'?task=jump_to&tid='.$_GET['tid'].'&qid='.$_GET['qid'].'&tqid='.$_GET['tqid'].'&ans_id='.$_GET['ans_id']);
    exit;
  }

  $results = getListQuestionByViewOrderGroupNonGroupJumpTo($_GET['tid'], $lang);

  $currentViewOrder = 0;
  foreach ($results['question'] as $k => $va) {
    if($va['test_question_id'] === $_GET['tqid']) $currentViewOrder = $k;
  }

  $smarty_appform->assign('listTestQuestion', $results);
  $smarty_appform->assign('answer', $common->find('answer', $condition = ['id' => $_GET['ans_id']], $type = 'one'));
  $smarty_appform->assign('question', $common->find('question', $condition = ['id' => $_GET['qid'], 'lang' => $lang], $type = 'one'));
  $smarty_appform->assign('currentViewOrder', $currentViewOrder);
  $smarty_appform->assign('question_jump_to', getListQuestionJumpTo($_GET['ans_id']));
  $smarty_appform->display('admin/admin_jump_to.tpl');
  exit;
}
//task test
if('test' === $task)
{
  //Clear session
  if(!$_POST) unset($_SESSION['test']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $title    = $common->clean_string($_POST['title']);
      $grapTitle= $common->clean_string($_POST['graphic_title']);
      $desc     = $common->clean_string($_POST['description']);
      $category = $common->clean_string($_POST['category']);
      $id       = $common->clean_string($_POST['id']);
      $catid    = $common->clean_string($_GET['catid']);
      //add value to session to use in template
      $_SESSION['test'] = $_POST;

      //form validation
      if(empty($title))     $error['title'] = 1;
      if(empty($category))  $error['category'] = 1;

      //Add test
      if(0 === count($error) && empty($id))
      {
        $testid = $common->save('test', $field = ['category_id' => $category,'title' => $title, 'graph_title' => $grapTitle, 'description' => $desc, 'lang' => $lang]);
        //unset session
        unset($_SESSION['test']);
        //Redirect
        if($_GET['catid']){
          header('location: '.$admin_file.'?task=test&catid='.$catid);
        }else {
          header('location: '.$admin_file.'?task=test');
        }
        exit;
      }
    }
  }//End action add

  //get edit test
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $title    = $common->clean_string($_POST['title']);
      $grapTitle= $common->clean_string($_POST['graphic_title']);
      $desc     = $common->clean_string($_POST['description']);
      $category = $common->clean_string($_POST['category']);
      $id       = $common->clean_string($_POST['id']);
      $catid    = $common->clean_string($_GET['catid']);
      //add value to session to use in template
      $_SESSION['test'] = $_POST;

      //form validation
      if(empty($title))     $error['title'] = 1;
      if(empty($category))  $error['category'] = 1;

      //update test
      if(0 === count($error) && !empty($id))
      {
        $common->update('test', $field = ['category_id' => $category, 'title' => $title, 'graph_title' => $grapTitle, 'description' => $desc], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['test']);
        //Redirect
        if($catid){
          header('location: '.$admin_file.'?task=test&catid='.$catid);
        }else {
          header('location: '.$admin_file.'?task=test');
        }
        exit;
      }
    }
    $smarty_appform->assign('getTestByID', $common->find('test', $condition = ['id' => $id], $type = 'one'));
    $smarty_appform->assign('getTestTopicByID', $common->find('test_topic_analysis', $condition = ['test_id' => $id], $type = 'all'));
  }//End action edit

  if('change_status' === $action && $_GET['id'])
  {
    $tid    = $common->clean_string($_GET['id']);
    $catid  = $common->clean_string($_GET['catid']);

    if(!empty($_GET['status'] == 1))
    {
      $common->update('test', $field = ['status' => 2], $condition = ['id' => $tid]);
    }elseif (!empty($_GET['status'] == 2)) {
      $common->update('test', $field = ['status' => 1], $condition = ['id' => $tid]);
    }
    //Redirect
    if($_GET['catid']){
      header('location: '.$admin_file.'?task=test&catid='.$catid);
    }else {
      header('location: '.$admin_file.'?task=test');
    }
    exit;
  }

  //action delete test
  if('delete' === $action && !empty($_GET['id']))
  {
    $tid    = $common->clean_string($_GET['id']);
    $catid  = $common->clean_string($_GET['catid']);
    $kwd    = $common->clean_string($_GET['kwd']);
    $next   = $common->clean_string($_GET['next']);

    $result = checkDeleteTest($tid);

    if(0 === $result['total_count'])
    {
      $common->delete('test', $field = ['id' => $tid, 'lang' => $lang]);
      $common->delete('test_topic_analysis', $field = ['test_id' => $tid]);
    }else {
      setcookie('checkTest', $result['test_title'], time() + 10);
      setcookie('checkResponse', $result['total_response'], time() + 10);
      setcookie('test_id', $tid, time() + 10);
    }
    if($_GET['catid']){
      header('location: '.$admin_file.'?task=test&catid='.$catid.'&tid='.$tid.'&kwd='.$kwd.'&next='.$next);
    }else {
      header('location: '.$admin_file.'?task=test');
    }
    exit;
  }

  //action delete permanently test
  if('delete_permanently' === $action && !empty($_GET['id']))
  {
    $tid    = $common->clean_string($_GET['id']);
    $catid  = $common->clean_string($_GET['catid']);
    $kwd    = $common->clean_string($_GET['kwd']);
    $next   = $common->clean_string($_GET['next']);

    //delete test group and test group question
    $rTestGroup = $common->find('test_group', $condition = ['test_id' => $tid], $type = 'all');
    if(!empty($rTestGroup)) {
      foreach ($rTestGroup as $key => $value) {
        $common->delete('test_group_question', $field = ['test_group_id' => $value['id']]);
      }
      $common->delete('test_group', $field = ['test_id' => $tid]);
    }
    //delete result and result condition
    $rResult = $common->find('result', $condition = ['test_id' => $tid], $type = 'all');
    if(!empty($rResult)) {
      foreach ($rResult as $key => $value) {
        $common->delete('result_condition', $field = ['result_id' => $value['id']]);
      }
      $common->delete('result', $field = ['test_id' => $tid]);
    }

    //delete response and response answer
    $rResponse = $common->find('response', $condition = ['test_id' => $tid], $type = 'all');
    if(!empty($rResponse)) {
      foreach ($rResponse as $key => $value) {
        $common->delete('response_answer', $field = ['response_id' => $value['id']]);
      }
      $common->delete('response', $field = ['test_id' => $tid]);
    }

    //delete apitransaction and mailerlite_group
    $rApitransaction = $common->find('apitransaction', $condition = ['test_id' => $tid], $type = 'all');
    if(!empty($rApitransaction)) {
      foreach ($rApitransaction as $key => $value) {
        $common->delete('mailerlite_group', $field = ['transaction_id' => $value['id']]);
      }
      $common->delete('apitransaction', $field = ['test_id' => $tid]);
    }

    //delete test_question, answer_id and answer_topic
    $rTestQues = $common->find('test_question', $condition = ['test_id' => $tid], $type = 'all');
    if(!empty($rTestQues)) {
      foreach ($rTestQues as $key => $value) {
        //Get answer by test question_id
        $rAnswer = $common->find('answer', $condition = ['test_question_id' => $value['id']], $type = 'all');
        if(!empty($rAnswer)){
          foreach ($rAnswer as $k => $va) {
            $common->delete('answer_topic', $field = ['answer_id' => $va['id']]);
          }
        }
        $common->delete('answer', $field = ['test_question_id' => $value['id']]);
      }//End fetch $rTestQues
    }

    //delete test_tmp and test_tmp_question
    $rTestTmp = $common->find('test_tmp', $condition = ['test_id' => $tid], $type = 'all');
    if(!empty($rTestTmp)) {
      foreach ($rTestTmp as $key => $value) {
        $common->delete('test_tmp_question', $field = ['test_tmp_id' => $value['id']]);
      }
      $common->delete('test_tmp', $field = ['test_id' => $tid]);
    }

    $common->delete('group_answer', $field = ['test_id' => $tid]);
    $common->delete('test_question_topic', $field = ['test_id' => $tid]);
    $common->delete('test_question_topic_hide', $field = ['test_id' => $tid]);
    $common->delete('test_topic_analysis', $field = ['test_id' => $tid]);
    $common->delete('test_topic_answer', $field = ['test_id' => $tid]);
    $common->delete('test_patient', $field = ['test_id' => $tid]);
    $common->delete('test_psychologist', $field = ['test_id' => $tid]);

    $common->delete('test_question', $field = ['test_id' => $tid]);
    $common->delete('test', $field = ['id' => $tid, 'lang' => $lang]);

    setcookie('checkTest', $result['test_title'], time() - 10);
    setcookie('checkResponse', $result['total_response'], time() - 10);
    setcookie('test_id', $tid, time() - 10);

    if($_GET['catid']){
      header('location: '.$admin_file.'?task=test&catid='.$catid.'&tid='.$tid.'&kwd='.$kwd.'&next='.$next);
    }else {
      header('location: '.$admin_file.'?task=test');
    }
    exit;
  }

  $kwd = !empty($_GET['kwd']) ?     $common->clean_string($_GET['kwd']) : '';
  $catid = !empty($_GET['catid']) ? $common->clean_string($_GET['catid']) : '';
  $testid = !empty($_GET['tid']) ?  $common->clean_string($_GET['tid']) : '';
  $listTest = listTest($kwd, $catid, $testid, $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listTest', $listTest);
  $smarty_appform->assign('test', $common->find('test', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->assign('category', $common->find('category', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->display('admin/admin_test.tpl');
  exit;
}
//Task result
if('result' === $task)
{
  //Clear session
  if(!$_POST) unset($_SESSION['result']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $test_id    = $common->clean_string($_POST['tid']);
      $result_id  = $common->clean_string($_POST['rid']);
      $topic_id   = $common->clean_string($_POST['topic']);
      $score_from = $common->clean_string($_POST['score_from']);
      $score_to   = $common->clean_string($_POST['score_to']);
      $message    = $common->clean_string($_POST['message']);

      //add value to session to use in template
      $_SESSION['result'] = $_POST;
      //form validation
      if(empty($test_id))     $error['test_id'] = 1;
      if(empty($topic_id))    $error['topic'] = 1;
      if(empty($score_from))  $error['score_from'] = 1;
      if(empty($score_to))    $error['score_to']  = 1;
      if(empty($message))     $error['message']  = 1;

      //Add result
      if(0 === count($error) && empty($result_id))
      {
        $result = $common->find('result', $condition = ['test_id' => $test_id, 'topic_id' => $topic_id], $type = 'one');

        $common->save('result', $field = ['test_id' => $test_id, 'topic_id' => $topic_id, 'score_from' => $score_from, 'score_to' => $score_to, 'message' => $message, 'view_order' => $result['view_order']]);
        //unset session
        unset($_SESSION['result']);
        //Redirect
        header('location: '.$admin_file.'?task=result&tid='.$test_id);
        exit;
      }
    }
  }//End action add

  //get edit test
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $test_id    = $common->clean_string($_POST['tid']);
      $result_id  = $common->clean_string($_POST['rid']);
      $topic_id   = $common->clean_string($_POST['topic']);
      $score_from = $common->clean_string($_POST['score_from']);
      $score_to   = $common->clean_string($_POST['score_to']);
      $message    = $common->clean_string($_POST['message']);

      //add value to session to use in template
      $_SESSION['result'] = $_POST;
      //form validation
      if(empty($test_id))     $error['test_id'] = 1;
      if(empty($topic_id))    $error['topic'] = 1;
      if(empty($score_from))  $error['score_from'] = 1;
      if(empty($score_to))    $error['score_to']  = 1;
      if(empty($message))     $error['message']  = 1;

      //update result
      if(0 === count($error) && !empty($result_id))
      {
        $common->update('result', $field = ['topic_id' => $topic_id, 'score_from' => $score_from, 'score_to' => $score_to, 'message' => $message], $condition = ['id' => $result_id]);
        //unset session
        unset($_SESSION['result']);
        //Redirect
        header('location: '.$admin_file.'?task=result&tid='.$test_id);
        exit;
      }
    }
    $smarty_appform->assign('getResultByID', $common->find('result', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End edit

  //action delete result
  if('delete' === $action && !empty($_GET['id']))
  {
    $result = checkDeleteResult($_GET['id']);

    if(0 === $result['total_count'])
    {
      $common->delete('result', $field = ['id' => $_GET['id']]);
      setcookie('checkResultFrom', $result['score_from'], time() - 10);
    }else {
      setcookie('checkResultFrom', $result['score_from'], time() + 10);
      setcookie('checkResultTo', $result['score_to'], time() + 10);
      setcookie('checkResultTopic', $result['name'], time() + 10);
    }

    header('location: '.$admin_file.'?task=result&tid='.$_GET['tid']);
    exit;
  }

  $listResult = listResult($_GET['tid'], '', $slimit = 10);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listResult', $listResult);
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
  $smarty_appform->assign('topic', $common->find('topic', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->display('admin/admin_result.tpl');
  exit;
}
//Task result condition
if('result_condition' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['result_con']);

  $error = array();
  //action add result condition
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $show_result_id   = $common->clean_string($_POST['show_result_id']);
      $result_id  = $common->clean_string($_POST['rid']);
      $result_con_id    = $common->clean_string($_POST['result_con_id']);
      //add value to session to use in template
      $_SESSION['result_con'] = $_POST;

      //form validation
      if(empty($show_result_id)) $error['show_result_id'] = 1;
      if(empty($result_id))     $error['result_id'] = 1;

      //Add result Condition
      if(0 === count($error) && empty($result_con_id))
      {
        $common->save('result_condition', $field = ['result_id' => $result_id, 'show_result_id' => $show_result_id]);
        //unset session
        unset($_SESSION['result_con']);
        //Redirect
        header('location: '.$admin_file.'?task=result_condition&tid='.$_GET['tid'].'&tpid='.$_GET['tpid'].'&rid='.$result_id);
        exit;
      }
    }
  }//End action add result condition

  //get edit result condition
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $show_result_id   = $common->clean_string($_POST['show_result_id']);
      $result_id  = $common->clean_string($_POST['rid']);
      $result_con_id    = $common->clean_string($_POST['result_con_id']);
      //add value to session to use in template
      $_SESSION['result_con'] = $_POST;

      //form validation
      if(empty($show_result_id)) $error['show_result_id'] = 1;
      if(empty($result_id))     $error['result_id'] = 1;

      //update result Condition
      if(0 === count($error) && !empty($result_con_id))
      {
        $common->update('result_condition', $field = ['result_id' => $result_id, 'show_result_id' => $show_result_id], $condition = ['id' => $result_con_id]);
        //unset session
        unset($_SESSION['result_con']);
        //Redirect
        header('location: '.$admin_file.'?task=result_condition&tid='.$_GET['tid'].'&tpid='.$_GET['tpid'].'&rid='.$result_id);
        exit;
      }
    }
    $smarty_appform->assign('getResultConByID', $common->find('result_condition', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action edit result condition

  //action delete result condition condition
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('result_condition', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=result_condition&tid='.$_GET['tid'].'&tpid='.$_GET['tpid'].'&rid='.$_GET['rid']);
    exit;
  }

  $reslut = listResultCondition($_GET['tid'], $_GET['rid']);
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('resultCondition', $reslut);
  $smarty_appform->assign('result', listResult($_GET['tid'], $_GET['tpid'], ''));
  $smarty_appform->assign('resultById', $common->find('result', $condition = ['id' => $_GET['rid']], $type = 'one'));
  $smarty_appform->display('admin/admin_result_condition.tpl');
  exit;
}
//Task New result
if('new_result' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['newresult']);
  $error = array();
  if ('add' === $action) {
    if($_POST)
    {
      //get value from form
      $test_id    = $common->clean_string($_POST['tid']);
      $n_rid      = $common->clean_string($_POST['nrid']);
      $topic_id   = $common->clean_string($_POST['topic']);
      $view_order = $common->clean_string($_POST['view_order']);
      $message    = $common->clean_string($_POST['message']);

      //add value to session to use in template
      $_SESSION['newresult'] = $_POST;
      //form validation
      if(empty($test_id))     $error['test_id'] = 1;
      if(empty($topic_id))    $error['topic'] = 1;
      if(empty($view_order))  $error['view_order'] = 1;
      if(empty($message))     $error['message']  = 1;

      $rNewResult = $common->find('new_result', $condition = ['id' => $n_rid], $type = 'one');
      if(!empty($test_id) && !empty($topic_id) && $rNewResult['topic_id'] != $topic_id && checkTopicNewResult($test_id, $topic_id) > 0) $error['is_existed'] = 1;
      //Add result
      if(0 === count($error) && empty($n_rid))
      {

        $common->save('new_result', $field = ['test_id' => $test_id, 'topic_id' => $topic_id, 'message' => $message, 'view_order' => $view_order]);
        //unset session
        unset($_SESSION['newresult']);
        //Redirect
        header('location: '.$admin_file.'?task=new_result&tid='.$test_id);
        exit;
      }
      //update result
      if(0 === count($error) && !empty($n_rid))
      {
        $common->update('new_result', $field = ['topic_id' => $topic_id, 'message' => $message, 'view_order' => $view_order], $condition = ['id' => $n_rid]);
        //unset session
        unset($_SESSION['newresult']);
        //Redirect
        header('location: '.$admin_file.'?task=new_result&tid='.$test_id);
        exit;
      }
    }
  }
  //action delete result
  if('delete' === $action && !empty($_GET['id']))
  {
    $rNewResult = $common->find('test_question_condition', $condition = ['new_result_id' => $_GET['id']], $type = 'all');
    foreach ($rNewResult as $k => $va) {
      $common->delete('test_question_condition', $field = ['id' => $va['id']]);
    }
    $common->delete('new_result', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=new_result&tid='.$_GET['tid']);
    exit;
  }
  //get edit test
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $topic   = $common->clean_string($_POST['topic']);
      $view_order  = $common->clean_string($_POST['view_order']);
      $message    = $common->clean_string($_POST['message']);
      //add value to session to use in template
      //form validation
      if(empty($topic)) $error['topic'] = 1;
      if(empty($view_order))     $error['view_order'] = 1;
      if(empty($message))     $error['message'] = 1;
      //update result Condition
      if(0 === count($error) && !empty($_GET['id']))
      {
        $common->update('new_result', $field = ['topic_id' => $topic, 'message' => $message, 'view_order' => $view_order], $condition = ['id' => $_GET['id']]);
        //Redirect
        header('location: '.$admin_file.'?task=new_result&tid='.$_GET['tid']);
        exit;
      }
    }
    $smarty_appform->assign('getNResultByID', $common->find('new_result', $condition = ['id' => $_GET['id']], $type = 'one'));
  }

  $results = getListNewResult($_GET['tid'], $lang);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listNewResult', $results);
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid'], 'lang' => $lang], $type = 'one'));
  $smarty_appform->assign('topic', getListTestTopic($_GET['tid']));
  $smarty_appform->display('admin/admin_new_result.tpl');
  exit;
}
//Task: test question condition
if('test_question_condition' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['test_que_con']);

  $error = array();
  if ('add' === $action) {
    if($_POST)
    {
      //get value from form
      $nrid       = $common->clean_string($_POST['nrid']);
      $tqcid      = $common->clean_string($_POST['tqcid']);
      $tqid       = $common->clean_string($_POST['tqid']);
      $value      = $common->clean_string($_POST['value']);
      $conditional  = $common->clean_string($_POST['conditional']);
      $operator     = $common->clean_string($_POST['operator']);
      //add value to session to use in template
      $_SESSION['test_que_con'] = $_POST;
      //form validation
      if(empty($nrid))        $error['nrid'] = 1;
      if(empty($tqid))        $error['tqid'] = 1;
      // if(empty($value))       $error['value'] = 1;
      if(empty($conditional)) $error['conditional'] = 1;
      if(empty($operator))    $error['operator']  = 1;
      //Add result
      if(0 === count($error) && empty($tqcid))
      {
        $common->save('test_question_condition', $field = ['new_result_id' => $nrid, 'test_question_id' => $tqid, 'conditional' => $conditional, 'operator' => $operator, 'value_condition' => $value]);
        //unset session
        unset($_SESSION['test_que_con']);
        //Redirect
        if($_GET['next']){
          header('location: '.$admin_file.'?task=test_question_condition&tid='.$_GET['tid'].'&tpid='.$_GET['tpid'].'&nrid='.$nrid.'&next='.$_GET['next']);
        }else {
          header('location: '.$admin_file.'?task=test_question_condition&tid='.$_GET['tid'].'&tpid='.$_GET['tpid'].'&nrid='.$nrid);
        }
        exit;
      }
      //update result
      if(0 === count($error) && !empty($tqcid))
      {
        $common->update('test_question_condition', $field = ['new_result_id' => $nrid, 'test_question_id' => $tqid, 'conditional' => $conditional, 'operator' => $operator, 'value_condition' => $value], $condition = ['id' => $tqcid]);
        //unset session
        unset($_SESSION['test_que_con']);
        //Redirect
        if($_GET['next']){
          header('location: '.$admin_file.'?task=test_question_condition&tid='.$_GET['tid'].'&tpid='.$_GET['tpid'].'&nrid='.$nrid.'&next='.$_GET['next']);
        }else {
          header('location: '.$admin_file.'?task=test_question_condition&tid='.$_GET['tid'].'&tpid='.$_GET['tpid'].'&nrid='.$nrid);
        }
        exit;
      }
    }
  }
  //action delete result
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('test_question_condition', $field = ['id' => $_GET['id']]);
    if($_GET['next']){
      header('location: '.$admin_file.'?task=test_question_condition&tid='.$_GET['tid'].'&tpid='.$_GET['tpid'].'&nrid='.$_GET['nrid'].'&next='.$_GET['next']);
    }else {
      header('location: '.$admin_file.'?task=test_question_condition&tid='.$_GET['tid'].'&tpid='.$_GET['tpid'].'&nrid='.$_GET['nrid']);
    }
    exit;
  }
  //get edit test question condition
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $nrid       = $common->clean_string($_POST['nrid']);
      $tqcid      = $common->clean_string($_POST['tqcid']);
      $tqid       = $common->clean_string($_POST['tqid']);
      $value      = $common->clean_string($_POST['value']);
      $conditional  = $common->clean_string($_POST['conditional']);
      $operator     = $common->clean_string($_POST['operator']);
      //form validation
      if(empty($nrid))        $error['nrid'] = 1;
      if(empty($tqid))        $error['tqid'] = 1;
      if(empty($conditional)) $error['conditional'] = 1;
      if(empty($operator))    $error['operator']  = 1;
      //update result Condition
      if(0 === count($error) && !empty($_GET['id']))
      {
        $common->update('test_question_condition', $field = ['new_result_id' => $nrid, 'test_question_id' => $tqid, 'conditional' => $conditional, 'operator' => $operator, 'value_condition' => $value], $condition = ['id' => $_GET['id']]);
        //Redirect
        header('location: '.$admin_file.'?task=test_question_condition&tid='.$_GET['tid'].'&tpid='.$_GET['tpid'].'&nrid='.$nrid.'&next='.$_GET['next']);
        exit;
      }
    }
    $smarty_appform->assign('getTestQuesConByID', $common->find('test_question_condition', $condition = ['id' => $_GET['id']], $type = 'one'));
  }
  $results = getListTestQuesCondition($_GET['nrid'], '', $slimit = 10);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listTestQueCondition', $results);
  $smarty_appform->assign('getViewConditonTestQue', getViewCondtionTestQuestion($_GET['nrid']));
  $smarty_appform->assign('listTestQuestion', getListTestQuestion('', $_GET['tid'], $type_check = 3, '', $lang, $slimit = ''));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid'], 'lang' => $lang], $type = 'one'));
  $smarty_appform->assign('topic', $common->find('topic', $condition = ['id' => $_GET['tpid'], 'lang' => $lang], $type = 'one'));
  $smarty_appform->assign('new_result', $common->find('new_result', $condition = ['id' => $_GET['nrid']], $type = 'one'));
  $smarty_appform->display('admin/admin_test_question_condition.tpl');
  exit;
}
//Task: topic
if('topic' === $task)
{
  //Clear session
  if(!$_POST) unset($_SESSION['topic']);

  $error = array();
  //action add topic
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $name = $common->clean_string($_POST['name']);
      $id   = $common->clean_string($_POST['id']);
      //add value to session to use in template
      $_SESSION['topic'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //Add topic
      if(0 === count($error) && empty($id))
      {
        $common->save('topic', $field = ['name' => $name, 'lang' => $lang]);
        //unset session
        unset($_SESSION['topic']);
        //Redirect
        header('location: '.$admin_file.'?task=topic');
        exit;
      }
    }
  }//End action add topic

  //action edit topic
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $name = $common->clean_string($_POST['name']);
      $id   = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['topic'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //update topic
      if(0 === count($error) && !empty($id))
      {
        $common->update('topic', $field = ['name' => $name, 'lang' => $lang], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['topic']);
        //Redirect
        header('location: '.$admin_file.'?task=topic');
        exit;
      }
    }
    $smarty_appform->assign('getTopicByID', $common->find('topic', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//end action edit topic

  //action delete topic
  if('delete' === $action && !empty($_GET['id']))
  {
    $result = checkDeleteTopic($_GET['id']);
    if('0' === $result['total_count'])
    {
      $common->delete('topic', $field = ['id' => $_GET['id']]);
    }else {
      setcookie('checkTopic', $result['name'], time() + 10);
    }

    header('location: '.$admin_file.'?task=topic');
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $result = listTopic($kwd, $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listTopic', $result);
  $smarty_appform->display('admin/admin_topic.tpl');
  exit;
}
//Task: topic hide
if('topic_hide' === $task)
{
  //Clear session
  if(!$_POST) unset($_SESSION['topic_hide']);

  $error = array();
  //action add topic_hide
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $less_than    = $common->clean_string($_POST['less_than']);
      $bigger_than  = $common->clean_string($_POST['bigger_than']);
      $tid_first    = $common->clean_string($_POST['topic_first']);
      $tid_second   = $common->clean_string($_POST['topic_second']);
      $id       = $common->clean_string($_POST['id']);
      $test_id  = $_GET['tid'];

      //add value to session to use in template
      $_SESSION['topic_hide'] = $_POST;
      //form validation
      if(empty($less_than))   $error['less_than']  = 1;
      if(empty($bigger_than)) $error['bigger_than']  = 1;
      if(empty($tid_first))   $error['topic_first']  = 1;
      if(empty($tid_second))   $error['topic_second']  = 1;

      //Add topic
      if(0 === count($error) && empty($id))
      {
        $common->save('test_question_topic_hide', $field = ['test_id' => $test_id, 'topic_id' => $tid_first, 'if_topic_id' => $tid_second, 'less_than' => $less_than, 'bigger_than' => $bigger_than]);
        //unset session
        unset($_SESSION['topic_hide']);
        //Redirect
        header('location: '.$admin_file.'?task=topic_hide&tid='.$test_id);
        exit;
      }
    }
  }//end action add topic_hide

  //action edit topic_hide
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $less_than    = $common->clean_string($_POST['less_than']);
      $bigger_than  = $common->clean_string($_POST['bigger_than']);
      $tid_first    = $common->clean_string($_POST['topic_first']);
      $tid_second   = $common->clean_string($_POST['topic_second']);
      $id       = $common->clean_string($_POST['id']);
      $test_id  = $_GET['tid'];

      //add value to session to use in template
      $_SESSION['topic_hide'] = $_POST;
      //form validation
      if(empty($less_than))   $error['less_than']  = 1;
      if(empty($bigger_than)) $error['bigger_than']  = 1;
      if(empty($tid_first))   $error['topic_first']  = 1;
      if(empty($tid_second))   $error['topic_second']  = 1;

      //update topic
      if(0 === count($error) && !empty($id))
      {
        $common->update('test_question_topic_hide', $field = ['topic_id' => $tid_first, 'if_topic_id' => $tid_second, 'less_than' => $less_than, 'bigger_than' => $bigger_than], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['topic_hide']);
        //Redirect
        header('location: '.$admin_file.'?task=topic_hide&tid='.$test_id);
        exit;
      }
    }
    $smarty_appform->assign('getTopicQHideByID', $common->find('test_question_topic_hide', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//end action edit topic_hide

  //action delete topic
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('test_question_topic_hide', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=topic_hide&tid='.$_GET['tid']);
    exit;
  }

  if('get_topic_hide' === $action)
  {
    $result = getListTestTopicHide($_GET['tid'], $_GET['tpid']);
    header('Content-type: application/json');
    echo json_encode($result);
    exit;
  }

  $result = getListTestQuestionTopicHide($_GET['tid']);
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listTestQueTopicHide', $result);
  $smarty_appform->assign('listTestTopic', getListTestTopicHide($_GET['tid'], ''));
  $smarty_appform->assign('getTest', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
  $smarty_appform->display('admin/admin_topic_hide.tpl');
  exit;
}
//Task: topic analysis
if('topic_analysis' === $task)
{
  //Clear session
  if(!$_POST) unset($_SESSION['topic_analysis']);

  $error = array();
  //action: add topic_analysis
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $name = $common->clean_string($_POST['name']);
      $id   = $common->clean_string($_POST['topic_asis_id']);

      //add value to session to use in template
      $_SESSION['topic_analysis'] = $_POST;
      //form validation
      if(empty($name))        $error['name']  = 1;

      //Add topic
      if(0 === count($error) && empty($id))
      {
        $common->save('topic_analysis', $field = ['name' => $name, 'lang' => $lang]);
        //unset session
        unset($_SESSION['topic_analysis']);
        //Redirect
        header('location: '.$admin_file.'?task=topic_analysis');
        exit;
      }
    }
  }//End action: add topic_analysis

  //action: edit topic_analysis
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $name = $common->clean_string($_POST['name']);
      $id   = $common->clean_string($_POST['topic_asis_id']);

      //add value to session to use in template
      $_SESSION['topic_analysis'] = $_POST;
      //form validation
      if(empty($name))  $error['name'] = 1;
      //update topic
      if(0 === count($error) && !empty($id))
      {
        $common->update('topic_analysis', $field = ['name' => $name, 'lang' => $lang], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['topic_analysis']);
        //Redirect
        header('location: '.$admin_file.'?task=topic_analysis');
        exit;
      }
    }
    $smarty_appform->assign('getTopicAsisByID', $common->find('topic_analysis', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action: edit topic_analysis

  //action delete topic
  if('delete' === $action && !empty($_GET['id']))
  {
    $result = checkDeleteTopicAnalysis($_GET['id']);
    if('0' === $result['total_count'])
    {
      $common->delete('topic_analysis', $field = ['id' => $_GET['id']]);
    }else {
      setcookie('checkTopicAnalysis', $result['name'], time() + 10);
    }

    header('location: '.$admin_file.'?task=topic_analysis');
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $result = listTopicAnalysis($kwd, $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listTopicAnalysis', $result);
  $smarty_appform->display('admin/admin_topic_analysis.tpl');
  exit;
}
//Task: test topic
if('test_topic' === $task)
{
  if('update_test_topic' === $action)
  {
    $test_id    = $_POST['testid'];
    $topic_id   = $_POST['topicid'];
    $view_order = $_POST['view_order'];

    if(!empty($test_id) && !empty($topic_id) && !empty($view_order))
    {
      $result = $common->update('result', $field = ['view_order' => $view_order], $condition = ['test_id' => $test_id, 'topic_id' => $topic_id]);
    }

    $resultCount = checkViewOrderTopic($test_id);
    header('Content-type: application/json');
    echo json_encode(array('status' => $result, 'checkCountView' => $resultCount));
    exit;
  }

  $result = getListTestTopic($_GET['tid']);

  $smarty_appform->assign('listTestTopic', $result);
  $smarty_appform->assign('checkViewOrderTopic', checkViewOrderTopic($_GET['tid']));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
  $smarty_appform->display('admin/admin_test_topic.tpl');
  exit;
}
//Task: test_topic_analysis
if('test_topic_analysis' === $task)
{
  //Clear session
  if(!$_POST) unset($_SESSION['test_topic_analysis']);

  $error = array();
  //action: add test_topic_analysis
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $test_id      = $common->clean_string($_POST['test_id']);
      $topic_id     = $common->clean_string($_POST['topic_id']);
      $ana_topic_id = $common->clean_string($_POST['ana_topic_id']);
      $less_than    = $common->clean_string($_POST['less_than_value']);
      $bigger_than  = $common->clean_string($_POST['bigger_than_value']);
      $id   = $common->clean_string($_POST['test_topic_asis_id']);

      //add value to session to use in template
      $_SESSION['test_topic_analysis'] = $_POST;
      //form validation
      if(empty($topic_id))    $error['topic_id']  = 1;
      if(empty($ana_topic_id))$error['ana_topic_id']  = 1;
      if(empty($less_than))   $error['less_than']   = 1;
      if(empty($bigger_than)) $error['bigger_than'] = 1;

      //Add topic
      if(0 === count($error) && empty($id))
      {
        $common->save('test_topic_analysis', $field = ['test_id' => $test_id, 'topic_id' => $topic_id, 'topic_analysis_id' => $ana_topic_id, 'less_than' => $less_than, 'bigger_than' => $bigger_than]);
        //unset session
        unset($_SESSION['test_topic_analysis']);
        //Redirect
        header('location: '.$admin_file.'?task=test_topic_analysis&tid='.$test_id);
        exit;
      }
    }
  }//End action: add test_topic_analysis

  //action: edit test_topic_analysis
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $test_id      = $common->clean_string($_POST['test_id']);
      $topic_id     = $common->clean_string($_POST['topic_id']);
      $ana_topic_id = $common->clean_string($_POST['ana_topic_id']);
      $less_than    = $common->clean_string($_POST['less_than_value']);
      $bigger_than  = $common->clean_string($_POST['bigger_than_value']);
      $id   = $common->clean_string($_POST['test_topic_asis_id']);

      //add value to session to use in template
      $_SESSION['test_topic_analysis'] = $_POST;
      //form validation
      if(empty($topic_id))    $error['topic_id']  = 1;
      if(empty($ana_topic_id))$error['ana_topic_id']  = 1;
      if(empty($less_than))   $error['less_than']   = 1;
      if(empty($bigger_than)) $error['bigger_than'] = 1;

      //update topic
      if(0 === count($error) && !empty($id))
      {
        $common->update('test_topic_analysis', $field = ['topic_id'=> $topic_id, 'topic_analysis_id' => $ana_topic_id, 'less_than' => $less_than, 'bigger_than' => $bigger_than], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['test_topic_analysis']);
        //Redirect
        header('location: '.$admin_file.'?task=test_topic_analysis&tid='.$test_id);
        exit;
      }
    }
    $smarty_appform->assign('getTestTopicAsisByID', $common->find('test_topic_analysis', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action: edit test_topic_analysis

  //action delete topic
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('test_topic_analysis', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=test_topic_analysis&tid='.$_GET['tid']);
    exit;
  }
  //get edit category
  if('edit' === $action && !empty($_GET['id']))
  {
    $smarty_appform->assign('getTestTopicAsisByID', $common->find('test_topic_analysis', $condition = ['id' => $_GET['id']], $type = 'one'));
  }
  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $result = getListTestTopicAnalysis($kwd, $_GET['tid'], $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('error', $error);
  // $smarty_appform->assign('listTestQueTopic', getListTestQuestionTopic($_GET['tid'], $lang, ''));
  $smarty_appform->assign('listTestTopic', getListTestTopicHide($_GET['tid'], ''));
  $smarty_appform->assign('listTestTopicAnalysis', $result);
  $smarty_appform->assign('listTopicAnalysis', $common->find('topic_analysis', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
  $smarty_appform->display('admin/admin_test_topic_analysis.tpl');
  exit;
}
//Task: test_topic_answer
if('test_topic_answer' === $task)
{
  //Clear session
  if(!$_POST) unset($_SESSION['test_topic_answer']);

  $error = array();
  //action: test_topic_answer
  if('add' === $action)
  {
    if($_POST)
    {
      $id       = $common->clean_string($_POST['id']);
      $topic_id = $common->clean_string($_POST['topic_id']);
      $average  = $common->clean_string($_POST['average']);
      $stdd     = $common->clean_string($_POST['stdd']);
      $multiplier = $common->clean_string($_POST['multiplier']);
      $constant   = $common->clean_string($_POST['constant']);

      //add value to session to use in template
      $_SESSION['test_topic_answer'] = $_POST;
      //form validation
      if(empty($topic_id))    $error['topic_id'] = 1;
      if(empty($average))     $error['average'] = 1;
      if(empty($stdd))        $error['stdd'] = 1;
      if(empty($multiplier))  $error['multiplier'] = 1;
      if(empty($constant))    $error['constant'] = 1;

      if(!empty($topic_id) && is_test_topic_answer_exist($_GET['tid'], $topic_id) > 0) $error['is_key_test_topic_ans_exist'] = 2;

      //Add test
      if(0 === count($error) && empty($id))
      {
        $common->save('test_topic_answer', $field =['test_id'     => $_GET['tid'],
        'topic_id'    => $topic_id,
        'average'     => $average,
        'stdd'        => $stdd,
        'multiplier'  => $multiplier,
        'constant'    => $constant]);
        //unset session
        unset($_SESSION['test_topic_answer']);
        //Redirect
        header('location: '.$admin_file.'?task=test_topic_answer&tid='.$_GET['tid']);
        exit;
      }
    }
  }//End action: add test_topic_answer

  //action: edit test_topic_answer
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      $id       = $common->clean_string($_POST['id']);
      $topic_id = $common->clean_string($_POST['topic_id']);
      $average  = $common->clean_string($_POST['average']);
      $stdd     = $common->clean_string($_POST['stdd']);
      $multiplier = $common->clean_string($_POST['multiplier']);
      $constant   = $common->clean_string($_POST['constant']);

      //add value to session to use in template
      $_SESSION['test_topic_answer'] = $_POST;
      //form validation
      if(empty($topic_id))    $error['topic_id'] = 1;
      if(empty($average))     $error['average'] = 1;
      if(empty($stdd))        $error['stdd'] = 1;
      if(empty($multiplier))  $error['multiplier'] = 1;
      if(empty($constant))    $error['constant'] = 1;

      $result = $common->find('test_topic_answer', $condition = ['id' => $id], $type = 'one');
      if(!empty($topic_id) && $result['topic_id'] !== $topic_id && is_test_topic_answer_exist($_GET['tid'], $topic_id) > 0) $error['is_key_test_topic_ans_exist'] = 2;

      //update test
      if(0 === count($error) && !empty($id))
      {
        $common->update('test_topic_answer', $field =['test_id'     => $_GET['tid'],
        'topic_id'    => $topic_id,
        'average'     => $average,
        'stdd'        => $stdd,
        'multiplier'  => $multiplier,
        'constant'    => $constant], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['test_topic_answer']);
        //Redirect
        header('location: '.$admin_file.'?task=test_topic_answer&tid='.$_GET['tid']);
        exit;
      }
    }
    $smarty_appform->assign('getTestTopicAnsByID', $common->find('test_topic_answer', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action: edit test_topic_answer

  //action delete topic
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('test_topic_answer', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=test_topic_answer&tid='.$_GET['tid']);
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $results = listTestTopicAnswer($_GET['tid'], $kwd);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listTestTopicAnswer', $results);
  // $smarty_appform->assign('listTestQueTopic', getListTestQuestionTopic($_GET['tid'], $lang, ''));
  $smarty_appform->assign('listTestTopic', getListTestTopicHide($_GET['tid'], ''));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
  $smarty_appform->display('admin/admin_test_topic_answer.tpl');
  exit;
}
//Task test group
if('test_group' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['test_group']);

  $error = array();
  //action: add test group
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $testid   = $common->clean_string($_POST['test']);
      $title    = $common->clean_string($_POST['title']);
      $view_order = $common->clean_string($_POST['view_order']);
      $id       = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['test_group'] = $_POST;
      //form validation
      if(empty($title))   $error['title']   = 1;
      if(empty($testid))  $error['testid']  = 1;
      if(empty($view_order))  $error['view_order']  = 1;

      //Add test group
      if(0 === count($error) && empty($id))
      {
        $common->save('test_group', $field = ['test_id' => $testid, 'title' => $title, 'view_order' => $view_order]);
        //unset session
        unset($_SESSION['test_group']);
        //Redirect
        if($_GET['tid']){
          header('location: '.$admin_file.'?task=test_group&tid='.$_GET['tid']);
        }else {
          header('location: '.$admin_file.'?task=test_group');
        }
        exit;
      }
    }
  }//End action: add test group

  //action: edit test group
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $testid   = $common->clean_string($_POST['test']);
      $title    = $common->clean_string($_POST['title']);
      $view_order = $common->clean_string($_POST['view_order']);
      $id       = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['test_group'] = $_POST;
      //form validation
      if(empty($title))   $error['title']   = 1;
      if(empty($testid))  $error['testid']  = 1;
      if(empty($view_order))  $error['view_order']  = 1;
      //update test group
      if(0 === count($error) && !empty($id))
      {
        $common->update('test_group', $field = ['test_id' => $testid, 'title' => $title, 'view_order' => $view_order], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['test_group']);
        //Redirect
        if($_GET['tid']){
          header('location: '.$admin_file.'?task=test_group&tid='.$_GET['tid']);
        }else {
          header('location: '.$admin_file.'?task=test_group');
        }
        exit;
      }
    }
    $smarty_appform->assign('getTestGroupByID', $common->find('test_group', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action: edit test group

  //action delete test group
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('test_group_question', $field = ['test_group_id' => $_GET['id']]);
    $common->delete('test_group', $field = ['id' => $_GET['id']]);

    if($_GET['tid']){
      header('location: '.$admin_file.'?task=test_group&tid='.$_GET['tid']);
    }else {
      header('location: '.$admin_file.'?task=test_group');
    }
    exit;
  }

  $testid = !empty($_GET['tid']) ? $_GET['tid'] : '';
  $result = listTestGroup($testid, $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('listTestGroup', $result);
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('test', $common->find('test', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->display('admin/admin_test_group.tpl');
  exit;
}
//Task test group question
if('test_group_question' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['test_g_question']);

  $error = array();
  //action: add test_group_question
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $tgroup_id    = $common->clean_string($_GET['tgid']);
      $id   = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['test_g_question'] = $_POST;
      //form validation
      if(empty($_POST['test_question']))  $error['test_question'] = 1;
      if(empty($tgroup_id)) $error['test_g_id']     = 1;
      if(!empty($_POST['test_question'])){
        foreach ($_POST['test_question'] as $key => $value) {
          if(empty($id) && is_exist_test_group_question($_GET['tid'], $value) > 0) {
            $error['is_exist_test_group_que'] += 1;
          }
        }
      }

      //Add test group
      if(0 === count($error) && empty($id))
      {
        foreach ($_POST['test_question'] as $key => $value) {
          $common->save('test_group_question', $field = ['test_group_id' => $tgroup_id, 'test_question_id' => $value]);
        }
        //unset session
        unset($_SESSION['test_g_question']);
        //Redirect
        header('location: '.$admin_file.'?task=test_group_question&tid='.$_GET['tid'].'&tgid='.$_GET['tgid']);
        exit;
      }
    }
  }//End action: add test_group_question

  //action delete test group
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('test_group_question', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=test_group_question&tid='.$_GET['tid'].'&tgid='.$_GET['tgid']);
    exit;
  }

  $result = listTestGroupQuestionAdmin($_GET['tgid']);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listTestGroupQuestion', $result);
  $smarty_appform->assign('listTestQueGroupAnswer', getTestQuestionViewOrder($_GET['tid']));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
  $smarty_appform->assign('test_group', $common->find('test_group', $condition = ['id' => $_GET['tgid']], $type = 'one'));
  $smarty_appform->display('admin/admin_test_group_question.tpl');
  exit;
}
//Task group answer question
if('group_answer_question' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['g_answer_ques']);

  $error = array();
  //action: add group_answer_question
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $id  = $common->clean_string($_GET['id']);
      $tid = $common->clean_string($_GET['tid']);
      $g_answer_title = $common->clean_string($_POST['g_answer_title']);
      $g_ans_test_que = $common->clean_string($_POST['g_ans_test_que']);

      //add value to session to use in template
      $_SESSION['g_answer_ques'] = $_POST;

      //form validation
      if(empty($id)){
        if(!empty($_POST['test_question'])){
          foreach ($_POST['test_question'] as $key => $value) {
            if(is_exist_group_answer_question($tid, $value) > 0) {
              $error['is_exist_group_answer_que']   = 1;
            }
          }
        }
        if(count($_POST['test_question']) == 0)  $error['test_question'] = 1;
      }
      if(empty($g_answer_title))  $error['g_answer_title'] = 1;

      //Add test group answer
      if(0 === count($error) && empty($id))
      {
        $group_ans_id = '';
        foreach ($_POST['test_question'] as $key => $value) {
          if($key == 0){
            $group_ans_id = $common->save('group_answer', $field = ['test_id' => $tid, 'test_question_id' => $value, 'g_answer_title' => $g_answer_title, 'flag' => 1]);
          }else {
            $common->save('group_answer', $field = ['test_id' => $tid, 'test_question_id' => $value]);
          }
          //Delete test_question_view_order by test_question_id
          $common->delete('test_question_view_order', $field = ['test_id' => $tid, 'test_question_id' => $value]);
          //Delete test_group_question by test_question
          $common->delete('test_group_question', $field = ['test_question_id' => $value]);
        }

        if(!empty($group_ans_id)){
          //for update sub_id in group answer
          updateGroupAnswer($tid, $group_ans_id);
        }

        //unset session
        unset($_SESSION['g_answer_ques']);
        //Redirect
        header('location: '.$admin_file.'?task=group_answer_question&tid='.$tid);
        exit;
      }
      //Update test group answer
      if(0 === count($error) && !empty($id))
      {
        $common->update('group_answer', $field = ['g_answer_title' => $g_answer_title], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['g_answer_ques']);
        //Redirect
        header('location: '.$admin_file.'?task=group_answer_question&tid='.$tid);
        exit;
      }
    }
  }//End action: add group_answer_question

  //get edit language
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $id  = $common->clean_string($_GET['id']);
      $tid = $common->clean_string($_GET['tid']);
      $g_answer_title = $common->clean_string($_POST['g_answer_title']);
      $g_ans_test_que = $common->clean_string($_POST['g_ans_test_que']);
      //add value to session to use in template
      $_SESSION['g_answer_ques'] = $_POST;

      //form validation
      if(empty($id)){
        if(!empty($_POST['test_question'])){
          foreach ($_POST['test_question'] as $key => $value) {
            if(is_exist_group_answer_question($tid, $value) > 0) {
              $error['is_exist_group_answer_que']   = 1;
            }
          }
        }
        if(count($_POST['test_question']) == 0)  $error['test_question'] = 1;
      }
      if(empty($g_answer_title))  $error['g_answer_title'] = 1;

      //Add test group answer
      if(0 === count($error) && empty($id))
      {
        $group_ans_id = '';
        foreach ($_POST['test_question'] as $key => $value) {
          if($key == 0){
            $group_ans_id = $common->save('group_answer', $field = ['test_id' => $tid, 'test_question_id' => $value, 'g_answer_title' => $g_answer_title, 'flag' => 1]);
          }else {
            $common->save('group_answer', $field = ['test_id' => $tid, 'test_question_id' => $value]);
          }
          //Delete test_question_view_order by test_question_id
          $common->delete('test_question_view_order', $field = ['test_id' => $tid, 'test_question_id' => $value]);
          //Delete test_group_question by test_question
          $common->delete('test_group_question', $field = ['test_question_id' => $value]);
        }

        if(!empty($group_ans_id)){
          //for update sub_id in group answer
          updateGroupAnswer($tid, $group_ans_id);
        }

        //unset session
        unset($_SESSION['g_answer_ques']);
        //Redirect
        header('location: '.$admin_file.'?task=group_answer_question&tid='.$tid);
        exit;
      }
      //Update test group answer
      if(0 === count($error) && !empty($id))
      {
        $common->update('group_answer', $field = ['g_answer_title' => $g_answer_title], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['g_answer_ques']);
        //Redirect
        header('location: '.$admin_file.'?task=group_answer_question&tid='.$tid);
        exit;
      }
    }
    $smarty_appform->assign('getGroupAnswerByID', $common->find('group_answer', $condition = ['id' => $_GET['id']], $type = 'one'));
  }

  if('detail' === $action && !empty($_GET['tid']))
  {
    $results = getListGroupAnswer($_GET['tid'], $_GET['id'], '', $slimit = 10);
    header('Content-type: application/json');
    echo json_encode($results);
    exit;
  }

  if('change_flag' === $action)
  {
    $flag_id = $_GET['flag_id'];
    $id  = $_GET['id'];
    $tid =  $_GET['tid'];

    $rGAnswer = $common->find('group_answer', $condition = ['id' => $flag_id, 'flag' => 1], $type = 'one');

    if(!empty($flag_id)) $common->update('group_answer', $field = ['flag' => 0, 'g_answer_title' => NULL], $condition = ['id' => $flag_id]);
    if(!empty($id))      $common->update('group_answer', $field = ['flag' => 1, 'g_answer_title' => $rGAnswer['g_answer_title']], $condition = ['id' => $id]);
    if(!empty($flag_id) && !empty($id)) updateGroupAnswerBySubID($id, $flag_id);
    //get group answer by id
    $getGAnswer = $common->find('group_answer', $condition = ['id' => $id], $type = 'one');
    //get test question view order
    $getTestQueViewOrder = $common->find('test_question_view_order', $condition = ['test_id' => $getGAnswer['test_id'], 'test_question_id' => $rGAnswer['test_question_id']], $type = 'one');
    //Update test question id in view order
    $common->update('test_question_view_order', $field = ['test_question_id' => $getGAnswer['test_question_id']], $condition = ['test_id' => $getGAnswer['test_id'], 'id' => $getTestQueViewOrder['id']]);


    $results = getListGroupAnswer($tid, $id, '', $slimit = 10);
    header('Content-type: application/json');
    echo json_encode($results);
    exit;
  }

  if('change_flag_byid' === $action)
  {
    $flag = 1;
    $rGAnswer = $common->find('group_answer', $condition = ['test_id' => $_GET['tid'], 'flag' => $flag], $type = 'one');

    if(!empty($rGAnswer))   $common->update('group_answer', $field = ['flag' => 0], $condition = ['id' => $rGAnswer['id']]);
    if(!empty($_GET['id'])) $common->update('group_answer', $field = ['flag' => 1], $condition = ['id' => $_GET['id']]);
    if(!empty($_GET['id']) && !empty($rGAnswer['id'])) updateGroupAnswerBySubID($_GET['id'], $rGAnswer['id']);

    header('location: '.$admin_file.'?task=group_answer_question&action=view_all&tid='.$_GET['tid'].'&gaid='.$_GET['id']);
    exit;
  }
  //action delete group answer by sub_id
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('group_answer', $field = ['sub_id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=group_answer_question&tid='.$_GET['tid']);
    exit;
  }
  //action delete group answer by id
  if('delete_byid' === $action && !empty($_GET['id']))
  {
    $common->delete('group_answer', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=group_answer_question&action=view_all&tid='.$_GET['tid'].'&gaid='.$_GET['gaid']);
    exit;
  }
  //action delete group answer by id
  if('delete_group_anwer' === $action && !empty($_GET['id']))
  {
    //Get Group Answer BY ID
    $rGAnswerByID = $common->find('group_answer', $condition = ['test_id' => $_GET['tid'], 'id' => $_GET['id']], $type = 'one');
    //Deleted
    $result = $common->delete('group_answer', $field = ['id' => $_GET['id']]);
    //Get Group Answer sub_id
    $rGAnswerBySubID = $common->find('group_answer', $condition = ['test_id' => $_GET['tid'], 'sub_id' => $rGAnswerByID['sub_id']], $type = 'all');
    header('Content-type: application/json');
    echo json_encode(array('status' => $result, 'countGanser' => COUNT($rGAnswerBySubID)));
    exit;
  }

  if('view_all' === $action)
  {

    $result = getListGroupAnswer($_GET['tid'], $_GET['gaid'], '', $slimit = 10);
    (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
    SmartyPaginate::assign($smarty_appform);

    $smarty_appform->assign('error', $error);
    $smarty_appform->assign('listGroupAnswer', $result);
    $smarty_appform->assign('listTestQuestion', getListTestQuestion('', $_GET['tid'], '', '', $lang, $slimit = ''));
    $smarty_appform->assign('groupAnswerCheckDisable', $common->find('group_answer', $condition = ['test_id' => $_GET['tid']], $type = 'all'));
    $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
    $smarty_appform->display('admin/admin_group_answer_que_view_all.tpl');
    exit;
  }

  $result = getListGroupAnswer($_GET['tid'], '', $flag = 1, $slimit = 10);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listGroupAnswer', $result);
  $smarty_appform->assign('listTestQuestion', getListTestQuestion('', $_GET['tid'], $type_check = 3, '', $lang, $slimit = ''));
  $smarty_appform->assign('groupAnswerCheckDisable', $common->find('group_answer', $condition = ['test_id' => $_GET['tid']], $type = 'all'));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
  $smarty_appform->display('admin/admin_group_answer_question.tpl');
  exit;
}
//Task View Order
if('test_question_view_order' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['view_order']);

  $error = array();
  //action: add test_question_view_order
  if('add' === $action)
  {
    if($_POST)
    {
      $id       = $common->clean_string($_POST['id']);
      $tid      = $common->clean_string($_GET['tid']);
      $view_order   = $common->clean_string($_POST['view_order']);
      $test_que_id  = $common->clean_string($_POST['test_question']);

      //add value to session to use in template
      $_SESSION['view_order'] = $_POST;
      //form validation
      if(empty($view_order))    $error['view_order'] = 1;
      if(empty($test_que_id))   $error['test_question'] = 1;
      if(!empty($test_que_id) && is_view_order_exist($_GET['tid'], $test_que_id) > 0) $error['is_view_order_exist'] = 1;

      //Add test
      if(0 === count($error) && empty($id))
      {
        $common->save('test_question_view_order', $field =['test_id' => $tid, 'test_question_id' => $test_que_id, 'view_order' => $view_order]);
        //unset session
        unset($_SESSION['view_order']);
        //Redirect
        header('location: '.$admin_file.'?task=test_question_view_order&tid='.$_GET['tid']);
        exit;
      }
    }
  }//End action: add test_question_view_order

  //action: edit test_question_view_order
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      $id       = $common->clean_string($_POST['id']);
      $tid      = $common->clean_string($_GET['tid']);
      $view_order   = $common->clean_string($_POST['view_order']);
      $test_que_id  = $common->clean_string($_POST['test_question']);

      //add value to session to use in template
      $_SESSION['view_order'] = $_POST;
      //form validation
      if(empty($view_order))    $error['view_order'] = 1;
      if(empty($test_que_id))   $error['test_question'] = 1;
      //Get test_question_view_order By Id
      $result = $common->find('test_question_view_order', $condition = ['test_id' => $tid, 'test_question_id' => $test_que_id], $type = 'one');
      if(!empty($test_que_id) && $result['test_question_id'] !== $test_que_id && is_view_order_exist($_GET['tid'], $test_que_id) > 0) $error['is_view_order_exist'] = 1;

      //update test
      if(0 === count($error) && !empty($id))
      {
        $common->update('test_question_view_order', $field =['test_id' => $tid, 'test_question_id' => $test_que_id,'view_order' => $view_order], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['view_order']);
        //Redirect
        header('location: '.$admin_file.'?task=test_question_view_order&tid='.$_GET['tid']);
        exit;
      }
    }
    $smarty_appform->assign('getViewOrderByID', $common->find('test_question_view_order', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action: test_question_view_order

  //action delete topic
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('test_question_view_order', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=test_question_view_order&tid='.$_GET['tid']);
    exit;
  }

  $results = getListViewOrderTestQuestion($_GET['tid']);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listTestQestionViewOrder', $results);
  $smarty_appform->assign('listTestQueGroupAnswer', getTestQuestionViewOrder($_GET['tid']));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
  $smarty_appform->display('admin/admin_test_que_view_order.tpl');
  exit;
}
//Task: mailerlite
if('mailerlite' === $task)
{
  //Clear session
  if(!$_POST) unset($_SESSION['mailerlite']);

  $error = array();
  //action: add mailerlite
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $title    = $common->clean_string($_POST['title']);
      $api_key  = $common->clean_string($_POST['api_key']);
      $id       = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['mailerlite'] = $_POST;
      //form validation
      if(empty($title))     $error['title']   = 1;
      if(empty($api_key))   $error['api_key'] = 1;
      //Add mailerlite
      if(0 === count($error) && empty($id))
      {
        $common->save('mailerlite', $field = ['title' => $title, 'api_key' => $api_key]);
        //unset session
        unset($_SESSION['mailerlite']);
        //Redirect
        header('location: '.$admin_file.'?task=mailerlite');
        exit;
      }
    }
  }//End action: add mailerlite

  //action: edit mailerlite
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $title    = $common->clean_string($_POST['title']);
      $api_key  = $common->clean_string($_POST['api_key']);
      $id       = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['mailerlite'] = $_POST;
      //form validation
      if(empty($title))     $error['title']   = 1;
      if(empty($api_key))   $error['api_key'] = 1;
      //update mailerlite
      if(0 === count($error) && !empty($id))
      {
        $common->update('mailerlite', $field = ['title' => $title, 'api_key' => $api_key], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['mailerlite']);
        //Redirect
        header('location: '.$admin_file.'?task=mailerlite');
        exit;
      }
    }
    $smarty_appform->assign('getMailerliteByID', $common->find('mailerlite', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action: edit mailerlite

  //Delete: mailerlite
  if('delete' === $action && !empty($_GET['id']))
  {
    $result = checkDeleteMailerLite($_GET['id']);
    if('0' === $result['total_count'])
    {
      $common->delete('mailerlite', $field = ['id' => $_GET['id']]);
    }else {
      setcookie('checkmailerlite', $result['title'], time() + 10);
    }
    header('location: '.$admin_file.'?task=mailerlite');
    exit;
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $result = listMailerlite($kwd);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listMailerlite', $result);
  $smarty_appform->display('admin/admin_mailerlite.tpl');
  exit;
}
//Task: Api translate
if('apitransaction' === $task)
{
  //Clear session
  if(!$_POST) unset($_SESSION['apitransaction']);

  $result_mailer = $common->find('mailerlite', $condition = ['id' => $_GET['mlid']], $type = 'one');
  if(!empty($result_mailer['api_key']))
  {
    $groupsApi = (new \MailerLiteApi\MailerLite($result_mailer['api_key']))->groups();
    $allGroups = $groupsApi->get();
  }

  $error = array();
  //action: add apitransaction
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $title   = $common->clean_string($_POST['title']);
      $testid  = $common->clean_string($_POST['testid']);
      $ml_id   = $common->clean_string($_GET['mlid']);
      $id      = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['apitransaction'] = $_POST;
      //form validation
      if(empty($title))  $error['title'] = 1;
      if(empty($ml_id))  $error['mlid']   = 1;
      if(empty($testid)) $error['testid']   = 1;
      if(empty($_POST['groupid'])) $error['groupid']   = 1;
      if(is_exist_transaction_test($testid, $ml_id) > 0) {
        $result_test = $common->find('test', $condition = ['id' => $testid], $type = 'one');
        $error['is_exist_test']   = 1;
        $error['test_id'] = $result_test['id'];
        $error['test_title'] = $result_test['title'];
      }
      //Add apitransaction
      if(0 === count($error) && empty($id))
      {
        //Add data to apitransaction
        $transaction_id = $common->save('apitransaction', $field = ['test_id' => $testid, 'ml_id' => $ml_id, 'title' => $title]);
        foreach ($allGroups as $value) {
          foreach ($_POST['groupid'] as $key => $v) {
            if($v == $value->id){
              //Add data to mailerlite_group
              $common->save('mailerlite_group', $field = ['transaction_id' => $transaction_id, 'group_title' => $value->name, 'group_id' => $value->id]);
            }
          }
        }
        //unset session
        unset($_SESSION['apitransaction']);
        //Redirect
        header('location: '.$admin_file.'?task=apitransaction&mlid='.$ml_id);
        exit;
      }
    }
  }//End action: add apitransaction

  //action: edit apitransaction
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $title   = $common->clean_string($_POST['title']);
      $testid  = $common->clean_string($_POST['testid']);
      $ml_id   = $common->clean_string($_GET['mlid']);
      $id      = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['apitransaction'] = $_POST;
      //form validation
      if(empty($title))  $error['title'] = 1;
      if(empty($ml_id))  $error['mlid']   = 1;
      if(empty($testid)) $error['testid']   = 1;
      if(empty($_POST['groupid'])) $error['groupid']   = 1;

      $result_trans = $common->find('apitransaction', $condition = ['id' => $id], $type = 'one');

      if($result_trans['test_id'] !== $testid && is_exist_transaction_test($testid, $ml_id) > 0) {
        $result_test = $common->find('test', $condition = ['id' => $testid], $type = 'one');
        $error['is_exist_test'] = 1;
        $error['test_id'] = $result_test['id'];
        $error['test_title'] = $result_test['title'];
      }
      //update mailerlite
      if(0 === count($error) && !empty($id))
      {
        //Delete: mailerlite group
        $common->delete('mailerlite_group', $field = ['transaction_id' => $id]);
        $common->update('apitransaction', $field = ['test_id' => $testid, 'title' => $title], $condition = ['id' => $id]);
        foreach ($allGroups as $value) {
          foreach ($_POST['groupid'] as $key => $v) {
            if($v == $value->id){
              //Add data to mailerlite_group
              $common->save('mailerlite_group', $field = ['transaction_id' => $id, 'group_title' => $value->name, 'group_id' => $value->id]);
            }
          }
        }
        //unset session
        unset($_SESSION['apitransaction']);
        //Redirect
        header('location: '.$admin_file.'?task=apitransaction&mlid='.$ml_id);
        exit;
      }
    }
    $smarty_appform->assign('getApiTranByID', $common->find('apitransaction', $condition = ['id' => $_GET['id']], $type = 'one'));
    $smarty_appform->assign('listEditMailerlite', $common->find('mailerlite_group', $condition = ['transaction_id' => $_GET['id']], $type = 'all'));
  }//End action: edit apitransaction

  if('detail' === $action && !empty($_GET['tid']))
  {
    $result_detail = $common->find('mailerlite_group', $condition = ['transaction_id' => $_GET['tid']], $type = 'all');
    header('Content-type: application/json');
    echo json_encode($result_detail);
    exit;
  }
  //Delete: mailerlite
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('apitransaction', $field = ['id' => $_GET['id']]);
    $common->delete('mailerlite_group', $field = ['transaction_id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=apitransaction&mlid='.$_GET['mlid']);
    exit;
  }
  //
  if('delete_mgroup' === $action)
  {
    $result = $common->delete('mailerlite_group', $field = ['id' => $_GET['mgid']]);
    $countMG = countMailerLiteGroup($_GET['tid']);

    if($countMG == 0){
      $common->delete('apitransaction', $field = ['id' => $_GET['tid']]);
    }

    header('Content-type: application/json');
    echo json_encode(array('status' => $result, 'countMG' => $countMG));
    exit;
  }

  $testid = !empty($_GET['tid']) ? $_GET['tid'] : '';
  $result = getListTransaction($_GET['mlid'], $testid, $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  // $smarty_appform->assign('testQuestionIsEmail', getListTestQuestionByIsEmail());
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('mailerliteGroup', $allGroups);
  $smarty_appform->assign('listTransaction', $result);
  $smarty_appform->assign('listTest', $common->find('test', $condition = ['lang' => $lang], $type = 'all'));
  $smarty_appform->display('admin/admin_api_transcation.tpl');
  exit;
}
//Task: Section
if('section' === $task)
{
  if(empty($_POST)) unset($_SESSION['section']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $tid = $common->clean_string($_POST['tid']);
      $name = $common->clean_string($_POST['name']);
      //add value to session to use in template
      $_SESSION['section'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //Add test
      if(0 === count($error))
      {
        $common->save('section', $field = ['test_id' => $tid, 'name' => $name]);
        //unset session
        unset($_SESSION['section']);
        //Redirect
        header('location: '.$admin_file.'?task=section&tid='.$tid);
        exit;
      }
    }
  }

  //action edit
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $tid = $common->clean_string($_POST['tid']);
      $name = $common->clean_string($_POST['name']);
      $id   = $common->clean_string($_POST['id']);
      //add value to session to use in template
      $_SESSION['section'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //update test
      if(0 === count($error) && !empty($id))
      {
        $common->update('section', $field = ['test_id' => $tid, 'name' => $name], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['section']);
        //Redirect
        header('location: '.$admin_file.'?task=section&tid='.$tid);
        exit;
      }
    }
    $smarty_appform->assign('getSectionByID', $common->find('section', $condition = ['id' => $_GET['id']], $type = 'one'));
  }

  //action delete category
  if('delete' === $action && !empty($_GET['id']))
  {
    $result = checkDeleteCategory($_GET['id']);
    if('0' === $result['total_count'])
    {
      $common->delete('category', $field = ['id' => $_GET['id']]);
    }else {
      setcookie('checkCategory', $result['name'], time() + 5);
    }
    header('location: '.$admin_file.'?task=category');
    exit;
  }

  $results = getListSectionByTest($_GET['tid'], $parent_id = 0);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('listSectionByTest', $results);
  $smarty_appform->display('admin/admin_section.tpl');
  exit;
}
//Task: Section
if('section_sub' === $task)
{
  if(empty($_POST)) unset($_SESSION['section_sub']);

  $error = array();
  //action add
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $tid    = $common->clean_string($_POST['tid']);
      $name   = $common->clean_string($_POST['name']);
      $par_id = $common->clean_string($_POST['par_id']);
      //add value to session to use in template
      $_SESSION['section_sub'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //Add test
      if(0 === count($error))
      {
        $common->save('section', $field = ['test_id' => $tid, 'name' => $name, 'parent_id' => $par_id]);
        //unset session
        unset($_SESSION['section']);
        //Redirect
        header('location: '.$admin_file.'?task=section_sub&tid='.$tid.'&par_id='.$par_id);
        exit;
      }
    }
  }

  //action edit
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $tid = $common->clean_string($_POST['tid']);
      $name = $common->clean_string($_POST['name']);
      $id   = $common->clean_string($_POST['id']);
      //add value to session to use in template
      $_SESSION['section_sub'] = $_POST;
      //form validation
      if(empty($name))  $error['name']  = 1;

      //update test
      if(0 === count($error) && !empty($id))
      {
        $common->update('section', $field = ['test_id' => $tid, 'name' => $name], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['section_sub']);
        //Redirect
        header('location: '.$admin_file.'?task=section_sub&tid='.$tid.'&par_id='.$par_id);
        exit;
      }
    }
    $smarty_appform->assign('getSectionByID', $common->find('section', $condition = ['id' => $_GET['id']], $type = 'one'));
  }

  //action delete category
  if('delete' === $action && !empty($_GET['id']))
  {
    $result = checkDeleteCategory($_GET['id']);
    if('0' === $result['total_count'])
    {
      $common->delete('category', $field = ['id' => $_GET['id']]);
    }else {
      setcookie('checkCategory', $result['name'], time() + 5);
    }
    header('location: '.$admin_file.'?task=category');
    exit;
  }

  $results = getListSectionByTest($_GET['tid'], $_GET['par_id']);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('listSectionByTest', $results);
  $smarty_appform->display('admin/admin_section_sub.tpl');
  exit;
}
//Task: Respone
if('response' === $task)
{
  if('export' === $action)
  {
    if($_POST)
    {
      $tid = $_GET['tid'];
      //Get Test By Id
      $resultTest = $common->find('test', $condition = ['id' => $tid], $type = 'one');
      $tqTitle  = preg_replace('/[^A-Za-z0-9 ().]/u','', $resultTest['title']);
      $fileName = str_replace(' ', '-', $tqTitle);
      $dateTime = date('Y-m-d h:i:s');
      $dateFile = date('Y-m-d-(h-i-s)');

      $fileNameCsv  = $fileName.'-'.$dateFile.'.csv';

      $results = $common->save('download', $field =['test_id'     => $tid,
      'title'       => $resultTest['title'],
      'file_name'   => $fileNameCsv,
      'created_at'  => $dateTime]);
      header('Content-type: application/json');
      echo json_encode($results);
      exit;
    }
  }
  $pat_id = !empty($_GET['pat_id']) ? $common->clean_string($_GET['pat_id']) : '';
  $psy_id = !empty($_GET['psy_id']) ? $common->clean_string($_GET['psy_id']) : '';
  $tid = !empty($_GET['tid']) ? $common->clean_string($_GET['tid']) : '';

  $result = getListResponseByTopic($tid, $pat_id, $psy_id, $slimit = 10);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('listResponse', $result);
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid']], $type = 'one'));
  $smarty_appform->display('admin/admin_response.tpl');
  exit;
}
//Task: Response Answer
if('response_answer' === $task)
{
  $rid = !empty($_GET['rid']) ? $common->clean_string($_GET['rid']) : '';

  $result = getListResponseAnswerByTopic($rid);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('listResponseAnswer', $result);
  $smarty_appform->assign('reponseById', getReponseById($rid));
  $smarty_appform->display('admin/admin_response_answer.tpl');
  exit;
}
//Task: Download List
if('download_list' === $task)
{
  if('export_all_email' === $action)
  {
    if($_POST)
    {
      $fileName = 'All-Email';
      $dateTime = date('Y-m-d h:i:s');
      $dateFile = date('Y-m-d-(h-i-s)');

      $fileNameCsv  = $fileName.'-'.$dateFile.'.csv';

      $results = $common->save('download', $field =['title'     => $fileName,
      'file_name' => $fileNameCsv,
      'is_email'  => 2,
      'created_at'=> $dateTime]);

      header('Content-type: application/json');
      echo json_encode($results);
      exit;
    }
  }

  $testid = !empty($_GET['tid']) ? $_GET['tid'] : '';
  $is_email = !empty($_GET['is_email']) ? $_GET['is_email'] : '';
  $status = !empty($_GET['status']) ? $_GET['status'] : '';

  $results = getListDownloadCSVfile($testid, $is_email, $status);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('listDownload', $results);
  $smarty_appform->assign('test', $common->find('test', $condition = null, $type = 'all'));
  $smarty_appform->display('admin/admin_download_list.tpl');
  exit;
}
//Task: Test Psychologist
if('test_psychologist' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['test_psy']);

  $error = array();
  //action: add test_psychologist
  if('add' === $action)
  {
    if($_POST)
    {
      $testid   = $common->clean_string_array($_POST['test']);
      $psy_id   = $common->clean_string($_POST['psy_id']);
      $id       = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['test_psy'] = $_POST;
      //form validation
      if(empty($testid))  $error['testid']  = 1;
      if(empty($psy_id))  $error['psy_id']  = 1;

      //Add test group
      if(0 === count($error) && empty($id))
      {
        foreach ($testid as $key => $va) {
          $common->save('test_psychologist', $field = ['psychologist_id' => $psy_id, 'test_id' => $va]);
        }
        //unset session
        unset($_SESSION['test_psy']);
        //Redirect
        header('location: '.$admin_file.'?task=test_psychologist');
        exit;
      }
    }
  }//End action: add test_psychologist

  //action: edit test_psychologist
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      $testid   = $common->clean_string($_POST['test']);
      $psy_id   = $common->clean_string($_POST['psy_id']);
      $id       = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['test_psy'] = $_POST;
      //form validation
      if(empty($testid))  $error['testid']  = 1;
      if(empty($psy_id))  $error['psy_id']  = 1;

      //update test group
      if(0 === count($error) && !empty($id))
      {
        $common->update('test_psychologist', $field = ['psychologist_id' => $psy_id, 'test_id' => $testid], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['test_psy']);
        //Redirect
        header('location: '.$admin_file.'?task=test_psychologist');
        exit;
      }
    }
    $smarty_appform->assign('getTestPsy', $common->find('test_psychologist', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//End action: test_psychologist

  //Delete: test psychologist
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('test_psychologist', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=test_psychologist');
    exit;
  }

  if('send_mail' === $action)
  {
    $error_smail = array();
    if($_POST)
    {
      $send_psy_id = $common->clean_string($_POST['send_psy_id']);
      $subject= $common->clean_string($_POST['subject']);
      $body   = $common->clean_string($_POST['message']);

      if(empty($send_psy_id)) $error_smail['send_psy_id'] = 1;
      if(empty($subject))     $error_smail['subject'] = 1;

      if(COUNT($error_smail) === 0)
      {
        $resultPsy = $common->find('psychologist', $condition = ['id' => $send_psy_id], $type = 'one');

        $message = Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom(array('noreply@e-khmer.com' => 'Admin PMS'))
                ->setTo(array($resultPsy['email'] => $resultPsy['first_name']))
                ->setBody($body);

        $result = $mailer->send($message);
        setcookie('sendMailSucessfully', 1, time() + 5);
        //Redirect
        header('location: '.$admin_file.'?task=test_psychologist');
        exit;
      }
    }
    $smarty_appform->assign('error_smail', $error_smail);
  }

  $tid    = !empty($_GET['tid']) ? $_GET['tid'] : '';
  $cid    = !empty($_GET['cid']) ? $_GET['cid'] : '';
  $psy_id = !empty($_GET['psy_id']) ? $_GET['psy_id'] : '';
  $status = !empty($_GET['status']) ? $_GET['status'] : '';

  $results = getListTestPsychologist($psy_id, $tid, $cid, $status, $assign_to = '', $stu_ana_file = '');

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('testPsychologist', $results);
  $smarty_appform->assign('test', $common->find('test', $condition = ['lang' => $lang, 'status' => 2], $type = 'all'));
  $smarty_appform->assign('psychologist', $common->find('psychologist', $condition = null, $type = 'all'));
  $smarty_appform->display('admin/admin_test_psychologist.tpl');
  exit;
}
//Task: Test Patient
if('test_patient' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['test_patient']);

  $error = array();
  //action: add test_patient
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $testid   = $common->clean_string_array($_POST['test']);
      $pat_tid  = $common->clean_string($_POST['pat_id']);
      $id       = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['test_psy'] = $_POST;
      //form validation
      if(empty($testid))  $error['testid']  = 1;
      if(empty($pat_tid))  $error['psy_id']  = 1;

      //Add test group
      if(0 === count($error) && empty($id))
      {
        foreach ($testid as $key => $va) {
          $common->save('test_patient', $field = ['patient_id' => $pat_tid, 'test_id' => $va]);
        }
        //unset session
        unset($_SESSION['test_patient']);
        //Redirect
        header('location: '.$admin_file.'?task=test_patient');
        exit;
      }
    }
  }//action: add test_patient

  //action: edit test_patient
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $testid   = $common->clean_string($_POST['test']);
      $pat_tid  = $common->clean_string($_POST['pat_id']);
      $id       = $common->clean_string($_POST['id']);

      //add value to session to use in template
      $_SESSION['test_psy'] = $_POST;
      //form validation
      if(empty($testid))  $error['testid']  = 1;
      if(empty($pat_tid))  $error['psy_id']  = 1;

      //update test group
      if(0 === count($error) && !empty($id))
      {
        $common->update('test_patient', $field = ['patient_id' => $pat_tid, 'test_id' => $testid], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['test_patient']);
        //Redirect
        header('location: '.$admin_file.'?task=test_patient');
        exit;
      }
    }
    $smarty_appform->assign('getTestPat', $common->find('test_patient', $condition = ['id' => $_GET['id']], $type = 'one'));
  }//action: edit test_patient

  //Delete: test psychologist
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('test_patient', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=test_patient');
    exit;
  }

  if('test_completed_psy' === $action)
  {
    $psy_id = $common->clean_string($_GET['psy_id']);
    $results = getListTestPsychologistCompleted($psy_id, $lang);
    header('Content-type: application/json');
    echo json_encode($results);
    exit;
  }

  $tid    = !empty($_GET['tid']) ? $_GET['tid'] : '';
  $pat_id = !empty($_GET['pat_id']) ? $_GET['pat_id'] : '';
  $status = !empty($_GET['status']) ? $_GET['status'] : '';

  $results = getListTestPatient($pat_id, '', $tid, $status, '', '', '', $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);

  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('testPatient', $results);
  $smarty_appform->assign('test', $common->find('test', $condition = ['lang' => $lang, 'status' => 2], $type = 'all'));
  $smarty_appform->assign('patient', $common->find('patient', $condition = null, $type = 'all'));
  $smarty_appform->display('admin/admin_test_patient.tpl');
  exit;
}
//Task: test question hide
if('test_question_hide' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['view_order']);

  $error = array();
  if('add' === $action)
  {
    if($_POST)
    {
      $id       = $common->clean_string($_POST['id']);
      $tid      = $common->clean_string($_GET['tid']);
      $test_que_id = $common->clean_string($_POST['test_question']);

      //add value to session to use in template
      $_SESSION['test_que_hide'] = $_POST;
      //form validation
      if(empty($tid)) $error['tid'] = 1;
      if(empty($test_que_id)) $error['test_question'] = 1;

      if(isTestQuestionHideExist($tid, $test_que_id) > 0) $error['test_que_exist'] = 1;

      //Add test
      if(0 === count($error) && empty($id))
      {
        $common->save('test_question_hide', $field =['test_id' => $tid, 'test_question_id' => $test_que_id]);
        //unset session
        unset($_SESSION['test_que_hide']);
        //Redirect
        header('location: '.$admin_file.'?task=test_question_hide&tid='.$tid);
        exit;
      }
    }
    $smarty_appform->assign('error', $error);
  }//End action: add

  //action delete topic
  if('delete' === $action && !empty($_GET['id']))
  {
    $id  = $common->clean_string($_GET['id']);
    $common->delete('test_question_hide_condition', $field = ['test_question_hide_id' => $id]);
    $common->delete('test_question_hide', $field = ['id' => $id]);
    header('location: '.$admin_file.'?task=test_question_hide&tid='.$_GET['tid']);
    exit;
  }
  //get edit test_topic_answer
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      $id       = $common->clean_string($_POST['id']);
      $tid      = $common->clean_string($_GET['tid']);
      $test_que_id = $common->clean_string($_POST['test_question']);

      //add value to session to use in template
      $_SESSION['test_que_hide'] = $_POST;
      //form validation
      if(empty($tid)) $error['tid'] = 1;
      if(empty($test_que_id)) $error['test_question'] = 1;

      $result = $common->find('test_question_hide', $condition = ['test_id' => $tid, 'test_question_id' => $id], $type = 'one');
      if(!empty($test_que_id) && $result['test_question_id'] !== $test_que_id && isTestQuestionHideExist($tid, $test_que_id) > 0)
      {
        $error['test_que_exist'] = 1;
      }

      //update test
      if(0 === count($error) && !empty($id))
      {
        $common->update('test_question_hide', $field =['test_id' => $tid, 'test_question_id' => $test_que_id], $condition = ['id' => $id]);
        //unset session
        unset($_SESSION['test_que_hide']);
        //Redirect
        header('location: '.$admin_file.'?task=test_question_hide&tid='.$tid);
        exit;
      }
    }
    $id  = $common->clean_string($_GET['id']);
    $smarty_appform->assign('error', $error);
    $smarty_appform->assign('getTestQueHideByID', $common->find('test_question_hide', $condition = ['id' => $id], $type = 'one'));
  }

  $tid  = $common->clean_string($_GET['tid']);

  $results = getListTestQuestionHide($tid);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('listTestQueHide', $results);
  // $smarty_appform->assign('listTestQueGroupAnswer', getTestQuestionGroupAnswer($tid));
  $smarty_appform->assign('listTestQuestion', getListTestQuestion($kwd, $tid, '', '', $lang, $slimit = ''));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $_GET['tid'], 'lang' => $lang], $type = 'one'));
  $smarty_appform->display('admin/admin_test_question_hide.tpl');
  exit;
}
//Task: Test Question Hide condition
if('test_question_hide_condition' === $task)
{
  //Clear session
  if(empty($_POST)) unset($_SESSION['test_que_hide_con']);

  $error = array();
  if('add' === $action)
  {
    if($_POST)
    {
      //get value from form
      $tqhc_id    = $common->clean_string($_POST['tqhc_id']);
      $tqh_id     = $common->clean_string($_POST['tqh_id']);
      $tqid       = $common->clean_string($_POST['tqid']);
      $value      = $common->clean_string($_POST['value']);
      $conditional  = $common->clean_string($_POST['conditional']);
      $operator     = $common->clean_string($_POST['operator']);

      //add value to session to use in template
      $_SESSION['test_que_hide_con'] = $_POST;
      //form validation
      if(empty($tqh_id))      $error['tqh_id'] = 1;
      if(empty($tqid))        $error['tqid'] = 1;
      if(empty($conditional)) $error['conditional'] = 1;
      if(empty($operator))    $error['operator']  = 1;

      //Add result
      if(0 === count($error) && empty($tqhc_id))
      {
        $common->save('test_question_hide_condition', $field = ['test_question_hide_id' => $tqh_id, 'test_question_id' => $tqid, 'conditional' => $conditional, 'operator' => $operator, 'value_condition' => $value]);
        //unset session
        unset($_SESSION['test_que_hide_con']);
        //Redirect
        header('location: '.$admin_file.'?task=test_question_hide_condition&tid='.$_GET['tid'].'&tqid='.$_GET['tqid'].'&tqh_id='.$_GET['tqh_id']);
        exit;
      }
    }
  }
  //action delete result
  if('delete' === $action && !empty($_GET['id']))
  {
    $id   = $common->clean_string($_GET['id']);
    $tid  = $common->clean_string($_GET['tid']);
    $tqid = $common->clean_string($_GET['tqid']);
    $tqh_id = $common->clean_string($_GET['tqh_id']);

    $common->delete('test_question_hide_condition', $field = ['id' => $id]);
    header('location: '.$admin_file.'?task=test_question_hide_condition&tid='.$tid.'&tqid='.$tqid.'&tqh_id='.$tqh_id);
    exit;
  }
  //get edit test question condition
  if('edit' === $action && !empty($_GET['id']))
  {
    if($_POST)
    {
      //get value from form
      $tqhc_id    = $common->clean_string($_POST['tqhc_id']);
      $tqh_id     = $common->clean_string($_POST['tqh_id']);
      $tqid       = $common->clean_string($_POST['tqid']);
      $value      = $common->clean_string($_POST['value']);
      $conditional  = $common->clean_string($_POST['conditional']);
      $operator     = $common->clean_string($_POST['operator']);

      //add value to session to use in template
      $_SESSION['test_que_hide_con'] = $_POST;
      //form validation
      if(empty($tqh_id))      $error['tqh_id'] = 1;
      if(empty($tqid))        $error['tqid'] = 1;
      if(empty($conditional)) $error['conditional'] = 1;
      if(empty($operator))    $error['operator']  = 1;

      //update result
      if(0 === count($error) && !empty($tqhc_id))
      {
        $common->update('test_question_hide_condition', $field = ['test_question_hide_id' => $tqh_id, 'test_question_id' => $tqid, 'conditional' => $conditional, 'operator' => $operator, 'value_condition' => $value], $condition = ['id' => $tqhc_id]);
        //unset session
        unset($_SESSION['test_que_hide_con']);
        //Redirect
        header('location: '.$admin_file.'?task=test_question_hide_condition&tid='.$_GET['tid'].'&tqid='.$_GET['tqid'].'&tqh_id='.$_GET['tqh_id']);
        exit;
      }
    }
    $smarty_appform->assign('getTestQuesHideConByID', $common->find('test_question_hide_condition', $condition = ['id' => $_GET['id']], $type = 'one'));
  }

  $tid  = $common->clean_string($_GET['tid']);
  $tqid = $common->clean_string($_GET['tqid']);
  $tqh_id = $common->clean_string($_GET['tqh_id']);

  $results = getListTestQuesHideCondition($tqh_id, '', $slimit = 10);

  $reTestQue = $common->find('test_question', $condition = ['id' => $tqid], $type = 'one');
  $smarty_appform->assign('error', $error);
  $smarty_appform->assign('listTestQuestionHideCon', $results);
  $smarty_appform->assign('viewTestQueHideCondition', getViewTestQuestionHideCondition($tqh_id));
  $smarty_appform->assign('listTestQuestionByNonHide', getListTestQuestionByNonHide($tid, $tqid));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $tid, 'lang' => $lang], $type = 'one'));
  $smarty_appform->assign('question', $common->find('question', $condition = ['id' => $reTestQue['question_id']], $type = 'one'));
  $smarty_appform->display('admin/admin_test_question_hide_condition.tpl');
  exit;
}
// Task copy_test_question "Copy Test Question, Answer, Answer Topic to other Test"
if('copy_test_question' === $task)
{
  //Get test question
  $rTestQues = $common->find('test_question', $condition = ['id' => $_GET['tqid']], $type = 'one');
  //Check is existed topic
  if(is_exist_test_question($_GET['test_id'], $rTestQues['question_id']) > 0)
  {
    $copy_result   = false;
  } else {
    //Save test question
    $tque_id = $common->save('test_question', $field = ['test_id' => $_GET['test_id'],
    'question_id' => $rTestQues['question_id'],
    'view_order'  => $rTestQues['view_order'],
    'is_required' => $_GET['required']]);
    //Get answer
    $rAnswer = $common->find('answer', $condition = ['test_question_id' => $_GET['tqid']], $type = 'all');
    //Get Topic in Result
    $rGetTopicResult = getTopicResult($_GET['test_id']);

    if(!empty($rAnswer)){
      //Fetch anser
      foreach ($rAnswer as $key => $value) {
        //Save answer
        $answer_id = $common->save('answer', $field = ['title' => $value['title'], 'test_question_id' => $tque_id, 'view_order' => $value['view_order'], 'calculate' => $value['calculate']]);
        $default_value = $value['view_order'];
        $weight_value = 1;

        if(!empty($rGetTopicResult) && !empty($answer_id)){
          foreach ($rGetTopicResult as $k => $v) {
            $common->save('answer_topic', $field = ['answer_id' => $answer_id, 'topic_id'  => $v['topic_id'], 'default_value' => $default_value, 'weight_value'  => $weight_value]);
          }

        }
      }//end Fetch anser
    }

    $copy_result = true;
  }
  header('Content-type: application/json');
  echo json_encode($copy_result);
  exit;
}
// Task copy_test_answer "Copy Test, Answer, Answer Topic to other question"
if('copy_test_answer' === $task)
{
  if($_POST)
  {
    $test_id      = $_POST['tid'];
    $test_que_id  = $_POST['tqid'];
    $question_id  = $_POST['question_id'];
    $required     = $_POST['required'];
    $copy_all     = $_POST['copy_all_value'];

    $rTestQuestionTopView = getTestQuestionTopView($test_id);
    $view_order = $rTestQuestionTopView['view_order'] + 1;

    //Check is has copy all
    if(!empty($copy_all)){
      $rTestQues = $common->find('test_question', $condition = ['test_id' => $test_id], $type = 'all');

      if(!empty($rTestQues)){
        foreach ($rTestQues as $key => $value) {
          if($test_que_id === $value['id']){
            //Get answer
            $allResultAnswer = $common->find('answer', $condition = ['test_question_id' => $value['id']], $type = 'all');
          }

          if($test_que_id != $value['id']){
            //Get answer by test question_id
            $rAnswer = $common->find('answer', $condition = ['test_question_id' => $value['id']], $type = 'all');
            if(!empty($rAnswer)){
              foreach ($rAnswer as $k => $va) {
                $common->delete('answer_topic', $field = ['answer_id' => $va['id']]);
              }
            }
            $common->delete('answer', $field = ['test_question_id' => $value['id']]);
          }//End check not eqaul test_question_id
        }//End fetch $rTestQues

        //fetch save data
        foreach ($rTestQues as $key => $value) {
          if($test_que_id != $value['id']){
            //Get Question By ID
            $rQuestion = $common->find('question', $condition = ['id' => $value['question_id']], $type = 'one');
            //Check question type
            if($rQuestion['type'] == 3 || $rQuestion['type'] == 4){

              //Get Topic in Result
              $rGetTopicResult = getTopicResult($test_id);

              if(!empty($allResultAnswer)){
                //Fetch anser
                foreach ($allResultAnswer as $k => $va) {
                  //Save answer
                  $answer_id = $common->save('answer', $field = ['title' => $va['title'], 'test_question_id' => $value['id'], 'view_order' => $va['view_order'], 'calculate' => $va['calculate']]);
                  $default_value = $va['view_order'];
                  $weight_value = 1;

                  if(!empty($rGetTopicResult) && !empty($answer_id)){
                    foreach ($rGetTopicResult as $k => $v) {
                      $result = $common->save('answer_topic', $field = ['answer_id' => $answer_id, 'topic_id'  => $v['topic_id'], 'default_value' => $default_value, 'weight_value' => $weight_value]);
                    }
                  }
                }//end Fetch answer
              }
            }//End check question type
          }

        }//fetch end save data
        if($result == true){
          $copy_result   = true;
        }else {
          $copy_result   = false;
        }
      }

    }else {
      //Get Question By ID
      $rQuestion = $common->find('question', $condition = ['id' => $question_id], $type = 'one');
      //Check is existed topic, save overide.
      if(is_exist_test_question($_POST['tid'], $_POST['question_id']) > 0) {
        //Get test question by test id
        $rTestQues = $common->find('test_question', $condition = ['test_id' => $test_id, 'question_id' => $question_id], $type = 'one');
        //Update test question
        $common->update('test_question', $field = ['is_required' => $required], $condition = ['id' => $rTestQues['id']]);

        if($rTestQues['id'] !== $test_que_id){
          //Get answer by test question_id
          $rAnswer = $common->find('answer', $condition = ['test_question_id' => $rTestQues['id']], $type = 'all');
          if(!empty($rAnswer)){
            foreach ($rAnswer as $key => $value) {
              $common->delete('answer_topic', $field = ['answer_id' => $value['id']]);
            }
            $common->delete('answer', $field = ['test_question_id' => $rTestQues['id']]);
          }

          //Check question type
          if($rQuestion['type'] == 3 || $rQuestion['type'] == 4){
            //Get answer
            $rAnswer = $common->find('answer', $condition = ['test_question_id' => $test_que_id], $type = 'all');
            //Get Topic in Result
            $rGetTopicResult = getTopicResult($test_id);

            if(!empty($rAnswer)){
              //Fetch anser
              foreach ($rAnswer as $key => $value) {
                //Save answer
                $answer_id = $common->save('answer', $field = ['title' => $value['title'], 'test_question_id' => $rTestQues['id'], 'view_order' => $value['view_order'], 'calculate' => $value['calculate']]);
                $default_value = $value['view_order'];
                $weight_value = 1;

                if(!empty($rGetTopicResult) && !empty($answer_id)){
                  foreach ($rGetTopicResult as $k => $v) {
                    $common->save('answer_topic', $field = ['answer_id' => $answer_id, 'topic_id'  => $v['topic_id'], 'default_value' => $default_value, 'weight_value' => $weight_value]);
                  }
                }
              }//end Fetch answer
            }
          }//End check question type

        }

        $copy_result   = true;
      }else {
        //Save test question
        $tque_id = $common->save('test_question', $field =['test_id' => $test_id,
        'question_id'  => $question_id,
        'view_order'   => $view_order,
        'is_required'  => $required]);
        //Check question type
        if($rQuestion['type'] == 3 || $rQuestion['type'] == 4){
          //Get answer
          $rAnswer = $common->find('answer', $condition = ['test_question_id' => $test_que_id], $type = 'all');
          //Get Topic in Result
          $rGetTopicResult = getTopicResult($test_id);

          if(!empty($rAnswer)){
            //Fetch anser
            foreach ($rAnswer as $key => $value) {
              //Save answer
              $answer_id = $common->save('answer', $field = ['title' => $value['title'], 'test_question_id' => $tque_id, 'view_order' => $value['view_order'], 'calculate' => $value['calculate']]);
              $default_value = $value['view_order'];
              $weight_value = 1;

              if(!empty($rGetTopicResult) && !empty($answer_id)){
                foreach ($rGetTopicResult as $k => $v) {
                  $common->save('answer_topic', $field = ['answer_id' => $answer_id, 'topic_id'  => $v['topic_id'], 'default_value' => $default_value, 'weight_value' => $weight_value]);
                }
              }
            }//end Fetch answer
          }
        }//End check question type

        $copy_result = true;
      }//End Check is existed topic
    }//End Check is has copy all

  }//End Post

  header('Content-type: application/json');
  echo json_encode($copy_result);
  exit;
}
//Task Ajax
if('ajax' === $task)
{
  //action save_group_answer
  if('save_group_answer' === $action)
  {
    $error = array();
    if($_POST){
      $data   = json_decode($_POST['data']);
      $tid    = $_POST['test_id'];
      $gans_flag_id    = $_POST['gans_flag_id'];

      if(!empty($data)){
        foreach ($data as $v) {
          if(is_exist_group_answer_question($tid, $v->test_que_id) > 0) {
            $error['is_exist_group_answer_que']   = 1;
          }
        }
      }
      //Add test group answer
      if(0 === count($error))
      {
        foreach ($data as $v) {
          $common->save('group_answer', $field = ['test_id' => $tid, 'test_question_id' => $v->test_que_id, 'sub_id' => $gans_flag_id]);
          $common->delete('test_question_view_order', $field = ['test_id' => $tid, 'test_question_id' => $v->test_que_id]);
          $common->delete('test_group_question', $field = ['test_question_id' => $v->test_que_id]);
        }
      }
    }
    if(!empty($error)){
      $results = $error;
    }else {
      $results = getListGroupAnswer($tid, $gans_flag_id, '', $slimit = 10);
    }

    header('Content-type: application/json');
    echo json_encode($results);
    exit;
  }
}
//task report staff activity_log
if('psychologist_activity' === $task)
{
  $psy_id = !empty($_GET['psy_id']) ? $_GET['psy_id'] : '';
  $gender = !empty($_GET['gender']) ? $_GET['gender'] : '';
  $kwd    = !empty($_GET['kwd']) ?    $_GET['kwd'] : '';

  $resultPsyActivity = psychologist_activity($kwd, $psy_id, $gender);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('resultPsyActivity', $resultPsyActivity);
  $smarty_appform->assign('listPsychologist', $common->find('psychologist', $condition = null, $type = 'all'));
  $smarty_appform->display('admin/admin_psychologist_activity.tpl');
  exit;
}
//Task: Result psychologist
if('result_test_psychologist' === $task)
{
  if('analysis_file' === $action)
  {
    $error = array();
    if($_POST)
    {
      //Check & Clean String
      $tpsy_id  = $common->clean_string($_POST['tpsy_id']);
      $tid      = $common->clean_string($_POST['tid']);
      $psy_id   = $common->clean_string($_POST['psy_id']);

      if(!empty($_FILES['analysis_file']['name']))
      {
        if($_FILES['analysis_file']['size'] > $allows['SIZE'][0])  $error['size'] = 1;
        if(!in_array($_FILES['analysis_file']['type'], $allows['TYPE']['document'])) $error['type'] = 1;
      }

      if(COUNT($error) === 0)
      {
        $analysis_file = $common->uploadFile($_FILES, time(), ANALYSIS_FILE_PATH, 'analysis_file');
        $common->update('test_psychologist', $field = ['analysis_file' => $analysis_file], $condition = ['id' => $tpsy_id, 'psychologist_id' => $psy_id]);
      }
      //Redirect
      header('location: '.$admin_file.'?task=result_test_psychologist&tid='.$tid.'&psy_id='.$psy_id.'&id='.$tpsy_id);
      exit;
    }

  }

  //Check & Clean String
  $tpsy_id  = $common->clean_string($_GET['id']);
  $tid      = $common->clean_string($_GET['tid']);
  $psy_id   = $common->clean_string($_GET['psy_id']);
  $sumTotal = 0; $sumAssignWeight = 0; $sumDefault = 0; $diagram_width = 820;
  $space_height = 50; $margin_left = 450; $space_row_col = 50;
  $moveTo_left  = $margin_left + 90;
  $moveTo_top   = 60;

  //Get Result Answer Topic
  $getResultTopic = getResultAnswerTopic('', $tpsy_id, $tid, '', '');
  // var_dump($getResultTopic);
  //Get List Topic in diagram second
  $resultTopicDiagramSecond = getListTopicDiagramSecond($getResultTopic, 10, 240, $lang);
  //Get List Topic Analysis
  $resultTopicAnalysis= listTopicAnalysisDiagram($margin_left + 25, 203, $space_row_col, $tid);
  //Get List Topic in diagram first
  $resultTopicDiagram = getListTopicDiagram('', $tpsy_id, $tid,10, 140);
  //Calculate width and height on canvas2
  $resultWidthHeightSecond = calWidthHeightDiagramSecond(COUNT($resultTopicDiagramSecond), COUNT($resultTopicAnalysis), $space_height + 160, $margin_left, $space_result_topic = 150);
  //Drawing Line Result Diagram Second
  $resultDrawingLineResultDiagramSecond = drawingPointLineResultDiagramSecond($resultTopicDiagramSecond, $resultTopicAnalysis, $margin_left + 25, $margin_top = 235, $resultWidthHeightSecond['width'] - 145);

  //Assign value to diagram first
  $smarty_appform->assign('listXlineDiagram', listXlineDiagram(COUNT($resultTopicDiagram), $diagram_width, $space_height + 60));//Horizontal Line
  $smarty_appform->assign('listXdiagramCenter', listXlineDiagramCenter(COUNT($resultTopicDiagram), $diagram_width - 70, $space_height + 85, $margin_left));//List Diagram Horizontal line center
  $smarty_appform->assign('getWidthHeight', calWidthHeightDiagram(COUNT($resultTopicDiagram), $diagram_width, $space_height));//Calculate width and Height on canvas
  $smarty_appform->assign('listNumberMinMax', listNumberMinMax($margin_left, $margin_top = 90));//List Text Number Min & Max
  $smarty_appform->assign('listTextMinMax', listTextMinMax($margin_left, $margin_top = 75));//List Text Min & Max
  $smarty_appform->assign('listBackgroudColor', listBackgroundColorDiagram($margin_left, 110, 50));//List backgroud diagram
  $smarty_appform->assign('drawingPointLine', drawingPointLineResult($resultTopicDiagram, $margin_left - 100, $margin_top = 135, $tid));//Drawing point line
  $smarty_appform->assign('listYLineDiagram', listYLineDiagram($margin_left, 50 + 50));//Vertical Line
  $smarty_appform->assign('listYLineDiagramCenter', listYLineDiagramCenter($margin_left, 50 + 50));//Vertical Line center
  $smarty_appform->assign('listSmallYLineDiagram', listSmall_YlineDiagram($margin_left, 90));//Vertical small Line
  $smarty_appform->assign('listTopicDiagram', $resultTopicDiagram);
  //end

  //Assign value to diagram second
  $smarty_appform->assign('getWidthHeightSecond', $resultWidthHeightSecond);
  $smarty_appform->assign('listTopicDiagramSecond', $resultTopicDiagramSecond);
  $smarty_appform->assign('listTopicAnalysis', $resultTopicAnalysis);
  $smarty_appform->assign('drawingLineResultDiagramSecond', $resultDrawingLineResultDiagramSecond);
  $smarty_appform->assign('listBackgroudColorSecond', listBackgroundColorDiagramSecond(COUNT($resultTopicAnalysis), $margin_left, $space_height + 160, $space_row_col));//List background color Diagram Second
  $smarty_appform->assign('listXlineDiagramSecond', listXlineDiagram(COUNT($resultTopicDiagramSecond), $resultWidthHeightSecond['width'], $space_height + 160));//Horizontal line
  $smarty_appform->assign('listXlineDiagramSecondCenter', listXlineDiagramCenter(COUNT($resultTopicDiagramSecond), $resultWidthHeightSecond['width'] - 150, $space_height + 185, $margin_left)); //List Diagram Second Horizontal line center
  $smarty_appform->assign('listRotateLineDiagramSecond', listRotateLineDiagramSecond(COUNT($resultTopicAnalysis), $margin_left, $space_height + 160, $moveTo_left, $moveTo_top, $space_row_col));//List Rotate Line Diagram Second
  $smarty_appform->assign('listYLineDiagramSecond', listYLineDiagramSecond(COUNT($resultTopicAnalysis), $margin_left, $space_height + 160));//Vertical Line
  //end

  $smarty_appform->assign('reponseAnswerByTestPsyt', getResponseAnswerByTestPsychologist($tid, $tpsy_id));
  $smarty_appform->assign('messageResultTopic', getMessageResultTopic('', $tpsy_id, $tid, $lang));
  $smarty_appform->assign('psychologist', $common->find('psychologist', $condition = ['id' => $psy_id], $type = 'one'));
  $smarty_appform->assign('test_psychologist', $common->find('test_psychologist', $condition = ['psychologist_id' => $psy_id, 'id' => $tpsy_id], $type = 'one'));
  $smarty_appform->assign('test', $common->find('test', $condition = ['id' => $tid, 'lang' => $lang], $type = 'one'));
  $smarty_appform->display('admin/admin_result_test_psychologist.tpl');
  exit;
}

$tid    = !empty($_GET['tid']) ? $common->clean_string($_GET['tid']) : '';
$cid    = !empty($_GET['cid']) ? $common->clean_string($_GET['cid']) : '';
$psy_id = !empty($_GET['psy_id']) ? $common->clean_string($_GET['psy_id']) : '';
$stu_ana_file = !empty($_GET['stu_ana_file']) ? $common->clean_string($_GET['stu_ana_file']) : '';
if(empty($stu_ana_file)) $stu_ana_file = 2;

//task home
$results = getListTestPsychologist($psy_id, $tid, $cid, '', $assign_to = 2, $stu_ana_file);
(0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
SmartyPaginate::assign($smarty_appform);

$smarty_appform->assign('listTestPsychologist', $results);
$smarty_appform->assign('category', $common->find('category', $condition = ['lang' => $lang], $type = 'all'));
$smarty_appform->assign('psychologist', $common->find('psychologist', $condition = null, $type = 'all'));
$smarty_appform->assign('test', $common->find('test', $condition = null, $type = 'all'));
$smarty_appform->display('admin/index.tpl');
exit;
?>
