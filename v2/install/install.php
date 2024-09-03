<?php
include '../common/config.php';
include '../common/function.chinese_name.php';  // add_many 用

// 設定此程式可執行之功能
// 將不允許執行的功能加上註解 (定義的值無意義)
define('HOME'        ,1); // 首頁
// define('CREATE_DB'   ,1); // 新增資料庫
define('CREATE_TABLE',1); // 新增資料表
define('DROP_TABLE'  ,1); // 刪除資料表
define('VIEW_DEFINE' ,1); // 查看定義
define('ADD_DATA'    ,1); // 新增預設資料
define('ADD_MANY'    ,1); // 新增多筆
define('LIST_DATA'   ,1); // 列出資料
define('EXPORT'      ,1); // 匯出 (單一資料表)
define('IMPORT_INPUT',1); // 資料匯入 (IMPORT Step1: input 上傳選檔)
define('IMPORT_SAVE' ,1); // 資料匯入 (IMPORT Step2: save 上傳儲存)
define('IMPORT_EXEC' ,1); // 資料匯入 (IMPORT Step3: exec 執行)
define('SQL_QUERY'   ,1); // 執行自定SQL


// ************ 以下為資料定義，依自行需要進行修改 ************
// 資料表之SQL語法 (採用陣列方式，可以設定多個。注意陣列的key即為資料表名稱)

$a_table['person'] = '
CREATE TABLE person (
    uid int NOT NULL auto_increment,
    usercode VARCHAR(255) NULL,
    username VARCHAR(255) NULL,
    address  VARCHAR(255) NULL,
    birthday DATE default NULL,
    height   INT default NULL,
    weight   INT default NULL,
    remark   VARCHAR(255) NULL,
    PRIMARY KEY  (uid)
)
';


// 如要預先新增記錄，定義於此
$a_record[] = "INSERT INTO person(usercode, username, address, birthday, height, weight, remark) VALUES
 ('P001', 'Allen', '台北', '2021-02-03', '170','75', ''),
 ('P002', 'Bruce', '台中', '2020-08-12', '180','85', 'OK'); ";


// 指定匯入匯出的資料表及對應欄位名稱
$is_title = true;  // 是否第一列要包含欄位名稱
$table_import = 'person';
$a_mapping = array(
'usercode',
'username',
'address',
'birthday',
'height',
'weight',
'remark' );


// 指定匯入匯出的預設檔名
$file_csv = 'output.csv';
$file_temp = '__temp__.csv';


// ************ 以下為此程式之功能執行，毋需修改 ************

// 修改過的 fgetcsv()，才可處理中文資料
function __fgetcsv(&$handle, $length = null, $d = ",", $e = '"') {
    $d = preg_quote($d);
    $e = preg_quote($e);
    $_line = "";
    $eof=false;
    while ($eof != true) {
        $_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
        $itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
        if ($itemcnt % 2 == 0)
            $eof = true;
    }
    $_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));
    $_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
    preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
    $_csv_data = $_csv_matches[1];

    for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
        $_csv_data[$_csv_i] = preg_replace("/^" . $e . "(.*)" . $e . "$/s", "$1", $_csv_data[$_csv_i]);
        $_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
    }
    return empty ($_line) ? false : $_csv_data;
}


function build_fields_table($sth) {
    $ret = '';

    // 以各欄位名稱當表格標題
    $fields = array(); 
    for ($i=0; $i<$sth->columnCount(); $i++) {
        $col = $sth->getColumnMeta($i);
        $fields[] = $col['name'];
    }

    $ret .= '<table border="1" cellpadding="2" cellspaceing="0">';
    $ret .= '<tr>';
    foreach ($fields as $val) {
        $ret .= '<th>' . $val . '</th>';
    }
    $ret .= '</tr>';

    // 列出各筆記錄資料
    while($row=$sth->fetch(PDO::FETCH_ASSOC)) {
        $ret .= '<tr>';
        foreach($row as $one) {
            $ret .= '<td>' . $one . '</td>';
        }
        $ret .= '</tr>';
    }
    $ret .= '</table>';

    return $ret;
}


