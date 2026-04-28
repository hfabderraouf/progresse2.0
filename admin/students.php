<?php
require_once '../database.php';
require_once '../Auth.php';

(new Auth($conn))->exigerRole('admin');

$mode = (isset($_GET['q']) && !empty($_GET['q'])) ? 'search' : 'list';
$success = isset($_GET["deleted"]) ? "Etudiant Supprimé!" : '';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === "DELETE") {
    $id = $_POST["id"] ?? null;
    if ($id) {
        $r = $conn->execute_query("DELETE FROM students WHERE id= ?", [$id]);

        header('Location: /admin/students.php?deleted');
        die();
    }
}

if ($mode === 'search') {
    $q = trim($_GET['q']);

    $r = $conn->execute_query(
        "SELECT id, matricule, nom, prenom, last_active FROM students WHERE matricule LIKE ? OR nom LIKE ? OR prenom LIKE ?",
        ["%{$q}%", "%{$q}%", "%{$q}%"]
    );
} else {
    $r = $conn->execute_query("SELECT id, matricule, nom, prenom, last_active FROM students");
}

$students = $r->fetch_all(MYSQLI_ASSOC);

function renderEtat($student)
{
    if (round((time() - strtotime($student['last_active'] ?? 0)) / 60) >= 5) {
        return '<div class="circle-gris" title="' . $student['last_active'] . '"></div>';
    } else {
        return '<div class="circle-vert" title="' . $student['last_active'] . '"></div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../static/usthb.png">
    <title>Gestion des étudiants</title>
    <link rel="stylesheet" href="../static/main.css">
    <link rel="stylesheet" href="../static/admin.css">
</head>

<body class="container">
    <div class="sidebar">
        <?php require '_nav.php' ?>
    </div>

    <div class="main">
        <div class="navbar">
            <?php if ($mode === "search"): ?>
                <a class="nav-link" href="/admin/students.php">◀ Retour</a>
            <?php endif; ?>

            <h3 style="margin: 0 auto;">
                <?= ($mode === "list") ? 'Gestion des étudiants' : "Résultats de la Recherche" ?>
            </h3>
        </div>
        <div class="content">
            <?php if ($success): ?>
                <div class="success-message">
                    <?= $success ?>
                </div>
                <script>
                    const urlObj = new URL(window.location.href);
                    urlObj.searchParams.delete('success');
                    window.history.replaceState(null, null, urlObj.href);
                </script>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error-message">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="" method="GET" class="search-form">
                <input id="inputRecherche" class="text-input" type="text" name="q" placeholder="Rechercher par Matricule, Nom ou Prénom..." value="<?= $q ?? '' ?>" required>
                <div class="search-btn-container">
                    <button class="btn search-btn" type="submit">Rechercher</button>
                </div>
            </form>

            <?php if ($mode === "list"): ?>
                <div style="display: flex; justify-content:flex-end; margin-bottom: 1rem">
                    <a class="btn" href="/admin/student_form.php" role="button">Ajouter Etudiant</a>
                </div>
            <?php else: ?>
                <div style="display: flex; justify-content:center; margin-bottom: 1rem;">
                    <b><?= count($students) . " Etudiant(s) trouvé(s)" ?></b>
                </div>
            <?php endif; ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Gestion</th>
                        <th>Etat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($students as $student) {
                        $etat = renderEtat($student);
                        echo <<< HTML
                            <tr>
                                <td>{$student["id"]}</td>
                                <td>{$student["matricule"]}</td>
                                <td>{$student["nom"]}</td>
                                <td>{$student["prenom"]}</td>
                                <td>
                                    <a href="/admin/student_form.php?edit={$student['id']}">Editer</a>
                                    <a class="btn releve-btn" href="/admin/student_releve.php?id={$student['id']}">Relevé</a>
                                    <form action="/admin/students.php?edit={$student['id']}" method="POST" class="delete-form">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="{$student['id']}">
                                        <button type="submit" class="delete-btn">Supprimer</button>
                                    </form>
                                </td>
                                <td>{$etat}</td>
                            </tr>
                        HTML;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>