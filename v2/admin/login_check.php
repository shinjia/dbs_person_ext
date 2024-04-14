<?php
session_start();

include '../common/define.php';

$usercode = $_POST['usercode'] ?? '';
$password = $_POST['password'] ?? '';

// 存帳號及密碼的文字檔，注意格式
$a_list = file(DEF_PASSWORD_FILE);

// 會員檢查，注意格式 (!!帳號##密碼@@)
$password_encrypt = md5(DEF_PASSWORD_PREFIX . $password);  // 加密
$chk_string  = '!!' . $usercode . '##' . $password_encrypt . '@@';

// 比對是否符合
$valid = false;
foreach($a_list as $one) {
    $newstr = substr($one, 0, strpos($one, '@@')+2);
    if($newstr==$chk_string) {
        $valid = true;
        // 取得 usertype
        $n1 = strpos($one,'@@');
        $n2 = strpos($one,'==');
        $usertype = substr($one, $n1+2, $n2-$n1-2);  // 取出 usertype
        break;
    }
}

// 權限是否通過
if($valid) {
    $_SESSION[DEF_SESSION_USERTYPE] = $usertype;
    $_SESSION[DEF_SESSION_USERCODE] = $usercode;
    $msg = $usercode . ' 你好，歡迎光臨！ ';
}
else {
    $_SESSION[DEF_SESSION_USERTYPE] = '';
    $_SESSION[DEF_SESSION_USERCODE] = '';
    $msg = '登入錯誤';
}


$html = <<< HEREDOC
<p>{$msg}</p>
<br><br>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>