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
if('login' === $task){
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

    if(0 === count($error)){
      //compare username and password in form
      if($admin_username  === $username && $admin_password === md5($password)){
        //assign value to session
        $_SESSION['is_admin_login'] = 'admin';
        //remove session to clear data
        unset($_SESSION['admin']);
        //redirect to admin.php
        header('Location:'.$admin_file);
        exit;
      }
      //wrong username and password
      $error['login'] = 1;
    }
  }
  //default of login task
  $smarty_appform->assign('error', $error);
  $smarty_appform->display('admin/login.tpl');
  exit;
}
//task: logout by clear session
if('logout' === $task){
  unset($_SESSION['is_admin_login']);
  header('Location:'.$admin_file.'?task=login');
  exit;
}
//redirect if no session
if(empty($_SESSION['is_admin_login'])){
  header('Location:'.$admin_file.'?task=login');
  exit;
}
//task multi_language
if('multi_language' === $task)
{
  $error = array();
  if($_POST)
  {
    //get value from form
    $key_lang       = $common->clean_string($_POST['key_lang']);
    $unique_id      = time();

    //add value to session to use in template
    $_SESSION['translate'] = $_POST;

    //form validation
    if(empty($key_lang))    $error['key_lang']  = 1;
    $result = getMultilangByID($_GET['unique_id']);

    if(!empty($key_lang) && $result['key_lang'] !== $key_lang && is_key_lang_exist($key_lang) > 0) $error['is_key_lang_exist'] = 2;

    //Add question
    if(0 === count($error) && empty($_GET['unique_id']))
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
    //update question
    if(0 === count($error) && !empty($_GET['unique_id']))
    {
      foreach ($_POST['title'] as $k => $v) {
        $common->update('multi_lang', $field = ['title' => $_POST['title'][$k], 'key_lang' => $key_lang, 'lang' => $_POST['lang'][$k]], $condition = ['id' => $_POST['id'][$k]]);
      }
      //unset session
      unset($_SESSION['question']);
      //Redirect
      header('location: '.$admin_file.'?task=multi_language');
      exit;
    }

  }
  //action delete question
  if('delete' === $action && !empty($_GET['unique_id']))
  {
    $common->delete('multi_lang', $field = ['unique_id' => $_GET['unique_id']]);
    header('location: '.$admin_file.'?task=multi_language');
    exit;
  }
  //get edit question
  if('edit' === $action && !empty($_GET['unique_id']))
  {
    $smarty_appform->assign('getMultilangByUID', getMultilangByID($_GET['unique_id']));
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
  $error = array();
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

    //Add language
    if(0 === count($error) && empty($id))
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
  //action delete language
  if('delete' === $action && !empty($_GET['id']))
  {
    // $result = $common->find('language', $condition = ['id' => $_GET['id']], $type = 'one');
    $common->delete('multi_lang', $field = ['language_id' => $_GET['id']]);
    $common->delete('language', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=add_language');
    exit;
  }
  //get edit language
  if('edit' === $action && !empty($_GET['id']))
  {
    $smarty_appform->assign('getLanguageByID', $common->find('language', $condition = ['id' => $_GET['id']], $type = 'one'));
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
  $error = array();
  if($_POST)
  {
    //get value from form
    $name = $common->clean_string($_POST['name']);
    $id   = $common->clean_string($_POST['id']);

    //add value to session to use in template
    $_SESSION['category'] = $_POST;
    //form validation
    if(empty($name))  $error['name']  = 1;

    //Add test
    if(0 === count($error) && empty($id))
    {
      $common->save('category', $field = ['name' => $name, 'lang' => $lang]);
      //unset session
      unset($_SESSION['category']);
      //Redirect
      header('location: '.$admin_file.'?task=category');
      exit;
    }
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
  //action delete category
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('category', $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=category');
    exit;
  }
  //get edit category
  if('edit' === $action && !empty($_GET['id']))
  {
    $smarty_appform->assign('getCategoryByID', $common->find('category', $condition = ['id' => $_GET['id']], $type = 'one'));
  }

  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $result = listCategory($kwd, $lang);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('listCategory', $result);
  $smarty_appform->display('admin/category.tpl');
  exit;
}
//task permission
if('staff_permission' === $task)
{
  $getsrid = (isset($_GET['sr_id']))?$_GET['sr_id']:'';
  unset($_SESSION['staff_permission']);
  $error = array();
  if($_POST)
  {
   $staff_function_id = $common->clean_string($_POST['staff_function_id']);
   $staff_role_id     = $common->clean_string($_POST['staff_role_id']);

   if(empty($staff_function_id))   $error['staff_function_id']=1;
   if(empty($staff_role_id))       $error['staff_role_id'] =1;
   $_SESSION['staff_permission'] = $_POST;

   if(0 === count($error) && empty($_POST['id']))
   {
     if(!empty($lang) && is_staff_permission_exist('staff_permission', $staff_role_id, $staff_function_id) > 0)
     {
       $error['exist_save'] = 1;
     }else{
       $common->save('staff_permission', $field = ['staff_function_id'=> $staff_function_id, 'staff_role_id'=> $staff_role_id] );
       //reset session
       unset($_SESSION['staff_permission']);
       //Redirect
       header('location: '.$admin_file.'?task=staff_permission');
       exit;
    }
   }
   if(0 === count($error) && !empty($_POST['id']))
   {
     $common->update('staff_permission', $field = ['staff_function_id' => $staff_function_id, 'staff_role_id' => $staff_role_id], $conditon = ['id' => $_POST['id']]);
     //reset session
     unset($_SESSION['staff_permission']);
     header('location: '.$admin_file.'?task=staff_permission');
     exit;
   }
   $smarty_appform->assign('error', $error);
  }
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete('staff_permission', $field = ['id'=>$_GET['id']]);
    header('location: '.$admin_file.'?task=staff_permission');
    exit;
  }
  //action edit staff permission
  if('edit' === $action && !empty($_GET['id'])) $smarty_appform->assign('edit_staff_permission', $common->find('staff_permission', $condition = ['id' => $_GET['id']], $type = 'one'));
  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
  $all_staffpermission = ListStaffPermission($kwd);
  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('search_by_role',ListStaffRoleByPermission());
  $smarty_appform->assign('list_Staff_Permission',$all_staffpermission);
  $smarty_appform->assign('list_staff_role', $common->find('staff_role', $condition = null, $type = 'all'));
  $smarty_appform->assign('list_staff_function', $common->find('staff_function', $condition = null, $type = 'all'));
  $smarty_appform->display('admin/staff_permission.tpl');
  exit;
}
//task staff Role
if('staff_role' === $task)
{
  $error = array();
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
  //action delete staff role
  if('delete' === $action && !empty($_GET['id']))
  {
    if(!empty($lang) && is_staff_role_exist('staff_permission', $_GET['id']) > 0)
    {
      $error['exist_delete'] = 1;
    }else{
      $common->delete("staff_role", $field = ['id' => $_GET['id']]);
      header('location: '.$admin_file.'?task=staff_role');
      exit;
    }
  }
  if('edit' === $action && !empty($_GET['id']))
  {
    $smarty_appform->assign('edit',$common->find('staff_role', $condition = ['id' => $_GET['id']], $type='one'));
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
  if(!empty($_SESSION['staff_info'])) unset($_SESSION['staff_info']);
  $error = array();
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
      if(!empty($password) && !$common->checkPassword($password))
      {
        $error['password'] = 2;
      }
      if(!empty($_POST['old_file'])){
        $image = $old_fle;
      }else{
        if(empty($_FILES['image']['name'])){
          $error['no_image'] = 1;
        }
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
          // echo $image;exit;
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
          $common->save('staff', $field = ['name'     => $name,
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
        $common->update('staff', $field = ['name' => $name,
                                                'password'  => $pass,
                                                'gender'    => $gender,
                                                'phone'     => $phone,
                                                'photo'     => $image,
                                                'staff_role_id' =>$staff_role
                                               ],
        $condition = ['id' => $id]);
        $_SESSION['staff_info'] = '';
        unset($_SESSION['staff_info']);
        //Redirect
        header('location:'.$admin_file.'?task=staff_info');
        exit;
      }
    $smarty_appform->assign('error', $error);
	}
  //action delete staff role
  if('delete' === $action && !empty($_GET['id']))
  {
    $common->delete("staff_role", $field = ['id' => $_GET['id']]);
    header('location: '.$admin_file.'?task=staff_info');
    exit;
  }
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
  if('edit' === $action && !empty($_GET['id']))
  {
    $smarty_appform->assign('edit',$common->find('staff', $condition = ['id' => $_GET['id']], $type='one'));
  }
  $kwd = !empty($_GET['kwd']) ? $_GET['kwd'] : '';
	$results = ListStaffInfo($kwd);
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
  if(!empty($_SESSION['staff_function'])) unset($_SESSION['staff_function']);
  $error = array();
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
    if(!empty($task) && !empty($action) && is_staff_function_exits($task, $action) > 0) $error['is_staff_function_exist'] = 2;
    if(count($error) === 0 && empty($id))
    {
      $common->save('staff_function',$field = ['title' => $title, 'task_name'=>$task,'action_name'=>$action]);
      $_SESSION['staff_function'] = '';
      unset($_SESSION['staff_function']);
      //Redirect
      header('location:'.$admin_file.'?task=staff_function');
      exit;
    }
    if(count($error) === 0 && !empty($id))
    {
      $common->update('staff_function', $field = ['title' => $title,'task_name'=>$task, 'action_name'=>$action], $condition = ['id' => $id]);
      $_SESSION['staff_function'] = '';
      unset($_SESSION['staff_function']);
      //Redirect
      header('location:'.$admin_file.'?task=staff_function');
      exit;
    }
    $smarty_appform->assign('error', $error);
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
  if('edit' === $action && !empty($_GET['id']))
  {
    $smarty_appform->assign('edit',$common->find('staff_function', $condition = ['id' => $_GET['id']], $type='one'));
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
  $error = array();
  if($_POST){
    //get value from form
    $id     = $common->clean_string($_POST['id']);
    $username = $common->clean_string($_POST['username']);
    $email  = $common->clean_string($_POST['email']);
    $phone  = $common->clean_string($_POST['phone']);
    $gender = $common->clean_string($_POST['gender']);
    $age    = $common->clean_string($_POST['age']);
    $password = $common->clean_string($_POST['password']);
    //add value to session to use in template
    $_SESSION['patient'] = $_POST;
    //form validation
    if(empty($username))  $error['username']  = 1;
    if(empty($email))   $error['email']   = 1;
    if(empty($phone))   $error['phone']   = 1;
    if(empty($gender))  $error['gender']  = 1;
    if(empty($age))     $error['age']     = 1;
    if(empty($password))  $error['password']  = 1;
    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
		   $error['invalid_email'] = 1;
		}
    if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
      $existed = check_patient_email($email);
      if($existed > 0){
        $error['exist_email'] = 1;
      }
    }
    //Save
    if(empty($id) && COUNT($error) === 0){
      $common->save('patient', $field =['psychologist_id' => $_SESSION['is_psycho_login_id'],
                                        'username'=> $username,
                                        'email'   => $email,
                                        'phone'   => $phone,
                                        'gender'  => $gender,
                                        'age'     => $age,
                                        'password'=> $password]);
    //unset session
    unset($_SESSION['patient']);
    //Redirect
    header('location: '.$admin_file.'?task=patient');
    exit;
    }
    //Update
    if(!empty($id) && COUNT($error) === 0){
      $common->update('patient', $field= ['username' => $username,
                                          'email'    => $email,
                                          'phone'    => $phone,
                                          'gender'   => $gender,
                                          'age'      => $age,
                                          'password' => $password],
                                 $condition = ['id' => $_GET['id'], 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
    //unset session
    unset($_SESSION['patient']);
    //Redirect
    header('location: '.$admin_file.'?task=patient');
    exit;
    }
  }
  //Change staus patient
  if('change_status' === $action && !empty($_GET['id']))
  {
    if(!empty($_GET['status'] == 1))
    {
      $common->update('patient', $field = ['status' => 2], $condition = ['id' => $_GET['id'], 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
    }elseif (!empty($_GET['status'] == 2)) {
      $common->update('patient', $field = ['status' => 1], $condition = ['id' => $_GET['id'], 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
    }
    header('location:'.$admin_file.'?task=patient');
    exit;
  }
  //action delete staff role
  if('delete' === $action && !empty($_GET['id']))
  {
    $deleted_at = date("Y-m-d");
    $common->update('patient', $field = ['deleted_at' => $deleted_at], $condition = ['id' => $_GET['id'], 'psychologist_id' => $_SESSION['is_psycho_login_id']]);
    header('location:'.$admin_file.'?task=patient');
    exit;
  }
  //Action: edit
  if('edit' === $action && !empty($_GET['id']))
  {
    $resutlGetPatientById = getPatientByID($_GET['id'], $_SESSION['is_psycho_login_id']);

    if(!empty($resutlGetPatientById) && COUNT($resutlGetPatientById) > 0){
      $smarty_appform->assign('editPatient', $resutlGetPatientById);
    }else {
      header('location:'.$admin_file.'?task=page_not_found');
      exit;
    }
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
if('psychologist' === $task){

  $result = listPsychologistAdmin($kwd, $psy_id, $gender, $status);

  (0 < $total_data) ? SmartyPaginate::setTotal($total_data) : SmartyPaginate::setTotal(1) ;
  SmartyPaginate::assign($smarty_appform);
  $smarty_appform->assign('listPsychologistData', $result);
  $smarty_appform->assign('listPsychologist', $common->find('psychologist', $condition = null, $type = 'all'));
  $smarty_appform->display('admin/admin_psychologist.tpl');
  exit;
}
//task home
$smarty_appform->display('admin/index.tpl');
exit;
 ?>
