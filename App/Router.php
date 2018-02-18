<?php

class Router
{
    public function routeRequest(){
        $action = (isset($_GET['action'])) ? $_GET['action'] : 'Home';
        try{
            $this->loadController($action);
        } catch (\Exception $e){
            $errorController = new ErrorController();
            $errorController->run($e->getMessage());
        }
    }

    /**
     * @param $action
     * @throws \Exception
     */
    private function loadController($action){
      if (file_exists('Controller/'.ucfirst($action).'Controller.php'))
      {
        $controllerName = ucfirst($action).'Controller';
        $actionController = new $controllerName();

        $actionController->run();
      }
      else
      {
        throw new Exception('L\'action '.$action.' est introuvable');
      }

    }
}