function build_fields_title($sth) {
    // 以各欄位名稱當表格標題
    $fields = array(); 
    for ($i=0; $i<$sth->columnCount(); $i++) {
        $col = $sth->getColumnMeta($i);
        $fields[] = $col['name'];
    }
    $ret = '';
    foreach ($fields as $val) {
        $ret .= $val . ',';
    }
    $ret = rtrim($ret, ',');  // 移除最後一個逗號
    $ret .= "\r\n";  // 換列
    return $ret;
}


// ***** 各段副程式 *****

function do_add_data($a_record) {
    $pdo = db_open();
    $_msg = '<h2>新增記錄</h2>';
    foreach($a_record as $key=>$sqlstr) {
        $sth = $pdo->query($sqlstr);
        if($sth===FALSE) {
            $_msg .= '<p>無法新增！</p>';
            $_msg .= print_r($pdo->errorInfo(),TRUE);
        }
        else {
            $new_uid = $pdo->lastInsertId();    // 傳回剛才新增記錄的 auto_increment 的欄位值
            $_msg .= '<p>新增成功 (最後 uid=' . $new_uid .  ')</p>';
        }
    }
    return $_msg;
}


function do_add_many($add_count) {
    $a_addr = array('基隆', '台北', '新北', '桃園', '新竹','台中', '彰化', '雲林', '嘉義', '台南', '高雄', '屏東', '台東', '花蓮', '宜蘭', '南投');

    if($add_count==0) {
        $_msg = <<< HEREDOC
        <h2>新增記錄</h2>
        <div class="center">
        <form method="post" action="?do=ADD_MANY" style="margin:0px;">
            一次新增 <input type="text" name="add_count" size="2" value="100"> 筆記錄
            <input type="submit" value="執行">
        </form>
        </div>
        HEREDOC;
    }
    else {
        $pdo = db_open();
        $record_all = '';  // 全部記錄串成的字串
        for($i=1; $i<=$add_count; $i++) {
            // 定義每個資料的範圍及規則
            $usercode = uniqid();
            $username = chinese_name();
            $address  = $a_addr[array_rand($a_addr)];
            $birthday = @date('Y-m-d', @strtotime('-'.mt_rand(0,650*60).' day'));  // 前六十年內的任一天
            $height   = mt_rand(150, 190);
            $weight   = mt_rand(45, 95);
            $remark   = CHR(mt_rand(65, 90));

            // 寫出 SQL 語法
            $record_all .= "(";
            $record_all .= "'$usercode',";
            $record_all .= "'$username',";
            $record_all .= "'$address',";
            $record_all .= "'$birthday',";
            $record_all .= "'$height',";
            $record_all .= "'$weight',";
            $record_all .= "'$remark'),";  // 注意：最後一個欄位之後的符號
        }

        $record_all = rtrim($record_all, ',');  // 移除最後一個逗號

        // SQL 語法
        $sqlstr = "INSERT INTO person(usercode, username, address, birthday, height, weight, remark) VALUES ";
        $sqlstr .= $record_all;

        $sth = $pdo->prepare($sqlstr);
        $timer1 = microtime(true);
        // 執行SQL
        try {
            $sth = $pdo->query($sqlstr);        
            $total_rec = $sth->rowCount();

            $timer2 = microtime(true);
            $time_diff = $timer2 - $timer1;

            $_msg = '<p class="center">新增成功 ' . $add_count . ' 記錄</p>';
            $_msg .= '<p class="center">執行耗費時間：' . $time_diff . ' (秒)</p>';
        }
        catch(PDOException $e) {
            $_msg = $e->getMessage();
        }
    }

    return $_msg;
}


function do_list_data($a_table) {
    $pdo = db_open();
    $_msg = '<h2>記錄內容</h2>';
    foreach($a_table as $key=>$sqlstr) {
        $sqlstr = 'SELECT * FROM ' . $key;
        $sth = $pdo->query($sqlstr);
        $_msg = '<h3>資料表『' . $key . '』</h3>';
        if ($sth===FALSE) {
            $_msg .= '<p>無法顯示</p>';
            $_msg .= print_r($pdo->errorInfo(),TRUE);
        }
        else {
            $_msg .= build_fields_table($sth);
        }
    }
    return $_msg;
}


