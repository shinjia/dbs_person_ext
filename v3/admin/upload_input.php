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


$usercode = $_GET['usercode'] ?? 'x';
$uid  = $_GET['uid'] ?? 0;
$page = $_GET['page'] ??  1;   // 目前的頁碼
$nump = $_GET['nump'] ?? 10;   // 每頁的筆數

// 網頁連結
$lnk_prev = 'display.php?uid=' . $uid . '&page=' . $page . '&nump=' . $nump;

// 網頁的表頭部分
$head = <<< HEREDOC
    <style>
        #drop_zone {
            border: 2px dashed #0087F7;
            border-radius: 5px;
            width: 480px;
            height: 200px;
            line-height: 200px;
            color: #0087F7;
            text-align: center;
            font-size: 24px;
            margin: 20px;
        }
    </style>
HEREDOC;


// 網頁的內容部分
$html = <<< HEREDOC
<h2>圖片上傳</h2>

<div>
<button onclick="location.href='{$lnk_prev}';" class="btn btn-info">返回單筆顯示</button>
</div>

<div id="drop_zone">拖放檔案至此區域以上傳</div>
<div id="show_zone">等候檔案上傳....</div>
    
<!-- <script src="img_drag_and_drop.js"></script> -->
<script>
    let dropZone = document.getElementById('drop_zone');
    let showZone = document.getElementById('show_zone');
    let file_element_name = 'file';  // FILE 表單元件名稱
    let url_upload = 'upload_save.php?usercode={$usercode}';  // PHP 處理檔案上傳的 URL

    document.addEventListener("DOMContentLoaded", function () {

    dropZone.addEventListener('dragover', function(e) {
        e.stopPropagation();
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
    });

    dropZone.addEventListener('drop', function(e) {
        e.stopPropagation();
        e.preventDefault();
        var files = e.dataTransfer.files;

        for (var i=0, f; f=files[i]; i++) {
            uploadFile(f);
        }
    });

    function uploadFile(file) {
        var xhr = new XMLHttpRequest();
        var formData = new FormData();
        xhr.open('POST', url_upload, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // 檔案上傳成功後的處理
                // alert(xhr.responseText); // 或其他回應處理
                console.log(xhr.responseText);
                // alert(obj.message);
                // 解析 JSON 字符串
                var obj;
                try {
                    obj = JSON.parse(xhr.responseText);
                    showZone.innerHTML += ('<br>' + obj.message);
                    showZone.innerHTML += ('<br><img src="' + obj.imgname + '" style="max-height:100px;">');
                } catch (e) {
                    console.error("Error parsing JSON!", e);
                    showZone.innerHTML += "<br>發生錯誤，無法解析伺服器響應。";
                }
            }
            else {
                // console.error("Server responded with status: " + xhr.status);
                // showZone.innerHTML = "上傳失敗，服務器錯誤 " + xhr.status;
            }
        };
        formData.append(file_element_name, file);
        formData.append('usercode', '{$usercode}');
        xhr.send(formData);
    }
});
</script>
HEREDOC;

include 'pagemake.php';
pagemake($html, $head);
?>