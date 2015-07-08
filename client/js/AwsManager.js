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

  if(this.status!="connected") return; // silent

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

}; // end invokeLambda
