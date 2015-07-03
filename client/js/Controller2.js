function Controller2($scope,$http) {

	$scope.loginStatus='None';
	$scope.dataServer='{}';

	$scope.loginReset=function() { $scope.loginU={email:'',pass:''}; };
	$scope.loginReset();

	$scope.loginInvalid=function(excludePass) {
		return ($scope.loginStatus!='None'&&$scope.loginStatus!='Logged in')||$scope.getStatus!='None'||!$scope.loginU.email||!(excludePass||$scope.loginU.pass);
	};
	$scope.login=function() {
		if($scope.loginInvalid()||!$scope.$parent.serverAvailable) return;

		$scope.loginStatus='Logging in';
		$.ajax({type:'POST',
			url: ZBOOTA_SERVER_URL+'/api/login.php',
			data: $scope.loginU,
			dataType: 'json',
			success: function(rt) {
				$scope.hideLogin();
				if(rt.hasOwnProperty("error")) {
					alert("Zboota login error: "+rt.error);
					$scope.$apply(function() { $scope.loginStatus='None'; });
					return;
				}
				$scope.$apply(function() {
					$scope.loginStatus='Logged in';
					// append data from server to here
					// Note that I tested that this line is not asynchronous
					$scope.$emit("requestAddCore",rt); // not sure if I can just call $scope.$parent.addCore directly because of $scope 

					window.localStorage.setItem('loginU',angular.toJson($scope.loginU));
					$scope.dataServer=angular.toJson(rt);

				});
				$scope.update(function() {
					$scope.$emit("loggedIn"); //$scope.$parent.get(); // retrieving data after login
				}); // updating with whatever was done while offline
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log("Error logging in. "+textStatus+","+errorThrown);
				$scope.$apply(function() { $scope.loginStatus='None'; });
				$scope.$parent.pingServer();
			}
		});
	};

	$scope.updateStatus='None';
	$scope.update=function(sFn) {
	// sFn: success callback function with no parameters

		if($scope.loginInvalid()||!$scope.$parent.serverAvailable) return;

		// check if any photos need to be uploaded
		toUpload=Object.keys($scope.$parent.data).filter(function(t) {
			return(
				$scope.$parent.photos.hasOwnProperty(t)&&
				!$scope.$parent.data[t].hasOwnProperty("photoUrl")
			);
		});
		if(toUpload.length>0) { 
			//console.log("Uploading unuploaded photo for ", toUpload);
			uploadPhotoAsDataUrl(toUpload);
		} else {
			// drop the ISF and PML data so that only the area, number, and label are stored
			// also drop the photo data url
			temp=angular.fromJson(angular.toJson($scope.$parent.data));
			for(t in temp) {
				temp2={'a':temp[t].a, 'n':temp[t].n, 'l':temp[t].l};
				if(temp[t].hp) temp2.hp=temp[t].hp;
				if(temp[t].y) temp2.y=temp[t].y;
				if(temp[t].t) temp2.t=temp[t].t;
				// do not upload photo as data URL because it borks the write capacity in dynamodb.
				// Instead, upload photo to S3 bucket with unique filename and add a new parameter that references this
				//if(temp[t].photo) temp2.photo=temp[t].photo;
				if(temp[t].photoUrl) temp2.photoUrl=temp[t].photoUrl;

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
//						alert("Error updating server. "+textStatus+","+errorThrown);
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
		} // end toUpload.length>0

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
	$scope.forgotPassword=function() {
		$scope.forgotPasswordStatus=true;
		$.ajax({type:'POST',
			url: ZBOOTA_SERVER_URL+'/api/forgotPassword.php',
			data: $scope.loginU,
			dataType: 'json',
			success: function(rt) {
				if(rt.hasOwnProperty("error")) {
					alert("Zboota forgot password error: "+rt.error);
					return;
				}
				alert("Your password has been emailed to you. Please check your email inbox, and possibly the junk mail folder, in a few minutes.");
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log("Error in forgot password. "+textStatus+","+errorThrown);
				$scope.$parent.pingServer();
			},
			complete: function() {
				$scope.hideLogin();
				$scope.$apply(function() { $scope.forgotPasswordStatus=false; });
			}
		});
	};

	// uploading photos only for logged in users
	$scope.$parent.uploadPhotoStatus=0;
	uploadPhotoAsDataUrlComplete=function() {
		$scope.$parent.uploadPhotoStatus=Math.max(0,$scope.$parent.uploadPhotoStatus-1);
		if($scope.$parent.uploadPhotoStatus==0) $scope.update(); // this would indicate the end of photos upload
	}
	uploadPhotoAsDataUrl=function(di2) {
	// di2: array of keys in $scope.$parent.data
		if($scope.loginInvalid()||!$scope.$parent.serverAvailable) return;
		if($scope.loginStatus!='Logged in') return;

		//console.log("Upload photo as data url ", di2);
		di2.map(function(di) {
			//console.log("Upload photo as data url ", di, $scope.$parent.data[di]);
			$scope.$parent.uploadPhotoStatus+=1;
			$http.post(	ZBOOTA_SERVER_URL+'/api/uploadPhotoAsDataUrl.php',
					{image_file:$scope.$parent.photos[di]},
				    {headers: {'Content-Type': 'application/x-www-form-urlencoded'}}
				).
				success( function(text) {
					$scope.$parent.data[di].photoUrl=text;
					uploadPhotoAsDataUrlComplete();
				}).
				error( function() {
					console.log("Error in upload",di);
					uploadPhotoAsDataUrlComplete();
					$scope.$parent.pingServer();
				});
		});
	};

};
