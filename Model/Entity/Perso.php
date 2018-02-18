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
			if (method_exists($this,$method))
			{
				$this->$method($value);
			}
		}
	}
	
	// setteurs
	public function setId ($id)
	{
		$this->_id = (int) $id;
	}
	
	public function setNom($nom)
	{
		$this->_nom = htmlspecialchars($nom);
	}
	
	public function setPassword($password)
	{
		$this->_password = $password;
	}
	
	public function setEtat($etat)
	{
		$this->_etat = $etat;
	}
	
	public function setLocalisation($localisation)
	{
		$this->_localisation = $localisation;
	}
	
	public function setLocalisation_x($localisation_x)
	{
		$this->_localisation_x = $localisation_x;
	}
	
	public function setLocalisation_y($localisation_y)
	{
		$this->_localisation_y = $localisation_y;
	}
	
	public function setDirection($direction)
	{
		switch($direction)
		{
			case "haut" :
				$this->_direction = 'haut';
				break;
			case "gauche" :
				$this->_direction = 'gauche';
				break;
			case "droite" :
				$this->_direction = 'droite';
				break;
			case "bas" : 
				$this->_direction = 'bas';
				break;
			default : 
				$this->_direction = 'bas';
		}
	}
	
	public function setNiveau($niveau)
	{
		$this->_niveau = (int) $niveau;
	}
	
	public function setExperience($experience)
	{
		$this->_experience = (int) $experience;
	}
	
	public function setDegats($degats)
	{
		$this->_degats = (int) $degats;
	}
	
	public function setTypeperso($typeperso)
	{
		$this->_typeperso = (int) $typeperso;
	}
	public function setSpecial($special)
	{
		$this->_special = (int) $special;
	}
	public function setInventaire($inventaire)
	{
		$this->_inventaire = $inventaire;
	}
	
	//getteurs
	public function id() {return $this->_id ;}
	public function nom() {return $this->_nom;}
	public function password() {return $this->_password;}
	public function etat() {return $this->_etat; }
	public function localisation() {return $this->_localisation; }
	public function localisation_x() {return $this->_localisation_x; }
	public function localisation_y() {return $this->_localisation_y; }
	public function direction() {return $this->_direction; }
	public function niveau() {return $this->_niveau; }
	public function experience() {return $this->_experience; }
	public function degats() {return $this->_degats; }
	public function typeperso() {return $this->_typeperso; }
	public function special() {return $this->_special; }
	public function inventaire() {return $this->_inventaire; }
	
	// Autres Fonctions
	public function frapper(Perso $cible)
	{
		// on verif que l'adversaire soit a cote
		if ($this->direction()=='haut' AND ($cible->localisation_x() == $this->localisation_x() ) AND ($cible->localisation_y() == $this->localisation_y() - 1 ) )
		{
			$justeacote = TRUE ;
		}
		elseif ($this->direction()=='bas' AND ($cible->localisation_x() == $this->localisation_x() ) AND ($cible->localisation_y() == $this->localisation_y() + 1 ) )
		{
			$justeacote = TRUE ;
		}
		elseif ($this->direction()=='gauche' AND ($cible->localisation_x() == $this->localisation_x() - 1) AND ($cible->localisation_y() == $this->localisation_y() ) )
		{
			$justeacote = TRUE ;
		}
		elseif ($this->direction()=='droite' AND ($cible->localisation_x() == $this->localisation_x() + 1) AND ($cible->localisation_y() == $this->localisation_y() ) )
		{
			$justeacote = TRUE ;
		}
		else
		{
			$justeacote = FALSE ;
		}
		
		if ($justeacote AND $this->id()!=$cible->id())
		{
			// on frappe
			$force = $this->niveau()*10 - floor( ($this->degats())/10 );
			$degats = ($force < 0 ) ? 0 : $force;
			return $cible->recevoirdegats($degats);
		}
		elseif ($this->id()==$cible->id())
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
		$this->setExperience($this->experience() + 1 );
		if ($this->experience()>99  AND $this->niveau() < 9 )
		{
			$this->setNiveau($this->niveau()+1);
			$this->setExperience(0);
			return 1;
		}
		elseif ($this->experience()>99  AND $this->niveau() == 9 )
		{
			$this->setNiveau(10);
			$this->setExperience(100);
			return 1;
		}
		elseif ($this->experience()>99 AND $this->niveau()> 9 )
		{
			$this->setNiveau(10);
			$this->setExperience(100);
			return 0;
		}
		
	}
	
	public function recevoirdegats($force)
	{
		$force = (int) $force ;
		$coef = (1/10) * (11 - $this->niveau());
		$degats = floor( $force * $coef ) ;
		$this->setDegats($this->degats() + $degats);
		if ($this->degats()>99 )
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
		if ($this->etat()=='alive')
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
		if (preg_match('#sleep#',$this->etat()))
		{
			$this->setEtat('alive');
		}
	}
	
	public function recuperer()
	{
		if ( $this->degats() > 0 )
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
