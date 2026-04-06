<?php
require_once __DIR__ . '/config/config.php';

// Redirect user on first entry based on auth cookie
if (isset($_COOKIE['username']) && isset($_COOKIE['role'])) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

header('Location: ' . BASE_URL . '/login.php');
exit;
?>
