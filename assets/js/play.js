angular.module('myApp')
  .controller('playCtrl',function ($scope,$http) {

  console.log('init play controller');
  $(document).ready(function () {
    $http.get('/index.php?action=api&get=statut').then(function (value) {
      console.log(value.data);
      $scope.user = value.data;
    });
  });
});