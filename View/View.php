<?php

/**
 * Class View
 */
class View {
  /**
   * @param $viewName
   * @param $params
   */
  public function createView($viewName, $params) {
    $viewFile = 'View/view' . ucfirst($viewName) . '.php';
    extract($params);
    if (file_exists($viewFile)) {
      require($viewFile);
    }
    else {
      $error = 'La vue ' . $viewName . ' n\'est pas disponible';
      require('View/viewError.php');
    }
    require('View/template.php');
  }
}
