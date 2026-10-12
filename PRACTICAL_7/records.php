<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Stored Registrations</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="records-container">

    <h1>Registered Students</h1>

    <a href="index.php" class="back-button">
        Back to Registration
    </a>


    <table>

        <thead>

        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Course</th>
            <th>Gender</th>
            <th>Message</th>
            <th>Date</th>
        </tr>

        </thead>


        <tbody>

        <?php

        $fileName = "data/registrations.csv";


        if (file_exists($fileName)) {

            $file = fopen($fileName, "r");


            if ($file !== false) {

                $header = fgetcsv($file);


                while (
                    ($record = fgetcsv($file)) !== false
                ) {

                    echo "<tr>";

                    foreach ($record as $value) {

                        echo "<td>" .
                             htmlspecialchars($value) .
                             "</td>";

                    }

                    echo "</tr>";

                }


                fclose($file);

            }

        } else {

            echo '
            <tr>
                <td colspan="7">
                    No records found.
                </td>
            </tr>';

        }

        ?>

        </tbody>

    </table>

</div>

</body>

</html>