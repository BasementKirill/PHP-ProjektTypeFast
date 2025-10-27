<?php
// ============================================================================
// LOGOUT HANDLER
// ============================================================================
// This file destroys the user session and redirects to homepage
session_start();
session_destroy();
header('Location: ../index.php');
exit;
?>
