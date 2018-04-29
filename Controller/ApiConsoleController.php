<?php

class ApiConsoleController
{
  private $messageManager;
  private $persoManager;
  
  public function __construct()
  {
    $this->messageManager = new MessageManager();
    $this->persoManager = new PersoManager();
  }
  
  public function postmsg(Perso $perso){
    $message = htmlspecialchars($_GET['message']);
    if (preg_match('#^/tp @me [0-9]{1,2} [0-9]{1,2}$#',$message))
    {
      $tab_message = explode(" ",$message);
      $localisation = $perso->getLocalisation();
      $coordx = $tab_message[2];
      $coordy = $tab_message[3];
      if ($this->persoManager->placeprise($localisation,$coordx,$coordy))
      {
        $message_perso = 'Impossible de vous t&eacute;l&eacute;port&eacute; &agrave; cet endroit';
        $this->messageManager->message_console($perso,$message_perso);
      }
      elseif ($perso->getLocalisation()!='C')
      {
        $message_perso = 'Impossible de vous t&eacute;l&eacute;port&eacute; depuis l\'int&eacute;rieur';
        $this->messageManager->message_console($perso,$message_perso);
      }
      else
      {
        $perso->hydrate([
          'localisation_x' => $coordx,
          'localisation_y' => $coordy
        ]);
        $this->persoManager->update($perso);
        $message_perso = 'Vous avez &eacute;t&eacute; t&eacute;l&eacute;port&eacute; en '.$coordx.','.$coordy.'';
        $this->messageManager->message_console($perso,$message_perso);
      }
      return ['retour' => 'success'];
    }
    elseif (preg_match('#^/effect recovered @me$#',$message))
    {
    
      $retour = $perso->recuperer();
      switch ($retour)
      {
        case 0 :
          $message_perso = 'Vous avez r&eacute;cuperer !';
          $this->messageManager->message_console($perso,$message_perso);
          $this->persoManager->update($perso);
          return ['retour' => 'guerison'];
          break;
        case 1 :
          $message_perso = 'Aucun d&eacute;g&acirc;ts &agrave; soigner!';
          $this->messageManager->message_console($perso,$message_perso);
          break;
        default :
          $message_perso = 'Erreur inconnue';
          $this->messageManager->message_console($perso,$message_perso);
      }
      return ['retour' => 'success'];
    }
    elseif (preg_match('#^/give @me more experience$#',$message))
    {
      $message_perso = 'Plus d\'exp&eacute;rience!';
      $this->messageManager->message_console($perso,$message_perso);
      if ($perso->gagnerexperience()==1 )
      {
        $message_perso = 'Bravo, vous passez au niveau '.$perso->getNiveau().'';
        $this->messageManager->message_console($perso,$message_perso);
      }
      $this->persoManager->update($perso);
      return ['retour' => 'success'];
    }
    elseif($message!='')
    {
      $public = new Perso([
        'id' => 0
      ]);
      $this->messageManager->message_console($public,$message);
      return ['retour' => 'success'];
    }
    return ['retour' => 'success'];
  }
}
