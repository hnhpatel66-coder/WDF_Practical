
<?php
require_once "auth.php";

checkLogin();

if (isset($_SESSION['user_id'])) {
    goToDashboard();
}

header("Location: login.php");
exit;
?>
