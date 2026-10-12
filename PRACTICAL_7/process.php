<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");

    exit();
}


$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$course = trim($_POST["course"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$message = trim($_POST["message"] ?? "");


$name = htmlspecialchars($name);
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars($phone);
$course = htmlspecialchars($course);
$gender = htmlspecialchars($gender);
$message = htmlspecialchars($message);


if ($name === "") {

    header(
        "Location: index.php?error=" .
        urlencode("Name is required.")
    );

    exit();
}


if (!preg_match("/^[a-zA-Z ]+$/", $name)) {

    header(
        "Location: index.php?error=" .
        urlencode("Name should contain only letters.")
    );

    exit();
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    header(
        "Location: index.php?error=" .
        urlencode("Please enter a valid email.")
    );

    exit();
}


if (!preg_match("/^[0-9]{10}$/", $phone)) {

    header(
        "Location: index.php?error=" .
        urlencode("Phone number must contain exactly 10 digits.")
    );

    exit();
}


$allowedCourses = [
    "B.Tech IT",
    "B.Tech CSE",
    "BCA",
    "MCA"
];


if (!in_array($course, $allowedCourses)) {

    header(
        "Location: index.php?error=" .
        urlencode("Please select a valid course.")
    );

    exit();
}


$allowedGenders = [
    "Male",
    "Female",
    "Other"
];


if (!in_array($gender, $allowedGenders)) {

    header(
        "Location: index.php?error=" .
        urlencode("Please select gender.")
    );

    exit();
}


$dataFolder = "data";

if (!is_dir($dataFolder)) {

    mkdir($dataFolder, 0755, true);
}


$fileName = $dataFolder . "/registrations.csv";


$isNewFile =
    !file_exists($fileName) ||
    filesize($fileName) === 0;


$file = fopen($fileName, "a");


if ($file === false) {

    header(
        "Location: index.php?error=" .
        urlencode("Unable to open storage file.")
    );

    exit();
}


if (flock($file, LOCK_EX)) {

    if ($isNewFile) {

        fputcsv(
            $file,
            [
                "Name",
                "Email",
                "Phone",
                "Course",
                "Gender",
                "Message",
                "Date"
            ]
        );

    }


    fputcsv(
        $file,
        [
            $name,
            $email,
            $phone,
            $course,
            $gender,
            $message,
            date("Y-m-d H:i:s")
        ]
    );


    fflush($file);

    flock($file, LOCK_UN);

    fclose($file);


    header("Location: index.php?success=1");

    exit();

}


fclose($file);


header(
    "Location: index.php?error=" .
    urlencode("Unable to save data.")
);

exit();

?>