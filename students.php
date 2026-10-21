<?php
require_once 'config.php';
require_once 'includes/auth.php';

require_role(['admin', 'staff']);

$pageTitle = "Students";

$q = trim($_GET['q'] ?? '');
$search = "%".$q."%";

$stmt = $conn->prepare(
    "SELECT * FROM students
     WHERE full_name LIKE ?
        OR email LIKE ?
        OR course LIKE ?
     ORDER BY id DESC"
);

$stmt->bind_param("sss", $search, $search, $search);
$stmt->execute();

$result = $stmt->get_result();

audit_log($conn, 'students_view', 'Student records viewed');

require 'includes/header.php';
?>

<h1>Student Records</h1>
<p class="text-secondary">Search student details.</p>

<form method="get" class="d-flex gap-2 mb-3">
    <input type="text" name="q"
           class="form-control"
           placeholder="Search name, email or course"
           value="<?= e($q) ?>">

    <button class="btn btn-primary">Search</button>

    <a href="students.php" class="btn btn-secondary">
        Reset
    </a>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Created</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result->num_rows === 0): ?>
                <tr>
                    <td colspan="5">No students found.</td>
                </tr>
            <?php endif; ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td><?= e($row['full_name']) ?></td>
                    <td><?= e($row['email']) ?></td>
                    <td><?= e($row['course']) ?></td>
                    <td><?= e($row['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>