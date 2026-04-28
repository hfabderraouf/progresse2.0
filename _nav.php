<?php
echo <<<HTML
<div class="navbar">
    <img class="nav-logo" src="./static/usthb.png">
    <a class="nav-link" href="/">Acceuil</a>
    <a class="nav-link" href="/about.php">A propos</a>
    <div style="margin-inline-start: auto;">
        <a class="nav-link" href="/login.php?t=student">Espace Etudiant</a>
        <a class="nav-link" href="/login.php?t=teacher">Espace Enseignant</a>
        <a class="nav-link" href="/login.php?t=admin">Espace Admin</a>
      
    </div>
</div>
HTML;