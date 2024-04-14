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


$page = $_GET['page'] ??  1;   // 目前的頁碼
$nump = $_GET['nump'] ?? 10;   // 每頁的筆數

// 網頁連結
$lnk_list = 'list_page.php?page=' . $page . '&nump=' . $nump;

$html = <<< HEREDOC
<h2>新增資料區</h2>
<button onclick="location.href='{$lnk_list}';" class="btn btn-primary">返回列表</button>
<form action="add_save.php" method="post">
    <p>代碼：<input type="text" name="usercode"></p>
    <p>姓名：<input type="text" name="username"></p>
    <p>地址：<input type="text" name="address"></p>
    <p>生日：<input type="text" name="birthday"></p>
    <p>身高：<input type="text" name="height"></p>
    <p>體重：<input type="text" name="weight"></p>
    <p>備註：<input type="text" name="remark"></p>
    <input type="hidden" name="page" value="{$page}">
    <input type="hidden" name="nump" value="{$nump}">
    <input type="submit" value="新增">
</form>
HEREDOC;

include 'pagemake.php';
pagemake($html);
?>