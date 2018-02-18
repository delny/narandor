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
      /*
      return array(
        'nom' => ,
        'position' => ,
        'niveau' => ,
        'experience' => ,
        'degats' => ,
        'force' => ,
      );*/
    }
  }
}
