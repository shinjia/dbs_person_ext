<?php
session_start();

include '../common/config.php';
include '../common/utility.php';
include '../common/define.php';

$ss_usertype = $_SESSION[DEF_SESSION_USERTYPE] ?? '';
$ss_usercode = $_SESSION[DEF_SESSION_USERTYPE] ?? '';

if($ss_usertype!=DEF_LOGIN_ADMIN) {
   header('Location: error.php');
   exit;
}

//*****以上是權限控管 *****

$usercode = $_GET['usercode'] ?? '';
$file = $_GET['file'] ?? '';

// 依類型定義相對應的路徑目錄
$path_img = '../upload/' . $usercode;

// 指定存檔的資料夾
$file_img = $path_img . '/' . $file;

if(!empty($file_img) && file_exists($file_img)) {
   // echo 'killed'; exit;
   unlink($file_img);
   $msg .= 'testing...圖檔已刪除';
}


$url = 'img_display.php?usercode=' . $usercode . '&r=' . uniqid();
header('Location: ' . $url);
exit;
?>