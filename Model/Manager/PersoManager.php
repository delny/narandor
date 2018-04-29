<?php
Class PersoManager extends DatabaseManager
{
	private $persoRepository;
	private $dateManager;
	private $mapManager;
	
	public function __construct()
	{
		$this->persoRepository = new PersoRepository();
		$this->dateManager = new DateManager();
		$this->mapManager = new MapManager();
	}

  /*----------*/
  /*	Date	  */
  /*----------*/

  public function formaterdate($dateaformater)
  {
    return $this->dateManager->formaterdate($dateaformater);
  }
  
  
  /**
   * Ajoute un joueur
   * @param Perso $perso
   * @return array
   */
	public function add(Perso $perso)
	{
		return $this->persoRepository->add($perso);
	}
  
  /**
   * Mets à jour un joueur
   * @param Perso $perso
   */
	public function update(Perso $perso)
	{
		return $this->persoRepository->update($perso);
	}
  
  /**
   * Supprime un joueur
   * @param Perso $perso
   */
	public function delete(Perso $perso)
	{
		return $this->persoRepository->delete($perso);
	}
  
  /**
   * @param $info
   * @return bool|Guerrier|Magicien|Perso
   */
	public function get($info)
	{
		if ($donnees = $this->persoRepository->get($info))
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
		$objectManager = new ObjectManager();
		$perso->setInventaire($objectManager->getinventaire($perso));
		return $perso;
	}
  
  /**
   * @param $localisation
   * @param $carte_x
   * @param $carte_y
   * @return array
   */
	public function getpersoscarte($localisation,$carte_x,$carte_y)
	{
		return $this->persoRepository->getpersoscarte($localisation,$carte_x,$carte_y);
	}
  
  /**
   * Récupère le joueur en face si il existe
   * @param Perso $perso
   * @return bool|Bot|Magicien|Perso
   */
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
		
		if ($donnees = $this->persoRepository->getPersoCarte($localisation,$x,$y) )
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
  
  /**
   * Détermine si nouvelle place prise
   * @param string $localisation
   * @param $x
   * @param $y
   * @return bool
   */
	public function placeprise($localisation = 'C',$x, $y)
	{
		if ($this->persoRepository->getPersoCarte($localisation,$x,$y))
		{
			return TRUE;
		}
		
		return $this->mapManager->placeprise($localisation,$x,$y);
	}
  
  /**
   * Déplace le joueur
   * @param Perso $perso
   * @param $direction
   * @return int
   */
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
  
  /**
   * @param Perso $perso
   * @return bool
   */
	public function ispuit(Perso $perso)
	{
	  if($perso->getLocalisation()!='C'){
	    return false;
    }
    
    if($perso->getDirection() == 'haut' && $perso->getLocalisationX()==25 && $perso->getLocalisationY()==55){
	    return true;
    }
    elseif ($perso->getDirection() == 'gauche' && $perso->getLocalisationX()==26 && $perso->getLocalisationY()==54){
	    return true;
    }
    elseif ($perso->getDirection() == 'droite' && $perso->getLocalisationX()==24 && $perso->getLocalisationY()==54){
	    return true;
    }
    elseif ($perso->getDirection() == 'bas' && $perso->getLocalisationX()==25 && $perso->getLocalisationY()==53){
	    return true;
    }
    
    return false;
	  
	}
  
  /**
   * @param Perso $perso
   * @return bool
   */
	public function iscoffre(Perso $perso)
	{
    if($perso->getLocalisation()!='C'){
      return false;
    }
    
    if($perso->getDirection() == 'haut' && $perso->getLocalisationX()==44 && $perso->getLocalisationY()==24){
      return true;
    }
    elseif ($perso->getDirection() == 'gauche' && $perso->getLocalisationX()==45 && $perso->getLocalisationY()==23){
      return true;
    }
    elseif ($perso->getDirection() == 'droite' && $perso->getLocalisationX()==43 && $perso->getLocalisationY()==23){
      return true;
    }
    
    return false;
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
  
  /**
   * Réveille les joueurs qui ont assez dormi
   */
	public function updatesleeppeople()
	{
		$persosendormis = $this->persoRepository->updateSleepPeople();
		
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
	
	public function updatemagiccount(Magicien $perso)
  {
    $regenerermagie = false;
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
          $regenerermagie = true;
        }
      }
      else
      {
        // on cree le fichier
        $new_fichier = fopen('tmp/magic'.$perso->getId().'.tmp','a+');
        fputs($new_fichier,time());
        fclose($new_fichier);
        $regenerermagie = true;
      }
    
      if ($regenerermagie)
      {
        $perso->regenerermagie();
        $this->update($perso);
      }
    }
  }
	
	/////////
	/* BOT */
	/////////
 
	public function getadversaireacote(Perso $perso)
	{
		$x = $perso->getLocalisationX();
		$y = $perso->getLocalisationY() - 1 ;
		
		if ($donnees = $this->persoRepository->getPersoCarte('C',$x,$y)) {
			return 1; //en haut
		} else {
			$x = $perso->getLocalisationX() + 1;
			$y = $perso->getLocalisationY();
			
			if ($donnees = $this->persoRepository->getPersoCarte('C',$x,$y)) {
				return 2; //a droite
			}
			else
			{
				$x = $perso->getLocalisationX();
				$y = $perso->getLocalisationY() + 1;
				
				if ($donnees = $this->persoRepository->getPersoCarte('C',$x,$y)) {
					return 3; //en bas
				}
				else
				{
					if ($donnees = $this->persoRepository->getPersoCarte('C',$x,$y)){
						return 4; //en bas
					}
					return 0;
					
				}
			}
		}
	}
	
}
