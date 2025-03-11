<?php
session_start();

$_SESSION = [];

session_unset();
session_destroy();

if (isset($_COOKIE['username'])) {
  setcookie('username', '', time() - 3600, '/');
}

header('Location: login.php');
exit;
