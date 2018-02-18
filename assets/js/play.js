angular.module('myApp')
  .controller('playCtrl',function ($scope,$http) {

  console.log('init play controller');
  $(document).ready(function () {
    getStatut();
  });
  setInterval (function(){
    getStatut();
  },2000);

  var getStatut = function () {
    $http.get('/index.php?action=api&get=statut').then(function (value) {
      $scope.user = value.data;
    });
  };

});