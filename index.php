<?php
  // on ouvre les sessions
  session_start();

  /*Run Application*/
  require('App/Application.php');
  $myApp = new Application();
  $myApp->run();
