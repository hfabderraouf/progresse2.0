<?php
require_once '../database.php';
require_once '../Auth.php';

$auth = new Auth($conn);

$auth->exigerRole('teacher');

$user = $auth->user();

$mode = isset($_GET["edit"]) ? 'edit' : 'list';
$errors = isset($_GET["notfound"]) ? ["Id erroné!"] : [];
$success = isset($_GET["success"]) ? "Modifications sauvgardés!" : '';

if ($mode === "edit") {
    if (empty($_GET["edit"]) || $_GET["edit"] < 1) {
        header('Location: /teacher/index.php');
        die();
    }

    $id = (int) $_GET["edit"];

    $r = $conn->execute_query(
        "SELECT id, intitule, coefficient, teacher_id FROM modules WHERE id = ? AND teacher_id = ?",
        [
            $id,
            $user['id']
        ]
    );
    $module = $r->fetch_assoc();

    if (!$module) {
        header('Location: /teacher/index.php?notfound');
        die();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $intitule = $_POST["intitule"] ?? null;
        $coefficient = $_POST["coefficient"] ?? null;

        if (empty($intitule)) $errors[] = "L'Intitulé est Obligatoire";
        if (empty($coefficient)) $errors[] = "Le Coefficient est Obligatoire et doit etre ≥ 1";

        if (!count($errors)) {
            $r = $conn->execute_query("UPDATE modules SET intitule = ?, coefficient= ? WHERE id = ?", [
                $intitule,
                $coefficient,
                $id
            ]);

            header('Location: /teacher/index.php?success');
            die();
        }
    }

    $r = $conn->execute_query("SELECT id, matricule, nom, prenom FROM teachers ORDER BY matricule ASC");
    $teachers = $r->fetch_all(MYSQLI_ASSOC);
} else {
    $r = $conn->execute_query(
        "SELECT modules.*, teachers.matricule, teachers.nom, teachers.prenom FROM modules JOIN teachers ON modules.teacher_id = teachers.id WHERE modules.teacher_id = ? ORDER BY modules.id ASC",
        [
            $user['id']
        ]
    );
    $modules = $r->fetch_all(MYSQLI_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../static/usthb.png">
    <title>Espace Enseignant</title>
    <link rel="stylesheet" href="../static/main.css">
    <link rel="stylesheet" href="../static/admin.css">
</head>

<body class="container">
    <div class="sidebar">
        <?php require '_nav.php' ?>
    </div>

    <div class="main">
        <div class="navbar">
            <?php if ($mode === "edit"): ?>
                <a class="nav-link" href="/teacher/index.php">◀ Retour</a>
            <?php endif; ?>

            <h3 style="margin: 0 auto;">
                <?= ($mode === "list") ? 'Vous modules' : "Edition Module #{$module['id']}" ?>
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

            <?php if ($errors): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <div><?= $error ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($mode === "list"): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Intitulé</th>
                            <th>Coefficient</th>
                            <th>Responsable</th>
                            <th>Gestion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($modules as $module) {
                            echo <<< HTML
                                <tr>
                                    <td>{$module["id"]}</td>
                                    <td>{$module["intitule"]}</td>
                                    <td>{$module["coefficient"]}</td>
                                    <td>[{$module['matricule']}] {$module['nom']} {$module['prenom']}</td>
                                    <td><a href="/teacher/index.php?edit={$module['id']}">Editer</a></td>
                                </tr>
                            HTML;
                        }
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <form action="" method="post" class="edit-form">
                    <div class="input-group">
                        <label for="inputIntitule">Intitulé</label>
                        <input id="inputIntitule" class="text-input" name="intitule" type="text" placeholder="Intitulé" value="<?= $module['intitule'] ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="inputCoefficient">Coefficient</label>
                        <input id="inputCoefficient" class="text-input" name="coefficient" type="number" placeholder="Coefficient" min="1" value="<?= $module['coefficient'] ?>" required>
                    </div>
                    <button class="btn" type="submit">Sauvgarder</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>