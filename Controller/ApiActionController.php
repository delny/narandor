<?php

class ApiActionController
{
  private $messageManager;
  private $objectManager;
  private $persoManager;
  
  public function __construct()
  {
    $this->messageManager = new MessageManager();
    $this->objectManager  = new ObjectManager();
    $this->persoManager   = new PersoManager();
  }
  
  /**
   * Porte d'entrée de ce sous-controller
   * @param Perso $perso
   * @return array
   */
  public function act(Perso $perso){
    if($perso->getEtat() !='alive'){
      return ['erreur' => 'il faut être vivant et réveillé pour agir' ];
    }
    //limitation actions
    $canAct = $this->actLimit($perso);
    
    if(!$canAct){
      return ['erreur' => 'limite actions atteinte' ];
    }
    
    if($cible = $this->persoManager->getadversaire($perso)){
      return $this->interactPlayer($perso,$cible);
    }
    
    if($this->persoManager->ispuit($perso)){
      return $this->well($perso);
    }
    
    if($this->persoManager->iscoffre($perso)){
      return $this->chest($perso);
    }
    
    if($this->persoManager->issage($perso)){
      return $this->oldSage($perso);
    }
    
    return ['erreur' => 'action inconnue'];
  }
  
  /**
   * Intéragir avec un autre joueur
   * @param Perso $perso
   * @param Perso $cible
   * @return array
   */
  private function interactPlayer(Perso $perso, Perso $cible){
    if ($cible->getEtat()!='dead' AND $_GET['act']=='hit')
    {
      return $this->hit($perso,$cible);
    }
    elseif ($_GET['act']=='sleep' AND $perso->getTypeperso()==2 )
    {
      return $this->sleep($perso,$cible);
    }
    
    return ['erreur' => 'action inconnue'];
  }
  
  /**
   * Touche entrée sur un joueur ou un bot - Frapper
   * @param Perso $perso
   * @param Perso $cible
   * @return array
   */
  private function hit(Perso $perso, Perso $cible){
    $retour = $perso->frapper($cible);
    switch ($retour)
    {
      case 0 :
        $message_perso = $cible->getNom().' n\'a rien senti !';
        $message_cible = $perso->getNom().' a tent&eacute; de vous frapper ... ';
        $this->messageManager->message_console($perso,$message_perso);
        $this->messageManager->message_console($cible,$message_cible);
        $this->persoManager->update($perso);
        $this->persoManager->update($cible);
        break;
      case 1 :
        $message_perso = 'Vous avez frapp&eacute; '.$cible->getNom();
        $message_cible = $perso->getNom(). ' vous a frapp&eacute; !';
        $this->messageManager->message_console($perso,$message_perso);
        $this->messageManager->message_console($cible,$message_cible);
        if ($perso->gagnerexperience()==1 )
        {
          $message_perso = 'Bravo, vous passez au niveau '.$perso->getNiveau();
          $this->messageManager->message_console($perso,$message_perso);
        }
        $this->persoManager->update($perso);
        $this->persoManager->update($cible);
        break;
      case 2 :
        $message_perso = 'Vous avez tu&eacute; '.$cible->getNom();
        $message_cible = $perso->getNom().' vous a tu&eacute; !';
        $this->messageManager->message_console($perso,$message_perso);
        $this->messageManager->message_console($cible,$message_cible);
        if ($perso->gagnerexperience()==1 )
        {
          $message_perso = 'Bravo, vous passez au niveau '.$perso->getNiveau();
          $this->messageManager->message_console($perso,$message_perso);
        }
        $this->persoManager->update($perso);
        $this->persoManager->update($cible);
        break;
      case 3 :
        $message_perso = 'Il est interdit de frapper les enfants!';
        $this->messageManager->message_console($perso,$message_perso);
        break;
      case 5 :
        $message_perso = 'Mais ... pourquoi voulez-vous vous frapper ?';
        $this->messageManager->message_console($perso,$message_perso);
        break;
      case 6 :
        $message_perso = 'Personne &agrave; frapper !';
        $this->messageManager->message_console($perso,$message_perso);
        break;
      default :
        $message_perso = 'Erreur inconnue';
        $this->messageManager->message_console($perso,$message_perso);
    }
    return ['retour' => 'success'];
  }
  
  /**
   * Touche s - Endormir si magicien
   * @param Magicien $perso
   * @param Perso $cible
   * @return array
   */
  private function sleep(Magicien $perso,Perso $cible){
    $retour = $perso->endormir($cible);
    switch ($retour)
    {
      case 0 :
        $message_perso = $cible->getNom().' est d&eacute;j&agrave; endormi !';
        $this->messageManager->message_console($perso,$message_perso);
        $this->persoManager->update($perso);
        break;
      case 1 :
        $message_perso = 'Vous avez endormi '.$cible->getNom();
        $message_cible = $perso->getNom(). ' vous a endormi !';
        $this->messageManager->message_console($perso,$message_perso);
        $this->messageManager->message_console($cible,$message_cible);
        $this->persoManager->update($perso);
        $this->persoManager->update($cible);
        break;
      case 2 :
        $message_perso = 'Vous n\'avez pas assez de magie pour endormir '.$cible->getNom();
        $this->messageManager->message_console($perso,$message_perso);
        $this->persoManager->update($perso);
        break;
      case 3 :
        $message_perso = 'Mais ... pourquoi voulez-vous vous endormir ?';
        $this->messageManager->message_console($perso,$message_perso);
        break;
      case 4 :
        $message_perso = 'Personne &agrave; endormir !';
        $this->messageManager->message_console($perso,$message_perso);
        break;
      default :
        $message_perso = 'Erreur inconnue';
        $this->messageManager->message_console($perso,$message_perso);
    }
    
  }
  
