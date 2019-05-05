angular.module('myApp')
  .controller('playCtrl',function ($scope,$http) {

  console.log('init play controller');
  $scope.chatopening = false;
  $(document).ready(function () {
    getAll();
  });
  setInterval (function(){
    refreshBot();
  },2000);

  var getAll = function () {
    getStatut();
    getMsg();
    getInventory();
    getMap();
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

  $scope.postMessage = function () {
    if ($scope.post && $scope.post.message) {
      $http.get('/index.php?action=api&call=postmsg&message=' + $scope.post.message).then(function (value) {
        if(value.data && value.data.retour && value.data.retour == 'success'){
          $scope.chatopening = false;
          $scope.post.message = '';
          getStatut();
          getMsg();
          getInventory();
        }
      });
    }
  };

  var getMap = function () {
    $http.get('/index.php?action=api&call=getmap').then(function (value) {
      $scope.persos = value.data.persos;
      if(value.data && value.data.map && value.data.map.mapId){
        var mapId = value.data.map.mapId;
        var imageUlr = 'assets/img/maps/' + mapId + '.png';
        $("#mapimg").css('background','url(' + imageUlr + ')');
      }
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
    console.log($scope.chatopening);
    if ($scope.chatopening) {
      switch (event.keyCode) {
        case 27:
          $scope.chatopening = false;
          $("#msgsendconsole").val('');
          $(function () {
            $(".corps").focus();
          });
        break;
      }
      return;
    }

    switch (event.keyCode) {
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
      case 84: // touche t : ouvre le champ texte pour la sessagerie
        $scope.chatopening = true;
        $(function () {
          $("#msgsendconsole").focus();
        });
        break;
      case 27:
        if($scope.chatopening)
        {
          $scope.chatopening = false;
          $("#msgsendconsole").val('');
          $(function () {
            $(".corps").focus();
          });
        }
        break;
      default:
        //console.log('Erreur : Aucune action ne correspond à cette touche');
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
      }else if(value.data && value.data.retour && value.data.retour == 'Passage'){
        getAll();
        $("#porte").get(0).play();
      }
      else if(value.data && value.data.retour && value.data.retour == 'fail'){
        $("#cantmove").get(0).play();
      }
    });
  };

});
