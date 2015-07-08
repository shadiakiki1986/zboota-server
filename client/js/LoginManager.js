var LoginManager = function($scope) {

  this.success = function(rt) {
        if(rt.hasOwnProperty("error")) {
          this.error(rt.error);
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

  this.complete = function() {
        $scope.hideLogin();
  };

  this.error = function(msg) {
        alert("Zboota login error: "+msg);
        $scope.$apply(function() {
           $scope.loginStatus='None';
          $scope.$parent.pingServer(false,true);
        });
  };

  this.loginNonLambda=function() {
    var self=this;
    $.ajax({type:'POST',
      url: ZBOOTA_SERVER_URL+'/api/login.php',
      data: $scope.loginU,
      dataType: 'json',
      success: function(rt) {
        self.success(rt);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        self.error(textStatus+","+errorThrown);
      },
      complete: function() { self.complete(); }
    });
  };

  this.loginLambda = function() {
    var self=this;
    $scope.$parent.awsMan.invokeLambda(
      "zboota-login",
      $scope.loginU,
      function(err,data) {
        if (err||data.StatusCode!=200) {
          self.error(err);
          self.complete();
          return;
        }
        rt=angular.fromJson(data.Payload);

        if(rt.hasOwnProperty("errorMessage")) {
          rt = { error: rt.errorMessage };
        }

        if(rt.hasOwnProperty("error")) {
          self.error(rt.error);
          self.complete();
          return;
        }

        rt=angular.fromJson(rt);
        self.success(rt);
        self.complete();
    });

  };

}; // end class
