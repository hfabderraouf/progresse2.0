<?php
require_once '../database.php';
require_once '../Auth.php';

(new Auth($conn))->exigerRole('admin');

$mode = isset($_GET["edit"]) ? 'edit' : 'create';

$errors = [];
$success = '';

if (isset($_GET["success"])) {
    $success = ($_GET["success"] === "created") ? "Etudiant(e) crée!" : 'Etudiant(e) modifié(e)!';
}

if ($mode === "edit") {

    if (empty($_GET["edit"]) || $_GET["edit"] < 1) {
        header('Location: /admin/students.php');
        die();
    }

    $id = (int) $_GET["edit"];

    $student = verifierExiste($id);

    [
        'matricule' => $matricule,
        'niveau' => $niveau,
        'nom' => $nom,
        'prenom' => $prenom,
        'dob' => $dob,
        'email' => $email,
        'username' => $username,
    ] = $student;
}

$niveaux = [
    "L1" => "Licence 1",
    "L2" => "Licence 2",
    "L3" => "Licence 3",
    "M1" => "Master 1",
    "M2" => "Master 2",
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricule = $_POST["matricule"] ?? null;
    $niveau = $_POST["niveau"] ?? null;
    $nom = $_POST["nom"] ?? null;
    $prenom = $_POST["prenom"] ?? null;
    $dob = $_POST["dob"] ?? null;
    $email = $_POST["email"] ?? null;
    $username = $_POST["username"] ?? null;
    $password = $_POST["password"] ?? null;

    if ((empty($matricule) || $matricule < 1 || strlen($matricule) != 8)) $errors["matricule"][] = 'Le matricule est obligatoire et doit comporter 8 chiffres!';
    if (empty($niveau)) $errors["niveau"] = "Le Niveau est Obligatoire";
    if (empty($nom) || mb_strlen($nom) > 250) $errors["nom"] = "Le Nom est Obligatoire (Max 250 characteres)";
    if (empty($prenom) || mb_strlen($prenom) > 250) $errors["prenom"] = "Le Prénom est Obligatoire (Max 250 characteres)";
    if (!empty($dob)) {
        $age = (new DateTime())->diff(new DateTime($dob))->y;

        if (!$age || $age < 15 || $age > 100) $errors["dob"] = "La Date de Naissance est Fausse (15 < Age < 100 Ans)";
    } else {
        $errors["dob"] = "La Date de Naissance est Obligatoire";
    }
    if (!empty($email)) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors["email"][] = "L'Email est Invalid";
    } else {
        $errors["email"][] = "L'Email est Obligatoire";
    }
    if (!empty($username)) {
        if (strlen($username) < 8 || strlen($username) > 25) {
            $errors["username"][] = "Le Nom d'Utilisateur doit contenir entre 8 et 25 characteres";
        };

        if (!preg_match('/^[a-zA-Z0-9]{8,25}$/', $username)) $errors["username"][] = "Le Nom d'Utilisateur doit contenir que des lettres et chiffres";
    } else {
        $errors["username"][] = "Le Nom d'Utilisateur est Obligatoire";
    }
    if (!empty($password)) {
        if (strlen($password) < 8) $errors["password"] = "Le Mot de Passe doit contenir au minimum 8 characteres";

        $password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        if (!$id) {
            $errors["password"] = "Le Mot de Passe est Obligatoire";
        }
    }

    $r = $conn->execute_query(
        "SELECT EXISTS (SELECT * FROM students WHERE matricule = ? AND id != ?), 
                EXISTS (SELECT * FROM students WHERE username = ? AND id != ?), 
                EXISTS (SELECT * FROM students WHERE email = ? AND id != ?)",
        [
            $matricule,
            $id ?? 0,
            $username,
            $id ?? 0,
            $email,
            $id ?? 0
        ]
    );
    $m_exists = $r->fetch_column(0);
    $u_exists = $r->fetch_column(1);
    $e_exists = $r->fetch_column(2);

    if ($m_exists) $errors["matricule"][] = "Ce Matricule existe déja!";
    if ($u_exists) $errors["username"][] = "Ce Nom d'Utilisateur existe déja!";
    if ($e_exists) $errors["email"][] = "Cet E-mail existe déja!";

    if (!count($errors)) {

        if ($id) {
            $q = "UPDATE students SET matricule = ?, niveau = ?, nom = ?, prenom = ?, dob = ?, email = ?, username = ?";
            $q .= $password ? ", password = ?" : "";
            $q .= " WHERE id = ?";
            $r = $conn->execute_query(
                $q,
                [
                    $matricule,
                    $niveau,
                    $nom,
                    $prenom,
                    $dob,
                    $email,
                    $username,
                    ...($password ? [$password] : []),
                    $id
                ]
            );

            header("Location: /admin/student_form.php?edit={$id}&success=updated");
            die();
        }

        $r = $conn->execute_query("INSERT INTO students (matricule, niveau, nom, prenom, dob, email, username, password) VALUES (?,?,?,?,?,?,?,?)", [
            $matricule,
            $niveau,
            $nom,
            $prenom,
            $dob,
            $email,
            $username,
            $password,
        ]);

        $matricule = $niveau = $nom = $prenom = $dob = $email = $username = $password = null;

        header("Location: /admin/student_form.php?edit={$conn->insert_id}&success=created");
        die();
    }
}

