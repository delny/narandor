angular.module('myApp')
  .controller('playCtrl',function ($scope,$http) {

  console.log('init play controller');
  $(document).ready(function () {
    getStatut();
    getMsg();
    getInventory();
  });
  setInterval (function(){
    getStatut();
    getMsg();
    getInventory();
  },2000);

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