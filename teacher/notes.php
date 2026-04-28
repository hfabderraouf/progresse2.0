<?php
require_once '../database.php';
require_once '../Auth.php';

$auth = new Auth($conn);

$auth->exigerRole('teacher');

$user = $auth->user();

$mode = isset($_GET["student_id"], $_GET["module_id"]) ? 'edit' : 'select';
$errors = isset($_GET["notfound"]) ? ["Id erroné!"] : [];
$success = isset($_GET["success"]) ? "Note sauvgardé!" : '';

if ($mode === 'edit') {
    $selectedStudentId = $_GET["student_id"] ?? null;
    $selectedModuleId = $_GET["module_id"] ?? null;

    if (!$selectedStudentId || !$selectedModuleId) {
        header('Location: /teacher/notes.php');
        die();
    }

    $r = $conn->execute_query("SELECT id, matricule, nom, prenom FROM students WHERE id = ?", [$selectedStudentId]);
    $student = $r->fetch_assoc();

    $r = $conn->execute_query("SELECT * FROM modules WHERE id = ? AND teacher_id = ?", [$selectedModuleId, $user['id']]);
    $module = $r->fetch_assoc();

    if (!$student || !$module) {
        header('Location: /teacher/notes.php?notfound');
        die();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $note = $_POST["note"] ?? null;

        if (!is_numeric($note) || $note < 0 || $note > 20) $errors["note"] = "La Note est Obligatoire [0 - 20]";

        if (!count($errors)) {
            $r = $conn->execute_query(
                "INSERT INTO notes (student_id, module_id, note) VALUES (?,?,?) AS new ON DUPLICATE KEY UPDATE note = new.note",
                [
                    $selectedStudentId,
                    $selectedModuleId,
                    $note,
                ]
            );

            $_GET['success'] = 1;

            header('Location: /teacher/notes.php?' . http_build_query($_GET));
            die();
        }
    }

    $r = $conn->execute_query("SELECT note FROM notes WHERE student_id = ? AND module_id = ?", [$selectedStudentId, $selectedModuleId]);
    $note = $r->fetch_column(0);
} else {
    $r = $conn->execute_query("SELECT id, matricule, nom, prenom FROM students ORDER BY matricule ASC");
    $students = $r->fetch_all(MYSQLI_ASSOC);

    $r = $conn->execute_query("SELECT * FROM modules WHERE teacher_id = ? ORDER BY id ASC", [$user['id']]);
    $modules = $r->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="../static/usthb.png">
    <title>Gestion des Notes</title>
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
                <a class="nav-link" href="/teacher/notes.php">◀ Retour</a>
            <?php endif; ?>

            <h3 style="margin: 0 auto;">
                <?= ($mode === "select") ? 'Gestion des notes' : "Edition notes de l'étudiant #{$selectedStudentId}" ?>
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
                        <div><?= $error ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($mode === "select"): ?>
                <h4>Veuillez selectionner l'étudiant(e) et le module:</h4>
                <form action="" method="GET" class="edit-form">
                    <div class="input-group">
                        <label for="inputStudent">Etudiant(e)</label>
                        <select id="inputStudent" class="text-input" name="student_id" required>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id'] ?>">
                                    <?= '[' . $student['matricule'] . '] ' . $student['nom'] . ' ' . $student['prenom'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="inputModule">Module</label>
                        <select id="inputModule" class="text-input" name="module_id" required>
                            <?php foreach ($modules as $module): ?>
                                <option value="<?= $module['id'] ?>">
                                    <?= '[#' . $module['id'] . '] ' . $module['intitule'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn" type="submit">Sélectioner</button>
                </form>
            <?php else: ?>
                <h4>Veuillez saisir la note:</h4>
                <form action="" method="POST" class="edit-form">
                    <div class="input-group">
                        <label for="inputStudent">Etudiant(e)</label>
                        <select id="inputStudent" class="text-input" name="student_id" required disabled>
                            <option value="<?= $student['id'] ?>" selected disabled>
                                <?= '[' . $student['matricule'] . '] ' . $student['nom'] . ' ' . $student['prenom'] ?>
                            </option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="inputModule">Module</label>
                        <select id="inputModule" class="text-input" name="module_id" required disabled>
                            <option value="<?= $module['id'] ?>" selected disabled>
                                <?= '[#' . $module['id'] . '] ' . $module['intitule'] ?>
                            </option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="inputNote">Note</label>
                        <input id="inputNote" class="text-input" name="note" type="number" min="0" max="20" value="<?= $note ?? '' ?>" required>
                    </div>
                    <button class="btn" type="submit">Sauvgarder</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>