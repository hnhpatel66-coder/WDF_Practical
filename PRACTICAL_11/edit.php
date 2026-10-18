
<?php
require "db.php";

$message = "";

// Get student ID
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid student ID.");
}

// Fetch student details
$stmt = $conn->prepare(
    "SELECT * FROM students WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

$stmt->close();

if (!$student) {
    die("Student not found.");
}

// Update student
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $semester = filter_input(
        INPUT_POST, "semester", FILTER_VALIDATE_INT
    );
    $phone = trim($_POST["phone"] ?? "");

    // Keep entered values in the form
    $student["name"] = $name;
    $student["email"] = $email;
    $student["course"] = $course;
    $student["semester"] = $semester;
    $student["phone"] = $phone;

    if (
        $name == "" ||
        $email == "" ||
        $course == "" ||
        $semester === false ||
        $semester === null ||
        $phone == ""
    ) {
        $message = "Please enter valid details in all fields.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    }
    elseif (!in_array($course, ["IT", "CE", "CSE"], true)) {
        $message = "Please select a valid course.";
    }
    elseif ($semester < 1 || $semester > 8) {
        $message = "Semester must be between 1 and 8.";
    }
    elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "Phone must contain exactly 10 digits.";
    }
    else {

        $sql = "UPDATE students
                SET name = ?,
                    email = ?,
                    course = ?,
                    semester = ?,
                    phone = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssisi",
            $name,
            $email,
            $course,
            $semester,
            $phone,
            $id
        );

        try {
            $stmt->execute();
            $stmt->close();

            header("Location: index.php?message=updated");
            exit();
        }
        catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $message = "This email already exists.";
            } else {
                $message = "Unable to update student.";
            }

            $stmt->close();
        }
    }
}

function safe($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            width: 400px;
            max-width: 90%;
            margin: 60px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px #ddd;
        }

        h2 {
            text-align: center;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 15px;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Edit Student</h2>

    <p class="error"><?php echo safe($message); ?></p>

    <form method="POST">

        <label>Student Name</label>
        <input type="text" name="name"
               maxlength="100"
               value="<?php echo safe($student['name']); ?>"
               required>

        <label>Email</label>
        <input type="email" name="email"
               maxlength="150"
               value="<?php echo safe($student['email']); ?>"
               required>

        <label>Course</label>
        <select name="course" required>
            <?php foreach (["IT", "CE", "CSE"] as $c) { ?>
                <option value="<?php echo $c; ?>"
                    <?php
                    if ($student["course"] == $c) {
                        echo "selected";
                    }
                    ?>>
                    <?php echo $c; ?>
                </option>
            <?php } ?>
        </select>

        <label>Semester</label>
        <input type="number" name="semester"
               min="1" max="8"
               value="<?php echo safe($student['semester']); ?>"
               required>

        <label>Phone Number</label>
        <input type="text" name="phone"
               pattern="[0-9]{10}"
               maxlength="10"
               value="<?php echo safe($student['phone']); ?>"
               required>

        <button type="submit">Update Student</button>

    </form>

    <a href="index.php">Back to Dashboard</a>

</div>

</body>
</html>
