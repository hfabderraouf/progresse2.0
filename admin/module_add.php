<?php
require_once '../database.php';
require_once '../Auth.php';

(new Auth($conn))->exigerRole('admin');

$errors = [];
$success = isset($_GET["success"]) ? "Module crée!" : '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $intitule = $_POST["intitule"] ?? null;
    $coefficient = $_POST["coefficient"] ?? null;
    $teacher_id = $_POST["teacher_id"] ?? null;

    if (empty($intitule)) $errors["intitule"] = "L'Intitulé est Obligatoire";
    if (empty($coefficient)) $errors["coefficient"] = "Le Coefficient est Obligatoire et doit etre ≥ 1";
    if (!empty($teacher_id)) {
        $r = $conn->execute_query("SELECT EXISTS (SELECT * FROM teachers WHERE id = ?)", [$teacher_id]);
        $exists = $r->fetch_column(0);

        if (!$exists) $errors["teacher_id"] = "Le Responsable selectioné n'existe pas!";
    } else {
        $errors["teacher_id"] = "Le Responsable est Obligatoire";
    }

    if (!count($errors)) {
        $r = $conn->execute_query("INSERT INTO modules (intitule, coefficient, teacher_id) VALUES (?,?,?)", [
            $intitule,
            $coefficient,
            $teacher_id,
        ]);

        $intitule = $coefficient = $teacher_id = null;

        header('Location: /admin/module_add.php?success');
        die();
    }
}

$r = $conn->execute_query("SELECT id, matricule, nom, prenom FROM teachers ORDER BY matricule ASC");
$teachers = $r->fetch_all(MYSQLI_ASSOC);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../static/usthb.png">
    <title>Gestion des Modules</title>
    <link rel="stylesheet" href="../static/main.css">
    <link rel="stylesheet" href="../static/admin.css">
</head>

<body class="container">
    <div class="sidebar">
        <?php require '_nav.php' ?>
    </div>

    <div class="main">
        <div class="navbar">
            <a class="nav-link" href="/admin/modules.php">◀ Retour</a>
            <h3 style="margin: 0 auto;">Ajouter un Module</h3>
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
                    <?php foreach ($errors as $key => $error): ?>
                        <div><?= $error ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="edit-form">
                <div class="input-group">
                    <label for="inputIntitule">Intitulé</label>
                    <input id="inputIntitule" class="text-input <?= isset($errors["intitule"]) ? 'is-invalid' : '' ?>" name="intitule" type="text" placeholder="Intitulé" value="<?= $intitule ?? '' ?>">
                    <div class="invalid-input" style="display: block;">
                        <?= $errors["intitule"] ?? '' ?>
                    </div>
                </div>
                <div class="input-group">
                    <label for="inputCoefficient">Coefficient</label>
                    <input id="inputCoefficient" class="text-input <?= isset($errors["coefficient"]) ? 'is-invalid' : '' ?>" name="coefficient" type="number" placeholder="Coefficient" min="1" value="<?= $coefficient ?? 1 ?>">
                    <div class="invalid-input">
                        <?= $errors["coefficient"] ?? '' ?>
                    </div>
                </div>
                <div class="input-group">
                    <label for="inputTeacher">Enseignant responsable</label>
                    <select id="inputTeacher" class="text-input <?= isset($errors["teacher_id"]) ? 'is-invalid' : '' ?>" name="teacher_id">
                        <option value="" <?= isset($teacher_id) ? '' : 'selected' ?> disabled>Selectionnez le Responsable</option>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>" <?= (isset($teacher_id) && $teacher['id'] === $teacher_id) ? 'selected' : '' ?>>
                                <?= '[' . $teacher['matricule'] . '] ' . $teacher['nom'] . ' ' . $teacher['prenom'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-input">
                        <?= $errors["teacher_id"] ?? '' ?>
                    </div>
                </div>
                <button class="btn" type="submit">Créer</button>
            </form>

        </div>
    </div>

</body>

</html>