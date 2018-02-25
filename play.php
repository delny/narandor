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


if ( isset($_POST['message']))
{
	$message = htmlspecialchars($_POST['message']);
	if (preg_match('#^/tp @me [0-9]{1,2} [0-9]{1,2}$#',$message))
	{
		$tab_message = explode(" ",$message);
		$localisation = $perso->getLocalisation();
		$coordx = $tab_message[2];
		$coordy = $tab_message[3];
		if ($persoManager->placeprise($localisation,$coordx,$coordy))
		{
			$message_perso = 'Impossible de vous t&eacute;l&eacute;port&eacute; &agrave; cet endroit';
      $messageManager->message_console($perso,$message_perso);
		}
		elseif ($perso->getLocalisation()!='C')
		{
			$message_perso = 'Impossible de vous t&eacute;l&eacute;port&eacute; depuis l\'int&eacute;rieur';
      $messageManager->message_console($perso,$message_perso);
		}
		else
		{
			$perso->hydrate([
				'localisation_x' => $coordx,
				'localisation_y' => $coordy
			]);
      $persoManager->update($perso);
			$message_perso = 'Vous avez &eacute;t&eacute; t&eacute;l&eacute;port&eacute; en '.$coordx.','.$coordy.'';
      $messageManager->message_console($perso,$message_perso);
		}
	}
	elseif (preg_match('#^/effect recovered @me$#',$message))
	{
		
		$retour = $perso->recuperer();
		switch ($retour)
		{
			case 0 : 
				$message_perso = 'Vous avez r&eacute;cuperer !';
        $messageManager->message_console($perso,$message_perso);
				$persoManager->update($perso);
				echo 'guerison';
				break;
			case 1 : 
				$message_perso = 'Aucun d&eacute;g&acirc;ts &agrave; soigner!';
        $messageManager->message_console($perso,$message_perso);
				echo 'Success';
				break;
			default : 
				$message_perso = 'Erreur inconnue';
        $messageManager->message_console($perso,$message_perso);
				echo 'Success';
		}
	}
	elseif (preg_match('#^/give @me more experience$#',$message))
	{
		$message_perso = 'Plus d\'exp&eacute;rience!';
    $messageManager->message_console($perso,$message_perso);
		if ($perso->gagnerexperience()==1 )
		{
			$message_perso = 'Bravo, vous passez au niveau '.$perso->getNiveau().'';
      $messageManager->message_console($perso,$message_perso);
		}
		$persoManager->update($perso);
		echo 'Success';
	}
	elseif($message!='')
	{
		$public = new Perso([
			'id' => 0
		]);
    $messageManager->message_console($public,$message);	
	}
	echo 'Success';
}
elseif (isset($_POST['direction']) AND $perso->getEtat()=='alive')
{
	// pour tout le monde -- se deplacer limiter à 5 par sec
	if (file_exists('tmp/move'.$perso->getId().'.tmp'))// on regarde si le fchier tmp existe et si oui on l'ouvre
	{
		$fichiermove = fopen('tmp/move'.$perso->getId().'.tmp','r+');
		$donnees_fichier = fgets($fichiermove);
		$tab_donnees = explode(";",$donnees_fichier);
		
		if ($tab_donnees[0] == time())
		{
			if ($tab_donnees[1] < 4)
			{
				$lignemove = $tab_donnees[1]+1;
				fseek($fichiermove,11);
				fputs($fichiermove,$lignemove);
				fclose($fichiermove);
				$moveoupas = TRUE;
			}
			else
			{
				$moveoupas = FALSE;
			}
		}
		else
		{
			$lignemove = ''.time().';1';
			fseek($fichiermove,0);
			fputs($fichiermove,$lignemove);
			fclose($fichiermove);
			$moveoupas = TRUE;
		}
	}
	else
	{
		// on cree le fichiermove
		$new_fichiermove = fopen('tmp/move'.$perso->getId().'.tmp','a+');
		fputs($new_fichiermove,time().';1');
		fclose($new_fichiermove);
		$agiroupas = TRUE;
	}
	
	$direction = $_POST['direction'];
	if ($direction == $perso->getDirection() AND $moveoupas )
	{
		$retour = $persoManager->sedeplacer($perso,$direction);
		switch ($retour)
		{
			case 0 : 
				echo 'Echec';
				break;
			case 1 : 
				$persoManager->update($perso);
				echo 'Success';
				break;
			case 2 : 
				$persoManager->update($perso);
				echo 'Passage';
				break;
			case 3 : 
				$persoManager->update($perso);
				echo 'Passage';
				break;
			default : 
				$message_perso = 'Erreur inconnue';
        $messageManager->message_console($perso,$message_perso);
				echo 'Success';
		}
	}
	else
	{
		$perso->hydrate(['direction' => $direction]);
		$persoManager->update($perso);
		echo 'Success';
	}
}
