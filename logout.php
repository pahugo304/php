<?php
require_once __DIR__ . '/includes/config.php';

$_SESSION = [];
session_destroy();

header('Location: /lol-portal/index.php');
exit;