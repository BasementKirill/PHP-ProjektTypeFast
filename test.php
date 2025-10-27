<?php
// ============================================================================
// TEST REDIRECT - Redirect to pages/test.php
// ============================================================================
// This file allows users to access test.php directly from root URL
// Usage: http://localhost/typefast/test.php

header('Location: pages/test.php');
exit;
?>