<?php
Class Perso
{
	private $_id;
	private $_nom;
	private $_password;
	private $_etat;
	private $_localisation;
	private $_localisation_x;
	private $_localisation_y;
	private $_direction;
	private $_niveau;
	private $_experience;
	private $_degats;
	private $_typeperso;
	private $_special;
	private $_inventaire;
	
	public function __construct(array $donnees)
	{
		$this->hydrate($donnees);
	}
	
	public function hydrate(array $donnees)
	{
		foreach($donnees as $key => $value)
		{
			$method = 'set'.ucfirst($key);
			if($key == 'localisation_x'){
			  $method = 'setLocalisationX';
      }
      if($key == 'localisation_y'){
        $method = 'setLocalisationY';
      }
			if (method_exists($this,$method))
			{
				$this->$method($value);
			}
		}
	}
  
  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->_id;
  }
  
  /**
   * @param mixed $id
   * @return Perso
   */
  public function setId($id)
  {
    $this->_id = $id;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getNom()
  {
    return $this->_nom;
  }
  
  /**
   * @param mixed $nom
   * @return Perso
   */
  public function setNom($nom)
  {
    $this->_nom = $nom;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getPassword()
  {
    return $this->_password;
  }
  
  /**
   * @param mixed $password
   * @return Perso
   */
  public function setPassword($password)
  {
    $this->_password = $password;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getEtat()
  {
    return $this->_etat;
  }
  
  /**
   * @param mixed $etat
   * @return Perso
   */
  public function setEtat($etat)
  {
    $this->_etat = $etat;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getLocalisation()
  {
    return $this->_localisation;
  }
  
  /**
   * @param mixed $localisation
   * @return Perso
   */
  public function setLocalisation($localisation)
  {
    $this->_localisation = $localisation;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getLocalisationX()
  {
    return $this->_localisation_x;
  }
  
  /**
   * @param mixed $localisation_x
   * @return Perso
   */
  public function setLocalisationX($localisation_x)
  {
    $this->_localisation_x = $localisation_x;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getLocalisationY()
  {
    return $this->_localisation_y;
  }
  
  /**
   * @param mixed $localisation_y
   * @return Perso
   */
  public function setLocalisationY($localisation_y)
  {
    $this->_localisation_y = $localisation_y;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getDirection()
  {
    return $this->_direction;
  }
  
  /**
   * @param mixed $direction
   * @return Perso
   */
  public function setDirection($direction)
  {
    $this->_direction = $direction;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getNiveau()
  {
    return $this->_niveau;
  }
  
  /**
   * @param mixed $niveau
   * @return Perso
   */
  public function setNiveau($niveau)
  {
    $this->_niveau = $niveau;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getExperience()
  {
    return $this->_experience;
  }
  
  /**
   * @param mixed $experience
   * @return Perso
   */
  public function setExperience($experience)
  {
    $this->_experience = $experience;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getDegats()
  {
    return $this->_degats;
  }
  
  /**
   * @param mixed $degats
   * @return Perso
   */
  public function setDegats($degats)
  {
    $this->_degats = $degats;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getTypeperso()
  {
    return $this->_typeperso;
  }
  
  /**
   * @param mixed $typeperso
   * @return Perso
   */
  public function setTypeperso($typeperso)
  {
    $this->_typeperso = $typeperso;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getSpecial()
  {
    return $this->_special;
  }
  
  /**
   * @param mixed $special
   * @return Perso
   */
  public function setSpecial($special)
  {
    $this->_special = $special;
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getInventaire()
  {
    return $this->_inventaire;
  }
  
  /**
   * @param mixed $inventaire
   * @return Perso
   */
  public function setInventaire($inventaire)
  {
    $this->_inventaire = $inventaire;
    return $this;
  }
	
	
	
	// Autres Fonctions
	public function frapper(Perso $cible)
	{
		// on verif que l'adversaire soit a cote
		if ($this->getDirection()=='haut' AND ($cible->getLocalisationX() == $this->getLocalisationX() ) AND ($cible->getLocalisationY() == $this->getLocalisationY() - 1 ) )
		{
			$justeacote = TRUE ;
		}
		elseif ($this->getDirection()=='bas' AND ($cible->getLocalisationX() == $this->getLocalisationX() ) AND ($cible->getLocalisationY() == $this->getLocalisationY() + 1 ) )
		{
			$justeacote = TRUE ;
		}
		elseif ($this->getDirection()=='gauche' AND ($cible->getLocalisationX() == $this->getLocalisationX() - 1) AND ($cible->getLocalisationY() == $this->getLocalisationY() ) )
		{
			$justeacote = TRUE ;
		}
		elseif ($this->getDirection()=='droite' AND ($cible->getLocalisationX() == $this->getLocalisationX() + 1) AND ($cible->getLocalisationY() == $this->getLocalisationY() ) )
		{
			$justeacote = TRUE ;
		}
		else
		{
			$justeacote = FALSE ;
		}
		
		if ($justeacote AND $thisIdEtat()!=$cibleIdEtat())
		{
			// on frappe
			$force = $this->getNiveau()*10 - floor( ($this->getDegats())/10 );
			$degats = ($force < 0 ) ? 0 : $force;
			return $cible->recevoirdegats($degats);
		}
		elseif ($thisIdEtat()==$cibleIdEtat())
		{
			// le perso se frappe lui-meme
			return 5;
		}
		else
		{
			// impossible de frapper ce perso
			return 5;
		}
		
	}
	
	public function gagnerexperience()
	{
		$this->setExperience($this->getExperience() + 1 );
		if ($this->getExperience()>99  AND $this->getNiveau() < 9 )
		{
			$this->setNiveau($this->getNiveau()+1);
			$this->setExperience(0);
			return 1;
		}
		elseif ($this->getExperience()>99  AND $this->getNiveau() == 9 )
		{
			$this->setNiveau(10);
			$this->setExperience(100);
			return 1;
		}
		elseif ($this->getExperience()>99 AND $this->getNiveau()> 9 )
		{
			$this->setNiveau(10);
			$this->setExperience(100);
			return 0;
		}
		
	}
	
	public function recevoirdegats($force)
	{
		$force = (int) $force ;
		$coef = (1/10) * (11 - $this->getNiveau());
		$degats = floor( $force * $coef ) ;
		$this->setDegats($this->getDegats() + $degats);
		if ($this->getDegats()>99 )
		{
			$this->setEtat('dead');
			return 2;
		}
		elseif ($degats > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	public function fairedodo($duree)
	{
		if ($this->getEtat()=='alive')
		{
			$fin = time() + $duree;
			$this->setEtat('sleep;'.$fin);
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	public function wakeup()
	{
		if (preg_match('#sleep#',$this->getEtat()))
		{
			$this->setEtat('alive');
		}
	}
	
	public function recuperer()
	{
		if ( $this->getDegats() > 0 )
		{
			$this->setDegats(0);
			return 0;
		}
		else
		{
			return 1;
		}
	}
	
	public function ouvrircoffre()
	{
		return 0;
	}
}
