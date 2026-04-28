<?php

require_once 'database.php';
require_once 'Auth.php';

$error = '';

$auth = new Auth($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === "DELETE") {
    $auth->logout();
}

$allowed_types = ['admin', 'teacher', 'student'];

if (!isset($_GET['t']) || empty($_GET['t']) || !in_array($_GET['t'], $allowed_types)) {
    header('Location: /');
    die();
}
$type = (string) $_GET['t'];

if ($user = $auth->user()) {
    header("Location: /{$user['type']}/");
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $user = $auth->login($type, $_POST['username'], $_POST['password']);
        if ($user) {
            header("Location: /{$type}/");
            die();
        } else {
            $error = "Identifiants erronés!";
        }
    } else {
        $error = "Veuillez entrer vous identifiants!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="./static/usthb.png">
    <title>Login</title>
    <link rel="stylesheet" href="./static/main.css">
    <link rel="stylesheet" href="./static/front.css">

    <style>
        body {
            display: flex;
            align-items: center;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            width: 300px;
            margin: 0 auto;
        }

        .logo {
            width: 180px;
            margin-bottom: 8px;
            margin-inline: auto;
        }

        .text-input {
            margin-bottom: 16px;
        }
    </style>
</head>

<body>
    <form method="post" action="" class="login-form">
        <a href="/" style="display: flex;">
            <img class="logo" src="./static/usthb.png">
        </a>
        <h3 style="text-align: center;">Connexion</h3>
        <?php if ($error): ?>
            <div class="error-message">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <input class="text-input" name="username" type="text" placeholder="Nom Utilisateur"  autocomplete="username" required autofocus>
        <input class="text-input" name="password" type="password" placeholder="Mot de passe" value="azerty" autocomplete="password" required>
        <button class="btn" type="submit">Connexion</button>
    </form>
</body>

</html>