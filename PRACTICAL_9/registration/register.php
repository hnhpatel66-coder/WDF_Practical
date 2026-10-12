<?php

require "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {

        $message = "All fields are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Invalid email format.";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";

    } else {

        $check = $conn->prepare(
            "SELECT user_id FROM users WHERE email = ? OR username = ?"
        );

        $check->bind_param("ss", $email, $username);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Email or username already exists.";

        } else {

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users (username, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sss",
                $username,
                $email,
                $hashed_password
            );

          if ($stmt->execute()) {

    // Save registration activity in audit_logs
    $action = "User Registered";

    $log = $conn->prepare(
        "INSERT INTO audit_logs (username, email, action)
         VALUES (?, ?, ?)"
    );

    $log->bind_param("sss", $username, $email, $action);

    if ($log->execute()) {
        $message = "Registration successful!";
    } else {
        $message = "Registration successful, but audit log failed.";
    }

    $log->close();

} else {
    $message = "Registration failed.";
} 

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>User Registration</title>

</head>

<body>

<h2>Secure User Registration</h2>

<form method="POST">

    <label>Username:</label><br>

    <input
        type="text"
        name="username"
        required
        minlength="3"
    >

    <br><br>

    <label>Email:</label><br>

    <input
        type="email"
        name="email"
        required
    >

    <br><br>

    <label>Password:</label><br>

    <input
        type="password"
        name="password"
        required
        minlength="6"
    >

    <br><br>

    <label>Confirm Password:</label><br>

    <input
        type="password"
        name="confirm_password"
        required
        minlength="6"
    >

    <br><br>

    <button type="submit">
        Register
    </button>

</form>

<br>

<?php

if ($message != "") {
    echo "<b>" . htmlspecialchars($message) . "</b>";
}

?>

</body>
</html>