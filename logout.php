<?php
session_start(); // መጀመሪያ ሴሽኑን መክፈት አለብን

// 1. ሁሉንም የሴሽን ቫሪያብሎች ባዶ ማድረግ
$_SESSION = array();

// 2. ሴሽን ኩኪ (Session Cookie) ካለ ማጥፋት
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. ሴሽኑን ሙሉ በሙሉ ማጥፋት (Destroy)
session_destroy();

// 4. ተጠቃሚውን ወደ ሎጊን ገጽ መላክ
header("Location: login.php");
exit();
?>