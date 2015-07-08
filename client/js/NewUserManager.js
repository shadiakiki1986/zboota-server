var NewUserManager = function($scope) {

  this.nonLambda = function() {
    $.ajax({type:'POST',
      url: ZBOOTA_SERVER_URL+'/api/new.php',
      data: {'email':$scope.loginU.email},
      dataType: 'json',
      success: this.success,
      error: function(jqXHR, textStatus, errorThrown) { this.error("Error adding new account. "+textStatus+","+errorThrown); },
      complete: function() { $scope.$apply(function() { $scope.newUStatus='None'; }); }

    });
  };

  this.success = function(rt) {
//console.log(rt);
        $scope.hideLogin();
        if(rt.hasOwnProperty("error")) {
          alert("Zboota new account error: "+rt.error);
          return;
        }
        alert("Please check your email in a few minutes (including possibly the junk mail folder) and log into the app using the random password in the email.");
  };

  this.error = function(msg) {
        alert(msg);
        $scope.$parent.pingServer();
  };

  this.complete = function() {
      $scope.$apply(function() { $scope.newUStatus='None'; });
  };

  this.lambda = function() {
    var self=this;
    $scope.$parent.awsMan.invokeLambda(
      "zboota-newUser",
      { email: $scope.loginU.email },
      function(err,data) {
        if (err||data.StatusCode!=200) {
          self.error(err);
          self.complete();
          return;
        }
        rt=angular.fromJson(data.Payload);
        if(rt.hasOwnProperty("errorMessage")) {
          rt = { error: rt.errorMessage };
        } else {
          rt=angular.fromJson(rt);
        }
        self.success(rt);
        self.complete();
    });

  };

};
