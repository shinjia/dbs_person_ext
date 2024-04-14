<?php

function get_entry_in_dir($path='.', $type='ALL', $includeHidden=false) {
   $directories = [];
   $files = [];

   // 檢查目錄是否存在且為目錄
   if (!is_dir($path)) {
      return [];  // 如果不是有效的目錄，直接返回空陣列
   }

   // 使用 scandir() 來獲取路徑中所有條目
   $entries = scandir($path);
   if($entries === false) {
       // 如果 scandir() 失敗，返回空陣列
      return [];
   }

   foreach($entries as $entry) {
      if(!$includeHidden && substr($entry, 0, 1)==='.') {
         continue;  // 跳過隱藏文件或目錄
      }
      if($entry==='.' || $entry==='..') {
         continue;  // 跳過特殊條目
      }

      $fullPath = $path . '/' . $entry;
      if(is_dir($fullPath)) {
         if($type !== 'FILE') {
            $directories[] = $entry;  // 只在需要時加入目錄
         }
      }
      else {
         if ($type !== 'DIR') {
            $files[] = $entry;  // 只在需要時加入文件
         }
      }
   }
   
   // 根據請求的類型返回相應的數組
   switch ($type) {
      case 'ALL':
         return array_merge($directories, $files);
      case 'DIR':
         return $directories;
      case 'FILE':
         return $files;
      default:
         return [];
   }
}   

/* Old Version
function get_entry_in_dir($_path=".", $_type="ALL") {
   $_ary["DIR"] = array();
   $_ary["FILE"] = array();
   if ($dir = @opendir($_path)) { 
      while(($file = readdir($dir)) !== false) { 
         if($file != "." && $file != "..") {
            if(is_dir($_path."/".$file)) {
               array_push($_ary["DIR"] , $file);
            }
            else {
               array_push($_ary["FILE"], $file);
            }
         } 
      }   
      closedir($dir); 
   }
   
   switch($_type) {
      case "ALL" :
           $_ary_ret = array_merge($_ary["DIR"], $_ary["FILE"]);
           break;
           
      case "DIR" :
           $_ary_ret = $_ary["DIR"];
           break;
           
      case "FILE" :
           $_ary_ret = $_ary["FILE"];
           break;
           
      default :
           $_ary_ret = array();
   }
   return $_ary_ret;
}
*/
?>