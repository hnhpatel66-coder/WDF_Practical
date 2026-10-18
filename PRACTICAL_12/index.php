
<?php

// Protect this page - Admin login required
require "admin_auth.php";
require "db.php";

// Function to safely display values
function clean($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

// Admin username from session
$adminName = $_SESSION["admin_name"] ?? "Admin";

// Get search and filter values
$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");
$status = trim($_GET["status"] ?? "");

// Prepare database query
$sql = "SELECT * FROM events WHERE 1=1";

$params = [];
$types = "";

// Search by event name or location
if ($search !== "") {

    $sql .= " AND (event_name LIKE ? OR location LIKE ?)";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "ss";
}

// Filter by category
if ($category !== "") {

    $sql .= " AND category = ?";

    $params[] = $category;
    $types .= "s";
}

// Filter by status
if ($status !== "") {

    $sql .= " AND status = ?";

    $params[] = $status;
    $types .= "s";
}

// Latest events first
$sql .= " ORDER BY id DESC";

// Execute prepared statement
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

// Fetch available categories
$categories = $conn->query(
    "SELECT DISTINCT category
     FROM events
     ORDER BY category ASC"
);

// Success messages
$messages = [
    "added" => "Event added successfully!",
    "updated" => "Event updated successfully!",
    "deleted" => "Event deleted successfully!"
];

$msg = $_GET["msg"] ?? "";

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Event Management Dashboard</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body {
    background: #f4f6f9;
    font-family: Arial, sans-serif;
}

.navbar-custom {
    background: #24324a;
    padding: 18px 0;
}

.navbar-title {
    color: white;
    font-size: 22px;
    font-weight: bold;
    margin: 0;
}

.main-container {
    margin-top: 35px;
    margin-bottom: 50px;
}

.dashboard-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.07);
}

.welcome-text {
    color: #24324a;
    font-weight: bold;
}

.poster {
    width: 85px;
    height: 75px;
    object-fit: contain;
    border-radius: 6px;
}

.table th,
.table td {
    vertical-align: middle;
}

.action-buttons {
    display: flex;
    gap: 7px;
    flex-wrap: wrap;
}

.search-heading {
    font-weight: 600;
}

.stat-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
}

.stat-number {
    font-size: 25px;
    font-weight: bold;
    color: #24324a;
}

</style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar-custom">

    <div class="container d-flex
                justify-content-between
                align-items-center gap-3 flex-wrap">

        <h3 class="navbar-title">
            Event Management System
        </h3>

        <div class="d-flex align-items-center gap-3">

            <span class="text-white">
                Admin:
                <?= clean($adminName) ?>
            </span>

            <!-- Secure Logout -->
            <form action="logout.php" method="POST"
                  class="m-0">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= clean(csrf_token()) ?>">

                <button
                    type="submit"
                    class="btn btn-danger btn-sm">

                    Logout

                </button>

            </form>

        </div>

    </div>

</nav>


