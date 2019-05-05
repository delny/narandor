<?php

/**
 * Class MapManager.
 */
class MapManager {
  private $takenPlaces;

  public function __construct() {
    //rempli les positions impossibles
    $this->takenPlaces = [
      '8,1', '8,2', '53,36', '56,36', '22,90', '25,90', '22,89', '25,89', '24,53', '26,53',
      '25,54', '24,55', '26,55', '44,22', '44,23', '42,15', '77,43', '78,43', '79,43', '77,51',
      '78,51', '79,51', '33,11', '34,11', '33,12', '33,13', '18,3', '12,15', '11,21', '31,47'
    ];
  }

  /**
   * Détermine si la position vers laquelle le jouer se dirige est prise
   * @param string $localisation
   * @param $x
   * @param $y
   * @return bool
   */
  public function placeprise($localisation = 'C', $x, $y) {
    $coord = '' . $x . ',' . $y . '';
    if ($localisation == 'C') {
      if (($x == 9 OR $x == 10) AND ($y < 34 OR ($y > 34 AND $y < 92))) //colonne arbre
      {
        return TRUE;
      } elseif (($x == 69 OR $x == 70) AND ($y > 34 AND $y < 92))//colonne arbre
      {
        return TRUE;
      } elseif ((($x > 10 AND $x < 54) OR ($x > 55 AND $x < 69)) AND ($y == 35)) // ligne arbre
      {
        return TRUE;
      } elseif ((($x > 10 AND $x < 23) OR ($x > 24 AND $x < 69)) AND ($y == 91)) // ligne arbre
      {
        return TRUE;
      } elseif (($x > 34 AND $x < 55) AND ($y > 54 AND $y < 75)) // lac
      {
        return TRUE;
      } elseif (($x > 54 AND $x < 64) AND ($y == 68)) // riviere - 1
      {
        return TRUE;
      } elseif (($x == 63) AND ($y > 62 AND $y < 68)) // riviere - 2
      {
        return TRUE;
      } elseif (($x == 63) AND ($y > 35 AND $y < 62)) // riviere - 3
      {
        return TRUE;
      } elseif (($x == 63) AND ($y > 23 AND $y < 34)) // riviere - 3
      {
        return TRUE;
      } elseif (($x == 41) AND ($y > 74 AND $y < 82)) // 2eme riviere part1
      {
        return TRUE;
      } elseif (($x == 41) AND ($y > 82 AND $y < 92)) // 2eme riviere part2
      {
        return TRUE;
      } elseif (($x == 41) AND ($y > 92)) // 2eme riviere part3
      {
        return TRUE;
      } elseif (($x == 55 OR $x == 74) AND ($y > 14 AND $y < 25)) // falaise - 1
      {
        return TRUE;
      } elseif (($x > 54 AND $x < 75) AND ($y == 15)) // falaise - 2
      {
        return TRUE;
      } elseif ((($x > 54 AND $x < 65) OR ($x > 65 AND $x < 75)) AND ($y == 24)) // falaise - 3
      {
        return TRUE;
      } elseif (($x == 24) AND ($y > 4 AND $y < 25)) // falaise - 1
      {
        return TRUE;
      } elseif (($x == 35) AND (($y > 4 AND $y < 12) OR ($y > 12 AND $y < 25))) // falaise - 2
      {
        return TRUE;
      } elseif ((($x > 23 AND $x < 36) AND ($y == 5 OR $y == 24))) // falaise - 3
      {
        return TRUE;
      } elseif (($x > 10 AND $x < 18) AND ($y > 29 AND $y < 33)) // maison - 1
      {
        return TRUE;
      } elseif ((($x > 10 AND $x < 13) OR ($x > 13 AND $x < 18)) AND ($y == 33)) // maison - 2
      {
        return TRUE;
      } elseif (($x > 5 AND $x < 10) AND ($y > 40 AND $y < 46)) // 2eme maison
      {
        return TRUE;
      } elseif (($x > 85 AND $x < 89) AND ($y > 33 AND $y < 36)) // fontaine
      {
        return TRUE;
      } elseif (($x > 75) AND ($y == 14 OR $y == 55)) //contour village - 1
      {
        return TRUE;
      } elseif (($x == 76) AND (($y > 14 AND $y < 34) OR ($y > 34 AND $y < 55))) //contour village - 2
      {
        return TRUE;
      } elseif (($x > 77 AND $x < 86) AND ($y > 21 AND $y < 27)) //maison village
      {
        return TRUE;
      } elseif ((($x > 79 AND $x < 89) AND ($y > 42 AND $y < 51)) OR ((($x > 79 AND $x < 82) OR ($x > 82 AND $x < 86) OR ($x > 86 AND $x < 89)) AND ($y == 51))) //immeuble
      {
        return TRUE;
      } elseif (($x > 90) AND ($y > 15 AND $y < 21)) //maison village - 1
      {
        return TRUE;
      } elseif (($x > 90) AND ($y > 21 AND $y < 27)) //maison village - 2
      {
        return TRUE;
      } elseif (($x > 90) AND ($y > 27 AND $y < 33)) //maison village - 3
      {
        return TRUE;
      } elseif (($x > 90) AND ($y > 33 AND $y < 39)) //maison village - 4
      {
        return TRUE;
      } elseif (($x > 90) AND ($y > 40 AND $y < 46)) //maison village - 5
      {
        return TRUE;
      } elseif (($x > 90) AND ($y > 46 AND $y < 52)) //maison village - 6
      {
        return TRUE;
      } elseif (in_array($coord, $this->takenPlaces)) {
        return TRUE;
      } else {
        return FALSE;
      }
    } elseif ($localisation == 'M') {
      $tab_places_prises = ['1,1', '1,2'];
      if ($x < 1 OR $y < 1 OR $x > 8 OR $y > 10) ///bordure maison
      {
        return TRUE;
      } elseif ($x != 3 AND ($y == 9 OR $y == 10))///bordure maison - suite
      {
        return TRUE;
      } elseif (in_array($coord, $tab_places_prises)) {
        return TRUE;
      } else {
        return FALSE;
      }
    } elseif ($localisation == 'N') {
      $tab_places_prises = ['17,1'];
      if ($x < 1 OR $y < 0 OR $x > 18 OR $y > 38) //bordure
      {
        return TRUE;
      }
      if (($x != 3 AND $x != 4 AND $x != 13 AND $x != 14) AND ($y == 9)) //bordure - bas
      {
        return TRUE;
      }
      if ($y == 19 OR $y == 29) //bordure - bas
      {
        return TRUE;
      } elseif (($x != 15 AND $y != 17) AND ($y == 0 OR $y == 10 OR $y == 20 OR $y == 30))//bordure sauf escalier
      {
        return TRUE;
      } elseif ($x == 15 AND ($y == 0 OR $y == 30))// escalier manquants
      {
        return TRUE;
      } elseif (in_array($coord, $tab_places_prises)) {
        return TRUE;
      } else {
        return FALSE;
      }
    }
  }

}
