
<?php
require "auth.php";
requireAdmin();
require "audit_helper.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method Not Allowed");
}

$token = $_POST["csrf_token"] ?? "";

if (!is_string($token) ||
    !hash_equals($_SESSION["csrf_token"], $token)) {
    http_response_code(403);
    exit("Invalid security token");
}

$userId = filter_input(
    INPUT_POST,
    "user_id",
    FILTER_VALIDATE_INT
);

if (!$userId || $userId === (int)$_SESSION["user_id"]) {
    http_response_code(400);
    exit("Invalid user selection");
}

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare(
        "SELECT username, status
         FROM users WHERE id = ? FOR UPDATE"
    );
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        throw new Exception("User not found");
    }

    $newStatus = $user["status"] === "active"
        ? "inactive"
        : "active";

    $stmt = $conn->prepare(
        "UPDATE users SET status = ? WHERE id = ?"
    );
    $stmt->bind_param("si", $newStatus, $userId);
    $stmt->execute();
    $stmt->close();

    $action = "Changed status of user ID " .
              $userId . " to " . $newStatus;

    if (!recordAudit(
        $conn,
        (int)$_SESSION["user_id"],
        $action
    )) {
        throw new Exception("Audit logging failed");
    }

    $conn->commit();

    header("Location: users.php");
    exit;

} catch (Throwable $e) {
    $conn->rollback();
    error_log($e->getMessage());
    http_response_code(500);
    exit("Unable to update user status.");
}
?>
