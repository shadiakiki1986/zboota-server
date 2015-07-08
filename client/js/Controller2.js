function Controller2($scope,$http) {

  $scope.dataServer='{}';

  $scope.loginReset=function() { $scope.loginU={email:'',pass:''}; };
  $scope.loginReset();

  $scope.loginInvalid=function(excludePass) {
    return ($scope.loginStatus!='None'&&$scope.loginStatus!='Logged in')||$scope.getStatus!='None'||!$scope.loginU.email||!(excludePass||$scope.loginU.pass);
  };

  $scope.loginStatus='None';
  lm = new LoginManager($scope);
  $scope.login=function() {
    console.log("login");
    if($scope.loginInvalid()||!$scope.$parent.serverAvailable) return;
    $scope.loginStatus='Logging in';
    if(!USE_AWS_LAMBDA) lm.loginNonLambda(); else lm.loginLambda();
  }

  $scope.updateStatus='None';
  um = new UpdateManager($scope);
  $scope.update=function(sFn) {
  // sFn: success callback function with no parameters

    if($scope.loginInvalid()||!$scope.$parent.serverAvailable) return;

    um.sFn = sFn;
    um.pre();
    if($scope.dataServer!=angular.toJson(this.dataNoIsf)) {
      // need to update
      $scope.updateStatus='Updating';
      //console.log("Updating login metadata",temp);
      if(!USE_AWS_LAMBDA) um.nonLambda(); else um.lambda();
    } else {
      if(sFn) sFn();
    }
  };

  $scope.newUStatus="None";
  num = new NewUserManager($scope);
  $scope.newU=function() {
    if($scope.loginInvalid(true)||!$scope.$parent.serverAvailable) return;
    $scope.newUStatus='Registering';
    if(!USE_AWS_LAMBDA) num.nonLambda(); else num.lambda();
  };

  $scope.logout = function () {
    window.localStorage.removeItem('loginU');
    $scope.loginReset();
    $scope.loginStatus='None';
    $scope.dataServer='{}';
  };

  angular.element(document).ready(function () {
    $scope.hideLogin();

    wlsgi1=window.localStorage.getItem('loginU');
    $scope.$apply(function() {
      if(wlsgi1!==null) {
        $scope.loginU=angular.fromJson(wlsgi1);
        // This is cancelled in favor of the "serverAvailable" event below // if($scope.$parent.serverAvailable) $scope.login();
      }
    });
  });

  $scope.$on('requestUpdate', function(event,fn) { $scope.update(); });
  $scope.$on('serverAvailable', function(event,fn) { $scope.login(); });

  $scope.showLogin=function() { $scope.loginType='Log in'; $('#loginModal').modal('show'); };
  $scope.hideLogin=function() { $('#loginModal').modal('hide'); };
  $scope.showNew=function() { $scope.loginType='New';    $('#loginModal').modal('show'); };

  $scope.forgotPasswordStatus=false;
  fpm = new ForgotPasswordManager($scope);
  $scope.forgotPassword=function() {
    if($scope.loginInvalid(true)||!$scope.$parent.serverAvailable) return;
    $scope.forgotPasswordStatus=true;
    if(!USE_AWS_LAMBDA) fpm.forgotPasswordNonLambda(); else fpm.forgotPasswordLambda();
  };


};
