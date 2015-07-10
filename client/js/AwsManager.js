var AwsManager = function($scope) {
  this.status = "disconnected";
} // end class

AwsManager.prototype.connect = function(cbFn,cbErr) {
  var self=this;
  switch(this.status) {
    case "disconnected":
      console.log("AWS Cognito: disconnected. Will try to connect");
      this.status="connecting";
  
      // Make the call to obtain credentials
      AWS.config.credentials.get(function(err){
        if(err) { 
          self.status="disconnected";
          if(cbErr!=null) cbErr(err);
          return;
        }

        self.status="connected";

        // change the state to disconnected after 14 minutes since the Cognito token expires in 15 minutes
        // Check related note in Controller1 / document ready function where I set the httpOptions timeout to 5000 milliseconds
        setTimeout(function() { self.status="disconnected"; console.log("Manually setting status to disconnected"); }, 14*60000); 

        console.log("AWS Cognito connected",err);
        self.accessKeyId = AWS.config.credentials.accessKeyId;
        self.secretAccessKey = AWS.config.credentials.secretAccessKey;
        self.sessionToken = AWS.config.credentials.sessionToken;
  
        self.ncheck = 0; // reset
        if(cbFn!=null) cbFn();
      });
      break;
    case "connecting":
       console.log("Got connecting .. wtf");
       break;
    case "connected":
      if(cbFn!=null) cbFn();
      break;
    default:
      console.log("Undefined status state");
  }

};

AwsManager.prototype.invokeLambda = function(lfn,lp,cbFn) {
// lfn: lambda function name
// lp: lambda payload, javascript object, before JSON.stringify
// cbFn: callback function, should accept err and data

  this.connect(function() {

    console.log("cognito connected, now lambda invoke");
    // zboota-app IAM user
    var lambda = new AWS.Lambda({
        'accessKeyId' : this.accessKeyId,
        'secretAccessKey'  : this.secretAccessKey,
        'sessionToken' : this.sessionToken,
        'region'  : "us-west-2"
    });
  
    // http://docs.aws.amazon.com/AWSJavaScriptSDK/latest/AWS/Lambda.html#invoke-property
    var params = {
      FunctionName: lfn, /* required */
      Payload: JSON.stringify(lp)
    };
    lambda.invoke(params, cbFn);

  });

}; // end invokeLambda
