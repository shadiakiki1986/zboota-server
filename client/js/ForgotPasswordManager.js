var ForgotPasswordManager = function($scope) {

  this.forgotPasswordCore=function() {
        if(rt.hasOwnProperty("error")) {
          alert("Zboota forgot password error: "+rt.error);
          return;
        }
        alert("Your password has been emailed to you. Please check your email inbox, and possibly the junk mail folder, in a few minutes.");
  };
  this.forgotPasswordCoreComplete=function() {
        $scope.hideLogin();
        $scope.$apply(function() { $scope.forgotPasswordStatus=false; });
  };
  this.forgotPasswordCoreError=function(msg) {
        console.log("Error in forgot password. "+msg);
        //$scope.$parent.pingServer();
  };

  this.forgotPasswordNonLambda=function() {
    var self=this;
    $.ajax({type:'POST',
      url: ZBOOTA_SERVER_URL+'/api/forgotPassword.php',
      data: $scope.loginU,
      dataType: 'json',
      success: function(rt) {
        self.forgotPasswordCore();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        self.forgotPasswordCoreError(textStatus+","+errorThrown);
      },
      complete: function() {
        self.forgotPasswordCoreComplete();
      }
    });
  };

  this.forgotPasswordLambda = function() {
    var self=this;
    $scope.$parent.awsMan.invokeLambda(
      "zboota-forgotPassword",
      $scope.loginU,
      function(err,data) {
        if (err||data.StatusCode!=200) {
          self.forgotPasswordCoreError(err);
          self.forgotPasswordCoreComplete();
          return;
        }
        rt=angular.fromJson(data.Payload);
        if(rt.hasOwnProperty("errorMessage")) {
          rt = { error: rt.errorMessage };
        }
        self.forgotPasswordCore();
        self.forgotPasswordCoreComplete();
    });

  };

}; // end class
