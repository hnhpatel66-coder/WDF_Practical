
<?php
function recordAudit($conn, $userId, $action) {

    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, action)
         VALUES (?, ?)"
    );

    $stmt->bind_param("is", $userId, $action);

    $success = $stmt->execute();
    $stmt->close();

    return $success;
}
?>
