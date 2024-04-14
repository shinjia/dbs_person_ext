<?php
session_start();

include '../common/define.php';

unset($_SESSION[DEF_SESSION_USERTYPE]);
unset($_SESSION[DEF_SESSION_USERCODE]);


$html = <<< HEREDOC
<p>已登出</p>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>