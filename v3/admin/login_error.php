<?php

$html = <<< HEREDOC
<p>你尚未登入系統，請<a href="login.php">按這裡</a>重新登入！</p>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>