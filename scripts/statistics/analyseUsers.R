# R < analyseUsers.R  --no-save --slave "--args 20150508103303"

fnx=eval(commandArgs()[5])
#fn1="~/.zboota-server/ddb-bkps/bkp-zboota-users-20150508103303.json"
#fn2="~/.zboota-server/ddb-bkps/bkp-zboota-cars-20150508103303.json"
fn1=sprintf("~/.zboota-server/ddb-bkps/bkp-zboota-users-%s.json",fnx)
fn2=sprintf("~/.zboota-server/ddb-bkps/bkp-zboota-cars-%s.json",fnx)

x=jsonlite::fromJSON(fn1)
y=lapply(names(x), function(z) x[[z]][["S"]])
names(y)=names(x)
y$passFail=NULL
y=as.data.frame(y,stringsAsFactors=F)

y$lastloginDate=ifelse(y$lastloginDate=="-",y$registrationDate,y$lastloginDate)
y$lld=as.Date(y$lastloginDate)
y$rd=as.Date(y$registrationDate)
y$d=y$lld-y$rd

w=y[y$d>1,c("email","lastloginDate","registrationDate")]
cat(sprintf("------------------\nUsers who returned: %i\n", nrow(w)))
w

#y[y$email=="upicleb@yahoo.com",c("email","lpns")]
#y[y$email=="shadiakiki1986@gmail.com",c("email","lastloginDate")]

w=y[which(sapply(1:nrow(y),function(z) any(sapply(jsonlite::fromJSON(y[z,"lpns"]),function(x) "hp"%in%names(x))))),c("email"),drop=F]
cat(sprintf("----------------------\nUsers with mechanique data: %i\n",nrow(w)))
w

#########################
##############################


x=jsonlite::fromJSON(fn2)
y=lapply(names(x), function(z) x[[z]][["S"]])
names(y)=names(x)
y=as.data.frame(y,stringsAsFactors=F)

z=y[!is.na(y$hp),c("id","l","emails")]
cat(sprintf("----------------------\nCars with mechanique data: %i\n",nrow(z)))
z

