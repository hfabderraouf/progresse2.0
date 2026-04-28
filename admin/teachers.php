<?php
require_once '../database.php';
require_once '../Auth.php';

(new Auth($conn))->exigerRole('admin');

$mode = isset($_GET["edit"]) ? 'edit' : 'list';
$error = isset($_GET["notfound"]) ? "Id erroné!" : '';
$success = isset($_GET["success"]) ? "Modifications sauvgardés!" : '';

if ($mode === "edit") {
    if (empty($_GET["edit"]) || $_GET["edit"] < 1) {
        header('Location: /admin/teachers.php');
        die();
    }

    $id = (int) $_GET["edit"];

    $r = $conn->execute_query("SELECT id, matricule, nom, prenom FROM teachers WHERE id = ?", [$id]);
    $teacher = $r->fetch_assoc();

    if (!$teacher) {
        header('Location: /admin/teachers.php?notfound');
        die();
    }

    if (isset($_POST["matricule"])) {
        $matricule = (string) $_POST["matricule"];
        if (empty($matricule) || $matricule < 1 || strlen($matricule) != 8) {
            $error = 'Le matricule est obligatoire et doit comporter 8 chiffres!';
        } else {
            $r = $conn->execute_query("UPDATE teachers SET matricule = ? WHERE id = ?", [$matricule, $id]);

            header('Location: /admin/teachers.php?success');
            die();
        }
    }
} else {
    $r = $conn->execute_query("SELECT id, matricule, nom, prenom, last_active FROM teachers");
    $teachers = $r->fetch_all(MYSQLI_ASSOC);
}

function renderEtat($teacher)
{
    if (round((time() - strtotime($teacher['last_active'] ?? 0)) / 60) >= 5) {
        return '<div class="circle-gris" title="' . $teacher['last_active'] . '"></div>';
    } else {
        return '<div class="circle-vert" title="' . $teacher['last_active'] . '"></div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../static/usthb.png">
    <title>Gestion des enseignants</title>
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
                <a class="nav-link" href="/admin/teachers.php">◀ Retour</a>
            <?php endif; ?>

            <h3 style="margin: 0 auto;">
                <?= ($mode === "list") ? 'Gestion des enseignants' : "Edition enseignant #{$teacher['id']}" ?>
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

            <?php if ($mode === "list"): ?>
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
                        foreach ($teachers as $teacher) {
                            $etat = renderEtat($teacher);
                            echo <<< HTML
                                <tr>
                                    <td>{$teacher["id"]}</td>
                                    <td>{$teacher["matricule"]}</td>
                                    <td>{$teacher["nom"]}</td>
                                    <td>{$teacher["prenom"]}</td>
                                    <td><a href="/admin/teachers.php?edit={$teacher['id']}">Editer</a></td>
                                    <td>{$etat}</td>
                                </tr>
                            HTML;
                        }
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <form action="" method="post" class="edit-form">
                    <div class="input-group">
                        <label for="inputNom">Nom</label>
                        <input id="inputNom" class="text-input" name="nom" type="text" placeholder="Nom" value="<?= $teacher['nom'] ?>" readonly>
                    </div>
                    <div class="input-group">
                        <label for="inputPrenom">Prénom</label>
                        <input id="inputPrenom" class="text-input" name="prenom" type="text" placeholder="Prenom" value="<?= $teacher['prenom'] ?>" readonly>
                    </div>
                    <div class="input-group">
                        <label for="inputMatricule">Matricule (Format: 12345678)</label>
                        <input id="inputMatricule" class="text-input" name="matricule" type="text" inputmode="numeric" pattern="^(?!0{8})\d{8}$" minlength="8" maxlength="8" value="<?= $teacher['matricule'] ?>" required autofocus>
                    </div>
                    <button class="btn" type="submit">Sauvgarder</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>