  /**
   * Intéraction avec le puit
   * @param Perso $perso
   * @return array
   */
  private function well(Perso $perso){
    $retour = $perso->recuperer();
    switch ($retour)
    {
      case 0 :
        $message_perso = 'Vous avez r&eacute;cuperer !';
        $this->messageManager->message_console($perso,$message_perso);
        if ($perso->gagnerexperience()==1 )
        {
          $message_perso = 'Bravo, vous passez au niveau '.$perso->getNiveau().'';
          $this->messageManager->message_console($perso,$message_perso);
        }
        $this->persoManager->update($perso);
        return ['retour' => 'guerison'];
        break;
      case 1 :
        $message_perso = 'Mmmm ... cette eau est bonne';
        $this->messageManager->message_console($perso,$message_perso);
        return ['retour' => 'success'];
        break;
      default :
        $message_perso = 'Erreur inconnue';
        $this->messageManager->message_console($perso,$message_perso);
        return ['retour' => 'success'];
    }
    
  }
  
  /**
   * Intéraction avec le coffre
   * @param Perso $perso
   * @return array
   */
  private function chest(Perso $perso){
    $retour = $perso->ouvrircoffre();
    if ($this->objectManager->isfullinventory($perso))
    {
      $message_perso = 'Votre inventaire est plein !';
      $this->messageManager->message_console($perso,$message_perso);
      $this->persoManager->update($perso);
      return ['retour' => 'fullinventory'];
    }
    
    switch ($retour)
    {
      case 0 :
        $message_perso = 'Ce coffre contient .... rien !';
        $this->messageManager->message_console($perso,$message_perso);
        $this->persoManager->update($perso);
        break;
      case 1 :
        $message_perso = 'Ouah! Un globe magique!';
        $this->messageManager->message_console($perso,$message_perso);
        $this->objectManager->addobjet($perso,1);
        break;
      case 3 :
        $message_perso = 'Ouah! Une potion de guerison !';
        $this->messageManager->message_console($perso,$message_perso);
        $this->objectManager->addobjet($perso,3);
        break;
      default :
        $message_perso = 'Erreur inconnue';
        $this->messageManager->message_console($perso,$message_perso);
    }
    return ['retour' => 'success'];
    
  }
  
  /**
   * Intéraction avec un PNJ
   * @param Perso $perso
   * @return array
   */
  private function oldSage(Perso $perso){
    $retour = $this->persoManager->issage($perso);
    switch ($retour)
    {
      case 10 :
        $message_perso = 'Tu m\'as l\'air bien fatigué! Reposes toi un peu';
        $this->messageManager->message_console($perso,$message_perso);
        $perso->recuperer();
        $this->persoManager->update($perso);
        break;
      case 11 :
        $message_perso = 'Tu m\'as l\'air bien en forme !';
        $this->messageManager->message_console($perso,$message_perso);
        break;
      case 12 :
        $message_perso = 'Tu es bien plus en forme que moi !';
        $this->messageManager->message_console($perso,$message_perso);
        break;
      case 40 :
        $message_perso = 'Qui est tu donc pour t\'adresser à moi!';
        $this->messageManager->message_console($perso,$message_perso);
        $this->persoManager->update($perso);
        break;
      case 41 :
        $message_perso = 'Voilà pour toi!';
        $message_perso_suite = 'Avec ceci, tu deviendra un grand magicien!';
        $this->messageManager->message_console($perso,$message_perso);
        $this->messageManager->message_console($perso,$message_perso_suite);
        $this->objectManager->addobjet($perso,1);
        $this->persoManager->update($perso);
        break;
      case 42 :
        $message_perso = 'tu es un guerrier Sors de chez moi !';
        $this->messageManager->message_console($perso,$message_perso);
        $this->persoManager->update($perso);
        break;
      case 43 :
        $message_perso = 'Tu es un grand magicien! Bravo !';
        $this->messageManager->message_console($perso,$message_perso);
        $this->persoManager->update($perso);
        break;
      default :
        $message_perso = 'Erreur inconnue';
        $this->messageManager->message_console($perso,$message_perso);
    }
    return ['retour' => 'success'];
  }
  
  /**
   * Gestion de la limite de deux actions par secondes
   * @param Perso $perso
   * @return bool
   */
  private function actLimit(Perso $perso){
    // pour tout le monde -- action limiter à 2 par sec
    if (file_exists('tmp/act'.$perso->getId().'.tmp'))// on regarde si le fchier tmp existe et si oui on l'ouvre
    {
      $fichier = fopen('tmp/act'.$perso->getId().'.tmp','r+');
      $donnees_fichier = fgets($fichier);
      $tab_donnees = explode(";",$donnees_fichier);
      if ($tab_donnees[0] == time())
      {
        if ($tab_donnees[1] < 2) // 2 actions par secondes
        {
          $ligneagir = $tab_donnees[1]+1;
          fseek($fichier,11);
          fputs($fichier,$ligneagir);
          fclose($fichier);
          return true;
        }
        return false;
      }
      else
      {
        $ligneagir = ''.time().';1';
        fseek($fichier,0);
        fputs($fichier,$ligneagir);
        fclose($fichier);
        return true;
      }
    }
    else
    {
      // on cree le fichier
      $new_fichier = fopen('tmp/'.$perso->getId().'.tmp','a+');
      fputs($new_fichier,time().';1');
      fclose($new_fichier);
      return true;
    }
  }
}