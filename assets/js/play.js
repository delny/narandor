angular.module('myApp')
  .controller('playCtrl',function ($scope,$http) {

  console.log('init play controller');
  $(document).ready(function () {
    getStatut();
    getMsg();
    getInventory();
    getMap();
    refreshBot();
  });
  setInterval (function(){
    getStatut();
    getMsg();
    getInventory();
    getMap();
    refreshBot();
  },1500);

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
  }

});