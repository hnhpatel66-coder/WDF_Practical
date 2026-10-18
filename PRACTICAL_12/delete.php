
<?php
require "admin_auth.php";
require "db.php";

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Invalid request method.");
}

// Get event ID
$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id || $id <= 0) {
    die("Invalid event ID.");
}

// Find the event and its poster
$stmt = $conn->prepare(
    "SELECT poster FROM events WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();

$stmt->close();

if (!$event) {
    die("Event not found.");
}

// Delete event from database
$stmt = $conn->prepare(
    "DELETE FROM events WHERE id = ?"
);

$stmt->bind_param("i", $id);

try {

    $stmt->execute();
    $deleted = $stmt->affected_rows;
    $stmt->close();

    if ($deleted === 1) {

        // Remove poster if one exists
        $poster = $event["poster"];

        if (
            !empty($poster) &&
            basename($poster) === $poster
        ) {

            $posterPath = __DIR__ . "/uploads/" . $poster;

            if (is_file($posterPath)) {
                if (!unlink($posterPath)) {
                    error_log("Could not delete event poster.");
                }
            }
        }

        header("Location: index.php?msg=deleted");
        exit;
    }

    header("Location: index.php");
    exit;

} catch (mysqli_sql_exception $e) {

    error_log($e->getMessage());
    http_response_code(500);
    die("Failed to delete event.");
}

?>
