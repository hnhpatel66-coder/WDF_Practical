
<?php
require "auth.php";
requireAdmin();

// Function to count records
function getCount($conn, $table) {

    $allowed = [
        "users",
        "students",
        "events",
        "registrations"
    ];

    if (!in_array($table, $allowed, true)) {
        return 0;
    }

    $result = $conn->query(
        "SELECT COUNT(*) AS total FROM $table"
    );

    return (int)$result->fetch_assoc()["total"];
}

// Dashboard statistics
$totalUsers = getCount($conn, "users");
$totalStudents = getCount($conn, "students");
$totalEvents = getCount($conn, "events");
$totalRegistrations = getCount($conn, "registrations");

// Latest event registrations
$latest = $conn->query("
    SELECT
        r.id,
        s.name,
        e.title,
        r.registered_at
    FROM registrations r
    JOIN students s ON r.student_id = s.id
    JOIN events e ON r.event_id = e.id
    ORDER BY r.registered_at DESC, r.id DESC
    LIMIT 5
");

// Monthly registrations
$monthlyData = $conn->query("
    SELECT
        DATE_FORMAT(registered_at, '%Y-%m') AS month,
        COUNT(*) AS total
    FROM registrations
    GROUP BY DATE_FORMAT(registered_at, '%Y-%m')
    ORDER BY month ASC
");

$months = [];
$registrationCounts = [];

while ($row = $monthlyData->fetch_assoc()) {
    $months[] = $row["month"];
    $registrationCounts[] = (int)$row["total"];
}

// Escape text safely
function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Practical 14 - Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        .page-title {
            font-weight: bold;
            color: #212529;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            transition: 0.3s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 34px;
            font-weight: bold;
        }

        .content-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .chart-container {
            position: relative;
            height: 320px;
            width: 100%;
        }

        .table th {
            white-space: nowrap;
        }
    </style>

</head>

<body>

<!-- Dynamic navigation -->
<?php include "menu.php"; ?>

<div class="container mt-4 mb-5">

    <!-- Dashboard heading -->
    <h2 class="page-title">
        Admin Dashboard
    </h2>

    <p class="text-muted">
        Welcome,
        <strong>
            <?= e($_SESSION["username"]) ?>
        </strong>!
        Here is your database overview.
    </p>

    <!-- Dashboard statistic cards -->
    <div class="row g-4 mt-2">

        <!-- Users -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body p-4">

                    <h6 class="text-muted">
                        Total Users
                    </h6>

                    <div class="stat-number text-primary">
                        <?= $totalUsers ?>
                    </div>

                    <small class="text-muted">
                        Registered user accounts
                    </small>

                </div>
            </div>
        </div>

        <!-- Students -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body p-4">

                    <h6 class="text-muted">
                        Total Students
                    </h6>

                    <div class="stat-number text-success">
                        <?= $totalStudents ?>
                    </div>

                    <small class="text-muted">
                        Student records
                    </small>

                </div>
            </div>
        </div>

        <!-- Events -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body p-4">

                    <h6 class="text-muted">
                        Total Events
                    </h6>

                    <div class="stat-number text-warning">
                        <?= $totalEvents ?>
                    </div>

                    <small class="text-muted">
                        Available events
                    </small>

                </div>
            </div>
        </div>

        <!-- Registrations -->
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body p-4">

                    <h6 class="text-muted">
                        Total Registrations
                    </h6>

                    <div class="stat-number text-info">
                        <?= $totalRegistrations ?>
                    </div>

                    <small class="text-muted">
                        Event registrations
                    </small>

                </div>
            </div>
        </div>

    </div>

    <!-- Latest registrations -->
    <div class="card content-card shadow-sm mt-5">

        <div class="card-header bg-white p-3">
            <h5 class="mb-0 fw-bold">
                Latest Registrations
            </h5>
        </div>

        <div class="card-body p-3">

            <div class="table-responsive">

                <table class="table table-striped table-hover">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Event Name</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($latest->num_rows > 0): ?>

                        <?php while ($row = $latest->fetch_assoc()): ?>

                            <tr>
                                <td>
                                    <?= (int)$row["id"] ?>
                                </td>

                                <td>
                                    <?= e($row["name"]) ?>
                                </td>

                                <td>
                                    <?= e($row["title"]) ?>
                                </td>

                                <td>
                                    <?= e($row["registered_at"]) ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="4"
                                class="text-center text-muted py-4">
                                No registrations found.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

    <!-- Monthly registration analytics -->
    <div class="card content-card shadow-sm mt-4">

        <div class="card-header bg-white p-3">
            <h5 class="mb-0 fw-bold">
                Monthly Event Registrations
            </h5>
        </div>

        <div class="card-body p-4">

            <?php if (count($months) > 0): ?>

                <div class="chart-container">
                    <canvas id="registrationChart"></canvas>
                </div>

            <?php else: ?>

                <div class="alert alert-info mb-0">
                    No registration data available for analytics.
                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>

<script>
const months = <?= json_encode(
    $months,
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
) ?>;

const registrationCounts = <?= json_encode(
    $registrationCounts
) ?>;

const chartElement = document.getElementById(
    "registrationChart"
);

if (chartElement && typeof Chart !== "undefined") {

    new Chart(chartElement, {

        type: "bar",

        data: {
            labels: months,

            datasets: [{
                label: "Event Registrations",
                data: registrationCounts,
                backgroundColor: "#0d6efd",
                borderColor: "#084298",
                borderWidth: 1,
                borderRadius: 6
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: true,
                    position: "top"
                },

                title: {
                    display: true,
                    text: "Monthly Registration Analytics"
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}
</script>

</body>
</html>
