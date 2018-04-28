<?php

class ApiController
{
  private $persoManager;
  private $userManager;
  private $objectManager;
  private $messageManager;
  private $apiActionController;
  private $apiBotController;
  private $apiConsoleController;
  private $apiMapController;
  private $apiMoveController;
  
  /**
   * ApiController constructor.
   */
  public function __construct()
  {
    //Manager
    $this->userManager    = new UserManager();
    $this->persoManager   = new PersoManager();
    $this->objectManager  = new ObjectManager();
    $this->messageManager = new MessageManager();
    //Sub-Controller
    $this->apiActionController  = new ApiActionController();
    $this->apiBotController     = new ApiBotController();
    $this->apiConsoleController    = new ApiConsoleController();
    $this->apiMapController     = new ApiMapController();
    $this->apiMoveController    = new ApiMoveController();
  }
  
  /**
   * Porte entrée API
   * @return bool
   */
  public function run()
  {
    $perso = $this->userManager->getUser();
    $data = array();
    if(!empty($_GET['call'])){
      $apiMethod = $_GET['call'];
    } else{
      echo json_encode(['erreur' => 'appel inconnu']);
    }
    
    /* A chaque appel, on gère les personnes endormis */
    $this->persoManager->updatesleeppeople();
    
    if(method_exists($this,$apiMethod)){
      $data = $this->$apiMethod($perso);
    }elseif (method_exists($this->apiActionController,$apiMethod)) {
      $data = $this->apiActionController->$apiMethod($perso);
    }elseif (method_exists($this->apiBotController,$apiMethod)) {
      $data = $this->apiBotController->$apiMethod($perso);
    }elseif (method_exists($this->apiConsoleController,$apiMethod)) {
      $data = $this->apiConsoleController->$apiMethod($perso);
    }elseif (method_exists($this->apiMapController,$apiMethod)) {
      $data = $this->apiMapController->$apiMethod($perso);
    }elseif (method_exists($this->apiMoveController,$apiMethod)) {
      $data = $this->apiMoveController->$apiMethod($perso);
    }else {
      echo json_encode(['erreur' => 'appel inconnu']);
    }
    
    echo json_encode($data);
  }
  
  /**
   * Renvoie informations du joueur
   * @param Perso $perso
   * @return array
   */
  private function getstatut(Perso $perso){
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
  private function getmsg(Perso $perso){
    $data = [];
    if($messages = $this->messageManager->recup_console($perso)){
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
  
  /**
   * Renvoie l'inventaire du joueur
   * @param Perso $perso
   * @return array
   */
  private function getinventory(Perso $perso){
    $data = [];
    // on recup les objets du joueur
    $objets = $this->objectManager->getobjetsjoueur($perso);
    foreach ($objets as $objet){
      $dataObject = [
        'objectId' => $objet['idobjet'],
        'name' => $objet['nom'],
        'useId' => $objet['idobjetunique'],
      ];
      array_push($data,$dataObject);
    }
    return $data;
  }
  
  /**
   * Utilise un objet
   * @param Perso $perso
   * @return array
   */
  private function useobject(Perso $perso){
    $object_id = !empty($_GET['objectid']) ? (int) $_GET['objectid'] : false;
    if(!$object_id){
      return ['erreur' => 'objet manquant'];
    }
    $objet = $this->objectManager->getobjet($perso,$object_id);
    switch ($objet['idobjet']){
      case 1:
        // Globe magique - chgt en magicien
        $perso->setTypeperso(2);
        $this->persoManager->update($perso);
        $message_perso = 'Le globe vous a transform&eacute; en magicien!';
        $this->messageManager->message_console($perso,$message_perso);
        $this->objectManager->deleteobjet($object_id);
        break;
      case 3:
        // potion de guerison
        $perso->recuperer();
        $this->persoManager->update($perso);
        $message_perso = 'Vous avez r&eacute;cuperer !';
        $this->messageManager->message_console($perso,$message_perso);
        $this->objectManager->deleteobjet($object_id);
      default:
        //rien ne se passe
        
    }
    $data = ['retour' => 'success'];
    return $data;
  }
  
  /**
   * Vider console de messages d'un joueur
   * @param Perso $perso
   * @return array
   */
  private function resetconsole(Perso $perso){
    $this->messageManager->resetconsole($perso);
    $data = ['retour' => 'success'];
    return $data;
  }
}
