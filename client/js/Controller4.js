function Controller4($scope,$http) {


	$scope.$parent.redundantUpDownStatus=false;
	$scope.addPhoto=function() {
	    if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
	      alert('The File APIs are not fully supported in this browser.');
	      return;
	    }   

	    input = document.getElementById('image1');
	    if (!input) {
	      alert("Um, couldn't find the fileinput element.");
	    }
	    else if (!input.files) {
	      alert("This browser doesn't seem to support the `files` property of file inputs.");
	    }
	    else if (!input.files[0]) {
	      alert("Please select a file before clicking 'Load'");               
	    }
	    else {
	      file = input.files[0];

/*
	The type check doesn't work in android. I imagine the size check also doesn't. Skipping

		//allow only valid image file types
		switch(file.type)
		{
		    case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
			break;
		    default:
			alert("Unsupported image type "+file.type+". Please use png, gif, jpeg, or pjpeg");
			return false
		}

		//Allowed file size is less than 1 MB (1048576)
		if(file.size>1048576)
		{   
		    alert("Too big Image file! Please reduce the size of your photo using an image editor.");
		    return false
		}
*/
		var filerdr = new FileReader();
		filerdr.onload = function(e) {
			$scope.$apply(function() { $scope.$parent.addC.photo=e.target.result; });
			// Redundant/Silent upload and download from the server
			// because displaying the photo in my tablet doesn't work
			// Otherwise this should have worked without this
			$scope.$parent.redundantUpDownStatus=true;
			$http.post(	ZBOOTA_SERVER_URL+'/api/uploadPhotoAsDataUrl.php',
					{image_file:e.target.result},
				    {headers: {'Content-Type': 'application/x-www-form-urlencoded'}}
				).
				success( function(text) {
console.log("redundant upload done",text);
					$scope.$parent.addC.photoUrl=text;
					$http.get(ZBOOTA_SERVER_URL+'/api/loadPhoto.php?name='+text)
						.success( function(rt) {
console.log("redundant download done");
							$scope.$parent.addC.photo=rt;
							$scope.$parent.redundantUpDownStatus=false;
						}).
						error( function(rt,et,ts) {
							console.log("Failed to get redundant photo "+text);
							$scope.$parent.redundantUpDownStatus=false;
						});

				}).
				error( function() {
					console.log("Error in redundant upload");
					$scope.$parent.redundantUpDownStatus=false;
				});

		}
		filerdr.readAsDataURL(input.files[0]);


		// drop the photoUrl field if any, indicating that the URL is no longer valid, since this is a different photo
		delete $scope.$parent.addC.photoUrl;

	      }
	};

	$scope.rmPhoto=function() {
		delete $scope.$parent.addC.photo;
		delete $scope.$parent.addC.photoUrl;
	}


	$scope.photoshow4=function() {
		if(!$scope.$parent.addC) {
			return false;
		} else if( !$scope.$parent.addC.hasOwnProperty('photo')) return false; else return $scope.$parent.addC.photo;
	};

};
