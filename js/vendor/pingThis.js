// http://www.brighthub.com/internet/web-development/articles/96425.aspx

function pingThis(uri,callback1,callback2,callback3) {
return {
	img:null,
	timer:null,
	init:function() {
		callback1();
		this.callback2=callback2;
		this.callback3=callback3;
		var session = new Date();
		var dontCache = session.getTime();
		var imgLink = uri+"?time="+dontCache;//prevent JavaScript from using cached image
		this.img = new Image();
		var self=this;
		this.img.onload = function() {
			clearTimeout(self.timer);
			self.timer = null;
			self.callback3();//			alert("Domain is available");
		};
		this.img.src = imgLink;
		this.timer = setTimeout(function() { self.pingFailure(); },5000);//wait five seconds
	},
	pingFailure:function() {
		clearTimeout(this.timer);
		this.timer = null;
		this.img = null;
		this.callback2(); //alert("Domain is not available");
	}
};
}
