<?php
include '../common/config.php';
include '../common/define.php';
include '../common/utility.php';

// 接收傳入變數
$uid = $_GET['uid'] ?? 0;
$page = $_GET['page'] ?? 1;   // 目前的頁碼
$nump = $_GET['nump'] ?? 10;   // 每頁的筆數

// 網頁內容預設
$ihc_content = '';
$ihc_error = '';

// 連接資料庫
$pdo = db_open();

// SQL 語法
$sqlstr = "SELECT * FROM person WHERE uid=? ";

$sth = $pdo->prepare($sqlstr);
$sth->bindValue(1, $uid, PDO::PARAM_INT);

// 執行 SQL
try { 
    $sth->execute();

    if($row = $sth->fetch(PDO::FETCH_ASSOC))
    {
        $uid = $row['uid'];

        $usercode = html_encode($row['usercode']);
        $username = html_encode($row['username']);
        $address  = html_encode($row['address']);
        $birthday = html_encode($row['birthday']);
        $height   = html_encode($row['height']);
        $weight   = html_encode($row['weight']);
        $remark   = html_encode($row['remark']);
        
        // 網頁連結
        $lnk_list = 'list_page.php?uid=' . $uid . '&page=' . $page . '&nump=' . $nump;

        $data = <<< HEREDOC
        <button onclick="location.href='{$lnk_list}';">返回列表</button>
        <form action="edit_save.php" method="post">
        <table>
            <tr><th>代碼</th><td><input type="text" name="usercode" value="{$usercode}"></td></tr>
            <tr><th>姓名</th><td><input type="text" name="username" value="{$username}"></td></tr>
            <tr><th>地址</th><td><input type="text" name="address" value="{$address}"></td></tr>
            <tr><th>生日</th><td><input type="text" name="birthday" value="{$birthday}"></td></tr>
            <tr><th>身高</th><td><input type="text" name="height" value="{$height}"></td></tr>
            <tr><th>體重</th><td><input type="text" name="weight" value="{$weight}"></td></tr>
            <tr><th>備註</th><td><input type="text" name="remark" value="{$remark}"></td></tr>
        </table>
        <p>
            <input type="hidden" name="uid" value="{$uid}">
            <input type="hidden" name="page" value="{$page}">
            <input type="hidden" name="nump" value="{$nump}">
            <input type="submit" value="送出">
        </p>
        </form>
HEREDOC;
    }
    else {
        $data = '<p>無資料</p>';
    }

    //網頁顯示
    $ihc_content = <<< HEREDOC
    <div>
        {$data}
    </div>
HEREDOC;
}
catch(PDOException $e) {
    // db_error(ERROR_QUERY, $e->getMessage());
    $ihc_error = error_message('ERROR_QUERY', $e->getMessage());
}

db_close();


//網頁顯示
$html = <<< HEREDOC
<h2>修改資料</h2>
{$ihc_content}
{$ihc_error}
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>