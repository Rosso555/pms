<?php

/**
 * File:        database.class.php
 *
 * データーベースに接続するライブラリ
 *
 * @copyright 2009 E-KHMER.
 * @author Sengtha Chay <sengtha@e-khmer.coms>
 * @version 0.1
 */
class database
{
	var $debug = false;
	var $_Link = null;
	var $_Connect = null;
	/**
     * データベースの接続を開く
     *
     * @param string $hostname 接続先のホスト名
     * @param string $user データベースのユーザー名
     * @param string $password データベースのパスワード
     * @param string $dbname データベース名
     */
	//function database($hostname, $user, $password, $dbname) {

		//$this->_Connect = mysql_connect($hostname, $user, $password);

		// if ($this->_Connect==false)
		// {
		// 	if ($this->debug) echo "エラー：\n".mysql_error();

		// }
		//データベース名を指定する
		// if($this->_Connect)
		// {
		// 	$this->_Link=mysql_select_db($dbname,$this->_Connect);
		// 	if($this->_Link==false)
		// 	{
		// 		if ($this->debug) echo "エラー：\n".mysql_error();

		// 	}
		// }

   	//}
  public function __construct($hostname, $user, $password, $dbname)
  {

    try
    {

      $this->_Connect = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $password);
			$this->_Connect->exec("SET NAMES utf8;");
			$this->_Connect->exec("SET CHARACTER SET utf8");
			$this->_Connect->exec("SET time_zone = '+7:00'");
      $this->_Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(Exception $e)
    {
      if ($this->debug) echo "Error：". $e->getMessage();

    }

  }

   	/**
     * データベースの接続を閉じる
     *
     */
   	function close() {
    	mysql_close($this->_Connect);
   	}
}

?>
