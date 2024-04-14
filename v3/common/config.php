<?php

// 資料庫連接的重要參數
define('DB_SERVERIP', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'class');

// For PDO MySQL
define('DB_SOURCE', 'mysql:host='.DB_SERVERIP.';dbname='.DB_DATABASE);

// 關於字元的設定參數
define('SET_CHARACTER', 'set character set utf8mb4');   // utf8或big5或此列加註移除
define('CHARSET', 'utf8');   // utf8或此列加註移除 (此用於PHP 5.0.5以上)

// 關於錯誤訊息的文字
define('ERROR_CONNECT'  , 'Cannot connect server');  // 無法連接伺服器
define('ERROR_DATABASE' , 'Cannot open database');  // 無法開啟資料庫
define('ERROR_CHARACTER', 'Character set error');  // 無法使用指定的校對字元集
define('ERROR_QUERY'    , 'Error in SQL Query');  // 無法執行資料庫查詢指令
define('ERROR_CHARSET'  , 'Set charset error');  // 無法設定指定的校對指令
define('ERROR_DBSOURCE' , 'DB source error.');  // 無法連接 DB_SOURCE(PDO)


function db_open() {
   try {
      $pdo = new PDO(DB_SOURCE, DB_USERNAME, DB_PASSWORD);
      if(defined('SET_CHARACTER')) $pdo->query(SET_CHARACTER);
      // 指定 PDO 錯誤模式和錯誤處理
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   } catch (PDOException $e) { db_error('ERROR_DBSOURCE', $e->getMessage()); } 
      
   return $pdo;
}

function db_close() {
   // Do nothing
}

function db_error($type='', $ext='') {
   $is_debug = true;  // 是否顯示系統捕捉的錯訊訊息

   $msg = '<h2>DB Error! </h2>';
   $msg .= '<p>' . $type . '</p>';

   if($is_debug) {
      $msg .= '<p>' . $ext . '</p>';
   }
   
   die($msg);
}
?>