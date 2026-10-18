
<?php
require "auth.php";
requireStudent();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Practical 14 - Student Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .welcome-card {
            border: none;
            border-radius: 12px;
        }
    </style>

</head>

<body>

<!-- Dynamic Role-Based Navigation -->
<?php include "menu.php"; ?>

<div class="container mt-5">

    <div class="card welcome-card shadow-sm">

        <div class="card-body p-4">

            <h2 class="fw-bold">
                Student Dashboard
            </h2>

            <hr>

            <h5>
                Welcome,
                <?= htmlspecialchars($_SESSION["username"]) ?>!
            </h5>

            <div class="alert alert-success mt-4">
                You are successfully logged in as a student.
            </div>

            <p>
                <strong>Username:</strong>
                <?= htmlspecialchars($_SESSION["username"]) ?>
            </p>

            <p>
                <strong>Your Role:</strong>
                <span class="badge bg-primary">
                    <?= htmlspecialchars($_SESSION["role"]) ?>
                </span>
            </p>

            <p class="text-muted">
                Student users cannot access admin-only pages
                such as Manage Users and Audit Logs.
            </p>

        </div>

    </div>

</div>

<!-- Bootstrap JS for mobile menu -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
