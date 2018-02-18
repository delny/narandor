<?php

class Controller
{
    /**
     * @param $viewName
     * @param $params
     */
    protected function renderView($viewName,$params){
        $view = new View();
        $view->createView($viewName,$params);
    }
}
