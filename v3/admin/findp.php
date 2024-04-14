<?php
session_start();

include '../common/config.php';
include '../common/define.php';
include '../common/utility.php';

$ss_usertype = $_SESSION[DEF_SESSION_USERTYPE] ?? '';
$ss_usercode = $_SESSION[DEF_SESSION_USERCODE] ?? '';

if($ss_usertype!=DEF_LOGIN_ADMIN) {
    header('Location: login_error.php');
    exit;
}

//======= 以上為權限控管檢查 ==========================


$html = <<< HEREDOC
<button onclick="history.back();" class="btn btn-primary">返回</button>
<h2>查詢資料</h2>
<form action="findp_x.php" method="post">
   <p>查詢名字內含字：<input type="text" name="key"></p>
   <p><input type="submit" value="查詢"></p>
</form>
HEREDOC;

include 'pagemake.php';
pagemake($html, '');
?>