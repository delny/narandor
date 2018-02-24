<?php

class PersoRepository extends DatabaseManager
{
  private $bdd;
  
  public function __construct()
  {
    $this->bdd = parent::getBDD();
  }
  
  /**
   * Obtient joueur selon info
   * @param $info
   * @return mixed
   */
  public function get($info){
    
    if (is_int($info))
    {
      $sql = $this->bdd->prepare('SELECT * FROM perso WHERE id = :info');
      $sql->bindValue(':info', $info);
      $sql->execute();
    }
    else
    {
      $sql = $this->bdd->prepare('SELECT * FROM perso WHERE nom = :info');
      $sql->bindValue(':info', $info);
      $sql->execute();
    }
    
    return $sql->fetch();
  }
  
  /**
   * Ajoute un joueur en bdd
   * @param Perso $perso
   * @return array
   */
  public function add(Perso $perso){
    $sql = $this->bdd->prepare('INSERT INTO perso (nom,password,etat,localisation,localisation_x,localisation_y, direction, niveau, experience, degats,typeperso,special)
			VALUES (:nom, :password, :etat,:localisation,:localisation_x,:localisation_y, :dir, :niveau, :experience, :degats,:typeperso,:special) ');
    $sql->bindValue(':nom', $perso->getNom() );
    $sql->bindValue(':password', $perso->getPassword() );
    $sql->bindValue(':etat', $perso->getEtat() );
    $sql->bindValue(':localisation', $perso->getLocalisation() );
    $sql->bindValue(':localisation_x', $perso->getLocalisationX() );
    $sql->bindValue(':localisation_y', $perso->getLocalisationY() );
    $sql->bindValue(':dir', $perso->getDirection() );
    $sql->bindValue(':niveau', $perso->getNiveau() );
    $sql->bindValue(':experience', $perso->getExperience() );
    $sql->bindValue(':degats', $perso->getDegats() );
    $sql->bindValue(':typeperso', $perso->getTypeperso() );
    $sql->bindValue(':special', $perso->getSpecial() );
    $sql->execute();
  
    $perso->hydrate(['id' => $this->_bdd->lastInsertId()]);
    return $sql->errorInfo();
  }
  
  /**
   * Mets à jour un joueur
   * @param Perso $perso
   */
  public function update(Perso $perso){
    $sql = $this->bdd->prepare('
			UPDATE perso SET nom = :nom, password = :password, etat = :etat, localisation = :localisation, localisation_x = :localisation_x, localisation_y = :localisation_y,
			direction = :dir, niveau = :niveau, experience = :experience, degats = :degats , typeperso = :typeperso, special = :special
			WHERE id = :id');
    $sql->bindValue(':id', $perso->getId() );
    $sql->bindValue(':nom', $perso->getNom() );
    $sql->bindValue(':password', $perso->getPassword() );
    $sql->bindValue(':etat', $perso->getEtat() );
    $sql->bindValue(':localisation', $perso->getLocalisation() );
    $sql->bindValue(':localisation_x', $perso->getLocalisationX() );
    $sql->bindValue(':localisation_y', $perso->getLocalisationY() );
    $sql->bindValue(':dir', $perso->getDirection() );
    $sql->bindValue(':niveau', $perso->getNiveau() );
    $sql->bindValue(':experience', $perso->getExperience() );
    $sql->bindValue(':degats', $perso->getDegats() );
    $sql->bindValue(':typeperso', $perso->getTypeperso() );
    $sql->bindValue(':special', $perso->getSpecial() );
    $sql->execute();
  }
  
  /**
   * Supprime un joueur
   * @param Perso $perso
   */
  public function delete(Perso $perso){
    $sql = $this->bdd->prepare('DELETE FROM perso WHERE id = :id');
    $sql->bindValue(':id', $perso->getId());
    $sql->execute();
  }
  
  /**
   * @param $localisation
   * @param $carte_x
   * @param $carte_y
   * @return array
   */
  public function getpersoscarte($localisation,$carte_x,$carte_y)
  {
    $recup_sql = $this->bdd->prepare('SELECT * FROM perso WHERE etat != :etat
			AND localisation = :localisation AND localisation_x BETWEEN :startx AND :endx AND localisation_y BETWEEN :starty AND :endy ');
    $recup_sql->bindValue(':etat','dead');
    $recup_sql->bindValue(':localisation',$localisation);
    $recup_sql->bindValue(':startx',$carte_x*10);
    $recup_sql->bindValue(':endx',$carte_x*10 + 9);
    $recup_sql->bindValue(':starty',$carte_y*10);
    $recup_sql->bindValue(':endy',$carte_y*10 + 9);
    $recup_sql->execute();
    
    return $recup_sql->fetchAll();
  }
  
  /**
   * @param $localisation
   * @param $x
   * @param $y
   * @return mixed
   */
  public function getPersoCarte($localisation,$x,$y){
    $get_sql = $this->bdd->prepare('SELECT * FROM perso WHERE etat != :etat AND localisation = :localisation AND localisation_x = :x AND localisation_y = :y');
    $get_sql->bindValue(':etat', 'dead');
    $get_sql->bindValue(':localisation',$localisation);
    $get_sql->bindValue(':x', $x);
    $get_sql->bindValue(':y', $y);
    $get_sql->execute();
    
    return $get_sql->fetch();
  }
  
  /**
   * @return mixed
   */
  public function updateSleepPeople(){
    $sql = $this->bdd->prepare('SELECT * FROM perso WHERE etat LIKE :sleep');
    $sql->bindValue(':sleep','%sleep%');
    $sql->execute();
  
    return $sql->fetchAll();
  }
}
