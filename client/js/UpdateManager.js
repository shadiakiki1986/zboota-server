var UpdateManager = function($scope) {

  this.dataNoIsf = {};
  this.sFn = false;

  this.pre=function() {

    // drop the ISF and PML data so that only the area, number, and label are stored
    temp=angular.fromJson(angular.toJson($scope.$parent.data));
    for(t in temp) {
      temp2={'a':temp[t].a, 'n':temp[t].n, 'l':temp[t].l};
      if(temp[t].hp) temp2.hp=temp[t].hp;
      if(temp[t].y) temp2.y=temp[t].y;
      if(temp[t].t) temp2.t=temp[t].t;

      temp[t]=temp2;
    }
    this.dataNoIsf = temp;
  };

  this.core = function(rt) {
    if(rt.hasOwnProperty("error")) {
      alert("Zboota update error: "+rt.error);
      return;
    }
    var self=this;
    $scope.$apply(function() { $scope.dataServer=angular.toJson(self.dataNoIsf); }); // match the two
  };

  this.complete = function() {
    var self=this;
        $scope.$apply(function() {
          $scope.updateStatus='None';
          if(self.sFn) self.sFn();
        });
  };

  this.error = function(msg) {
//            alert("Error updating server. "+textStatus+","+errorThrown);
          console.log(msg);
          $scope.$parent.pingServer();
  };

  this.nonLambda = function() {
      var self=this;
      $.ajax({type:'POST',
        url: ZBOOTA_SERVER_URL+'/api/update.php',
        data: {'email':$scope.loginU.email,'pass':$scope.loginU.pass,'lpns':angular.toJson(this.dataNoIsf)},
        dataType: 'json',
        success: this.core,
        error: function(jqXHR, textStatus, errorThrown) {
          self.error("Error updating server. "+textStatus+","+errorThrown);
        },
        complete: this.complete
      });
  };

  this.lambda = function() {
    var self=this;
    $scope.$parent.awsMan.invokeLambda(
      "zboota-update",
      { email: $scope.loginU.email,
        pass: $scope.loginU.pass,
        lpns: angular.toJson(this.dataNoIsf)
      },
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
        self.core(rt);
        self.complete();
    });

  };

}; // end class
