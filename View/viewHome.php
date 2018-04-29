<?php
$titre = WEBSITE_TITLE.' -- Accueil';
?>

<?php ob_start(); ?>

  <div id="corps" >
    <div class="welcome">
      <h1>Narandor<br /> (version Beta 0.8)</h1>
      <h2>Derni&egrave;re mise &agrave; jour : 26 novembre 2016</h2>
      <div><p>Se connecter / Cr&eacute;er un compte</p></div>
      <?php if (!empty($erreur)): ?>
        <div class="error"><?php print $erreur ?></div>
      <?php endif; ?>
      <div class="formlogin">
        <form method="post" action="index.php">
          <input type="text" name="login" placeholder="Login"/>
          <input type="password" name="password" placeholder="Mot de passe"/>
          <input type="submit" value="Jouer !"/>
        </form>
      </div>
    </div>
  </div>

<?php $contenu = ob_get_clean(); ?>

<?php
$donnees_vue = array(
    "titre" => $titre,
    "contenu" => $contenu
);
