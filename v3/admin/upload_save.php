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


// 串連上傳的 usercode
$usercode = $_POST['usercode'] ?? 'x';

// 管理者自訂的上傳規則
$allow_ext = array('jpg');  // 設定可接受上傳的檔案類型
$allow_size = 5000 * 1024;  // 限制接受的檔案大小 (此處設定為 5MB)
$allow_overwrite = true;   // 限制不能覆蓋相同檔名 (若接受，則相同檔名時會覆蓋舊檔)

// 指定存檔的資料夾
// 判斷能否存入，若無則建立新的資料夾
if(!is_dir(PATH_UPLOAD_ROOT)) {
    mkdir(PATH_UPLOAD_ROOT, 0777, true);
}

$path = PATH_UPLOAD_ROOT . $usercode . '/';
// 判斷能否存入，若無則建立新的資料夾
if(!is_dir($path)) {
    mkdir($path, 0777, true);
}

// 系統設定的錯誤訊息
$upload_errors = array(
    UPLOAD_ERR_INI_SIZE   => '上傳檔案大小超過系統限制。',
    UPLOAD_ERR_FORM_SIZE  => '上傳檔案大小超過HTML表單限制。',
    UPLOAD_ERR_PARTIAL    => '檔案上傳不完整。',
    UPLOAD_ERR_NO_FILE    => '沒有選擇檔案上傳。',
    UPLOAD_ERR_NO_TMP_DIR => '沒有暫存資料夾，請通知管理員。',
    UPLOAD_ERR_CANT_WRITE => '檔案無法寫入磁碟，請通知管理員。',
    UPLOAD_ERR_EXTENSION  => '因副檔名限制無法上傳，請通知管理員。',
);

$response = array();

if (isset($_FILES['file'])) {
    $a_file = $_FILES['file'];
    // 實際上傳的檔案資料
    $file_name  = $a_file['name'];
    $file_size  = $a_file['size'];
    $file_type  = $a_file['type'];
    $file_tmp   = $a_file['tmp_name'];
    $file_error = $a_file['error'];
    // 取得副檔名 (最後一個小數點後的文字)
    $tmp = explode('.', $file_name);
    $file_ext = end($tmp);

    // 指定儲存的檔名
    $save_name = $file_name;
    $save_full = $path . $save_name;

    // 上傳檔案處理
    $check_ok = true;
    if($file_error==UPLOAD_ERR_OK && $file_size>0) {  // 先確認有檔案傳上來後再做處理
        // 檢查副檔案是否可以接受
        if(!in_array(strtolower($file_ext), $allow_ext)) {
            $check_ok = false;        
            $response['status'] = 'error';
            $response['message'] = '不接受的檔案類型';
        }
        
        // 檢查是否已有相同檔案存在
        if (!$allow_overwrite) {
            if(file_exists($save_full)) {            
                $check_ok = false;
                $response['status'] = 'error';
                $response['message'] = '檔案已存在，無法儲存';
            }
        }
            
        // 檢查檔案大小是否在限制之內 
        if($file_size > $allow_size) {
            $check_ok = false;
            $response['status'] = 'error';
            $response['message'] = '檔案大小超過限制';
        }
            
        // 檢查檔案是真地透過HTTP POST上傳
        if(!is_uploaded_file($file_tmp)) {
            $check_ok = false;
            $response['status'] = 'error';
            $response['message'] = '非此次上傳之檔案，無法處理';
        }
            
        // 檢查完畢，上傳的最後處理
        if($check_ok) {
            if(@move_uploaded_file($file_tmp, $save_full)) {
                $response['status'] = 'success';
                $response['message'] = '檔案上傳成功: ' . $save_name;
                $response['imgname'] = $save_full;
            }
            else {
                $response['status'] = 'error';
                $response['message'] = '不明的原因，檔案上傳失敗';
            }
        }
    }
    else {
        $response['status'] = 'error';
        $response['message'] = '錯誤……' . $file_error . "=>" . $upload_errors[$file_error];
    }
}

// for Test
// echo '<pre>';
// print_r($response);
// echo '</pre>';

// 回傳 JSON 格式的回應給客戶端
header('Content-Type: application/json');
echo json_encode($response);
?>