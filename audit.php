<?php
require_once 'config.php';
require_once 'includes/auth.php';

require_role(['admin']);

$pageTitle = "Audit Log";

$logs = $conn->query(
    "SELECT id, actor_name, action, details,
            ip_address, created_at
     FROM audit_logs
     ORDER BY id DESC LIMIT 250"
);

require 'includes/header.php';
?>

<h1>Audit Log</h1>
<p class="text-secondary">Recent activities performed in the system.</p>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                    <th>Date and Time</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($logs->num_rows === 0): ?>
                <tr>
                    <td colspan="6">No activities recorded.</td>
                </tr>
            <?php endif; ?>

            <?php while ($row = $logs->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td><?= e($row['actor_name']) ?></td>
                    <td><?= e($row['action']) ?></td>
                    <td><?= e($row['details']) ?></td>
                    <td><?= e($row['ip_address']) ?></td>
                    <td><?= e($row['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>