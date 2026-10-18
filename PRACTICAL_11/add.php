
<?php
require "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $semester = filter_input(
        INPUT_POST, "semester", FILTER_VALIDATE_INT
    );
    $phone = trim($_POST["phone"] ?? "");

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
    elseif ($semester < 1 || $semester > 8) {
        $message = "Semester must be between 1 and 8.";
    }
    elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "Phone number must contain 10 digits.";
    }
    else {

        $sql = "INSERT INTO students
                (name, email, course, semester, phone)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssis",
            $name,
            $email,
            $course,
            $semester,
            $phone
        );

        try {
            $stmt->execute();
            header("Location: index.php?message=added");
            exit();
        }
        catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $message = "This email already exists.";
            } else {
                $message = "Unable to add student.";
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            width: 400px;
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
            background: green;
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

    <h2>Add New Student</h2>

    <p class="error">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST">

        <label>Student Name</label>
        <input type="text" name="name"
               maxlength="100" required>

        <label>Email</label>
        <input type="email" name="email"
               maxlength="150" required>

        <label>Course</label>
        <select name="course" required>
            <option value="">Select Course</option>
            <option value="IT">IT</option>
            <option value="CE">CE</option>
            <option value="CSE">CSE</option>
        </select>

        <label>Semester</label>
        <input type="number" name="semester"
               min="1" max="8" required>

        <label>Phone Number</label>
        <input type="text" name="phone"
               pattern="[0-9]{10}"
               maxlength="10" required>

        <button type="submit">Add Student</button>

    </form>

    <a href="index.php">Back to Dashboard</a>

</div>

</body>
</html>
