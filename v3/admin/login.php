<?php

$html = <<< HEREDOC
<h2>登入</h2>
<form name="form1" method="post" action="login_check.php">
帳號：<input type="text"     name="usercode" size="10"><br>
密碼：<input type="password" name="password" size="10"><br>
<br>
<input type="submit" value="登入">
</form>
<hr>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>