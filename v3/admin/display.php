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

include '../common/function.get_entry_in_dir.php';

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
        <table class="table">
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
        $lnk_edit = 'edit.php?uid=' . $uid . '&page=' . $page . '&nump=' . $nump;
        $lnk_delete = 'delete.php?uid=' . $uid . '&page=' . $page . '&nump=' . $nump;
        $lnk_upload = 'upload_input.php?usercode=' . $usercode . '&uid=' . $uid . '&page=' . $page . '&nump=' . $nump;
        $lnk_img = 'img_display.php?usercode=' . $usercode . '&uid=' . $uid . '&page=' . $page . '&nump=' . $nump;

        // 網頁內容
        $ihc_content = <<< HEREDOC
        <p>
            <button onclick="location.href='{$lnk_prev}';" class="btn btn-info">返回列表</button>
            <button onclick="location.href='{$lnk_edit}';" class="btn btn-warning">修改</button>
            <button onclick="location.href='{$lnk_upload}';" class="btn btn-success">上傳圖片</button>
            <button onclick="location.href='{$lnk_img}';" class="btn btn-primary">管理圖檔</button>
            <button onclick="if(confirm('確定要刪除嗎？')) {location.href='{$lnk_delete}';}" class="btn btn-danger">刪除</button>
        </p>
        {$data}
HEREDOC;
    }
    else {
        $ihc_data = '<p class="center">查不到相關記錄！</p>';
    }
}
catch(PDOException $e) {
    $ihc_error = error_message('ERROR_QUERY', $e->getMessage());
}

db_close();

// 顯示圖檔部份

// 依類型定義相對應的路徑目錄
$path_img = PATH_UPLOAD_ROOT . $usercode;

// 讀取目錄列出檔案
$a_dir = get_entry_in_dir($path_img, 'FILE');  // 讀取實際檔案
if(!empty($a_dir)) {
    sort($a_dir);

    // 移除非 .jpg 檔
    foreach($a_dir as $key=>$value) {
        $tmp=explode('.', $value);
        $file_ext   = end($tmp);  // 最後一個小數點後的文字為副檔名
        if(strtolower($file_ext)!='jpg') {
            unset($a_dir[$key]); 
        }
    }
}
// echo $path_img;
// echo '<pre>';
// print_r($a_dir);
// echo '</pre>';

$cnt = 0;
$columns = 3;
$data = '<div>';
foreach($a_dir as $one) {  
    $file_show = $path_img . '/' . $one;
    $file_link = $path_img . '/' . $one;

    $img_size = 240;
    $show_w = 240 + 10;
    $show_h = 240 + 20;

    $data .= '<img src="' . $file_show . '" style="padding:6px; vertical-align: middle; max-width:' . $img_size . 'px; max-height:' . $img_size . 'px; _width:expression(this.width > ' . $img_size . ' && this.width > this.height ? ' . $img_size . ': auto);">';
}
$data .= '</div>';


//網頁顯示
$html = <<< HEREDOC
<h2>詳細資料</h2>
{$ihc_content}
{$ihc_error}
<hr>
<h2>圖片資料</h2>
{$data}
<br>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>