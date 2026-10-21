<?php
require_once 'config.php';
require_once 'includes/auth.php';

require_role(['admin', 'staff']);

$pageTitle = "Registrations";

$result = $conn->query(
    "SELECT r.id, s.full_name, s.email,
            e.title, e.event_date, r.registered_at
     FROM registrations r
     JOIN students s ON s.id = r.student_id
     JOIN events e ON e.id = r.event_id
     ORDER BY r.id DESC"
);

audit_log($conn, 'registrations_view', 'Registrations viewed');

require 'includes/header.php';
?>

<h1>Event Registrations</h1>
<p class="text-secondary">List of registered students and events.</p>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Event</th>
                    <th>Event Date</th>
                    <th>Registered At</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result->num_rows === 0): ?>
                <tr>
                    <td colspan="6">No registrations found.</td>
                </tr>
            <?php endif; ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td><?= e($row['full_name']) ?></td>
                    <td><?= e($row['email']) ?></td>
                    <td><?= e($row['title']) ?></td>
                    <td><?= e($row['event_date']) ?></td>
                    <td><?= e($row['registered_at']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>