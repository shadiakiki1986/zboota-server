var LoginManager = function($scope) {

  this.loginCore = function(rt) {
        if(rt.hasOwnProperty("error")) {
          this.loginCoreError(rt.error);
          return;
        }

        $scope.$apply(function() {
          $scope.loginStatus='Logged in';
          // append data from server to here
          // Note that I tested that this line is not asynchronous
          $scope.$emit("requestAddCore",rt); // not sure if I can just call $scope.$parent.addCore directly because of $scope 

          window.localStorage.setItem('loginU',angular.toJson($scope.loginU));
          $scope.dataServer=angular.toJson(rt);
          $scope.update(function() {
            $scope.$emit("loggedIn"); //$scope.$parent.get(); // retrieving data after login
          }); // updating with whatever was done while offline

        });
  };

  this.loginCoreComplete = function() {
        $scope.hideLogin();
  };

  this.loginCoreError = function(msg) {
        alert("Zboota login error: "+msg);
        $scope.$apply(function() { $scope.loginStatus='None'; });
        //$scope.$parent.pingServer();
  };

  this.loginNonLambda=function() {
    var self=this;
    $.ajax({type:'POST',
      url: ZBOOTA_SERVER_URL+'/api/login.php',
      data: $scope.loginU,
      dataType: 'json',
      success: function(rt) {
        self.loginCore(rt);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        self.loginCoreError(textStatus+","+errorThrown);
      },
      complete: function() { self.loginCoreComplete(); }
    });
  };

  this.loginLambda = function() {
    var self=this;
    $scope.$parent.awsMan.invokeLambda(
      "zboota-login",
      $scope.loginU,
      function(err,data) {
        if (err||data.StatusCode!=200) {
          self.loginCoreError(err);
          self.loginCoreComplete();
          return;
        }
        rt=angular.fromJson(data.Payload);
        if(rt.hasOwnProperty("errorMessage")) {
          rt = { error: rt.errorMessage };
        } else {
          rt=angular.fromJson(rt);
        }
        self.loginCore(rt);
        self.loginCoreComplete();
    });

  };

}; // end class
