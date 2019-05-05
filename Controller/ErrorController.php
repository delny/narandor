<?php

/**
 * Class ErrorController
 */
class ErrorController extends Controller {
  /**
   * @param $error
   */
  public function run($error) {
    $this->renderView('error', [
      'error' => $error,
    ]);
  }
}
