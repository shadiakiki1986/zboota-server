=Ver 0.0.3=
2015-05-18
* added/improved some tests

2015-05-17
* photos: moving from using local folder to S3 bucket + tested
* added backup photos script + tested
* added line output in log after each sync

2015-05-16
* added statistic scripts on live data (versus analyse.R on bkp files)

2015-05-15
* force update a car if it is available in the ddb but with old data
* split out uploadPhotoCore from uploadPhoto
** also added uploadPhotoAsDataUrl
** also simplified core
* added scipts listEmpty, etc
* removed trailing period from email sentence of forgot password and new email
* get2 is same as get but for angular get
** I need to keep get because current apps use it
** I should decomission it when no apps use it anymore
* loading photo as dataurl instaed of image because of javascript security exceptino in the latter case
* somehow I had accidentally removed the conversion of errors to json from login.php
** I remember it was when I saw an error that the user was not supposed to see
** so I judged that I should keep errors internal
** but now I realize that some errors are supposed to bubble up to the user, such as wrong password, or unregistered email
* some CORS, angular http, jquery ajax, php crap

2015-05-13
* suppress html parsing warnings
* can upload photos and download them
* removed error returned as json and kept as internal server error
* removing empty fields before sending to dynamodb: made more general to any field, and not just the label, area, or number
* Changelog: removed double equal from dates
* new.php, update.php: adding isset(argc) to avoid the warning when used from html

2015-05-08
* not force refreshing data in case hp/y/t data missing
** just remove dm before returning to user
* added test to see service availability
* api minor improvements of check variables at start to avoid notices
* furthered test_09_connectivity.sh

2015-05-07
* case of cached data modified with the mechanique info removed should not return mechanique results ... done
* case of timeout frmo isf, pml, dawlati servers => returmn 'Not available' to client instead of internal server error
* minor notice corrections
* added header message
* added thousands separator to mechanique value

2015-05-06
* adding mechanique information from dawlati.gov.lb
* added force to get.php
* ok to go live with dawlati mechanique schedule
* added check if car modified in getCore.php and forcing sync if so
* added case of no results found to syncCoreDawlati...

=Ver 0.0.2=
2015-05-04
* include AWS_PHAR instead of require_once ...
* added backing up data weekly

2015-04-24
* dropUnconfirmed: changed to 30 days instead of 7 days
* added timestamp to log entries
* added addedTs to zboota-cars: this is the time at which a car is first added
** this will allow me to track the evolution of number of added cars for unregistered users
* added default timestamp of current time for lastGetTs if missing

2015-04-23
* aws.phar is no longer soft-linked from a million places
** because now I just use defined variable AWS_PHAR in the config file
* also stopped dropping orphan cars on a daily basis
** and attached a last get date in order to drop "stale" cars instead
** since all unregistered emails use "orphan" cars
* new accounts' initial list of cars was [] but should be {}

=Ver 0.0.1=

2015-04-22
* added statistics.php

2015-04-20
* some improvements to home page

2015-04-03
* some improvements to the home page

2015-02-14
* removed confirmation code
* generating my own password
* added lastloginDate
* using lastloginDate='' to capture 'unconfirmed' emails
* dropOrphan, dropStale, dropUnconfirmed all part of syncAll script

2015-02-11
* added deeplink in zabet notification email

2015-02-09
* added forgot password
* moved most code to ZbootaClient class (which also has interface)
* added increment pass/fail steps to confirmation and forgot password, so that if a user does so more than MAX times, the account is also locked (to avoid email flooding)

2015-02-05
* zboota-users: status -> status2 (reserved dynamodb keyword)
* added dropUnconfirmed accounts
* added dropOrphanCars to crontab

2015-01-28
* added notification preprocessing to avoid emailing users about same tickets every day
* split out notification sending php from syncAll php file
* added drop orphan cars in zboota-cars

2015-01-15
* First release
* client SPA
* dynamodb table for users
* can login + register new