<?php

class GameController extends Controller
{
  public function run(){
    $persoManager = new PersoManager();
    $objectManager = new ObjectManager();
    $userManager = new UserManager();
    $messageManager = new MessageManager();
    $persoManager->insertvisite();
    $perso = $userManager->getUser();
    if (empty($perso))
    {
      header('Location: index.php');
      exit;
    }
    
    if ( isset($_GET['restart']) AND $_GET['restart']=='ok')
    {
      if ( $perso->getEtat()=='dead' )
      {
        $x = 1 ;
        $y = 1 ;
        while ($persoManager->placeprise('C',$x,$y))
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
        $objectManager->viderinventaire($perso);
        $persoManager->update($perso);
      }
      
      header('Location: index.php?action=game');
      exit;
    }
    
    if ( isset($_GET['reset']) AND $_GET['reset']=='console')
    {
      $messageManager->resetconsole($perso);
      header('Location: index.php?action=game');
      exit;
    }
    $this->renderView('game',[]);
  }
}
