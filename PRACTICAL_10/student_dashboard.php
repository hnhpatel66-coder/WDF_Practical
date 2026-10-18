
<?php
require_once "auth.php";
requireLogin("student");

$username = htmlspecialchars($_SESSION['username']);

if (empty($_SESSION['logout_csrf'])) {
    $_SESSION['logout_csrf'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial;
            background: #f0fdf4;
            text-align: center;
            padding: 50px;
        }

        .card {
            background: white;
            max-width: 450px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
        }

        button {
            padding: 12px 25px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="card">
    <h1>Student Dashboard</h1>
    <h2>Welcome, <?= $username ?></h2>

    <p>Role: Student</p>
    <p>Login successful!</p>
    <p>Session Timeout: 15 minutes</p>

    <form action="logout.php" method="POST">
        <input type="hidden"
               name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['logout_csrf']) ?>">
        <button type="submit">Logout</button>
    </form>
</div>

</body>
</html>
