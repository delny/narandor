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
  
  public function postmessage(Perso $perso){
    return ['retour' => 'success'];
  }
}