function do_create_table($a_table) {
    $pdo = db_open();        
    $_msg = '<h2>執行資料表建立</h2>';
    foreach($a_table as $key=>$sqlstr) {
        $_msg .= '<h3>資料表『' . $key . '』</h3>';
        try { 
            $sth = $pdo->query($sqlstr);
            if($sth===FALSE) {
                $_msg .= '<p>建立失敗！</p>';
                $_msg .= print_r($pdo->errorInfo(),TRUE);
            }
            else {
                $_msg .= '<p>建立完成</p>';
            }
        } catch(PDOException $e) {
            $_msg .= '<p>無法建立！</p>';
            $_msg .= $e->getMessage();
        }
    }
    return $_msg;
}


function do_drop_table($a_table) {
    $pdo = db_open();
    // 執行SQL及處理結果
    $_msg = '<h2>執行資料表刪除</h2>';
    foreach($a_table as $key=>$sqlstr) {
        $sqlstr = 'DROP TABLE ' . $key;
        $_msg .= '<h3>資料表『' . $key . '』</h3>';
        try {
            $sth = $pdo->exec($sqlstr);
            if($sth===FALSE) {
                $_msg .= '<p>刪除失敗！</p>';
                $_msg .= print_r($pdo->errorInfo(),TRUE);
            }
            else {
                $_msg .= '<p>刪除成功</p>';
            }
        } catch(PDOException $e) {
            $_msg .= '<p>無法刪除！</p>';
            $_msg .= $e->getMessage();
        }
    }
    return $_msg;
}


function do_create_database() {
    try {
        $pdo = new PDO('mysql:host='.DB_SERVERIP, DB_USERNAME, DB_PASSWORD);
        if(defined('SET_CHARACTER')) $pdo->query(SET_CHARACTER);
        
        $sqlstr = 'CREATE DATABASE ' . DB_DATABASE;
        $sqlstr .= ' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ';   // or utf8
        
        $pdo->exec($sqlstr);  // or die(print_r($pdo->errorInfo(), true));
    }
    catch (PDOException $e) {
        die("DB ERROR: ". $e->getMessage());
    }
    $_msg .= '<h2>資料庫建立</h2>';
    $_msg .= print_r($pdo->errorInfo(),TRUE);
    $_msg .= '<p>資料庫『' . DB_DATABASE . '』</p>';
    $_msg .= '<p>' . $sqlstr . '</p>';
    $_msg .= '<p>如要刪除 DROP DATABASE ' . DB_DATABASE . '</p>';
    return $_msg;
}


function do_view_define($a_table, $a_record) {
    $_msg = '<table border="0"><tr><td>';
    $_msg .= '<div align="left">';
    $_msg .= '<h2>資料表 (程式內定義)</h2>';
    foreach($a_table as $key=>$sqlstr) {
        $_msg .= '<h3>' . $key . '<h3>';
        $_msg .= '<pre>' . $sqlstr . '</pre><hr />';
    }
    $_msg .= '<h2>預設 SQL (程式內定義)</h2><hr />';
    foreach($a_record as $key=>$sqlstr) {
        $_msg .= '<pre>' . $sqlstr . '</pre>';
    }
    $_msg .= '</div>';
    $_msg .= '</td></tr></table>';
    return $_msg;
}


function do_export($table_import, $a_mapping, $file_csv, $is_title) {
    // 資料表及匯入的各個欄位
    $ary = array();
    foreach($a_mapping as $k=>$value) {
        $ary[] = $value;
    }
    // 開始匯出
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $file_csv);
    $output = fopen("php://output", "w"); 
    if($is_title) {
        fputcsv($output, $ary);  // 匯出欄位名稱
    }
    // 連接資料庫
    $pdo = db_open();
    $sqlstr = "SELECT * FROM " . $table_import;
    $sth = $pdo->prepare($sqlstr);
    if($sth->execute()) {
        $total_rec = $sth->rowCount();
        $data = '';
        while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            unset($row['uid']);  // remove uid field
            fputcsv($output, $row);
        }
    }
    else {
        die('Error!');
    }
    fclose($output);
    die();
}


function do_import_input() {
    $_msg = <<< HEREDOC
    <h2>匯入檔案上傳</h2>
    <p>選擇要匯入的 .csv 檔案 (請自行確認格式及內容的正確)</p>
    <form name="form1" method="post" action="?do=IMPORT_SAVE" enctype="multipart/form-data">
    檔案：<input type="file" name="file">
    <input type="submit" value="上傳">
    </form>
HEREDOC;
    return $_msg;
}


