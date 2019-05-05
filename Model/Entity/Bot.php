<?php

/**
 * Class Bot
 */
Class Bot extends Perso {
  /**
   * @inheritdoc
   */
  public function gagnerexperience() {
    return 0;
  }

  /**
   * @inheritdoc
   */
  public function ouvrircoffre() {
    return 0;
  }

  /**
   * @inheritdoc
   */
  public function recevoirdegats($force) {
    if ($this->getTypeperso() == 10) {
      return 3;
    }
    else {
      $force = (int)$force;
      $coef = (1 / 10) * (11 - $this->getNiveau());
      $degats = floor($force * $coef);
      $this->setDegats($this->getDegats() + $degats);
      if ($this->getDegats() > 99) {
        $this->setEtat('dead');
        return 2;
      }
      elseif ($degats > 0) {
        return 1;
      }
      else {
        return 0;
      }
    }
  }
}
