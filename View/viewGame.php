<?php
$titre = WEBSITE_TITLE.' -- Accueil';
?>

<?php ob_start(); ?>

  <body onkeydown="setTimeout(function(){sendaction(event);}, 200);">

  <div style="color:#3782F2;position:absolute;top:0px;left:0px;" ><a href="../index.php">Anthonydelgado.fr ©</a></div>
  <h1>Narandor (version 0.7)</h1>
  <div class="corps" ng-controller="playCtrl">
    <fieldset class="dashboard">
      <legend>Tableau de bord</legend>
      <div class="dbcontent">
        <fieldset>
          <legend>Mes informations</legend>
          <div class="mystatut">
            <div ng-class="{unactive : user.etat != 'alive'}">
              <div>Nom : {{ user.nom}}</div>
              <div>Position {{ user.position }}</div>
              <div class="infosname">Niveau</div><div class="barreprogression"><div style="width:{{ user.niveau }}0%">{{ user.niveau }}/10</div></div>
              <div class="infosname">Exp&eacute;rience</div><div class="barreprogression"><div style="width:{{ user.experience }}%">{{ user.experience }}/100</div></div>
              <div class="infosname">D&eacute;g&acirc;ts</div><div class="barreprogression barredegats"><div style="width:{{ user.degats }}%">{{ user.degats }}/100</div></div>
              <div class="infosname">Force</div><div class="barreprogression barreforce"><div style="width:{{ user.force }}%">{{ user.force }}/100</div></div>
              <div ng-if="user.protection" class="infosname">Protection</div><div ng-if="user.protection" class="barreprogression"><div style="width:{{ user.protection }}%">{{ user.protection }}/100</div></div>
              <div ng-if="user.magie" class="infosname">Magie</div><div ng-if="user.magie" class="barreprogression"><div style="width:{{ user.magie }}%">{{ user.magie }}/100</div></div>
            </div>
            <div ng-if="user.sleep">Vous avez &eacute;t&eacute; endormi !</div>
            <div ng-if="user.sleep">Vous devez attendre {{ user.sleep }} secondes avant de pouvoir bouger!</div>
            <div ng-if="user.etat == 'mort'">
              <div>Vous &ecirc;tes mort ! </div>
              <div><a href="index.php?action=game&restart=ok">Cliquez ICI pour rena&icirc;tre </a></div>
            </div>
          </div>
          <hr>
          <div id="myinventory">
            Chargement en cours...
          </div>
          <hr>
          <div class="commandes">
            <a href="#" onclick="open('how_to_play.php', 'Popup', 'scrollbars=1,resizable=0');return false;">Comment Jouer ?</a><br />
          </div>
        </fieldset>
        <fieldset class="console">
          <legend>Console</legend>
          <div class="resetconsole">
            <span  onclick="resetconsole();" title="Effacer la console" style="cursor:pointer;"></span>
          </div>
          <div class="msglist">
            <div ng-repeat="message in messages">
              <p ng-class="{msginfo : message.exp == 0}">{{ message.date }} :{{ message.contenu }}</p>
            </div>
          </div>
        </fieldset>
    </fieldset>
    <fieldset class="map" >
      <legend>Carte de jeu</legend>
      <div id="cartedejeu">
      </div>
      <div class="formsendmsg" style="display:none;">
        Appuyer sur Entr&eacute;e pour envoyer un message
        <form>
          <textarea name="message" id="msgsendconsole"></textarea>
        </form>
      </div>
    </fieldset>
  </div>
  <script src="assets/js/app.js"></script>
  <audio id="cantmove" src="assets/audio/bip.mp3"></audio>
  <audio id="porte" src="assets/audio/porte.mp3"></audio>
  </body>

<?php $contenu = ob_get_clean(); ?>

<?php
$donnees_vue = array(
    "titre" => $titre,
    "contenu" => $contenu
);
