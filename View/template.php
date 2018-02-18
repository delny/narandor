<!DOCTYPE html>
<html ng-app="myApp">
<head>
  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <title><?php echo $titre; ?></title>
  <meta name="keywords" content="anthony, delgado, reseaux, informatique, perpignan, cabestany">
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <link href='https://fonts.googleapis.com/css?family=Lora' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" title="default" href="assets/style.css" />
  <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico"  />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
</head>
</html>
<?php echo $contenu; ?>
<!-- angular vendor files -->
<script src="assets/js/bower_components/angular/angular.js"></script>
<script src="assets/js/bower_components/angular-route/angular-route.js"></script>
<script src="assets/js/bower_components/angular-sanitize/angular-sanitize.js"></script>
<!-- angular app files -->
<script src="assets/js/myApp.js"></script>
<script src="assets/js/play.js"></script>
</html>
