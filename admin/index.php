<?php
require_once '../database.php';
require_once '../Auth.php';

(new Auth($conn))->exigerRole('admin');

$r1 = $conn->execute_query("SELECT COUNT(*) FROM teachers");
$r2 = $conn->execute_query("SELECT COUNT(*) FROM students");
$r3 = $conn->execute_query("SELECT COUNT(*) FROM modules");
$count_t = $r1->fetch_column(0);
$count_s = $r2->fetch_column(0);
$count_m = $r3->fetch_column(0);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../static/usthb.png">
    <title>Dashboard Administrateur</title>
    <link rel="stylesheet" href="../static/main.css">
    <link rel="stylesheet" href="../static/admin.css">
</head>

<body class="container">
    <div class="sidebar">
        <?php require '_nav.php' ?>
    </div>

    <div class="main">
        <div class="navbar">
            <h3 style="margin: 0 auto;">Statistiques</h3>
        </div>
        <div class="content">
            <p>Nombre total d'étudiants : <strong> <?= $count_s ?> </strong></p>
            <p>Nombre de modules : <strong> <?= $count_m ?> </strong></p>
            <p>Nombre d'enseignants : <strong> <?= $count_t ?> </strong></p>
        </div>
    </div>

</body>

</html>