function do_import_save($file_temp) {
    $a_file = $_FILES["file"];  // 上傳的檔案內容
    // 上傳檔案處理
    if($a_file["size"]>0) {
        $save_filename = $file_temp;
        move_uploaded_file($a_file["tmp_name"], $save_filename);
    }
    header('Location: ?do=IMPORT_EXEC');
}


function do_import_exec($table_import, $a_mapping, $file_temp) {
    $msg = '';
    // 資料表及匯入的各個欄位
    $sqlstr = "INSERT INTO $table_import(";
    foreach($a_mapping as $k=>$value) {
        $sqlstr .= $value . ',';
    }
    $sqlstr = rtrim($sqlstr, ',');  // 移除最後一個逗號
    $sqlstr .= ') VALUES ';

    $pdo = db_open();
    $time1 = microtime(TRUE);
    $cnt_record = 0;
    // 讀入資料後逐筆新增
    if((@$handle = fopen($file_temp, "r")) !== FALSE) {
        $record_all = '';
        while(($row = __fgetcsv($handle)) !== FALSE) {
            $cnt_record++;
            $cnt = 0;
            $record_one = '(';
            foreach($a_mapping as $value) {
                $one = str_replace("'", "\'", $row[$cnt]);  // 處理單引號
                $cnt++;
                $record_one .= "'" . $one . "',";
            }
            $record_one = rtrim($record_one, ',');  // 移除最後一個逗號
            $record_one .= '),';

            $record_all .= $record_one;
        }
        fclose($handle);
        $record_all = rtrim($record_all, ',');  // 移除最後一個逗號
        $sqlstr .= $record_all;

        if($pdo->query($sqlstr)) {
            $msg .= '已新增 ' . $cnt_record . ' 筆記錄';
        }
    }
    @unlink($file_temp);
    $time2 = microtime(TRUE);
    $spend = $time2 - $time1;
    $msg .= '<p>共花費時間：' . $spend . '</p>';
    return $msg;
}


function do_sql_query($sql, $a_table, $is_sql_save, $is_fields_title, $file_csv) {    
    $_msg = <<< HEREDOC
    <h2>請輸入SQL指令</h2>
    <form name="form1" method="post" action="?do=SQL_QUERY">
    <textarea name="sql" rows="4" cols="80">{$sql}</textarea><br />
    <input type="submit" value="送出查詢">
    是否輸出到檔案 <input type="checkbox" name="sql_save" value="Y"> |
    含表頭 <input type="checkbox" name="fields_title" value="Y">
    </form>
    <hr />
HEREDOC;
    if(empty($sql)) {
        $_msg .= '<h2>SQL 範例</h2>' ;
        $_msg .= '<p>';
        $_msg .= 'SHOW TABLES<br>';
        $_msg .= 'SHOW TABLE STATUS<br>';
        $_msg .= '=======================<br>';
        foreach($a_table as $key=>$sqlstr) {
            $_msg .= 'DESC ' . $key . '<br>';
            // $_msg .= '<p>SHOW COLUMNS FROM ' . $key . '</p>';
            $_msg .= 'SELECT count(*) FROM ' . $key . '<br>';
            $_msg .= 'SELECT * FROM ' . $key . '<br>';
            $_msg .= '-----------------------------------<br>';
        }
        $_msg .= '</p>';

    }
    else {
        try {
            $pdo = db_open();
            $sqlstr = $sql;
            $sth = $pdo->query($sqlstr);
            if($sth===FALSE) {
                $_msg .= '<h3>執行結果失敗！</h3>';
                $_msg .= print_r($pdo->errorInfo(),TRUE);
            }
            else {
                // SELECT 語法結果
                $_msg .= '<h3>rowCount: ' . $sth->rowCount() . '</h3>';
                $_msg .= build_fields_table($sth);
            }
        }
        catch (PDOException $e) {            
            $_msg .= '<h3>執行結果失敗！</h3>';
            $_msg .= $e->getMessage();
        }
    }
    
    // 依 SQL 匯出
    if($is_sql_save) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $file_csv);
        $output = fopen("php://output", "w"); 
        // 連接資料庫
        $pdo = db_open();
        $sqlstr = $sql;
        $sth = $pdo->prepare($sqlstr);
        if($sth->execute()) {
            // 匯出欄位名稱
            if($is_fields_title) {
                echo build_fields_title($sth);
            }
            
            // 匯出各筆資料
            $total_rec = $sth->rowCount();
            $data = '';
            while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                // unset($row['uid']);  // remove uid field
                fputcsv($output, $row);
            }
        }
        else {
            die('Error!');
        }
        fclose($output);
        die();
    }
    return $_msg;
}


