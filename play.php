<?php
  session_start();
  require ('App/Autoloader.php');
  Autoloader::register();
  $pid = (isset($_SESSION['pid'])) ? (int) $_SESSION['pid'] : false;
  $persoManager = new PersoManager();
  $objectManager = new ObjectManager();
  $messageManager = new MessageManager();
  $perso = $persoManager->get($pid);
  // @TODO : deplacer tout ça proprement dans un controller
if (empty($pid))
{
  var_dump('pas de pid !');
  var_dump($_SESSION['pid']);
	exit;
}

// compte pour regenerermagie
if ($perso->getTypeperso()==2)
{
	if (file_exists('tmp/magic'.$perso->getId().'.tmp'))// on regarde si le fchier tmp existe et si oui on l'ouvre
	{
		$fichier = fopen('tmp/magic'.$perso->getId().'.tmp','r+');
		$donnees_fichier = fgets($fichier);
		
		if (time() -  $donnees_fichier > 5)
		{
			fseek($fichier,0);
			fputs($fichier,time());
			fclose($fichier);
			$reponse = TRUE;
		}
		else
		{
			$reponse = FALSE;
		}
	}
	else
	{
		// on cree le fichier
		$new_fichier = fopen('tmp/magic'.$perso->getId().'.tmp','a+');
		fputs($new_fichier,time());
		fclose($new_fichier);
		$reponse = TRUE;
	}

	if ($reponse)
	{
		$perso->regenerermagie();
    $persoManager->update($perso);
	}	
}
