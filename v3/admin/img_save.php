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


// 管理者自訂的上傳規則
$allow_ext = array('jpg');  // 設定可接受上傳的檔案類型
$allow_size = 20000 * 1024;  // 限制接受的檔案大小 (此處設定為 20000K)
$allow_overwrite = true;   // 限制不能覆蓋相同檔名 (若接受，則相同檔名時會覆蓋舊檔)
ini_set('memory_limit', '64M');

$a_file = $_FILES['file'];  // 上傳的檔案內容
$usercode = $_POST['usercode'] ?? 'x';
$uid = $_POST['uid'] ?? '';
$page = $_POST['page'] ?? 1;
$nump = $_POST['nump'] ?? 10;

$path_img = PATH_UPLOAD_ROOT . $usercode;

// 系統設定的錯誤訊息
$upload_errors = array(
   UPLOAD_ERR_INI_SIZE   => '上傳檔案大小超過系統限制。',
   UPLOAD_ERR_FORM_SIZE  => '上傳檔案大小超過HTML表單限制。',
   UPLOAD_ERR_PARTIAL    => '檔案上傳不完整。',
   UPLOAD_ERR_NO_FILE    => '沒有選擇檔案上傳。',
   UPLOAD_ERR_NO_TMP_DIR => '沒有暫存資料夾，請通知管理員。',
   UPLOAD_ERR_CANT_WRITE => '檔案無法寫入磁碟，請通知管理員。',
   UPLOAD_ERR_EXTENSION  => '因副檔名限制無法上傳，請通知管理員。',
);

// 判斷能否存入，若無則建立新的資料夾及新檔案
$path_chk = '.';
$a_path = explode('/', $path_img);
foreach($a_path as $one) {
   $path_chk .= '/' . $one;
   if(!empty($one)) {
      // 路徑
      if(!is_dir($path_chk)) {
         mkdir($path_chk, 0777, true);
      }
   }
}



// 實際上傳的檔案資料
$file_name  = $a_file['name'];
$tmp=explode('.', $a_file['name']);
$file_ext   = end($tmp);  // 最後一個小數點後的文字為副檔名
$file_size  = $a_file['size'];    // 檔案大小
$file_type  = $a_file['type'];
$file_tmp   = $a_file['tmp_name'];
$file_error = $a_file['error'];

// 指定儲存的檔名
$save_filename = $path_img . '/' . $file_name;
   
// 上傳檔案處理
$msg = '';
$check_ok = true;
if($file_error==UPLOAD_ERR_OK && $file_size>0) {  // 先確認有檔案傳上來後再做處理
   // 檢查副檔案是否可以接受
   if(!in_array(strtolower($file_ext), $allow_ext)) {
      $check_ok = false;
      $msg .= '不允許為此類型的檔案。<br>';
   }
   
   // 檢查是否已有相同檔案存在
   if (!$allow_overwrite) {
      if(file_exists($save_filename)) {
         $check_ok = false;
         $msg .= $file_name . ' 檔案已存在，無法儲存。<br>';
      }
   }
	
   // 檢查檔案大小是否在限制之內 
   if($file_size > $allow_size) {
      $check_ok = false;
      $msg .= '檔案大小超過限制。<br>';
   }
	
   // 檢查檔案是真地透過HTTP POST上傳
   if(!is_uploaded_file($file_tmp)) {
      $check_ok = false;
      $msg .= '非此次上傳之檔案，無法處理。<br>';
   }
	
   // 檢查完畢，上傳的最後處理
   if($check_ok) {
      if(@move_uploaded_file($file_tmp, $save_filename)) {
         chmod($save_filename, 0777);
         if(!file_exists($save_filename)) {
            //echo 'fail...' . $save_small ;
            //exit;
         }
         $msg .= '檔案上傳成功：' . $save_filename;
      }
      else {
         $msg .= '不明的原因，檔案上傳失敗。<br>' . $save_filename;
      }
   }
}
else {
   $msg .= '錯誤……' . $file_error . '=>' . $upload_errors[$file_error];
}


$url = 'img_display.php?usercode=' . $usercode . '&uid=' . $uid . '&page=' . $page . '&nump=' . $nump;

header('Location: ' . $url);
exit;
?>