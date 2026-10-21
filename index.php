<?php
require_once 'config.php';
require_once 'includes/auth.php';

require_role(['admin', 'staff']);

$pageTitle = "Dashboard";

$users = $conn->query(
    "SELECT COUNT(*) AS total FROM users"
)->fetch_assoc()['total'];

$students = $conn->query(
    "SELECT COUNT(*) AS total FROM students"
)->fetch_assoc()['total'];

$events = $conn->query(
    "SELECT COUNT(*) AS total FROM events"
)->fetch_assoc()['total'];

$registrations = $conn->query(
    "SELECT COUNT(*) AS total FROM registrations"
)->fetch_assoc()['total'];

$recent = $conn->query(
    "SELECT s.full_name, e.title, r.registered_at
     FROM registrations r
     JOIN students s ON s.id = r.student_id
     JOIN events e ON e.id = r.event_id
     ORDER BY r.id DESC LIMIT 5"
);

audit_log($conn, 'dashboard_opened', 'Dashboard viewed');

require 'includes/header.php';
?>

<h1 class="mb-2">Dashboard Overview</h1>
<p class="text-secondary mb-4">
    Welcome to the Campus Administration System.
</p>

<div class="row g-3 mb-4">
    <?php
    $cards = [
        'Total Users' => $users,
        'Total Students' => $students,
        'Total Events' => $events,
        'Registrations' => $registrations
    ];

    foreach ($cards as $label => $number):
    ?>
        <div class="col-md-3 col-6">
            <div class="card metric-card">
                <div class="card-body">
                    <p class="text-secondary mb-2">
                        <?= e($label) ?>
                    </p>
                    <h2><?= (int)$number ?></h2>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h4>Recent Registrations</h4>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Event</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($recent->num_rows === 0): ?>
                    <tr>
                        <td colspan="3">No registrations available.</td>
                    </tr>
                <?php endif; ?>

                <?php while ($row = $recent->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($row['full_name']) ?></td>
                        <td><?= e($row['title']) ?></td>
                        <td><?= e($row['registered_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>