<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf() {
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) ||
        !hash_equals(csrf_token(), $token)) {
        die("Invalid request.");
    }
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!current_user()) {
        header("Location: login.php");
        exit;
    }
}

function require_role($roles) {
    require_login();

    if (!in_array(current_user()['role'], $roles, true)) {
        http_response_code(403);
        die("Access denied.");
    }
}

function redirect_to($page) {
    header("Location: " . $page);
    exit;
}

function flash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

function take_flash() {
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $message;
}

function audit_log($conn, $action, $details = '') {
    $user = current_user();

    $userId = $user['id'] ?? null;
    $actor = $user['name'] ?? 'Guest';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    $stmt = $conn->prepare(
        "INSERT INTO audit_logs
        (user_id, actor_name, action, details, ip_address)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "issss",
        $userId,
        $actor,
        $action,
        $details,
        $ip
    );

    $stmt->execute();
    $stmt->close();
}
?>