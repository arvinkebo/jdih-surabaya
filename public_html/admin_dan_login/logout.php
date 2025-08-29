<?php
session_start();
session_unset();
session_destroy();

// ✅ REDIRECT YANG BENAR - ke URL route login admin
header("Location: /login");
exit();
?>