// ***** 主程式 *****

$do = $_GET['do'] ?? 'HOME';

// 接收傳入變數 (供 SQL_INPUT 及 SQL_QUERY 使用)
$sql = $_POST['sql'] ?? '';
$sql = stripslashes($sql);  // 去除表單傳遞時產生的脫逸符號

$sql_save = $_POST['sql_save'] ?? '';
$fields_title = $_POST['fields_title'] ?? '';

$is_sql_save = ($sql_save=='Y') ? true : false;
$is_fields_title = ($fields_title=='Y') ? true : false;

// add many 用
$add_count = $_POST['add_count'] ?? 0;

$msg = '';

// 檢查功能是否允許
if(!defined($do)) {
    $msg .= '******無法執行此功能！******';
}
else {
switch($do) {
    case 'ADD_DATA' :
        $msg = do_add_data($a_record);
        break;

    case 'ADD_MANY' :
        $msg = do_add_many($add_count);
        break;

    case 'LIST_DATA' :
        $msg = do_list_data($a_table);
        break;
        
    case 'CREATE_TABLE' : 
        $msg = do_create_table($a_table);
        break;
        
    case 'DROP_TABLE' : 
        $msg = do_drop_table($a_table);
        break;

    case 'CREATE_DATABASE' : 
        $msg = do_create_database();
        break;

    case 'VIEW_DEFINE' :
        $msg = do_view_define($a_table, $a_record);
        break;

    case 'SQL_QUERY' :
        $msg = do_sql_query($sql, $a_table, $is_sql_save, $is_fields_title, $file_csv);
        break;

    case 'EXPORT' :
        do_export($table_import, $a_mapping, $file_csv, $is_title);

    case 'IMPORT_INPUT' :
        $msg = do_import_input();
        break;
        
    case 'IMPORT_SAVE' :
        do_import_save($file_temp);
        break;

    case 'IMPORT_EXEC' :
        $msg = do_import_exec($table_import, $a_mapping, $file_temp);
        break;
} /* end of switch */
} /* end of if..else */


// 顯示功能表列
$menu  = '';
$menu .= '| <a href="?do=HOME">安裝首頁</a> ';
$menu .= !defined('VIEW_DEFINE') ? '' : '| <a href="?do=VIEW_DEFINE">程式內SQL定義</a> ';
$menu .= '| --- ';
$menu .= !defined('CREATE_DATABASE') ? '' : '| <a href="?do=CREATE_DATABASE">建立資料庫</a> ';
$menu .= '| --- ';
$menu .= !defined('CREATE_TABLE') ? '' : '| <a href="?do=CREATE_TABLE">建立資料表</a> ';
$menu .= !defined('DROP_TABLE') ? '' : '| <a href="?do=DROP_TABLE" onClick="return confirm(\'確定要刪除嗎？\');">刪除資料表</a> ';
$menu .= '| --- ';
$menu .= !defined('EXPORT') ? '' : '| <a href="?do=EXPORT">匯出</a> ';
$menu .= !defined('IMPORT_INPUT') ? '' : '| <a href="?do=IMPORT_INPUT">匯入</a> ';
$menu .= '| --- ';
$menu .= !defined('ADD_DATA') ? '' : '| <a href="?do=ADD_DATA">新增預設記錄</a> ';
$menu .= !defined('ADD_MANY') ? '' : '| <a href="?do=ADD_MANY">隨機新增多筆</a> ';
$menu .= !defined('LIST_DATA') ? '' : '| <a href="?do=LIST_DATA">查看記錄</a> ';
$menu .= '| --- ';
$menu .= !defined('SQL_QUERY') ? '' : '| <a href="?do=SQL_QUERY">SQL測試</a> ';
$menu .= '|';


$html = <<< HEREDOC
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>基本資料庫系統 - 安裝程式</title>
</head>
<body>
    <h2>初始安裝工具程式</h2>
    <p>{$menu}</p>
    <hr>
    <div align="center">
    {$msg}
    </div>
</body>
</html>
HEREDOC;

echo $html;
?>