<?php
Class Guerrier extends Perso
{
	
	// Autres Fonctions
	
	public function recevoirdegats($force)
	{
		$force = (int) $force ;
		$coef = (1/10) * (11 - $this->niveau());
		$coefprotection = 1 - ($this->special()/200);
		$degats = floor( $force * $coef * $coefprotection ) ;
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
?>