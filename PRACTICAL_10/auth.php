
<?php
require_once "db.php";

const TIMEOUT = 900; // 15 minutes
const REMEMBER_DAYS = 7;

function forgetRemember() {
    global $conn;

    if (!empty($_COOKIE['remember_me'])) {
        $parts = explode(':', $_COOKIE['remember_me']);

        if (count($parts) === 2 &&
            preg_match('/^[a-f0-9]{24}$/', $parts[0])) {
            $stmt = $conn->prepare(
                "DELETE FROM remember_tokens WHERE selector = ?"
            );
            $stmt->bind_param("s", $parts[0]);
            $stmt->execute();
            $stmt->close();
        }
    }

    setcookie('remember_me', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS'])
                    && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    unset($_COOKIE['remember_me']);
}

function endSession() {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => 'Lax'
        ]);
    }

    session_destroy();
}

function startLogin($user) {
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['last_activity'] = time();
}

function createRememberToken($userId) {
    global $conn;

    $selector = bin2hex(random_bytes(12));
    $validator = bin2hex(random_bytes(32));
    $hash = hash('sha256', $validator);

    $stmt = $conn->prepare(
        "INSERT INTO remember_tokens
        (user_id, selector, token_hash, expires_at)
        VALUES (?, ?, ?, DATE_ADD(UTC_TIMESTAMP(), INTERVAL 7 DAY))"
    );

    $stmt->bind_param(
        "iss",
        $userId,
        $selector,
        $hash
    );

    $stmt->execute();
    $stmt->close();

    setcookie('remember_me', $selector . ':' . $validator, [
        'expires' => time() + (REMEMBER_DAYS * 86400),
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS'])
                    && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function restoreRememberLogin() {
    global $conn;

    if (empty($_COOKIE['remember_me'])) {
        return;
    }

    $parts = explode(':', $_COOKIE['remember_me']);

    if (count($parts) !== 2) {
        forgetRemember();
        return;
    }

    [$selector, $validator] = $parts;

    if (!preg_match('/^[a-f0-9]{24}$/', $selector) ||
        !preg_match('/^[a-f0-9]{64}$/', $validator)) {
        forgetRemember();
        return;
    }

    $stmt = $conn->prepare(
        "SELECT t.user_id, t.token_hash,
                u.username, u.role
         FROM remember_tokens t
         JOIN users u ON t.user_id = u.user_id
         WHERE t.selector = ?
         AND t.expires_at > UTC_TIMESTAMP()"
    );

    $stmt->bind_param("s", $selector);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user ||
        !hash_equals(
            $user['token_hash'],
            hash('sha256', $validator)
        )) {
        forgetRemember();
        return;
    }

    // Delete old token before issuing a new one
    forgetRemember();

    startLogin($user);
    createRememberToken($user['user_id']);
}

function checkLogin() {
    if (isset($_SESSION['user_id'])) {
        if (time() - $_SESSION['last_activity'] >= TIMEOUT) {
            forgetRemember();
            endSession();
            header("Location: login.php?timeout=1");
            exit;
        }

        $_SESSION['last_activity'] = time();
        return;
    }

    restoreRememberLogin();
}

function requireLogin($role) {
    checkLogin();

    if (empty($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        exit("Access Denied: Unauthorized role.");
    }
}

function goToDashboard() {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit;
}
?>
