<?php

/**
 * Class MessageManager
 */
class MessageManager extends DatabaseManager {
  /**
   * @var PDO
   */
  private $bdd;

  public function __construct() {
    $this->bdd = parent::getBDD();
  }

  /**
   * @param Perso $perso
   * @param $message
   */
  public function message_console(Perso $perso, $message) {
    $sql = $this->bdd->prepare('INSERT INTO console (id_perso,message,date_message) VALUES (:id_perso,:message,NOW())');
    $sql->bindValue(':id_perso', $perso->getId());
    $sql->bindValue(':message', $message);
    $sql->execute();
  }

  /**
   * @param Perso $perso
   * @return array
   */
  public function recup_console(Perso $perso) {
    $sql = $this->bdd->prepare('SELECT message,date_message,id_perso FROM console WHERE id_perso = :id_perso OR id_perso = 0 ORDER BY id DESC');
    $sql->bindValue(':id_perso', $perso->getId());
    $sql->execute();

    return $sql->fetchAll();
  }

  /**
   * @param Perso $perso
   */
  public function resetconsole(Perso $perso){
    $sql = $this->bdd->prepare('DELETE FROM console WHERE id_perso = :id_perso');
    $sql->bindValue(':id_perso', $perso->getId());
    $sql->execute();
  }
}