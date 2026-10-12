<?php

require "db.php";

$student_id = 2;
$event_id = 3;
$registration_date = date("Y-m-d");

$sql = "INSERT INTO registrations
        (student_id, event_id, registration_date)
        VALUES (:student_id, :event_id, :registration_date)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':student_id' => $student_id,
    ':event_id' => $event_id,
    ':registration_date' => $registration_date
]);

echo "Student registered successfully!";

?>