<?php

$usercode = $_POST['usercode'] ?? '';
$password = $_POST['password'] ?? '';
$usertype = $_POST['usertype'] ?? '';

include '../common/define.php';

// 存帳號及密碼的文字檔，注意格式
$file_password = DEF_PASSWORD_FILE;   // 存帳號及密碼的文字檔

// 注意格式
$password_encrypt = md5(DEF_PASSWORD_PREFIX . $password);  // 加密
$new_user_string  = '!!' . $usercode . '##' . $password_encrypt . '@@' . $usertype . '==';
$new_user_string .= "\r\n";  // 注意不同的作業系統的結尾符號

// 將新資料加到原有清單之後
file_put_contents($file_password, $new_user_string, FILE_APPEND);


$html = <<< HEREDOC
<p>使用者帳號已建立</p>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>