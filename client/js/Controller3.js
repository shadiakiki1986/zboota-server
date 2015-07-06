function Controller3($scope,$http) {

  $scope.message="";
  getHeaderMessage=function() {
    $http.get(ZBOOTA_SERVER_URL+'/api/headerMessage.php').
      success(function(rt) {
        if(rt.message) {
          $scope.message=rt.message;
        }
      });
  };

  $scope.$on('serverAvailable', function(event,fn) { getHeaderMessage(); });
};
