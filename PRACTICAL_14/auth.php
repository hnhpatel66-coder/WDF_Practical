
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

// Generate CSRF token for security
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

function requireLogin() {
    global $conn;

    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }

    // Verify current user from database
    $stmt = $conn->prepare(
        "SELECT username, role, status
         FROM users WHERE id = ?"
    );

    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user || $user["status"] !== "active") {
        $_SESSION = [];
        session_destroy();

        header("Location: login.php");
        exit;
    }

    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];
}

function requireAdmin() {
    requireLogin();

    if ($_SESSION["role"] !== "admin") {
        http_response_code(403);
        exit("Access Denied: Admins Only");
    }
}

function requireStudent() {
    requireLogin();

    if ($_SESSION["role"] !== "student") {
        http_response_code(403);
        exit("Access Denied: Students Only");
    }
}
?>
