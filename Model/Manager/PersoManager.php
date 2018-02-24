<?php
Class PersoManager extends DatabaseManager
{
	private $_bdd;
	private $dateManager;
	private $carteManager;
	
	public function __construct()
	{
		$this->_bdd = parent::getBDD();
		$this->dateManager = new DateManager();
		$this->carteManager = new MapManager();
	}

  /*----------*/
  /*	Date	  */
  /*----------*/

  public function formaterdate($dateaformater)
  {
    return $this->dateManager->formaterdate($dateaformater);
  }

	
	/*----------*/
	/*	Perso	*/
	/*----------*/
	public function add(Perso $perso)
	{
		$sql = $this->_bdd->prepare('INSERT INTO perso (nom,password,etat,localisation,localisation_x,localisation_y, direction, niveau, experience, degats,typeperso,special) 
			VALUES (:nom, :password, :etat,:localisation,:localisation_x,:localisation_y, :dir, :niveau, :experience, :degats,:typeperso,:special) ');
		$sql->bindValue(':nom', $perso->getNom() );
		$sql->bindValue(':password', $perso->getPassword() );
		$sql->bindValue(':etat', $perso->getEtat() );
		$sql->bindValue(':localisation', $perso->getLocalisation() );
		$sql->bindValue(':localisation_x', $perso->getLocalisationX() );
		$sql->bindValue(':localisation_y', $perso->getLocalisationY() );
		$sql->bindValue(':dir', $perso->getDirection() );
		$sql->bindValue(':niveau', $perso->getNiveau() );
		$sql->bindValue(':experience', $perso->getExperience() );
		$sql->bindValue(':degats', $perso->getDegats() );
		$sql->bindValue(':typeperso', $perso->getTypeperso() );
		$sql->bindValue(':special', $perso->getSpecial() );
		$sql->execute();
		
		$perso->hydrate(['id' => $this->_bdd->lastInsertId()]);
		
		return $sql->errorInfo();
	}
	
	public function update(Perso $perso)
	{
		$sql = $this->_bdd->prepare('
			UPDATE perso SET nom = :nom, password = :password, etat = :etat, localisation = :localisation, localisation_x = :localisation_x, localisation_y = :localisation_y, 
			direction = :dir, niveau = :niveau, experience = :experience, degats = :degats , typeperso = :typeperso, special = :special
			WHERE id = :id');
		$sql->bindValue(':id', $perso->getId() );
		$sql->bindValue(':nom', $perso->getNom() );
		$sql->bindValue(':password', $perso->getPassword() );
		$sql->bindValue(':etat', $perso->getEtat() );
		$sql->bindValue(':localisation', $perso->getLocalisation() );
		$sql->bindValue(':localisation_x', $perso->getLocalisationX() );
		$sql->bindValue(':localisation_y', $perso->getLocalisationY() );
		$sql->bindValue(':dir', $perso->getDirection() );
		$sql->bindValue(':niveau', $perso->getNiveau() );
		$sql->bindValue(':experience', $perso->getExperience() );
		$sql->bindValue(':degats', $perso->getDegats() );
		$sql->bindValue(':typeperso', $perso->getTypeperso() );
		$sql->bindValue(':special', $perso->getSpecial() );
		$sql->execute();
	}
	
	public function delete(Perso $perso)
	{
		$sql = $this->_bdd->prepare('DELETE FROM perso WHERE id = :id');
		$sql->bindValue(':id', $perso->getId());
		$sql->execute();
	}
	
	public function get($info)
	{
		if (is_int($info))
		{
			$sql = $this->_bdd->prepare('SELECT * FROM perso WHERE id = :info');
			$sql->bindValue(':info', $info);
			$sql->execute();
		}
		else
		{
			$sql = $this->_bdd->prepare('SELECT * FROM perso WHERE nom = :info');
			$sql->bindValue(':info', $info);
			$sql->execute();
		}

		if ($donnees = $sql->fetch() )
		{
			switch($donnees['typeperso'])
			{
				case 0 :
				  $perso = new Perso($donnees);
				break;
				case 1 :
				  $perso = new Guerrier($donnees);
				break;
				case 2 :
          $perso = new Magicien($donnees);
				break;
				default:
          $perso = new Perso($donnees);
			}
		}
		else
		{
			return FALSE;
		}
		$perso->setInventaire($this->getinventaire($perso));
		return $perso;
	}
	
	public function getpersoscarte($localisation,$carte_x,$carte_y)
	{
		$recup_sql = $this->_bdd->prepare('SELECT * FROM perso WHERE etat != :etat 
			AND localisation = :localisation AND localisation_x BETWEEN :startx AND :endx AND localisation_y BETWEEN :starty AND :endy ');
		$recup_sql->bindValue(':etat','dead');
		$recup_sql->bindValue(':localisation',$localisation);
		$recup_sql->bindValue(':startx',$carte_x*10);
		$recup_sql->bindValue(':endx',$carte_x*10 + 9);
		$recup_sql->bindValue(':starty',$carte_y*10);
		$recup_sql->bindValue(':endy',$carte_y*10 + 9);
		$recup_sql->execute();

		return $recup_sql->fetchAll();
	}

	public function getPersoCarte($localisation,$x,$y)
  {
    $get_sql = $this->_bdd->prepare('SELECT * FROM perso WHERE etat != :etat AND localisation = :localisation AND localisation_x = :x AND localisation_y = :y');
    $get_sql->bindValue(':etat', 'dead');
    $get_sql->bindValue(':localisation',$localisation);
    $get_sql->bindValue(':x', $x);
    $get_sql->bindValue(':y', $y);
    $get_sql->execute();
  }
	// recuperer le joueur en face si il existe
	public function getadversaire(Perso $perso)
	{
		$direction_perso = $perso->getDirection();
		$localisation = $perso->getLocalisation();
		switch($direction_perso)
		{
			case "haut" :
				$x = $perso->getLocalisationX();
				$y = $perso->getLocalisationY() - 1 ;
				break;
			case "gauche" :
				$x = $perso->getLocalisationX() - 1;
				$y = $perso->getLocalisationY();
				break;
			case "droite" :
				$x = $perso->getLocalisationX() + 1 ;
				$y = $perso->getLocalisationY();
				break;
			case "bas" : 
				$y = $perso->getLocalisationX() + 1 ;
				$x = $perso->getLocalisationY();
				break;
			default : 
				$x = 0;
				$y = 0;
		}
		
		$get_sql = $this->_bdd->prepare('SELECT * FROM perso WHERE etat != :etat AND localisation = :localisation AND localisation_x = :x AND localisation_y = :y');
		$get_sql->bindValue(':etat', 'dead');
		$get_sql->bindValue(':localisation',$localisation);
		$get_sql->bindValue(':x', $x);
		$get_sql->bindValue(':y', $y);
		$get_sql->execute();
		
		if ($donnees = $get_sql->fetch() )
		{
			switch($donnees['typeperso'])
			{
				case 0 :
				return new Perso($donnees);
				break;
				case 1 :
				return new Guerrier($donnees);
				break;
				case 2 :
				return new Magicien($donnees);
				break;
				case 10 :
				return new Bot($donnees);
				break;
				default:
				return new Perso($donnees);	
			}
		}
		else
		{
			return FALSE;
		}
	}
	
	// determiner si nouvelle place prise
	public function placeprise($localisation = 'C',$x, $y)
	{
		$coord = ''.$x.','.$y.'';
		
		$sql = $this->_bdd->prepare('SELECT id FROM perso WHERE etat != :etat AND localisation = :localisation AND localisation_x = :x AND localisation_y = :y');
		$sql->bindValue(':etat', 'dead');
		$sql->bindValue(':localisation',$localisation);
		$sql->bindValue(':x', $x);
		$sql->bindValue(':y', $y);
		$sql->execute();
		if ($sql->fetchAll())
		{
			return TRUE;
		}
		elseif ($localisation=='C')
		{
			// si decor present
			$tab_places_prises = ['8,1','8,2','53,36','56,36','22,90','25,90','22,89','25,89','24,53','26,53','25,54','24,55','26,55','44,22','44,23','42,15'];
			$tab_places_prises_2 = ['77,43','78,43','79,43','77,51','78,51','79,51','33,11','34,11','33,12','33,13'];
			$tab_places_prises_3 = ['18,3','12,15','11,21','31,47'];//roche
			if (($x == 9 OR $x == 10) AND ($y < 34 OR ($y > 34 AND $y < 92))) //colonne arbre
			{
				return TRUE ;
			}
			elseif (($x == 69 OR $x == 70) AND ($y > 34 AND $y < 92))//colonne arbre
			{
				return TRUE;
			}
			elseif ( (($x > 10 AND $x < 54) OR ($x > 55 AND $x < 69)) AND ($y == 35) ) // ligne arbre
			{
				return TRUE;
			}
			elseif ( (($x > 10 AND $x < 23) OR ($x > 24 AND $x < 69)) AND ($y == 91) ) // ligne arbre
			{
				return TRUE;
			}
			elseif ( ($x > 34 AND $x < 55) AND ($y > 54 AND $y < 75) ) // lac
			{
				return TRUE;
			}
			elseif ( ($x > 54 AND $x < 64) AND ($y==68) ) // riviere - 1
			{
				return TRUE;
			}
			elseif ( ($x==63) AND ($y > 62 AND $y < 68 ) ) // riviere - 2
			{
				return TRUE;
			}
			elseif ( ($x==63) AND ($y > 35 AND $y < 62 ) ) // riviere - 3
			{
				return TRUE;
			}
			elseif ( ($x==63) AND ($y > 23 AND $y < 34 ) ) // riviere - 3
			{
				return TRUE;
			}
			elseif ( ($x==41) AND ($y > 74 AND $y < 82 ) ) // 2eme riviere part1
			{
				return TRUE;
			}
			elseif ( ($x==41) AND ($y > 82 AND $y < 92 ) ) // 2eme riviere part2
			{
				return TRUE;
			}
			elseif ( ($x==41) AND ($y > 92) ) // 2eme riviere part3
			{
				return TRUE;
			}
			elseif ( ($x==55 OR $x==74) AND ($y > 14 AND $y < 25 ) ) // falaise - 1
			{
				return TRUE;
			}
			elseif ( ($x > 54 AND $x < 75 ) AND ($y==15) ) // falaise - 2
			{
				return TRUE;
			}
			elseif ( (($x > 54 AND $x < 65 ) OR ($x > 65 AND $x < 75 )) AND ($y==24) ) // falaise - 3
			{
				return TRUE;
			}
			elseif ( ($x==24) AND ($y > 4 AND $y < 25 ) ) // falaise - 1
			{
				return TRUE;
			}
			elseif ( ($x==35) AND (($y > 4 AND $y < 12 ) OR ($y > 12 AND $y < 25 )) ) // falaise - 2
			{
				return TRUE;
			}
			elseif ( ( ($x > 23 AND $x < 36 ) AND ($y==5 OR $y==24) ) ) // falaise - 3
			{
				return TRUE;
			}
			elseif ( ($x > 10 AND $x < 18 ) AND ($y > 29 AND $y < 33) ) // maison - 1
			{
				return TRUE;
			}
			elseif ( (($x > 10 AND $x < 13 ) OR ($x > 13 AND $x < 18 )) AND ($y==33) ) // maison - 2
			{
				return TRUE;
			}
			elseif ( ($x > 5 AND $x < 10 ) AND ($y > 40 AND $y < 46) ) // 2eme maison
			{
				return TRUE;
			}
			elseif ( ($x > 85 AND $x < 89 ) AND ($y > 33 AND $y < 36) ) // fontaine
			{
				return TRUE;
			}
			elseif ( ($x > 75) AND ($y == 14 OR $y == 55) ) //contour village - 1
			{
				return TRUE;
			}
			elseif ( ($x == 76) AND (($y > 14 AND $y < 34) OR ($y > 34 AND $y < 55)) ) //contour village - 2
			{
				return TRUE;
			}
			elseif ( ($x > 77 AND $x < 86 ) AND ($y > 21 AND $y < 27) ) //maison village
			{
				return TRUE;
			}
			elseif ((($x > 79 AND $x < 89 ) AND ($y > 42 AND $y < 51)) OR ((($x > 79 AND $x < 82 ) OR ($x > 82 AND $x < 86 ) OR ($x > 86 AND $x < 89 )) AND ($y == 51))) //immeuble
			{
				return TRUE;
			}
			elseif ( ($x > 90) AND ($y > 15 AND $y < 21) ) //maison village - 1
			{
				return TRUE;
			}
			elseif ( ($x > 90) AND ($y > 21 AND $y < 27) ) //maison village - 2
			{
				return TRUE;
			}
			elseif ( ($x > 90) AND ($y > 27 AND $y < 33) ) //maison village - 3
			{
				return TRUE;
			}
			elseif ( ($x > 90) AND ($y > 33 AND $y < 39) ) //maison village - 4
			{
				return TRUE;
			}
			elseif ( ($x > 90) AND ($y > 40 AND $y < 46) ) //maison village - 5
			{
				return TRUE;
			}
			elseif ( ($x > 90) AND ($y > 46 AND $y < 52) ) //maison village - 6
			{
				return TRUE;
			}
			elseif (in_array($coord,$tab_places_prises))
			{
				return TRUE;
			}
			elseif (in_array($coord,$tab_places_prises_2))
			{
				return TRUE;
			}
			elseif (in_array($coord,$tab_places_prises_3))
			{
				return TRUE;
			}
			else
			{	
				return FALSE;
			}
		}
		elseif ($localisation=='M')
		{
			$tab_places_prises = ['1,1','1,2'];
			if ($x < 1 OR $y < 1 OR $x > 8 OR $y > 10) ///bordure maison
			{
				return TRUE;
			}
			elseif ( $x!=3 AND ($y==9 OR $y==10))///bordure maison - suite
			{
				return TRUE;
			}
			elseif (in_array($coord,$tab_places_prises))
			{
				return TRUE;
			}
			else
			{	
				return FALSE;
			}
		}
		elseif ($localisation=='N')
		{
			$tab_places_prises = ['17,1'];
			if ($x < 1 OR $y < 0 OR $x > 18 OR $y > 38) //bordure
			{
				return TRUE;
			}
			if ( ($x!=3 AND $x!=4 AND $x!=13 AND $x!=14) AND ($y == 9)) //bordure - bas
			{
				return TRUE;
			}
			if ($y == 19  OR $y == 29) //bordure - bas
			{
				return TRUE;
			}
			elseif ( ($x!=15 AND $y!=17) AND ($y==0 OR $y==10 OR $y==20 OR $y==30))//bordure sauf escalier
			{
				return TRUE;
			}
			elseif ( $x==15 AND ($y==0 OR $y==30))// escalier manquants
			{
				return TRUE;
			}
			elseif (in_array($coord,$tab_places_prises))
			{
				return TRUE;
			}
			else
			{	
				return FALSE;
			}
		}
	}
	
	// se déplacer
	public function sedeplacer(Perso $perso,$direction)
	{
		$x = $perso->getLocalisationX();
		$y = $perso->getLocalisationY();
		switch($direction)
		{
			case "haut" :
				$new_x = $perso->getLocalisationX();
				$new_y = $perso->getLocalisationY() - 1 ;
				break;
			case "gauche" :
				$new_x = $perso->getLocalisationX() - 1;
				$new_y = $perso->getLocalisationY();
				break;
			case "droite" :
				$new_x = $perso->getLocalisationX() + 1 ;
				$new_y = $perso->getLocalisationY();
				break;
			case "bas" : 
				$new_x = $perso->getLocalisationX();
				$new_y = $perso->getLocalisationY() + 1;
				break;
			default : 
				$new_x = $perso->getLocalisationX();
				$new_y = $perso->getLocalisationY();
		}
		
		$localisation = $perso->getLocalisation();
		// verif des nouvelles coord
		if ($localisation=='C' AND ($new_x < 1 OR $new_y < 1 OR $new_x > 98 OR $new_y > 98))
		{
			return 0;
		}
		elseif ($localisation=='C' AND $new_x==13 AND $new_y==33)
		{
			$perso->hydrate([
				'localisation' => 'M',
				'localisation_x' => 3,
				'localisation_y' => 9
				]);
			return 2;
		}
		elseif ($localisation=='C' AND $new_x==82 AND $new_y==51)
		{
			$perso->hydrate([
				'localisation' => 'N',
				'localisation_x' => 4,
				'localisation_y' => 9
				]);
			return 2;
		}
		elseif ($localisation=='C' AND $new_x==86 AND $new_y==51)
		{
			$perso->hydrate([
				'localisation' => 'N',
				'localisation_x' => 14,
				'localisation_y' => 9
				]);
			return 2;
		}
		elseif ($localisation=='M' AND $new_x==3 AND $new_y==10)
		{
			$perso->hydrate([
				'localisation' => 'C',
				'localisation_x' => 13,
				'localisation_y' => 33
				]);
			return 2;
		}
		elseif ($localisation=='N' AND ($x==3 OR $x==4) AND $y==9 AND $direction=='bas')
		{
			$perso->hydrate([
				'localisation' => 'C',
				'localisation_x' => 82,
				'localisation_y' => 51
				]);
			return 2;
		}
		elseif ($localisation=='N' AND ($x==13 OR $x==14) AND $y==9 AND $direction=='bas')
		{
			$perso->hydrate([
				'localisation' => 'C',
				'localisation_x' => 86,
				'localisation_y' => 51
				]);
			return 2;
		}
		elseif ($localisation=='N' AND (($new_x==17 AND $new_y==0) OR ($new_x==15 AND $new_y==10) OR ($new_x==17 AND $new_y==20)))
		{
			$desty = $new_y + 10;
			$perso->hydrate([
				'direction' => 'bas',
				'localisation_x' => $new_x,
				'localisation_y' => $desty
				]);
			return 3;
		}
		elseif ($localisation=='N' AND ($new_x==17 AND $new_y==10) OR ($new_x==15 AND $new_y==20) OR ($new_x==17 AND $new_y==30))
		{
			$desty = $new_y - 10;
			$perso->hydrate([
				'direction' => 'bas',
				'localisation_x' => $new_x,
				'localisation_y' => $desty
				]);
			return 3;
		}
		elseif ($this->placeprise($localisation,$new_x,$new_y)) // sur decor ou autre perso
		{
			return 0;
		}
		else
		{
			$perso->hydrate([
				'localisation' => $localisation,
				'localisation_x' => $new_x,
				'localisation_y' => $new_y
				]);
			return 1;
		}
	}
	
	public function ispuit(Perso $perso)
	{
		switch($perso->getDirection())
		{
			case "haut" :
				if ($perso->getLocalisation()=='C' AND $perso->getLocalisationX()==25 AND $perso->getLocalisationY()==55 )
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
				break;
			case "gauche" :
				if ($perso->getLocalisation()=='C' AND $perso->getLocalisationX()==26 AND $perso->getLocalisationY()==54 )
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
				break;
			case "droite" :
				if ($perso->getLocalisation()=='C' AND $perso->getLocalisationX()==24 AND $perso->getLocalisationY()==54 )
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
				break;
			case "bas" : 
				if ($perso->getLocalisation()=='C' AND $perso->getLocalisationX()==25 AND $perso->getLocalisationY()==53 )
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
				break;
			default : 
				return FALSE;
		}
	}
	
	public function iscoffre(Perso $perso)
	{
		switch($perso->getDirection())
		{
			case "haut" :
				if ($perso->getLocalisation()=='C' AND $perso->getLocalisationX()==44 AND $perso->getLocalisationY()==24 )
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
				break;
			case "gauche" :
				if ($perso->getLocalisation()=='C' AND $perso->getLocalisationX()==45 AND $perso->getLocalisationY()==23 )
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
				break;
			case "droite" :
				if ($perso->getLocalisation()=='C' AND $perso->getLocalisationX()==43 AND $perso->getLocalisationY()==23 )
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
				break;
			case "bas" : 
				return FALSE;
				break;
			default : 
				return FALSE;
		}
	}
	
	public function issage(Perso $perso)
	{
		switch($perso->getDirection())
		{
			case "haut" :
				return FALSE;
				break;
			case "gauche" :
				if ($perso->getLocalisation()=='M' AND $perso->getLocalisationX()==2 AND $perso->getLocalisationY()==1 )//sorcier dans maison
				{
					if($perso->getNiveau()==1)
					{
						return 40;
					}
					elseif($perso->getTypeperso()==0)
					{
						return 41; //donne globe magique
					}
					elseif($perso->getTypeperso()==1)
					{
						return 42;
					}
					elseif($perso->getTypeperso()==2)
					{
						return 43;
					}
					else
					{
						return 40;
					}
				}
				else
				{
					return FALSE;
				}
				break;
			case "droite" :
				if ($perso->getLocalisation()=='C' AND $perso->getLocalisationX()==7 AND $perso->getLocalisationY()==1 ) // sage debut
				{
					if($perso->getNiveau()==1 AND $perso->getDegats()>0 )
					{
						return 10; //soigne
					}
					elseif($perso->getNiveau()==1 AND $perso->getDegats()==0 )
					{
						return 11; // pas de degats
					}
					else
					{
						return 12;
					}
				}
				else
				{
					return FALSE;
				}
				break;
			case "bas" : 
				return FALSE;
				break;
			default : 
				return FALSE;
		}
	}
	
	public function updatesleeppeople()
	{
		$sql = $this->_bdd->prepare('SELECT * FROM perso WHERE etat LIKE :sleep');
		$sql->bindValue(':sleep','%sleep%');
		$sql->execute();
		
		$persosendormis = $sql->fetchAll();
		
		foreach ($persosendormis as $persoendormi)
		{
			$dormeur = new Perso($persoendormi);
			$etat = $dormeur->getEtat();
			$tab_etat = explode(";",$etat);
			
			if (time() - $tab_etat[1] > 0)
			{
				$dormeur->wakeup();
				$this->update($dormeur);
			}
		}
	}
	
	////////////
	/* OBJETS */
	////////////
	
	public function getobjetsjoueur(Perso $perso)
	{
		$sql = $this->_bdd->prepare('SELECT inventaire.id as idobjetunique,idperso,idobjet,nom FROM inventaire JOIN objets ON idobjet=objets.id WHERE idperso = :idperso');
		$sql->bindValue(':idperso',$perso->getId());
		$sql->execute();
		
		return $sql->fetchAll();
	}
	
	public function getinventaire(Perso $perso)
	{
		$sql = $this->_bdd->prepare('SELECT idobjet FROM inventaire WHERE idperso = :idperso');
		$sql->bindValue(':idperso',$perso->getId());
		$sql->execute();
		
		$tab_inventaire = [];
		if ($recup_sql = $sql->fetch())
		{
			$tab_inventaire = [];
			do
			{
				$idobjet = $recup_sql['idobjet'];
				array_push($tab_inventaire,$idobjet);
			}
			while ($recup_sql = $sql->fetch());	
		}
		else
		{
			$tab_inventaire = FALSE;
		}
		return $tab_inventaire;
	}
	
	public function addobjet(Perso $perso,$idobjet)
	{
		$sql = $this->_bdd->prepare('INSERT INTO inventaire (idperso,idobjet) VALUES (:idperso,:idobjet)');
		$sql->bindValue(':idperso',$perso->getId());
		$sql->bindValue(':idobjet',$idobjet);
		$sql->execute();
	}
	
	public function getobjet(Perso $perso,$idobjetunique)
	{
		$sql = $this->_bdd->prepare('SELECT idperso,idobjet FROM inventaire WHERE id = :idobjetunique AND idperso = :idperso');
		$sql->bindValue(':idobjetunique',$idobjetunique);
		$sql->bindValue(':idperso',$perso->getId());
		$sql->execute();
		return $sql->fetch();
	}
	
	public function deleteobjet($idobjetunique)
	{
		$sql = $this->_bdd->prepare('DELETE FROM inventaire WHERE id = :idobjetunique');
		$sql->bindValue(':idobjetunique',$idobjetunique);
		$sql->execute();
	}
	
	public function isfullinventory(Perso $perso)
	{
		$sql = $this->_bdd->prepare('SELECT COUNT(idperso) as total FROM inventaire  WHERE idperso = :idperso');
		$sql->bindValue(':idperso',$perso->getId());
		$sql->execute();
		$retour = $sql->fetch();
		$total = $retour['total'];
		
		if ($total < 8)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function viderinventaire(Perso $perso)
	{
		$sql = $this->_bdd->prepare('DELETE FROM inventaire WHERE idperso = :idperso');
		$sql->bindValue(':idperso',$perso->getId());
		$sql->execute();
	}
	
	public function message_console(Perso $perso, $message)
	{
		$sql = $this->_bdd->prepare('INSERT INTO console (id_perso,message,date_message) VALUES (:id_perso,:message,NOW())');
		$sql->bindValue(':id_perso',$perso->getId());
		$sql->bindValue(':message',$message);
		$sql->execute();
	}
	
	public function recup_console(Perso $perso)
	{
		$sql = $this->_bdd->prepare('SELECT message,date_message,id_perso FROM console WHERE id_perso = :id_perso OR id_perso = 0 ORDER BY id DESC');
		$sql->bindValue(':id_perso',$perso->getId());
		$sql->execute();
		
		return $sql->fetchAll();
	}
	
	public function resetconsole(Perso $perso)
	{
		$sql = $this->_bdd->prepare('DELETE FROM console WHERE id_perso = :id_perso');
		$sql->bindValue(':id_perso',$perso->getId());
		$sql->execute();
	}
	
	public function insertvisite()
	{
		// inscription de la visite dans la bdd
		$page=$_SERVER['PHP_SELF'];
		$adresse_ip=$_SERVER['REMOTE_ADDR'];
		$navigateur=$_SERVER['HTTP_USER_AGENT'];
		$page_prec= (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'sans' ;
		
		$sql = $this->_bdd->prepare('SELECT * FROM visite WHERE adresse_ip = :adresse_ip AND (TIMESTAMPDIFF(hour,NOW() ,date_visite)=0 )');
		$sql->bindValue(':adresse_ip',$adresse_ip );
		$sql->execute();
		if (!$sql->fetch() )
		{
			$insert_visite = $this->_bdd->prepare('INSERT INTO visite (date_visite,page,pageprec,adresse_ip,navigateur) VALUES(NOW(),?,?,?,?)');
			$insert_visite->execute(array($page,$page_prec,$adresse_ip,$navigateur));
			$insert_visite->closeCursor();
		}
	}
	
	/////////
	/* BOT */
	/////////

	public function getbot($info)
	{
		if (is_int($info))
		{
			$sql = $this->_bdd->prepare('SELECT * FROM perso WHERE id = :id');
			$sql->bindValue(':id', $info);
			$sql->execute();
		}
		else
		{
			$sql = $this->_bdd->prepare('SELECT * FROM perso WHERE nom = :nom');
			$sql->bindValue(':nom', $info);
			$sql->execute();
		}

		if ($donnees = $sql->fetch() )
		{
			return new Bot($donnees);
		}
		else
		{
			return FALSE;
		}
	}
	
	public function getadversaireacote(Perso $perso)
	{
		$x = $perso->getLocalisationX();
		$y = $perso->getLocalisationY() - 1 ;
		$get_sql = $this->_bdd->prepare('SELECT * FROM perso WHERE etat != :etat AND localisation_x = :x AND localisation_y = :y');
		$get_sql->bindValue(':etat', 'dead');
		$get_sql->bindValue(':x', $x);
		$get_sql->bindValue(':y', $y);
		$get_sql->execute();
		
		if ($donnees = $get_sql->fetch())
		{
			return 1; //en haut
		}
		else
		{
			$x = $perso->getLocalisationX() + 1;
			$y = $perso->getLocalisationY();
			$get_sql = $this->_bdd->prepare('SELECT * FROM perso WHERE etat != :etat AND localisation_x = :x AND localisation_y = :y');
			$get_sql->bindValue(':etat', 'dead');
			$get_sql->bindValue(':x', $x);
			$get_sql->bindValue(':y', $y);
			$get_sql->execute();
			
			if ($donnees = $get_sql->fetch())
			{
				return 2; //a droite
			}
			else
			{
				$x = $perso->getLocalisationX();
				$y = $perso->getLocalisationY() + 1;
				$get_sql = $this->_bdd->prepare('SELECT * FROM perso WHERE etat != :etat AND localisation_x = :x AND localisation_y = :y');
				$get_sql->bindValue(':etat', 'dead');
				$get_sql->bindValue(':x', $x);
				$get_sql->bindValue(':y', $y);
				$get_sql->execute();
				
				if ($donnees = $get_sql->fetch())
				{
					return 3; //en bas
				}
				else
				{
					$x = $perso->getLocalisationX();
					$y = $perso->getLocalisationY() + 1;
					$get_sql = $this->_bdd->prepare('SELECT * FROM perso WHERE etat != :etat AND localisation_x = :x AND localisation_y = :y');
					$get_sql->bindValue(':etat', 'dead');
					$get_sql->bindValue(':x', $x);
					$get_sql->bindValue(':y', $y);
					$get_sql->execute();
					
					if ($donnees = $get_sql->fetch())
					{
						return 4; //en bas
					}
					else
					{
						return 0;
					}
				}
			}
		}
	}
	
}
