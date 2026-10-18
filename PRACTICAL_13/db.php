
<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "practical13";

try {

    $conn = new mysqli(
        $host,
        $username,
        $password,
        $database
    );

    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {

    error_log("Database error: " . $e->getMessage());

    throw $e;
}

?>
