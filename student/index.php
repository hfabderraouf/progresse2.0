<?php
require_once '../database.php';
require_once '../Auth.php';

$auth = new Auth($conn);

$auth->exigerRole('student');

$student = $auth->user();

if (!$student) {
    header('Location: /admin/students.php?notfound');
    die();
}

[
    'id' => $id,
    'matricule' => $matricule,
    'niveau' => $niveau,
    'nom' => $nom,
    'prenom' => $prenom,
    'dob' => $dob,
    'email' => $email,
    'username' => $username,
] = $student;

$r = $conn->execute_query("SELECT notes.*, modules.intitule, modules.coefficient FROM notes JOIN modules ON notes.module_id = modules.id WHERE notes.student_id = ? ", [$id]);
$modules = $r->fetch_all(MYSQLI_ASSOC);

$moyenne = 0;
$total_coefficients = 0;
$note_elim = false;

foreach ($modules as $module) {
    if ($module['note'] < 7) $note_elim = true;

    $moyenne += $module['note'] * $module['coefficient'];

    $total_coefficients += $module['coefficient'];
}

$moyenne = round($moyenne / $total_coefficients, 2);

$decision = ($moyenne >= 10 && !$note_elim) ? "Admis(e)" : "Ajourné(e)";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../static/usthb.png">
    <title>Espace Etudiant</title>
    <link rel="stylesheet" href="../static/main.css">
    <link rel="stylesheet" href="../static/admin.css">
    <link rel="stylesheet" href="../static/releve.css">
    <script src="../static/html2pdf.bundle.min.js"></script>
</head>

<body class="container">
    <div class="sidebar">
        <?php require '_nav.php' ?>
    </div>

    <div class="main">
        <div class="navbar">
            <h3 style="margin: 0 auto;">
                Votre Cursus
            </h3>
            <a class="btn" onclick="window.print()">🖶 Imprimer</a>
            <a class="btn" style="margin-left: .5rem;" onclick="telecharger()">💾 Telecharger</a>
        </div>
        <div class="content">
            <div class="releve" id="releve">
                <div style="display: flex; align-items: center;">
                    <img class="logo" src="../static/usthb.png" width="54">
                    <b style="margin: 0 1rem;">Université des Sciences et de la Technologie Houari Boumediene</b>
                </div>

                <div style="text-align: center;">
                    <h2 style="border-bottom: 4px double black;display: inline-block;">Relevé de Notes</h2>
                </div>
                <table class="table-info">
                    <tbody>
                        <tr>
                            <td>Matricule: </td>
                            <td><b><?= $student['matricule'] ?></b></td>
                            <td>Niveau: </td>
                            <td><b><?= $student['niveau'] ?></b></td>
                        </tr>
                        <tr>
                            <td>Nom: </td>
                            <td><b><?= $student['nom'] ?></b></td>
                        </tr>
                        <tr>
                            <td>Prenom: </td>
                            <td><b><?= $student['prenom'] ?></b></td>
                        </tr>
                        <tr>
                            <td>Né le: </td>
                            <td><b><?= $student['dob'] ?></b></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table">
                    <thead>
                        <th>Module</th>
                        <th style="width: 1%;">Coefficient</th>
                        <th style="width: 1%;">Note</th>
                    </thead>
                    <tbody>
                        <?php foreach ($modules as $module): ?>
                            <tr>
                                <td><?= $module["intitule"] ?></td>
                                <td><?= $module["coefficient"] ?></td>
                                <td><?= $module["note"] > 7 ? $module["note"] : '<b><u>' . $module["note"] . '</u></b>' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2"><i>Moyenne générale</i></td>
                            <td><b><?= $moyenne ?? 0 ?></b> / 20</td>
                        </tr>
                        <tr>
                            <td colspan="2"><i>Décésion</i></td>
                            <td><b><?= $decision ?? '' ?></b></td>
                        </tr>
                    </tbody>
                </table>

                <p style="text-align: end;">Alger, Le: <b><?= date('Y-m-d') ?></b></p>
            </div>
        </div>
    </div>
    <script>
        const element = document.getElementById('releve');
        const opt = {
            filename: '<?= "releve_{$student['matricule']}" ?>.pdf',
            html2canvas: {
                scale: 2
            },
            jsPDF: {
                unit: 'mm',
                format: 'A4',
                orientation: 'portrait'
            }
        };

        function telecharger() {
            html2pdf().set(opt).from(element).toContainer().save();
        }
    </script>

</body>

</html>