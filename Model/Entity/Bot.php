<?php
Class Bot extends Perso
{
	
	public function gagnerexperience()
	{
		return 0;	
	}
	
	public function ouvrircoffre()
	{
		return 0;
	}
	
	public function recevoirdegats($force)
	{
		if ($this->typeperso()==10 )
		{
			return 3;
		}
		else
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
	}
}
?>