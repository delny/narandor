<?php

class ApiController
{
  public function run()
  {
    if(empty($_GET['get'])){
      return false;
    }
    //appel du manager
    $userManager = new UserManager();
    
    if($_GET['get'] == 'statut'){
      $perso = $userManager->getUser();
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
      
      echo json_encode($data);
    }
  }
}
