<?php

function get_entry_in_dir($_path=".", $_type="ALL")
{
   $_ary["DIR"] = array();
   $_ary["FILE"] = array();
   if ($dir = @opendir($_path))
   { 
      while(($file = readdir($dir)) !== false)
      { 
         if($file != "." && $file != "..")
         {
            if(is_dir($_path."/".$file))
            {
               array_push($_ary["DIR"] , $file);
            }
            else
            {
               array_push($_ary["FILE"], $file);
            }
         } 
      }   
      closedir($dir); 
   }
   
   switch($_type)
   {
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

?>