<?php
  session_start();
  require ('App/Autoloader.php');
  Autoloader::register();
  $pid = (isset($_SESSION['pid'])) ? (int) $_SESSION['pid'] : false;
  $persoManager = new PersoManager();
  $perso = $persoManager->get($pid);
if (!isset($pid))
{
	exit;
}
$tab_coord_x_bot = [0,15,27,67,8,0,0,0,0,0,0,83,81];
$tab_coord_y_bot = [0,54,7,38,17,0,0,0,0,0,0,32,17];

$tab_min_niveau_bot = [0,3,6,2,1];
$tab_max_niveau_bot = [0,7,9,3,2];

$tab_min_x_bot = [0,11,25,64,1,0,0,0,0,0,0,78,77];
$tab_max_x_bot = [0,34,34,68,8,0,0,0,0,0,0,88,87];

$tab_min_y_bot = [0,36,6,36,7,0,0,0,0,0,0,29,15];
$tab_max_y_bot = [0,76,23,56,27,0,0,0,0,0,0,38,21];

// robot qui attaque
for ($i=1;$i<5;$i++)
{
	if (!$bot = $persoManager->getbot('BOT'.$i)) // s'il n existe pas on le creer
	{
		$niveau = rand($tab_min_niveau_bot[$i],$tab_max_niveau_bot[$i]);
		$bot = new Bot([
			'nom' => 'BOT'.$i,
			'password' => 'NULL',
			'etat' => 'alive',
			'localisation' => 'C',
			'localisation_x' => $tab_coord_x_bot[$i],
			'localisation_y' => $tab_coord_y_bot[$i],
			'direction' => 'droite',
			'niveau' => $niveau,
			'experience' => 0,
			'degats' => 0,
			'typeperso' => 0,
			'special' => 0
		]);
		$persoManager->add($bot);
	}

	// s'il est mort on le resucite
	if ( $bot->getEtat()=='dead' )
	{
		if (!$persoManager->getpersoscarte('C',floor($tab_coord_x_bot[$i]/10),floor($tab_coord_y_bot[$i]/10)))
		{
			$niveau = rand($tab_min_niveau_bot[$i],$tab_max_niveau_bot[$i]);
			$bot->hydrate([
				'etat' => 'alive',
				'localisation_x' => $tab_coord_x_bot[$i],
				'localisation_y' => $tab_coord_y_bot[$i],
				'direction' => 'droite',
				'niveau' => $niveau,
				'experience' => 0,
				'degats' => 0
				]);
			$persoManager->update($bot);
		}
	}
	elseif (preg_match('#sleep#',$bot->getEtat()))
	{
		// on attend qu'il se reveille
		echo 'Success';
	}
	elseif($joueur = $persoManager->getadversaire($bot)) //il attaque
	{
		if ($joueur->getEtat()=='alive')
		{
			$retour = $bot->frapper($joueur);
		}
		else
		{
			$retour = 4;
		}
		switch ($retour)
		{
			case 0 : 
				$message_cible = $bot->getNom().' a tent&eacute; de vous frapper ... ';
				$persoManager->message_console($joueur,$message_cible);
				$persoManager->update($bot);
				$persoManager->update($joueur);
				break;
			case 1 : 
				$message_cible = $bot->getNom(). ' vous a frapp&eacute; !';
				$persoManager->message_console($joueur,$message_cible);
				if ($bot->gagnerexperience()==1 )
				{
					$message_perso = 'Bravo, vous passez au niveau '.$bot->getNiveau().'';
					$persoManager->message_console($bot,$message_perso);
				}
				$persoManager->update($bot);
				$persoManager->update($joueur);
				break;
			case 2 : 
				$message_cible = $bot->getNom().' vous a tu&eacute; !';
				$persoManager->message_console($joueur,$message_cible);
				if ($bot->gagnerexperience()==1 )
				{
					$message_perso = 'Bravo, vous passez au niveau '.$bot->getNiveau().'';
					$persoManager->message_console($bot,$message_perso);
				}
				$persoManager->update($bot);
				$persoManager->update($joueur);
				break;
			case 3 : 
				$message_perso = 'Mais ... pourquoi voulez-vous vous frapper ?';
				$persoManager->message_console($bot,$message_perso);
				break;
			case 4 : 
				$message_perso = 'Personne &agrave; frapper !';
				$persoManager->message_console($bot,$message_perso);
				break;
			default : 
				$message_perso = 'Erreur inconnue';
				$persoManager->message_console($bot,$message_perso);
		}
		echo 'Success';
	}
	elseif ($retour = $persoManager->getadversaireacote($bot)) //si quelqun a cote il se tourne vers lui
	{
		switch ($retour)
		{
			case 1 : 
				$bot->Setdirection('haut');
				break;
			case 2 : 
				$bot->Setdirection('droite');
				break;
			case 3 : 
				$bot->Setdirection('bas');
				break;
			case 4 : 
				$bot->Setdirection('gauche');
				break;
			default : 
				$bot->Setdirection('gauche');
		}
		$persoManager->update($bot);
		echo 'Success';
	}
	else // si personne il se deplace
	{
		if ($bot->getLocalisationX()<=$tab_min_x_bot[$i])
		{
			$direction = 'droite';
		}
		elseif ($bot->getLocalisationX()>=$tab_max_x_bot[$i])
		{
			$direction = 'gauche';
		}
		elseif ($bot->getLocalisationY()<=$tab_min_y_bot[$i])
		{
			$direction = 'bas';
		}
		elseif ($bot->getLocalisationY()>=$tab_max_y_bot[$i])
		{
			$direction = 'haut';
		}
		else
		{
			$direction_number = rand(1,4);
			switch ($direction_number)
			{
				case 1:
					$direction = 'haut';
				break;
				case 2:
					$direction = 'droite';
				break;
				case 3:
					$direction = 'bas';
				break;
				case 4:
					$direction = 'gauche';
				break;
				default:
					$direction = 'haut';
			}
		}
		
		
		if ($direction == $bot->getDirection())
		{
			$persoManager->sedeplacer($bot,$direction);
		}
		else
		{
			$bot->hydrate(['direction' => $direction]);
		}
		
		$persoManager->update($bot);
		echo 'Success';
	}
}

