<?php

// 如果要直接重導到某頁，執行下列語法
// header('location: list_page.php');
// exit;

// 顯示網頁內容
$html = <<< HEREDOC
<h2>資料管理系統 dbs_person v1.1</h2>

<p><a href="list_page.php">list_page 列表 (分頁)</a></p>
<p><a href="list_all.php">lis_all 列表 (全部，建議不再使用)</a></p>
<hr/>
<p><a href="findp.php">查詢姓名 (分頁顯示版本)</a></p>
<hr>
HEREDOC;


include 'pagemake.php';
pagemake($html);
?>