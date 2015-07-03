ZBOOTA_SERVER_URL="http://genesis.akikieng.com/zboota-server"

function an2id(a,n) { return a+'/'+n; }

Array.prototype.unique = function () {
    var r = new Array();
    o:for(var i = 0, n = this.length; i < n; i++)
    {
    	for(var x = 0, y = r.length; x < y; x++)
    	{
    		if(r[x]==this[i])
    		{
//                alert('this is a DUPE!');
    			continue o;
    		}
    	}
    	r[r.length] = this[i];
    }
    return r;
}
