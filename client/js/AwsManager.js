var AwsManager = function() {
  this.status = "disconnected";
  this.ncheck = 0;

} // end class

AwsManager.prototype.connect = function(cbFn) {
  var self=this;
  if(this.status=="disconnected") {
    this.status="connecting";
    //console.log("AWS Cognito connecting");

    // Make the call to obtain credentials
    AWS.config.credentials.get(function(err){
      if (err) {
        console.log("Error: "+err);
        return;
      }
      self.status="connected";
      //console.log("AWS Cognito connected");
      self.accessKeyId = AWS.config.credentials.accessKeyId;
      self.secretAccessKey = AWS.config.credentials.secretAccessKey;
      self.sessionToken = AWS.config.credentials.sessionToken;

      if(cbFn!=null) cbFn();
    });
  }
};

AwsManager.prototype.invokeLambda = function(lfn,lp,cbFn) {
// lfn: lambda function name
// lp: lambda payload, javascript object, before JSON.stringify
// cbFn: callback function, should accept err and data

  var self=this;
  switch(this.status) {
  case "disconnected":
    //console.log("AWS Cognito: disconnected. Will connect");
    this.connect(function() { self.invokeLambda(lfn,lp,cbFn); });
    return;
  case "connecting":
    this.ncheck++;
    //console.log("Already connecting, re-attempt", this.ncheck);
    if(this.ncheck<5) {
      setTimeout(function() { self.invokeLambda(lfn,lp,cbFn); },1000);
    } else {
      console.log("Aborting waiting for aws cognito connect");
    }
    return;
  }

  this.ncheck = 0; // reset
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

};
