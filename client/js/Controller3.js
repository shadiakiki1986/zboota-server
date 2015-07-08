function Controller3($scope,$http) {

  $scope.message="";
  getHeaderMessage=function() {
    // for instructions on how to update the header message, please refer to the README notes in
    // https://github.com/shadiakiki1986/zboota-server-nodejs
    $http.get("https://s3-us-west-2.amazonaws.com/zboota-server/headerMessage/headerMessage.txt").
      success(function(rt) {
        rt=rt.trim();
        if(rt!="") {
          $scope.message=rt;
        }
      }).error(function(err) { console.log("Failed to get header", err); });
  };

  $scope.$on('serverAvailable', function(event,fn) { getHeaderMessage(); });
};
