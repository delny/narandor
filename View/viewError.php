<?php

$titre = WEBSITE_TITLE.' -- Erreur';
?>

<?php ob_start(); ?>

    <div class="alert alert-danger" role="alert">
        <p>Une erreur vient de se produire : </p>
        <p><?php echo $error; ?></p>
    </div>
<?php $contenu = ob_get_clean(); ?>

<?php
$donnees_vue = array(
    "titre" => $titre,
    "contenu" => $contenu
);
