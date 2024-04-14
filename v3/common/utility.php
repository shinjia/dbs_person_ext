<?php

function error_message($type='', $ext='') {
    $is_debug = true;

    $a_errmsg = array(
    'ERROR_QUERY' => '資料庫執行發生錯誤',
    'default'     => '有錯誤發生！' );  // 注意最後一項的結尾符號

    $_msg = $a_errmsg[$type] ?? $a_errmsg['default'];

    if($type=='page') {
        $msg = '檔案資料『' . $ext . '』不存在';
    }
    
    $ret_str  = '<h2>錯誤警告</h2>';
    $ret_str .= '<p class="center">' . $_msg . '</p>';
    $ret_str .= ($is_debug) ? ('<p class="center">' . $ext . '</p>') : '';

    return $ret_str;
}


function pagination($total_rec, $total_page, $page, $nump=10) {
    // ------ 分頁處理開始 -------------------------------------
    // 處理分頁之超連結：上一頁、下一頁、第一首、最後頁
    $lnk_pageprev = '?nump='.$nump.'&page=' . (($page==1)?(1):($page-1));
    $lnk_pagenext = '?nump='.$nump.'&page=' . (($page==$total_page)?($total_page):($page+1));
    $lnk_pagehead = '?nump='.$nump.'&page=1';
    $lnk_pagelast = '?nump='.$nump.'&page=' . $total_page;

    // 處理各頁之超連結：列出所有頁數 (暫未用到，保留供參考)
    $lnk_pagelist = "";
    for($i=1; $i<=$page-1; $i++)
    { $lnk_pagelist .= '<a href="?nump='.$nump.'&page='.$i.'">'.$i.'</a> '; }
    $lnk_pagelist .= '[' . $i . '] ';
    for($i=$page+1; $i<=$total_page; $i++)
    { $lnk_pagelist .= '<a href="?nump='.$nump.'&page='.$i.'">'.$i.'</a> '; }

    // 處理各頁之超連結：下拉式跳頁選單
    $frm_pagegoto = '';
    $frm_pagegoto .= '<select class="form-select" name="page" onChange="submit();">';
    for($i=1; $i<=$total_page; $i++) {
        $is_current = (($i-$page)==0) ? ' SELECTED' : '';
        $frm_pagegoto .= '<option' . $is_current . '>' . $i . '</option>';
    }
    $frm_pagegoto .= '</select>';

    // 設定每頁筆數的功能    
    $frm_setnump = '<input type="text" name="nump" value="' . $nump . '" size="1" class="form-control" onChange="submit();">';

    // 將各種超連結組合成HTML顯示畫面
    $_nav = <<< HEREDOC
    <form method="GET" action="" style="margin:0;">
    <ul class="pagination justify-content-center">
    
        <li class="page-item disabled me-3">
            <span class="page-link text-nowrap">總筆數：{$total_rec}</span>
        </li>
        <!-- 總頁數 -->
        <li class="page-item disabled me-3">
            <span class="page-link text-nowrap">總頁數：{$total_page}</span>
        </li>

        <!-- 移至頁碼的按鈕 -->
        <li class="page-item" data-bs-toggle="tooltip" title="第一頁"><a class="page-link" href="{$lnk_pagehead}" aria-label="First">«</a></li>
        <li class="page-item" data-bs-toggle="tooltip" title="上一頁"><a class="page-link" href="{$lnk_pageprev}" aria-label="Previous">‹</a></li>
        <!-- 動態生成的頁碼按鈕可以在這裏插入 -->
        <li class="page-item" data-bs-toggle="tooltip" title="下一頁"><a class="page-link" href="{$lnk_pagenext}" aria-label="Next">›</a></li>
        <li class="page-item" data-bs-toggle="tooltip" title="最末頁"><a class="page-link" href="{$lnk_pagelast}" aria-label="Last">»</a></li>

        <!-- 移至頁碼的下拉選單 -->
        <li class="page-item disabled ms-3">
            <span class="page-link text-nowrap">目前頁</span>
        </li>
        <li class="page-item me-3">
            <div class="input-group">
                {$frm_pagegoto}
            </div>
        </li>

        <!-- 每頁筆數及設定按鈕 -->
        <li class="page-item disabled">
            <span class="page-link text-nowrap">每頁筆數</span>
        </li>
        <li class="page-item">
            {$frm_setnump}
        </li>            
        <li class="page-item">
            <button class="btn btn-outline-secondary">設定</button>
        </li>        
    </ul>
    </form>
HEREDOC;
    // ------ 分頁處理結束 -------------------------------------

    return $_nav;
}


