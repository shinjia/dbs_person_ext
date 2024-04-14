<?php

include '../common/define.php';

$member = DEF_LOGIN_MEMBER;
$admin  = DEF_LOGIN_ADMIN;

$html = <<< HEREDOC
<h2>建立使用者帳號</h2>
<form name="form1" method="post" action="user_save.php">
帳號：<input type="text"     name="usercode" size="10"><br>
密碼：<input type="password" name="password" size="10"><br>
<br>
用戶類型：
<br><input type="radio" name="usertype" value="{$member}" checked>會員
<br><input type="radio" name="usertype" value="{$admin}">系統管理者
<p>
<input type="submit" value="建立">
</p>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>