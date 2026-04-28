<?php
session_start();

date_default_timezone_set('Africa/Algiers');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli('localhost', 'root', '', 'progress');

$conn->query("SET time_zone = '+01:00'");