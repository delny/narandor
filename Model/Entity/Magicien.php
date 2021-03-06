<?php

/**
 * Class Magicien
 */
Class Magicien extends Perso {

  /**
   * Endormir un joueur.
   *
   * @param Perso $cible
   *
   * @return int
   */
  public function endormir(Perso $cible) {
    // on verif que l'adversaire soit a cote
    if ($this->getDirection() == 'haut' AND ($cible->getLocalisationX() == $this->getLocalisationX()) AND ($cible->getLocalisationY() == $this->getLocalisationY() - 1)) {
      $justeacote = TRUE;
    }
    elseif ($this->getDirection() == 'bas' AND ($cible->getLocalisationX() == $this->getLocalisationX()) AND ($cible->getLocalisationY() == $this->getLocalisationY() + 1)) {
      $justeacote = TRUE;
    }
    elseif ($this->getDirection() == 'gauche' AND ($cible->getLocalisationX() == $this->getLocalisationX() - 1) AND ($cible->getLocalisationY() == $this->getLocalisationY())) {
      $justeacote = TRUE;
    }
    elseif ($this->getDirection() == 'droite' AND ($cible->getLocalisationX() == $this->getLocalisationX() + 1) AND ($cible->getLocalisationY() == $this->getLocalisationY())) {
      $justeacote = TRUE;
    }
    else {
      $justeacote = FALSE;
    }

    if ($this->getSpecial() > 30) {
      if ($justeacote AND $this->getId() != $cible->getId()) {
        // on endort
        $this->Setspecial($this->getSpecial() - 30);
        return $cible->fairedodo($this->getNiveau() * 60);
      }
      elseif ($this->getId() == $cible->getId()) {
        // le perso s endort lui-meme
        return 3;
      }
    }
    else {
      // impossible de endormir ce perso car pas assez energie
      return 2;
    }

  }

  /**
   * Regénére la magie.
   */
  public function regenerermagie() {
    if ($this->getSpecial() < 100) {
      $this->setspecial($this->getSpecial() + 1);
    }
    else {
      $this->setspecial(100);
    }
  }

  /**
   * @inheritdoc
   */
  public function ouvrircoffre()
  {
    $nombre = rand(1, 12);
    if (in_array($nombre, $this->getInventaire())) {
      return 0;
    }
    else {
      switch ($nombre) {
        case 3 :
          return 3;
          break;
        default:
          return 0;
      }
    }
  }
}
