
<?php
require_once "auth.php";
requireLogin("admin");

$username = htmlspecialchars($_SESSION['username']);

$stmt = $conn->prepare(
    "SELECT last_login FROM users WHERE user_id = ?"
);

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$lastLogin = $user['last_login'] ?? 'Not available';

if (empty($_SESSION['logout_csrf'])) {
    $_SESSION['logout_csrf'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial;
            text-align: center;
            background: #eef2f7;
            padding: 50px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 450px;
            margin: auto;
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
    <h1>Admin Dashboard</h1>
    <h2>Welcome, <?= $username ?></h2>

    <p>Role: Administrator</p>
    <p>Login successful!</p>

    <p>Last Login:
        <?= htmlspecialchars($lastLogin) ?>
    </p>

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
