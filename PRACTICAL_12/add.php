
<?php
require "db.php";

$message = "";
$messageType = "";

function clean($value) {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $event_name = trim($_POST["event_name"] ?? "");
    $event_date = trim($_POST["event_date"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = trim($_POST["status"] ?? "");

    $poster = null;

    // Validate required fields
    if (
        $event_name === "" ||
        $event_date === "" ||
        $location === "" ||
        $category === ""
    ) {
        $message = "Please fill all required fields.";

    } elseif (
        strlen($event_name) > 150 ||
        strlen($location) > 150
    ) {
        $message = "Event name or location is too long.";

    } elseif (!in_array(
        $category,
        ["Technology", "Competition", "Workshop", "Cultural", "Sports"],
        true
    )) {
        $message = "Invalid category selected.";

    } elseif (!in_array($status, ["Open", "Closed"], true)) {
        $message = "Invalid event status.";

    } else {

        $date = DateTime::createFromFormat("!Y-m-d", $event_date);

        if (!$date || $date->format("Y-m-d") !== $event_date) {
            $message = "Please enter a valid event date.";
        }
    }

    // Poster validation
    if (
        $message === "" &&
        isset($_FILES["poster"]) &&
        $_FILES["poster"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        $file = $_FILES["poster"];

        if ($file["error"] !== UPLOAD_ERR_OK) {
            $message = "Poster upload failed.";

        } elseif ($file["size"] > 2 * 1024 * 1024) {
            $message = "Poster must be smaller than 2 MB.";

        } elseif ($file["size"] <= 0) {
            $message = "Poster file is empty.";

        } elseif (!is_uploaded_file($file["tmp_name"])) {
            $message = "Invalid uploaded file.";

        } else {

            // Validate actual image type
            $imageInfo = @getimagesize($file["tmp_name"]);

            $allowedTypes = [
                IMAGETYPE_JPEG => "jpg",
                IMAGETYPE_PNG => "png",
                IMAGETYPE_WEBP => "webp"
            ];

            if (
                $imageInfo === false ||
                !isset($allowedTypes[$imageInfo[2]])
            ) {
                $message = "Only JPG, PNG and WEBP images are allowed.";

            } else {

                $extension = $allowedTypes[$imageInfo[2]];

                // Generate a unique filename
                $poster = bin2hex(random_bytes(16)) . "." . $extension;

                $uploadDir = __DIR__ . "/uploads/";

                if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
                    $message = "Upload folder is missing or not writable.";

                } elseif (!move_uploaded_file(
                    $file["tmp_name"],
                    $uploadDir . $poster
                )) {
                    $message = "Failed to save poster.";
                }
            }
        }
    }

    // Insert event into database
    if ($message === "") {

        $stmt = $conn->prepare(
            "INSERT INTO events
            (event_name, event_date, location,
             category, description, poster, status)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sssssss",
            $event_name,
            $event_date,
            $location,
            $category,
            $description,
            $poster,
            $status
        );

        try {

            if ($stmt->execute()) {

                header("Location: index.php?msg=added");
                exit;

            } else {
                $message = "Failed to add event.";
            }

        } catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            $message = "Database error. Could not add event.";
        }

        if ($message !== "" && $poster !== null) {
            $savedFile = __DIR__ . "/uploads/" . $poster;

            if (is_file($savedFile)) {
                unlink($savedFile);
            }
        }

        $stmt->close();
    }

    if ($message !== "") {
        $messageType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Event</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
    font-family: Arial, sans-serif;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.container {
    max-width: 800px;
    margin-top: 40px;
}

h2 {
    color: #24324a;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Add New Event</h2>
        <a href="index.php" class="btn btn-secondary">
            Back to Home
        </a>
    </div>

    <div class="card p-4">

        <?php if ($message !== ""): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= clean($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label">Event Name</label>

                <input type="text"
                       name="event_name"
                       class="form-control"
                       maxlength="150"
                       value="<?= clean($_POST['event_name'] ?? '') ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Event Date</label>

                <input type="date"
                       name="event_date"
                       class="form-control"
                       value="<?= clean($_POST['event_date'] ?? '') ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Location</label>

                <input type="text"
                       name="location"
                       class="form-control"
                       maxlength="150"
                       value="<?= clean($_POST['location'] ?? '') ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>

                <select name="category" class="form-select" required>

                    <option value="">Select Category</option>

                    <?php
                    $categories = [
                        "Technology",
                        "Competition",
                        "Workshop",
                        "Cultural",
                        "Sports"
                    ];

                    foreach ($categories as $cat):
                    ?>

                    <option value="<?= clean($cat) ?>"
                        <?= ($_POST["category"] ?? "") === $cat
                            ? "selected" : "" ?>>
                        <?= clean($cat) ?>
                    </option>

                    <?php endforeach; ?>

                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>

                <textarea name="description"
                          class="form-control"
                          rows="4"><?= clean($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Event Status</label>

                <select name="status" class="form-select" required>

                    <option value="Open"
                        <?= ($_POST["status"] ?? "Open") === "Open"
                            ? "selected" : "" ?>>
                        Open
                    </option>

                    <option value="Closed"
                        <?= ($_POST["status"] ?? "") === "Closed"
                            ? "selected" : "" ?>>
                        Closed
                    </option>

                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Event Poster (Optional)</label>

                <input type="hidden"
                       name="MAX_FILE_SIZE"
                       value="2097152">

                <input type="file"
                       name="poster"
                       class="form-control"
                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                <small class="text-muted">
                    Allowed: JPG, PNG, WEBP | Maximum size: 2 MB
                </small>
            </div>

            <button type="submit" class="btn btn-success">
                Save Event
            </button>

            <a href="index.php" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>

</div>

</body>
</html>
