
<?php
require_once "auth.php";

// Check existing login session
checkLogin();

if (isset($_SESSION['user_id'])) {
    goToDashboard();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify CSRF token
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals(
        $_SESSION['csrf_token'],
        $submittedToken
    )) {
        $error = "Invalid request. Refresh the page and try again.";

    } else {

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if (empty($email) || empty($password)) {
            $error = "All fields are required.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email.";

        } else {

            // Search user using email
            $stmt = $conn->prepare(
                "SELECT user_id, username, password, role
                 FROM users
                 WHERE email = ?"
            );

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $stmt->close();

            // Verify password
            if ($user && password_verify(
                $password,
                $user['password']
            )) {

                // Clear any old remember token
                forgetRemember();

                // Create secure login session
                startLogin($user);

                // Update last login
                $update = $conn->prepare(
                    "UPDATE users
                     SET last_login = NOW()
                     WHERE user_id = ?"
                );

                $update->bind_param(
                    "i",
                    $user['user_id']
                );

                $update->execute();
                $update->close();

                // Remember Me
                if ($remember) {
                    createRememberToken(
                        $user['user_id']
                    );
                }

                // Redirect based on role
                goToDashboard();

            } else {
                $error = "Invalid email or password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Practical 10 - Secure Login</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: white;
            width: 360px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }

        h2 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 12px;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 16px 0;
        }

        .remember label {
            margin: 0;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #1d4ed8;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .hint {
            text-align: center;
            margin-top: 20px;
            color: #64748b;
            font-size: 12px;
        }
    </style>
</head>

<body>

<div class="login-box">

    <h2>Secure Login</h2>

    <?php if (!empty($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['timeout'])): ?>
        <div class="error">
            Your session has expired.
            Please login again.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['logout'])): ?>
        <div class="success">
            You have logged out successfully.
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
        >

        <label for="email">Email Address</label>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            required
        >

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <div class="remember">
            <input
                type="checkbox"
                id="remember"
                name="remember"
            >

            <label for="remember">
                Remember Me for 7 days
            </label>
        </div>

        <button type="submit">
            Login
        </button>

    </form>

    <p class="hint">
        Practical 10 - PHP Session Authentication
    </p>

</div>

</body>
</html>
