<?php
require_once 'config.php';
require_once 'includes/auth.php';

$count = $conn->query(
    "SELECT COUNT(*) AS total FROM users"
)->fetch_assoc()['total'];

if ($count > 0) {
    die("Admin account already exists. Delete install.php.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $email = filter_var(
        trim($_POST['email'] ?? ''),
        FILTER_VALIDATE_EMAIL
    );
    $password = $_POST['password'] ?? '';

    if (!$name || !$email || strlen($password) < 10) {
        $error = "Enter valid details and a password of 10+ characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users
            (name, email, password_hash, role, status)
            VALUES (?, ?, ?, 'admin', 'active')"
        );

        $stmt->bind_param("sss", $name, $email, $hash);
        $stmt->execute();

        header("Location: login.php");
        exit;
    }
}

$pageTitle = "Create Admin";
require 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2>Create Administrator</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="csrf_token"
                           value="<?= e(csrf_token()) ?>">

                    <label class="form-label">Full Name</label>
                    <input class="form-control mb-3"
                           name="name" required>

                    <label class="form-label">Email</label>
                    <input class="form-control mb-3"
                           type="email" name="email" required>

                    <label class="form-label">Password</label>
                    <input class="form-control mb-3"
                           type="password" name="password"
                           minlength="10" required>

                    <button class="btn btn-primary w-100">
                        Create Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>