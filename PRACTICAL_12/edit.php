
<?php
require "admin_auth.php";
require "db.php";

function clean($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("Invalid event ID.");
}

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    die("Event not found.");
}

$message = "";

$categories = [
    "Technology",
    "Competition",
    "Workshop",
    "Cultural",
    "Sports"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $event["event_name"] = trim($_POST["event_name"] ?? "");
    $event["event_date"] = trim($_POST["event_date"] ?? "");
    $event["location"] = trim($_POST["location"] ?? "");
    $event["category"] = trim($_POST["category"] ?? "");
    $event["description"] = trim($_POST["description"] ?? "");
    $event["status"] = trim($_POST["status"] ?? "");

    $oldPoster = $event["poster"];
    $newPoster = $oldPoster;
    $uploadedPoster = null;

    if (
        $event["event_name"] === "" ||
        $event["event_date"] === "" ||
        $event["location"] === "" ||
        $event["category"] === ""
    ) {
        $message = "Please fill all required fields.";

    } elseif (
        strlen($event["event_name"]) > 150 ||
        strlen($event["location"]) > 150
    ) {
        $message = "Event name or location is too long.";

    } elseif (!in_array($event["category"], $categories, true)) {
        $message = "Invalid category.";

    } elseif (!in_array($event["status"], ["Open", "Closed"], true)) {
        $message = "Invalid status.";

    } else {

        $date = DateTime::createFromFormat(
            "!Y-m-d",
            $event["event_date"]
        );

        if (!$date || $date->format("Y-m-d") !== $event["event_date"]) {
            $message = "Invalid event date.";
        }
    }

    // Validate and upload new poster if selected
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

                $uploadedPoster = bin2hex(random_bytes(16))
                                  . "." . $extension;

                $uploadDir = __DIR__ . "/uploads/";

                if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
                    $message = "Upload folder is missing or not writable.";

                } elseif (!move_uploaded_file(
                    $file["tmp_name"],
                    $uploadDir . $uploadedPoster
                )) {
                    $message = "Could not save the poster.";

                } else {
                    $newPoster = $uploadedPoster;
                }
            }
        }
    }

    // Update database
    if ($message === "") {

        $stmt = $conn->prepare(
            "UPDATE events
             SET event_name = ?,
                 event_date = ?,
                 location = ?,
                 category = ?,
                 description = ?,
                 status = ?,
                 poster = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "sssssssi",
            $event["event_name"],
            $event["event_date"],
            $event["location"],
            $event["category"],
            $event["description"],
            $event["status"],
            $newPoster,
            $id
        );

        try {

            $stmt->execute();
            $stmt->close();

            // Remove previous poster after successful update
            if (
                $uploadedPoster !== null &&
                !empty($oldPoster) &&
                basename($oldPoster) === $oldPoster
            ) {
                $oldPath = __DIR__ . "/uploads/" . $oldPoster;

                if (is_file($oldPath)) {
                    unlink($oldPath);
                }
            }

            header("Location: index.php?msg=updated");
            exit;

        } catch (mysqli_sql_exception $e) {

            error_log($e->getMessage());
            $message = "Failed to update event.";

            if ($uploadedPoster !== null) {
                $newPath = __DIR__ . "/uploads/" . $uploadedPoster;

                if (is_file($newPath)) {
                    unlink($newPath);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Event</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
    font-family: Arial, sans-serif;
}

.container {
    max-width: 800px;
    margin-top: 40px;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.poster {
    width: 140px;
    height: 110px;
    object-fit: contain;
}
</style>
</head>

<body>

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Event</h2>
        <a href="index.php" class="btn btn-secondary">
            Back to Home
        </a>
    </div>

    <div class="card p-4">

        <?php if ($message !== ""): ?>
            <div class="alert alert-danger">
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
                       value="<?= clean($event['event_name']) ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Event Date</label>

                <input type="date"
                       name="event_date"
                       class="form-control"
                       value="<?= clean($event['event_date']) ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Location</label>

                <input type="text"
                       name="location"
                       class="form-control"
                       maxlength="150"
                       value="<?= clean($event['location']) ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>

                <select name="category" class="form-select" required>

                    <?php foreach ($categories as $cat): ?>

                        <option value="<?= clean($cat) ?>"
                            <?= $event["category"] === $cat
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
                          rows="4"><?= clean($event['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>

                <select name="status" class="form-select">

                    <option value="Open"
                        <?= $event["status"] === "Open"
                            ? "selected" : "" ?>>
                        Open
                    </option>

                    <option value="Closed"
                        <?= $event["status"] === "Closed"
                            ? "selected" : "" ?>>
                        Closed
                    </option>

                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Current Poster</label>

                <?php if (!empty($event["poster"])): ?>

                    <div>
                        <img class="poster"
                             src="uploads/<?= rawurlencode(basename($event['poster'])) ?>"
                             alt="Current event poster">
                    </div>

                <?php else: ?>

                    <p class="text-muted">No poster uploaded.</p>

                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Replace Poster (Optional)</label>

                <input type="file"
                       name="poster"
                       class="form-control"
                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                <small class="text-muted">
                    JPG, PNG, WEBP only | Maximum 2 MB
                </small>
            </div>

            <button type="submit" class="btn btn-success">
                Update Event
            </button>

            <a href="index.php" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>
</div>

</body>
</html>
