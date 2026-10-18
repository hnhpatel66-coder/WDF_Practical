
<?php
require "auth.php";
requireAdmin();

$logs = $conn->query(
    "SELECT a.id, u.username, a.action, a.created_at
     FROM audit_logs a
     LEFT JOIN users u ON a.user_id = u.id
     ORDER BY a.id DESC
     LIMIT 100"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1">
<title>Audit Logs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">
</head>

<body class="bg-light">

<?php include "menu.php"; ?>

<div class="container mt-4">
    <h2>Admin Audit Logs</h2>

    <div class="card shadow-sm mt-4">
        <div class="card-body table-responsive">

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Time</th>
                    </tr>
                </thead>

                <tbody>
                <?php while ($log = $logs->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int)$log["id"] ?></td>
                        <td><?= htmlspecialchars(
                            $log["username"] ?? "Deleted User"
                        ) ?></td>
                        <td><?= htmlspecialchars($log["action"]) ?></td>
                        <td><?= htmlspecialchars($log["created_at"]) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
