<?php
include '../common/config.php';
include '../common/define.php';
include '../common/utility.php';

$html = <<< HEREDOC
<button onclick="history.back();">返回</button>
<h2>查詢資料</h2>
<form action="find_x.php" method="post">
   <p>查詢名字內含字：<input type="text" name="key"></p>
   <p><input type="submit" value="查詢"></p>
</form>
HEREDOC;

include 'pagemake.php';
pagemake($html, '');
?>