
<?php
require_once "auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method not allowed.");
}

if (empty($_SESSION['logout_csrf']) ||
    !hash_equals(
        $_SESSION['logout_csrf'],
        $_POST['csrf_token'] ?? ''
    )) {
    http_response_code(403);
    exit("Invalid logout request.");
}

forgetRemember();
endSession();

header("Location: login.php?logout=1");
exit;
?>