function pagination_ext($total_rec, $total_page, $page, $nump=10, $a_ext=array()) {
    $pre = '';
    $pre_post = ''; 
    foreach($a_ext as $key=>$value) {
        $pre .= $key . '=' . $value . '&';
        $pre_post .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
    }

    // ------ 分頁處理開始 -------------------------------------
    // 處理分頁之超連結：上一頁、下一頁、第一首、最後頁
    $lnk_pageprev = '?'.$pre.'nump='.$nump.'&page=' . (($page==1)?(1):($page-1));
    $lnk_pagenext = '?'.$pre.'nump='.$nump.'&page=' . (($page==$total_page)?($total_page):($page+1));
    $lnk_pagehead = '?'.$pre.'nump='.$nump.'&page=1';
    $lnk_pagelast = '?'.$pre.'nump='.$nump.'&page=' . $total_page;

    // 處理各頁之超連結：列出所有頁數 (暫未用到，保留供參考)
    $lnk_pagelist = "";
    for($i=1; $i<=$page-1; $i++)
    { $lnk_pagelist .= '<a href="?'.$pre.'nump='.$nump.'&page='.$i.'">'.$i.'</a> '; }
    $lnk_pagelist .= '[' . $i . '] ';
    for($i=$page+1; $i<=$total_page; $i++)
    { $lnk_pagelist .= '<a href="?'.$pre.'nump='.$nump.'&page='.$i.'">'.$i.'</a> '; }

    // 處理各頁之超連結：下拉式跳頁選單
    $frm_pagegoto = '';
    $frm_pagegoto .= '<select class="form-select" name="page" onChange="submit();">';
    for($i=1; $i<=$total_page; $i++) {
        $is_current = (($i-$page)==0) ? ' SELECTED' : '';
        $frm_pagegoto .= '<option' . $is_current . '>' . $i . '</option>';
    }
    $frm_pagegoto .= '</select>';

    // 設定每頁筆數的功能    
    $frm_setnump = '<input type="text" name="nump" value="' . $nump . '" size="1" class="form-control" onChange="submit();">';

    // 將各種超連結組合成HTML顯示畫面
    $_nav = <<< HEREDOC
    <form method="GET" action="" style="margin:0;">
    <ul class="pagination justify-content-center">
    
        <li class="page-item disabled me-3">
            <span class="page-link text-nowrap">總筆數：{$total_rec}</span>
        </li>
        <!-- 總頁數 -->
        <li class="page-item disabled me-3">
            <span class="page-link text-nowrap">總頁數：{$total_page}</span>
        </li>

        <!-- 移至頁碼的按鈕 -->
        <li class="page-item" data-bs-toggle="tooltip" title="第一頁"><a class="page-link" href="{$lnk_pagehead}" aria-label="First">«</a></li>
        <li class="page-item" data-bs-toggle="tooltip" title="上一頁"><a class="page-link" href="{$lnk_pageprev}" aria-label="Previous">‹</a></li>
        <!-- 動態生成的頁碼按鈕可以在這裏插入 -->
        <li class="page-item" data-bs-toggle="tooltip" title="下一頁"><a class="page-link" href="{$lnk_pagenext}" aria-label="Next">›</a></li>
        <li class="page-item" data-bs-toggle="tooltip" title="最末頁"><a class="page-link" href="{$lnk_pagelast}" aria-label="Last">»</a></li>

        <!-- 移至頁碼的下拉選單 -->
        <li class="page-item disabled ms-3">
            <span class="page-link text-nowrap">目前頁</span>
        </li>
        <li class="page-item me-3">
            <div class="input-group">
                {$frm_pagegoto}
            </div>
        </li>

        <!-- 每頁筆數及設定按鈕 -->
        <li class="page-item disabled">
            <span class="page-link text-nowrap">每頁筆數</span>
        </li>
        <li class="page-item">
            {$frm_setnump}
            {$pre_post}
        </li>            
        <li class="page-item">
            <button class="btn btn-outline-secondary">設定</button>
        </li>        
    </ul>
    </form>
HEREDOC;
    // ------ 分頁處理結束 -------------------------------------

    return $_nav;
}



function html_encode($sText) {
    $_str = $sText;
        
    $_str = htmlspecialchars($_str, ENT_QUOTES, 'UTF-8');
    //$_str = preg_replace("/&amp;#([[:alnum:]]{3,5});/is","&#\\1;",$_str);
    //$_str = preg_replace("/&amp;([[:alpha:]]{2,7});/is","&\\1;",$_str);
    //$_str = str_replace("\"","&quot;",$_str);
    //$_str = str_replace("'","&#39;",$_str);

    /******************************
    /* 幾個和html轉換有關的函式
    * addslashes() 在特定的符號前加反斜線 (包括 ' " \ 及 NULL)
    (使用get_magic_quotes_gpc() 檢查，若為真，則不能再次使用 addslashes)
    * stripslashes() 刪除由addslashes() 函式增加的反斜線
    * htmlspecialchars() 轉換下表的五個符號
    * htmlentities() 除了轉換下表的五個符號, 也轉換中文 (速度慢)
    * htmlspecialchars_decode() 是htmlspecialchars的反函式
    * html_entity_decode() 是htmlentities的反函式
    * mysql_real_escape_string() 轉換在SQL指令中使用的特殊符號 (包括 x00 \n \r ' " x1a)   
    -----------------------------------------
    & (ampersand)     &amp;
    " (double quote)  &quot;
    ' (single quote)  '&#039; or &apos;
    < (less than)     &lt;
    > (greater than)  &gt;
    ***************************************/
    return $_str;
}

?>