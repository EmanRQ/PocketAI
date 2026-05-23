<?php
require_once 'db.php';
session_unset();
session_destroy();
header("Location: login-form.html");
exit;
?>