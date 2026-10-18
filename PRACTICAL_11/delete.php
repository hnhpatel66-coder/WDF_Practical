
<?php
require "db.php";

// Get student ID from URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid student ID.");
}

// Fetch selected student
$stmt = $conn->prepare(
    "SELECT id, name, email FROM students WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

$stmt->close();

if (!$student) {
    die("Student not found.");
}

// Start session for CSRF protection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION["delete_token"])) {
    $_SESSION["delete_token"] = bin2hex(random_bytes(32));
}

$message = "";

// Delete student after confirmation
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["delete_token"] ?? "";

    if (!hash_equals($_SESSION["delete_token"], $token)) {
        $message = "Invalid request. Please try again.";
    }
    elseif (($_POST["confirm"] ?? "") !== "yes") {
        $message = "Deletion not confirmed.";
    }
    else {

        $delete = $conn->prepare(
            "DELETE FROM students WHERE id = ?"
        );

        $delete->bind_param("i", $id);

        try {
            $delete->execute();

            if ($delete->affected_rows === 1) {
                unset($_SESSION["delete_token"]);

                $delete->close();

                header("Location: index.php?message=deleted");
                exit();
            }
            else {
                $message = "Student could not be deleted.";
            }

        }
        catch (mysqli_sql_exception $e) {
            $message = "Unable to delete student.";
        }

        $delete->close();
    }
}

// Safe output function
function safe($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Delete Student</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 400px;
            max-width: 90%;
            margin: 80px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 12px #ddd;
            text-align: center;
        }

        h2 {
            color: #dc3545;
        }

        p {
            color: #333;
            line-height: 1.6;
        }

        .student-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            overflow-wrap: anywhere;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            cursor: pointer;
            margin: 5px;
        }

        .delete {
            background: #dc3545;
        }

        .cancel {
            background: #6c757d;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Delete Student</h2>

    <p>
        Are you sure you want to permanently
        delete this student?
    </p>

    <div class="student-info">

        <p>
            <b>Student ID:</b>
            <?php echo (int)$student["id"]; ?>
        </p>

        <p>
            <b>Name:</b>
            <?php echo safe($student["name"]); ?>
        </p>

        <p>
            <b>Email:</b>
            <?php echo safe($student["email"]); ?>
        </p>

    </div>

    <?php if ($message !== "") { ?>
        <p class="error">
            <?php echo safe($message); ?>
        </p>
    <?php } ?>

    <form method="POST"
          action="delete.php?id=<?php echo (int)$id; ?>">

        <input
            type="hidden"
            name="confirm"
            value="yes"
        >

        <input
            type="hidden"
            name="delete_token"
            value="<?php echo safe($_SESSION['delete_token']); ?>"
        >

        <button type="submit" class="btn delete">
            Yes, Delete
        </button>

        <a href="index.php" class="btn cancel">
            Cancel
        </a>

    </form>

</div>

</body>
</html>
