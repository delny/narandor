<?php
Class Magicien extends Perso
{
	
	// Autres Fonctions
	public function endormir(Perso $cible)
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
		
		if ($this->special() > 30 )
		{
			if ($justeacote AND $this->id()!=$cible->id())
			{
				// on endort
				$this->Setspecial($this->special()-30);
				return $cible->fairedodo($this->niveau()*60);
			}
			elseif ($this->id()==$cible->id())
			{
				// le perso s endort lui-meme
				return 3;
			}
		}
		else
		{
			// impossible de endormir ce perso car pas assez energie
			return 2;
		}
		
	}
	
	public function regenerermagie()
	{	
		if ($this->special()<100)
		{
			$this->setspecial($this->special()+1);	
		}
		else
		{
			$this->setspecial(100);	
		}
	}
	
	public function ouvrircoffre()
	{
		$nombre = rand(1,12);
		if (in_array($nombre,$this->inventaire()))
		{
			return 0;
		}
		else
		{
			switch($nombre)
			{
				case 3 :
					return 3;
				break;
				default:
					return 0;
			}	
		}
	}
}
