<?php
require_once '../database.php';
require_once '../Auth.php';

$user = (new Auth($conn))->user();

function nav($user)
{

    $links = [
        "Cursus" => "/student/index.php",
    ];

    echo <<<HTML
        <a href="/" class="header">
            <img class="logo" src="../static/usthb.png" width="54">
            <div style="margin-inline-start: 4px;">
                <b>PROGRESS</b>
                <div>Espace Etudiant</div>
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
            <div style="padding: .5rem 1rem;">Etudiant: [#{$user['id']}] {$user['nom']}</div>
            <form action="/login.php" method="post">
                <input type="hidden" name="_method" value="DELETE">
                <button class="nav-link" type="submit" style="border: 0; background: none; cursor: pointer;color:red" href="#">Déconnexion</button>
            </form>
        </div>
    HTML;
}
?>

<?= nav($user) ?>
