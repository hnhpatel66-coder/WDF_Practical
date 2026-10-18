
<?php

if (session_status() === PHP_SESSION_NONE) {

    ini_set("session.use_strict_mode", "1");
    ini_set("session.use_only_cookies", "1");

    session_name("PRACTICAL12_ADMIN");

    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/PRACTICAL_12",
        "secure" => !empty($_SERVER["HTTPS"])
                    && $_SERVER["HTTPS"] !== "off",
        "httponly" => true,
        "samesite" => "Lax"
    ]);

    session_start();
}

function csrf_token() {
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION["csrf_token"];
}

function verify_csrf() {
    $submitted = $_POST["csrf_token"] ?? "";

    if (
        !is_string($submitted) ||
        !hash_equals(csrf_token(), $submitted)
    ) {
        http_response_code(403);
        exit("Invalid security token.");
    }
}
