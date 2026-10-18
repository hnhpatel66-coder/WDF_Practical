
<?php
require "db.php";

// Read search and filter values
$search = trim($_GET["search"] ?? "");
$course = $_GET["course"] ?? "";

// Allowed courses
$allowedCourses = ["IT", "CE", "CSE"];

if (!in_array($course, $allowedCourses, true)) {
    $course = "";
}

// Create SQL query
$sql = "SELECT * FROM students WHERE 1=1";

// Add search condition
if ($search != "") {
    $sql .= " AND (
        name LIKE ?
        OR email LIKE ?
        OR course LIKE ?
    )";
}

// Add course filter
if ($course != "") {
    $sql .= " AND course = ?";
}

$sql .= " ORDER BY id ASC";

$stmt = $conn->prepare($sql);

// Bind values securely
$searchValue = "%" . $search . "%";

if ($search != "" && $course != "") {
    $stmt->bind_param(
        "ssss",
        $searchValue,
        $searchValue,
        $searchValue,
        $course
    );
}
elseif ($search != "") {
    $stmt->bind_param(
        "sss",
        $searchValue,
        $searchValue,
        $searchValue
    );
}
elseif ($course != "") {
    $stmt->bind_param("s", $course);
}

$stmt->execute();
$result = $stmt->get_result();

// Display success messages
$message = "";

if (isset($_GET["message"])) {
    if ($_GET["message"] == "added") {
        $message = "Student added successfully!";
    }
    elseif ($_GET["message"] == "updated") {
        $message = "Student updated successfully!";
    }
    elseif ($_GET["message"] == "deleted") {
        $message = "Student deleted successfully!";
    }
}

// Escape output
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
    <title>Student Management System</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .search-box {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        input, select {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 11px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn {
            padding: 9px 12px;
            text-decoration: none;
            border-radius: 5px;
            color: white;
            display: inline-block;
            margin: 3px;
        }

        .add {
            background: green;
            margin-bottom: 20px;
        }

        .edit {
            background: #e69500;
        }

        .delete {
            background: red;
        }

        .reset {
            background: #6c757d;
        }

        .message {
            background: #e6ffe6;
            color: green;
            text-align: center;
            padding: 12px;
            margin-bottom: 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }
    </style>
</head>

<body>

<div class="container">

<h2>Student Management System</h2>

<?php if ($message != "") { ?>
    <div class="message">
        <?php echo safe($message); ?>
    </div>
<?php } ?>

<!-- Search and Filter -->
<div class="search-box">

    <form method="GET" action="index.php">

        <input
            type="text"
            name="search"
            placeholder="Search name, email or course"
            value="<?php echo safe($search); ?>"
        >

        <select name="course">

            <option value="">All Courses</option>

            <?php foreach ($allowedCourses as $c) { ?>

                <option
                    value="<?php echo safe($c); ?>"
                    <?php
                    if ($course == $c) {
                        echo "selected";
                    }
                    ?>
                >
                    <?php echo safe($c); ?>
                </option>

            <?php } ?>

        </select>

        <button type="submit">Search</button>

        <a href="index.php" class="btn reset">
            Reset
        </a>

    </form>

</div>

<a href="add.php" class="btn add">
    + Add Student
</a>

<a href="export.php" class="btn"
   style="background:#17a2b8;">
    Export Students to CSV
</a>


<p>
    <b>Students Found:</b>
    <?php echo $result->num_rows; ?>
</p>

<div class="table-container">

<table>

<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Course</th>
    <th>Semester</th>
    <th>Phone</th>
    <th>Actions</th>
</tr>

<?php if ($result->num_rows > 0) { ?>

    <?php while ($row = $result->fetch_assoc()) { ?>

        <tr>
            <td><?php echo (int)$row["id"]; ?></td>
            <td><?php echo safe($row["name"]); ?></td>
            <td><?php echo safe($row["email"]); ?></td>
            <td><?php echo safe($row["course"]); ?></td>
            <td><?php echo (int)$row["semester"]; ?></td>
            <td><?php echo safe($row["phone"]); ?></td>

            <td>
                <a
                    href="edit.php?id=<?php echo (int)$row['id']; ?>"
                    class="btn edit"
                >
                    Edit
                </a>

                <a
                    href="delete.php?id=<?php echo (int)$row['id']; ?>"
                    class="btn delete"
                >
                    Delete
                </a>
            </td>
        </tr>

    <?php } ?>

<?php } else { ?>

    <tr>
        <td colspan="7">
            No student records found.
        </td>
    </tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>

<?php
$stmt->close();
?>
