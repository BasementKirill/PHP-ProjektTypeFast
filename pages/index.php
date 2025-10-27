<?php
// ============================================================================
// PAGES DIRECTORY INDEX - Redirect to main application
// ============================================================================
// This file prevents directory listing and redirects users to the main page
// When someone accesses /pages/ directly, they get redirected to the homepage

header('Location: ../index.php');
exit;
?>
