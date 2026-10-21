<?php
require_once 'config.php';
require_once 'includes/auth.php';

require_role(['admin']);

$pageTitle = "User Management";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $email = filter_var(
            trim($_POST['email'] ?? ''),
            FILTER_VALIDATE_EMAIL
        );
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'staff';

        if (!$name || !$email ||
            strlen($password) < 10 ||
            !in_array($role, ['admin', 'staff'], true)) {
            $error = "Please check the details and password.";
        } else {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare(
                    "INSERT INTO users
                    (name, email, password_hash, role)
                    VALUES (?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "ssss", $name, $email, $hash, $role
                );

                $stmt->execute();

                audit_log($conn, 'user_created', $email);
                flash("User created successfully.");
                redirect_to('users.php');

            } catch (mysqli_sql_exception $ex) {
                $error = "Email already exists or user could not be created.";
            }
        }
    }

    if ($action === 'toggle') {
        $id = (int)($_POST['user_id'] ?? 0);

        if ($id === (int)current_user()['id']) {
            $error = "You cannot change your own status.";
        } elseif ($id > 0) {
            $stmt = $conn->prepare(
                "UPDATE users
                 SET status = IF(status='active','inactive','active')
                 WHERE id = ?"
            );

            $stmt->bind_param("i", $id);
            $stmt->execute();

            audit_log($conn, 'user_status_changed', "User ID ".$id);
            flash("User status updated.");
            redirect_to('users.php');
        }
    }
}

$users = $conn->query(
    "SELECT id, name, email, role, status, created_at
     FROM users ORDER BY id DESC"
);

require 'includes/header.php';
?>

<h1>User Management</h1>
<p class="text-secondary">Create users and manage their access.</p>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h4>Add New User</h4>

        <form method="post" class="row g-3">
            <input type="hidden" name="csrf_token"
                   value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="create">

            <div class="col-md-3">
                <label class="form-label">Name</label>
                <input class="form-control" name="name" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Email</label>
                <input class="form-control" type="email"
                       name="email" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Password</label>
                <input class="form-control" type="password"
                       name="password" minlength="10" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Role</label>
                <select class="form-select" name="role">
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary">Add</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$u['id'] ?></td>
                    <td><?= e($u['name']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= e($u['role']) ?></td>
                    <td><?= e($u['status']) ?></td>
                    <td>
                        <?php if ((int)$u['id'] !== (int)current_user()['id']): ?>
                            <form method="post">
                                <input type="hidden" name="csrf_token"
                                       value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action"
                                       value="toggle">
                                <input type="hidden" name="user_id"
                                       value="<?= (int)$u['id'] ?>">

                                <button class="btn btn-sm btn-warning">
                                    Toggle Status
                                </button>
                            </form>
                        <?php else: ?>
                            Current User
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>