<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;
}

function login($password) {
    $config = include 'config.php';
    if ($password === $config['admin_password']) {
        $_SESSION['admin_auth'] = true;
        return true;
    }
    return false;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
