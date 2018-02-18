<?php
  session_start();
  require ('App/Autoloader.php');
  Autoloader::register();
  $pid = (isset($_SESSION['pid'])) ? (int) $_SESSION['pid'] : false;
  $persoManager = new PersoManager();
  $perso = $persoManager->get($pid);
if (isset($_POST['recherche']) AND $_POST['recherche']=='ok' AND !empty($perso) )
{
	$localisation = $perso->localisation();
	$coord_x = $perso->localisation_x();
	$coord_y = $perso->localisation_y();

	$carte = $localisation.substr('0'.$coord_x, -2,1).''.substr('0'.$coord_y, -2,1).'';
	$carte_x = substr('0'.$coord_x, -2,1);
	$carte_y = substr('0'.$coord_y, -2,1);

	// on recup les persos de cette carte
	$persosaplacer = $persoManager->getpersoscarte($localisation,$carte_x,$carte_y);


	// carte
	echo '<div style="background:url(assets/img/maps/'.$carte.'.png);">';
	// on place les persos
	foreach ($persosaplacer as $persoaplacer)
	{
		$coord_x_pap = $persoaplacer['localisation_x'];
		$coord_y_pap = $persoaplacer['localisation_y'];
		$direction = $persoaplacer['direction'];
		
		$dest_x_pap = ($coord_x_pap%10)*50;
		$dest_y_pap = ($coord_y_pap%10)*50;
		
		$typeperso = $persoaplacer['typeperso'];
		
		switch($typeperso)
		{
			case 0:
			$type = 'default';
			break;
			case 1 :
			$type = 'guerrier';
			break;
			case 2 :
			$type = 'magicien';
			break;
			case 10 :
			$type = 'enfant';
			break;
			default:
			$type = 'default';
		}
		
		echo '<span class="perso" style="top:'.$dest_y_pap.'px;left:'.$dest_x_pap.'px;background:url(assets/img/profiles/'.$type.'_'.$direction.'.png);">';
		$nom_perso = ($persoaplacer['nom'] == $perso->nom()) ? 'Vous' : $persoaplacer['nom'];
		echo $nom_perso.'<br />N°'.$persoaplacer['niveau'].'' ;
		echo '</span>';
	}
	echo '</div>';
}
?>