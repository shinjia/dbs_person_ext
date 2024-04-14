<?php
session_start();

include '../common/config.php';
include '../common/utility.php';
include '../common/define.php';

$ss_usertype = $_SESSION[DEF_SESSION_USERTYPE] ?? '';
$ss_usercode = $_SESSION[DEF_SESSION_USERTYPE] ?? '';

if($ss_usertype!=DEF_LOGIN_ADMIN) {
   header('Location: error.php');
   exit;
}

//*****以上是權限控管 *****

include '../common/function.get_entry_in_dir.php';

$usercode = $_GET['usercode'] ?? '';

// 依類型定義相對應的路徑目錄
$path_img = '../upload/' . $usercode;

// 讀取目錄列出檔案
$a_dir = get_entry_in_dir($path_img, 'FILE');  // 讀取實際檔案
sort($a_dir);

// 移除非 .jpg 檔
foreach($a_dir as $key=>$one) {
   $tmp=explode(".", $one);
   $file_ext   = end($tmp);  // 最後一個小數點後的文字為副檔名
   if(strtolower($file_ext)!='jpg' && strtolower($file_ext)!='png') {
      unset($a_dir[$key]); 
   }
}

//echo $path_img;
//echo '<pre>';
//print_r($a_dir);
//echo '</pre>';

$cnt = 0;
$columns = 3;
$data = '';
$data .= '<table>';
foreach($a_dir as $one) {  
   // 多欄處理：若為第一欄，資料顯示前需要先加上新列的頭 <TR>
   if(($cnt % $columns)==0) {
      $data .= '<tr>';
   }

   $file_show = $path_img . '/' . $one;
   $file_link = $path_img . '/' . $one;
   
   $img_size = 240;
   $show_w = 240 + 10;
   $show_h = 240 + 20;
   $data .= <<< HEREDOC
   <td align="center" style="width:{$show_w}px;  border:1px; border: 1px solid black;">
      <div class="table_empty">
         <a href="{$file_link}" rel="lightbox[patt]">
         <img src="{$file_show}" style="vertical-align: middle; max-width:{$img_size}px; max-height:{$img_size}px; _width:expression(this.width > {$img_size} && this.width > this.height ? {$img_size}: auto);">
         </a>
         <br>
         <span style="display:none;">
         | <a href="{$file_link}">查看</a>
         | <a href="img_delete.php?usercode={$usercode}&file={$one}" onclick="return confirm('確定要刪除嗎？ ');">刪除</span></a>
         </span>
      <input type="button" value="{$one}" onclick="window.location.href='{$file_link}';">
      <input type="button" value="刪" onclick="do_delete('{$one}');">
      </div>
   </td>
HEREDOC;

   // 多欄處理：若為最後一欄，資料顯示後需要加上此列的尾 </TR>
   if(($cnt % $columns)==($columns-1)) {
      $data .= '</tr>';
   }

   $cnt++;
}
   
// 多欄處理：若每頁筆數($numpp)未調整成欄數的倍數，則每頁均需補後面不足的空項
$cnt1 = $cnt % $columns;  // 此列已顯示的項目數
if( ($cnt1<$columns) && ($cnt1>0) ) { // 不是最後也不是第一個
   for($i=$cnt1+1; $i<=$columns; $i++) {
      $data .= '<td><div style="width:'.$img_size .'px;">&nbsp;</div></td>';
   }
   $data .= '</tr>';
}

$data .= '</table>';

$data_input = <<< HEREDOC
<form name="form1" method="post" action="img_save.php" enctype="multipart/form-data"> 
   <div class="table_empty">  
   <table border="0">
      <tr>
         <td>新檔案上傳：</td>
         <td>
         <input type="file" name="file">
         <input type="hidden" name="MAX_FILE_SIZE" value="20000000">
         <input type="hidden" name="usercode" value="{$usercode}">
         <input type="submit" value="上傳"> (可上傳.jpg檔，或.zip自動解壓)
         </td>
      </tr>
   </table>
   </div>
</form>
HEREDOC;


$head = '';

$head .= <<< HEREDOC
<script language="javascript">
function do_delete(fname) {
   if(confirm('確定要刪除嗎？')) {
      window.location.href= 'poster_delete.php?usercode={$usercode}&file=' + fname;
   }
}
</script>
HEREDOC;


$html = <<< HEREDOC
{$data_input}
<br>
{$data}
HEREDOC;

include 'pagemake.php';
pagemake($html, $head);
?>