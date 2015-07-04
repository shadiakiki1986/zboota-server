function Controller1($scope, $http) {

	$scope.data={};
	$scope.photos={};

	$scope.getStatus="None";

	$scope.dataTs=null;
	dataTsAll=[];
	getParN=0;
	getParStatus={};
	$scope.getParStatusFn=function(a,n) { return getParStatus[an2id(a,n)]; };
	$scope.getPar = function() {
		// wrapper around $scope.get to call it in parallel
		if(!$scope.serverAvailable) return;
		if(Object.keys($scope.data).length==0) return;

		$scope.getStatus="Requesting";
		dataTsAll=[];
		getParN=0;
		for(var x in $scope.data) {
			get($scope.data[x],x);
		}
	}
	$scope.$on('loggedIn', function(event) { $scope.getPar(); });
	$scope.getError={};
	$scope.getErrorAny=function() { return Object.keys($scope.getError).length>0; };

	get=function(dk,k) {
		if(false) getNonLambda(dk,k); else getLambda(dk,k);
	};

	getNonLambda = function(dk,k) {
	// dk: entry from $scope.data to retrieve
	//  k: key from $scope.data corresponding to dk
	// Note: Also check getLambda function below
		if(!$scope.serverAvailable) return;
		if(Object.keys($scope.data).length==0) return;
		getParStatus[k]=true;

		$http({method:'POST',
			url: ZBOOTA_SERVER_URL+'/api/get2.php',
			data: {lpns:JSON.stringify([dk])},
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).
			success( function(rt) { $scope.getCore(rt,k); } ).
			error( function(et) {
				getParN+=1;

				console.log("Error getting zboota from server. "+et);
				getParStatus[k]=false;
				if(getParN==Object.keys($scope.data).length) $scope.getStatus="None";
				$scope.pingServer();
			})
		;
	};

	$scope.getCore = function(rt,k) {
	// rt: return value from my api on success

		//console.log("got data",rt);
		getParN+=1;

		if(rt.hasOwnProperty("error")) {
			//alert("We're having trouble getting your car's zboota from the servers. Please try again later.");
			//console.log("error, "+rt.error);
			$scope.getError[k]=rt.error;
		} else {
			delete $scope.getError[k];
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
				// When all are retrieved, save to local storage
				if(getParN==Object.keys($scope.data).length) {
					window.localStorage.setItem('data',angular.toJson($scope.data));
					window.localStorage.setItem('dataTs',angular.toJson($scope.dataTs));
				}
			}
		}
		getParStatus[k]=false;
		if(getParN==Object.keys($scope.data).length) $scope.getStatus="None";
	};

	$scope.dataHas=function(a,n) { return $scope.data.hasOwnProperty(an2id(a,n)); };
	$scope.del=function(a,n) {
		id=an2id(a,n);
		delete $scope.data[id];
		delete $scope.photos[id];
		window.localStorage.setItem('data',angular.toJson($scope.data));
		window.localStorage.setItem('photos',angular.toJson($scope.photos));
		$scope.$broadcast('requestUpdate');
		if($scope.noData2()) {
			$scope.dataTs=null;
			window.localStorage.removeItem('dataTs');
		}
		delete $scope.getError[id];
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
		$scope.getPar(); // to get info of newly added car or edited car
		$scope.addReset();
		$scope.hideAdd();
	};

	$scope.addCore=function(xxx,isChild) {
	// xxx:   {"n":n,"a":a,"l":l};
		myscope=$scope;
		if(myscope.editStatus) if(myscope.editStatus!=an2id(xxx.a,xxx.n)) {
			delete myscope.data[myscope.editStatus]; // this is the case when the editing involves change the area code and/or number
			delete myscope.photos[myscope.editStatus];
		}
		if(xxx.hasOwnProperty("y")&&xxx.y=="") delete xxx.y;
		if(xxx.hasOwnProperty("hp")&&xxx.hp=="") delete xxx.hp;
		if(xxx.hasOwnProperty("t")&&xxx.t=="") delete xxx.t;

		// split out photo
		photo=null;
		if(xxx.hasOwnProperty("photo")) { photo=xxx.photo; delete xxx.photo; }

		// check if need to get image
		// including in case where the image stored in localStorage is the dataurl of the original image, hence reloading the image from the server can yield a shorter dataurl
		// This also serves that the image was not showing up on my tablet
		id=an2id(xxx.a,xxx.n);
//console.log(id,xxx.photoUrl,myscope.data[id].photoUrl);
		if(xxx.hasOwnProperty('photoUrl') && (!myscope.data.hasOwnProperty(id) || myscope.data[id].photoUrl!=xxx.photoUrl || !myscope.photoshow1(xxx.a,xxx.n) || xxx.photoUrl.length>180000)) {
			console.log("Need to get photo "+xxx.photoUrl+" for "+id);

			// http://stackoverflow.com/a/16566198
			// but https://html.spec.whatwg.org/multipage/scripting.html#dom-canvas-todataurl
			/*
			var img = new Image();
			img.onload = function () {
				var canvas = document.createElement("canvas");
				canvas.width =this.width;
				canvas.height =this.height;
				var ctx = canvas.getContext("2d");
				ctx.drawImage(this, 0, 0);
				var dataURL = canvas.toDataURL("image/png");
				myscope.$apply(function() { myscope.photos[an2id(xxx.a,xxx.n)]=dataURL; });
			};
			img.src = ZBOOTA_SERVER_URL+'/api/loadPhoto.php?name='+xxx.photoUrl;
			*/
			$http.get(ZBOOTA_SERVER_URL+'/api/loadPhoto.php?name='+xxx.photoUrl)
				.success( function(rt) {
					id=an2id(xxx.a,xxx.n);
					myscope.photos[id]=rt;
					window.localStorage.setItem('photos',angular.toJson(myscope.photos));
				}).
				error( function(rt,et,ts) {
					console.log("Failed to get photo "+xxx.photoUrl);
					$scope.pingServer();
				});

		} // end check if need to get image
		if(photo!=null) {
			// just added photo
			myscope.photos[id]=photo;
		} else {
			if(!isChild) {
				// photo must have been deleted
				// Cannot replace this with $scope.photoshow1 due to scope and calling this from Controller2
				if(myscope.photoshow1(xxx.a,xxx.n)) { 
					delete myscope.photos[id];
				}
			}
		}

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
		window.localStorage.setItem('photos',angular.toJson(myscope.photos));

		if(!isChild) myscope.$broadcast('requestUpdate');


	}; // end addCore

	MAX_N_PING=3;
	$scope.pingStatus={a:0,b:0,n:0};
	$scope.pingServer=function(force) {
		$scope.serverAvailable=false;

		if(force) $scope.pingStatus.n=0; // reset counter
		$scope.pingStatus.n+=1;
		if($scope.pingStatus.n>MAX_N_PING) {
			// disable pinging so as to avoid infinite loop of ping, server available, login, get, get error for some reason, ping, server available, login, get, get error, ping, ...
			console.log("max ping reached");
			return; 
		}
		$scope.pingStatus.b=1;
		$http.get(ZBOOTA_SERVER_URL+'/api/get.php', {timeout:5000}).
			success( function(rt) {
				$scope.serverAvailable=true;
				$scope.$broadcast('serverAvailable');
				$scope.pingStatus.b=0;
				$scope.pingStatus.a=1;
			}).
			error( function(et) {
				$scope.serverAvailable=false;
				$scope.pingStatus.b=2;
				$scope.pingStatus.a=1;
				//alert("Server"+ZBOOTA_SERVER_URL+" unavailable. "+et+";");
			})
		;
	};

	angular.element(document).ready(function () {
		$scope.hideAdd();

		$scope.pingServer();
		wlsgi1=window.localStorage.getItem('data');
		wlsgi2=window.localStorage.getItem('dataTs');
		photos=window.localStorage.getItem('photos');

		$scope.$apply(function() {
			if(wlsgi1!==null) { $scope.data=angular.fromJson(wlsgi1); }
			if(wlsgi2!==null) { $scope.dataTs=angular.fromJson(wlsgi2); }
			if(photos!==null) { $scope.photos=angular.fromJson(photos); }
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
		ps1=$scope.photoshow1(a,n);
		if(ps1) $scope.addC.photo=angular.fromJson(angular.toJson(ps1)); // recuperating photo
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

	$scope.photoshow1=function(a,n) {
		id=an2id(a,n);
		if(!$scope.photos.hasOwnProperty(id)) return false; else return $scope.photos[id];
	};

	getLambda = function(dk,k) {
	// same as get function, but using AWS Lambda
	// dk: entry from $scope.data to retrieve
	//  k: key from $scope.data corresponding to dk
		if(!$scope.serverAvailable) return;
		if(Object.keys($scope.data).length==0) return;
		getParStatus[k]=true;

		// cognito role
		// Initialize the Amazon Cognito credentials provider
		AWS.config.region = 'us-east-1'; // Region
		AWS.config.credentials = new AWS.CognitoIdentityCredentials({
		    IdentityPoolId: 'us-east-1:639fd2a8-8277-4726-b9b3-3231ed0d5f71',
		});

		// Make the call to obtain credentials
		AWS.config.credentials.get(function(err){
		    if (err) {
			console.log("Error: "+err);
			return;
		    }

		    //     console.log("Cognito Identity Id: " + AWS.config.credentials.identityId);

		    // Credentials will be available when this function is called.
		    var accessKeyId = AWS.config.credentials.accessKeyId;
		    var secretAccessKey = AWS.config.credentials.secretAccessKey;
		    var sessionToken = AWS.config.credentials.sessionToken;

			// zboota-app IAM user
			var lambda = new AWS.Lambda({
			    'accessKeyId' : accessKeyId,
			    'secretAccessKey'  : secretAccessKey,
			    'sessionToken' : sessionToken,
			    'region'  : "us-west-2"
			});

			// http://docs.aws.amazon.com/AWSJavaScriptSDK/latest/AWS/Lambda.html#invoke-property
			var params = {
			  FunctionName: 'zboota-get', /* required */
			  Payload: angular.toJson([dk])
			};
			lambda.invoke(params, function(err, data) {
			  if (err||data.StatusCode!=200) {
				console.log("Error getting zboota from server.");
				console.log(err, err.stack); // an error occurred
				getParN+=1;

				getParStatus[k]=false;
				if(getParN==Object.keys($scope.data).length) $scope.getStatus="None";
				$scope.pingServer();
			  } else {
				rt=angular.fromJson(data.Payload);
				//console.log("Success in getting zboota from server");
				//console.log(rt);           // successful response
				$scope.$apply(function() { $scope.getCore(rt,k); });
			  }
			});

		});


	};

};
