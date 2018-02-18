<?php

class UserManager
{
  public function getUser()
  {
    if(empty($_SESSION['pid'])){
      return false;
    }
    $persoManager = new PersoManager();
    return $persoManager->get( (int) $_SESSION['pid']);
  }
}