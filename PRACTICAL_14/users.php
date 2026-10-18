
<?php
require "auth.php";
requireAdmin();

$users = $conn->query(
    "SELECT id, username, email, role, status
     FROM users ORDER BY id ASC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<?php include "menu.php"; ?>

<div class="container mt-4">

    <h2>Manage Users</h2>

    <div class="card shadow-sm mt-4">
        <div class="card-body table-responsive">

            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$user["id"] ?></td>
                    <td><?= htmlspecialchars($user["username"]) ?></td>
                    <td><?= htmlspecialchars($user["email"]) ?></td>
                    <td><?= htmlspecialchars($user["role"]) ?></td>

                    <td>
                        <?= htmlspecialchars($user["status"]) ?>
                    </td>

                    <td>
                        <?php if ((int)$user["id"] !==
                                      (int)$_SESSION["user_id"]): ?>

                        <form method="POST"
                              action="toggle_status.php">

                            <input type="hidden" name="user_id"
                                   value="<?= (int)$user["id"] ?>">

                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars(
                                       $_SESSION["csrf_token"],
                                       ENT_QUOTES,
                                       "UTF-8"
                                   ) ?>">

                            <button class="btn btn-sm
                                <?= $user["status"] === "active"
                                    ? "btn-warning"
                                    : "btn-success" ?>">

                                <?= $user["status"] === "active"
                                    ? "Deactivate"
                                    : "Activate" ?>

                            </button>
                        </form>

                        <?php else: ?>
                            <span class="text-muted">
                                Current Account
                            </span>
                        <?php endif; ?>
                    </td>
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
