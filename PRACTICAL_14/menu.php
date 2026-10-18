
<?php
// Include this file only after auth.php
// and requireAdmin() or requireStudent().

$role = $_SESSION["role"] ?? "";
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand"
           href="<?= $role === 'admin'
               ? 'dashboard.php'
               : 'student_dashboard.php' ?>">
            Practical 14
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainMenu">

            <ul class="navbar-nav me-auto">

                <?php if ($role === "admin"): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            Admin Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            Manage Users
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="audit_logs.php">
                            Audit Logs
                        </a>
                    </li>

                <?php elseif ($role === "student"): ?>

                    <li class="nav-item">
                        <a class="nav-link"
                           href="student_dashboard.php">
                            Student Dashboard
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

            <span class="navbar-text text-white me-3">
                <?= htmlspecialchars($_SESSION["username"] ?? "") ?>
                (<?= htmlspecialchars($role) ?>)
            </span>

            <a href="logout.php" class="btn btn-danger btn-sm">
                Logout
            </a>

        </div>
    </div>
</nav>
