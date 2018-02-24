<?php

class ApiBotController
{
  //Constantes
  CONST COORD_X_BOT = [0,15,27,67,8,0,0,0,0,0,0,83,81];
  CONST COORD_Y_BOT = [0,15,27,67,8,0,0,0,0,0,0,83,81];
  
  CONST MIN_NIVEAU = [0,3,6,2,1];
  CONST MAX_NIVEAU = [0,7,9,3,2];
  
  CONST MIN_X_BOT = [0,11,25,64,1,0,0,0,0,0,0,78,77];
  CONST MAX_X_BOT = [0,34,34,68,8,0,0,0,0,0,0,88,87];
  
  CONST MIN_Y_BOT = [0,36,6,36,7,0,0,0,0,0,0,29,15];
  CONST MAX_Y_BOT = [0,76,23,56,27,0,0,0,0,0,0,38,21];
  //manager
  private $botManager;
  private $persoManager;
  
  public function __construct()
  {
    $this->botManager = new BotManager();
    $this->persoManager = new PersoManager();
  }
  
  /**
   * Gestion des bots
   * @param Perso $perso
   * @return array
   */
  public function refreshbots(Perso $perso){
    //gestion des bots agressifs
    for ($i=1;$i<5;$i++)
    {
      $this->refreshBot($i, true);
    }
    //gestion enfants
    for ($i=11;$i<13;$i++)
    {
      $this->refreshBot($i, false);
    }
    $data = ['retour' => 'success'];
    return $data;
  }
  
  /**
   * Gestion d'un bot selon numéro
   * @param $i
   * @param bool $actif
   */
  private function refreshBot($i,$actif = false){
    $bot = $this->getBot($i);
  
    // s'il est mort on le resucite
    if ($actif && $bot->getEtat()=='dead' )
    {
      $this->rebornBot($bot,$i);
    }
    elseif (preg_match('#sleep#',$bot->getEtat()))
    {
      // on attend qu'il se reveille
      echo 'Success';
    }
    elseif($actif && ($joueur = $this->persoManager->getadversaire($bot))) //il attaque
    {
      $this->botAttackPlayer($bot,$joueur);
    }
    elseif ($retour = $this->persoManager->getadversaireacote($bot)) //si quelqun a cote il se tourne vers lui
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
      $this->persoManager->update($bot);
    }
    else // si personne il se deplace
    {
      if ($bot->getLocalisationX()<=self::MIN_X_BOT[$i])
      {
        $direction = 'droite';
      }
      elseif ($bot->getLocalisationX()>=self::MAX_X_BOT[$i])
      {
        $direction = 'gauche';
      }
      elseif ($bot->getLocalisationY()<=self::MIN_Y_BOT[$i])
      {
        $direction = 'bas';
      }
      elseif ($bot->getLocalisationY()>=self::MAX_Y_BOT[$i])
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
        $this->persoManager->sedeplacer($bot,$direction);
      }
      else
      {
        $bot->hydrate(['direction' => $direction]);
      }
  
      $this->persoManager->update($bot);
    }
  }
  
  /**
   * Obtient/créer le bot suivant son numero
   * @param $i
   * @return bool|Bot
   */
  private function getBot($i){
    if (!$bot = $this->botManager->getbot('BOT'.$i)) // s'il n existe pas on le creer
    {
      $niveau = rand(self::MIN_NIVEAU[$i],self::MAX_NIVEAU[$i]);
      $bot = new Bot([
        'nom' => 'BOT'.$i,
        'password' => 'NULL',
        'etat' => 'alive',
        'localisation' => 'C',
        'localisation_x' => self::COORD_X_BOT[$i],
        'localisation_y' => self::COORD_Y_BOT[$i],
        'direction' => 'droite',
        'niveau' => $niveau,
        'experience' => 0,
        'degats' => 0,
        'typeperso' => 0,
        'special' => 0
      ]);
      $this->persoManager->add($bot);
    }
    
    return $bot;
  }
  
  /**
   * Récussite un bot
   * @param Bot $bot
   * @param $i
   */
  private function rebornBot(Bot $bot, $i){
    if (!$this->persoManager->getpersoscarte('C',floor(self::COORD_X_BOT[$i]/10),floor(self::COORD_Y_BOT[$i]/10)))
    {
      $niveau = rand(self::MIN_NIVEAU[$i],self::MAX_NIVEAU[$i]);
      $bot->hydrate([
        'etat' => 'alive',
        'localisation_x' => self::COORD_X_BOT[$i],
        'localisation_y' => self::COORD_Y_BOT[$i],
        'direction' => 'droite',
        'niveau' => $niveau,
        'experience' => 0,
        'degats' => 0
      ]);
      $this->persoManager->update($bot);
    }
  }
  
  /**
   * Un bot attaque un joueur
   * @param Bot $bot
   * @param Perso $player
   */
  private function botAttackPlayer(Bot $bot, Perso $player){
    if ($player->getEtat()=='alive')
    {
      $retour = $bot->frapper($player);
    }
    else
    {
      $retour = 4;
    }
    switch ($retour)
    {
      case 0 :
        $message_cible = $bot->getNom().' a tent&eacute; de vous frapper ... ';
        $this->persoManager->message_console($player,$message_cible);
        $this->persoManager->update($player);
        break;
      case 1 :
        $message_cible = $bot->getNom(). ' vous a frapp&eacute; !';
        $this->persoManager->message_console($player,$message_cible);
        $this->persoManager->update($player);
        break;
      case 2 :
        $message_cible = $bot->getNom().' vous a tu&eacute; !';
        $this->persoManager->message_console($player,$message_cible);
        $this->persoManager->update($player);
        break;
      default :
    }
  }
}
