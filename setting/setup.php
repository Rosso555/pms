<?php
date_default_timezone_set('Asia/Phnom_Penh');

//Database setting
define('DB_HOSTNAME', 'db.e-khmer.com');
define('DB_USER', 'scusersbdd');
define('DB_PASSWORD', '23#dAAaSAsVsSdf4df32dS');
define('DB_DATABASE_NAME', 'pmsekdb');

//User role
define('ACC_PATIENT', 1);
define('ACC_PSYLOGIST', 2);
define('ACC_CO_WORKER', 3);


/* we can use it anywhere */
/* global varriable */
$admin_file = 'admin.php';
$index_file = 'index.php';
$patient_file = 'patient.php';
$psychologist_file = 'psychologist.php';

//varriable initialize
$debug = true;
$offset = 0;
$limit = 10;
$total_data = null;
$space = '';
$allows   =   array(
  'EXT'   =>  array('jpg', 'png','jpeg', 'gif', 'tiff', 'bmp', 'ico', 'flv', 'mp4', 'ogg',
    'webm', 'qt', 'mp3', 'wav', 'acc', 'pdf', 'doc', 'mov', 'txt', 'xml',
    'zip', 'rar'),
  'SIZE'  =>  array('8388608'),
  'TYPE'  =>  array(
    'image' =>  array('image/jpeg', 'image/png', 'image/jpeg', 'image/gif',
            'image/bmp', 'image/vnd.microsoft.icon', 'image/tiff'),
    'video' =>  array('video/quicktime', 'video/x-flv',
            'video/mp4', 'video/ogg', 'video/webm'),
    'audio' =>  array('audio/mpeg', 'audio/mp4', 'audio/ogg', 'audio/wav', 'audio/aac',
            'audio/webm'),
    'document'  =>  array('text/plain', 'application/pdf', 'application/msword',
            'application/xml', 'application/zip', 'application/x-rar-compressed', 'text/csv',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
  )
);

$admin_username = 'admin';
$admin_password = '783aa605875aa00477ecffeeddbbe65d';

$site_url = 'http://www.pms.e-khmer.com';
$path_exec = '/usr/local/php56/bin/php /home/scusersbnn/pms.e-khmer.com';
//Path
$pathinfo = '/home/scusersbnn/pms.e-khmer.com';

//Email account
define('EMAIL_USERNAME', 'noreply@e-khmer.com');
define('EMAIL_PASSWORD', '7K<dSQ%D#$');
$mail_signature = '
*********************************************************
Psychology Management System (PMS)
E-mail: support@e-khmer.com
Phone: (855)715533175
Website: http://www.pms.e-khmer.com/
*********************************************************';

$site_url = 'http://www.pms.e-khmer.com/';
// define("IMAGE_FILES_PATH", "/home/scusersbnn/chapmeas.e-khmer.com/images/");
define("IMAGE_PATH", "/home/scusersbnn/pms.e-khmer.com/images/staff/");
define("ANALYSIS_FILE_PATH", "/home/scusersbnn/pms.e-khmer.com/documents/analysis_file/");
define("CSV_DOWNLOAD_PATH", "/home/scusersbnn/pms.e-khmer.com/documents/csv_download/");

//Thumbnail
$thumbnail_width= 140;
$thumbnail_height = 140;


//require smarty class
require_once('external_libs/Smarty-3.1.14/libs/Smarty.class.php');
//create new class extend from Smarty Class
class PMS_SMARTY extends Smarty
{
  function __construct()
  {
		parent::__construct();
		$this->template_dir = 'designs/templates/';
		$this->compile_dir = 'designs/templates_c/';
		$this->config_dir = 'designs/configs/';
		$this->cache_dir = 'designs/cache/';

		$this->assign('app_name', 'E-KHMER');

  }
}
