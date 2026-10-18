
<?php
require "db.php";
require "session.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    verify_csrf();

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter username and password.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id, username, password
             FROM admins
             WHERE username = ?"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (
            $admin &&
            password_verify($password, $admin["password"])
        ) {
            session_regenerate_id(true);

            $_SESSION["admin_id"] = (int)$admin["id"];
            $_SESSION["admin_name"] = $admin["username"];
            $_SESSION["role"] = "admin";
            $_SESSION["last_activity"] = time();

            header("Location: index.php");
            exit;

        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Admin Login - Practical 12</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">

<style>
body {
    min-height: 100vh;
    background: #f4f6f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: Arial, sans-serif;
}

.login-card {
    max-width: 430px;
    width: 100%;
    padding: 35px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

h2 {
    color: #24324a;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="login-card">

    <h2 class="text-center mb-2">
        Admin Login
    </h2>

    <p class="text-center text-muted mb-4">
        Event Management System
    </p>

    <?php if ($error !== ""): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["timeout"])): ?>
        <div class="alert alert-warning">
            Your session expired. Please log in again.
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden"
               name="csrf_token"
               value="<?= htmlspecialchars(csrf_token()) ?>">

        <div class="mb-3">
            <label class="form-label">
                Username
            </label>

            <input type="text"
                   name="username"
                   class="form-control"
                   maxlength="50"
                   autocomplete="username"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">
                Password
            </label>

            <input type="password"
                   name="password"
                   class="form-control"
                   autocomplete="current-password"
                   required>
        </div>

        <button type="submit"
                class="btn btn-primary w-100">
            Login
        </button>

    </form>

</div>

</body>
</html>
