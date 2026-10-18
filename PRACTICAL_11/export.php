
<?php
require "db.php";

// Download file name
$filename = "students_export.csv";

// Set CSV download headers
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");

// Open output stream
$output = fopen("php://output", "w");

// Write column headings
fputcsv($output, [
    "ID",
    "Name",
    "Email",
    "Course",
    "Semester",
    "Phone"
]);

// Fetch student records securely
$stmt = $conn->prepare(
    "SELECT id, name, email, course, semester, phone
     FROM students ORDER BY id ASC"
);

$stmt->execute();
$result = $stmt->get_result();

// Write records into CSV
while ($row = $result->fetch_assoc()) {

    // Prevent spreadsheet formula injection
    foreach (["name", "email", "course", "phone"] as $field) {
        if (preg_match('/^[\s]*[=+\-@\t\r]/u', $row[$field])) {
            $row[$field] = "'" . $row[$field];
        }
    }

    fputcsv($output, array_values($row));
}

$stmt->close();
fclose($output);
exit();
?>
