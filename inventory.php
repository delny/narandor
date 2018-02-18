<?php
  session_start();
  require ('App/Autoloader.php');
  Autoloader::register();
  $pid = (isset($_SESSION['pid'])) ? (int) $_SESSION['pid'] : false;
  $persoManager = new PersoManager();
  $perso = $persoManager->get($pid);
  // @TODO : intégrer ça de manière plus conforme MVC
if (isset($_POST['get']) AND $_POST['get']=='inventory' AND !empty($perso) )
{

	// on recup les objets du joueur
	$objets = $persoManager->getobjetsjoueur($perso);

	// on place les objets sur l'inventaire
	echo 'Inventaire';
	echo '<div style="background:url(assets/img/objets/inventory.png);">';
	$i=1;
	foreach ($objets as $objet)
	{
		$idobjet = $objet['idobjet'];
		$nomobjet = $objet['nom'];
		$idobjetunique = $objet['idobjetunique'];
		
		switch($i)
		{
			case 1:
			$x = 8;
			$y = 8;
			break;
			case 2 :
			$x = 62;
			$y = 8;
			break;
			case 3 :
			$x = 116;
			$y = 8;
			break;
			case 4 :
			$x = 170;
			$y = 8;
			break;
			case 5 :
			$x = 8;
			$y = 62;
			break;
			case 6 :
			$x = 62;
			$y = 62;
			break;
			case 7 :
			$x = 116;
			$y = 62;
			break;
			case 8 :
			$x = 170;
			$y = 62;
			break;
			default:
			$x = 8;
			$y = 8;
		}
		
		echo '<span class="objet" title="cliquez pour utiliser" Onclick="useobject('.$idobjetunique.');" style="left:'.$x.'px;top:'.$y.'px;background:url(assets/img/objets/objet'.$idobjet.'.png);">';
		echo $nomobjet ;
		echo '</span>';
		$i++;
	}
	echo '</div>';
}
?>