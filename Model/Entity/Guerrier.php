<?php

/**
 * Class Guerrier
 */
Class Guerrier extends Perso {

  /**
   * @inheritdoc
   */
  public function recevoirdegats($force) {
    $force = (int)$force;
    $coef = (1 / 10) * (11 - $this->getNiveau());
    $coefprotection = 1 - ($this->getSpecial() / 200);
    $degats = floor($force * $coef * $coefprotection);
    $this->setDegats($this->getDegats() + $degats);
    if ($this->getDegats() > 99) {
      $this->setEtat('dead');
      return 2;
    } elseif ($degats > 0) {
      return 1;
    } else {
      return 0;
    }
  }

  /**
   * @inheritdoc
   */
  public function ouvrircoffre() {
    $nombre = rand(1, 12);
    if (in_array($nombre, $this->getInventaire())) {
      return 0;
    } else {
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
