<?php

class HomeController extends Controller
{
    public function run(){
      // creation du manager
      $manager = new PersoManager();
      $userManager = new UserManager();
      $perso = $userManager->getUser();
      if(!empty($perso))
      {
        header('Location: index.php?action=game');
        exit;
      }
      if (isset($_POST['login'],$_POST['password']))
      {
        $nom = $_POST['login'];
        $password = $_POST['password'];

        if (preg_match('#BOT#i',$nom))
        {
          $erreur = 'Nom invalide';
        }
        elseif(!preg_match('#^[a-zA-Z0-9]{1,}$#',$nom))
        {
          $erreur = 'Nom invalide';
        }
        elseif ($perso = $manager->get($nom)) // il existe
        {
          $retour = TRUE;
          if( sha1('B1*x'.$password)!=$perso->getPassword() ) // les mots de passe correspondent
          {
            $erreur = 'L\'utilisateur existe d&eacute;j&agrave; et le mot de passe est incorrect ';
          }
        }
        else // on le creer
        {
          $x = 1 ;
          $y = 1 ;
          while ($manager->placeprise('C',$x,$y))
          {
            $x = rand(1,9);
            $y = rand(1,9);
          }
          $retour = FALSE;
          $perso = new Perso ([
            'nom' => $nom,
            'password' => sha1('B1*x'.$password),
            'etat' => 'alive',
            'localisation' => 'C',
            'localisation_x' => $x,
            'localisation_y' => $y,
            'direction' => 'bas',
            'niveau' => 1,
            'experience' => 0,
            'degats' => 0,
            'typeperso' => 0,
            'special' => 1
          ]);
          $manager->add($perso);
        }

        if (!isset($erreur))
        {
          $_SESSION['pid'] = $perso->getId();
          $retouroubienvenue = ($retour) ? 'Bon retour' : 'Bienvenue';
          $message_perso = $retouroubienvenue.' &agrave; Narandor' ;
          $manager->message_console($perso,$message_perso);
          header('Location: index.php?action=game');
          exit;
        }
      }
      $this->renderView('home',[]);
    }
}
