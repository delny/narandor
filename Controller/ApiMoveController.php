<?php

/**
 * Class ApiMoveController.
 */
class ApiMoveController {
  private $persoManager;
  CONST DIRECTIONS = ['gauche', 'haut', 'droite', 'bas'];

  public function __construct()
  {
    $this->persoManager = new PersoManager();
  }

  /**
   * Porte dentrée sous-controller.
   *
   * @param Perso $perso
   *
   * @return array
   */
  public function move(Perso $perso) {
    if (empty($_GET['direction'])) {
      return ['erreur' => 'aucune direction'];
    }

    if (!in_array($_GET['direction'], self::DIRECTIONS)) {
      return ['erreur' => 'direction inconnue'];
    }

    //limitation déplacements
    $canAct = $this->actLimit($perso);

    if (!$canAct) {
      return ['erreur' => 'limite actions atteinte'];
    }

    $direction = $_GET['direction'];
    return $this->moveTo($perso, $direction);
  }

  /** Se déplacer/se tourner dans une direction.
   *
   * @param Perso $perso
   * @param $direction
   *
   * @return array
   */
  private function moveTo(Perso $perso, $direction) {
    if ($direction == $perso->getDirection()) {
      $retour = $this->persoManager->sedeplacer($perso, $direction);
      switch ($retour) {
        case 0 :
          return ['retour' => 'fail'];
          break;
        case 1 :
          $this->persoManager->update($perso);
          return ['retour' => 'success'];
          break;
        case 2 :
          $this->persoManager->update($perso);
          return ['retour' => 'Passage'];
          break;
        case 3 :
          $this->persoManager->update($perso);
          return ['retour' => 'Passage'];
          break;
        default :
          return ['erreur' => 'erreur inconnue'];
      }
    }

    $perso->hydrate(['direction' => $direction]);
    $this->persoManager->update($perso);
    return ['retour' => 'success'];

  }

  /**
   * Gestion de la limite de déplacements par secondes.
   *
   * @param Perso $perso
   *
   * @return bool
   */
  private function actLimit(Perso $perso) {
    if (file_exists('tmp/move' . $perso->getId() . '.tmp'))// on regarde si le fchier tmp existe et si oui on l'ouvre
    {
      $fichiermove = fopen('tmp/move' . $perso->getId() . '.tmp', 'r+');
      $donnees_fichier = fgets($fichiermove);
      $tab_donnees = explode(";", $donnees_fichier);

      if ($tab_donnees[0] == time()) {
        if ($tab_donnees[1] < 4)// 4 déplacements par secondes
        {
          $lignemove = $tab_donnees[1] + 1;
          fseek($fichiermove, 11);
          fputs($fichiermove, $lignemove);
          fclose($fichiermove);
          return true;
        }
        return false;
      } else {
        $lignemove = '' . time() . ';1';
        fseek($fichiermove, 0);
        fputs($fichiermove, $lignemove);
        fclose($fichiermove);
        return true;
      }
    } else {
      // on cree le fichiermove
      $new_fichiermove = fopen('tmp/move' . $perso->getId() . '.tmp', 'a+');
      fputs($new_fichiermove, time() . ';1');
      fclose($new_fichiermove);
      return true;
    }
  }
}