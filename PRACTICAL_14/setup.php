
<?php
require "db.php";

// Temporary demonstration accounts.
// Change these passwords after setup.
$accounts = [
    ["admin", "admin@example.com", "Admin@12345", "admin"],
    ["student", "student@example.com", "Student@12345", "student"]
];

foreach ($accounts as $account) {
    [$username, $email, $password, $role] = $account;

    // Check if user already exists
    $check = $conn->prepare(
        "SELECT id FROM users WHERE username = ? OR email = ?"
    );

    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo htmlspecialchars($username) . " already exists.<br>";
        $check->close();
        continue;
    }
    $check->close();

    // Securely hash password
    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $stmt = $conn->prepare(
        "INSERT INTO users
         (username, email, password, role, status)
         VALUES (?, ?, ?, ?, 'active')"
    );

    $stmt->bind_param(
        "ssss",
        $username,
        $email,
        $hashedPassword,
        $role
    );

    if ($stmt->execute()) {
        echo htmlspecialchars($username) .
             " account created successfully!<br>";
    }

    $stmt->close();
}

$conn->close();
?>
