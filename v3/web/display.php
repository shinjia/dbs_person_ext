<?php
include '../common/config.php';
include '../common/define.php';
include '../common/utility.php';

// 接收傳入變數
$uid = $_GET['uid'] ?? 0;
$page = $_GET['page'] ??  1;   // 目前的頁碼
$nump = $_GET['nump'] ?? 10;   // 每頁的筆數


// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

// 連接資料庫
$pdo = db_open();

// SQL 語法
$sqlstr = "SELECT * FROM person WHERE uid=?";

$sth = $pdo->prepare($sqlstr);
$sth->bindValue(1, $uid, PDO::PARAM_INT);

// 執行 SQL
try {
    $sth->execute();
    
    if($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        $uid = $row['uid'];
        $usercode = html_encode($row['usercode']);
        $username = html_encode($row['username']);
        $address  = html_encode($row['address']);
        $birthday = html_encode($row['birthday']);
        $height   = html_encode($row['height']);
        $weight   = html_encode($row['weight']);
        $remark   = html_encode($row['remark']);

        $data = <<< HEREDOC
        <table>
            <tr><th>代碼</th><td>{$usercode}</td></tr>
            <tr><th>姓名</th><td>{$username}</td></tr>
            <tr><th>地址</th><td>{$address}</td></tr>
            <tr><th>生日</th><td>{$birthday}</td></tr>
            <tr><th>身高</th><td>{$height}</td></tr>
            <tr><th>體重</th><td>{$weight}</td></tr>
            <tr><th>備註</th><td>{$remark}</td></tr>
        </table>
HEREDOC;

        // 網頁連結
        $lnk_prev = 'list_page.php?uid=' . $uid . '&page=' . $page . '&nump=' . $nump;

        // 網頁內容
        $ihc_content = <<< HEREDOC
        <p>
            <button onclick="location.href='{$lnk_prev}';">返回列表</button>
        </p>
        {$data}
HEREDOC;
    }
    else {
        $ihc_data = '<p>查不到相關記錄！</p>';
    }
}
catch(PDOException $e) {
    // db_error(ERROR_QUERY, $e->getMessage());
    $ihc_error = error_message('ERROR_QUERY', $e->getMessage());
}

db_close();


//網頁顯示
$html = <<< HEREDOC
<h2>詳細資料</h2>
{$ihc_content}
{$ihc_error}
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>