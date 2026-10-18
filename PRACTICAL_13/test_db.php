
<?php

try {

    require "db.php";

    echo "Database connected successfully!";

    $result = $conn->query("SELECT DATABASE() AS db_name");

    $row = $result->fetch_assoc();

    echo "<p>Database: " .
         htmlspecialchars($row["db_name"]) .
         "</p>";

} catch (Throwable $e) {

    echo "Connection failed. Check database name, MySQL service and credentials.";
}

?>
