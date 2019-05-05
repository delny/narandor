<?php

/**
 * Class ObjectManager
 */
class ObjectManager extends DatabaseManager {
  /**
   * @var PDO
   */
  private $bdd;

  /**
   * ObjectManager constructor.
   */
  public function __construct() {
    $this->bdd = parent::getBDD();
  }

  /**
   * @param Perso $perso
   * @return array
   */
  public function getobjetsjoueur(Perso $perso) {
    $sql = $this->bdd->prepare('SELECT inventaire.id as idobjetunique,idperso,idobjet,nom FROM inventaire JOIN objets ON idobjet=objets.id WHERE idperso = :idperso');
    $sql->bindValue(':idperso', $perso->getId());
    $sql->execute();

    return $sql->fetchAll();
  }

  /**
   * @param Perso $perso
   * @return array|bool
   */
  public function getinventaire(Perso $perso) {
    $sql = $this->bdd->prepare('SELECT idobjet FROM inventaire WHERE idperso = :idperso');
    $sql->bindValue(':idperso', $perso->getId());
    $sql->execute();

    $tab_inventaire = [];
    if ($recup_sql = $sql->fetch()) {
      $tab_inventaire = [];
      do {
        $idobjet = $recup_sql['idobjet'];
        array_push($tab_inventaire, $idobjet);
      } while ($recup_sql = $sql->fetch());
    } else {
      $tab_inventaire = FALSE;
    }
    return $tab_inventaire;
  }

  /**
   * @param Perso $perso
   * @param $idobjet
   */
  public function addobjet(Perso $perso, $idobjet) {
    $sql = $this->bdd->prepare('INSERT INTO inventaire (idperso,idobjet) VALUES (:idperso,:idobjet)');
    $sql->bindValue(':idperso', $perso->getId());
    $sql->bindValue(':idobjet', $idobjet);
    $sql->execute();
  }

  /**
   * @param Perso $perso
   * @param $idobjetunique
   * @return mixed
   */
  public function getobjet(Perso $perso, $idobjetunique) {
    $sql = $this->bdd->prepare('SELECT idperso,idobjet FROM inventaire WHERE id = :idobjetunique AND idperso = :idperso');
    $sql->bindValue(':idobjetunique', $idobjetunique);
    $sql->bindValue(':idperso', $perso->getId());
    $sql->execute();
    return $sql->fetch();
  }

  /**
   * @param $idobjetunique
   */
  public function deleteobjet($idobjetunique) {
    $sql = $this->bdd->prepare('DELETE FROM inventaire WHERE id = :idobjetunique');
    $sql->bindValue(':idobjetunique', $idobjetunique);
    $sql->execute();
  }

  /**
   * @param Perso $perso
   * @return bool
   */
  public function isfullinventory(Perso $perso) {
    $sql = $this->bdd->prepare('SELECT COUNT(idperso) as total FROM inventaire  WHERE idperso = :idperso');
    $sql->bindValue(':idperso', $perso->getId());
    $sql->execute();
    $retour = $sql->fetch();
    $total = $retour['total'];

    if ($total < 8) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  /**
   * @param Perso $perso
   */
  public function viderinventaire(Perso $perso) {
    $sql = $this->bdd->prepare('DELETE FROM inventaire WHERE idperso = :idperso');
    $sql->bindValue(':idperso', $perso->getId());
    $sql->execute();
  }
}