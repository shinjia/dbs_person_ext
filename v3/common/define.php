<?php

// 系統代碼 (用於系統檢查，例如 uid,chk)
define('SYSTEM_CODE', 'DBS');

/***** SESSION LOGIN 權限控管相關 ******************/
// 定義 SESSION 的變數名稱
define('DEF_SESSION_USERTYPE', 'DBS_usertype');
define('DEF_SESSION_USERCODE', 'DBS_usercode');

// 用於登入權限及密碼檢查
define('DEF_PASSWORD_FILE', 'user_password.txt');  // 密碼文字檔
define('DEF_PASSWORD_PREFIX', 'DBS');  // 密碼加密的前置文字

// 登入權限檢查的判斷條件，不同的系統要改名稱
define('DEF_LOGIN_ADMIN', 'DBS_ADMIN');   // 登入檢查，ADMIN
define('DEF_LOGIN_MEMBER', 'DBS_MEMBER');   // 登入檢查，MEMBER


/***** 圖檔上傳 ******************/
define('PATH_UPLOAD_ROOT', '../upload/');

?>