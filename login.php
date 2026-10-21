<?php
require_once 'config.php';
require_once 'includes/auth.php';

if (current_user()) {
    redirect_to('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE email = ? LIMIT 1"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if ($user &&
        $user['status'] === 'active' &&
        password_verify($password, $user['password_hash'])) {

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        audit_log($conn, 'login_success', 'User logged in');
        redirect_to('index.php');

    } else {
        $error = "Invalid email or password.";
    }
}

$pageTitle = "Login";
require 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2>Welcome Back</h2>
                <p class="text-secondary">Sign in to your account.</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="csrf_token"
                           value="<?= e(csrf_token()) ?>">

                    <label class="form-label">Email</label>
                    <input type="email" name="email"
                           class="form-control mb-3" required>

                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                           class="form-control mb-3" required>

                    <button class="btn btn-primary w-100">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>