$r = $conn->execute_query("SELECT id, matricule, nom, prenom FROM teachers ORDER BY matricule ASC");
$teachers = $r->fetch_all(MYSQLI_ASSOC);

function verifierExiste($id)
{
    global $conn;

    $r = $conn->execute_query("SELECT * FROM students WHERE id = ?", [$id]);
    $student = $r->fetch_assoc();

    if (!$student) {
        header('Location: /admin/students.php?notfound');
        die();
    }

    return $student;
}

function printValidationErrors($champ)
{
    global $errors;

    if (isset($errors[$champ]) && is_array($errors[$champ])) {
        foreach ($errors[$champ] as $e) {
            echo "<div>{$e}</div>";
        }
    } else {
        echo "<div>" . ($errors[$champ] ?? "") . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../static/usthb.png">
    <title>Gestion des Etudiants</title>
    <link rel="stylesheet" href="../static/main.css">
    <link rel="stylesheet" href="../static/admin.css">
</head>

<body class="container">
    <div class="sidebar">
        <?php require '_nav.php' ?>
    </div>

    <div class="main">
        <div class="navbar">
            <a class="nav-link" href="/admin/students.php">◀ Retour</a>
            <h3 style="margin: 0 auto;">
                <?= ($mode === "edit") ? "Edition étudiant #{$student['id']}" : 'Ajouter un Etudiant' ?>
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
                    <?php foreach ($errors as $key => $error): ?>
                        <?php if (is_array($error)): ?>
                            <?php foreach ($error as $e): ?>
                                <div><?= $e ?></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div><?= $error ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="edit-form">
                <div class="input-group">
                    <label for="inputMatricule">Matricule (Format: 12345678)</label>
                    <input id="inputMatricule" class="text-input <?= isset($errors["nom"]) ? 'is-invalid' : '' ?>" name="matricule" type="text" placeholder="Matricule" inputmode="numeric" pattern="^(?!0{8})\d{8}$" minlength="8" maxlength="8" value="<?= $matricule ?? '' ?>" required>
                    <div class="invalid-input" style="display: block;">
                        <?php printValidationErrors("matricule") ?>
                    </div>
                </div>
                <div class="input-group">
                    <label for="inputNiveau">Niveau</label>
                    <select id="inputNiveau" class="text-input <?= isset($errors["niveau"]) ? 'is-invalid' : '' ?>" name="niveau">
                        <option value="" <?= isset($niveau) ? '' : 'selected' ?> disabled>Selectionnez le Niveau</option>
                        <?php foreach ($niveaux as $key => $text): ?>
                            <option value="<?= $key ?>" <?= (isset($niveau) && $niveau === $key) ? 'selected' : '' ?>> <?= $text ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-input">
                        <?= $errors["niveau"] ?? '' ?>
                    </div>
                </div>
                <div class="input-group">
                    <label for="inputName">Nom</label>
                    <input id="inputName" class="text-input <?= isset($errors["nom"]) ? 'is-invalid' : '' ?>" name="nom" type="text" placeholder="Nom" value="<?= $nom ?? '' ?>">
                    <div class="invalid-input" style="display: block;">
                        <?= $errors["nom"] ?? '' ?>
                    </div>
                </div>
                <div class="input-group">
                    <label for="inputPrenom">Prénom</label>
                    <input id="inputPrenom" class="text-input <?= isset($errors["prenom"]) ? 'is-invalid' : '' ?>" name="prenom" type="text" placeholder="Prénom" value="<?= $prenom ?? '' ?>">
                    <div class="invalid-input">
                        <?= $errors["prenom"] ?? '' ?>
                    </div>
                </div>
                <div class="input-group">
                    <label for="inputDOB">Date de Naissance</label>
                    <input id="inputDOB" class="text-input <?= isset($errors["dob"]) ? 'is-invalid' : '' ?>" name="dob" type="date" max="<?= date('Y-m-d') ?>" value="<?= $dob ?? '' ?>">
                    <div class="invalid-input" style="display: block;">
                        <?= $errors["dob"] ?? '' ?>
                    </div>
                </div>
                <div class="input-group">
                    <label for="inputEmail">E-mail</label>
                    <input id="inputEmail" class="text-input <?= isset($errors["email"]) ? 'is-invalid' : '' ?>" name="email" type="email" placeholder="E-mail" value="<?= $email ?? '' ?>">
                    <div class="invalid-input" style="display: block;">
                        <?php printValidationErrors("email") ?>
                    </div>
                </div>
                <div class="input-group">
                    <label for="inputUsername">Nom d'Utilisateur</label>
                    <input id="inputUsername" class="text-input <?= isset($errors["username"]) ? 'is-invalid' : '' ?>" name="username" type="text" maxlength="255" placeholder="Nom d'Utilisateur" value="<?= $username ?? '' ?>">
                    <div class="invalid-input" style="display: block;">
                        <?php printValidationErrors("username") ?>
                    </div>
                </div>
                <div class="input-group">
                    <label for="inputPassword">Mot de Passe</label>
                    <input id="inputPassword" class="text-input <?= isset($errors["password"]) ? 'is-invalid' : '' ?>" name="password" type="text" placeholder="Mot de Passe">
                    <div class="invalid-input" style="display: block;">
                        <?= $errors["password"] ?? '' ?>
                    </div>
                </div>
                <button class="btn" type="submit"><?= ($mode === "edit") ? "Sauvgarder" : 'Créer' ?></button>
            </form>

        </div>
    </div>

</body>

</html>