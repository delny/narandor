<?php
  session_start();
  require ('App/Autoloader.php');
  Autoloader::register();
  $pid = (isset($_SESSION['pid'])) ? (int) $_SESSION['pid'] : false;
  $persoManager = new PersoManager();
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

/*
Gestion personnes endormis
*/
$persoManager->updatesleeppeople();
/*
Fin gestion endormi
*/
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
			$persoManager->message_console($perso,$message_perso);
		}
		elseif ($perso->getLocalisation()!='C')
		{
			$message_perso = 'Impossible de vous t&eacute;l&eacute;port&eacute; depuis l\'int&eacute;rieur';
			$persoManager->message_console($perso,$message_perso);
		}
		else
		{
			$perso->hydrate([
				'localisation_x' => $coordx,
				'localisation_y' => $coordy
			]);
      $persoManager->update($perso);
			$message_perso = 'Vous avez &eacute;t&eacute; t&eacute;l&eacute;port&eacute; en '.$coordx.','.$coordy.'';
			$persoManager->message_console($perso,$message_perso);
		}
	}
	elseif (preg_match('#^/effect recovered @me$#',$message))
	{
		
		$retour = $perso->recuperer();
		switch ($retour)
		{
			case 0 : 
				$message_perso = 'Vous avez r&eacute;cuperer !';
				$persoManager->message_console($perso,$message_perso);
				$persoManager->update($perso);
				echo 'guerison';
				break;
			case 1 : 
				$message_perso = 'Aucun d&eacute;g&acirc;ts &agrave; soigner!';
				$persoManager->message_console($perso,$message_perso);
				echo 'Success';
				break;
			default : 
				$message_perso = 'Erreur inconnue';
				$persoManager->message_console($perso,$message_perso);
				echo 'Success';
		}
	}
	elseif (preg_match('#^/give @me more experience$#',$message))
	{
		$message_perso = 'Plus d\'exp&eacute;rience!';
		$persoManager->message_console($perso,$message_perso);
		if ($perso->gagnerexperience()==1 )
		{
			$message_perso = 'Bravo, vous passez au niveau '.$perso->getNiveau().'';
			$persoManager->message_console($perso,$message_perso);
		}
		$persoManager->update($perso);
		echo 'Success';
	}
	elseif($message!='')
	{
		$public = new Perso([
			'id' => 0
		]);
		$persoManager->message_console($public,$message);	
	}
	echo 'Success';
}
elseif ( isset($_POST['restart']) AND $_POST['restart']=='ok')
{
	if ( $perso->getEtat()=='dead' )
	{
		$x = 1 ;
		$y = 1 ;
		while ($persoManager->placeprise('C',$x,$y))
		{
			$x = rand(1,9);
			$y = rand(1,9);
		}
		$perso->hydrate([
			'etat' => 'alive',
			'localisation' => 'C',
			'localisation_x' => $x,
			'localisation_y' => $y,
			'direction' => 'bas',
			'niveau' => 1,
			'experience' => 0,
			'degats' => 0,
			'special' => 1
			]);
		$persoManager->update($perso);
		$message_perso = 'Bon retour &agrave; Narandor';
		$persoManager->message_console($perso,$message_perso);
	}
}
elseif ( isset($_POST['reset']) AND $_POST['reset']=='console')
{
	$persoManager->resetconsole($perso);
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
				$persoManager->message_console($perso,$message_perso);
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
elseif (isset($_POST['agir']) AND $perso->getEtat()=='alive')
{
	
	// pour tout le monde -- action limiter à 2 par sec
	if (file_exists('tmp/'.$perso->getId().'.tmp'))// on regarde si le fchier tmp existe et si oui on l'ouvre
	{
		$fichier = fopen('tmp/'.$perso->getId().'.tmp','r+');
		$donnees_fichier = fgets($fichier);
		$tab_donnees = explode(";",$donnees_fichier);
		if ($tab_donnees[0] == time())
		{
			if ($tab_donnees[1] < 2)
			{
				$ligneagir = $tab_donnees[1]+1;
				fseek($fichier,11);
				fputs($fichier,$ligneagir);
				fclose($fichier);
				$agiroupas = TRUE;
			}
			else
			{
				$agiroupas = FALSE;
			}
		}
		else
		{
			$ligneagir = ''.time().';1';
			fseek($fichier,0);
			fputs($fichier,$ligneagir);
			fclose($fichier);
			$agiroupas = TRUE;
		}
	}
	else
	{
		// on cree le fichier
		$new_fichier = fopen('tmp/'.$perso->getId().'.tmp','a+');
		fputs($new_fichier,time().';1');
		fclose($new_fichier);
		$agiroupas = TRUE;
	}
	
	if ($agiroupas)
	{
		if ($cible = $persoManager->getadversaire($perso))
		{
			if ($cible->getEtat()!='dead' AND $_POST['agir']=='frapper')
			{
				$retour = $perso->frapper($cible);
			}
			elseif ($_POST['agir']=='endormir' AND $perso->getTypeperso()==2 )
			{
				$retour = $perso->endormir($cible);
			}
			else
			{
				$retour = 4;
			}
			
			if ($_POST['agir']=='frapper')
			{
				switch ($retour)
				{
					case 0 : 
						$message_perso = $cible->getNom().' n\'a rien senti !';
						$message_cible = $perso->getNom().' a tent&eacute; de vous frapper ... ';
						$persoManager->message_console($perso,$message_perso);
						$persoManager->message_console($cible,$message_cible);
						$persoManager->update($perso);
						$persoManager->update($cible);
						break;
					case 1 : 
						$message_perso = 'Vous avez frapp&eacute; '.$cible->getNom();
						$message_cible = $perso->getNom(). ' vous a frapp&eacute; !';
						$persoManager->message_console($perso,$message_perso);
						$persoManager->message_console($cible,$message_cible);
						if ($perso->gagnerexperience()==1 )
						{
							$message_perso = 'Bravo, vous passez au niveau '.$perso->getNiveau();
							$persoManager->message_console($perso,$message_perso);
						}
						$persoManager->update($perso);
						$persoManager->update($cible);
						break;
					case 2 : 
						$message_perso = 'Vous avez tu&eacute; '.$cible->getNom();
						$message_cible = $perso->getNom().' vous a tu&eacute; !';
						$persoManager->message_console($perso,$message_perso);
						$persoManager->message_console($cible,$message_cible);
						if ($perso->gagnerexperience()==1 )
						{
							$message_perso = 'Bravo, vous passez au niveau '.$perso->getNiveau();
							$persoManager->message_console($perso,$message_perso);
						}
						$persoManager->update($perso);
						$persoManager->update($cible);
						break;
					case 3 : 
						$message_perso = 'Il est interdit de frapper les enfants!';
						$persoManager->message_console($perso,$message_perso);
						break;
					case 5 : 
						$message_perso = 'Mais ... pourquoi voulez-vous vous frapper ?';
						$persoManager->message_console($perso,$message_perso);
						break;
					case 6 : 
						$message_perso = 'Personne &agrave; frapper !';
						$persoManager->message_console($perso,$message_perso);
						break;
					default : 
						$message_perso = 'Erreur inconnue';
						$persoManager->message_console($perso,$message_perso);
				}
			}
			elseif ($_POST['agir']=='endormir')
			{
				switch ($retour)
				{
					case 0 : 
						$message_perso = $cible->getNom().' est d&eacute;j&agrave; endormi !';
						$persoManager->message_console($perso,$message_perso);
						$persoManager->update($perso);
						break;
					case 1 : 
						$message_perso = 'Vous avez endormi '.$cible->getNom();
						$message_cible = $perso->getNom(). ' vous a endormi !';
						$persoManager->message_console($perso,$message_perso);
						$persoManager->message_console($cible,$message_cible);
						$persoManager->update($perso);
						$persoManager->update($cible);
						break;
					case 2 : 
						$message_perso = 'Vous n\'avez pas assez de magie pour endormir '.$cible->getNom();
						$persoManager->message_console($perso,$message_perso);
						$persoManager->update($perso);
						break;
					case 3 : 
						$message_perso = 'Mais ... pourquoi voulez-vous vous endormir ?';
						$persoManager->message_console($perso,$message_perso);
						break;
					case 4 : 
						$message_perso = 'Personne &agrave; endormir !';
						$persoManager->message_console($perso,$message_perso);
						break;
					default : 
						$message_perso = 'Erreur inconnue';
						$persoManager->message_console($perso,$message_perso);
				}
			}
			echo 'Success';
		}
		elseif ($persoManager->ispuit($perso))
		{
			$retour = $perso->recuperer();
			
			switch ($retour)
			{
				case 0 : 
					$message_perso = 'Vous avez r&eacute;cuperer !';
					$persoManager->message_console($perso,$message_perso);
					if ($perso->gagnerexperience()==1 )
					{
						$message_perso = 'Bravo, vous passez au niveau '.$perso->getNiveau().'';
						$persoManager->message_console($perso,$message_perso);
					}
					$persoManager->update($perso);
					echo 'guerison';
					break;
				case 1 : 
					$message_perso = 'Mmmm ... cette eau est bonne';
					$persoManager->message_console($perso,$message_perso);
					echo 'Success';
					break;
				default : 
					$message_perso = 'Erreur inconnue';
					$persoManager->message_console($perso,$message_perso);
					echo 'Success';
			}
		}
		elseif ($persoManager->iscoffre($perso))
		{
			$retour = $perso->ouvrircoffre();
			
			if ($persoManager->isfullinventory($perso))
			{
				$message_perso = 'Votre inventaire est plein !';
				$persoManager->message_console($perso,$message_perso);
				$persoManager->update($perso);
				echo 'Success';
			}
			else
			{
				switch ($retour)
				{
					case 0 : 
						$message_perso = 'Ce coffre contient .... rien !';
						$persoManager->message_console($perso,$message_perso);
						$persoManager->update($perso);
						echo 'Success';
						break;
					case 1 : 
						$message_perso = 'Ouah! Un globe magique!';
						$persoManager->message_console($perso,$message_perso);
						$persoManager->addobjet($perso,1);
						echo 'Success';
						break;
					case 3 : 
						$message_perso = 'Ouah! Une potion de guerison !';
						$persoManager->message_console($perso,$message_perso);
						$persoManager->addobjet($perso,3);
						echo 'Success';
						break;
					default : 
						$message_perso = 'Erreur inconnue';
						$persoManager->message_console($perso,$message_perso);
						echo 'Success';
				}
			}
		}
		elseif ($retour = $persoManager->issage($perso))
		{
			switch ($retour)
			{
				case 10 : 
					$message_perso = 'Tu m\'as l\'air bien fatigu&eacute;! Reposes toi un peu';
					$persoManager->message_console($perso,$message_perso);
					$perso->recuperer();
					$persoManager->update($perso);
					echo 'Success';
					break;
				case 11 : 
					$message_perso = 'Tu m\'as l\'air bien en forme !';
					$persoManager->message_console($perso,$message_perso);
					echo 'Success';
					break;
				case 12 : 
					$message_perso = 'Tu es bien plus en forme que moi !';
					$persoManager->message_console($perso,$message_perso);
					echo 'Success';
					break;
				case 40 : 
					$message_perso = 'Qui est tu donc pour t\'adresser &agrave; moi!';
					$persoManager->message_console($perso,$message_perso);
					$persoManager->update($perso);
					echo 'Success';
					break;
				case 41 : 
					$message_perso = 'Voil&agrave; pour toi!';
					$message_perso_suite = 'Avec ceci, tu deviendra un grand magicien!';
					$persoManager->message_console($perso,$message_perso);
					$persoManager->message_console($perso,$message_perso_suite);
					$persoManager->addobjet($perso,1);
					$persoManager->update($perso);
					echo 'Success';
					break;
				case 42 : 
					$message_perso = 'tu es un guerrier Sors de chez moi !';
					$persoManager->message_console($perso,$message_perso);
					$persoManager->update($perso);
					echo 'Success';
					break;
				case 43 : 
					$message_perso = 'Tu es un grand magicien! Bravo !';
					$persoManager->message_console($perso,$message_perso);
					$persoManager->update($perso);
					echo 'Success';
					break;
				default : 
					$message_perso = 'Erreur inconnue';
					$persoManager->message_console($perso,$message_perso);
					echo 'Success';
			}

		}
	}
}
elseif (isset($_POST['use']) AND $perso->getEtat()=='alive')
{
	$idobjetunique = (int) $_POST['use'];
	$objet = $persoManager->getobjet($perso,$idobjetunique);
	$idobjet = (int) $objet['idobjet'];
	switch($idobjet)
	{
		case 1 : 
			// chgt en magicien
			$perso->setTypeperso(2);
			$persoManager->update($perso);
			$message_perso = 'Le globe vous a transform&eacute; en magicien!';
			$persoManager->message_console($perso,$message_perso);
			$persoManager->deleteobjet($idobjetunique);
			echo 'Success';
		break;
		case 3 :
			// potion de guerison
			$perso->recuperer();
			$persoManager->update($perso);
			$message_perso = 'Vous avez r&eacute;cuperer !';
			$persoManager->message_console($perso,$message_perso);
			$persoManager->deleteobjet($idobjetunique);
			echo 'Success';
		break;
		default:
		 echo 'Success';
	}
}
elseif (isset($_POST['get']) AND $_POST['get']=='msg')
{
	if ($messages = $persoManager->recup_console($perso))
		{
			foreach ($messages as $message)
			{
				if ($message['id_perso']==0)
				{
					?><p style="color:rgb(34,177,76);"><?php echo $persoManager->formaterdate($message['date_message']); ?> : <?php echo $message['message']; ?></p><?php
				}
				else
				{
					?><p><?php echo $persoManager->formaterdate($message['date_message']); ?> : <?php echo $message['message']; ?></p><?php
				}
			}
		}
		else
		{
			echo '<p>Aucun message</p>';
		}
}
elseif (isset($_POST['get']) AND $_POST['get']=='statut')
{
	if ($perso->getEtat()=='alive' )
	{
		$force_perso = $perso->getNiveau()*10 - floor( ($perso->getDegats())/10 );
		echo '<div>Nom : '.$perso->getNom().'</div><div>Position : '.$perso->getLocalisationX().','.$perso->getLocalisationY().'</div>';
		echo '<div class="infosname">Niveau</div><div class="barreprogression"><div style="width:'.$perso->getNiveau().'0%">'.$perso->getNiveau().'/10</div></div>';
		echo '<div class="infosname">Exp&eacute;rience</div><div class="barreprogression"><div style="width:'.$perso->getExperience().'%">'.$perso->getExperience().'/100</div></div>';
		echo '<div class="infosname">D&eacute;g&acirc;ts</div><div class="barreprogression barredegats"><div style="width:'.$perso->getDegats().'%">'.$perso->getDegats().'/100';
		echo '</div></div>';
		echo '<div class="infosname">Force</div><div class="barreprogression barreforce"><div style="width:'.$force_perso.'%">';
		echo ''.$force_perso.'/100</div></div>';
		switch($perso->getTypeperso())
		{
			case 0:
			echo ' ';
			break;
			case 1 :
			echo '<div class="infosname">Protection</div><div class="barreprogression"><div style="width:'.$perso->getSpecial().'%">'.$perso->getSpecial().'/100</div></div>';
			break;
			case 2 :
			echo '<div class="infosname">Magie</div><div class="barreprogression"><div style="width:'.$perso->getSpecial().'%">'.$perso->getSpecial().'/100</div></div>';
			break;
			default:
			echo ' ';
		}
	}
	elseif(preg_match('#sleep#',$perso->getEtat()))
	{
		$fin = explode(';',$perso->getEtat())[1];
		$wait = $fin - time();
		$force_perso = $perso->getNiveau()*10 - floor( ($perso->getDegats())/10 );
		echo '<div>Nom : '.$perso->getNom().'</div><div>Position : '.$perso->getLocalisationX().','.$perso->getLocalisationY().'</div>';
		echo '<div class="infosname">Niveau</div><div class="barreprogression"><div style="width:'.$perso->getNiveau().'0%">'.$perso->getNiveau().'/10</div></div>';
		echo '<div class="infosname">Exp&eacute;rience</div><div class="barreprogression"><div style="width:'.$perso->getExperience().'%">'.$perso->getExperience().'/100</div></div>';
		echo '<div class="infosname">D&eacute;g&acirc;ts</div><div class="barreprogression barredegats"><div style="width:'.$perso->getDegats().'%">'.$perso->getDegats().'/100';
		echo '</div></div>';
		echo '<div class="infosname">Force</div><div class="barreprogression barreforce"><div style="width:'.$force_perso.'%">';
		echo ''.$force_perso.'/100</div></div>';
		switch($perso->getTypeperso())
		{
			case 0:
			echo ' ';
			break;
			case 1 :
			echo '<div class="infosname">Protection</div><div class="barreprogression"><div style="width:'.$perso->getSpecial().'%">'.$perso->getSpecial().'/100</div></div>';
			break;
			case 2 :
			echo '<div class="infosname">Magie</div><div class="barreprogression"><div style="width:'.$perso->getSpecial().'%">'.$perso->getSpecial().'/100</div></div>';
			break;
			default:
			echo ' ';
		}
		echo '<div>Vous avez &eacute;t&eacute; endormi !</div>';
		echo '<div>Vous devez attendre '.$wait.' secondes avant de pouvoir bouger!</div>';
	}
	elseif ($perso->getEtat()=='dead' )
	{
		echo '<div>Vous &ecirc;tes mort ! </div><div><a href="index.php?action=game&restart=ok">Cliquez ICI pour rena&icirc;tre </a></div>';
	}
	else
	{
		echo '<div>Erreur</div>';
	}
}
