<?php
require_once 'config.php';
require_once 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Use the Logout button.");
}

verify_csrf();

if (current_user()) {
    audit_log($conn, 'logout', 'User logged out');
}

$_SESSION = [];
session_destroy();

header("Location: login.php");
exit;
?>