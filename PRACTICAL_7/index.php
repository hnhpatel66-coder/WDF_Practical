<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Registration</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>Student Registration Form</h1>

    <p class="subtitle">
        Enter your details below
    </p>

    <?php

    if (isset($_GET["success"])) {
        echo '<div class="success">
                Registration completed successfully.
              </div>';
    }

    if (isset($_GET["error"])) {
        echo '<div class="error">
                ' . htmlspecialchars($_GET["error"]) . '
              </div>';
    }

    ?>

    <form action="process.php" method="POST">

        <label>Full Name</label>

        <input
            type="text"
            name="name"
            placeholder="Enter your full name"
            required
        >


        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >


        <label>Phone Number</label>

        <input
            type="text"
            name="phone"
            placeholder="Enter 10 digit phone number"
            required
        >


        <label>Course</label>

        <select name="course" required>

            <option value="">
                Select Course
            </option>

            <option value="B.Tech IT">
                B.Tech IT
            </option>

            <option value="B.Tech CSE">
                B.Tech CSE
            </option>

            <option value="BCA">
                BCA
            </option>

            <option value="MCA">
                MCA
            </option>

        </select>


        <label>Gender</label>

        <div class="radio-group">

            <label>
                <input
                    type="radio"
                    name="gender"
                    value="Male"
                    required
                >
                Male
            </label>

            <label>
                <input
                    type="radio"
                    name="gender"
                    value="Female"
                >
                Female
            </label>

            <label>
                <input
                    type="radio"
                    name="gender"
                    value="Other"
                >
                Other
            </label>

        </div>


        <label>Message</label>

        <textarea
            name="message"
            placeholder="Enter your message"
            rows="4"
        ></textarea>


        <button type="submit">
            Register
        </button>

    </form>


    <a href="records.php" class="view-records">
        View Stored Records
    </a>

</div>

</body>

</html>