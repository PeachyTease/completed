<?php
require_once '_auth_check.php';

if ($_SESSION['admin']['role'] !== 'owner') {
    header("Location: dashboard.php");
    exit;
}
