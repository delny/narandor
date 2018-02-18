<?php

class ApiController
{
  private $persoManager;
  private $userManager;
  
  /**
   * ApiController constructor.
   */
  public function __construct()
  {
    $this->userManager = new UserManager();
    $this->persoManager = new PersoManager();
  }
  
  /**
   * Porte entrée API
   * @return bool
   */
  public function run()
  {
    if(empty($_GET['get'])){
      return false;
    }
    $perso = $this->userManager->getUser();
    
    if($_GET['get'] == 'statut'){
      $data = $this->getStatut($perso);
    } elseif ($_GET['get'] == 'msg'){
      $data = $this->getMessages($perso);
    }
    
    echo json_encode($data);
  }
  
  /**
   * Renvoie informations du joueur
   * @param Perso $perso
   * @return array
   */
  private function getStatut(Perso $perso){
    $force_perso = $perso->getNiveau()*10 - floor( ($perso->getDegats())/10 );
    $data =  array(
      'nom' => $perso->getNom(),
      'etat' => 'alive',
      'position' => $perso->getLocalisationX().','.$perso->getLocalisationY(),
      'niveau' => $perso->getNiveau(),
      'experience' => $perso->getExperience(),
      'degats' => $perso->getDegats(),
      'force' => $force_perso,
    );
  
    if($perso->getTypeperso() == 1){
      $data['protection'] = $perso->getSpecial();
    }
    if($perso->getTypeperso() == 2){
      $data['magie'] = $perso->getSpecial();
    }
    if(preg_match('#sleep#',$perso->getEtat())){
      $fin = explode(';',$perso->getEtat())[1];
      $wait = $fin - time();
      $data['sleep'] = $wait;
      $data['etat'] = 'endormi';
    }
    if($perso->getEtat()=='dead'){
      $data['etat'] = 'mort';
    }
    return $data;
  }
  
  /**
   * Renvoie la liste des messages du joueur
   * @param Perso $perso
   * @return array
   */
  private function getMessages(Perso $perso){
    $data = [];
    if($messages = $this->persoManager->recup_console($perso)){
      foreach ($messages as $message){
        $data_msg = [
          'exp' => $message['id_perso'],
          'date' => $this->persoManager->formaterdate($message['date_message']),
          'contenu' => $message['message'],
        ];
        array_push($data,$data_msg);
      }
    }
    return $data;
  }
}
