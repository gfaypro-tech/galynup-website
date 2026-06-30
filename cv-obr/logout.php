<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
logout();
header('Location: index.php');
exit;
