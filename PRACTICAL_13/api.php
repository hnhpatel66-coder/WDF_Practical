
<?php

// Practical 13 - PHP JSON API CRUD

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store");

// Return JSON response
function responseJSON($success, $message, $data = null, $status = 200) {

    http_response_code($status);

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);

    exit;
}

// Read JSON request body
function getInput() {

    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        responseJSON(false, "Invalid JSON data.", null, 400);
    }

    return $input;
}

// Validate student data
function validateStudent($input) {

    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $course = trim($input["course"] ?? "");

    if ($name === "" || $email === "" || $course === "") {
        responseJSON(false, "All fields are required.", null, 400);
    }

    if (
        strlen($name) > 100 ||
        strlen($email) > 150 ||
        strlen($course) > 100
    ) {
        responseJSON(false, "Input is too long.", null, 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        responseJSON(false, "Invalid email address.", null, 400);
    }

    return [$name, $email, $course];
}

// Validate student ID
function validateId($input) {

    $id = filter_var(
        $input["id"] ?? null,
        FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1]]
    );

    if ($id === false || $id === null) {
        responseJSON(false, "Invalid student ID.", null, 400);
    }

    return $id;
}

// Connect database
try {
    require "db.php";
} catch (Throwable $e) {
    error_log($e->getMessage());
    responseJSON(false, "Database connection failed.", null, 500);
}

$method = $_SERVER["REQUEST_METHOD"];

try {

    // 1. GET - Display all students
    if ($method === "GET") {

        $sql = "SELECT id, name, email, course
                FROM students ORDER BY id DESC";

        $result = $conn->query($sql);

        $students = $result->fetch_all(MYSQLI_ASSOC);

        responseJSON(true, "Students loaded.", $students);
    }

    // 2. POST - Add student
    elseif ($method === "POST") {

        $input = getInput();

        [$name, $email, $course] = validateStudent($input);

        $sql = "INSERT INTO students (name, email, course)
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("sss", $name, $email, $course);

        $stmt->execute();

        responseJSON(
            true,
            "Student added successfully.",
            ["id" => $conn->insert_id],
            201
        );
    }

    // 3. PUT - Update student
    elseif ($method === "PUT") {

        $input = getInput();

        $id = validateId($input);

        [$name, $email, $course] = validateStudent($input);

        $sql = "UPDATE students
                SET name = ?, email = ?, course = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssi",
            $name,
            $email,
            $course,
            $id
        );

        $stmt->execute();

        responseJSON(true, "Student updated successfully.");
    }

    // 4. DELETE - Remove student
    elseif ($method === "DELETE") {

        $input = getInput();

        $id = validateId($input);

        $sql = "DELETE FROM students WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            responseJSON(false, "Student not found.", null, 404);
        }

        responseJSON(true, "Student deleted successfully.");
    }

    // Invalid request method
    else {

        header("Allow: GET, POST, PUT, DELETE");

        responseJSON(false, "Method not allowed.", null, 405);
    }

} catch (mysqli_sql_exception $e) {

    if ($e->getCode() == 1062) {
        responseJSON(false, "Email already exists.", null, 409);
    }

    error_log($e->getMessage());

    responseJSON(false, "Database operation failed.", null, 500);
}

$conn->close();

?>