for ($i=11;$i<13;$i++)// enfant
{
	if (!$bot = $persoManager->getbot('BOT'.$i)) // s'il n existe pas on le creer
	{
		$bot = new Bot([
			'nom' => 'BOT'.$i,
			'password' => 'NULL',
			'etat' => 'alive',
			'localisation' => 'C',
			'localisation_x' => $tab_coord_x_bot[$i],
			'localisation_y' => $tab_coord_y_bot[$i],
			'direction' => 'droite',
			'niveau' => 0,
			'experience' => 0,
			'degats' => 0,
			'typeperso' => 10,
			'special' => 0
		]);
		$persoManager->add($bot);
	}

	if (preg_match('#sleep#',$bot->getEtat()))
	{
		// on attend qu'il se reveille
		echo 'Success';
	}
	elseif ($retour = $persoManager->getadversaireacote($bot)) //si quelqun a cote il se tourne vers lui
	{
		switch ($retour)
		{
			case 1 : 
				$bot->Setdirection('haut');
				break;
			case 2 : 
				$bot->Setdirection('droite');
				break;
			case 3 : 
				$bot->Setdirection('bas');
				break;
			case 4 : 
				$bot->Setdirection('gauche');
				break;
			default : 
				$bot->Setdirection('gauche');
		}
		$persoManager->update($bot);
		echo 'Success';
	}
	else // si personne il se deplace
	{
		if ($bot->getLocalisationX()<=$tab_min_x_bot[$i])
		{
			$direction = 'droite';
		}
		elseif ($bot->getLocalisationX()>=$tab_max_x_bot[$i])
		{
			$direction = 'gauche';
		}
		elseif ($bot->getLocalisationY()<=$tab_min_y_bot[$i])
		{
			$direction = 'bas';
		}
		elseif ($bot->getLocalisationY()>=$tab_max_y_bot[$i])
		{
			$direction = 'haut';
		}
		else
		{
			$direction_number = rand(1,4);
			switch ($direction_number)
			{
				case 1:
					$direction = 'haut';
				break;
				case 2:
					$direction = 'droite';
				break;
				case 3:
					$direction = 'bas';
				break;
				case 4:
					$direction = 'gauche';
				break;
				default:
					$direction = 'haut';
			}
		}
		
		
		if ($direction == $bot->getDirection() )
		{
			$persoManager->sedeplacer($bot,$direction);
		}
		else
		{
			$bot->hydrate(['direction' => $direction]);
		}
		
		$persoManager->update($bot);
		echo 'Success';
	}
}
