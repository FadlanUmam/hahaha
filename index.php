<?php
require_once 'config/config.php';

// Redirect to login if not logged in, otherwise redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();
?>
