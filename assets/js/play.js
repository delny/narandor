angular.module('myApp')
  .controller('playCtrl',function ($scope,$http) {

  console.log('init play controller');
  $(document).ready(function () {
    getAll();
  });
  setInterval (function(){
    getAll();
  },2000);

  var getAll = function () {
    getStatut();
    getMsg();
    getInventory();
    getMap();
    refreshBot();
  };
  
  var getStatut = function () {
    $http.get('/index.php?action=api&call=getstatut').then(function (value) {
      $scope.user = value.data;
    });
  };

  var getInventory = function () {
    $http.get('/index.php?action=api&call=getinventory').then(function (value) {
      $scope.objets = value.data;
    });
  };

  var getMsg = function () {
    $http.get('/index.php?action=api&call=getmsg').then(function (value) {
      $scope.messages = value.data;
    });
  };

  var getMap = function () {
    $http.get('/index.php?action=api&call=getmap').then(function (value) {
      $scope.persos = value.data.persos;
      var mapId = value.data.map.mapId;
      var imageUlr = 'assets/img/maps/' + mapId + '.png';
      $("#mapimg").css('background','url(' + imageUlr + ')');
    });
  };

  var refreshBot = function () {
    $http.get('/index.php?action=api&call=refreshbots').then(function (value) {
      if(value.data && value.data.retour && value.data.retour == 'success'){
        getStatut();
        getMsg();
        getInventory();
      }
    });
  };

  $scope.useObject = function (objectId) {
    $http.get('/index.php?action=api&call=useobject&objectid=' + objectId).then(function (value) {
      if(value.data && value.data.retour && value.data.retour == 'success'){
        getStatut();
        getMsg();
        getInventory();
      }
    });
  };

  $scope.resetConsole = function () {
    $http.get('/index.php?action=api&call=resetconsole').then(function (value) {
      if(value.data && value.data.retour && value.data.retour == 'success'){
        getMsg();
      }
    });
  };
  
  /* Gestion actions/déplacements */
  $scope.action = function (event) {
    switch (event.keyCode){
      case 13:
        act('hit');
        break;
      case 83:
        act('sleep');
        break;
      case 37:
        move('gauche');
        break;
      case 38:
        move('haut');
        break;
      case 39:
        move('droite');
        break;
      case 40:
        move('bas');
        break;
      default:
        console.log('Erreur : Aucune action ne correspond à cette touche');
    }
  };

  var act = function (action) {
    $http.get('/index.php?action=api&call=act&act=' + action).then(function (value) {
      if(value.data && value.data.retour && value.data.retour == 'success'){
        getAll();
      }
    });
  };

  var move = function (direction) {
    $http.get('/index.php?action=api&call=move&direction=' + direction).then(function (value) {
      if(value.data && value.data.retour && value.data.retour == 'success'){
        getAll();
      }else if(value.data && value.data.retour && value.data.retour == 'passage'){
        getAll();
        $("#porte").get(0).play();
      }
      else if(value.data && value.data.retour && value.data.retour == 'fail'){
        $("#cantmove").get(0).play();
      }
    });
  }

});
