angular.module('myApp')
  .controller('playCtrl',function ($scope,$http) {

  console.log('init play controller');
  $(document).ready(function () {
    getStatut();
    getMsg();
  });
  setInterval (function(){
    getStatut();
    getMsg();
  },2000);

  var getStatut = function () {
    $http.get('/index.php?action=api&get=statut').then(function (value) {
      $scope.user = value.data;
    });
  };

  var getMsg = function () {
    $http.get('/index.php?action=api&get=msg').then(function (value) {
      $scope.messages = value.data;
    });
  };

});