<?php

class GameController extends Controller
{
  public function run(){
    $manager = new PersoManager();
    $manager->insertvisite();
    $pid = (isset($_SESSION['pid'])) ? (int) $_SESSION['pid'] : false;
    $perso = $manager->get($pid);
    if (empty($pid))
    {
      header('Location: index.php');
      exit;
    }
    
    if ( isset($_GET['restart']) AND $_GET['restart']=='ok')
    {
      if ( $perso->etat()=='dead' )
      {
        $x = 1 ;
        $y = 1 ;
        while ($manager->placeprise('C',$x,$y))
        {
          $x = rand(1,9);
          $y = rand(1,9);
        }
        $perso->hydrate([
          'etat' => 'alive',
          'localisation' => 'C',
          'localisation_x' => $x,
          'localisation_y' => $y,
          'direction' => 'bas',
          'niveau' => 1,
          'experience' => 0,
          'degats' => 0,
          'special' => 1
        ]);
        $manager->viderinventaire($perso);
        $manager->update($perso);
      }
      
      header('Location: index.php?action=game');
      exit;
    }
    
    if ( isset($_GET['reset']) AND $_GET['reset']=='console')
    {
      $manager->resetconsole($perso);
      header('Location: index.php?action=game');
      exit;
    }
    $this->renderView('game',[]);
  }
}
