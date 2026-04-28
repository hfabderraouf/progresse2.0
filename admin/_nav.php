<?php
require_once '../database.php';
require_once '../Auth.php';

$user = (new Auth($conn))->user();

function nav($user)
{

    $links = [
        "Statistiques" => "/admin/index.php",
        "Gestion des étudiants" => "/admin/students.php",
        "Gestion des enseignants" => "/admin/teachers.php",
        "Gestion des modules" => "/admin/modules.php",
        "Gestion des notes" => "/admin/notes.php",
       
    ];

    echo <<<HTML
        <a href="/" class="header">
            <img class="logo" src="../static/usthb.png" width="54" style="border-radius: 30%;" >
            <div style="margin-inline-start: 4px;">
                <b>PROGRESS</b>
                <div>Espace Administrateur</div>
            </div>
        </a>
        <hr>
    HTML;

    foreach ($links as $title => $path) {
        $class = "nav-link";
        if ($_SERVER["SCRIPT_NAME"] === $path) $class .= " active";
        if (str_ends_with($path, "index.php")) $path = substr_replace($path, "", -9);

        echo <<<HTML
            <a class="{$class}" href="{$path}">{$title}</a>
        HTML;
    }


    echo <<<HTML
        <div style="margin-block-start: auto;">
            <div style="padding: .5rem 1rem;">Utilisateur: [#{$user['id']}] {$user['nom']}</div>
            <form action="/login.php" method="post">
                <input type="hidden" name="_method" value="DELETE">
                <button class="nav-link" type="submit" style="border: 0; background: none; cursor: pointer;color:red" href="#">Déconnexion</button>
            </form>
        </div>
    HTML;
}
?>

<?= nav($user) ?>
