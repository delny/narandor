<?php
/**
 * Created by PhpStorm.
 * User: a.delgado
 * Date: 10/11/2017
 * Time: 09:57
 */

class ErrorController extends Controller
{
    /**
     * @param $error
     */
    public function run($error){
        $this->renderView('error',[
            'error' => $error,
        ]);
    }
}
