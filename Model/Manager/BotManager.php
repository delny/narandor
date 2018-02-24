<?php

class BotManager extends DatabaseManager
{
  private $bdd;
  
  public function __construct()
  {
    $this->bdd = parent::getBDD();
  }
  
  
  public function getbot($info)
  {
    if (is_int($info))
    {
      $sql = $this->bdd->prepare('SELECT * FROM perso WHERE id = :id');
      $sql->bindValue(':id', $info);
      $sql->execute();
    }
    else
    {
      $sql = $this->bdd->prepare('SELECT * FROM perso WHERE nom = :nom');
      $sql->bindValue(':nom', $info);
      $sql->execute();
    }
    
    if ($donnees = $sql->fetch() )
    {
      return new Bot($donnees);
    }
    else
    {
      return FALSE;
    }
  }
}