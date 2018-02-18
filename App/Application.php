<?php

class Application
{
    public function run (){
        /*load autoloader*/
        require_once('Autoloader.php');
        Autoloader::register();

        /*load Configuration*/
        require ('Config/parameters.php');

        /*routeRequest*/
        $router = new Router();
        $router->routeRequest();
    }
}
