function Controller1($scope, $http) {

  $scope.data={};
  $scope.awsMan = null;
  $scope.getStatus="None";
  $scope.getError=false;

  $scope.dataTs=null;
  dataTsAll=[];
  $scope.get = function() {
    // wrapper around $scope.get to call it in parallel
    if(!$scope.serverAvailable) return;
    if(Object.keys($scope.data).length==0) return;

    $scope.getStatus="Requesting";
    dataTsAll=[];

    // non-parallel retrieval
    dk=$scope.data;
    if(!$scope.serverAvailable) return;
    if(Object.keys(dk).length==0) return;

    // drop isf, pml, dm fields before submission
    dk2=angular.fromJson(angular.toJson(dk));
    Object.keys(dk2).forEach(function(k) {
      if(dk2[k].hasOwnProperty("isf")) delete dk2[k].isf;
      if(dk2[k].hasOwnProperty("pml")) delete dk2[k].pml;
      if(dk2[k].hasOwnProperty("dm" )) delete dk2[k].dm ;
    });

    if(!USE_AWS_LAMBDA) getNonLambda(dk2); else getLambda(dk2);
  }

  $scope.$on('loggedIn', function(event) { $scope.get(); });


  getNonLambda = function(dk) {
  // dk: associative array of entries from $scope.data to retrieve
  // Note: Also check getLambda function below

    // convert dk to non-associative array
    dk2=Object.keys(dk).map(function(x) { return dk[x]; });

    $http({method:'POST',
      url: ZBOOTA_SERVER_URL+'/api/get2.php',
      data: {lpns:JSON.stringify(dk2)},
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).
      success( function(rt) { $scope.getCore(rt,Object.keys(dk)); } ).
      error( function(et) { $scope.getCoreError(et); });
  };

  $scope.getCoreError = function(et) {
      console.log("Error getting zboota from server. "+et);
      $scope.getError=et;
      $scope.getStatus="None";
      if(et=="Timeout") $scope.pingServer(false,true); else $scope.pingServer();
  };

  $scope.getCore = function(rt,ks) {
  // rt: return value from my api on success
  // ks: array of keys of originally passed associative array

    console.log("got data",rt);

    if(rt.hasOwnProperty("errorMessage")) {
      rt.error = rt.errorMessage;
      delete rt.errorMessage;
    }

    if(rt.hasOwnProperty("error")) {
      //alert("We're having trouble getting your car's zboota from the servers. Please try again later.");
      //console.log("error, "+rt.error);
      $scope.getError=rt.error;
    } else {
      $scope.getError=false;
      if(Object.keys(rt).length>0) {
        for(var i in rt) {
          $scope.data[i].isf=rt[i].isf;
          $scope.data[i].pml=rt[i].pml;
          if(rt[i].dm) {
            $scope.data[i].dm=rt[i].dm;
          } else {
            if($scope.data[i].hasOwnProperty("dm") && $scope.data[i].dm!="") delete $scope.data[i].dm;
          }
          dataTsAll.push(moment(rt[i].dataTs,'YYYY-MM-DD h:mm:ss').format('YYYY-MM-DD'));
        }
        $scope.dataTs=new Date(dataTsAll.unique().sort()[0]);//new Date();

        window.localStorage.setItem('data',angular.toJson($scope.data));
        window.localStorage.setItem('dataTs',angular.toJson($scope.dataTs));
      }
    }
    $scope.getStatus="None";
  };

  $scope.dataHas=function(a,n) { return $scope.data.hasOwnProperty(an2id(a,n)); };
  $scope.del=function(a,n) {
    id=an2id(a,n);
    delete $scope.data[id];
    window.localStorage.setItem('data',angular.toJson($scope.data));
    $scope.$broadcast('requestUpdate');
    if($scope.noData2()) {
      $scope.dataTs=null;
      window.localStorage.removeItem('dataTs');
    }
  };
  $scope.momentFormat1=function(a) { return moment(a).format('MMMM Do YYYY, h:mm:ss a'); };
  $scope.momentFormat2=function(a) { return moment(a).format('YYYY-MM-DD'); };

  $scope.noData=function() { return Object.keys($scope.data).length==0; };
  $scope.noData2=function() { return Object.keys($scope.data)
      .map(function(x) { return $scope.data[x].isf; })
      .filter(function(x) { return x!='-'; })
      .length==0; };

  $scope.areas=["B","G","R","Z","S","T","D","J","M","N","O"];
  $scope.cartypes=["","Private cars", "Motorcycles", "Mass public transport trucks", "Taxis", "Public buses & minibuses", "Private transport vehicles", "Other private vehicles: Ambulances, etc..."];
  $scope.horsepowers=["","1 - 10", "11-20", "21-30", "31-40", "41-50", "51 and above"];
  $scope.years=["","2015", "2014", "2013", "2012", "2011", "2010", "2009", "2008", "2007", "2006", "2005", "2004", "2003", "2002", "2001 and before"];

  $scope.addC={'n':'','a':'','l':''};
  $scope.addReset=function() {
    $scope.addC={'n':'','a':'','l':''};
    $scope.editStatus=false;
    input = document.getElementById('image1');
    if(input!=null) input.value="";

  };

  $scope.add=function() {
    $scope.addCore($scope.addC,false);
    $scope.get(); // to get info of newly added car or edited car
    $scope.addReset();
    $scope.hideAdd();
  };

  $scope.addCore=function(xxx,isChild) {
  // xxx:   {"n":n,"a":a,"l":l};
    myscope=$scope;
    if(myscope.editStatus) if(myscope.editStatus!=an2id(xxx.a,xxx.n)) {
      delete myscope.data[myscope.editStatus]; // this is the case when the editing involves change the area code and/or number
    }
    if(xxx.hasOwnProperty("y")&&xxx.y=="") delete xxx.y;
    if(xxx.hasOwnProperty("hp")&&xxx.hp=="") delete xxx.hp;
    if(xxx.hasOwnProperty("t")&&xxx.t=="") delete xxx.t;

    id=an2id(xxx.a,xxx.n);

    // Instead of myscope.data[id]=angular.fromJson(angular.toJson(xxx))
    // which overwrites the isf, pml, and dm fields
    // split them out field by field.
    // Ideally, I would gather isf, pml, and dm under a field "meta",
    // but I'm too lazy
    temp=angular.fromJson(angular.toJson(xxx));
    if(myscope.data.hasOwnProperty(id)) {
      myscope.data[id].a=temp.a;
      myscope.data[id].n=temp.n;
      myscope.data[id].l=temp.l;
      myscope.data[id].hp=temp.hp;
      myscope.data[id].y=temp.y;
      myscope.data[id].t=temp.t;
    } else {
      myscope.data[id]=temp;
    }

    window.localStorage.setItem('data',angular.toJson(myscope.data));

    if(!isChild) myscope.$broadcast('requestUpdate');


  }; // end addCore

  MAX_N_PING=3;
  $scope.pingStatus={a:0,b:0,n:0};
  pingSuccess = function(rt,skipBroadcastServerAvailable) {
    $scope.$apply(function() {
      $scope.serverAvailable=true;
      if(!skipBroadcastServerAvailable) $scope.$broadcast('serverAvailable');
      $scope.pingStatus.b=0;
      $scope.pingStatus.a=1;
    });
  };
  pingError = function(et) {
    $scope.$apply(function() {
      $scope.serverAvailable=false;
      $scope.pingStatus.b=2;
      $scope.pingStatus.a=1;
      //alert("Server"+ZBOOTA_SERVER_URL+" unavailable. "+et+";");
      console.log("Error: ",et);
    });
  };
  $scope.pingServer=function(force,skipBroadcastServerAvailable) {
    console.log("pingServer");
    $scope.serverAvailable=false;
    $scope.awsMan.status="disconnected";

    if(force) $scope.pingStatus.n=0; // reset counter
    $scope.pingStatus.n+=1;
    if($scope.pingStatus.n>MAX_N_PING) {
      // disable pinging so as to avoid infinite loop of ping, server available, login, get, get error for some reason, ping, server available, login, get, get error, ping, ...
      console.log("max ping reached");
      return; 
    }
    $scope.pingStatus.b=1;
    if(!USE_AWS_LAMBDA) {
	    $http.get(ZBOOTA_SERVER_URL+'/api/get.php', {timeout:5000})
	      .success( pingSuccess )
	      .error( pingError );
    } else {
      $scope.awsMan.connect(function() {
        $scope.awsMan.invokeLambda("zboota-get",[{"n":"123","a":"B"}],function(err,data) {
          console.log("lambda invoke conclusion",err,data);
          if(err) pingError(err.message); else pingSuccess(data,skipBroadcastServerAvailable);
        });
      },pingError);
    }

  };

  angular.element(document).ready(function () {
    $scope.hideAdd();

    // cognito role
    // Initialize the Amazon Cognito credentials provider
    AWS.config.region = 'us-east-1'; // Region
    AWS.config.credentials = new AWS.CognitoIdentityCredentials({
	IdentityPoolId: 'us-east-1:639fd2a8-8277-4726-b9b3-3231ed0d5f71',
    });
    // note that this is only the timeout for making a connection.
    // The timeout for the tokens is by default 15 minutes, as documented here under TokenDuration
    // http://docs.aws.amazon.com/AWSJavaScriptSDK/latest/AWS/CognitoIdentity.html
    // That is why I run a setTimeout in AwsManager to change the connection status to disconnected 15 minutes after the initial connection
    AWS.config.httpOptions = { timeout: 5000 }; 
    $scope.awsMan = new AwsManager();

    // proceed
    wlsgi1=window.localStorage.getItem('data');
    wlsgi2=window.localStorage.getItem('dataTs');
    if(window.localStorage.getItem('dataTs')!==null) window.localStorage.removeItem('photos');

    $scope.$apply(function() {
      if(wlsgi1!==null) { $scope.data=angular.fromJson(wlsgi1); }
      if(wlsgi2!==null) { $scope.dataTs=angular.fromJson(wlsgi2); }
    });
    setInterval(function() { $scope.$apply(function() { $scope.tnow=new Date();}); }, 60000);

    $("#addC_n_error").hide();
    $('#addC_n').change(function() {
      if(!$.isNumeric($("#addC_n").val())) {
        $("#addC_n").parent().addClass("has-error");
        $("#addC_n_error").show();
      } else {
        $("#addC_n").parent().removeClass("has-error");
        $("#addC_n_error").hide();
      }
    });

    //$scope.pingServer();
    setTimeout($scope.pingServer,5000); // delaying 5 seconds before connecting
    //pingError(""); // dummy throw error on ping explicitly

  });

  $scope.$on('requestAddCore', function(event,fns) { for(var i in fns) $scope.addCore(fns[i],true); });

  $scope.showAdd=function() { $('#addModal').modal('show'); };
  $scope.hideAdd=function() { $('#addModal').modal('hide'); };
  $scope.getCarRowClass=function(a,n) {
    temp=$scope.data[an2id(a,n)];
    if(temp.isf=='Not available'||temp.pml=='Not available'||mechIsCurrentMonth(a,n)||temp.dm=="There are no results matching the specifications you've entered...") {
      return "info";
    } else {
      if(temp.isf!='None'||temp.pml!='None'||mechIsCurrentMonth(a,n)) {
        return "danger"; //lightpink"; 
      } else {
        return ""; //orange";
      }
    }
  };

  $scope.dataDateVsToday=function() {
    if($scope.momentFormat2($scope.tnow)!=$scope.momentFormat2($scope.dataTs)) return "text-danger bg-danger"; else return "";
  };

  $scope.editStatus=false;
  $scope.edit=function(a,n) {
    $scope.editStatus=an2id(a,n);
    $scope.addC=angular.fromJson(angular.toJson($scope.data[an2id(a,n)]));
    $scope.showAdd();
  };
  $scope.addCisInvalid=function() {
    var addC=$scope.addC;
    if(addC) {
      return !addC.a||!addC.n||!addC.l||!((!addC.y&&!addC.hp&&!addC.t)||(addC.y&&addC.hp&&addC.t))||!$.isNumeric(addC.n);
    } else {
      return true;
    }
  };

  mechIsCurrentMonth=function(a,n) {
    d=$scope.data[an2id(a,n)];
    if(!d.dm) {
      return false;
    } else {
      if(d.dm=="Mechanique: There are no results matching the specifications you've entered...") {
        return false;
      } else {
        m=d.dm.replace(/.* LL, due in (.*), mandatory inspection: .*/g, "$1");
        m2=new Date().getMonth();
        months=["January","February","March","April","May","June","July","August","September","October","November","December"];
        return(m==months[m2]);
      }
    }
  };

  getLambda = function(dk) {
  // same as get function, but using AWS Lambda
  // dk: entry from $scope.data to retrieve
  //  k: key from $scope.data corresponding to dk

      // convert dk to non-associative array
      dk2=Object.keys(dk).map(function(x) { return dk[x]; });

      $scope.awsMan.invokeLambda('zboota-get',dk2,function(err, data) {
        if (err||data.StatusCode!=200) {
          $scope.getCoreError(err.message); 
        } else {
          rt=angular.fromJson(data.Payload);
          //console.log("Lambda Success in getting zboota from server");
          //console.log(rt);           // successful response
          $scope.$apply(function() { $scope.getCore(rt,Object.keys(dk)); });
        }
      });

  };

};
