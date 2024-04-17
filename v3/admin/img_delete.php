<?php
session_start();

include '../common/config.php';
include '../common/utility.php';
include '../common/define.php';

$ss_usertype = $_SESSION[DEF_SESSION_USERTYPE] ?? '';
$ss_usercode = $_SESSION[DEF_SESSION_USERCODE] ?? '';

if($ss_usertype!=DEF_LOGIN_ADMIN) {
   header('Location: error.php');
   exit;
}

//*****以上是權限控管 *****

$file = $_GET['file'] ?? '';
$usercode = $_GET['usercode'] ?? '';
$uid = $_GET['uid'] ?? '';
$page = $_GET['page'] ?? '1';
$nump = $_GET['nump'] ?? '10';

// 依類型定義相對應的路徑目錄
$path_img = PATH_UPLOAD_ROOT . $usercode;

// 指定存檔的資料夾
$file_img = $path_img . '/' . $file;

if(!empty($file_img) && file_exists($file_img)) {
   unlink($file_img);
   $msg .= 'testing...圖檔已刪除';
}


$url = 'img_display.php?usercode=' . $usercode . '&uid=' . $uid . '&page=' . $page . '&nump=' . $nump . '&r=' . uniqid();
header('Location: ' . $url);
exit;
?>