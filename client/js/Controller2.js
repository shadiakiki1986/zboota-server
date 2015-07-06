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
    if($scope.loginInvalid()||!$scope.$parent.serverAvailable) return;
    $scope.loginStatus='Logging in';
    if(!USE_AWS_LAMBDA) lm.loginNonLambda(); else lm.loginLambda();
  }

  $scope.updateStatus='None';
  $scope.update=function(sFn) {
  // sFn: success callback function with no parameters

    if($scope.loginInvalid()||!$scope.$parent.serverAvailable) return;

    // drop the ISF and PML data so that only the area, number, and label are stored
    temp=angular.fromJson(angular.toJson($scope.$parent.data));
    for(t in temp) {
      temp2={'a':temp[t].a, 'n':temp[t].n, 'l':temp[t].l};
      if(temp[t].hp) temp2.hp=temp[t].hp;
      if(temp[t].y) temp2.y=temp[t].y;
      if(temp[t].t) temp2.t=temp[t].t;

      temp[t]=temp2;
    }

    if($scope.dataServer!=angular.toJson(temp)) {
      // need to update
      $scope.updateStatus='Updating';
      //console.log("Updating login metadata",temp);
      $.ajax({type:'POST',
        url: ZBOOTA_SERVER_URL+'/api/update.php',
        data: {'email':$scope.loginU.email,'pass':$scope.loginU.pass,'lpns':angular.toJson(temp)},
        dataType: 'json',
        success: function(rt) {
          if(rt.hasOwnProperty("error")) {
            alert("Zboota update error: "+rt.error);
            return;
          }
          $scope.$apply(function() { $scope.dataServer=angular.toJson(temp); }); // match the two
        },
        error: function(jqXHR, textStatus, errorThrown) {
//            alert("Error updating server. "+textStatus+","+errorThrown);
          $scope.$parent.pingServer();
        },
        complete: function() { $scope.$apply(function() {
          $scope.updateStatus='None';
          if(sFn) sFn();
        }); }
      });
    } else {
      if(sFn) sFn();
    }
  };

  $scope.newUStatus="None";
  $scope.newU=function() {
    if($scope.loginInvalid(true)||!$scope.$parent.serverAvailable) return;

    $scope.newUStatus='Registering';
    $.ajax({type:'POST',
      url: ZBOOTA_SERVER_URL+'/api/new.php',
      data: {'email':$scope.loginU.email},
      dataType: 'json',
      success: function(rt) {
//console.log(rt);
        $scope.hideLogin();
        if(rt.hasOwnProperty("error")) {
          alert("Zboota new account error: "+rt.error);
          return;
        }
        alert("Please check your email in a few minutes (including possibly the junk mail folder) and log into the app using the random password in the email.");
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert("Error adding new account. "+textStatus+","+errorThrown);
        $scope.$parent.pingServer();
      },
      complete: function() { $scope.$apply(function() { $scope.newUStatus='None'; }); }

    });
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