<div class="container main-container">

    <!-- PAGE HEADER -->

    <div class="d-flex
                justify-content-between
                align-items-center
                flex-wrap gap-3 mb-4">

        <div>

            <h2 class="welcome-text">
                Welcome,
                <?= clean($adminName) ?>!
            </h2>

            <p class="text-muted mb-0">
                Manage your events from this dashboard.
            </p>

        </div>

        <a href="add.php"
           class="btn btn-primary">

            + Add Event

        </a>

    </div>


    <!-- SUCCESS MESSAGE -->

    <?php if (isset($messages[$msg])): ?>

        <div class="alert alert-success
                    alert-dismissible fade show">

            <?= clean($messages[$msg]) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>

        </div>

    <?php endif; ?>


    <!-- DASHBOARD SUMMARY -->

    <?php

    $totalQuery = $conn->query(
        "SELECT
            COUNT(*) AS total,
            COALESCE(SUM(status = 'Open'), 0) AS open_events,
            COALESCE(SUM(status = 'Closed'), 0) AS closed_events
         FROM events"
    );

    $stats = $totalQuery->fetch_assoc();

    ?>

    <div class="row g-3 mb-4">

        <div class="col-md-4">

            <div class="card dashboard-card p-3">

                <div class="stat-box">

                    <h6>Total Events</h6>

                    <div class="stat-number">
                        <?= (int)$stats["total"] ?>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card dashboard-card p-3">

                <div class="stat-box">

                    <h6>Open Events</h6>

                    <div class="stat-number text-success">
                        <?= (int)$stats["open_events"] ?>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card dashboard-card p-3">

                <div class="stat-box">

                    <h6>Closed Events</h6>

                    <div class="stat-number text-danger">
                        <?= (int)$stats["closed_events"] ?>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- SEARCH AND FILTER -->

    <div class="card dashboard-card p-4 mb-4">

        <h5 class="search-heading mb-3">
            Search and Filter Events
        </h5>

        <form method="GET"
              action="index.php"
              class="row g-3">

            <!-- Search -->
            <div class="col-md-4">

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search event or location"
                    value="<?= clean($search) ?>">

            </div>

            <!-- Category Filter -->
            <div class="col-md-3">

                <select name="category"
                        class="form-select">

                    <option value="">
                        All Categories
                    </option>

                    <?php while (
                        $cat = $categories->fetch_assoc()
                    ): ?>

                        <option
                            value="<?= clean($cat['category']) ?>"
                            <?= $category === $cat["category"]
                                ? "selected" : "" ?>>

                            <?= clean($cat["category"]) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>

            <!-- Status Filter -->
            <div class="col-md-2">

                <select name="status"
                        class="form-select">

                    <option value="">
                        All Status
                    </option>

                    <option value="Open"
                        <?= $status === "Open"
                            ? "selected" : "" ?>>
                        Open
                    </option>

                    <option value="Closed"
                        <?= $status === "Closed"
                            ? "selected" : "" ?>>
                        Closed
                    </option>

                </select>

            </div>

            <!-- Buttons -->
            <div class="col-md-3">

                <button
                    type="submit"
                    class="btn btn-success">

                    Filter

                </button>

                <a href="index.php"
                   class="btn btn-secondary">

                    Reset

                </a>

            </div>

        </form>

    </div>


    <!-- EVENT TABLE -->

    <div class="card dashboard-card p-4">

        <div class="d-flex
                    justify-content-between
                    align-items-center mb-3">

            <h5 class="mb-0">
                All Events
            </h5>

            <span class="badge bg-primary">
                <?= (int)$result->num_rows ?> Results
            </span>

        </div>

        <div class="table-responsive">

            <table class="table
                          table-bordered
                          table-hover">

                <thead class="table-dark">

                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php while (
                        $row = $result->fetch_assoc()
                    ): ?>

                        <tr>

                            <!-- Event ID -->
                            <td>
                                <?= (int)$row["id"] ?>
                            </td>

                            <!-- Poster -->
                            <td>

                                <?php if (
                                    !empty($row["poster"])
                                ): ?>

                                    <img
                                        src="uploads/<?= rawurlencode(basename($row['poster'])) ?>"
                                        class="poster"
                                        alt="Event Poster">

                                <?php else: ?>

                                    <span class="text-muted">
                                        No Poster
                                    </span>

                                <?php endif; ?>

                            </td>

                            <!-- Name -->
                            <td>
                                <?= clean($row["event_name"]) ?>
                            </td>

                            <!-- Date -->
                            <td>
                                <?= clean($row["event_date"]) ?>
                            </td>

                            <!-- Location -->
                            <td>
                                <?= clean($row["location"]) ?>
                            </td>

                            <!-- Category -->
                            <td>
                                <?= clean($row["category"]) ?>
                            </td>

                            <!-- Status -->
                            <td>

                                <?php if (
                                    $row["status"] === "Open"
                                ): ?>

                                    <span class="badge bg-success">
                                        Open
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-danger">
                                        Closed
                                    </span>

                                <?php endif; ?>

                            </td>

                            <!-- Actions -->
                            <td>

                                <div class="action-buttons">

                                    <!-- Edit -->
                                    <a
                                        href="edit.php?id=<?= (int)$row['id'] ?>"
                                        class="btn btn-warning btn-sm">

                                        Edit

                                    </a>

                                    <!-- Delete -->
                                    <form
                                        action="delete.php"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this event?');">

                                        <!-- CSRF Protection -->
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= clean(csrf_token()) ?>">

                                        <!-- Event ID -->
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$row['id'] ?>">

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm">

                                            Delete

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="8"
                            class="text-center text-muted p-4">

                            No events found.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
