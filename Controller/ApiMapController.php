<?php

class ApiMapController
{
  private $mapManager;
  private $persoManager;
  
  public function __construct()
  {
    //managers
    $this->mapManager   = new MapManager();
    $this->persoManager = new PersoManager();
  }
  
  /**
   * Renvoie la carte où se trouve le joueurs et les personnages qui s'y trouvent
   * @param Perso $perso
   * @return array
   */
  public function getmap(Perso $perso){
    $data = [
      'map' => [],
      'persos' => [],
    ];
    $localisation = $perso->getLocalisation();
    $coord_x = $perso->getLocalisationX();
    $coord_y = $perso->getLocalisationY();
  
    $carte = $localisation.substr('0'.$coord_x, -2,1).''.substr('0'.$coord_y, -2,1).'';
    $carte_x = substr('0'.$coord_x, -2,1);
    $carte_y = substr('0'.$coord_y, -2,1);
    
    $data['map']['mapId'] = $carte;
  
    // on recup les persos de cette carte
    $persosaplacer = $this->persoManager->getpersoscarte($localisation,$carte_x,$carte_y);
    
    foreach ($persosaplacer as $persoaplacer){
      $coord_x_pap = $persoaplacer['localisation_x'];
      $coord_y_pap = $persoaplacer['localisation_y'];
      $direction = $persoaplacer['direction'];
  
      $dest_x_pap = ($coord_x_pap%10)*50;
      $dest_y_pap = ($coord_y_pap%10)*50;
  
      $typeperso = $persoaplacer['typeperso'];
      
      switch($typeperso)
      {
        case 0:
          $type = 'default';
          break;
        case 1 :
          $type = 'guerrier';
          break;
        case 2 :
          $type = 'magicien';
          break;
        case 10 :
          $type = 'enfant';
          break;
        default:
          $type = 'default';
      }
  
      $nom_perso = ($persoaplacer['nom'] == $perso->getNom()) ? 'Vous' : $persoaplacer['nom'];
      
      $data_perso = [
        'posX' => $dest_x_pap,
        'posY' => $dest_y_pap,
        'type' => $type,
        'direction' => $direction,
        'name' => $nom_perso,
      ];
      
      array_push($data['persos'],$data_perso);
    }
    
    return $data;
  }
}
