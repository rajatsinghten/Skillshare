<?php
session_start();
session_unset();
session_destroy();

// Redirect to login page or homepage
header("Location: pages/index.php");
exit();